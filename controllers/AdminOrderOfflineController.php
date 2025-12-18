<?php
// SIMPAN DATA
if ($action === 'store') {

    try {
        $pdo->beginTransaction();

        $invoice = 'OFF-' . date('Ymd') . '-' . rand(100,999);
        $total   = 0;

        // INSERT ORDER
        $stmtOrder = $pdo->prepare("
            INSERT INTO orders 
            (invoice_number, total_price, status, payment_method, order_type, created_at)
            VALUES (?, 0, ?, ?, 'offline', NOW())
        ");
        $stmtOrder->execute([
            $invoice,
            $_POST['status'],
            $_POST['payment_method']
        ]);

        $order_id = $pdo->lastInsertId();

        // PREPARE STATEMENT
        $stmtPrice = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmtItem  = $pdo->prepare("
            INSERT INTO order_items 
            (order_id, product_id, size_id, quantity, price)
            VALUES (?, ?, ?, ?, ?)
        ");

        foreach ($_POST['product_id'] as $i => $productId) {

            $sizeId = $_POST['size_id'][$i];
            $qty    = (int) $_POST['qty'][$i];

            if (!$productId || !$sizeId || !$qty) continue;

            // Ambil stok
            $stmtStock = $pdo->prepare("
                SELECT stock 
                FROM product_sizes 
                WHERE product_id=? AND size_id=?
            ");
            $stmtStock->execute([$productId, $sizeId]);
            $stock = (int) $stmtStock->fetchColumn();

            if ($qty > $stock) {
                throw new Exception("Stok tidak mencukupi untuk produk ID $productId");
            }

            // Ambil harga
            $stmtPrice->execute([$productId]);
            $price = $stmtPrice->fetchColumn();

            $total += $price * $qty;

            // Insert item
            $stmtItem->execute([
                $order_id,
                $productId,
                $sizeId,
                $qty,
                $price
            ]);

            // Kurangi stok
            $pdo->prepare("
                UPDATE product_sizes 
                SET stock = stock - ? 
                WHERE product_id=? AND size_id=?
            ")->execute([$qty, $productId, $sizeId]);
        }


        // UPDATE TOTAL
        $pdo->prepare("
            UPDATE orders 
            SET total_price = ? 
            WHERE id = ?
        ")->execute([$total, $order_id]);

        $pdo->commit();

        header("Location: index.php?page=admin_orders_offline&success=1");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die($e->getMessage());
    }
}

// DETAIL ORDER OFFLINE
if ($action === 'detail') {

    $order_id = $_GET['id'];

    // Order utama
    $stmt = $pdo->prepare("
        SELECT * FROM orders 
        WHERE id = ? AND order_type = 'offline'
    ");
    $stmt->execute([$order_id]);
    $order = $stmt->fetch();

    // Item
    $stmtItems = $pdo->prepare("
        SELECT 
            oi.*, 
            p.name AS product_name,
            s.name AS size_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        JOIN sizes s ON oi.size_id = s.id
        WHERE oi.order_id = ?
    ");
    $stmtItems->execute([$order_id]);
    $items = $stmtItems->fetchAll();

    // Return JSON (untuk modal)
    echo json_encode([
        'order' => $order,
        'items' => $items
    ]);
    exit;
}

// GET STOCK
if ($action === 'get_stock') {
    $stmt = $pdo->prepare("
        SELECT stock 
        FROM product_sizes 
        WHERE product_id = ? AND size_id = ?
    ");
    $stmt->execute([
        $_GET['product_id'],
        $_GET['size_id']
    ]);

    echo json_encode([
        'stock' => (int) $stmt->fetchColumn()
    ]);
    exit;
}


// AMBIL DATA ORDER OFFLINE
$stmt = $pdo->query("
    SELECT *
    FROM orders
    WHERE order_type = 'offline'
    ORDER BY created_at DESC
");

$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// PRODUCTS
$products = $pdo->query("SELECT id, name FROM products ORDER BY name")->fetchAll();

// SIZES
$sizes = $pdo->query("SELECT id, name FROM sizes ORDER BY name")->fetchAll();

include 'views/admin/orders_offline.php';

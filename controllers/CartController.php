<?php
// Add cart
if ($action == 'add') {
    $productId = $_POST['product_id'];
    $sizeId    = $_POST['size_id'];

    // Validasi stok size
    $stmt = $pdo->prepare("
        SELECT stock FROM product_sizes
        WHERE product_id = ? AND size_id = ?
    ");
    $stmt->execute([$productId, $sizeId]);
    $stock = $stmt->fetchColumn();

    if ($stock <= 0) {
        echo "<script>alert('Stok size ini habis');history.back();</script>";
        exit;
    }

    // Init cart
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = [];
    }

    if (!isset($_SESSION['cart'][$productId][$sizeId])) {
        $_SESSION['cart'][$productId][$sizeId] = 1;
    } else {
        // Cegah melebihi stok
        if ($_SESSION['cart'][$productId][$sizeId] < $stock) {
            $_SESSION['cart'][$productId][$sizeId]++;
        }
    }

    header("Location: index.php?page=cart");
    exit;
}


// 2. HAPUS ITEM
if ($action == 'delete') {
    $productId = $_GET['pid'];
    $sizeId    = $_GET['size'];

    unset($_SESSION['cart'][$productId][$sizeId]);

    // Jika size kosong, hapus produk
    if (empty($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }

    header("Location: index.php?page=cart");
    exit;
}

// 3. UPDATE QUANTITY (Plus / Minus) BERDASARKAN STOK
if ($action == 'update') {

    $productId = $_GET['pid'];
    $sizeId    = $_GET['size'];
    $type      = $_GET['type']; // plus / minus

    // Pastikan item ada di cart
    if (!isset($_SESSION['cart'][$productId][$sizeId])) {
        header("Location: index.php?page=cart");
        exit;
    }

    // Ambil stok asli dari DB
    $stmt = $pdo->prepare("
        SELECT stock FROM product_sizes
        WHERE product_id = ? AND size_id = ?
    ");
    $stmt->execute([$productId, $sizeId]);
    $stock = (int) $stmt->fetchColumn();

    // Qty saat ini di cart
    $currentQty = $_SESSION['cart'][$productId][$sizeId];

    if ($type === 'plus') {

        // ❗ CEGAH MELEBIHI STOK
        if ($currentQty < $stock) {
            $_SESSION['cart'][$productId][$sizeId]++;
        }

    } elseif ($type === 'minus') {

        $_SESSION['cart'][$productId][$sizeId]--;

        // Jika qty <= 0 → hapus item
        if ($_SESSION['cart'][$productId][$sizeId] <= 0) {
            unset($_SESSION['cart'][$productId][$sizeId]);

            // Jika produk tidak punya size lagi → hapus produk
            if (empty($_SESSION['cart'][$productId])) {
                unset($_SESSION['cart'][$productId]);
            }
        }
    }

    header("Location: index.php?page=cart");
    exit;
}

// 4. PROSES CHECKOUT
if ($action == 'checkout') {

    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Silakan login');location='index.php?page=login'</script>";
        exit;
    }

    if (empty($_SESSION['cart'])) {
        header("Location: index.php?page=shop");
        exit;
    }

    try {
        $pdo->beginTransaction();

        $user_id = $_SESSION['user']['id'];
        $invoice = 'INV-' . date('Ymd') . '-' . rand(100,999);
        $total_price = 0;

        // 1️⃣ INSERT ORDER
        $stmtOrder = $pdo->prepare("
            INSERT INTO orders (user_id, invoice_number, total_price, status, payment_method, created_at, order_type)
            VALUES (?, ?, 0, 'pending', 'QRIS', NOW(), 'online')
        ");
        $stmtOrder->execute([$user_id, $invoice]);
        $order_id = $pdo->lastInsertId();

        // 2️⃣ PREPARE STATEMENTS (WAJIB SEBELUM LOOP)
        $stmtPrice = $pdo->prepare("SELECT price FROM products WHERE id = ?");
        $stmtItem  = $pdo->prepare("
            INSERT INTO order_items (order_id, product_id, size_id, quantity, price)
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmtStock = $pdo->prepare("
            UPDATE product_sizes
            SET stock = stock - ?
            WHERE product_id = ? AND size_id = ?
        ");

        // 3️⃣ LOOP CART
        foreach ($_SESSION['cart'] as $productId => $sizes) {

            $stmtPrice->execute([$productId]);
            $price = $stmtPrice->fetchColumn();

            foreach ($sizes as $sizeId => $qty) {

                $subtotal = $price * $qty;
                $total_price += $subtotal;

                // Insert item
                $stmtItem->execute([
                    $order_id,
                    $productId,
                    $sizeId,
                    $qty,
                    $price
                ]);

                // Kurangi stok size
                $stmtStock->execute([
                    $qty,
                    $productId,
                    $sizeId
                ]);
            }
        }

        // 4️⃣ UPDATE TOTAL PRICE
        $stmtUpdate = $pdo->prepare("
            UPDATE orders SET total_price = ? WHERE id = ?
        ");
        $stmtUpdate->execute([$total_price, $order_id]);

        $pdo->commit();

        unset($_SESSION['cart']);
        header("Location: index.php?page=cart&status=success&order_id=$order_id");
        exit;


    } catch (Exception $e) {
        $pdo->rollBack();
        die("Checkout error: " . $e->getMessage());
    }
}

if (isset($_GET['status']) && $_GET['status'] == 'success') {

    if (!isset($_GET['order_id'])) {
        header("Location: index.php?page=shop");
        exit;
    }

    $order_id = (int) $_GET['order_id'];
    $user_id  = $_SESSION['user']['id'];

    // Ambil data order
    $stmt = $pdo->prepare("
        SELECT o.*, u.name, u.phone, u.address
        FROM orders o
        JOIN users u ON u.id = o.user_id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$order_id, $user_id]);
    $order = $stmt->fetch();

    if (!$order) {
        die('Invoice tidak ditemukan');
    }

    // Ambil item order + size
    $stmtItems = $pdo->prepare("
        SELECT 
            oi.quantity,
            oi.price,
            p.name AS product_name,
            p.image,
            s.name AS size_name
        FROM order_items oi
        JOIN products p ON p.id = oi.product_id
        JOIN sizes s ON s.id = oi.size_id
        WHERE oi.order_id = ?
    ");
    $stmtItems->execute([$order_id]);
    $items = $stmtItems->fetchAll();

    include 'views/user/checkout_success.php';
    exit;
}else {
    include 'views/user/cart.php';
}
?>
<?php
require_once __DIR__ . '/../vendor/autoload.php';

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

// 4.1 PROSES CHECKOUT
if ($action == 'checkout') {
    // Pastikan cart ada
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        echo "<script>alert('Cart kosong');window.location='index.php?page=shop';</script>";
        exit;
    }

    $userId = $_SESSION['user']['id'];

    $stmtUser = $pdo->prepare("
        SELECT name, phone, address, email
        FROM users
        WHERE id = ?
    ");
    $stmtUser->execute([$userId]);
    $user = $stmtUser->fetch();

    // Ambil semua item cart
    $cart = $_SESSION['cart'];

    $total = 0;
    $items = [];

    foreach ($cart as $productId => $sizes) {
        foreach ($sizes as $sizeId => $qty) {

            // Ambil data produk + size
            $stmt = $pdo->prepare("
                SELECT 
                    p.name,
                    p.image,
                    p.price,
                    s.name AS size_name
                FROM product_sizes ps
                JOIN products p ON p.id = ps.product_id
                JOIN sizes s ON s.id = ps.size_id
                WHERE ps.product_id = ? AND ps.size_id = ?
            ");
            $stmt->execute([$productId, $sizeId]);
            $data = $stmt->fetch();

            if ($data) {
                $subtotal = $data['price'] * $qty;
                $total += $subtotal;

                $items[] = [
                    'name' => $data['name'],
                    'image' => $data['image'],
                    'price' => $data['price'],
                    'size' => $data['size_name'],
                    'qty' => $qty,
                    'subtotal' => $subtotal
                ];
            }
        }
    }

    // Generate token
    $snap_token = '';

    \Midtrans\Config::$serverKey = '';
    \Midtrans\Config::$isProduction = false;
    \Midtrans\Config::$isSanitized = true;
    \Midtrans\Config::$is3ds = true;

    // Reference
    // $params = array(
    //     'transaction_details' => array(
    //         'order_id' => rand(),
    //         'gross_amount' => 10000,
    //     )
    // );

    $kode_unik = strtoupper(uniqid()); // kode unik
    $order_id = 'INV-' . date('Ymd') . '-' . substr($kode_unik, -6);
    $_SESSION['invoice'] = $order_id;

    $transaction_details = array(
        'order_id' => $order_id,
        'gross_amount' => $total,
    );

    $customer_details = array(
        'first_name'    => $user['name'],
        'last_name'     => "",
        'email'         => $user['email'],
        'phone'         => $user['phone']
    );

    $transaction = array(
        'transaction_details' => $transaction_details,
        'customer_details' => $customer_details,
        // 'item_details' => $item_details,
    );

    try{
        $snapToken = \Midtrans\Snap::getSnapToken($transaction);
    } catch(\Exception $e) {
        echo $e->getMessage();
    }

    include 'views/user/checkout.php';
    exit; 
}

if ($action == 'success') {
    $invoice = $_GET['order_id'];
    $user_id = $_SESSION['user']['id'];

    // cek apakah order sudah ada
    $check = $pdo->prepare("SELECT id FROM orders WHERE invoice_number = ?");
    $check->execute([$invoice]);
    $order = $check->fetch();

    if ($order) {
        // ✅ update status kalau sudah ada
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET status = 'paid' 
            WHERE invoice_number = ?
        ");
        $stmt->execute([$invoice]);
        $order_id = $order['id'];

        // 6️⃣ Bersihkan session cart
        unset($_SESSION['cart'], $_SESSION['order_id']);

        header("Location: index.php?page=orders");
        exit;
    } else {
        try {
            $pdo->beginTransaction();
            
            // 1️⃣ Ambil user & buat invoice unik
            $user_id = $_SESSION['user']['id'];
            $invoice = $_GET['order_id'];
            $total_price = 0;

            // 2️⃣ INSERT ORDER (langsung status paid karena success)
            $stmtOrder = $pdo->prepare("
                INSERT INTO orders 
                (user_id, invoice_number, total_price, status, payment_method, created_at, order_type)
                VALUES (?, ?, 0, 'paid', 'Bank Transfer', NOW(), 'online')
            ");
            $stmtOrder->execute([$user_id, $invoice]);
            $order_id = $pdo->lastInsertId();

            // 3️⃣ PREPARE STATEMENTS
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

            // 4️⃣ LOOP CART UNTUK INSERT ORDER_ITEMS & UPDATE STOCK
            foreach ($_SESSION['cart'] as $productId => $sizes) {
                $stmtPrice->execute([$productId]);
                $price = $stmtPrice->fetchColumn();

                foreach ($sizes as $sizeId => $qty) {
                    $subtotal = $price * $qty;
                    $total_price += $subtotal;

                    // Insert ke order_items
                    $stmtItem->execute([
                        $order_id,
                        $productId,
                        $sizeId,
                        $qty,
                        $price
                    ]);

                    // Kurangi stok
                    $stmtStock->execute([
                        $qty,
                        $productId,
                        $sizeId
                    ]);
                }
            }

            // 5️⃣ Update total price di orders
            $stmtUpdate = $pdo->prepare("
                UPDATE orders SET total_price = ? WHERE id = ?
            ");
            $stmtUpdate->execute([$total_price, $order_id]);

            $pdo->commit();

            // 6️⃣ Bersihkan session cart
            unset($_SESSION['cart'], $_SESSION['order_id']);

            // Redirect / tampil pesan sukses
            header("Location: index.php?page=orders");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Checkout error: " . $e->getMessage());
        }
    }
}

if ($action == 'pending') {

    $invoice = $_GET['order_id'];
    $user_id = $_SESSION['user']['id'];
    $total_price = 0;

    // cek apakah order sudah ada
    $check = $pdo->prepare("SELECT id FROM orders WHERE invoice_number = ?");
    $check->execute([$invoice]);
    $order = $check->fetch();

    if ($order) {
        // ✅ update status kalau sudah ada
        $stmt = $pdo->prepare("
            UPDATE orders 
            SET status = 'pending' 
            WHERE invoice_number = ?
        ");
        $stmt->execute([$invoice]);
        $order_id = $order['id'];

    } else {
        // ⚠️ INSERT ORDER baru karena belum ada
        $pdo->beginTransaction();
        try {
            $stmtOrder = $pdo->prepare("
                INSERT INTO orders 
                (user_id, invoice_number, total_price, status, payment_method, created_at, order_type)
                VALUES (?, ?, 0, 'pending', 'Bank Transfer', NOW(), 'online')
            ");
            $stmtOrder->execute([$user_id, $invoice]);
            $order_id = $pdo->lastInsertId();

            // Prepare statements untuk order_items & stock
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

            // loop cart
            foreach ($_SESSION['cart'] as $productId => $sizes) {
                $stmtPrice->execute([$productId]);
                $price = $stmtPrice->fetchColumn();

                foreach ($sizes as $sizeId => $qty) {
                    $subtotal = $price * $qty;
                    $total_price += $subtotal;

                    // insert order_items
                    $stmtItem->execute([
                        $order_id,
                        $productId,
                        $sizeId,
                        $qty,
                        $price
                    ]);

                    // kurangi stock
                    $stmtStock->execute([
                        $qty,
                        $productId,
                        $sizeId
                    ]);
                }
            }

            // update total price
            $stmtUpdate = $pdo->prepare("UPDATE orders SET total_price = ? WHERE id = ?");
            $stmtUpdate->execute([$total_price, $order_id]);

            $pdo->commit();

        } catch (Exception $e) {
            $pdo->rollBack();
            die("Pending error: " . $e->getMessage());
        }
    }

    echo "Menunggu pembayaran...";
}

// 4.2 PROSES PAYMENT
if ($action == 'payments') {
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Silakan login');location='index.php?page=login'</script>";
        exit;
    }

    if (empty($_SESSION['cart'])) {
        header("Location: index.php?page=shop");
        exit;
    }
}

// 5. GET STATUS
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
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



// 4. PROSES CHECKOUT (Simpan ke DB)
if ($action == 'checkout') {
    // Cek Login dulu
    if (!isset($_SESSION['user'])) {
        echo "<script>alert('Silakan Login untuk melanjutkan checkout!'); window.location='index.php?page=login';</script>";
        exit;
    }

    // Cek Keranjang Kosong
    if (empty($_SESSION['cart'])) {
        header("Location: index.php?page=shop");
        exit;
    }

    try {
        $pdo->beginTransaction();

        // A. Hitung Total & Siapkan Data
        $user_id = $_SESSION['user']['id'];
        $invoice = 'INV-' . date('Ymd') . '-' . rand(100, 999);
        $total_price = 0;
        
        $total_price = 0;

        foreach ($_SESSION['cart'] as $productId => $sizes) {

            foreach ($sizes as $sizeId => $qty) {

                // Ambil harga produk
                $stmt = $pdo->prepare("SELECT price FROM products WHERE id = ?");
                $stmt->execute([$productId]);
                $price = $stmt->fetchColumn();

                $total_price += $price * $qty;

                // Insert order_items (WAJIB ada size_id)
                $stmtItem->execute([
                    $order_id,
                    $productId,
                    $sizeId,
                    $qty,
                    $price
                ]);

                // Kurangi stok size
                $stmt = $pdo->prepare("
                    UPDATE product_sizes
                    SET stock = stock - ?
                    WHERE product_id = ? AND size_id = ?
                ");
                $stmt->execute([$qty, $productId, $sizeId]);
            }
        }


        // B. Insert ke Tabel ORDERS
        $sqlOrder = "INSERT INTO orders (user_id, invoice_number, total_price, status, created_at) VALUES (?, ?, ?, 'pending', NOW())";
        $stmtOrder = $pdo->prepare($sqlOrder);
        $stmtOrder->execute([$user_id, $invoice, $total_price]);
        $order_id = $pdo->lastInsertId();

        // C. Insert ke Tabel ORDER_ITEMS
        $sqlItem = "INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)";
        $stmtItem = $pdo->prepare($sqlItem);

        foreach ($products_db as $p) {
            $qty = $_SESSION['cart'][$p['id']];
            $stmtItem->execute([$order_id, $p['id'], $qty, $p['price']]);
        }

        $pdo->commit();

        // D. Kosongkan Cart & Redirect
        unset($_SESSION['cart']);
        header("Location: index.php?page=cart&status=success&inv=$invoice&total=$total_price");
        exit;

    } catch (Exception $e) {
        $pdo->rollBack();
        die("Terjadi kesalahan: " . $e->getMessage());
    }
}

// Load View
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    include 'views/user/checkout_success.php';
} else {
    include 'views/user/cart.php';
}
?>
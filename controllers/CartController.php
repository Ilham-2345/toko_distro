<?php
// controllers/CartController.php

// 1. TAMBAH ITEM KE CART
if ($action == 'add') {
    $id = $_GET['id'];
    // Jika belum ada, set 1. Jika sudah, tambah 1.
    if (!isset($_SESSION['cart'][$id])) {
        $_SESSION['cart'][$id] = 1;
    } else {
        $_SESSION['cart'][$id]++;
    }
    // Redirect kembali ke halaman sebelumnya atau cart
    header("Location: index.php?page=cart");
    exit;
}

// 2. HAPUS ITEM
if ($action == 'delete') {
    $id = $_GET['id'];
    unset($_SESSION['cart'][$id]);
    header("Location: index.php?page=cart");
    exit;
}

// 3. UPDATE QUANTITY (Plus/Minus)
if ($action == 'update') {
    $id = $_GET['id'];
    $type = $_GET['type']; // 'plus' atau 'minus'
    
    if (isset($_SESSION['cart'][$id])) {
        if ($type == 'plus') {
            $_SESSION['cart'][$id]++;
        } elseif ($type == 'minus') {
            $_SESSION['cart'][$id]--;
            // Jika 0, hapus item
            if ($_SESSION['cart'][$id] <= 0) {
                unset($_SESSION['cart'][$id]);
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
        
        // Ambil harga terbaru dari DB untuk keamanan
        $ids = array_keys($_SESSION['cart']);
        $in  = str_repeat('?,', count($ids) - 1) . '?';
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
        $stmt->execute($ids);
        $products_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Hitung Grand Total
        foreach ($products_db as $p) {
            $qty = $_SESSION['cart'][$p['id']];
            $total_price += $p['price'] * $qty;
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

// 5. VIEW CART (Tampilkan Data)
// Jika keranjang kosong
if (empty($_SESSION['cart'])) {
    $cartItems = [];
} else {
    // Ambil detail produk berdasarkan ID yg ada di session
    $ids = array_keys($_SESSION['cart']);
    // Trik SQL WHERE IN (?,?,?)
    $in  = str_repeat('?,', count($ids) - 1) . '?';
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id IN ($in)");
    $stmt->execute($ids);
    $cartItems = $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Load View
if (isset($_GET['status']) && $_GET['status'] == 'success') {
    include 'views/user/checkout_success.php';
} else {
    include 'views/user/cart.php';
}
?>
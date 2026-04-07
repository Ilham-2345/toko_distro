<?php
// controllers/OrderController.php

if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

$userId = $_SESSION['user']['id'];

// ============================
// DETAIL ORDER (INVOICE)
// ============================
if (isset($_GET['page']) && $_GET['page'] === 'invoice' && isset($_GET['id'])) {

    $orderId = $_GET['id'];

    // Ambil order berdasarkan user (biar aman)
    $stmt = $pdo->prepare("
        SELECT o.*, u.name, u.phone, u.address
        FROM orders o
        JOIN users u ON u.id = o.user_id
        WHERE o.id = ? AND o.user_id = ?
    ");
    $stmt->execute([$orderId, $userId]);
    $order = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$order) {
        echo "Order tidak ditemukan!";
        exit;
    }

    // Ambil item order
    $stmt = $pdo->prepare("
        SELECT 
            oi.*,
            p.name AS product_name,
            p.image,
            s.name AS size_name
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        LEFT JOIN sizes s ON oi.size_id = s.id
        WHERE oi.order_id = ?
    ");
    $stmt->execute([$orderId]);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    include 'views/user/invoice.php';
    exit;
}

// Ambil orders user
$stmt = $pdo->prepare("
    SELECT *
    FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
");
$stmt->execute([$userId]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil item per order
$orderItems = [];

if ($orders) {
    $orderIds = array_column($orders, 'id');
    $in = str_repeat('?,', count($orderIds) - 1) . '?';

    $stmt = $pdo->prepare("
        SELECT 
            oi.order_id,
            p.name,
            p.image,
            oi.quantity,
            oi.price
        FROM order_items oi
        JOIN products p ON oi.product_id = p.id
        WHERE oi.order_id IN ($in)
    ");
    $stmt->execute($orderIds);
    $items = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($items as $item) {
        $orderItems[$item['order_id']][] = $item;
    }
}

include 'views/user/orders.php';

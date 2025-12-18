<?php
// controllers/OrderController.php

if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

$userId = $_SESSION['user']['id'];

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

<?php
// controllers/AdminOrderController.php

$orderId = $_GET['id'] ?? null;

if (!$orderId) {
    die('Order ID tidak ditemukan');
}

// 1. Ambil data order + user
$stmt = $pdo->prepare("
    SELECT 
        o.*,
        u.name AS customer_name,
        u.phone,
        u.address
    FROM orders o
    JOIN users u ON o.user_id = u.id
    WHERE o.id = ?
");
$stmt->execute([$orderId]);
$order = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$order) {
    die('Pesanan tidak ditemukan');
}

// 2. Ambil item pesanan + produk + size
$stmt = $pdo->prepare("
    SELECT 
        oi.quantity,
        oi.price,
        p.name AS product_name,
        p.image,
        s.name AS size_name
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    JOIN sizes s ON oi.size_id = s.id
    WHERE oi.order_id = ?
");
$stmt->execute([$orderId]);
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load view
include 'views/admin/order_detail.php';

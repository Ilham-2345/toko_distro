<?php
// controllers/AdminOrderController.php

// Pastikan admin login
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'pegawai'])) {
    header("Location: index.php?page=login");
    exit;
}

/*
|--------------------------------------------------------------------------
| UPDATE STATUS ORDER
|--------------------------------------------------------------------------
*/
// UPDATE STATUS ORDER
if ($action === 'update_status') {

    $orderId = $_POST['order_id'];
    $status  = $_POST['status'];

    // Validasi status
    $allowedStatus = ['pending', 'paid', 'shipped', 'completed', 'cancelled'];

    if (!in_array($status, $allowedStatus)) {
        die('Status tidak valid');
    }

    $stmt = $pdo->prepare("
        UPDATE orders
        SET status = ?
        WHERE id = ?
    ");
    $stmt->execute([$status, $orderId]);

    header("Location: index.php?page=admin_orders");
    exit;
}


/*
|--------------------------------------------------------------------------
| FILTER ORDER
|--------------------------------------------------------------------------
*/
$where  = [];
$params = [];

if (!empty($_GET['customer'])) {
    $where[] = "u.name LIKE ?";
    $params[] = "%" . $_GET['customer'] . "%";
}

if (!empty($_GET['from'])) {
    $where[] = "DATE(o.created_at) >= ?";
    $params[] = $_GET['from'];
}

if (!empty($_GET['to'])) {
    $where[] = "DATE(o.created_at) <= ?";
    $params[] = $_GET['to'];
}

if (!empty($_GET['status'])) {
    $where[] = "o.status = ?";
    $params[] = $_GET['status'];
}

$whereSQL = $where ? "WHERE " . implode(" AND ", $where) : "";

/*
|--------------------------------------------------------------------------
| GET ORDER DATA
|--------------------------------------------------------------------------
*/
$stmt = $pdo->prepare("
    SELECT 
        o.id,
        o.created_at,
        o.total_price,
        o.status,
        u.name AS customer_name
    FROM orders o
    JOIN users u ON o.user_id = u.id
    $whereSQL
    ORDER BY o.created_at DESC
");
$stmt->execute($params);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Load View
include 'views/admin/orders.php';

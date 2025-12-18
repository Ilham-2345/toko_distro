<?php
// Keamanan: admin & pegawai
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin','pegawai'])) {
    header("Location: index.php?page=login");
    exit;
}

// Ambil filter tanggal
$from = $_GET['from'] ?? null;
$to   = $_GET['to'] ?? null;

$whereDate = "";
$params = [];

if ($from && $to) {
    $whereDate = "AND DATE(o.created_at) BETWEEN ? AND ?";
    $params[] = $from;
    $params[] = $to;
}

/* ======================
   1. TOTAL PESANAN
====================== */
$stmtTotalOrder = $pdo->prepare("
    SELECT COUNT(*) 
    FROM orders o
    WHERE o.status IN ('paid','completed')
    $whereDate
");
$stmtTotalOrder->execute($params);
$totalOrders = $stmtTotalOrder->fetchColumn();

/* ======================
   2. TOTAL PENDAPATAN
====================== */
$stmtRevenue = $pdo->prepare("
    SELECT SUM(o.total_price)
    FROM orders o
    WHERE o.status IN ('paid','completed')
    $whereDate
");
$stmtRevenue->execute($params);
$totalRevenue = $stmtRevenue->fetchColumn() ?? 0;

/* ======================
   3. PRODUK TERLARIS
====================== */
$stmtBestProduct = $pdo->prepare("
    SELECT p.name, SUM(oi.quantity) as total_qty
    FROM order_items oi
    JOIN products p ON p.id = oi.product_id
    JOIN orders o ON o.id = oi.order_id
    WHERE o.status IN ('paid','completed')
    $whereDate
    GROUP BY oi.product_id
    ORDER BY total_qty DESC
    LIMIT 1
");
$stmtBestProduct->execute($params);
$bestProduct = $stmtBestProduct->fetch();

/* ======================
   4. DETAIL PENJUALAN
====================== */
$stmtDetail = $pdo->prepare("
    SELECT 
        o.id,
        o.created_at,
        o.invoice_number,
        SUM(oi.quantity) as total_items,
        o.total_price
    FROM orders o
    JOIN order_items oi ON oi.order_id = o.id
    WHERE o.status IN ('paid','completed')
    $whereDate
    GROUP BY o.id
    ORDER BY o.created_at DESC
");
$stmtDetail->execute($params);
$details = $stmtDetail->fetchAll();

/* ======================
   LOAD VIEW
====================== */
include 'views/admin/report.php';

<?php
// Cek keamanan — hanya admin yang boleh
if (!isset($_SESSION['user']) || !in_array($_SESSION['user']['role'], ['admin', 'pegawai'])) {
    header("Location: index.php?page=login");
    exit;
}

// Load View
include 'views/admin/stock.php';
?>
<?php 
// Cek keamanan — hanya admin yang boleh
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

// Load View
include 'views/admin/categories.php';

// Logic

?>
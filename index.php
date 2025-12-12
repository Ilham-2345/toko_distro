<?php
require_once 'config/database.php';

// Routing Sederhana
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$action = isset($_GET['action']) ? $_GET['action'] : 'view';

// Cek Role untuk akses folder admin
if (strpos($page, 'admin') !== false && (!isset($_SESSION['user']) || $_SESSION['user']['role'] != 'admin')) {
    header("Location: index.php?page=login");
    exit;
}

// Switch Page
switch ($page) {
    case 'home':
        include 'views/user/home.php';
        break;
    case 'shop':
        include 'views/user/shop.php';
        break;
    case 'login':
        include 'views/auth/login.php';
        break;
    
    // Fitur Admin
    case 'admin_dashboard':
        require 'controllers/DashboardController.php';
        break;
    case 'admin_products':
        require 'controllers/ProductController.php';
        break;
    case 'admin_orders':
        require 'controllers/OrderController.php';
        break;
    case 'auth':
        require 'controllers/AuthController.php';
        break;
        
    // Fitur User
    case 'cart':
        require 'controllers/CartController.php';
        break;
        
    default:
        echo "Halaman tidak ditemukan!";
        break;
}
?>
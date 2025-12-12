<?php 

// decprecated
// if (isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin') {
//     // Cegah loop jika sudah berada di halaman admin_dashboard
//     if (!isset($_GET['page']) || $_GET['page'] !== 'admin_dashboard') {
//         header("Location: index.php?page=admin_dashboard");
//         exit;
//     }
// }

// Cek keamanan — hanya admin yang boleh
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

// Setelah lolos, tampilkan dashboard
include 'views/admin/dashboard.php';
?>
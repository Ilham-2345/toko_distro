<?php
// Pastikan hanya admin
if ($_SESSION['user']['role'] !== 'admin') {
    header('Location: index.php?page=admin_dashboard');
    exit;
}

$action = $_GET['action'] ?? null;

/* TAMBAH STAFF */
if ($action === 'store') {

    $name     = $_POST['name'];
    $email    = $_POST['email'];
    $phone    = 0;
    $address    = '';
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role, created_at, phone, address)
        VALUES (?, ?, ?, 'pegawai', NOW(), ?, ?)
    ");
    $stmt->execute([$name, $email, $password, $phone, $address]);

    header("Location: index.php?page=admin_staff&success=1");
    exit;
}

/* HAPUS STAFF */
if ($action === 'delete') {

    $id = $_GET['id'];

    $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role='pegawai'");
    $stmt->execute([$id]);

    header("Location: index.php?page=admin_staff&deleted=1");
    exit;
}

/* AMBIL DATA STAFF */
$stmt = $pdo->query("
    SELECT id, name, email, created_at 
    FROM users 
    WHERE role='pegawai'
    ORDER BY created_at DESC
");

$staffs = $stmt->fetchAll();

include 'views/admin/staff_list.php';

?>
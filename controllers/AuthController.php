<?php
// Simpan di: controllers/AuthController.php

if ($action == 'login_process') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        // Set Session
        $_SESSION['user'] = [
            'id' => $user['id'],
            'name' => $user['name'],
            'role' => $user['role']
        ];

        // Redirect sesuai Role
        if ($user['role'] == 'admin' || $user['role'] == 'pegawai') {
            header("Location: index.php?page=admin_dashboard");
        } else {
            header("Location: index.php?page=home");
        }
    } else {
        echo "<script>alert('Email atau Password salah!'); window.location='index.php?page=login';</script>";
    }
}

if ($action == 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
}
?>
<?php
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
            'email' => $user['email'],
            'role' => $user['role']
        ];

        // Redirect sesuai Role
        if ($user['role'] == 'admin' || $user['role'] == 'pegawai') {
            header("Location: index.php?page=home");
        } else {
            header("Location: index.php?page=home");
        }
    } else {
        echo "<script>alert('Email atau Password salah!'); window.location='index.php?page=login';</script>";
    }
}

if ($action == 'register') {

    $name     = trim($_POST['name']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // Validasi sederhana
    if (empty($name) || empty($email) || empty($password)) {
        echo "<script>alert('Semua field wajib diisi!'); window.location='index.php?page=register';</script>";
        exit;
    }

    // Cek email sudah terdaftar
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        echo "<script>alert('Email sudah terdaftar!'); window.location='index.php?page=register';</script>";
        exit;
    }

    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Role default = user
    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role)
        VALUES (?, ?, ?, 'user')
    ");
    $stmt->execute([$name, $email, $hashedPassword]);

    echo "<script>alert('Registrasi berhasil, silakan login'); window.location='index.php?page=login';</script>";
}


if ($action == 'logout') {
    session_destroy();
    header("Location: index.php?page=login");
}
?>
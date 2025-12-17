<?php
// Cegah akses jika belum login
if (!isset($_SESSION['user'])) {
    header("Location: index.php?page=login");
    exit;
}

$user = $_SESSION['user'];
?>

<?php include 'views/layouts/header.php'; ?>

<div class="container my-5" style="max-width: 700px;">

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <h3 class="fw-bold mb-4">Profil Saya</h3>

            <div class="row mb-3">
                <div class="col-4 text-muted">Nama</div>
                <div class="col-8 fw-semibold"><?= htmlspecialchars($user['name']) ?></div>
            </div>

            <div class="row mb-3">
                <div class="col-4 text-muted">Email</div>
                <div class="col-8"><?= htmlspecialchars($user['email']) ?></div>
            </div>

            <hr>

            <div class="d-flex gap-3">
                <a href="index.php?page=home" class="btn btn-outline-dark">
                    Kembali
                </a>

                <a href="index.php?page=auth&action=logout" 
                   class="btn btn-danger">
                    Logout
                </a>
            </div>

        </div>
    </div>

</div>

<?php include 'views/layouts/footer.php'; ?>

<?php 
// Hitung total produk
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products = $stmt->fetchColumn();

// Hitung total pesanan
$stmt = $pdo->query("SELECT COUNT(*) FROM orders");
$total_orders = $stmt->fetchColumn();

// Hitung total users dengan role 'user'
$stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role='user'");
$total_users = $stmt->fetchColumn();

$user = $_SESSION['user'];
?>

<?php include 'views/layouts/admin/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-1 fw-bold">Halo <?= htmlspecialchars($user['name']); ?> 👋</h1>
    <p class="text-muted mb-4">Ringkasan data sistem hari ini</p>

    <div class="row g-4">

        <!-- Total Produk -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 dashboard-card bg-primary text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box me-3">
                        <i class="bi bi-box-seam fs-1"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Total Produk</h6>
                        <h2 class="fw-bold mb-0"><?= $total_products ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Pesanan -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 dashboard-card bg-success text-white">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box me-3">
                        <i class="bi bi-cart-check fs-1"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Total Pesanan</h6>
                        <h2 class="fw-bold mb-0"><?= $total_orders ?></h2>
                    </div>
                </div>
            </div>
        </div>

        <!-- Total Users -->
        <div class="col-md-4">
            <div class="card border-0 shadow-sm h-100 dashboard-card bg-warning text-dark">
                <div class="card-body d-flex align-items-center">
                    <div class="icon-box me-3">
                        <i class="bi bi-people fs-1"></i>
                    </div>
                    <div>
                        <h6 class="mb-1">Total Users</h6>
                        <h2 class="fw-bold mb-0"><?= $total_users ?></h2>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'views/layouts/admin/footer.php'; ?>

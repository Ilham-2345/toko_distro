<?php 
$stmt = $pdo->query("SELECT COUNT(*) FROM products");
$total_products = $stmt->fetchColumn();

?>

<?php include 'views/layouts/admin/header.php'; ?>

<div class="container mt-5">
    <h1 class="mb-4">Halo Admin 👋</h1>
    
    <div class="row g-4">
        <!-- Card: Total Produk -->
        <div class="col-md-4">
            <div class="card d-flex justify-content-center align-items-center" style="height: 200px">
                <div class="text-center">
                    <h5 class="card-title">Total Produk</h5>
                    <h2 class="display-6 fw-bold"><?= $total_products ?></h2>
                </div>
            </div>
        </div>

        <!-- Card: Total Pesanan -->
        <div class="col-md-4">
            <div class="card d-flex justify-content-center align-items-center" style="height: 200px">
                <div class="text-center">
                    <h5 class="card-title">Total Pesanan</h5>
                    <h2 class="display-6 fw-bold">5</h2>
                </div>
            </div>
        </div>

        <!-- Card: Total Users (Opsional) -->
        <div class="col-md-4">
            <div class="card d-flex justify-content-center align-items-center" style="height: 200px">
                <div class="text-center">
                    <h5 class="card-title">Total Users</h5>
                    <h2 class="display-6 fw-bold">3</h2>
                </div>
            </div>
        </div>
    </div>

</div>

<?php include 'views/layouts/admin/footer.php'; ?>

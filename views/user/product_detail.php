<?php
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$productId = $_GET['id'];

$stmt = $pdo->prepare("
    SELECT products.*, categories.name AS category_name
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    WHERE products.id = ?
");
$stmt->execute([$productId]);

$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika produk tidak ditemukan
if (!$product) {
    echo "Produk tidak ditemukan.";
    exit;
}
?>


<?php include 'views/layouts/header.php'; ?>
<div class="container product-detail my-5">
    <div class="row g-5">

        <!-- KIRI: GAMBAR PRODUK -->
        <div class="col-md-5 text-center">
            <img src="uploads/<?= $product['image'] ?>" class="img-fluid" alt="<?= $product['name'] ?>">

        </div>

        <!-- KANAN: DETAIL PRODUK -->
        <div class="col-md-7">

            <span class="badge bg-dark mb-2">In Stock</span>

            <h4 class="fw-bold mt-2"><?= $product['name'] ?></h4>

            <h5 class="fw-semibold my-3">
                Rp <?= number_format($product['price']) ?>
            </h5>

            <span class="badge bg-dark mb-2">
                <?= $product['stock'] > 0 ? 'In Stock' : 'Out of Stock' ?>
            </span>

            <p>Total Stok: <strong><?= $product['stock'] ?></strong></p>

            <!-- STOK PER UKURAN -->
            <div class="d-flex gap-4 mb-4">
                <div class="text-center size-item">
                    <img src="tshirt.jpg" alt="">
                    <div class="small mt-1">S : 2</div>
                </div>
                <div class="text-center size-item">
                    <img src="tshirt.jpg" alt="">
                    <div class="small mt-1">M : 3</div>
                </div>
                <div class="text-center size-item">
                    <img src="tshirt.jpg" alt="">
                    <div class="small mt-1">L : 2</div>
                </div>
                <div class="text-center size-item">
                    <img src="tshirt.jpg" alt="">
                    <div class="small mt-1">XL : 2</div>
                </div>
            </div>

            <hr>

            <!-- DESKRIPSI -->
            <p><?= nl2br($product['description']) ?></p>

            <!-- TOMBOL -->
            <div class="mt-4">
                <a href="index.php" class="btn btn-dark">Kembali</a>
            </div>

        </div>
    </div>
</div>
<?php include 'views/layouts/footer.php'; ?>
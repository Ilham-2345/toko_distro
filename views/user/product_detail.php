<?php
// Ambil data produk
$stmt = $pdo->prepare("
    SELECT p.*
    FROM products p
    WHERE p.id = ?
");
$stmt->execute([$_GET['id']]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

// Jika produk tidak ditemukan
if (!$product) {
    echo "Produk tidak ditemukan.";
    exit;
}

// Ambil stok per size
$stmt = $pdo->prepare("
    SELECT s.id, s.name, ps.stock
    FROM product_sizes ps
    JOIN sizes s ON ps.size_id = s.id
    WHERE ps.product_id = ?
");
$stmt->execute([$product['id']]);
$sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Hitung total stok
$totalStock = array_sum(array_column($sizes, 'stock'));

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
            <h4 class="fw-bold mt-2"><?= $product['name'] ?></h4>

            <h5 class="fw-semibold my-3">
                Rp <?= number_format($product['price']) ?>
            </h5>

            <span class="badge bg-<?= $totalStock > 0 ? 'success' : 'secondary' ?> mb-2">
                <?= $totalStock > 0 ? 'In Stock' : 'Out of Stock' ?>
            </span>

            <p>Total Stok: <strong><?= $totalStock ?></strong></p>


            <!-- STOK PER UKURAN -->
            <form action="index.php?page=cart&action=add" method="POST">
                <input type="hidden" name="size_id" id="selected_size">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                <div class="d-flex gap-3 mb-4 flex-wrap">
                    <?php foreach ($sizes as $s): ?>
                        <?php if ($s['stock'] > 0): ?>
                            <button 
                                type="button"
                                class="btn btn-outline-dark size-btn"
                                data-size="<?= $s['id'] ?>"
                            >
                                <?= $s['name'] ?>
                                <div class="small">Sisa <?= $s['stock'] ?></div>
                            </button>
                        <?php else: ?>
                            <button 
                                type="button"
                                class="btn btn-outline-secondary disabled"
                                disabled
                            >
                                <?= $s['name'] ?>
                                <div class="small">Habis</div>
                            </button>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </div>

                <!-- DESKRIPSI -->
                <p><?= nl2br($product['description']) ?></p>
                <hr>

                <div class="d-flex gap-3 align-items-center">
                    <button type="submit" class="btn btn-dark" id="addToCartBtn" disabled>
                        Add to Cart
                    </button>
                    <!-- TOMBOL -->
                    <div>
                        <a href="index.php" class="btn">Kembali</a>
                    </div>
                </div>
            </form>
            
            



        </div>
    </div>
</div>

<script>
    const sizeButtons = document.querySelectorAll('.size-btn');
    const sizeInput   = document.getElementById('selected_size');
    const addBtn      = document.getElementById('addToCartBtn');

    sizeButtons.forEach(btn => {
        btn.addEventListener('click', () => {

            // reset active
            sizeButtons.forEach(b => b.classList.remove('active'));

            // set active
            btn.classList.add('active');

            // set hidden input
            sizeInput.value = btn.dataset.size;

            // enable add to cart
            addBtn.disabled = false;
        });
    });
</script>

<?php include 'views/layouts/footer.php'; ?>
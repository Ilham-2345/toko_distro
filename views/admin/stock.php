<?php
$stmt = $pdo->query("
    SELECT 
        p.id,
        p.name,
        p.image,
        c.name AS category_name,
        COALESCE(SUM(ps.stock), 0) AS total_stock
    FROM products p
    LEFT JOIN categories c ON p.category_id = c.id
    LEFT JOIN product_sizes ps ON p.id = ps.product_id
    GROUP BY p.id
    ORDER BY p.id DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

$stmt = $pdo->query("
    SELECT 
        ps.product_id,
        s.name,
        ps.stock
    FROM product_sizes ps
    JOIN sizes s ON ps.size_id = s.id
");
$sizeRows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$productSizes = [];

foreach ($sizeRows as $row) {
    $productSizes[$row['product_id']][] = [
        'name'  => $row['name'],
        'stock' => $row['stock']
    ];
}

// Update Stock
if (isset($_GET['action']) && $_GET['action'] === 'update_stock') {

    $productId = $_POST['product_id'];
    $stocks    = $_POST['stocks'];

    $stmt = $pdo->prepare("
        UPDATE product_sizes 
        SET stock = ?
        WHERE product_id = ? AND size_id = ?
    ");

    foreach ($stocks as $sizeId => $stock) {
        $stmt->execute([$stock, $productId, $sizeId]);
    }

    header("Location: index.php?page=admin_stock&status=updated");
    exit;
}



?>

<?php include 'views/layouts/admin/header.php'; ?>

<div class="container my-3">

    <h2 class="fw-bold mb-3">Manajemen Stock Produk</h2>
    <hr>

    <!-- Notifikasi -->
    <?php if(isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
        <div class="alert alert-success">Stock produk berhasil diperbarui.</div>
    <?php endif; ?>

    <!-- Tabel Produk -->
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-3">Daftar Produk</h4>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="60">ID</th>
                            <th width="80">Gambar</th>
                            <th>Nama Produk</th>
                            <th>Stok</th>
                            <th>Status</th>
                            <th class="text-center" width="200">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach($products as $p): ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><img src="uploads/<?= $p['image'] ?>" width="60" class="rounded"></td>
                            <td><?= $p['name'] ?></td>
                            <td>
                                <?php if (!empty($productSizes[$p['id']])): ?>
                                    <?php foreach ($productSizes[$p['id']] as $s): ?>
                                        <?= $s['name'] ?>: <?= $s['stock'] ?><br>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>

                            <td>
                                <?php
                                if ($p['total_stock'] == 0) {
                                    $status = ['Habis', 'secondary'];
                                } elseif ($p['total_stock'] <= 3) {
                                    $status = ['Menipis', 'danger'];
                                } else {
                                    $status = ['Aman', 'success'];
                                }
                                ?>
                                <span class="btn btn-<?= $status[1] ?> btn-sm"><?= $status[0] ?></span>
                            </td>


                            <td class="text-center">

                                <!-- Tombol Update Stock -->
                                <button 
                                    type="button"
                                    class="btn btn-success btn-sm px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateStockModal"
                                    data-id="<?= $p['id'] ?>"
                                    data-name="<?= htmlspecialchars($p['name']) ?>"
                                >
                                    Update Stock
                                </button>


                            </td>
                        </tr>
                        <?php $i++; ?>
                        <?php endforeach; ?>
                    </tbody>

                </table>
            </div>

        </div>
    </div>

</div>

<!-- Modal Update Stock -->
<div class="modal fade" id="updateStockModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <form action="index.php?page=admin_stock&action=update_stock" method="POST" class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">Update Stok Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <input type="hidden" name="product_id" id="modal_product_id">

        <div class="mb-3">
          <label class="form-label">Nama Produk</label>
          <input type="text" id="modal_product_name" class="form-control" readonly>
        </div>

        <div id="stock-container">
          <!-- INPUT SIZE AKAN DIISI VIA AJAX -->
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-dark">Simpan Stok</button>
      </div>

    </form>
  </div>
</div>

<script>
let modal = document.getElementById('updateStockModal');

modal.addEventListener('show.bs.modal', function (event) {
    let button = event.relatedTarget;

    let id   = button.getAttribute('data-id');
    let name = button.getAttribute('data-name');

    document.getElementById('modal_product_id').value = id;
    document.getElementById('modal_product_name').value = name;

    fetch('views/admin/get_product_sizes.php?product_id=' + id)
        .then(res => res.text())
        .then(html => {
            document.getElementById('stock-container').innerHTML = html;
        });
});
</script>
<?php include 'views/layouts/admin/footer.php'; ?>

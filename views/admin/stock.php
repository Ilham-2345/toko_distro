<?php

// LOAD CATEGORY DATA
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil data produk
$stmt = $pdo->query("
    SELECT products.*, categories.name AS category_name
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    ORDER BY products.id DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// =============================
// LOGIC UPDATE STOK
// =============================
if (isset($_GET['action']) && $_GET['action'] === 'update_stock') {

    $id    = $_POST['product_id'];
    $stock = $_POST['stock'];

    $stmt = $pdo->prepare("UPDATE products SET stock = ? WHERE id = ?");
    $stmt->execute([$stock, $id]);

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
                            <td><strong><?= $p['stock'] ?></strong></td>

                            <td class="text-center">

                                <!-- Tombol Update Stock -->
                                <button 
                                    class="btn btn-success btn-sm px-3"
                                    data-bs-toggle="modal"
                                    data-bs-target="#updateStockModal"
                                    data-id="<?= $p['id'] ?>"
                                    data-name="<?= $p['name'] ?>"
                                    data-stock="<?= $p['stock'] ?>"
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
<div class="modal fade" id="updateStockModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
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

        <div class="mb-3">
          <label class="form-label">Stok Baru</label>
          <input type="number" name="stock" id="modal_product_stock" class="form-control" required>
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
        <button type="submit" class="btn btn-dark">Simpan Perubahan</button>
      </div>

    </form>
  </div>
</div>

<script>
    let modal = document.getElementById('updateStockModal');
    modal.addEventListener('show.bs.modal', function (event) {
        let button = event.relatedTarget;

        let id    = button.getAttribute('data-id');
        let name  = button.getAttribute('data-name');
        let stock = button.getAttribute('data-stock');

        document.getElementById('modal_product_id').value = id;
        document.getElementById('modal_product_name').value = name;
        document.getElementById('modal_product_stock').value = stock;
    });
</script>


<?php include 'views/layouts/admin/footer.php'; ?>

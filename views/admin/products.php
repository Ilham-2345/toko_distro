<?php
// Hanya admin yang boleh akses
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

// LOAD CATEGORY DATA
$stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);

// LOGIC TAMBAH PRODUK
if (isset($_GET['action']) && $_GET['action'] === 'add') {

    $name       = $_POST['name'];
    $price      = $_POST['price'];
    $description = $_POST['description'];
    $categoryId = $_POST['category_id'];

    // Upload gambar 
    $fileName = null;

    if (!empty($_FILES['image']['name'])) {

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = time() . "_" . rand(1000,9999) . "." . $ext; // nama unik

        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $fileName);
    }

    // Simpan ke DB
    $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id, image, description) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$name, $price, $categoryId, $fileName, $description]);

    header("Location: index.php?page=admin_products&status=success");
    exit;
}

// LOGIC HAPUS PRODUK
if (isset($_GET['delete_id'])) {

    // Ambil gambar untuk dihapus
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    $image = $stmt->fetchColumn();

    // Hapus gambar jika ada
    if ($image && file_exists("uploads/" . $image)) {
        unlink("uploads/" . $image);
    }

    // Hapus produk
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);

    header("Location: index.php?page=admin_products&status=deleted");
    exit;
}

// LOGIC UPDATE PRODUK
if (isset($_GET['action']) && $_GET['action'] === 'update') {

    $id          = $_POST['id'];
    $name        = $_POST['name'];
    $price       = $_POST['price'];
    $description = $_POST['description'];
    $categoryId  = $_POST['category_id'];

    // Ambil gambar lama
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $oldImage = $stmt->fetchColumn();

    $fileName = $oldImage;

    // Jika upload gambar baru
    if (!empty($_FILES['image']['name'])) {

        if ($oldImage && file_exists("uploads/" . $oldImage)) {
            unlink("uploads/" . $oldImage);
        }

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = time() . "_" . rand(1000,9999) . "." . $ext;
        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $fileName);
    }

    $stmt = $pdo->prepare("
        UPDATE products 
        SET name=?, price=?, category_id=?, image=?, description=? 
        WHERE id=?
    ");
    $stmt->execute([$name, $price, $categoryId, $fileName, $description, $id]);

    header("Location: index.php?page=admin_products&status=updated");
    exit;
}


// Ambil data produk
$stmt = $pdo->query("
    SELECT products.*, categories.name AS category_name
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    ORDER BY products.id DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>


<?php include 'views/layouts/admin/header.php'; ?>
<div class="container my-3">

    <h2 class="fw-bold mb-3">Manajemen Produk</h2>
    <hr>

    <!-- Notifikasi -->
    <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
        <div class="alert alert-success">Produk berhasil ditambahkan.</div>
    <?php endif; ?>

    <?php if (isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
        <div class="alert alert-danger">Produk berhasil dihapus.</div>
    <?php endif; ?>


    <!-- Form Tambah Produk -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-3">Tambah Produk Baru</h4>

            <form action="index.php?page=admin_products&action=add" 
                  method="POST" 
                  enctype="multipart/form-data" 
                  class="row g-3">

                <div class="col-md-6">
                    <label class="form-label">Nama Produk</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Produk" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Harga</label>
                    <input type="number" name="price" class="form-control" placeholder="Harga" required>
                </div>

                <div class="col-md-3">
                    <label class="form-label">Kategori</label>
                    <select name="category_id" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <?php foreach($categories as $c): ?>
                            <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="description-area" class="form-label">Description</label>
                    <textarea class="form-control" name="description" id="description-area" rows="3"></textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label">Gambar Produk</label>
                    <input type="file" name="image" class="form-control" required>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-dark px-4">Simpan Produk</button>
                </div>

            </form>
        </div>
    </div>


    <!-- Tabel Produk -->
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-3">Daftar Produk</h4>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Gambar</th>
                            <th>Nama</th>
                            <th>Kategori</th>
                            <th>Harga</th>
                            <th class="text-center">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= $i; ?></td>

                            <td>
                                <img src="uploads/<?= $p['image'] ?>" width="60" class="rounded">
                            </td>

                            <td><?= $p['name'] ?></td>
                            <td><?= $p['category_name'] ?: '-' ?></td>
                            <td>Rp <?= number_format($p['price']) ?></td>

                            <td class="text-center">
                                <button 
                                    class="btn btn-success btn-sm px-3 btn-edit"
                                    data-bs-toggle="modal"
                                    data-bs-target="#editModal"
                                    data-id="<?= $p['id'] ?>"
                                    data-name="<?= htmlspecialchars($p['name']) ?>"
                                    data-price="<?= $p['price'] ?>"
                                    data-category="<?= $p['category_id'] ?>"
                                    data-description="<?= htmlspecialchars($p['description']) ?>"
                                >
                                    Edit
                                </button>


                                <a href="index.php?page=admin_products&delete_id=<?= $p['id'] ?>" 
                                   class="btn btn-danger btn-sm px-3"
                                   onclick="return confirm('Yakin ingin menghapus produk ini?')">
                                    Hapus
                                </a>
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

<div class="modal fade" id="editModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">

            <form action="index.php?page=admin_products&action=update"
                  method="POST"
                  enctype="multipart/form-data">

                <div class="modal-header">
                    <h5 class="modal-title">Edit Produk</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body row g-3">

                    <input type="hidden" name="id" id="edit-id">

                    <div class="col-md-6">
                        <label class="form-label">Nama Produk</label>
                        <input type="text" name="name" id="edit-name" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Harga</label>
                        <input type="number" name="price" id="edit-price" class="form-control" required>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">Kategori</label>
                        <select name="category_id" id="edit-category" class="form-select" required>
                            <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= $c['name'] ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Deskripsi</label>
                        <textarea name="description" id="edit-description" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="col-12">
                        <label class="form-label">Ganti Gambar (Opsional)</label>
                        <input type="file" name="image" class="form-control">
                        <small class="text-muted">Kosongkan jika tidak ingin mengganti gambar</small>
                    </div>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-dark">Update Produk</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    document.querySelectorAll('.btn-edit').forEach(btn => {
        btn.addEventListener('click', function () {
            document.getElementById('edit-id').value = this.dataset.id;
            document.getElementById('edit-name').value = this.dataset.name;
            document.getElementById('edit-price').value = this.dataset.price;
            document.getElementById('edit-category').value = this.dataset.category;
            document.getElementById('edit-description').value = this.dataset.description;
        });
    });
</script>

<?php include 'views/layouts/admin/footer.php'; ?>

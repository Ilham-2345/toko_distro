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
    $categoryId = $_POST['category_id'];

    // Upload gambar 
    $fileName = null;

    if (!empty($_FILES['image']['name'])) {

        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $fileName = time() . "_" . rand(1000,9999) . "." . $ext; // nama unik

        move_uploaded_file($_FILES['image']['tmp_name'], "uploads/" . $fileName);
    }

    // Simpan ke DB
    $stmt = $pdo->prepare("INSERT INTO products (name, price, category_id, image) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $price, $categoryId, $fileName]);

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
                                <a href="index.php?page=admin_products&action=edit&id=<?= $p['id'] ?>" 
                                   class="btn btn-success btn-sm px-3">
                                    Edit
                                </a>

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
<?php include 'views/layouts/admin/footer.php'; ?>

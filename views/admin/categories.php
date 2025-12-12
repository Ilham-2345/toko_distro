<?php
// Hanya admin yang boleh masuk
if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
    header("Location: index.php?page=login");
    exit;
}

// LOGIC TAMBAH KATEGORI
if (isset($_GET['action']) && $_GET['action'] === 'add') {
    if (!empty($_POST['name'])) {

        $stmt = $pdo->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->execute([$_POST['name']]);

        header("Location: index.php?page=admin_categories&status=added");
        exit;
    }
}

// LOGIC HAPUS
if (isset($_GET['delete_id'])) {

    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);

    header("Location: index.php?page=admin_categories&status=deleted");
    exit;
}

// Ambil data kategori
$stmt = $pdo->query("SELECT * FROM categories ORDER BY id DESC");
$categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>



<?php include 'views/layouts/admin/header.php'; ?>

<div class="container my-3">

    <h2 class="fw-bold mb-3">Manajemen Kategori</h2>
    <hr>

    <!-- Notifikasi -->
    <?php if(isset($_GET['status']) && $_GET['status'] === 'added'): ?>
        <div class="alert alert-success">Kategori berhasil ditambahkan.</div>
    <?php endif; ?>

    <?php if(isset($_GET['status']) && $_GET['status'] === 'deleted'): ?>
        <div class="alert alert-danger">Kategori berhasil dihapus.</div>
    <?php endif; ?>

    <!-- Form Tambah Kategori -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h4 class="mb-3">Tambah Kategori Baru</h4>

            <form action="index.php?page=admin_categories&action=add" 
                  method="POST"
                  class="row g-3">

                <div class="col-6">
                    <label class="form-label">Nama Kategori</label>
                    <input type="text" name="name" class="form-control" placeholder="Nama Kategori" required>
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-dark px-4">Simpan</button>
                </div>

            </form>
        </div>
    </div>

    <!-- Tabel Kategori -->
    <div class="card shadow-sm">
        <div class="card-body">

            <h4 class="mb-3">Daftar Kategori</h4>

            <div class="table-responsive">
                <table class="table table-striped align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="60">ID</th>
                            <th>Nama</th>
                            <th class="text-center" width="200">Aksi</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach($categories as $c): ?>
                        <tr>
                            <td><?= $i ?></td>
                            <td><?= $c['name'] ?></td>

                            <td class="text-center">

                                <!-- Tombol Edit (Nanti bisa kamu buatkan halamannya) -->
                                <a href="index.php?page=admin_categories_edit&id=<?= $c['id'] ?>" 
                                   class="btn btn-success btn-sm px-3">
                                    Edit
                                </a>

                                <!-- Tombol Hapus -->
                                <a href="index.php?page=admin_categories&delete_id=<?= $c['id'] ?>" 
                                   class="btn btn-danger btn-sm px-3"
                                   onclick="return confirm('Yakin ingin menghapus kategori ini?')">
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

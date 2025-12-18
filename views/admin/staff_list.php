<?php include 'views/layouts/admin/header.php'; ?>
<div class="container my-4">

    <h3 class="fw-bold mb-4">Manajemen Staff</h3>

    <!-- FORM TAMBAH STAFF -->
    <div class="card mb-4">
        <div class="card-header fw-semibold">Tambah Staff</div>
        <div class="card-body">

            <form method="POST" action="index.php?page=admin_staff&action=store">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label>Nama</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-4">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>
                </div>

                <button class="btn btn-dark mt-3">Tambah Staff</button>
            </form>

        </div>
    </div>

    <!-- TABLE STAFF -->
    <div class="card">
        <div class="card-header fw-semibold">Daftar Staff</div>
        <div class="card-body">

            <table class="table">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Tanggal Dibuat</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>

                <?php if (empty($staffs)): ?>
                    <tr>
                        <td colspan="5" class="text-center p-4">
                            Belum ada staff
                        </td>
                    </tr>
                <?php endif; ?>

                <?php foreach ($staffs as $s): ?>
                    <tr>
                        <td>#<?= $s['id'] ?></td>
                        <td><?= $s['name'] ?></td>
                        <td><?= $s['email'] ?></td>
                        <td><?= date('d/m/Y', strtotime($s['created_at'])) ?></td>
                        <td>
                            <a 
                                href="index.php?page=admin_staff&action=delete&id=<?= $s['id'] ?>"
                                class="btn btn-danger btn-sm"
                                onclick="return confirm('Hapus staff ini?')"
                            >
                                Hapus
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>

                </tbody>
            </table>

        </div>
    </div>

</div>
<?php include 'views/layouts/admin/footer.php'; ?>
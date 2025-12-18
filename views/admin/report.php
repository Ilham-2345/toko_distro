<?php include 'views/layouts/admin/header.php'; ?>

<div class="container my-4">

<h3 class="fw-bold mb-3">Laporan Penjualan</h3>

<!-- FILTER -->
<form class="card p-3 mb-4" method="GET">
    <input type="hidden" name="page" value="admin_report">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label>Dari Tanggal</label>
            <input type="date" name="from" class="form-control" value="<?= $_GET['from'] ?? '' ?>">
        </div>
        <div class="col-md-3">
            <label>Sampai Tanggal</label>
            <input type="date" name="to" class="form-control" value="<?= $_GET['to'] ?? '' ?>">
        </div>
        <div class="col-md-2">
            <button class="btn btn-dark w-100">Tampilkan</button>
        </div>
    </div>
</form>

<!-- SUMMARY -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card p-3">
            <small>Total Pesanan</small>
            <h4 class="fw-bold"><?= $totalOrders ?></h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <small>Pendapatan</small>
            <h4 class="fw-bold">Rp <?= number_format($totalRevenue) ?></h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3">
            <small>Produk Terlaris</small>
            <h5 class="fw-bold">
                <?= $bestProduct ? $bestProduct['name'] : '-' ?>
            </h5>
        </div>
    </div>
</div>

<!-- DETAIL -->
<div class="card">
    <div class="card-header fw-semibold">Detail Penjualan</div>
    <div class="card-body">

        <table class="table mb-0">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Tanggal</th>
                    <th>Invoice</th>
                    <th>Total Items</th>
                    <th>Total Bayar</th>
                </tr>
            </thead>
            <tbody>

            <?php if (empty($details)): ?>
                <tr>
                    <td colspan="5" class="text-center p-4">Data kosong</td>
                </tr>
            <?php endif; ?>

            <?php foreach ($details as $d): ?>
                <tr>
                    <td>#<?= $d['id'] ?></td>
                    <td><?= date('d/m/Y', strtotime($d['created_at'])) ?></td>
                    <td><?= $d['invoice_number'] ?></td>
                    <td><?= $d['total_items'] ?></td>
                    <td>Rp <?= number_format($d['total_price']) ?></td>
                </tr>
            <?php endforeach; ?>

            </tbody>
        </table>

    </div>
</div>

</div>

<?php include 'views/layouts/admin/footer.php'; ?>

<?php include 'views/layouts/admin/header.php'; ?>

<div class="container my-4">

    <h3 class="fw-bold mb-4">Detail Pesanan #<?= $order['id'] ?></h3>

    <!-- INFORMASI -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="border rounded p-3">
                <h6 class="fw-bold mb-3">Informasi Pelanggan</h6>
                <p class="mb-1">Nama: <?= $order['customer_name'] ?></p>
                <p class="mb-1">Alamat: <?= $order['address'] ?></p>
                <p class="mb-0">Telepon: <?= $order['phone'] ?></p>
            </div>
        </div>

        <div class="col-md-6">
            <div class="border rounded p-3">
                <h6 class="fw-bold mb-3">Informasi Pesanan</h6>
                <p class="mb-1">Tanggal: <?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
                <p class="mb-1">
                    Status:
                    <span class="badge bg-info"><?= ucfirst($order['status']) ?></span>
                </p>
                <p class="mb-1">Metode Pembayaran: <?= $order['payment_method'] ?? '-' ?></p>
                <p class="mb-0">Total: <strong>Rp <?= number_format($order['total_price']) ?></strong></p>
            </div>
        </div>
    </div>

    <!-- DAFTAR ITEM -->
    <div class="border rounded p-3">
        <h6 class="fw-bold mb-3">Daftar Pesanan</h6>

        <table class="table align-middle">
            <thead class="table-dark">
                <tr>
                    <th>Gambar</th>
                    <th>Nama Produk</th>
                    <th>Size</th>
                    <th>Harga</th>
                    <th>Qty</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>

                <?php 
                $subtotal = 0;
                foreach ($orderItems as $item): 
                    $itemSubtotal = $item['price'] * $item['quantity'];
                    $subtotal += $itemSubtotal;
                ?>
                <tr>
                    <td width="80">
                        <img src="uploads/<?= $item['image'] ?>" width="60">
                    </td>
                    <td><?= $item['product_name'] ?></td>
                    <td><?= $item['size_name'] ?></td>
                    <td>Rp <?= number_format($item['price']) ?></td>
                    <td><?= $item['quantity'] ?></td>
                    <td>Rp <?= number_format($itemSubtotal) ?></td>
                </tr>
                <?php endforeach; ?>

            </tbody>
        </table>

        <!-- TOTAL -->
        <div class="text-end mt-3">
            <p class="mb-1">Subtotal: Rp <?= number_format($subtotal) ?></p>
            <h5 class="fw-bold">Total Akhir: Rp <?= number_format($order['total_price']) ?></h5>
        </div>

        <!-- ACTION -->
        <div class="d-flex justify-content-end gap-2 mt-3">
            <a href="index.php?page=admin_orders" class="btn btn-secondary">Kembali</a>
        </div>
    </div>

</div>

<?php include 'views/layouts/admin/footer.php'; ?>

<?php include 'views/layouts/header.php'; ?>

<style>
.badge-status {
    padding: 5px 12px;
    border-radius: 5px;
    background: #ffc107;
    font-weight: bold;
}
.table th {
    background: #000;
    color: #fff;
}
</style>

<div class="container">
    <section class="mb-3">
        <div class="invoice-box">

            <div class="invoice-header">
                <div>
                    <h3>Invoice</h3>
                    <p><strong>No Invoice:</strong> <?= $order['invoice_number'] ?></p>
                    <p><strong>Tanggal:</strong> <?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
                </div>
                <div>
                    <span class="badge-status"><?= strtoupper($order['status']) ?></span>
                </div>
            </div>

            <hr>

            <h5>Informasi Pelanggan</h5>
            <p>
                <strong>Nama:</strong> <?= $order['name'] ?><br>
                <strong>Telepon:</strong> <?= $order['phone'] ?><br>
                <strong>Alamat:</strong> <?= $order['address'] ?>
            </p>

            <hr>

            <h5>Detail Pesanan</h5>

            <table class="table">
                <thead>
                    <tr>
                        <th>Produk</th>
                        <th>Size</th>
                        <th>Harga</th>
                        <th>Qty</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($items as $item): ?>
                    <tr>
                        <td>
                            <img src="uploads/<?= $item['image'] ?>" width="60"><br>
                            <?= $item['product_name'] ?>
                        </td>
                        <td><?= $item['size_name'] ?></td>
                        <td>Rp <?= number_format($item['price']) ?></td>
                        <td><?= $item['quantity'] ?></td>
                        <td>Rp <?= number_format($item['price'] * $item['quantity']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="text-end">
                <h5>Total Pembayaran: Rp <?= number_format($order['total_price']) ?></h5>
                <p>Metode Pembayaran: <strong><?= $order['payment_method'] ?></strong></p>
            </div>

            <hr>

            <a href="index.php?page=orders" class="btn btn-dark btn-sm">
                Kembali
            </a>

        </div>
    </section>
</div>

<?php include 'views/layouts/footer.php'; ?>

<?php include 'views/layouts/header.php'; ?>

<style>
    .invoice-box {
        max-width: 900px;
        margin: 60px auto;
        border: 2px solid #000;
        padding: 30px;
    }
    .invoice-header {
        display: flex;
        justify-content: space-between;
        margin-bottom: 20px;
    }
    .table th {
        background: #000;
        color: #fff;
    }
</style>

<div class="invoice-box">

    <div class="invoice-header">
        <div>
            <h3>Checkout</h3>
            <p><strong>Tanggal:</strong> <?= date('d/m/Y') ?></p>
        </div>
    </div>

    <hr>

    <h5>Informasi Pelanggan</h5>
    <p>
        <strong>Nama:</strong> <?= $user['name'] ?><br>
        <strong>Telepon:</strong> <?= $user['phone'] ?><br>
        <strong>Alamat:</strong> <?= $user['address'] ?>
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
                    <?= $item['name'] ?>
                </td>
                <td><?= $item['size'] ?></td>
                <td>Rp <?= number_format($item['price']) ?></td>
                <td><?= $item['qty'] ?></td>
                <td>Rp <?= number_format($item['subtotal']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="text-end">
        <h5>Total Pembayaran: Rp <?= number_format($total) ?></h5>
    </div>

    <hr>

    <!-- FORM CHECKOUT PAYMENT -->
    <button id="pay-button" type="button" class="btn btn-dark">
        <i class="bi bi-shield-check"></i> Bayar
    </button>

    <a href="index.php?page=cart" class="btn btn-outline-secondary ms-2">
        Kembali ke Cart
    </a>
</div>

<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key=""></script>
<script type="text/javascript">
    document.getElementById('pay-button').onclick = function(){
        // SnapToken acquired from previous step
        snap.pay('<?= $snapToken ?>', {
            // Optional
            onSuccess: function(result){
                window.location = "index.php?page=cart&action=success&order_id=" + result.order_id;
            },
            // Optional
            onPending: function(result){
                // kirim invoice/order_id ke server via AJAX
                fetch('index.php?page=cart&action=pending&order_id=' + result.order_id)
                .then(response => response.text())
                .then(data => {
                    document.getElementById('status').innerText = "Menunggu pembayaran...";
                });
            },
            // Optional
            onError: function(result){
                console.info('error' + ' ' + result);
            }
        });
    };
</script>

<?php include 'views/layouts/footer.php'; ?>
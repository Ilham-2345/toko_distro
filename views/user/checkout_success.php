<?php include 'views/layouts/header.php'; ?>

<style>
    .success-box { max-width: 600px; margin: 80px auto; padding: 40px; text-align: center; border: 2px solid #000; }
    .success-icon { font-size: 4rem; margin-bottom: 20px; }
    .bank-details { background: #f4f4f4; padding: 20px; margin: 20px 0; text-align: left; }
    .btn-wa { background: #25D366; color: white; padding: 10px 20px; text-decoration: none; display: inline-block; font-weight: bold; margin-top: 10px; }
</style>

<div class="success-box">
    <div class="success-icon">✓</div>
    <h1>Pesanan Berhasil Dibuat!</h1>
    <p>Terima kasih sudah berbelanja. Mohon selesaikan pembayaran agar pesanan segera diproses.</p>

    <div class="bank-details">
        <p><strong>Invoice ID:</strong> <?= $_GET['inv'] ?></p>
        <p><strong>Total Pembayaran:</strong> Rp <?= number_format($_GET['total']) ?></p>
        <hr>
        <p>Silakan transfer ke rekening berikut:</p>
        <h3>BCA: 123-456-7890</h3>
        <p>A/n PT. Thanks Jokowi Indonesia</p>
    </div>

    <p>Sudah Transfer? Konfirmasi via WhatsApp:</p>
    <a href="https://wa.me/628123456789?text=Halo%20Admin,%20saya%20sudah%20transfer%20untuk%20invoice%20<?= $_GET['inv'] ?>" class="btn-wa" target="_blank">
        Konfirmasi Pembayaran
    </a>
    <br><br>
    <a href="index.php?page=shop" style="text-decoration: underline; color: black;">Kembali ke Beranda</a>
</div>

<?php include 'views/layouts/footer.php'; ?>
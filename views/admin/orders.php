<?php
// Ambil semua order
$stmt = $pdo->query("SELECT orders.*, users.name as customer FROM orders JOIN users ON orders.user_id = users.id ORDER BY created_at DESC");
$orders = $stmt->fetchAll();

// Logic Update Status
if(isset($_POST['update_status'])){
    $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->execute([$_POST['status'], $_POST['order_id']]);
    header("Location: index.php?page=admin_orders");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Orders</title>
    <style>
        /* Gunakan style yg sama dengan produk */
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 10px; }
        .badge { padding: 5px; color: white; border-radius: 4px; font-size: 12px;}
        .pending { background: orange; } .paid { background: blue; } .shipped { background: purple; }
    </style>
</head>
<body>
    <h1>Daftar Pesanan Masuk</h1>
    <a href="index.php?page=admin_products">Kembali ke Produk</a>
    <br><br>
    
    <table>
        <thead>
            <tr>
                <th>Invoice</th>
                <th>Pelanggan</th>
                <th>Total</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($orders as $o): ?>
            <tr>
                <td>#<?= $o['invoice_number'] ?></td>
                <td><?= $o['customer'] ?></td>
                <td>Rp <?= number_format($o['total_price']) ?></td>
                <td><span class="badge <?= $o['status'] ?>"><?= strtoupper($o['status']) ?></span></td>
                <td>
                    <form method="POST" style="display:inline;">
                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                        <select name="status" onchange="this.form.submit()">
                            <option value="pending" <?= $o['status']=='pending'?'selected':'' ?>>Pending</option>
                            <option value="paid" <?= $o['status']=='paid'?'selected':'' ?>>Lunas</option>
                            <option value="shipped" <?= $o['status']=='shipped'?'selected':'' ?>>Dikirim</option>
                        </select>
                    </form>
                    <a href="index.php?page=invoice&id=<?= $o['id'] ?>" target="_blank" style="margin-left:10px;">Cetak Invoice</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
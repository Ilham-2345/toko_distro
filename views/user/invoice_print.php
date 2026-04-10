<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
        }
        .header {
            display: flex;
            justify-content: space-between;
        }
        .badge {
            padding: 5px 10px;
            background: #ffc107;
            font-weight: bold;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th {
            background: #000;
            color: #fff;
            padding: 5px;
        }
        td {
            padding: 5px;
        }
        .text-end {
            text-align: right;
        }
    </style>
</head>
<body>

<div class="header">
    <div>
        <h1>Distro Store&reg;</h1>
        <h2>Invoice</h2>
        <p>No: <?= $order['invoice_number'] ?></p>
        <p>Tanggal: <?= date('d/m/Y', strtotime($order['created_at'])) ?></p>
    </div>
    <div>
        <span class="badge"><?= strtoupper($order['status']) ?></span>
    </div>
</div>

<hr>

<h4>Informasi Pelanggan</h4>
<p>
    Nama: <?= $order['name'] ?><br>
    Telepon: <?= $order['phone'] ?><br>
    Alamat: <?= $order['address'] ?>
</p>

<hr>

<h4>Detail Pesanan</h4>

<table>
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
    <h4>Total: Rp <?= number_format($order['total_price']) ?></h4>
    <p>Metode: <strong><?= $order['payment_method'] ?></strong></p>
</div>

</body>
</html>
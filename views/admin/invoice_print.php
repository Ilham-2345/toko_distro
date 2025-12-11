<?php
// Ambil detail order
$id = $_GET['id'];
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

// Ambil item produk dalam order ini
$stmt = $pdo->prepare("SELECT order_items.*, products.name FROM order_items JOIN products ON order_items.product_id = products.id WHERE order_id = ?");
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice #<?= $order['invoice_number'] ?></title>
    <style>
        body { font-family: 'Courier New', monospace; max-width: 600px; margin: auto; padding: 20px; border: 1px solid #eee; }
        .header { text-align: center; border-bottom: 2px dashed #000; padding-bottom: 20px; margin-bottom: 20px; }
        .meta { display: flex; justify-content: space-between; margin-bottom: 20px; }
        table { width: 100%; border-collapse: collapse; }
        th { text-align: left; border-bottom: 1px solid #000; }
        td { padding: 5px 0; }
        .total { font-weight: bold; border-top: 1px solid #000; padding-top: 10px; margin-top: 10px; text-align: right; }
    </style>
</head>
<body onload="window.print()">
    <div class="header">
        <h2>THANKSJOKOWI® STORE</h2>
        <p>Jl. Jade Chamber No.01, Guizhong City</p>
    </div>

    <div class="meta">
        <div>
            <strong>Invoice:</strong> #<?= $order['invoice_number'] ?><br>
            <strong>Date:</strong> <?= date('d/m/Y', strtotime($order['created_at'])) ?>
        </div>
        <div>
            <strong>Status:</strong> <?= strtoupper($order['status']) ?>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Item</th>
                <th>Qty</th>
                <th>Price</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($items as $item): ?>
            <tr>
                <td><?= $item['name'] ?></td>
                <td><?= $item['quantity'] ?></td>
                <td><?= number_format($item['price']) ?></td>
                <td><?= number_format($item['price'] * $item['quantity']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="total">
        TOTAL BAYAR: Rp <?= number_format($order['total_price']) ?>
    </div>
    
    <p style="text-align: center; margin-top: 40px; font-size: 12px;">Terima kasih telah berbelanja!</p>
</body>
</html>
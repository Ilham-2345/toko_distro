<?php
// Pastikan hanya admin yg bisa akses
if($_SESSION['user']['role'] != 'admin'){ header("Location: index.php?page=login"); exit; }

// Logic Hapus
if(isset($_GET['delete_id'])){
    $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $stmt->execute([$_GET['delete_id']]);
    header("Location: index.php?page=admin_products");
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Products</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 10px; text-align: left; }
        th { background-color: black; color: white; }
        .btn { padding: 5px 10px; text-decoration: none; color: white; background: black; font-size: 12px; }
        .btn-red { background: red; }
        .form-add { background: #f9f9f9; padding: 20px; border: 1px solid #ddd; margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Manajemen Produk</h1>
    <a href="index.php?page=admin_orders">Ke Halaman Order</a> | 
    <a href="index.php?page=auth&action=logout">Logout</a>
    <hr>

    <div class="form-add">
        <h3>Tambah Produk Baru</h3>
        <form action="index.php?page=admin_products&action=add" method="POST" enctype="multipart/form-data">
            <input type="text" name="name" placeholder="Nama Produk" required>
            <input type="number" name="price" placeholder="Harga" required>
            <select name="category_id">
                <option value="1">T-Shirt</option> <option value="2">Jacket</option>
            </select>
            <input type="file" name="image" required>
            <button type="submit" class="btn">Simpan Produk</button>
        </form>
    </div>

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Gambar</th>
                <th>Nama</th>
                <th>Harga</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($products as $p): ?>
            <tr>
                <td><?= $p['id'] ?></td>
                <td><img src="uploads/<?= $p['image'] ?>" width="50"></td>
                <td><?= $p['name'] ?></td>
                <td>Rp <?= number_format($p['price']) ?></td>
                <td>
                    <a href="#" class="btn">Edit</a>
                    <a href="index.php?page=admin_products&delete_id=<?= $p['id'] ?>" class="btn btn-red" onclick="return confirm('Hapus?')">Hapus</a>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
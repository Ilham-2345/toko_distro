<?php
$where = "WHERE 1=1";
$params = [];

// Filter Search
if (isset($_GET['search'])) {
    $where .= " AND name LIKE ?";
    $params[] = "%" . $_GET['search'] . "%";
}

// Filter Category
if (isset($_GET['cat'])) {
    $where .= " AND category_id = ?";
    $params[] = $_GET['cat'];
}

// Filter Price
if (isset($_GET['sort'])) {
    $order = ($_GET['sort'] == 'low') ? "ORDER BY price ASC" : "ORDER BY price DESC";
} else {
    $order = "ORDER BY id DESC";
}

$sql = "SELECT * FROM products $where $order";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();
?>

<div class="filter-bar">
    <form action="" method="GET">
        <input type="hidden" name="page" value="shop">
        <input type="text" name="search" placeholder="Cari produk...">
        <select name="sort">
            <option value="new">Terbaru</option>
            <option value="low">Termurah</option>
            <option value="high">Termahal</option>
        </select>
        <button type="submit">Filter</button>
    </form>
</div>

<div class="product-grid">
    <?php foreach($products as $p): ?>
        <div class="card">
            <img src="uploads/<?= $p['image'] ?>" alt="<?= $p['name'] ?>">
            <h3><?= $p['name'] ?></h3>
            <p>Rp <?= number_format($p['price']) ?></p>
            <a href="index.php?page=cart&action=add&id=<?= $p['id'] ?>">Add to Cart</a>
        </div>
    <?php endforeach; ?>
</div>
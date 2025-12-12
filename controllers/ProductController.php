<?php
// READ Data
$stmt = $pdo->query("SELECT products.*, categories.name as cat_name FROM products JOIN categories ON products.category_id = categories.id");
$products = $stmt->fetchAll();

// Load View
include 'views/admin/products.php';
?>
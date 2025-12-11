<?php
// Logic sederhana langsung di dalam file controller untuk simplifikasi
if ($_SERVER['REQUEST_METHOD'] == 'POST' && $action == 'add') {
    $name = $_POST['name'];
    $price = $_POST['price'];
    $category = $_POST['category_id'];
    
    // Upload Gambar
    $target_dir = "uploads/";
    $image_name = time() . "_" . basename($_FILES["image"]["name"]);
    move_uploaded_file($_FILES["image"]["tmp_name"], $target_dir . $image_name);

    $sql = "INSERT INTO products (category_id, name, price, image) VALUES (?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$category, $name, $price, $image_name]);
    
    header("Location: index.php?page=admin_products");
}

// READ Data
$stmt = $pdo->query("SELECT products.*, categories.name as cat_name FROM products JOIN categories ON products.category_id = categories.id");
$products = $stmt->fetchAll();

// Load View
include 'views/admin/products.php';
?>
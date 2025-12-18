<?php
require '../../config/database.php';

$productId = $_GET['product_id'];

$stmt = $pdo->prepare("
    SELECT ps.size_id, s.name, ps.stock
    FROM product_sizes ps
    JOIN sizes s ON ps.size_id = s.id
    WHERE ps.product_id = ?
");
$stmt->execute([$productId]);

$sizes = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($sizes as $s):
?>
<div class="mb-3 row">
    <label class="col-sm-2 col-form-label"><?= $s['name'] ?></label>
    <div class="col-sm-10">
        <input type="number"
               name="stocks[<?= $s['size_id'] ?>]"
               value="<?= $s['stock'] ?>"
               class="form-control"
               min="0">
    </div>
</div>
<?php endforeach; ?>

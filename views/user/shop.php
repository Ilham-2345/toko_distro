<?php 

$stmt = $pdo->query("
    SELECT products.*, categories.name AS category_name
    FROM products
    LEFT JOIN categories ON products.category_id = categories.id
    ORDER BY products.id DESC
");
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil kategori untuk sidebar
$catStmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
$categories = $catStmt->fetchAll(PDO::FETCH_ASSOC);


// json
$productsJson = json_encode($products);
$categoriesJson = json_encode($categories);
?>

<?php include 'views/layouts/header.php'; ?>

<div class="container shop my-5"  x-data="productFilter(<?= htmlspecialchars($productsJson) ?>)">
    <h2 class="fw-bold mb-4">Katalog Produk</h2>

    <div class="row g-4">

        <!-- SIDEBAR FILTER -->
        <div class="col-md-3">

            <div class="card p-3 shadow-sm mb-3">

                <!-- SEARCH -->
                <div class="mb-3">
                    <div class="input-group">
                        <span class="input-group-text bg-white">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" placeholder="Search" x-model="search">
                    </div>
                </div>

                <hr>

                <!-- KATEGORI -->
                <h6 class="fw-bold mb-2">Kategori</h6>
                <?php foreach ($categories as $c): ?>
                    <div class="form-check mb-1">
                        <input class="form-check-input"
                            type="checkbox"
                            value="<?= $c['name'] ?>"
                            x-model="selectedCategories">
                        <label class="form-check-label">
                            <?= $c['name'] ?>
                        </label>
                    </div>
                <?php endforeach; ?>

                <hr>

                <!-- AVAILABILITY -->
                <h6 class="fw-bold mb-2">Availability</h6>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="all" x-model="availability">
                    <label class="form-check-label">All</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="in" x-model="availability">
                    <label class="form-check-label">In Stock</label>
                </div>

                <hr>

                <!-- PRICE -->
                <h6 class="fw-bold mb-2">Price</h6>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="under250" x-model="priceRange">
                    <label class="form-check-label">Under Rp 250,000</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="250to500" x-model="priceRange">
                    <label class="form-check-label">Rp 250,000 - Rp 500,000</label>
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="radio" value="500to750" x-model="priceRange">
                    <label class="form-check-label">Rp 500,000 - Rp 750,000</label>
                </div>
            </div>
        </div>

        <!-- GRID PRODUK -->
        <div class="col-md-9">
            <div class="row g-4">
                <template x-for="product in filteredProducts" :key="product.id">
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm border-0 product-card">

                            <div style="height: 280px; overflow: hidden">
                                <img :src="'uploads/' + product.image"
                                    class="card-img-top"
                                    :alt="product.name">
                            </div>

                            <div class="card-body">
                                <h6 class="fw-semibold mb-1" x-text="product.name"></h6>
                                <small class="text-muted" x-text="product.category_name"></small>

                                <p class="mt-2 mb-0 fw-semibold">
                                    Rp <span x-text="Number(product.price).toLocaleString()"></span>
                                </p>
                            </div>

                            <div class="card-footer bg-white border-0 text-end">
                                <a :href="'index.php?page=product_detail&id=' + product.id"
                                class="text-decoration-none text-dark">
                                    <i class="bi bi-caret-right-fill"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                </template>

                <!-- Jika kosong -->
                <div x-show="filteredProducts.length === 0" class="text-center mt-5">
                    <p class="text-muted">Produk tidak ditemukan</p>
                </div>
            </div>

        </div>
    </div>
</div>

<script>
function productFilter(products) {
    return {
        products: products,
        search: '',
        selectedCategories: [],
        availability: 'all',
        priceRange: '',

        get filteredProducts() {
            return this.products.filter(p => {

                // SEARCH
                if (this.search &&
                    !p.name.toLowerCase().includes(this.search.toLowerCase())) {
                    return false;
                }

                // CATEGORY
                if (this.selectedCategories.length &&
                    !this.selectedCategories.includes(p.category_name)) {
                    return false;
                }

                // AVAILABILITY
                if (this.availability === 'in' && p.stock <= 0) {
                    return false;
                }

                // PRICE
                if (this.priceRange === 'under250' && p.price >= 250000) return false;
                if (this.priceRange === '250to500' &&
                    (p.price < 250000 || p.price > 500000)) return false;
                if (this.priceRange === '500to750' &&
                    (p.price < 500000 || p.price > 750000)) return false;

                return true;
            });
        }
    }
}
</script>
<?php include 'views/layouts/footer.php'; ?>

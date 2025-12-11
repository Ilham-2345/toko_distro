<?php include 'views/layouts/header.php'; ?>

<style>
    .cart-container { max-width: 1000px; margin: 40px auto; padding: 20px; font-family: 'Inter', sans-serif; }
    h1 { font-family: 'Teko', sans-serif; font-size: 3rem; margin-bottom: 20px; text-transform: uppercase; }
    
    /* Table Styles */
    .cart-table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
    .cart-table th { text-align: left; padding: 15px; border-bottom: 2px solid #000; text-transform: uppercase; letter-spacing: 1px; }
    .cart-table td { padding: 15px; border-bottom: 1px solid #eee; vertical-align: middle; }
    
    .product-info { display: flex; align-items: center; gap: 15px; }
    .product-info img { width: 60px; height: 60px; object-fit: cover; border: 1px solid #ddd; }
    
    .qty-control a { text-decoration: none; display: inline-block; width: 25px; height: 25px; line-height: 25px; text-align: center; background: #eee; color: #000; font-weight: bold; }
    .qty-control span { display: inline-block; width: 30px; text-align: center; }
    
    .btn-remove { color: red; text-decoration: none; font-size: 0.9rem; }
    
    /* Summary Box */
    .cart-summary { background: #f9f9f9; padding: 30px; width: 300px; margin-left: auto; border: 1px solid #ddd; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: 15px; font-size: 1.1rem; }
    .total-row { font-weight: bold; border-top: 2px solid #000; padding-top: 15px; font-size: 1.3rem; }
    
    .btn-checkout { display: block; width: 100%; background: #000; color: #fff; text-align: center; padding: 15px; text-decoration: none; text-transform: uppercase; font-weight: bold; letter-spacing: 1px; margin-top: 20px; transition: 0.3s; }
    .btn-checkout:hover { background: #333; }
</style>

<div class="cart-container">
    <h1>Shopping Cart</h1>

    <?php if (empty($cartItems)): ?>
        <p style="text-align: center; padding: 50px; background: #f9f9f9;">
            Keranjang kamu kosong. <br><br>
            <a href="index.php?page=shop" style="color: black; font-weight: bold;">Mulai Belanja &rarr;</a>
        </p>
    <?php else: ?>
        
        <table class="cart-table">
            <thead>
                <tr>
                    <th width="50%">Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php 
                $grandTotal = 0;
                foreach ($cartItems as $item): 
                    $qty = $_SESSION['cart'][$item['id']];
                    $subtotal = $item['price'] * $qty;
                    $grandTotal += $subtotal;
                ?>
                <tr>
                    <td>
                        <div class="product-info">
                            <img src="uploads/<?= $item['image'] ?>" alt="Img">
                            <div>
                                <strong><?= $item['name'] ?></strong><br>
                                <small>ID: #<?= $item['id'] ?></small>
                            </div>
                        </div>
                    </td>
                    <td>Rp <?= number_format($item['price']) ?></td>
                    <td>
                        <div class="qty-control">
                            <a href="index.php?page=cart&action=update&type=minus&id=<?= $item['id'] ?>">-</a>
                            <span><?= $qty ?></span>
                            <a href="index.php?page=cart&action=update&type=plus&id=<?= $item['id'] ?>">+</a>
                        </div>
                    </td>
                    <td>Rp <?= number_format($subtotal) ?></td>
                    <td>
                        <a href="index.php?page=cart&action=delete&id=<?= $item['id'] ?>" class="btn-remove" onclick="return confirm('Hapus item ini?')">× Remove</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <div class="cart-summary">
            <div class="summary-row">
                <span>Subtotal</span>
                <span>Rp <?= number_format($grandTotal) ?></span>
            </div>
            <div class="summary-row total-row">
                <span>TOTAL</span>
                <span>Rp <?= number_format($grandTotal) ?></span>
            </div>
            
            <?php if(isset($_SESSION['user'])): ?>
                <a href="index.php?page=cart&action=checkout" class="btn-checkout">Checkout Now</a>
            <?php else: ?>
                <a href="index.php?page=login" class="btn-checkout">Login to Checkout</a>
            <?php endif; ?>
        </div>

    <?php endif; ?>
</div>

<?php include 'views/layouts/footer.php'; ?>
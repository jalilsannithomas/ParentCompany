<?php
session_start();
require_once('../settings/core.php');

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('header.php');
include("../controllers/cart_controller.php");

$cart_items = get_cart_items_ctr($_SESSION['user_id']);
if ($cart_items === false) {
    $cart_items = array();
}

$total = get_cart_total_ctr($_SESSION['user_id']);
if ($total === false) {
    $total = array('total' => 0);
}
?>

<div class="section" id="cart-page">
    <div class="cart-header">
        <h1>Cart</h1>
        <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if(empty($cart_items)): ?>
        <div class="empty-cart">
            <p>Your cart is empty</p>
            <a href="../index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <?php foreach($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="product-image">
                        <?php if (!empty($item['product_image'])): ?>
                            <img src="../images/products/<?= htmlspecialchars($item['product_image']) ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                        <?php else: ?>
                            <img src="../images/placeholder.png" alt="Product image not available">
                        <?php endif; ?>
                    </div>
                    <div class="product-info">
                        <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                        <p>Size: <?= htmlspecialchars($item['size']) ?> | Color: <?= htmlspecialchars($item['color']) ?> | Price: $<?= number_format($item['product_price'], 2) ?></p>
                        
                        <div class="quantity-controls">
                            <form action="../actions/update_cart.php" method="POST" class="update-form">
                                <input type="hidden" name="product_id" value="<?= $item['p_id'] ?>">
                                <input type="hidden" name="update_cart" value="1">
                                <div class="quantity-wrapper">
                                    <button type="button" class="qty-btn minus" onclick="updateQty(this, -1)">-</button>
                                    <input type="number" name="quantity" value="<?= $item['qty'] ?>" min="1" max="10" class="qty-input" onchange="this.form.submit()">
                                    <button type="button" class="qty-btn plus" onclick="updateQty(this, 1)">+</button>
                                </div>
                            </form>
                            <form action="../actions/remove_from_cart.php" method="POST" class="remove-form" onsubmit="return confirm('Are you sure you want to remove this item?');">
                                <input type="hidden" name="product_id" value="<?= $item['p_id'] ?>">
                                <button type="submit" class="btn-remove">Remove</button>
                            </form>
                        </div>
                    </div>
                    <div class="product-total">
                        $<?= number_format($item['product_price'] * $item['qty'], 2) ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="cart-summary">
                <div class="total">Cart Total: $<?= number_format($total['total'], 2) ?></div>
                <a href="checkout.php" class="btn-checkout">Proceed to Checkout</a>
            </div>
        </div>
    <?php endif; ?>
</div>

<style>
.cart-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 20px;
}

.cart-item {
    display: flex;
    align-items: center;
    padding: 20px;
    margin-bottom: 20px;
    background: #2a2a2a;
    border-radius: 8px;
}

.product-image {
    width: 120px;
    height: 120px;
    margin-right: 20px;
}

.product-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 4px;
}

.product-info {
    flex: 1;
}

.product-info h3 {
    margin: 0 0 10px 0;
}

.quantity-controls {
    display: flex;
    align-items: center;
    margin-top: 15px;
    gap: 20px;
}

.quantity-wrapper {
    display: flex;
    align-items: center;
    background: #333;
    border-radius: 4px;
}

.qty-btn {
    padding: 8px 12px;
    background: #444;
    border: none;
    color: white;
    cursor: pointer;
}

.qty-btn:hover {
    background: #555;
}

.qty-input {
    width: 50px;
    text-align: center;
    border: none;
    background: #333;
    color: white;
    padding: 8px;
}

.btn-remove {
    padding: 8px 16px;
    background: #ff4444;
    border: none;
    border-radius: 4px;
    color: white;
    cursor: pointer;
}

.btn-remove:hover {
    background: #ff6666;
}

.product-total {
    font-size: 1.2em;
    font-weight: bold;
    margin-left: 20px;
}

.cart-summary {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    gap: 20px;
    margin-top: 30px;
}

.total {
    font-size: 1.4em;
    font-weight: bold;
}

.btn-checkout {
    padding: 12px 24px;
    background: #8a2be2;
    color: white;
    text-decoration: none;
    border-radius: 4px;
}

.btn-checkout:hover {
    background: #9a3cf1;
}
</style>

<script>
function updateQty(btn, change) {
    const input = btn.parentElement.querySelector('.qty-input');
    let newVal = parseInt(input.value) + change;
    if (newVal >= 1 && newVal <= 10) {
        input.value = newVal;
        input.form.submit();
    }
}
</script>

<?php include('footer.php'); ?>
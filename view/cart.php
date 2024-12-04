<?php
session_start();
require_once('../settings/core.php');
ob_clean();

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
        <h1 class="page-title">Cart</h1>
        <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <?php if(empty($cart_items)): ?>
        <div class="empty-cart">
            <p>Your cart is empty</p>
            <a href="../index.php" class="btn btn-primary">Continue Shopping</a>
        </div>
    <?php else: ?>
        <div class="cart-container">
            <div class="cart-items">
                <?php foreach($cart_items as $item): ?>
                    <div class="cart-item">
                        <div class="item-image">
                            <?php
                            $imagePath = '../images/products/' . $item['product_image'];
                            ?>
                            <img src="<?= $imagePath ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                        </div>
                        <div class="item-details">
                            <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                            <div class="item-specs">
                                <span class="spec">Size: <?= htmlspecialchars($item['size']) ?></span>
                                <span class="spec">Color: <?= htmlspecialchars($item['color']) ?></span>
                            </div>
                            <div class="item-price">$<?= number_format($item['product_price'], 2) ?></div>
                            <form class="quantity-form" action="../actions/update_cart.php" method="POST">
                                <input type="hidden" name="product_id" value="<?= $item['p_id'] ?>">
                                <div class="quantity-controls">
                                    <button type="button" class="qty-btn" onclick="updateQuantity(this, -1)">-</button>
                                    <input type="number" name="quantity" value="<?= $item['qty'] ?>" min="1" class="qty-input">
                                    <button type="button" class="qty-btn" onclick="updateQuantity(this, 1)">+</button>
                                </div>
                                <button type="submit" name="update_cart" class="btn btn-secondary">Update</button>
                                <button type="submit" name="remove_item" class="btn btn-danger">Remove</button>
                            </form>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="cart-summary">
                <div class="summary-content">
                    <h2>Order Summary</h2>
                    <div class="summary-row">
                        <span>Subtotal</span>
                        <span>$<?= number_format(isset($total['total']) ? $total['total'] : 0, 2) ?></span>
                    </div>
                    <div class="summary-row total">
                        <span>Total</span>
                        <span>$<?= number_format(isset($total['total']) ? $total['total'] : 0, 2) ?></span>
                    </div>
                    <form action="checkout.php" method="POST">
                        <button type="submit" name="proceed_checkout" class="btn btn-primary">Proceed to Checkout</button>
                    </form>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include('footer.php'); ?>
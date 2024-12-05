<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../settings/security.php');

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('header.php');
include(__DIR__ . "/../controllers/cart_controller.php");
include(__DIR__ . "/../controllers/user_controller.php");

$cart_items = get_cart_items_ctr($_SESSION['user_id']);
$total = get_cart_total_ctr($_SESSION['user_id']);
$user = get_user_ctr($_SESSION['user_id']);

if(empty($cart_items)) {
    header("Location: cart.php");
    exit();
}
?>
<div class="checkout-page">
    <h1 class="page-title">Checkout</h1>
    <div class="checkout-container">
        <div class="order-summary">
            <h2>Order Summary</h2>
            <div class="summary-items">
                <?php foreach($cart_items as $item): ?>
                    <div class="summary-item">
                        <div class="item-info">
                            <img src="<?= $item['product_image'] ?>" alt="<?= htmlspecialchars($item['product_name']) ?>">
                            <div class="item-details">
                                <h3><?= htmlspecialchars($item['product_name']) ?></h3>
                                <p>Size: <?= htmlspecialchars($item['size']) ?> | Color: <?= htmlspecialchars($item['color']) ?></p>
                                <p>Quantity: <?= $item['qty'] ?></p>
                            </div>
                        </div>
                        <div class="item-price">
                            $<?= number_format($item['product_price'] * $item['qty'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="order-total">
                <span>Total:</span>
                <span>$<?= number_format($total['total'], 2) ?></span>
            </div>
        </div>

        <div class="payment-section">
            <h2>Payment Details</h2>
            <div class="payment-info">
                <p>Payment Method: Mobile Money</p>
                <p class="note">Please ensure your mobile money number is correct.</p>
            </div>
            <form id="paymentForm">
                <div class="form-group">
                    <label>Mobile Money Number</label>
                    <input type="tel" id="momo_number" name="momo_number" value="<?= htmlspecialchars($user['user_phone']) ?>" pattern="^\+?[0-9]{10,15}$" placeholder="+233xxxxxxxxx" required>
                </div>
                <input type="hidden" id="email-address" value="<?= htmlspecialchars($user['user_email']) ?>">
                <input type="hidden" id="amount" value="<?= $total['total'] ?>">
                <button type="submit" class="btn btn-primary">Complete Payment</button>
            </form>
        </div>
    </div>
</div>
<script src="https://js.paystack.co/v1/inline.js"></script>
<script src="../js/pay.js?v=<?= time() ?>"></script>
<?php include('footer.php'); ?>

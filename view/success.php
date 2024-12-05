<?php
session_start();
require_once(__DIR__ . '/../settings/security.php');
require_once(__DIR__ . '/../controllers/order_controller.php');

if (!isset($_SESSION['user_id']) || !isset($_SESSION['order_id'])) {
    header("Location: login.php");
    exit();
}

$order_confirmation = get_order_confirmation_ctr($_SESSION['order_id']);
if (!$order_confirmation) {
    header("Location: checkout.php?error=invalid_order");
    exit();
}

include('header.php');
?>

<div class="success-page">
    <div class="success-container">
        <div class="success-header">
            <div class="success-icon">
                <i class="fas fa-check-circle"></i>
            </div>
            <h1>Thank You for Your Purchase!</h1>
            <p class="subtitle">Your order has been successfully placed and is being processed.</p>
        </div>

        <div class="order-details">
            <h2>Order Summary</h2>
            <div class="order-info">
                <div class="info-row">
                    <span class="label">Order Number:</span>
                    <span class="value">#<?= htmlspecialchars($order_confirmation['order_info']['order_id']) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Order Date:</span>
                    <span class="value"><?= date('F j, Y', strtotime($order_confirmation['order_info']['created_at'])) ?></span>
                </div>
                <div class="info-row">
                    <span class="label">Total Amount:</span>
                    <span class="value">$<?= number_format($order_confirmation['order_info']['total_amount'], 2) ?></span>
                </div>
            </div>

            <div class="order-items">
                <h3>Items Purchased</h3>
                <?php foreach ($order_confirmation['order_details'] as $item): ?>
                    <div class="order-item">
                        <div class="item-details">
                            <h4><?= htmlspecialchars($item['product_name']) ?></h4>
                            <p>Quantity: <?= $item['quantity'] ?></p>
                        </div>
                        <div class="item-price">
                            $<?= number_format($item['subtotal'], 2) ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <div class="confirmation-message">
            <p>A confirmation email has been sent to <?= htmlspecialchars($order_confirmation['order_info']['email']) ?></p>
            <p class="thank-you-message">Thank you for shopping with ReThread Collective!</p>
        </div>

        <div class="action-buttons">
            <a href="index.php" class="continue-shopping-btn">Continue Shopping</a>
            <a href="orders.php" class="view-orders-btn">View My Orders</a>
        </div>
    </div>
</div>

<style>
.success-page {
    padding: 40px 20px;
    max-width: 800px;
    margin: 0 auto;
    font-family: 'Arial', sans-serif;
}

.success-container {
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    padding: 40px;
}

.success-header {
    text-align: center;
    margin-bottom: 40px;
}

.success-icon {
    margin-bottom: 20px;
}

.success-icon i {
    color: #4CAF50;
    font-size: 64px;
}

.success-header h1 {
    color: #333;
    font-size: 32px;
    margin-bottom: 10px;
}

.subtitle {
    color: #666;
    font-size: 18px;
}

.order-details {
    border-top: 1px solid #eee;
    padding-top: 30px;
    margin-bottom: 30px;
}

.order-info {
    background: #f9f9f9;
    padding: 20px;
    border-radius: 8px;
    margin-bottom: 30px;
}

.info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 10px;
    padding: 5px 0;
}

.label {
    color: #666;
    font-weight: bold;
}

.value {
    color: #333;
}

.order-items {
    margin-top: 30px;
}

.order-item {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 0;
    border-bottom: 1px solid #eee;
}

.item-details h4 {
    margin: 0 0 5px 0;
    color: #333;
}

.item-price {
    font-weight: bold;
    color: #333;
}

.confirmation-message {
    text-align: center;
    margin: 30px 0;
    padding: 20px;
    background: #f5f5f5;
    border-radius: 8px;
}

.thank-you-message {
    font-size: 20px;
    color: #4CAF50;
    margin-top: 15px;
    font-weight: bold;
}

.action-buttons {
    display: flex;
    justify-content: center;
    gap: 20px;
    margin-top: 30px;
}

.continue-shopping-btn, .view-orders-btn {
    padding: 15px 30px;
    border-radius: 25px;
    font-size: 16px;
    font-weight: bold;
    text-decoration: none;
    text-align: center;
    transition: all 0.3s ease;
}

.continue-shopping-btn {
    background: #4CAF50;
    color: white;
    border: none;
}

.continue-shopping-btn:hover {
    background: #45a049;
    transform: translateY(-2px);
}

.view-orders-btn {
    background: #fff;
    color: #333;
    border: 2px solid #333;
}

.view-orders-btn:hover {
    background: #f5f5f5;
    transform: translateY(-2px);
}

@media (max-width: 600px) {
    .success-container {
        padding: 20px;
    }
    
    .action-buttons {
        flex-direction: column;
    }
    
    .continue-shopping-btn, .view-orders-btn {
        width: 100%;
        margin: 5px 0;
    }
}
</style>

<?php 
unset($_SESSION['order_id']); // Clear the order ID from session
include('footer.php'); 
?>

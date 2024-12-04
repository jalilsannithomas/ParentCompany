<?php 
require_once('../settings/security.php');
session_start();
Security::check_session_timeout();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if order exists
if(!isset($_GET['order'])) {
    header("Location: profile.php");
    exit();
}

// Fetch order details using the controller function
$order_id = $_GET['order'];
$order_details = get_order_details_ctr($order_id); // Updated to use the correct function
?>

<div class="confirmation-page section">
    <h1 class="page-title">Order Confirmed</h1>
    <div class="confirmation-details">
        <h2>Order #<?= htmlspecialchars($order_id) ?></h2>
        <p>Thank you for your purchase. A confirmation has been sent to your phone.</p>
        
        <?php foreach($order_details as $item): ?>
            <div class="order-item">
                <span><?= htmlspecialchars($item['name']) ?> (<?= htmlspecialchars($item['size']) ?>, <?= htmlspecialchars($item['color']) ?>)</span>
                <span>Quantity: <?= htmlspecialchars($item['quantity']) ?></span>
                <span>$<?= number_format($item['price'] * $item['quantity'], 2) ?></span>
            </div>
        <?php endforeach; ?>
    </div>
    <a href="user_dashboard.php" class="btn btn-primary">Back to Dashboard</a>
</div>

<?php include('footer.php'); ?>

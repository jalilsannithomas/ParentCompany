<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('../settings/security.php');

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('header.php');
require_once('../controllers/order_controller.php');

if(isset($_GET['order_id'])) {
    $order_id = $_GET['order_id'];
    $order_details = get_order_details_ctr($order_id);
    $order_summary = get_order_summary_ctr($order_id);
} else {
    header("Location: user_dashboard.php");

}
?>

<div class="order-details-page">
    <div class="order-details-header">
        <h1 class="page-title">Order Details</h1>
        <a href="user_dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>

    <div class="order-summary">
        <h2>Order Summary</h2>
        <p>Order ID: #<?= htmlspecialchars($order_summary['order_id']) ?></p>
        <p>Customer: <?= htmlspecialchars($order_summary['user_name']) ?></p>
        <p>Phone: <?= htmlspecialchars($order_summary['user_phone']) ?></p>
        <p>Total Items: <?= htmlspecialchars($order_summary['total_items']) ?></p>
        <p>Total Quantity: <?= htmlspecialchars($order_summary['total_quantity']) ?></p>
        <p>Total: $<?= number_format($order_summary['order_amount'], 2) ?></p>
        <p>Status: <span class="status-<?= htmlspecialchars($order_summary['order_status']) ?>"><?= ucfirst($order_summary['order_status']) ?></span></p>
        <p>Payment Status: <span class="status-<?= htmlspecialchars($order_summary['payment_status']) ?>"><?= ucfirst($order_summary['payment_status']) ?></span></p>
    </div>

    <div class="order-items">
        <h2>Order Items</h2>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Product</th>
                    <th>Size</th>
                    <th>Color</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($order_details as $item): ?>
                <tr>
                    <td><?= htmlspecialchars($item['product_name']) ?></td>
                    <td><?= htmlspecialchars($item['size']) ?></td>
                    <td><?= htmlspecialchars($item['color']) ?></td>
                    <td><?= htmlspecialchars($item['quantity']) ?></td>
                    <td>$<?= number_format($item['product_price'] * $item['quantity'], 2) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<style>
.order-details-page {
    padding: 2rem;
}

.order-details-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 2rem;
}

.order-summary, .order-items {
    background: var(--accent-bg);
    padding: 1.5rem;
    border-radius: 8px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    margin-bottom: 2rem;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #cce5ff; color: #004085; }
.status-completed { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }
</style>

<?php include('footer.php'); ?>

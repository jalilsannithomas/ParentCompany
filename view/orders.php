<?php 
require_once('../settings/security.php');
session_start();
Security::check_session_timeout();

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../view/header.php');
include("../controllers/order_controller.php");

$orders = get_user_orders_ctr($_SESSION['user_id']);
?>

<div class="section" id="orders-page">
    <h1 class="page-title">My Orders</h1>
    
    <a href="../admin/index.php" class="btn btn-secondary">Back to Admin Dashboard</a>
    
    <?php if(empty($orders)): ?>
        <p>No orders found</p>
    <?php else: ?>
        <div class="orders-grid">
            <?php foreach($orders as $order): ?>
                <div class="order-card">
                    <div class="order-header">
                        <h3>Order #<?= htmlspecialchars($order['order_id']) ?></h3>
                        <span class="order-date"><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
                    </div>
                    <div class="order-info">
                        <p>Total: $<?= number_format($order['order_amount'], 2) ?></p>
                        <p>Status: <span class="status-<?= htmlspecialchars($order['order_status']) ?>"><?= ucfirst($order['order_status']) ?></span></p>
                        <p>Payment: <span class="payment-<?= htmlspecialchars($order['payment_status']) ?>"><?= ucfirst($order['payment_status']) ?></span></p>
                    </div>
                    <div class="order-actions">
                        <a href="order_details.php?order_id=<?= $order['order_id'] ?>" class="btn btn-secondary">View Details</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
#orders-page {
    padding: 2rem;
}

.page-title {
    text-align: center;
    margin-bottom: 2rem;
    font-size: 2.5rem;
}

.orders-grid {
    display: grid;
    gap: 2rem;
    padding: 2rem;
}

.order-card {
    background: var(--accent-bg);
    border-radius: 8px;
    padding: 1.5rem;
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

.order-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.order-info {
    margin-bottom: 1rem;
}

.status-pending { background: #fff3cd; color: #856404; }
.status-processing { background: #cce5ff; color: #004085; }
.status-completed { background: #d4edda; color: #155724; }
.status-cancelled { background: #f8d7da; color: #721c24; }
</style>

<?php include('footer.php'); ?>

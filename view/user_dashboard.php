<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once('../settings/security.php');
require_once('../controllers/order_controller.php');
require_once('../controllers/user_controller.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$user = get_user_ctr($user_id);
$orders = get_user_orders_ctr($user_id);

include('header.php');
?>
<div class="admin-page">
    <div class="admin-section">
        <h2>Welcome, <?= htmlspecialchars($user['user_name']) ?></h2>
        <div class="data-table">
            <h3>Account Details</h3>
            <div class="profile-info">
                <p><strong>Email:</strong> <?= htmlspecialchars($user['user_email']) ?></p>
                <p><strong>Phone:</strong> <?= htmlspecialchars($user['user_phone']) ?></p>
<button onclick="location.href='/ReThread_Collective/actions/update_profile.php'" class="btn btn-primary">Edit Profile</button> <!-- Updated to absolute URL -->
            </div>
        </div>
    </div>
    <div class="admin-section">
        <h2>Order History</h2>
        <div class="data-table">
            <?php if (count($orders) > 0): ?>
                <table>
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?= $order['order_id'] ?></td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td><span class="status-<?= $order['order_status'] ?>"><?= ucfirst($order['order_status']) ?></span></td>
                                <td>$<?= number_format($order['order_amount'], 2) ?></td>
                                <td><button onclick="location.href='order_details.php?order_id=<?= $order['order_id'] ?>'" class="btn btn-secondary">View</button></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p class="no-orders">No orders yet.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="quick-links">
        <button onclick="location.href='shop.php'" class="btn btn-primary">Shop Now</button>
        <button onclick="location.href='cart.php'" class="btn btn-secondary">View Cart</button>
        <button onclick="location.href='contact.php'" class="btn btn-danger">Contact Support</button>
    </div>
</div>
<?php include('footer.php'); ?>

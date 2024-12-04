<?php
session_start();
require_once('settings/security.php');

if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('header.php');
include("controllers/user_controller.php");
include("controllers/order_controller.php");

$user = get_user_ctr($_SESSION['user_id']);
$orders = get_all_orders_ctr();
?>
<div class="profile-page">
    <h1 class="page-title">My Profile</h1>
    <div class="profile-container">
        <div class="profile-section">
            <h2>Profile Information</h2>
            
            <?php if(isset($_GET['success'])): ?>
                <div class="alert success">Profile updated successfully</div>
            <?php endif; ?>

            <?php if(isset($_GET['error'])): ?>
                <div class="alert error">
                    <?php
                        switch($_GET['error']) {
                            case 'mismatch':
                                echo 'Passwords do not match';
                                break;
                            case 'current':
                                echo 'Current password is incorrect';
                                break;
                            default:
                                echo 'An error occurred. Please try again.';
                        }
                    ?>
                </div>
            <?php endif; ?>

            <form action="actions/update_profile.php" method="POST" class="profile-form">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" name="name" value="<?= htmlspecialchars($user['user_name']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" value="<?= htmlspecialchars($user['user_email']) ?>" required>
                </div>
                <div class="form-group">
                    <label>Phone</label>
                    <input type="tel" name="phone" value="<?= htmlspecialchars($user['user_phone']) ?>" required 
                           pattern="^\+?[0-9]{10,15}$" placeholder="+233xxxxxxxxx">
                </div>
                <button type="submit" name="update_profile" class="btn btn-primary">Update Profile</button>
            </form>

            <div class="password-section">
                <h3>Change Password</h3>
                <form action="actions/update_password.php" method="POST" class="password-form">
                    <div class="form-group">
                        <label>Current Password</label>
                        <input type="password" name="current_password" required>
                    </div>
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required 
                               minlength="8" pattern="(?=.*\d)(?=.*[a-z])(?=.*[A-Z]).{8,}">
                        <small>Minimum 8 characters, at least one number and one uppercase letter</small>
                    </div>
                    <div class="form-group">
                        <label>Confirm New Password</label>
                        <input type="password" name="confirm_password" required>
                    </div>
                    <button type="submit" name="update_password" class="btn btn-primary">Change Password</button>
                </form>
            </div>
        </div>

        <div class="orders-section">
            <h2>Order History</h2>
            <?php if(empty($orders)): ?>
                <p class="no-orders">No orders yet</p>
            <?php else: ?>
                <div class="orders-list">
                    <?php foreach($orders as $order): ?>
                        <div class="order-card">
                            <div class="order-header">
                                <div class="order-info">
                                    <h3>Order #<?= htmlspecialchars($order['order_id']) ?></h3>
                                    <span class="order-date"><?= date('M d, Y', strtotime($order['created_at'])) ?></span>
                                </div>
                                <div class="order-status status-<?= htmlspecialchars($order['order_status']) ?>">
                                    <?= ucfirst($order['order_status']) ?>
                                </div>
                            </div>
                            <div class="order-details">
                                <div class="order-amount">
                                    Total: $<?= number_format($order['order_amount'], 2) ?>
                                </div>
                                <div class="order-payment status-<?= htmlspecialchars($order['payment_status']) ?>">
                                    Payment: <?= ucfirst($order['payment_status']) ?>
                                </div>
                            </div>
                            <button onclick="viewOrderDetails(<?= $order['order_id'] ?>)" 
                                    class="btn btn-secondary">View Details</button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<!-- Order Details Modal -->
<div id="order-details-modal" class="modal">
    <div class="modal-content">
        <div class="modal-header">
            <h2>Order Details</h2>
            <span class="close">&times;</span>
        </div>
        <div class="modal-body" id="order-details-content">
            <!-- Content will be loaded dynamically -->
        </div>
    </div>
</div>
<script>
function viewOrderDetails(orderId) {
    fetch(`actions/get_order_details.php?id=${orderId}`)
        .then(response => response.json())
        .then(data => {
            if(data.success) {
                document.getElementById('order-details-content').innerHTML = formatOrderDetails(data.order);
                document.getElementById('order-details-modal').style.display = 'block';
            }
        });
}

function formatOrderDetails(order) {
    return `
        <div class="order-details-view">
            <div class="order-summary">
                <div class="summary-row">
                    <span>Order Date:</span>
                    <span>${new Date(order.created_at).toLocaleDateString()}</span>
                </div>
                <div class="summary-row">
                    <span>Status:</span>
                    <span class="status-${order.order_status}">${order.order_status}</span>
                </div>
                <div class="summary-row">
                    <span>Payment Status:</span>
                    <span class="status-${order.payment_status}">${order.payment_status}</span>
                </div>
            </div>
            
            <div class="order-items">
                <h3>Items</h3>
                ${order.items.map(item => `
                    <div class="order-item">
                        <div class="item-info">
                            <h4>${item.product_name}</h4>
                            <p>Size: ${item.size} | Color: ${item.color}</p>
                            <p>Quantity: ${item.quantity}</p>
                        </div>
                        <div class="item-price">
                            $${(item.product_price * item.quantity).toFixed(2)}
                        </div>
                    </div>
                `).join('')}
            </div>
            
            <div class="order-total">
                <span>Total:</span>
                <span>$${order.order_amount.toFixed(2)}</span>
            </div>
        </div>
    `;
}

// Close modal when clicking outside
window.onclick = function(event) {
    const modal = document.getElementById('order-details-modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
}

// Close modal when clicking the close button
document.querySelector('.close').onclick = function() {
    document.getElementById('order-details-modal').style.display = 'none';
}
</script>
<?php include('footer.php'); ?>

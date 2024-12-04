<?php
session_start();
require_once('../settings/security.php');

if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include_once("../controllers/product_controller.php");
include_once("../controllers/order_controller.php");
include_once("../controllers/raffle_controller.php");

$total_products = get_product_count_ctr();
$recent_orders = get_recent_orders_ctr();
$recent_entries = get_recent_entries_ctr(5);
$total_orders = get_orders_count_ctr();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - Parent Company</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php include('includes/sidebar.php'); ?>
Copy    <div class="admin-content">
        <div class="admin-header">
            <h1>Admin Dashboard</h1>
            <div class="admin-user">
                Admin: <?= $_SESSION['user_name'] ?>
            </div>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <h3>Total Products</h3>
                <p class="stat-number"><?= $total_products ?></p>
            </div>
            <div class="stat-card">
                <h3>Total Orders</h3>
                <p class="stat-number"><?= $total_orders ?></p>
            </div>
            <div class="stat-card">
                <h3>Raffle Entries</h3>
                <p class="stat-number"><?= get_entry_count_ctr() ?></p>
            </div>
        </div>

        <div class="admin-section">
            <div class="section-header">
                <h2>Recent Orders</h2>
                <a href="orders.php" class="view-all">View All</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_orders as $order): ?>
                            <tr>
                                <td>#<?= $order['order_id'] ?></td>
                                <td><?= $order['user_name'] ?></td>
                                <td>$<?= number_format($order['order_amount'], 2) ?></td>
                                <td>
                                    <span class="status-badge status-<?= $order['order_status'] ?>">
                                        <?= ucfirst($order['order_status']) ?>
                                    </span>
                                </td>
                                <td><?= date('M d, Y', strtotime($order['created_at'])) ?></td>
                                <td>
                                    <button onclick="window.location.href = 'order_details.php?order_id=<?= $order['order_id'] ?>'" 
                                            class="btn-action">
                                        View
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="admin-section">
            <div class="section-header">
                <h2>Recent Raffle Entries</h2>
                <a href="raffle.php" class="view-all">View All</a>
            </div>
            <div class="table-responsive">
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Phone</th>
                            <th>Instagram</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($recent_entries as $entry): ?>
                            <tr>
                                <td><?= $entry['name'] ?></td>
                                <td><?= $entry['phone'] ?></td>
                                <td>@<?= $entry['instagram'] ?></td>
                                <td><?= date('M d, Y', strtotime($entry['created_at'])) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.admin-body { background: #000; color: #fff; }
.admin-wrapper { display: flex; min-height: 100vh; }
.admin-content { flex: 1; padding: 2rem; }
.admin-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; padding-bottom: 1rem; border-bottom: 1px solid #333; }
.stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-bottom: 2rem; }
.stat-card { background: #111; padding: 1.5rem; border-radius: 8px; }
.stat-number { font-size: 2.5rem; font-weight: bold; margin-top: 0.5rem; }
.admin-section { background: #111; padding: 1.5rem; border-radius: 8px; margin-bottom: 2rem; }
.section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem; }
.admin-table { width: 100%; border-collapse: collapse; }
.admin-table th, .admin-table td { padding: 1rem; text-align: left; border-bottom: 1px solid #333; }
.admin-table th { background: #000; font-weight: bold; }
.status-badge { padding: 0.25rem 0.5rem; border-radius: 4px; font-size: 0.875rem; }
.status-pending { background: #ffd700; color: #000; }
.status-processing { background: #0088cc; }
.status-completed { background: #00cc88; }
.status-cancelled { background: #cc0000; }
.btn-action { background: #333; color: #fff; padding: 0.5rem 1rem; border-radius: 4px; border: none; cursor: pointer; }
.view-all { color: #0088cc; text-decoration: none; }
@media (max-width: 768px) { .admin-wrapper { flex-direction: column; } .stats-grid { grid-template-columns: 1fr; } }
</style>

<script src="js/admin.js"></script>
</body>
</html>
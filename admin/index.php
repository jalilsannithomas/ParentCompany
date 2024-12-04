<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
error_log("Session ID: " . session_id());
error_log("Session data: " . print_r($_SESSION, true));

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Parent Company</title>
    <link rel="stylesheet" href="../css/styles.css">
    <style>
    /* Admin-specific styles - Dark Theme */
    body {
        background-color: #1a1a1a;
        color: #e0e0e0;
    }

    .admin-wrapper {
        display: flex;
        min-height: 100vh;
    }

    .admin-sidebar {
        width: 250px;
        background-color: #2c2c2c;
        color: #e0e0e0;
        padding: 20px;
    }

    .admin-content {
        flex: 1;
        padding: 20px;
        background-color: #1a1a1a;
    }

    .admin-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 20px;
        background-color: #2c2c2c;
        padding: 15px;
        border-radius: 8px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
        margin-bottom: 30px;
    }

    .stat-card {
        background-color: #2c2c2c;
        padding: 20px;
        border-radius: 8px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .stat-number {
        font-size: 24px;
        font-weight: bold;
        margin-top: 10px;
        color: #4caf50;
    }

    .admin-table {
        width: 100%;
        border-collapse: collapse;
        background-color: #2c2c2c;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .admin-table th, .admin-table td {
        padding: 12px;
        border: 1px solid #444;
        text-align: left;
    }

    .admin-table th {
        background-color: #3c3c3c;
        font-weight: bold;
        color: #e0e0e0;
    }

    .btn-small {
        padding: 5px 10px;
        background-color: #4caf50;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        display: inline-block;
    }

    .btn-small:hover {
        background-color: #45a049;
    }

    .admin-section {
        background-color: #2c2c2c;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .admin-section h2 {
        color: #e0e0e0;
        margin-bottom: 15px;
    }

    @media (max-width: 768px) {
        .admin-wrapper {
            flex-direction: column;
        }
        
        .admin-sidebar {
            width: 100%;
        }
        
        .stats-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php 
        echo "<!-- Debug: Before sidebar include -->\n";
        include('includes/sidebar.php');
        echo "<!-- Debug: After sidebar include -->\n";
        ?>
        <div class="admin-content">
            <div class="admin-header">
                <h1>Admin Dashboard</h1>
                <div class="admin-user">
                    Admin: <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Unknown' ?>
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
    <h2>Recent Orders</h2>
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer</th>
                <th>Total</th>
                <th>Date</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($recent_orders as $order): ?>
                <tr>
                    <td>#<?= isset($order['order_id']) ? htmlspecialchars($order['order_id']) : 'N/A' ?></td>
                    <td><?= isset($order['customer_name']) ? htmlspecialchars($order['customer_name']) : 'N/A' ?></td>
                    <td>$<?= isset($order['order_amount']) ? number_format($order['order_amount'], 2) : '0.00' ?></td>
                    <td><?= isset($order['created_at']) ? date('M d, Y', strtotime($order['created_at'])) : 'N/A' ?></td>
                    <td><?= isset($order['order_status']) ? ucfirst($order['order_status']) : 'N/A' ?></td>
                    <td>
                        <a href="order_details.php?order_id=<?= isset($order['order_id']) ? $order['order_id'] : '' ?>" class="btn-small">View</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

            <div class="admin-section">
                <h2>Recent Raffle Entries</h2>
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
                        <?php foreach ($recent_entries as $entry): ?>
                            <tr>
                                <td><?= isset($entry['name']) ? htmlspecialchars($entry['name']) : 'N/A' ?></td>
                                <td><?= isset($entry['phone']) ? htmlspecialchars($entry['phone']) : 'N/A' ?></td>
                                <td>@<?= isset($entry['instagram']) ? htmlspecialchars($entry['instagram']) : 'N/A' ?></td>
                                <td><?= isset($entry['created_at']) ? date('M d, Y', strtotime($entry['created_at'])) : 'N/A' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="js/admin.js"></script>
</body>
</html>
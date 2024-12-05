<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

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
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Parent Company</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
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

    .admin-main {
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

    .dashboard-grid {
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

    .recent-activity {
        background-color: #2c2c2c;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.2);
    }

    .recent-activity h2 {
        color: #e0e0e0;
        margin-bottom: 15px;
    }

    .table-container {
        overflow-x: auto;
    }

    .status-badge {
        padding: 5px 10px;
        border-radius: 4px;
        font-size: 12px;
        font-weight: bold;
        text-transform: uppercase;
    }

    .status-pending {
        background-color: #ff9800;
        color: #fff;
    }

    .status-shipped {
        background-color: #4caf50;
        color: #fff;
    }

    .status-delivered {
        background-color: #03a9f4;
        color: #fff;
    }

    @media (max-width: 768px) {
        .admin-wrapper {
            flex-direction: column;
        }
        
        .admin-sidebar {
            width: 100%;
        }
        
        .dashboard-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
</head>
<body class="admin-body">
    <div class="admin-wrapper">
        <?php include('includes/sidebar.php'); ?>
        
        <main class="admin-main">
            <header class="admin-header">
                <h1>Dashboard</h1>
                <div class="admin-user">
                    Admin: <?php echo isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Unknown'; ?>
                </div>
            </header>

            <div class="dashboard-grid">
                <div class="stat-card">
                    <h3>Total Products</h3>
                    <p class="stat-number"><?php echo $total_products; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Total Orders</h3>
                    <p class="stat-number"><?php echo $total_orders; ?></p>
                </div>
                
                <div class="stat-card">
                    <h3>Recent Raffle Entries</h3>
                    <p class="stat-number"><?php echo count($recent_entries); ?></p>
                </div>
            </div>

            <section class="recent-activity">
                <h2>Recent Orders</h2>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_orders as $order): ?>
                                <tr>
                                    <td>#<?php echo $order['order_id']; ?></td>
                                    <td><?php echo htmlspecialchars($order['customer_name']); ?></td>
                                    <td>GH₵<?php echo number_format($order['order_amount'], 2); ?></td>
                                    <td><span class="status-badge status-<?php echo strtolower($order['order_status']); ?>"><?php echo $order['order_status']; ?></span></td>
                                    <td><?php echo date('M d, Y', strtotime($order['order_date'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>

            <section class="recent-activity">
                <h2>Recent Raffle Entries</h2>
                <div class="table-container">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Entry ID</th>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Instagram</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($recent_entries as $entry): ?>
                                <tr>
                                    <td>#<?php echo $entry['entry_id']; ?></td>
                                    <td><?php echo htmlspecialchars($entry['name']); ?></td>
                                    <td><?php echo htmlspecialchars($entry['phone']); ?></td>
                                    <td>@<?php echo htmlspecialchars($entry['instagram']); ?></td>
                                    <td><?php echo date('M d, Y', strtotime($entry['created_at'])); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </section>
        </main>
    </div>
</body>
</html>
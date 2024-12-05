<?php
require_once(__DIR__ . '/../settings/core.php');
require_once(__DIR__ . '/../controllers/order_controller.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit;
}

if (!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== 'admin') {
    header("Location: ../index.php");
    exit;
}

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

$orders = get_all_orders_ctr();
$order_count = get_orders_count_ctr();

// Debug output
error_log("Session data: " . print_r($_SESSION, true));
error_log("Orders data: " . print_r($orders, true));
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Orders - Admin</title>
    <style>
        body {
            background-color: #1e1e1e;
            color: #ffffff;
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
        }

        .admin-content {
            padding: 2rem;
            margin-bottom: 60px;
        }

        h1 {
            color: #ffffff;
            margin-bottom: 1.5rem;
            font-size: 1.8rem;
            font-weight: 500;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .stats {
            display: flex;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .stat-card {
            background: #2b2b2b;
            padding: 1.5rem;
            border-radius: 8px;
            flex: 1;
        }

        .stat-card h3 {
            margin: 0;
            font-size: 0.9rem;
            font-weight: 500;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 0.5rem;
        }

        .stat-card .value {
            font-size: 2rem;
            font-weight: 500;
            color: #4a90e2;
        }

        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: #2b2b2b;
            border-radius: 8px;
            overflow: hidden;
        }

        .admin-table th {
            background: #333333;
            padding: 1rem;
            text-align: left;
            font-weight: 500;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-bottom: 1px solid #3d3d3d;
        }

        .admin-table td {
            padding: 1rem;
            border-bottom: 1px solid #3d3d3d;
            font-size: 0.9rem;
        }

        .admin-table tr:hover {
            background: #333333;
        }

        .status-badge {
            padding: 0.3rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .status-pending {
            background: rgba(255, 193, 7, 0.2);
            color: #ffc107;
        }

        .status-processing {
            background: rgba(33, 150, 243, 0.2);
            color: #2196f3;
        }

        .status-completed {
            background: rgba(76, 175, 80, 0.2);
            color: #4caf50;
        }

        .status-cancelled {
            background: rgba(244, 67, 54, 0.2);
            color: #f44336;
        }

        .btn-small {
            padding: 0.4rem 0.8rem;
            border-radius: 4px;
            font-size: 0.8rem;
            font-weight: 500;
            text-decoration: none;
            letter-spacing: 0.5px;
            background: #4a90e2;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn-small:hover {
            background: #357abd;
        }

        .error {
            background: rgba(244, 67, 54, 0.1);
            color: #f44336;
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }
    </style>
</head>
<body>
    <?php include(__DIR__ . "/admin_navbar.php"); ?>
    
    <div class="admin-content">
        <h1>Manage Orders</h1>

        <div class="stats">
            <div class="stat-card">
                <h3>Total Orders</h3>
                <div class="value"><?= $order_count ?></div>
            </div>
        </div>
        
        <?php if ($orders === false): ?>
            <p class="error">Error loading orders. Please try again.</p>
        <?php else: ?>
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Customer Name</th>
                        <th>Date</th>
                        <th>Status</th>
                        <th>Total Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="6">No orders found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?= htmlspecialchars($order['order_id']) ?></td>
                                <td><?= htmlspecialchars($order['name'] ?? $order['user_name'] ?? 'Unknown') ?></td>
                                <td><?= htmlspecialchars(date('Y-m-d H:i', strtotime($order['created_at']))) ?></td>
                                <td>
                                    <span class="status-badge status-<?= strtolower($order['order_status']) ?>">
                                        <?= htmlspecialchars(ucfirst($order['order_status'])) ?>
                                    </span>
                                </td>
                                <td>$<?= number_format($order['total_amount'], 2) ?></td>
                                <td>
                                    <a href="order_details.php?id=<?= $order['order_id'] ?>" class="btn-small">View</a>
                                    <a href="edit_order.php?id=<?= $order['order_id'] ?>" class="btn-small">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <?php include(__DIR__ . "/admin_footer.php"); ?>
</body>
</html>

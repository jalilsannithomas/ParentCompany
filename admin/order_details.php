<?php
include "../settings/core.php";
include "../controllers/order_controller.php";

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] != 1) {
    header("Location: ../admin/order_details.php");
    exit();
}

// Check if order_id is provided
if (!isset($_GET['order_id'])) {
    header("Location: index.php");
    exit();
}

$order_id = $_GET['order_id'];
$order_details = get_order_details_ctr($order_id);
$order_items = get_order_items_ctr($order_id);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Details</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="../css/admin.css">
    <style>
        /* Add any additional styles here */
        body {
            background-color: #1a1a1a;
            color: #e0e0e0;
        }
        .admin-content {
            padding: 20px;
        }
        .order-details, .order-items {
            background-color: #2c2c2c;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        th, td {
            padding: 10px;
            border: 1px solid #444;
            text-align: left;
        }
        th {
            background-color: #3c3c3c;
        }
    </style>
</head>
<body>
    <div class="admin-content">
        <h1>Order Details</h1>
        <div class="order-details">
            <h2>Order #<?= htmlspecialchars($order_details['order_id']) ?></h2>
            <p>Customer: <?= htmlspecialchars($order_details['customer_name']) ?></p>
            <p>Date: <?= date('M d, Y', strtotime($order_details['created_at'])) ?></p>
            <p>Status: <?= ucfirst($order_details['order_status']) ?></p>
            <p>Total: $<?= number_format($order_details['order_amount'], 2) ?></p>
        </div>
        <div class="order-items">
            <h2>Order Items</h2>
            <table>
                <thead>
                    <tr>
                        <th>Product</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['product_name']) ?></td>
                            <td><?= htmlspecialchars($item['quantity']) ?></td>
                            <td>$<?= number_format($item['product_price'], 2) ?></td>
                            <td>$<?= number_format($item['quantity'] * $item['product_price'], 2) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <a href="index.php" class="btn-small">Back to Dashboard</a>
    </div>
</body>
</html>
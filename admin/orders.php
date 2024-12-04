<?php
include("../view/header.php");
include("../controllers/order_controller.php");

$orders = get_all_orders_ctr();
$order_count = get_orders_count_ctr(); // Get the total number of orders
?>

<div class="admin-content">
    <h1>Manage Orders</h1>
    <p>Total Orders: <?= $order_count ?></p> <!-- Display the total number of orders -->
    <table class="admin-table">
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Customer Name</th>
                <th>Status</th>
                <th>Total</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($orders)): ?>
                <tr>
                    <td colspan="5">No orders found.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['order_id']) ?></td>
                        <td><?= htmlspecialchars($order['customer_name']) ?></td>
                        <td><?= htmlspecialchars($order['order_status']) ?></td>
                        <td>$<?= number_format($order['total'], 2) ?></td>
                        <td>
                            <a href="admin_edit_order.php?id=<?= $order['order_id'] ?>" class="btn-small">Edit</a>
                            <form action="../actions/admin_delete_order.php" method="POST" style="display:inline;">
                                <input type="hidden" name="order_id" value="<?= $order['order_id'] ?>">
                                <button type="submit" class="btn-small" onclick="return confirm('Are you sure you want to delete this order?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php include('footer.php'); ?>

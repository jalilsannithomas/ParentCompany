<?php
require_once(dirname(__FILE__) . '/../controllers/order_controller.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];

    if (delete_order_ctr($order_id)) {
        header("Location: ../admin/orders.php?message=Order deleted successfully");
    } else {
        header("Location: ../admin/orders.php?error=Failed to delete order");
    }
} else {
    header("Location: ../admin/orders.php");
}

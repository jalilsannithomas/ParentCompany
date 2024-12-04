<?php
require_once(dirname(__FILE__) . '/../controllers/order_controller.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'];
    $status = $_POST['status'];

    if (update_order_ctr($order_id, $status)) {
        header("Location: ../admin/orders.php?message=Order updated successfully");
    } else {
        header("Location: ../admin/orders.php?error=Failed to update order");
    }
} else {
    header("Location: ../admin/orders.php");
}

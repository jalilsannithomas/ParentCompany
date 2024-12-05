<?php
require_once(dirname(__FILE__) . '/../classes/order_class.php');

function create_order_ctr($user_id, $amount) {
    if (!is_numeric($user_id) || !is_numeric($amount)) {
        error_log("Invalid user_id or amount in create_order_ctr");
        return false;
    }
    $order = new order_class();
    return $order->create_order($user_id, $amount);
}

function add_order_detail_ctr($order_id, $product_id, $quantity, $size = null, $color = null) {
    if (!is_numeric($order_id) || !is_numeric($product_id) || !is_numeric($quantity)) {
        error_log("Invalid parameters in add_order_detail_ctr");
        return false;
    }
    $order = new order_class();
    return $order->add_order_detail($order_id, $product_id, $quantity, $size, $color);
}

function get_user_orders_count_ctr($user_id) {
    if (!is_numeric($user_id)) {
        return 0;
    }
    $order = new order_class();
    return $order->get_user_orders_count($user_id);
}

function get_user_orders_ctr($user_id) {
    $order = new order_class();
    return $order->get_user_orders($user_id);
}

function get_all_orders_ctr() {
    $order = new order_class();
    return $order->get_all_orders();
}

function get_order_details_ctr($id) {
    $order = new order_class();
    return $order->get_order_details($id);
}

function update_order_ctr($id, $status) {
    $order = new order_class();
    return $order->update_order_status($id, $status);
}

function delete_order_ctr($id) {
    $order = new order_class();
    return $order->delete_order($id);
}

function get_recent_orders_ctr() {
    $order = new order_class();
    return $order->get_recent_orders();
}

function get_orders_count_ctr() {
    $order = new order_class();
    return $order->get_orders_count();
}

function process_checkout_ctr($user_id, $cart_items, $payment_details) {
    if (!is_numeric($user_id)) {
        return [
            'success' => false,
            'message' => 'Invalid user ID'
        ];
    }

    $order = new order_class();
    return $order->process_checkout($user_id, $cart_items, $payment_details);
}

function get_order_confirmation_ctr($order_id) {
    if (!is_numeric($order_id)) {
        return null;
    }

    $order = new order_class();
    return $order->get_order_confirmation($order_id);
}
?>

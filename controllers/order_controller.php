<?php
require_once(dirname(__FILE__) . '/../classes/order_class.php');

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
?>

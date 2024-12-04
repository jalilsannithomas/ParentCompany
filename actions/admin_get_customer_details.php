<?php
session_start();
require_once("../settings/security.php");

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include("../controllers/user_controller.php");
include("../controllers/order_controller.php");

if(isset($_GET['id'])) {
    $user_id = $_GET['id'];
    $customer = get_user_ctr($user_id);
    $orders = get_user_orders_ctr($user_id);

    if($customer) {
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'customer' => $customer,
            'orders' => $orders
        ]);
        exit();
    } else {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Customer not found']);
        exit();
    }
} else {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}
?>
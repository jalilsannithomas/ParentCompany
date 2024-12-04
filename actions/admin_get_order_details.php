<?php
session_start();
require_once("../settings/security.php");

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include("../controllers/order_controller.php");

if(isset($_GET['id'])) {
    $order_id = $_GET['id'];
    $order = get_order_summary_ctr($order_id);
    $items = get_order_details_ctr($order_id);
    
    if($order && $items) {
        $order['items'] = $items;
        
        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'order' => $order
        ]);
        exit();
    }
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Order not found']);
?>
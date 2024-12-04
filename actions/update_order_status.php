<?php
include("../controllers/order_controller.php");
include("../settings/core.php");

if(!isset($_SESSION['user_id']) || !is_admin()) {
    header("Location: ../view/login.php");
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$response = ['success' => false, 'message' => ''];

if(isset($data['order_id']) && isset($data['status'])) {
    try {
        // Validate status
        $valid_statuses = ['pending', 'processing', 'completed', 'cancelled'];
        if(!in_array($data['status'], $valid_statuses)) {
            $response['message'] = 'Invalid status';
            echo json_encode($response);
            exit();
        }

        if(update_order_status_ctr($data['order_id'], $data['status'])) {
            // Log the status change
            $log_message = "Order #{$data['order_id']} status updated to {$data['status']}";
            log_admin_action($_SESSION['user_id'], 'order_status_update', $log_message);
            
            $response['success'] = true;
            $response['message'] = 'Order status updated successfully';
        } else {
            $response['message'] = 'Failed to update order status';
        }
    } catch(Exception $e) {
        $response['message'] = 'System error occurred';
    }
} else {
    $response['message'] = 'Invalid request';
}

header('Content-Type: application/json');
echo json_encode($response);

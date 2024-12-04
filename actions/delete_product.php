<?php
include("../controllers/product_controller.php");
include("../settings/core.php");

if(!isset($_SESSION['user_id']) || !is_admin()) {
    header("Location: ../view/login.php");
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$response = ['success' => false, 'message' => ''];

if(isset($data['product_id'])) {
    try {
        // Check if product exists and has no pending orders
        $product = get_one_product_ctr($data['product_id']);
        
        if($product) {
            if(delete_product_ctr($data['product_id'])) {
                $response['success'] = true;
                $response['message'] = 'Product deleted successfully';
            } else {
                $response['message'] = 'Failed to delete product';
            }
        } else {
            $response['message'] = 'Product not found';
        }
    } catch(Exception $e) {
        $response['message'] = 'System error occurred';
    }
} else {
    $response['message'] = 'Invalid request';
}

header('Content-Type: application/json');
echo json_encode($response);
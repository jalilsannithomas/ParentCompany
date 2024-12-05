<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../settings/security.php');
require_once('../controllers/cart_controller.php');
require_once('../controllers/payment_controller.php');
require_once('../controllers/order_controller.php');
require_once('../controllers/product_controller.php');

if(isset($_POST['process_payment'])) {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $phone = $_POST['momo_number'];
    $reference = 'PAY'.time().rand(100,999);
    
    try {
        // Start transaction
        $db = new db_connection();
        $db->db->begin_transaction();
        
        // Get cart items first
        $cart_items = get_cart_items_ctr($user_id);
        if (!$cart_items) {
            throw new Exception("Cart is empty");
        }
        
        // Create order
        $order_id = create_order_ctr($user_id, $amount);
        if (!$order_id) {
            throw new Exception("Failed to create order");
        }
        
        // Add order details and update stock
        foreach ($cart_items as $item) {
            // Add to order details
            $success = add_order_detail_ctr($order_id, $item['p_id'], $item['qty'], $item['size'], $item['color']);
            if (!$success) {
                throw new Exception("Failed to add order details");
            }
            
            // Update product stock
            $success = update_product_stock_ctr($item['p_id'], $item['qty']);
            if (!$success) {
                throw new Exception("Failed to update product stock");
            }
        }
        
        // Add payment record
        if (!add_payment_ctr($order_id, $amount, $phone, $reference)) {
            throw new Exception("Failed to add payment record");
        }
        
        // Simulate payment success
        $payment_success = true;
        
        if ($payment_success) {
            // Update statuses
            update_payment_status_ctr($reference, 'completed');
            update_order_status_ctr($order_id, 'processing');
            
            // Clear cart
            clear_cart_ctr($user_id);
            
            // Commit transaction
            $db->db->commit();
            
            $_SESSION['order_success'] = true;
            header("Location: ../view/order_confirmation.php?order=".$order_id);
            exit();
        } else {
            throw new Exception("Payment failed");
        }
        
    } catch (Exception $e) {
        // Rollback on error
        if (isset($db)) {
            $db->db->rollback();
        }
        error_log("Order processing failed: " . $e->getMessage());
        header("Location: ../view/checkout.php?error=payment");
        exit();
    }
}
?>
<?php

// Enable error reporting
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once(__DIR__ . '/../settings/security.php');
require_once('../controllers/cart_controller.php');

include("../controllers/payment_controller.php");
include("../controllers/order_controller.php");

if(isset($_POST['process_payment'])) {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $phone = $_POST['momo_number'];
    $reference = 'PAY'.time().rand(100,999);

    try {
        // Create order first
        $order_id = create_order_ctr($user_id, $amount);

        if($order_id) {
            // Add payment record
            if(add_payment_ctr($order_id, $amount, $phone, $reference)) {
                // Here you would integrate with actual mobile money API
                // For now, we'll simulate a successful payment
                $payment_success = true;

                if($payment_success) {
                    update_payment_status_ctr($reference, 'completed');
                    update_order_status_ctr($order_id, 'processing');

                    // Clear the cart after successful order
                    clear_cart_ctr($user_id);

                    $_SESSION['order_success'] = true;
                    header("Location: ../order_confirmation.php?order=".$order_id);
                    exit();
                } else {
                    update_payment_status_ctr($reference, 'failed');
                    update_order_status_ctr($order_id, 'cancelled');
                    header("Location: ../checkout.php?error=payment");
                    exit();
                }
            } else {
                throw new Exception("Failed to add payment record.");
            }
        } else {
            throw new Exception("Failed to create order.");
        }
    } catch(Exception $e) {
        error_log("Process Payment Error: " . $e->getMessage());
        header("Location: ../checkout.php?error=1");
        exit();
    }
}
?>
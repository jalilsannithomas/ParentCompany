<?php
session_start();
require_once('../settings/core.php');
require_once('../controllers/cart_controller.php');

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

// Check if product ID was provided
if (!isset($_POST['product_id'])) {
    $_SESSION['error'] = "No product specified";
    header("Location: ../view/cart.php");
    exit();
}

$product_id = $_POST['product_id'];
$user_id = $_SESSION['user_id'];

// Try to remove the item
if (remove_from_cart_ctr($product_id, $user_id)) {
    $_SESSION['success'] = "Item removed from cart";
} else {
    $_SESSION['error'] = "Failed to remove item from cart";
}

// Redirect back to cart
header("Location: ../view/cart.php");
exit();
?>

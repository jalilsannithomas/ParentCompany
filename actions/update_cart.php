<?php
session_start();
require_once '../settings/core.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

include("../controllers/cart_controller.php");

if(isset($_POST['update_cart'])) {
    // Validate inputs
    if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
        $_SESSION['error'] = "Missing required fields";
        header("Location: ../view/cart.php");
        exit();
    }

    $p_id = $_POST['product_id'];
    $uid = $_SESSION['user_id'];
    $qty = intval($_POST['quantity']);

    // Validate quantity
    if ($qty < 1 || $qty > 10) {
        $_SESSION['error'] = "Invalid quantity. Must be between 1 and 10.";
        header("Location: ../view/cart.php");
        exit();
    }
    
    $result = update_cart_qty_ctr($p_id, $uid, $qty);

    if($result) {
        $_SESSION['success'] = "Cart updated successfully";
    } else {
        $_SESSION['error'] = "Failed to update cart. Please try again.";
    }
    
    header("Location: ../view/cart.php");
    exit();
}

if(isset($_POST['remove_item'])) {
    $p_id = $_POST['product_id'];
    $uid = $_SESSION['user_id'];
    
    $result = remove_from_cart_ctr($p_id, $uid);

    if($result) {
        header("Location: ../view/cart.php");
        exit();
    } else {
        header("Location: ../view/cart.php?error=remove_failed");
        exit();
    }
}

// If we get here, it means the form wasn't submitted properly
$_SESSION['error'] = "Invalid request";
header("Location: ../view/cart.php");
exit();
?>
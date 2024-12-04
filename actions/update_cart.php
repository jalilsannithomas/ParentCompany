<?php
session_start();
require_once '../settings/core.php';

include("../controllers/cart_controller.php");

if(isset($_POST['update_cart'])) {
    $p_id = $_POST['product_id'];
    $uid = $_SESSION['user_id'];
    $qty = $_POST['quantity'];
    
    $result = update_cart_qty_ctr($p_id, $uid, $qty);

    if($result) {
        header("Location: ../view/cart.php");
        exit();
    } else {
        header("Location: ../view/cart.php?error=update_failed");
        exit();
    }
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

// Redirect to the cart page if accessed directly without form submission
header("Location: ../view/cart.php");
exit();
?>
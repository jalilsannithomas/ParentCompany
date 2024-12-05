<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../view/login.php");
    exit();
}

include("../controllers/cart_controller.php");

if(isset($_POST['addtocart'])) {
    $p_id = $_POST['product_id'];
    $uid = $_SESSION['user_id'];
    $qty = 1;
    $size = $_POST['size'];
    $color = $_POST['color'];

    $result = add_to_cart_ctr($p_id, $uid, $qty, $size, $color);

    if($result) {
        $_SESSION['success'] = "Item added to cart successfully";
        header("Location: ../view/cart.php");
        exit();
    } else {
        $_SESSION['error'] = "Failed to add item to cart";
        header("Location: ../view/shop.php");
        exit();
    }
}

// If we get here, it means the form wasn't submitted properly
$_SESSION['error'] = "Invalid request";
header("Location: ../view/shop.php");
exit();
?>
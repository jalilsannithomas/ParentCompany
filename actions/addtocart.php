<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start(); // Add this line to start the session

include("../controllers/cart_controller.php");

if(isset($_POST['addtocart'])) {
    $p_id = $_POST['product_id'];
    $uid = $_SESSION['user_id'];
    $qty = 1; // Set the quantity to 1 since it's not provided in the form
    $size = $_POST['size'];
    $color = $_POST['color'];

    $result = add_to_cart_ctr($p_id, $uid, $qty, $size, $color);

    if($result) {
        header("Location: ../view/cart.php");
        exit();
    } else {
        // Handle the case when adding to cart fails
        header("Location: ../index.php?error=add_to_cart_failed");
        exit();
    }
}

// Redirect to the home page if accessed directly without form submission
header("Location: ../index.php");
exit();
?>
<?php
session_start();
require_once("../settings/security.php");

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include("../controllers/product_controller.php");

if(isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    
    // Get product info before deletion
    $product = get_one_product_ctr($id);
    
    if($product) {
        if(delete_product_ctr($id)) {
            // Delete product image if it exists
            $image_path = "../" . $product['product_image'];
            if(file_exists($image_path) && is_file($image_path)) {
                unlink($image_path);
            }
            
            header("Location: ../admin/products.php?success=delete");
            exit();
        } else {
            error_log("Failed to delete product ID: " . $id);
            header("Location: ../admin/products.php?error=delete");
            exit();
        }
    } else {
        error_log("Product not found with ID: " . $id);
        header("Location: ../admin/products.php?error=not_found");
        exit();
    }
}

header("Location: ../admin/products.php");
?>
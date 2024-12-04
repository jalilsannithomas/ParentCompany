<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
require_once("../settings/security.php");

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include("../controllers/product_controller.php");

if(isset($_POST['edit_product'])) {
    $id = $_POST['product_id'];
    $name = trim($_POST['product_name']);
    $price = $_POST['product_price'];
    $stock = $_POST['product_stock'];
    $desc = trim($_POST['product_desc']);
    $size = $_POST['size'];
    $color = $_POST['color'];
    
    $current_product = get_one_product_ctr($id);
    $image_path = $current_product['product_image'];
    
    if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['product_image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if(in_array($filetype, $allowed)) {
            $new_filename = time() . '_' . $filename;
            $upload_path = "../images/products/" . $new_filename;
            
            if(move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                if(file_exists("../" . $current_product['product_image'])) {
                    unlink("../" . $current_product['product_image']);
                }
                $image_path = "images/products/" . $new_filename;
            }
        }
    }
    
    if(update_product_ctr($id, $name, $price, $desc, $stock, $image_path, $color, $size)) {
        header("Location: ../admin/products.php?success=edit");
        exit();
    }
    
    header("Location: ../admin/products.php?error=edit");
    exit();
}
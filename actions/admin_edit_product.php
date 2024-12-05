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
    // Validate product_id is set and not empty
    if (!isset($_POST['product_id']) || empty($_POST['product_id'])) {
        header("Location: ../admin/products.php?error=invalid_id");
        exit();
    }

    $id = $_POST['product_id'];
    $name = trim($_POST['product_name']);
    $price = $_POST['product_price'];
    $desc = trim($_POST['product_desc']);
    $stock = $_POST['product_stock'];
    $size = isset($_POST['size']) ? $_POST['size'] : 'Medium';
    $color = isset($_POST['color']) ? $_POST['color'] : 'White';
    
    // Get current product details
    $current_product = get_one_product_ctr($id);
    if (!$current_product) {
        header("Location: ../admin/products.php?error=product_not_found");
        exit();
    }
    
    $image_path = $current_product['product_image'];
    
    // Handle image upload if present
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
    
    // Update product with correct parameter order
    if(update_product_ctr($id, $name, $price, $desc, $stock, $size, $color)) {
        header("Location: ../admin/products.php?success=edit");
        exit();
    }
    
    header("Location: ../admin/products.php?error=edit");
    exit();
}
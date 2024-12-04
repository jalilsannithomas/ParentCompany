<?php
session_start();
require_once("../settings/security.php");

include("../controllers/product_controller.php");

if(isset($_POST['add_product'])) {
    $name = trim($_POST['product_name']);
    $price = $_POST['product_price'];
    $stock = $_POST['product_stock'];
    $desc = trim($_POST['product_desc']);
    $size = $_POST['size'];
    $color = $_POST['color'];

    // Handle image upload
    if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
        $allowed = ['jpg', 'jpeg', 'png'];
        $filename = $_FILES['product_image']['name'];
        $filetype = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if(in_array($filetype, $allowed)) {
            $new_filename = time() . '_' . $filename;
            $upload_path = "../images/products/" . $new_filename;

            if(move_uploaded_file($_FILES['product_image']['tmp_name'], $upload_path)) {
                $image_path = "images/products/" . $new_filename;

                if(add_product_ctr($name, $price, $desc, $stock, $color, $size, $image_path)) {
                    header("Location: ../admin/products.php?success=add");
                    exit();
                } else {
                    error_log("Failed to add product: " . $name);
                    header("Location: ../admin/products.php?error=add");
                    exit();
                }
            } else {
                error_log("Failed to upload product image: " . $filename);
                header("Location: ../admin/products.php?error=image_upload");
                exit();
            }
        } else {
            error_log("Invalid file type for product image: " . $filename);
            header("Location: ../admin/products.php?error=invalid_image");
            exit();
        }
    } else {
        error_log("No product image uploaded");
        header("Location: ../admin/products.php?error=no_image");
        exit();
    }
}
?>
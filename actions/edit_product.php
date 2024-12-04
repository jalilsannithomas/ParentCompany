<?php
require_once('../settings/security.php'); 
include("../controllers/product_controller.php");
include("../settings/core.php");

session_start();
Security::check_session_timeout();

if(!isset($_SESSION['user_id']) || !is_admin()) {
   header("Location: ../view/login.php"); 
   exit();
}

if(isset($_POST['edit_product'])) {
   $id = $_POST['product_id'];
   $name = trim($_POST['product_name']); 
   $price = $_POST['product_price'];
   $desc = trim($_POST['product_desc']);
   $stock = $_POST['product_stock'];
   $color = $_POST['color'];
   $size = $_POST['size'];
   
   // Handle image upload if new image provided
   $image = $_POST['current_image'];
   if(isset($_FILES['product_image']) && $_FILES['product_image']['error'] === 0) {
      $target_dir = "../images/products/";
      $image = time() . '_' . basename($_FILES["product_image"]["name"]);
      $target_file = $target_dir . $image;

      // Validate and upload new image  
      $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));
      if($imageFileType == "jpg" || $imageFileType == "png" || $imageFileType == "jpeg") {
         if(move_uploaded_file($_FILES["product_image"]["tmp_name"], $target_file)) {
            // Delete old image if it exists and is different
            if($_POST['current_image'] && $_POST['current_image'] !== $image) {
               unlink($target_dir . $_POST['current_image']);
            }  
         } else {
            header("Location: ../view/admin.php?error=image_upload");
            exit();
         }
      } else {
         header("Location: ../view/admin.php?error=invalid_image");
         exit();
      }
   } 
   
   if(update_product_ctr($id, $name, $price, $desc, $stock, $color, $size, $image)) {
      header("Location: ../view/admin.php?success=product_updated");
   } else {
      header("Location: ../view/admin.php?error=update_failed");
   }
}
?>
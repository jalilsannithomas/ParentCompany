<?php
session_start();
error_reporting(E_ALL); // Enable error reporting
ini_set('display_errors', 1); // Display errors on the page
include("../settings/core.php");
include("../controllers/user_controller.php");

if(isset($_POST['update_profile'])) {
    $user_id = $_SESSION['user_id'];
    $name = sanitize_input($_POST['name']);
    $email = sanitize_input($_POST['email']);
    $phone = sanitize_input($_POST['phone']);
    
    if(update_user_ctr($user_id, $name, $email, $phone)) {
        header("Location: ../view/user_dashboard.php?success=1"); // Redirect to user dashboard after successful update
    } else {
        header("Location: ../view/profile.php?error=1"); 
    }
} else {
    header("Location: ../view/profile.php");
    exit();
}
?>
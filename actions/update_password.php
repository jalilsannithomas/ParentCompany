<?php
session_start();
include("../controllers/user_controller.php");

if(!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}

if(isset($_POST['update_password'])) {
    $user_id = $_SESSION['user_id'];
    $current = $_POST['current_password'];
    $new = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];

    if($new !== $confirm) {
        header("Location: ../profile.php?error=mismatch");
        exit();
    }

    if(update_password_ctr($user_id, $current, $new)) {
        header("Location: ../profile.php?success=password");
    } else {
        header("Location: ../profile.php?error=invalid");
    }
    exit();
}

header("Location: ../profile.php");
?>
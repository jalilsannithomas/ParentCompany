<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../controllers/user_controller.php");

if(isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $phone = $_POST['phone'];
    
    if(add_user_ctr($name, $email, $password, $phone)) {
        header("Location: ../view/login.php?success=1");
    } else {
        header("Location: ../view/register.php?error=1");
    }
}

<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
error_reporting(E_ALL);
ini_set('display_errors', 1);

include(__DIR__ . '/../controllers/user_controller.php');
include(__DIR__ . '/../settings/core.php');

if(isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    
    error_log("Login attempt for email: $email");
    
    $user = verify_user_ctr($email, $password);
    
    if($user) {
        error_log("Successful login for user: " . $user['user_id']);
        
        $_SESSION['user_id'] = $user['user_id'];
        $_SESSION['user_role'] = $user['user_role'];
        
        if($user['user_role'] == 'admin') {
            header("Location: ../admin/index.php");
        } else {
            header("Location: ../view/user_dashboard.php");
        }
        exit();
    }

    error_log("Failed login attempt for email: $email");
    header("Location: ../view/login.php?error=invalid");
    exit();
}

header("Location: ../view/login.php");
exit();
?>
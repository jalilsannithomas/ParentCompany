<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
ob_start();

function check_login() {
    if(!isset($_SESSION['user_id'])) {
        header("Location: ../login.php");
        exit();
    }
    return true;
}

function get_user_id() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

function check_role($role) {
    if(!isset($_SESSION['user_role']) || $_SESSION['user_role'] !== $role) {
        header("Location: ../index.php");
        exit();
    }
    return true;
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

function validate_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function validate_phone($phone) {
    return preg_match("/^(\+233|0)[0-9]{9}$/", $phone);
}

function check_email_exists($email) {
    require_once("../controllers/user_controller.php");
    return check_email_exists_ctr($email);
}

function generate_csrf_token() {
    if(!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}

function require_login() {
    if(!check_login()) {
        header("Location: ../login.php");
        exit();
    }
}

function require_admin() {
    require_login();
    if(!is_admin()) {
        header("Location: ../index.php");
        exit();
    }
}
?>
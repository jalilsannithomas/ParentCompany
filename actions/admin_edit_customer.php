<?php
require_once('../settings/security.php');
require_once('../controllers/user_controller.php');

// Ensure only admins can access this
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Get POST data
$user_id = $_POST['user_id'] ?? '';
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$phone = $_POST['phone'] ?? '';
$password = $_POST['password'] ?? '';

// Validate input
if (empty($user_id) || empty($name) || empty($email) || empty($phone)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required']);
    exit();
}

// Validate email format
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format']);
    exit();
}

// Check if email already exists for another user
$existing_user = get_user_by_email_ctr($email);
if ($existing_user && $existing_user['user_id'] != $user_id) {
    echo json_encode(['success' => false, 'message' => 'Email already exists']);
    exit();
}

// Update user information
$result = update_user_ctr($user_id, $name, $email, $phone);

// Update password if provided
if (!empty($password)) {
    if (strlen($password) < 8) {
        echo json_encode(['success' => false, 'message' => 'Password must be at least 8 characters long']);
        exit();
    }
    
    $password_result = update_user_password_ctr($user_id, $password);
    if (!$password_result) {
        echo json_encode(['success' => false, 'message' => 'Failed to update password']);
        exit();
    }
}

if ($result) {
    echo json_encode(['success' => true, 'message' => 'User updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update user']);
}

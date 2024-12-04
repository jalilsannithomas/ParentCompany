<?php
require_once(__DIR__ . '/../classes/user_class.php');

$user = new user_class();
$result = $user->update_admin_password();
if ($result) {
    echo "Admin password updated. New hash: $result";
} else {
    echo "Failed to update admin password.";
}
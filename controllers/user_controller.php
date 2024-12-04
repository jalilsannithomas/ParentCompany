<?php
include_once '../classes/user_class.php';

function check_email_exists_ctr($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    $user = new user_class();
    $result = $user->get_user_by_email($email);
    return !empty($result);
}

function add_user_ctr($name, $email, $password, $phone, $role = 'customer') {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return false;
    }

    if (strlen($password) < 8) {
        return false;
    }

    $name = htmlspecialchars(trim($name));
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($phone));

    $allowed_roles = ['customer', 'admin', 'staff'];
    $role = in_array($role, $allowed_roles) ? $role : 'customer';

    $user = new user_class();
    return $user->add_user($name, $email, $password, $phone, $role);
}

function verify_user_ctr($email, $password) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        error_log("Invalid email format: $email");
        return false;
    }

    if (empty($password)) {
        error_log("Empty password provided");
        return false;
    }

    $user = new user_class();
    $verified_user = $user->verify_user($email, $password);

    if (!$verified_user) {
        error_log("User verification failed for email: $email");
        return false;
    }

    return $verified_user;
}

function get_user_ctr($user_id) {
    if (!is_numeric($user_id)) {
        return false;
    }

    $user = new user_class();
    return $user->get_user($user_id);
}

function update_user_ctr($user_id, $name, $email, $phone, $current_user_role = null) {
    if (!is_numeric($user_id)) {
        return false;
    }

    $name = htmlspecialchars(trim($name));
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    $phone = htmlspecialchars(trim($phone));

    $user = new user_class();

    $existing = $user->get_user_by_email($email);
    if ($existing && $existing['user_id'] != $user_id) {
        return false;
    }

    if ($current_user_role !== null) {
        $current_user = $user->get_user($user_id);

        if (!$current_user || ($current_user_role !== 'admin' && $current_user_role !== $current_user['user_role'])) {
            return false;
        }
    }

    return $user->update_user($user_id, $name, $email, $phone);
}

function update_password_ctr($user_id, $current_password, $new_password, $current_user_role = null) {
    if (strlen($new_password) < 8) {
        return false;
    }

    $user = new user_class();
    $user_data = $user->get_user($user_id);

    if (!$user_data) {
        return false;
    }

    if (!password_verify($current_password, $user_data['user_pass'])) {
        return false;
    }

    if ($current_user_role !== null) {
        if ($current_user_role !== 'admin' && $current_user_role !== $user_data['user_role']) {
            return false;
        }
    }

    return $user->update_password($user_id, $new_password);
}

function delete_user_ctr($user_id, $current_user_role = null) {
    if (!is_numeric($user_id)) {
        return false;
    }

    $user = new user_class();

    if ($current_user_role !== null) {
        if ($current_user_role !== 'admin') {
            return false;
        }
    }

    return $user->delete_user($user_id);
}

function get_all_customers_ctr($current_user_role = null) {
    if ($current_user_role !== null && $current_user_role !== 'admin') {
        return false;
    }

    $user = new user_class();
    return $user->get_all_customers();
}

/**
 * Function to get all orders for a specific user
 * @param int $user_id
 * @return array
 */
function get_all_user_orders_ctr($user_id) { // Renamed function
    if (!is_numeric($user_id)) {
        return [];
    }

    $user = new user_class();
    return $user->get_user_orders($user_id); // Assuming `get_user_orders` is defined in user_class
}

/**
 * Function to get the count of orders for a specific user
 * @param int $user_id
 * @return int
 */
function get_user_order_count_ctr($user_id = null) {
    if (!is_numeric($user_id)) {
        return 0;
    }

    $user = new user_class();
    $orders = $user->get_user_orders($user_id); // Assuming `get_user_orders` is defined in user_class
    return count($orders);
}
?>

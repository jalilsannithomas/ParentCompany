<?php
// Session timeout configuration
define('SESSION_TIMEOUT', 1800); // 30 minutes in seconds

// Security class
class Security {
    // Input sanitization
    public static function sanitize_input($data) {
        $data = trim($data);
        $data = stripslashes($data);
        $data = htmlspecialchars($data);
        return $data;
    }

    // Email validation
    public static function validate_email($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    // Phone number validation (Ghana format)
    public static function validate_phone($phone) {
        return preg_match("/^(\+233|0)[0-9]{9}$/", $phone);
    }

    // Check session timeout
    public static function check_session_timeout() {
        if (isset($_SESSION['LAST_ACTIVITY'])) {
            if (time() - $_SESSION['LAST_ACTIVITY'] > SESSION_TIMEOUT) {
                // Session expired
                session_unset();
                session_destroy();
                header("Location: ../view/login.php?error=timeout");
                exit();
            }
        }
        $_SESSION['LAST_ACTIVITY'] = time();
    }
}

function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}
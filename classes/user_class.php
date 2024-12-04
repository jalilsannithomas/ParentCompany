<?php
require_once(dirname(__FILE__).'/../settings/db_class.php');

class user_class extends db_connection {
    public function __construct() {
        parent::__construct();
    }

    public function add_user($name, $email, $password, $phone, $role = 'customer') {
        $name = mysqli_real_escape_string($this->db, $name);
        $email = mysqli_real_escape_string($this->db, $email);
        $phone = mysqli_real_escape_string($this->db, $phone);
        $hash = password_hash($password, PASSWORD_DEFAULT);
        
        $sql = "INSERT INTO users (user_name, user_email, user_pass, user_phone, user_role)
                VALUES ('$name', '$email', '$hash', '$phone', '$role')";
        return $this->db_query($sql);
    }

    public function verify_user($email, $password) {
        $email = mysqli_real_escape_string($this->db, $email);
        $sql = "SELECT * FROM users WHERE user_email='$email' AND user_status=1";
        
        error_log("Verifying user with email: $email");
        error_log("SQL query: $sql");
        
        $user = $this->db_fetch_one($sql);
        
        if($user) {
            error_log("User found. Verifying password.");
            error_log("Stored hash: " . $user['user_pass']);
            error_log("Provided password: " . $password);
            
            if(password_verify($password, $user['user_pass'])) {
                error_log("Password verified successfully.");
                return $user;
            } else {
                error_log("Password verification failed.");
            }
        } else {
            error_log("User not found with email: $email");
        }
        
        return false;
    }

    public function get_user($user_id) {
        $user_id = mysqli_real_escape_string($this->db, $user_id);
        $sql = "SELECT user_id, user_name, user_email, user_phone, user_role, created_at 
                FROM users 
                WHERE user_id='$user_id' AND user_status=1";
        return $this->db_fetch_one($sql);
    }

    public function update_user($user_id, $name, $email, $phone) {
        $user_id = mysqli_real_escape_string($this->db, $user_id);
        $name = mysqli_real_escape_string($this->db, $name);
        $email = mysqli_real_escape_string($this->db, $email);
        $phone = mysqli_real_escape_string($this->db, $phone);

        $sql = "UPDATE users 
                SET user_name='$name', user_email='$email', user_phone='$phone' 
                WHERE user_id='$user_id'";
        return $this->db_query($sql);
    }

    public function update_password($user_id, $new_password) {
        $user_id = mysqli_real_escape_string($this->db, $user_id);
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        
        $sql = "UPDATE users 
                SET user_pass='$hash' 
                WHERE user_id='$user_id'";
        return $this->db_query($sql);
    }

    public function delete_user($user_id) {
        $user_id = mysqli_real_escape_string($this->db, $user_id);
        $sql = "UPDATE users 
                SET user_status=0 
                WHERE user_id='$user_id'";
        return $this->db_query($sql);
    }

    public function get_all_customers() {
        $sql = "SELECT * FROM users 
                WHERE user_role = 'customer' 
                AND user_status = 1 
                ORDER BY created_at DESC";
        return $this->db_fetch_all($sql);
    }

    public function get_user_by_email($email) {
        $email = mysqli_real_escape_string($this->db, $email);
        $sql = "SELECT * FROM users 
                WHERE user_email = '$email' 
                AND user_status = 1";
        return $this->db_fetch_one($sql);
    }

    public function update_admin_password() {
        $new_password = "Louise2000!";
        $hash = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET user_pass='$hash' WHERE user_email='admin@parentcompany.com'";
        if ($this->db_query($sql)) {
            return $hash;
        }
        return false;
    }

    /**
     * Get all orders for a specific user
     * @param int $user_id
     * @return array
     */
    public function get_user_orders($user_id) {
        $user_id = mysqli_real_escape_string($this->db, $user_id);
        $sql = "SELECT order_id, user_id, created_at, order_status, order_amount 
                FROM orders 
                WHERE user_id = '$user_id' 
                ORDER BY created_at DESC";
        return $this->db_fetch_all($sql);
    }
}
?>

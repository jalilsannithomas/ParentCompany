<?php
require_once(__DIR__ . '/db_connection.php'); // Use require_once
class admin_class extends db_connection {
    public function log_admin_action($admin_id, $action_type, $description) {
        $admin_id = mysqli_real_escape_string($this->db, $admin_id);
        $action_type = mysqli_real_escape_string($this->db, $action_type);
        $description = mysqli_real_escape_string($this->db, $description);

        $sql = "INSERT INTO admin_logs (admin_id, action_type, description)
                VALUES ('$admin_id', '$action_type', '$description')";
        return $this->db_query($sql);
    }

    public function get_admin_logs() {
        $sql = "SELECT al.*, u.user_name 
                FROM admin_logs al
                JOIN users u ON al.admin_id = u.user_id
                ORDER BY al.created_at DESC";
        return $this->db_fetch_all($sql);
    }

    public function get_admin_logs_by_user($admin_id) {
        $admin_id = mysqli_real_escape_string($this->db, $admin_id);
        $sql = "SELECT * FROM admin_logs 
                WHERE admin_id = '$admin_id' 
                ORDER BY created_at DESC";
        return $this->db_fetch_all($sql);
    }

    public function get_admin_logs_by_type($action_type) {
        $action_type = mysqli_real_escape_string($this->db, $action_type);
        $sql = "SELECT * FROM admin_logs 
                WHERE action_type = '$action_type' 
                ORDER BY created_at DESC";
        return $this->db_fetch_all($sql);
    }

    public function clear_old_logs($days) {
        $days = (int)$days;
        $sql = "DELETE FROM admin_logs 
                WHERE created_at < DATE_SUB(NOW(), INTERVAL $days DAY)";
        return $this->db_query($sql);
    }
}
?>
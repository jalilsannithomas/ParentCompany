<?php
require_once(dirname(__FILE__) . '/../settings/db_class.php');

class order_class extends db_connection {
    public function get_user_orders_count($user_id) {
        $user_id = mysqli_real_escape_string($this->db, $user_id);
        $sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = '$user_id'";
        $result = $this->db_query($sql);
        return $result ? $result->fetch_assoc()['count'] : 0;
    }

    public function get_all_orders() {
        $sql = "SELECT * FROM orders";
        $result = $this->db_query($sql);
        return $this->db_fetch_all($sql);
    }

    public function get_order_details($id) {
        $sql = "SELECT * FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function update_order_status($id, $status) {
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function get_recent_orders() {
        $sql = "SELECT * FROM orders ORDER BY created_at DESC LIMIT 10";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
    }

    public function get_orders_count() {
        $sql = "SELECT COUNT(*) as count FROM orders";
        $result = $this->db_query($sql);
        return $result ? $result->fetch_assoc()['count'] : 0;
    }

    public function delete_order($id) {
        $sql = "DELETE FROM orders WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        return $stmt->execute();
    }

    public function get_user_orders($user_id) {
        $user_id = mysqli_real_escape_string($this->db, $user_id);
        $sql = "SELECT * FROM orders WHERE user_id = '$user_id' ORDER BY created_at DESC";
        return $this->db_fetch_all($sql);
    }
}
?>
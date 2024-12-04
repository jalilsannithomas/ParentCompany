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

    public function get_order_details($order_id) {
        $sql = "SELECT o.*, u.user_name as customer_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                WHERE o.order_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("i", $order_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function get_order_summary($order_id) {
        $sql = "SELECT o.order_id, o.total_amount as order_amount, o.order_status, o.payment_status,
                u.name as user_name, u.phone as user_phone,
                COUNT(DISTINCT oi.product_id) as total_items,
                SUM(oi.quantity) as total_quantity
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                JOIN order_items oi ON o.order_id = oi.order_id
                WHERE o.order_id = ?
                GROUP BY o.order_id";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        return $stmt->get_result()->fetch_assoc();
    }

    public function create_order($user_id, $total_amount, $payment_status = 'pending') {
        $sql = "INSERT INTO orders (user_id, total_amount, payment_status, order_status) VALUES (?, ?, ?, 'pending')";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ids", $user_id, $total_amount, $payment_status);
        
        if ($stmt->execute()) {
            return $this->db->insert_id;
        }
        return false;
    }

    public function update_order_status($id, $status) {
        $sql = "UPDATE orders SET order_status = ? WHERE order_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("si", $status, $id);
        return $stmt->execute();
    }

    public function get_recent_orders() {
        $sql = "SELECT o.*, u.user_name as customer_name 
                FROM orders o 
                JOIN users u ON o.user_id = u.user_id 
                ORDER BY o.created_at DESC 
                LIMIT 10";
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

    public function get_order_items($order_id) {
        $sql = "SELECT oi.*, p.product_name 
                FROM order_items oi 
                JOIN products p ON oi.product_id = p.product_id 
                WHERE oi.order_id = ?";
        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            throw new Exception("Prepare failed: " . $this->db->error);
        }
        $stmt->bind_param("i", $order_id);
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: " . $stmt->error);
        }
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}
?>
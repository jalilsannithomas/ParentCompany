<?php
require_once(__DIR__ . '/../settings/db_class.php');


class cart_class extends db_connection {
    public function add_to_cart($pid, $uid, $qty, $size, $color) {
        $pid = $this->real_escape_string($pid);
        $uid = $this->real_escape_string($uid); 
        $qty = $this->real_escape_string($qty);
        $size = $this->real_escape_string($size);
        $color = $this->real_escape_string($color);
        
        // Check if product exists and has enough stock
        $product = $this->check_stock($pid, $qty);
        if(!$product) {
            return false;
        }

        // Check if already in cart 
        $existing = $this->get_one_cart_item($pid, $uid);
        if($existing) {
            // Update quantity instead
            return $this->update_cart_qty($pid, $uid, $existing['qty'] + $qty);
        }
        
        $sql = "INSERT INTO cart (p_id, user_id, qty, size, color) 
                VALUES ('$pid', '$uid', '$qty', '$size', '$color')";
        return $this->db_query($sql);
    }
    
    private function check_stock($pid, $qty) {
        $sql = "SELECT product_stock FROM products 
                WHERE product_id = '$pid' AND product_stock >= $qty";
        return $this->db_fetch_one($sql);
    }
    
    public function get_cart_items($uid) {
        $sql = "SELECT cart.*, products.product_name, products.product_price, products.product_image 
                FROM cart
                JOIN products ON cart.p_id = products.product_id
                WHERE cart.user_id = '$uid'";
        return $this->db_fetch_all($sql);
    }
    
    public function get_one_cart_item($pid, $uid) {
        $sql = "SELECT * FROM cart 
                WHERE p_id = '$pid' AND user_id = '$uid'";
        return $this->db_fetch_one($sql);
    }
    
    public function get_cart_total($uid) {
        $sql = "SELECT SUM(cart.qty * products.product_price) as total 
                FROM cart
                JOIN products ON cart.p_id = products.product_id
                WHERE cart.user_id = '$uid'";
        return $this->db_fetch_one($sql);
    }
    
    public function update_cart_qty($pid, $uid, $qty) {
        // Check stock availability first
        if(!$this->check_stock($pid, $qty)) {
            return false;
        }
        
        $sql = "UPDATE cart 
                SET qty = '$qty' 
                WHERE p_id = '$pid' AND user_id = '$uid'";
        return $this->db_query($sql);
    }
    
    public function remove_from_cart($pid, $uid) {
        $sql = "DELETE FROM cart 
                WHERE p_id = '$pid' AND user_id = '$uid'";
        return $this->db_query($sql);
    }
    
    public function clear_cart($uid) {
        $sql = "DELETE FROM cart WHERE user_id = '$uid'";
        return $this->db_query($sql);
    }
    
    public function get_cart_count($uid) {
        $sql = "SELECT COUNT(*) as count FROM cart WHERE user_id = '$uid'";
        $result = $this->db_fetch_one($sql);
        return $result ? $result['count'] : 0;
    }
}
?>
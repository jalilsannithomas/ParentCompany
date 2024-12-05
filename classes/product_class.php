<?php
require_once(__DIR__ . '/../settings/db_class.php');

class Product extends db_connection {
    // Get all products
    public function get_all_products() {
        $sql = "SELECT * FROM products";
        return $this->db_fetch_all($sql);
    }
    
    // Get one product
    public function get_one_product($id) {
        $sql = "SELECT * FROM products WHERE product_id = ?";
        $params = [$id];
        return $this->db_fetch_one($sql, $params);
    }
    
    // Add product
    public function add_product($name, $price, $desc, $category, $brand, $image, $keywords) {
        $sql = "INSERT INTO products (product_name, product_price, product_desc, category, brand, product_image, keywords) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $params = [$name, $price, $desc, $category, $brand, $image, $keywords];
        return $this->db_query($sql, $params);
    }
    
    // Update product
    public function update_product($id, $name, $price, $desc, $stock, $size, $color) {
        // Validate that id is not empty
        if (empty($id)) {
            error_log("Product update failed: Empty ID");
            return false;
        }
        
        $sql = "UPDATE products 
                SET product_name = ?, 
                    product_price = ?, 
                    product_desc = ?, 
                    product_stock = ?,
                    size = ?,
                    color = ?
                WHERE product_id = ?";
        $params = [$name, $price, $desc, $stock, $size, $color, $id];
        $result = $this->db_query($sql, $params);
        
        if ($result === false) {
            error_log("Product update failed for ID: " . $id);
            return false;
        }
        
        return true;
    }
    
    // Delete product
    public function delete_product($id) {
        $sql = "DELETE FROM products WHERE product_id = ?";
        $params = [$id];
        return $this->db_query($sql, $params);
    }
    
    // Update stock
    public function update_stock($product_id, $quantity) {
        // First check if we have enough stock
        $current_stock = $this->get_one_product($product_id)['stock'];
        if ($current_stock < $quantity) {
            return false;
        }
        
        $sql = "UPDATE products SET stock = stock - ? WHERE product_id = ? AND stock >= ?";
        $params = [$quantity, $product_id, $quantity];
        return $this->db_query($sql, $params);
    }
    
    // Search products
    public function search_products($term) {
        $sql = "SELECT * FROM products WHERE product_name LIKE ? OR product_desc LIKE ? OR keywords LIKE ?";
        $term = "%$term%";
        $params = [$term, $term, $term];
        return $this->db_fetch_all($sql, $params);
    }
    
    // Filter products
    public function filter_products($category, $brand, $min_price, $max_price) {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = [];
        
        if ($category) {
            $sql .= " AND category = ?";
            $params[] = $category;
        }
        
        if ($brand) {
            $sql .= " AND brand = ?";
            $params[] = $brand;
        }
        
        if ($min_price) {
            $sql .= " AND product_price >= ?";
            $params[] = $min_price;
        }
        
        if ($max_price) {
            $sql .= " AND product_price <= ?";
            $params[] = $max_price;
        }
        
        return $this->db_fetch_all($sql, $params);
    }
    
    // Get product count
    public function get_product_count() {
        $sql = "SELECT COUNT(*) as total FROM products";
        $result = $this->db_fetch_one($sql);
        return $result['total'];
    }
}
?>

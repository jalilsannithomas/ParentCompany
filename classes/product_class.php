<?php
require_once(dirname(__FILE__).'/../settings/db_class.php');

class product_class extends db_connection {
    public function add_product($name, $price, $desc, $stock, $color, $size, $image) {
        $sql = "INSERT INTO products (product_name, product_price, product_desc, product_stock, color, size, product_image) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sdsisss", $name, $price, $desc, $stock, $color, $size, $image);
        return $stmt->execute();
    }

    public function get_all_products() {
        $sql = "SELECT * FROM products WHERE product_stock > 0";
        error_log("SQL Query: " . $sql); // Log the query instead of echoing
        $result = $this->db_query($sql);
        if (!$result) {
            error_log("Error executing query: " . $this->db->error); // Log the error
            return []; // Return an empty array instead of false
        }
        return $this->db_fetch_all($sql);
    }

    public function get_one_product($id) {
        $sql = "SELECT * FROM products WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function update_product($id, $name, $price, $desc, $stock, $image, $color, $size) {
        $sql = "UPDATE products SET product_name = ?, product_price = ?, product_desc = ?, product_stock = ?, product_image = ?, color = ?, size = ? WHERE product_id = ?";
        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("sdsisssi", $name, $price, $desc, $stock, $image, $color, $size, $id);
        return $stmt->execute();
    }

    public function delete_product($id) {
        // First check if product exists and get its details
        $product = $this->get_one_product($id);
        if (!$product) {
            return false;
        }

        try {
            // Start transaction
            $this->db->begin_transaction();

            // Delete from cart first (due to foreign key constraint)
            $sql = "DELETE FROM cart WHERE p_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Set product_id to NULL in order_details (soft delete approach for order history)
            $sql = "UPDATE order_details SET product_id = NULL WHERE product_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Finally delete the product
            $sql = "DELETE FROM products WHERE product_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("i", $id);
            $stmt->execute();

            // Commit transaction
            $this->db->commit();
            return true;

        } catch (Exception $e) {
            // Rollback on error
            $this->db->rollback();
            error_log("Error deleting product: " . $e->getMessage());
            return false;
        }
    }

    public function get_product_count() {
        $sql = "SELECT COUNT(*) as count FROM products";
        $result = $this->db_fetch_one($sql);
        return $result['count'];
    }

    public function filter_products($size, $color, $price_range) {
        $sql = "SELECT * FROM products WHERE 1=1";
        $params = array();

        if (!empty($size)) {
            $sql .= " AND size = ?";
            $params[] = $size;
        }
        if (!empty($color)) {
            $sql .= " AND color = ?";
            $params[] = $color;
        }
        if (!empty($price_range)) {
            list($min, $max) = explode('-', $price_range);
            if ($max === '+') {
                $sql .= " AND product_price >= ?";
                $params[] = $min;
            } else {
                $sql .= " AND product_price BETWEEN ? AND ?";
                $params[] = $min;
                $params[] = $max;
            }
        }

        $sql .= " AND product_stock > 0";

        $stmt = $this->db->prepare($sql);
        if (!empty($params)) {
            $types = str_repeat('s', count($params));
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }
}

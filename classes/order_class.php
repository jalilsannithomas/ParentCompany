<?php
require_once(dirname(__FILE__) . '/../settings/db_class.php');

class order_class extends db_connection {
    public function __construct() {
        parent::__construct();
    }

    public function get_user_orders_count($user_id) {
        $user_id = mysqli_real_escape_string($this->db, $user_id);
        $sql = "SELECT COUNT(*) as count FROM orders WHERE user_id = '$user_id'";
        $result = $this->db_query($sql);
        return $result ? $result->fetch_assoc()['count'] : 0;
    }

    public function get_all_orders() {
        try {
            // Debug: Log the start of the function
            error_log("Starting get_all_orders function");
            
            $sql = "SELECT o.order_id, o.user_id, o.order_status, o.created_at, o.total_amount,
                           u.user_name, u.name,
                           COALESCE(SUM(od.quantity), 0) as total_items
                    FROM orders o 
                    LEFT JOIN users u ON o.user_id = u.user_id
                    LEFT JOIN order_details od ON o.order_id = od.order_id
                    GROUP BY o.order_id, o.user_id, o.order_status, o.created_at, u.user_name, u.name, o.total_amount
                    ORDER BY o.created_at DESC";
            
            // Debug: Log the SQL query
            error_log("SQL Query: " . $sql);
            
            $result = $this->db_query($sql);
            
            if (!$result) {
                error_log("Database query failed: " . mysqli_error($this->db));
                return [];
            }
            
            $orders = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $orders[] = $row;
            }
            
            // Debug: Log the number of orders found
            error_log("Found " . count($orders) . " orders");
            
            return $orders;
        } catch (Exception $e) {
            error_log("Error in get_all_orders: " . $e->getMessage());
            return [];
        }
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

    public function create_order($user_id, $amount) {
        try {
            $this->db->begin_transaction();
            
            // Debug: Log order creation attempt
            error_log("Creating order for user_id: $user_id with amount: $amount");
            
            // Create the order with order_status
            $sql = "INSERT INTO orders (user_id, total_amount, order_status) VALUES (?, ?, 'pending')";
            $stmt = $this->db->prepare($sql);
            $stmt->bind_param("id", $user_id, $amount);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to create order: " . $stmt->error);
            }
            
            $order_id = $this->db->insert_id;
            error_log("Created order with ID: $order_id");
            
            // Get cart items
            $cart_sql = "SELECT p_id, qty FROM cart WHERE user_id = ?";
            $cart_stmt = $this->db->prepare($cart_sql);
            $cart_stmt->bind_param("i", $user_id);
            $cart_stmt->execute();
            $cart_result = $cart_stmt->get_result();
            
            // Debug: Log cart items
            error_log("Found " . $cart_result->num_rows . " cart items");
            
            // Add order details and update stock
            while ($item = $cart_result->fetch_assoc()) {
                // Add to order_details
                $detail_sql = "INSERT INTO order_details (order_id, product_id, quantity) VALUES (?, ?, ?)";
                $detail_stmt = $this->db->prepare($detail_sql);
                $detail_stmt->bind_param("iii", $order_id, $item['p_id'], $item['qty']);
                
                if (!$detail_stmt->execute()) {
                    throw new Exception("Failed to add order details: " . $detail_stmt->error);
                }
                
                error_log("Added order detail for product: " . $item['p_id'] . " with quantity: " . $item['qty']);
                
                // Update product stock
                $stock_sql = "UPDATE products SET product_stock = product_stock - ? WHERE product_id = ?";
                $stock_stmt = $this->db->prepare($stock_sql);
                $stock_stmt->bind_param("ii", $item['qty'], $item['p_id']);
                
                if (!$stock_stmt->execute()) {
                    throw new Exception("Failed to update product stock: " . $stock_stmt->error);
                }
            }
            
            // Clear the user's cart
            $clear_cart_sql = "DELETE FROM cart WHERE user_id = ?";
            $clear_cart_stmt = $this->db->prepare($clear_cart_sql);
            $clear_cart_stmt->bind_param("i", $user_id);
            
            if (!$clear_cart_stmt->execute()) {
                throw new Exception("Failed to clear cart: " . $clear_cart_stmt->error);
            }
            
            $this->db->commit();
            error_log("Order creation completed successfully");
            return $order_id;
            
        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Order creation failed: " . $e->getMessage());
            return false;
        }
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
        try {
            $sql = "SELECT COUNT(*) as count FROM orders";
            error_log("Getting orders count with query: " . $sql);
            
            $result = $this->db_query($sql);
            if (!$result) {
                error_log("Failed to get orders count: " . mysqli_error($this->db));
                return 0;
            }
            
            $count = mysqli_fetch_assoc($result)['count'];
            error_log("Found $count orders");
            return $count;
            
        } catch (Exception $e) {
            error_log("Error in get_orders_count: " . $e->getMessage());
            return 0;
        }
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

    public function add_order_detail($order_id, $product_id, $quantity, $size = null, $color = null) {
        try {
            error_log("Adding order detail - Order ID: $order_id, Product ID: $product_id, Quantity: $quantity");
            
            $sql = "INSERT INTO order_details (order_id, product_id, quantity, size, color) VALUES (?, ?, ?, ?, ?)";
            $stmt = $this->db->prepare($sql);
            if (!$stmt) {
                throw new Exception("Failed to prepare statement: " . $this->db->error);
            }
            
            $stmt->bind_param("iiiss", $order_id, $product_id, $quantity, $size, $color);
            
            if (!$stmt->execute()) {
                throw new Exception("Failed to add order detail: " . $stmt->error);
            }
            
            error_log("Successfully added order detail");
            return true;
            
        } catch (Exception $e) {
            error_log("Error in add_order_detail: " . $e->getMessage());
            return false;
        }
    }

    // Process checkout from cart
    public function process_checkout($user_id, $cart_items, $payment_details) {
        try {
            $this->db->begin_transaction();

            // Calculate total amount
            $total_amount = 0;
            foreach ($cart_items as $item) {
                $total_amount += $item['price'] * $item['quantity'];
            }

            // Create order
            $user_id = mysqli_real_escape_string($this->db, $user_id);
            $sql = "INSERT INTO orders (user_id, total_amount, order_status, created_at) 
                    VALUES ('$user_id', '$total_amount', 'pending', NOW())";
            
            $result = $this->db_query($sql);
            if (!$result) {
                throw new Exception("Failed to create order");
            }

            $order_id = $this->db->insert_id;

            // Process payment
            $payment_result = $this->process_payment($payment_details, $total_amount);
            if (!$payment_result['success']) {
                throw new Exception("Payment failed: " . $payment_result['message']);
            }

            // Add order details and reduce stock
            foreach ($cart_items as $item) {
                $product_id = mysqli_real_escape_string($this->db, $item['product_id']);
                $quantity = mysqli_real_escape_string($this->db, $item['quantity']);
                $price = mysqli_real_escape_string($this->db, $item['price']);
                
                // Add order detail
                $sql = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                        VALUES ('$order_id', '$product_id', '$quantity', '$price')";
                if (!$this->db_query($sql)) {
                    throw new Exception("Failed to add order details");
                }

                // Reduce stock
                $sql = "UPDATE products 
                        SET stock = stock - '$quantity' 
                        WHERE product_id = '$product_id' AND stock >= '$quantity'";
                if (!$this->db_query($sql)) {
                    throw new Exception("Failed to update stock");
                }

                // Verify stock was actually reduced
                $sql = "SELECT stock FROM products WHERE product_id = '$product_id'";
                $result = $this->db_query($sql);
                $stock = $result->fetch_assoc()['stock'];
                if ($stock < 0) {
                    throw new Exception("Insufficient stock for product ID: " . $product_id);
                }
            }

            // Update order status to confirmed
            $sql = "UPDATE orders SET order_status = 'confirmed' WHERE order_id = '$order_id'";
            if (!$this->db_query($sql)) {
                throw new Exception("Failed to update order status");
            }

            // Clear cart
            $sql = "DELETE FROM cart WHERE user_id = '$user_id'";
            if (!$this->db_query($sql)) {
                throw new Exception("Failed to clear cart");
            }

            $this->db->commit();
            return [
                'success' => true,
                'order_id' => $order_id,
                'message' => 'Order processed successfully'
            ];

        } catch (Exception $e) {
            $this->db->rollback();
            error_log("Checkout process failed: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    private function process_payment($payment_details, $amount) {
        // TODO: Integrate with actual payment gateway
        // This is a placeholder implementation
        try {
            // Validate payment details
            if (empty($payment_details['payment_method'])) {
                throw new Exception("Payment method is required");
            }

            // In a real implementation, you would:
            // 1. Integrate with a payment gateway (e.g., Stripe, PayPal)
            // 2. Process the payment
            // 3. Handle the response

            // Simulate payment processing
            return [
                'success' => true,
                'transaction_id' => uniqid('trans_'),
                'message' => 'Payment processed successfully'
            ];

        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    public function get_order_confirmation($order_id) {
        $order_id = mysqli_real_escape_string($this->db, $order_id);
        
        $sql = "SELECT o.*, u.name as customer_name, u.email,
                       od.product_id, od.quantity, od.price,
                       p.product_name
                FROM orders o
                JOIN users u ON o.user_id = u.user_id
                JOIN order_details od ON o.order_id = od.order_id
                JOIN products p ON od.product_id = p.product_id
                WHERE o.order_id = '$order_id'";
        
        $result = $this->db_query($sql);
        if (!$result) {
            return null;
        }

        $order_details = [];
        $order_info = null;

        while ($row = $result->fetch_assoc()) {
            if (!$order_info) {
                $order_info = [
                    'order_id' => $row['order_id'],
                    'customer_name' => $row['customer_name'],
                    'email' => $row['email'],
                    'total_amount' => $row['total_amount'],
                    'order_status' => $row['order_status'],
                    'created_at' => $row['created_at']
                ];
            }

            $order_details[] = [
                'product_name' => $row['product_name'],
                'quantity' => $row['quantity'],
                'price' => $row['price'],
                'subtotal' => $row['quantity'] * $row['price']
            ];
        }

        return [
            'order_info' => $order_info,
            'order_details' => $order_details
        ];
    }
}
?>
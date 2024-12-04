<?php
require_once(__DIR__ . '/../settings/db_class.php');
class payment_class extends db_connection {
    public function add_payment($order_id, $amount, $phone, $reference) {
        $phone = $this->real_escape_string($phone);
        $reference = $this->real_escape_string($reference);
        
        $sql = "INSERT INTO payments (order_id, amount, phone_number, reference_id) 
                VALUES ('$order_id', '$amount', '$phone', '$reference')";
        return $this->db_query($sql);
    }

    public function log_payment_status($payment_id, $status_change) {
        $status_change = $this->real_escape_string($status_change);
        
        $sql = "INSERT INTO payment_logs (payment_id, status_change) 
                VALUES ('$payment_id', '$status_change')";
        return $this->db_query($sql);
    }

    public function get_payment($reference) {
        $sql = "SELECT * FROM payments 
                WHERE reference_id='$reference'";
        return $this->db_fetch_one($sql);
    }

    public function get_order_payment($order_id) {
        $sql = "SELECT * FROM payments 
                WHERE order_id='$order_id'";
        return $this->db_fetch_one($sql);
    }

    public function update_payment_status($reference, $status) {
        $valid_statuses = ['pending', 'completed', 'failed'];
        if(!in_array($status, $valid_statuses)) {
            return false;
        }
        
        $sql = "UPDATE payments 
                SET status='$status' 
                WHERE reference_id='$reference'";
        
        if($this->db_query($sql)) {
            if($status === 'completed') {
                $payment = $this->get_payment($reference);
                if($payment) {
                    $order = new order_class();
                    $order->update_order_status($payment['order_id'], 'processing');
                }
            }
            return true;
        }
        return false;
    }
}
?>
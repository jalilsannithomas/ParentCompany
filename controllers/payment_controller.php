<?php

include("../classes/payment_class.php");

function add_payment_ctr($order_id, $amount, $phone, $reference) {
    // Validate inputs
    if(!is_numeric($order_id) || !is_numeric($amount) || $amount <= 0) {
        return false;
    }

    // Basic phone number validation
    if(!preg_match("/^\+?[0-9]{10,15}$/", $phone)) {
        return false;
    }

    $payment = new payment_class();
    return $payment->add_payment($order_id, $amount, $phone, $reference);
}

function log_payment_status_ctr($payment_id, $status_change) {
    if(!is_numeric($payment_id)) {
        return false;
    }

    $payment = new payment_class();
    return $payment->log_payment_status($payment_id, $status_change);
}

function get_payment_ctr($reference) {
    $payment = new payment_class();
    return $payment->get_payment($reference);
}

function get_order_payment_ctr($order_id) {
    if(!is_numeric($order_id)) {
        return false;
    }

    $payment = new payment_class();
    return $payment->get_order_payment($order_id);
}

function update_payment_status_ctr($reference, $status) {
    $valid_statuses = ['pending', 'completed', 'failed'];
    if(!in_array($status, $valid_statuses)) {
        return false;
    }

    $payment = new payment_class();
    return $payment->update_payment_status($reference, $status);
}
?>
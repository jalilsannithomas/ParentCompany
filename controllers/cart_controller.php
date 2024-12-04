<?php
include("../classes/cart_class.php");

function add_to_cart_ctr($p_id, $u_id, $qty, $size = 'Medium', $color = 'White') {
    if(!is_numeric($p_id) || !is_numeric($u_id) || !is_numeric($qty) || $qty <= 0) {
        return false;
    }
    
    $valid_sizes = ['Medium', 'Large'];
    $valid_colors = ['White', 'Black'];
    
    if(!in_array($size, $valid_sizes) || !in_array($color, $valid_colors)) {
        return false;
    }
    
    $cart = new cart_class();
    return $cart->add_to_cart($p_id, $u_id, $qty, $size, $color);
}

function get_cart_items_ctr($user_id) {
    if(!is_numeric($user_id)) {
        return false;
    }
    
    $cart = new cart_class();
    return $cart->get_cart_items($user_id);
}

function get_cart_total_ctr($user_id) {
    if(!is_numeric($user_id)) {
        return false;
    }
    
    $cart = new cart_class();
    return $cart->get_cart_total($user_id);
}

function update_cart_qty_ctr($p_id, $u_id, $qty) {
    if(!is_numeric($p_id) || !is_numeric($u_id) || !is_numeric($qty) || $qty <= 0) {
        return false;
    }
    
    $cart = new cart_class();
    return $cart->update_cart_qty($p_id, $u_id, $qty);
}

function remove_from_cart_ctr($p_id, $u_id) {
    if(!is_numeric($p_id) || !is_numeric($u_id)) {
        return false;
    }
    
    $cart = new cart_class();
    return $cart->remove_from_cart($p_id, $u_id);
}

function clear_cart_ctr($user_id) {
    if(!is_numeric($user_id)) {
        return false;
    }
    
    $cart = new cart_class();
    return $cart->clear_cart($user_id);
}

function get_cart_count_ctr($user_id) {
    if(!is_numeric($user_id)) {
        return 0;
    }
    
    $cart = new cart_class();
    return $cart->get_cart_count($user_id);
}
?>
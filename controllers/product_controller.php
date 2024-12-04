<?php
require_once(dirname(__FILE__) . '/../classes/product_class.php');

function add_product_ctr($name, $price, $desc, $stock, $color, $size, $image) {
    $product = new product_class();
    return $product->add_product($name, $price, $desc, $stock, $color, $size, $image);
}

function get_all_products_ctr() {
    $product = new product_class();
    return $product->get_all_products();
}

function get_one_product_ctr($id) {
    $product = new product_class();
    return $product->get_one_product($id);
}

function update_product_ctr($id, $name, $price, $desc, $stock, $color, $size, $image) {
    $product = new product_class();
    return $product->update_product($id, $name, $price, $desc, $stock, $color, $size, $image);
}

function delete_product_ctr($id) {
    $product = new product_class();
    return $product->delete_product($id);
}

function get_product_count_ctr() {
    $product = new product_class();
    return $product->get_product_count();
}

function filter_products_ctr($size, $color, $price_range) {
    $product = new product_class();
    return $product->filter_products($size, $color, $price_range);
}
?>
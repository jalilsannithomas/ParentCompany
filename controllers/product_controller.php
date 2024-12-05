<?php
require_once(__DIR__ . '/../classes/product_class.php');

// Get all products
function get_all_products_ctr() {
    $product = new Product();
    return $product->get_all_products();
}

// Get one product
function get_one_product_ctr($id) {
    $product = new Product();
    return $product->get_one_product($id);
}

// Add product
function add_product_ctr($name, $price, $desc, $category, $brand, $image, $keywords) {
    $product = new Product();
    return $product->add_product($name, $price, $desc, $category, $brand, $image, $keywords);
}

// Update product
function update_product_ctr($id, $name, $price, $desc, $stock, $size, $color) {
    $product = new Product();
    return $product->update_product($id, $name, $price, $desc, $stock, $size, $color);
}

// Delete product
function delete_product_ctr($id) {
    $product = new Product();
    return $product->delete_product($id);
}

// Update product stock
function update_product_stock_ctr($product_id, $quantity) {
    $product = new Product();
    return $product->update_stock($product_id, $quantity);
}

// Search products
function search_products_ctr($term) {
    $product = new Product();
    return $product->search_products($term);
}

// Filter products
function filter_products_ctr($category, $brand, $min_price, $max_price) {
    $product = new Product();
    return $product->filter_products($category, $brand, $min_price, $max_price);
}

// Get product count
function get_product_count_ctr() {
    $product = new Product();
    return $product->get_product_count();
}
?>
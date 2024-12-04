<?php
include_once("../classes/raffle_class.php");

function add_raffle_entry_ctr($name, $phone, $instagram) {
    // Validate name
    if(empty($name) || strlen($name) > 100) {
        return false;
    }
    
    // Validate phone number
    if(!preg_match("/^\+?[0-9]{10,15}$/", $phone)) {
        return false;
    }
    
    // Validate Instagram handle
    if(!preg_match("/^@?[a-zA-Z0-9._]{1,30}$/", $instagram)) {
        return false;
    }
    
    // Remove @ if present at start of Instagram handle
    $instagram = ltrim($instagram, '@');
    
    $raffle = new raffle_class();
    return $raffle->add_raffle_entry($name, $phone, $instagram);
}

function get_all_entries_ctr() {
    $raffle = new raffle_class();
    return $raffle->get_all_entries();
}

function check_duplicate_entry_ctr($phone) {
    if(!preg_match("/^\+?[0-9]{10,15}$/", $phone)) {
        return true; // Return true to prevent invalid numbers from entering
    }
    
    $raffle = new raffle_class();
    return $raffle->check_duplicate_entry($phone);
}

function delete_entry_ctr($entry_id) {
    if(!is_numeric($entry_id)) {
        return false;
    }
    
    $raffle = new raffle_class();
    return $raffle->delete_entry($entry_id);
}

function get_recent_entries_ctr($limit = 10) {
    $limit = (int)$limit;
    if($limit <= 0) {
        $limit = 10;
    }
    
    $raffle = new raffle_class();
    return $raffle->get_recent_entries($limit);
}

function get_entry_count_ctr() {
    $raffle = new raffle_class();
    return $raffle->get_entry_count();
}

function select_winner_ctr() {
    $raffle = new raffle_class();
    return $raffle->select_winner();
}
?>
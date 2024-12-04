<?php
//connect to admin class
include("../classes/admin_class.php");

//--INSERT--//
function log_admin_action($admin_id, $action_type, $description) {
    $admin = new admin_class();
    return $admin->log_admin_action($admin_id, $action_type, $description);
}

//--SELECT--//
function get_admin_logs_ctr() {
    $admin = new admin_class();
    return $admin->get_admin_logs();
}

function get_admin_logs_by_user_ctr($admin_id) {
    $admin = new admin_class();
    return $admin->get_admin_logs_by_user($admin_id);
}

function get_admin_logs_by_type_ctr($action_type) {
    $admin = new admin_class();
    return $admin->get_admin_logs_by_type($action_type);
}

//--DELETE--//
function clear_old_logs_ctr($days) {
    $admin = new admin_class();
    return $admin->clear_old_logs($days);
}
?>

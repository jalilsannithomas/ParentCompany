<?php
require 'settings/db_class.php';

$email = 'user@example.com'; // Change this to the email you want to check
$db = new db_connection();
$sql = "SELECT * FROM users WHERE user_email = '$email' AND user_status = 1";
$result = $db->db_query($sql);

if ($result) {
    print_r($result);
} else {
    echo "No user found or query failed.";
}
?>

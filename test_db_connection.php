<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('settings/db_class.php');

try {
    // Test database connection
    $db = new db_connection();
    echo "Database connection successful!\n";
    
    // Test raffle entries table
    $result = $db->db_query("SELECT COUNT(*) as count FROM raffle_entries");
    if ($result) {
        $row = $result->fetch_assoc();
        echo "Number of raffle entries: " . $row['count'] . "\n";
        
        // Test getting all entries
        $entries = $db->db_query("SELECT * FROM raffle_entries LIMIT 1");
        if ($entries && $entries->num_rows > 0) {
            echo "Successfully retrieved raffle entry.\n";
            $entry = $entries->fetch_assoc();
            echo "Sample entry: \n";
            print_r($entry);
        } else {
            echo "No raffle entries found in the table.\n";
        }
    } else {
        echo "Error querying raffle_entries table: " . $db->db->error . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

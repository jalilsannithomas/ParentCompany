<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('settings/db_cred.php');

try {
    // Create a direct database connection
    $conn = new mysqli(SERVER, USERNAME, PASSWD, DATABASE);
    
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    
    echo "Connected to database successfully\n";
    
    // Check if raffle_entries table exists
    $result = $conn->query("SHOW TABLES LIKE 'raffle_entries'");
    if ($result->num_rows > 0) {
        echo "raffle_entries table exists\n";
        
        // Get table structure
        $result = $conn->query("DESCRIBE raffle_entries");
        echo "\nTable structure:\n";
        while ($row = $result->fetch_assoc()) {
            print_r($row);
        }
        
        // Count entries
        $result = $conn->query("SELECT COUNT(*) as count FROM raffle_entries");
        $count = $result->fetch_assoc();
        echo "\nNumber of entries: " . $count['count'] . "\n";
        
        // Get a sample entry
        $result = $conn->query("SELECT * FROM raffle_entries LIMIT 1");
        if ($result->num_rows > 0) {
            echo "\nSample entry:\n";
            print_r($result->fetch_assoc());
        } else {
            echo "\nNo entries found in the table\n";
        }
    } else {
        echo "raffle_entries table does not exist\n";
    }
    
    $conn->close();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>

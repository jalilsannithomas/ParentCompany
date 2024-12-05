<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$username = 'jalil.sanni-thomas';
$password = 'Louise2000';
$database = 'webtech_fall2024_jalil_sanni-thomas';

// Array of possible hostnames to try
$hosts = [
    'localhost',
    '127.0.0.1',
    'mysql',
    'db',
    'localhost:3308'
];

foreach ($hosts as $host) {
    echo "\nTrying to connect to host: $host\n";
    
    try {
        $conn = new mysqli($host, $username, $password, $database);
        
        if ($conn->connect_error) {
            echo "Connection failed: " . $conn->connect_error . "\n";
            continue;
        }
        
        echo "SUCCESS! Connected successfully to host: $host\n";
        
        // Test the connection by running a simple query
        $result = $conn->query("SELECT 1");
        if ($result) {
            echo "Database query successful!\n";
            
            // Try to check if users table exists
            $result = $conn->query("SHOW TABLES LIKE 'users'");
            if ($result->num_rows > 0) {
                echo "Users table exists!\n";
            } else {
                echo "Users table does not exist.\n";
            }
        }
        
        $conn->close();
        break; // Exit the loop if connection successful
        
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage() . "\n";
    }
}
?>

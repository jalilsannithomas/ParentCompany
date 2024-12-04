<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include("../controllers/raffle_controller.php");

if(isset($_POST['enter_raffle'])) {
    try {
        // Log the POST data
        error_log("Raffle entry attempt - POST data: " . print_r($_POST, true));
        
        // Sanitize inputs
        $name = trim($_POST['name']);
        $phone = trim($_POST['phone']);
        $instagram = trim($_POST['instagram']);
        
        // Validate inputs
        if(empty($name) || empty($phone) || empty($instagram)) {
            error_log("Raffle entry validation failed - Empty fields");
            header("Location: ../view/raffle.php?error=validation");
            exit();
        }
        
        // Log the sanitized data
        error_log("Sanitized data - Name: $name, Phone: $phone, Instagram: $instagram");
        
        // Check for duplicate entry
        if(!check_duplicate_entry_ctr($phone)) {
            // Add new entry
            $result = add_raffle_entry_ctr($name, $phone, $instagram);
            if($result) {
                error_log("Raffle entry successful for phone: $phone");
                header("Location: ../view/raffle.php?success=1");
                exit();
            } else {
                error_log("Failed to add raffle entry to database");
                header("Location: ../view/raffle.php?error=db");
                exit();
            }
        } else {
            error_log("Duplicate phone number detected: $phone");
            header("Location: ../view/raffle.php?error=duplicate");
            exit();
        }
    } catch (Exception $e) {
        error_log("Exception in raffle entry: " . $e->getMessage());
        header("Location: ../view/raffle.php?error=system");
        exit();
    }
} else {
    error_log("No raffle entry form data received");
    header("Location: ../view/raffle.php?error=nodata");
    exit();
}
?>
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
        
        // Log sanitized inputs
        error_log("Sanitized inputs - Name: $name, Phone: $phone, Instagram: $instagram");
        
        // Validate inputs
        if(empty($name) || empty($phone) || empty($instagram)) {
            error_log("Validation failed - Empty fields detected");
            header("Location: ../view/raffle.php?error=validation");
            exit();
        }
        
        // Validate phone number format
        if(!preg_match("/^\+?[0-9]{10,15}$/", $phone)) {
            error_log("Validation failed - Invalid phone format: $phone");
            header("Location: ../view/raffle.php?error=validation&field=phone");
            exit();
        }
        
        // Validate Instagram handle
        if(!preg_match("/^@?[a-zA-Z0-9._]{1,30}$/", $instagram)) {
            error_log("Validation failed - Invalid Instagram handle: $instagram");
            header("Location: ../view/raffle.php?error=validation&field=instagram");
            exit();
        }
        
        // Check for duplicate entry
        if(check_duplicate_entry_ctr($phone)) {
            error_log("Duplicate entry detected for phone: $phone");
            header("Location: ../view/raffle.php?error=duplicate");
            exit();
        }
        
        // Add new entry
        $result = add_raffle_entry_ctr($name, $phone, $instagram);
        if($result) {
            error_log("Successfully added raffle entry for phone: $phone");
            header("Location: ../view/raffle.php?success=1");
            exit();
        } else {
            error_log("Failed to add raffle entry to database");
            header("Location: ../view/raffle.php?error=db");
            exit();
        }
        
    } catch (Exception $e) {
        error_log("Exception in raffle entry: " . $e->getMessage());
        header("Location: ../view/raffle.php?error=system");
        exit();
    }
} else {
    error_log("No POST data received for raffle entry");
    header("Location: ../view/raffle.php?error=nodata");
    exit();
}
?>
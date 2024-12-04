<?php
session_start();
require_once("../settings/security.php");

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header("location: ../login.php");
    exit();
}

include("../controllers/raffle_controller.php");

$entries = get_all_entries_ctr();

// Set headers for CSV download
header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="raffle_entries_'.date('Y-m-d').'.csv"');

// Create output stream
$output = fopen('php://output', 'w');

// Add CSV headers
fputcsv($output, ['Name', 'Phone', 'Instagram', 'Entry Date']);

// Add entries to CSV
foreach($entries as $entry) {
    fputcsv($output, [
        $entry['name'],
        $entry['phone'],
        $entry['instagram'],
        $entry['created_at']
    ]);
}

fclose($output);
?>
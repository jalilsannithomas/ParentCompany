<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();
require_once("../settings/security.php");

header('Content-Type: application/json');

try {
    // Check authentication
    if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    include("../controllers/raffle_controller.php");

    // Get all entries
    $entries = get_all_entries_ctr();
    if($entries === false) {
        throw new Exception('Failed to fetch raffle entries');
    }

    if(empty($entries)) {
        echo json_encode([
            'success' => false,
            'message' => 'No entries found in the raffle'
        ]);
        exit();
    }

    // Randomly select a winner
    $winner = $entries[array_rand($entries)];
    
    if(!$winner) {
        throw new Exception('Failed to select winner');
    }

    // Format winner data
    $winner_data = [
        'name' => htmlspecialchars($winner['name']),
        'phone' => htmlspecialchars($winner['phone']),
        'instagram' => htmlspecialchars($winner['instagram']),
        'entry_id' => (int)$winner['entry_id'],
        'created_at' => $winner['created_at']
    ];

    echo json_encode([
        'success' => true,
        'winner' => $winner_data
    ]);

} catch (Exception $e) {
    error_log("Error in winner selection: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred while selecting the winner: ' . $e->getMessage()
    ]);
}
?>
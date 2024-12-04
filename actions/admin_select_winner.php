<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

session_start();
require_once("../settings/security.php");

ob_start();
header('Content-Type: application/json');

try {
    if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    include_once("../controllers/raffle_controller.php");
    $winner = select_winner_ctr();

    if ($winner && is_array($winner)) {
        $response = ['success' => true, 'winner' => $winner];
    } else {
        $response = ['success' => false, 'message' => 'Failed to select winner or no entries found'];
    }
} catch (Exception $e) {
    error_log("Error in winner selection: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    $response = ['success' => false, 'message' => 'An error occurred'];
}

ob_end_clean();
echo json_encode($response);
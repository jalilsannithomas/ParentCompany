<?php
session_start();
require_once("../settings/security.php");

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include("../controllers/raffle_controller.php");

// Accept both POST form data and JSON input
$entry_id = null;
if (isset($_POST['entry_id'])) {
    $entry_id = $_POST['entry_id'];
} else {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['entry_id'])) {
        $entry_id = $data['entry_id'];
    }
}

if ($entry_id !== null) {
    $result = delete_entry_ctr($entry_id);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Entry deleted successfully' : 'Failed to delete entry'
    ]);
    exit();
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request: No entry ID provided']);
?>
<?php
session_start();
require_once("../settings/security.php");

if(!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include("../controllers/raffle_controller.php");

$data = json_decode(file_get_contents('php://input'), true);

if(isset($data['entry_id'])) {
    $result = delete_entry_ctr($data['entry_id']);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $result,
        'message' => $result ? 'Entry deleted successfully' : 'Failed to delete entry'
    ]);
    exit();
}

header('Content-Type: application/json');
echo json_encode(['success' => false, 'message' => 'Invalid request']);
?>
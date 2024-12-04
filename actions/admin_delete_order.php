<?php
require_once(dirname(__FILE__) . '/../controllers/order_controller.php');
require_once(dirname(__FILE__) . '/../settings/connection.php');

header('Content-Type: application/json');

function check_order_exists($order_id) {
    global $conn;
    $query = "SELECT COUNT(*) as count FROM orders WHERE order_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'] > 0;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['order_id'])) {
        echo json_encode(['success' => false, 'error' => 'Order ID is required']);
        exit;
    }

    $order_id = $_POST['order_id'];
    
    try {
        if (!check_order_exists($order_id)) {
            echo json_encode(['success' => false, 'error' => 'Order not found']);
            exit;
        }

        $result = delete_order_ctr($order_id);
        echo json_encode(['success' => $result]);
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage()
        ]);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
}
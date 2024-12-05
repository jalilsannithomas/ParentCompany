<?php
session_start();
require_once(__DIR__ . '/controllers/order_controller.php');
require_once(__DIR__ . '/controllers/cart_controller.php');

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['reference']) || !isset($_SESSION['user_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request'
    ]);
    exit();
}

$reference = $_POST['reference'];
$user_id = $_SESSION['user_id'];

// Verify Paystack transaction
$curl = curl_init();
curl_setopt_array($curl, array(
    CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_HTTPHEADER => [
        "accept: application/json",
        "authorization: Bearer sk_live_9a6014b42cdcda3d809932a101d4078609c76530", // Live secret key
        "cache-control: no-cache"
    ],
));

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo json_encode([
        'success' => false,
        'message' => 'Payment verification failed: ' . $err
    ]);
    exit();
}

$tranx = json_decode($response);

if (!$tranx->status || $tranx->data->status !== 'success') {
    echo json_encode([
        'success' => false,
        'message' => 'Payment was not successful'
    ]);
    exit();
}

// Get cart items
$cart_items = get_cart_items_ctr($user_id);

if (empty($cart_items)) {
    echo json_encode([
        'success' => false,
        'message' => 'Cart is empty'
    ]);
    exit();
}

// Prepare payment details
$payment_details = [
    'payment_method' => 'paystack',
    'reference' => $reference,
    'amount' => $tranx->data->amount / 100, // Convert from kobo to cedis
    'currency' => $tranx->data->currency,
    'status' => $tranx->data->status
];

// Process the order
$result = process_checkout_ctr($user_id, $cart_items, $payment_details);

if ($result['success']) {
    $_SESSION['order_id'] = $result['order_id'];
    echo json_encode([
        'success' => true,
        'order_id' => $result['order_id'],
        'message' => 'Order processed successfully'
    ]);
} else {
    echo json_encode([
        'success' => false,
        'message' => $result['message']
    ]);
}
exit();
?>

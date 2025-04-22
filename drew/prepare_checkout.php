<?php
session_start();

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please log in.';
    echo json_encode($response);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$cart_items = $input['cart_items'] ?? [];

if (empty($cart_items)) {
    $response['message'] = 'Cart is empty.';
    echo json_encode($response);
    exit;
}

// Store cart items in session for checkout
$_SESSION['order'] = $cart_items;
$response['success'] = true;

echo json_encode($response);
?>
<?php
session_start();
header('Content-Type: application/json');

// Check if the request includes the required data
if (!isset($_POST['product_id']) || !isset($_POST['quantity'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required data']);
    exit;
}

$product_id = $_POST['product_id'];
$quantity = (int)$_POST['quantity'];

// Validate quantity
if ($quantity < 1) {
    echo json_encode(['success' => false, 'message' => 'Quantity must be at least 1']);
    exit;
}

// Check if the product exists in the cart
if (!isset($_SESSION['cart'][$product_id])) {
    echo json_encode(['success' => false, 'message' => 'Product not found in cart']);
    exit;
}

// Update the quantity
$_SESSION['cart'][$product_id]['quantity'] = $quantity;

// Return success
echo json_encode([
    'success' => true,
    'message' => 'Cart updated',
    'product_id' => $product_id,
    'quantity' => $quantity
]);
exit;
?>
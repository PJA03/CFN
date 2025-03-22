<?php
session_start();
include '../conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Not logged in']);
    exit();
}

$product_id = $_POST['product_id'] ?? '';
$quantity = $_POST['quantity'] ?? 0;

if ($product_id && $quantity > 0) {
    $_SESSION['cart'][$product_id]['quantity'] = (int)$quantity;
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
}
?>
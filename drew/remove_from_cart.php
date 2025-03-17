<?php
session_start();
header('Content-Type: application/json');

if (isset($_POST['product_id'])) {
    $product_id = $_POST['product_id'];
    
    // Check if the product exists in the cart
    if (isset($_SESSION['cart'][$product_id])) {
        // Remove the product from the cart
        unset($_SESSION['cart'][$product_id]);
        
        echo json_encode(['success' => true, 'message' => 'Product removed from cart']);
        exit;
    } else {
        echo json_encode(['success' => false, 'message' => 'Product not found in cart']);
        exit;
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No product ID provided']);
    exit;
}
?>
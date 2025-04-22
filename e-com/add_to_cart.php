<?php
session_start();
require_once '../conn.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please log in to add items to your cart.';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = filter_input(INPUT_POST, 'productID', FILTER_SANITIZE_NUMBER_INT);
$variant_id = filter_input(INPUT_POST, 'variant_id', FILTER_SANITIZE_NUMBER_INT) ?? 0;
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT) ?? 1;
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

if (!$product_id || $quantity <= 0 || !$price) {
    $response['message'] = 'Invalid product, quantity, or price.';
    echo json_encode($response);
    exit();
}

// Validate variant_id
if ($variant_id == 0) {
    // Fetch the default variant for the product
    $default_query = "SELECT variant_id, price FROM tb_productvariants WHERE productID = ? AND is_default = 1 LIMIT 1";
    $default_stmt = $conn->prepare($default_query);
    $default_stmt->bind_param("i", $product_id);
    $default_stmt->execute();
    $default_result = $default_stmt->get_result();
    if ($default_result->num_rows > 0) {
        $default_row = $default_result->fetch_assoc();
        $variant_id = $default_row['variant_id'];
        $price = $default_row['price']; // Update price to match default variant
    } else {
        $response['message'] = 'No default variant found for this product.';
        $default_stmt->close();
        echo json_encode($response);
        exit();
    }
    $default_stmt->close();
}

// Verify the variant exists and get the correct price
$variant_query = "SELECT price, stock FROM tb_productvariants WHERE variant_id = ? AND productID = ?";
$variant_stmt = $conn->prepare($variant_query);
$variant_stmt->bind_param("ii", $variant_id, $product_id);
$variant_stmt->execute();
$variant_result = $variant_stmt->get_result();

if ($variant_result->num_rows == 0) {
    $response['message'] = 'Invalid variant selected.';
    $variant_stmt->close();
    echo json_encode($response);
    exit();
}

$variant = $variant_result->fetch_assoc();
$price = $variant['price']; // Use the price from the variant
$stock = $variant['stock'];

if ($stock < $quantity) {
    $response['message'] = 'Not enough stock available.';
    $variant_stmt->close();
    echo json_encode($response);
    exit();
}

$variant_stmt->close();

// Check if the product with the same variant is already in the cart
$check_query = "SELECT cart_id, quantity FROM tb_cart WHERE user_id = ? AND productID = ? AND variant_id = ?";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("iii", $user_id, $product_id, $variant_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Update quantity if item exists
    $row = $check_result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    $update_query = "UPDATE tb_cart SET quantity = ?, price_total = ? WHERE cart_id = ?";
    $update_stmt = $conn->prepare($update_query);
    $price_total = $price * $new_quantity;
    $update_stmt->bind_param("idi", $new_quantity, $price_total, $row['cart_id']);
    if ($update_stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Failed to update cart: ' . $update_stmt->error;
    }
    $update_stmt->close();
} else {
    // Insert new item into cart
    $insert_query = "INSERT INTO tb_cart (user_id, productID, variant_id, quantity, price, price_total) VALUES (?, ?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    $price_total = $price * $quantity;
    $insert_stmt->bind_param("iiiidd", $user_id, $product_id, $variant_id, $quantity, $price, $price_total);
    if ($insert_stmt->execute()) {
        $response['success'] = true;
    } else {
        $response['message'] = 'Failed to add to cart: ' . $insert_stmt->error;
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();
echo json_encode($response);
?>
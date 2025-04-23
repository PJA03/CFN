<?php
session_start();
require_once '../conn.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

// Debug logging
$debug_log = fopen("../debug_log.txt", "a");
function debug_log($message) {
    global $debug_log;
    fwrite($debug_log, date('Y-m-d H:i:s') . " - " . $message . "\n");
}

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please log in to add items to your cart.';
    echo json_encode($response);
    debug_log("User not logged in.");
    fclose($debug_log);
    exit();
}

$user_id = $_SESSION['user_id'];
$product_id = filter_input(INPUT_POST, 'productID', FILTER_SANITIZE_NUMBER_INT);
$variant_id = filter_input(INPUT_POST, 'variant_id', FILTER_SANITIZE_NUMBER_INT) ?: null;
$quantity = filter_input(INPUT_POST, 'quantity', FILTER_SANITIZE_NUMBER_INT) ?: 1;
$price = filter_input(INPUT_POST, 'price', FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);

debug_log("Received data - productID: $product_id, variant_id: " . ($variant_id ?? 'NULL') . ", quantity: $quantity, price: $price");

if (!$product_id || $quantity <= 0 || !$price) {
    $response['message'] = 'Invalid product, quantity, or price.';
    echo json_encode($response);
    debug_log("Invalid input - productID: $product_id, quantity: $quantity, price: $price");
    fclose($debug_log);
    exit();
}

// Fetch product and variant details
$product_query = "SELECT p.category, v.price, v.stock 
                 FROM tb_products p 
                 LEFT JOIN tb_productvariants v ON p.productID = v.productID 
                 WHERE p.productID = ? " . ($variant_id ? "AND v.variant_id = ?" : "AND v.is_default = 1");
$stmt = $conn->prepare($product_query);

if ($variant_id) {
    $stmt->bind_param("ii", $product_id, $variant_id);
} else {
    $stmt->bind_param("i", $product_id);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    $response['message'] = 'Product or variant not found.';
    echo json_encode($response);
    debug_log("Product or variant not found - productID: $product_id, variant_id: " . ($variant_id ?? 'NULL'));
    $stmt->close();
    fclose($debug_log);
    exit();
}

$row = $result->fetch_assoc();
$category = strtolower($row['category']);
$stock = $row['stock'] ?? 0;
$variant_price = $row['price'] ?? 0;
$stmt->close();

// Validate variant for perfume products
if ($category === 'perfume' && !$variant_id) {
    $response['message'] = 'Please select a variant for this perfume product.';
    echo json_encode($response);
    debug_log("Perfume product requires a variant - productID: $product_id");
    fclose($debug_log);
    exit();
}

// Use the price from the form (already validated in productpage.php)
if ($variant_price != $price) {
    debug_log("Price mismatch - DB price: $variant_price, Form price: $price. Using form price.");
}

// Validate stock
if ($stock < $quantity) {
    $response['message'] = 'Not enough stock available.';
    echo json_encode($response);
    debug_log("Not enough stock - productID: $product_id, requested: $quantity, available: $stock");
    fclose($debug_log);
    exit();
}

// Check if the item is already in the cart
$check_query = "SELECT quantity FROM tb_cart WHERE user_id = ? AND productID = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL))";
$check_stmt = $conn->prepare($check_query);
$check_stmt->bind_param("iiii", $user_id, $product_id, $variant_id, $variant_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows > 0) {
    // Update quantity if item exists
    $row = $check_result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    if ($stock < $new_quantity) {
        $response['message'] = 'Not enough stock available to update cart.';
        echo json_encode($response);
        debug_log("Not enough stock to update - productID: $product_id, requested: $new_quantity, available: $stock");
        $check_stmt->close();
        fclose($debug_log);
        exit();
    }
    $update_query = "UPDATE tb_cart SET quantity = ?, price = ? WHERE user_id = ? AND productID = ? AND (variant_id = ? OR (variant_id IS NULL AND ? IS NULL))";
    $update_stmt = $conn->prepare($update_query);
    $update_stmt->bind_param("idiiii", $new_quantity, $price, $user_id, $product_id, $variant_id, $variant_id);
    if ($update_stmt->execute()) {
        $response['success'] = true;
        debug_log("Updated cart - productID: $product_id, variant_id: " . ($variant_id ?? 'NULL') . ", new_quantity: $new_quantity");
    } else {
        $response['message'] = 'Failed to update cart: ' . $update_stmt->error;
        debug_log("Failed to update cart - productID: $product_id, error: " . $update_stmt->error);
    }
    $update_stmt->close();
} else {
    // Insert new item into cart
    $insert_query = "INSERT INTO tb_cart (user_id, productID, variant_id, quantity, price) VALUES (?, ?, ?, ?, ?)";
    $insert_stmt = $conn->prepare($insert_query);
    if ($variant_id) {
        $insert_stmt->bind_param("iiiii", $user_id, $product_id, $variant_id, $quantity, $price);
    } else {
        $insert_stmt = $conn->prepare("INSERT INTO tb_cart (user_id, productID, quantity, price) VALUES (?, ?, ?, ?)");
        $insert_stmt->bind_param("iiid", $user_id, $product_id, $quantity, $price);
    }
    if ($insert_stmt->execute()) {
        $response['success'] = true;
        debug_log("Added to cart - productID: $product_id, variant_id: " . ($variant_id ?? 'NULL') . ", quantity: $quantity");
    } else {
        $response['message'] = 'Failed to add to cart: ' . $insert_stmt->error;
        debug_log("Failed to add to cart - productID: $product_id, error: " . $insert_stmt->error);
    }
    $insert_stmt->close();
}

$check_stmt->close();
$conn->close();
echo json_encode($response);
debug_log("Response sent: " . json_encode($response));
fclose($debug_log);
exit();
?>
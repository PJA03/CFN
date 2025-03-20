<?php
session_start();
include 'conn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    //TODO: Make it an alert tapos stay on the product details page
    header('Location: ../Registration_Page/registration.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Check if the file was uploaded
if (!isset($_FILES['payment_proof']) || $_FILES['payment_proof']['error'] != 0) {
    $_SESSION['payment_error'] = "Error uploading file. Please try again.";
    header('Location: paymentmethod.php');
    exit;
}

// Get file info
$file = $_FILES['payment_proof'];
$file_name = $file['name'];
$file_tmp = $file['tmp_name'];
$file_size = $file['size'];
$file_error = $file['error'];

// Get file extension
$file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

// Check file extension
$allowed = array('jpg', 'jpeg', 'png');
if (!in_array($file_ext, $allowed)) {
    $_SESSION['payment_error'] = "Invalid file type. Please upload a JPG, JPEG, or PNG file.";
    header('Location: paymentmethod.php');
    exit;
}

// Check file size (5MB max)
if ($file_size > 5242880) {
    $_SESSION['payment_error'] = "File is too large. Maximum size is 5MB.";
    header('Location: paymentmethod.php');
    exit;
}

// Generate unique filename
$new_file_name = uniqid('payment_') . '.' . $file_ext;
$upload_path = 'uploads/' . $new_file_name;

// Create uploads directory if it doesn't exist
if (!is_dir('uploads')) {
    mkdir('uploads', 0777, true);
}

// Move uploaded file to destination
if (!move_uploaded_file($file_tmp, $upload_path)) {
    $_SESSION['payment_error'] = "Failed to upload file. Please try again.";
    header('Location: paymentmethod.php');
    exit;
}

// Get user ID - assuming user is logged in
$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1; // Default to 1 if not set

// Calculate total amount
$total_amount = 0;
foreach ($_SESSION['order'] as $item) {
    $total_amount += $item['price'] * $item['quantity'];
}

// Add VAT and delivery fee
$vat = $total_amount * 0.12;
$delivery_fee = 40.00;
$total_amount = $total_amount + $vat + $delivery_fee;

// Current date and time
$order_date = date('Y-m-d H:i:s');

// Insert order into database
$stmt = $conn->prepare("INSERT INTO tb_orders (user_id, order_date, total_amount, status, payment_proof) VALUES (?, ?, ?, ?, ?)");
$status = "Pending"; // Initial status
$stmt->bind_param("isdss", $user_id, $order_date, $total_amount, $status, $upload_path);

if (!$stmt->execute()) {
    $_SESSION['payment_error'] = "Failed to process order. Please try again. Error: " . $stmt->error;
    header('Location: paymentmethod.php');
    exit;
}

// Get the order ID
$order_id = $stmt->insert_id;

// Insert order items
foreach ($_SESSION['order'] as $product_id => $item) {
    $price = $item['price'];
    $quantity = $item['quantity'];
    $subtotal = $price * $quantity;
    
    $stmt = $conn->prepare("INSERT INTO tb_order_items (order_id, product_id, quantity, price, subtotal) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iiidi", $order_id, $product_id, $quantity, $price, $subtotal);
    
    if (!$stmt->execute()) {
        // Log error but continue processing
        error_log("Failed to insert order item: " . $stmt->error);
    }
}

// Clear the cart and order
$_SESSION['cart'] = array();
$_SESSION['order'] = array();

// Redirect to success page
$_SESSION['order_success'] = true;
$_SESSION['order_id'] = $order_id;
header('Location: checkout_success.php');
exit;
?>
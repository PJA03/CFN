<?php
session_start();
include 'conn.php'; // Ensure this file connects to your database

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
$new_filename = uniqid('payment') . '.' . $file_ext;
$upload_path = 'uploads/' . $new_filename;

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

$user_query = $conn->prepare("SELECT email, first_name, last_name FROM tb_user WHERE user_id = ?");
$user_query->bind_param("i", $user_id);
$user_query->execute();
$user_result = $user_query->get_result();

if ($user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $email = $user_data['email'];
    $first_name = $user_data['first_name'];
    $last_name = $user_data['last_name'];
} else {
    // Handle the case where user data is not found
    $_SESSION['payment_error'] = "User data not found. Please log in again.";
    header('Location: paymentmethod.php');
    exit;
}

// Current date and time
$order_date = date('Y-m-d H:i:s');

// Insert each product in the order as a separate row in tb_orders
foreach ($_SESSION['order'] as $product_id => $item) {
    $product_name = $item['product_name'];
    $quantity = $item['quantity'];
    $price = $item['price'];
    $price_total = $price * $quantity;

    // Insert order into database
    $stmt = $conn->prepare("INSERT INTO tb_orders (order_date, productID, product_name, user_id, email, first_name, last_name, quantity, status, payment_option, payment_proof, isApproved, price_total) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $status = "Waiting for Payment"; // Initial status
    $payment_option = "QR Code"; // Default payment option
    $isApproved = 0; // Default to not approved

    $stmt->bind_param("sisssssisssid", $order_date, $product_id, $product_name, $user_id, $email, $first_name, $last_name, $quantity, $status, $payment_option, $upload_path, $isApproved, $price_total);

    if (!$stmt->execute()) {
        $_SESSION['payment_error'] = "Failed to process order. Please try again. Error: " . $stmt->error;
        header('Location: paymentmethod.php');
        exit;
    }
}

// Clear the cart and order
$_SESSION['cart'] = array();
$_SESSION['order'] = array();

// Redirect to success page
$_SESSION['order_success'] = true;
header('Location: checkout_success.php');
exit;
?>
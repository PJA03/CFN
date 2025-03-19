<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    die("You must be logged in to add items to the cart.");
}

// Database connection
$servername = "localhost";
$username = "root";  // Adjust if needed
$password = "";      // Adjust if needed
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the user ID from the session
$user_id = $_SESSION['user_id'];

// Get the product details from the form
$productID = isset($_POST['productID']) ? intval($_POST['productID']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0.0;

// Validate the input
if ($productID <= 0 || $quantity <= 0 || $price <= 0) {
    die("Invalid product or quantity.");
}

// Insert the cart data into the database
$stmt = $conn->prepare("INSERT INTO tb_cart (user_id, productID, quantity, price) 
                        VALUES (?, ?, ?, ?)
                        ON DUPLICATE KEY UPDATE quantity = quantity + VALUES(quantity)");
$stmt->bind_param("iiid", $user_id, $productID, $quantity, $price);

if ($stmt->execute()) {
    echo "Product added to cart successfully!";
    header("Location: productpage.php?id=$productID"); // Redirect back to the product page
    exit();
} else {
    echo "Failed to add product to cart.";
}

$stmt->close();
$conn->close();
?>
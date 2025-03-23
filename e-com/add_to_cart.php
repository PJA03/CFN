<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    // Instead of redirecting, return a JSON error for AJAX
    header('Content-Type: application/json');
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'User not logged in']);
    exit;
}
$user_id = $_SESSION['user_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Check if the request is a POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Content-Type: application/json');
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

// Get form data
$productID = isset($_POST['productID']) ? intval($_POST['productID']) : 0;
$quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
$price = isset($_POST['price']) ? floatval($_POST['price']) : 0;

// Validate inputs
if ($productID <= 0 || $quantity <= 0 || $price <= 0) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product data']);
    exit;
}

// Fetch product details from the database (for validation or additional info)
$sql = "SELECT product_name, product_image FROM tb_products WHERE productID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header('Content-Type: application/json');
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Product not found']);
    exit;
}

$product = $result->fetch_assoc();
$productName = $product['product_name'];

// Add to cart in the database (tb_cart table)
$query = "INSERT INTO tb_cart (user_id, productID, quantity, price) 
          VALUES (?, ?, ?, ?) 
          ON DUPLICATE KEY UPDATE quantity = quantity + ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iiidi", $user_id, $productID, $quantity, $price, $quantity);
if (!$stmt->execute()) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to add to cart: ' . $stmt->error]);
    exit;
}

// Return success response
header('Content-Type: application/json');
echo json_encode(['success' => true, 'message' => "Added $quantity $productName to cart!"]);
exit;
?>
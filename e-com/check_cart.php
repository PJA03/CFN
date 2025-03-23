<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['in_cart' => false, 'message' => 'User not logged in']);
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
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from the request
$productID = isset($_GET['productID']) ? intval($_GET['productID']) : 0;

if ($productID <= 0) {
    echo json_encode(['in_cart' => false, 'message' => 'Invalid product ID']);
    exit;
}

// Check if the product is in the cart
$query = "SELECT quantity FROM tb_cart WHERE user_id = ? AND productID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $user_id, $productID);
$stmt->execute();
$result = $stmt->get_result();

$in_cart = $result->num_rows > 0;
$quantity = $in_cart ? $result->fetch_assoc()['quantity'] : 0;

// Return the result as JSON
header('Content-Type: application/json');
echo json_encode(['in_cart' => $in_cart, 'quantity' => $quantity]);
exit;
?>
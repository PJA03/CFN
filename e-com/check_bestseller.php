<?php
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

// Get product ID from the request
$productID = isset($_GET['productID']) ? intval($_GET['productID']) : 0;

if ($productID <= 0) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
    exit;
}

// Check if the product is already in the best sellers
$query = "SELECT bestseller_id FROM tb_bestsellers WHERE productID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();

$in_best_sellers = $result->num_rows > 0;

// Return the result as JSON
header('Content-Type: application/json');
echo json_encode(['success' => true, 'in_best_sellers' => $in_best_sellers]);
exit;
?>
<?php
// addbestseller.php
ini_set('display_errors', 1);
error_reporting(E_ALL);
header('Content-Type: application/json');

// Clear output buffer to ensure no extra HTML is output
if (ob_get_length()) {
    ob_clean();
}

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method.']);
    exit;
}

// Get the JSON input and decode it
$input = file_get_contents("php://input");
$data = json_decode($input, true);
if (!isset($data['productID'])) {
    echo json_encode(['success' => false, 'error' => 'Product ID missing.']);
    exit;
}

$productID = intval($data['productID']);

// Database connection parameters
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Check if the product is already a best seller
$stmt = $conn->prepare("SELECT COUNT(*) FROM tb_bestsellers WHERE productID = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => "Prepare failed: " . $conn->error]);
    exit;
}
$stmt->bind_param("i", $productID);
$stmt->execute();
$stmt->bind_result($count);
$stmt->fetch();
$stmt->close();

if ($count > 0) {
    echo json_encode(['success' => false, 'error' => 'Product is already a best seller.']);
    $conn->close();
    exit;
}

// Insert the product into best_sellers table
$stmt = $conn->prepare("INSERT INTO tb_bestsellers (productID, display_order) VALUES (?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => "Prepare failed: " . $conn->error]);
    exit;
}
$display_order = 0; // adjust this if you need custom ordering
$stmt->bind_param("ii", $productID, $display_order);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => "Execute failed: " . $stmt->error]);
}

$stmt->close();
$conn->close();
?>

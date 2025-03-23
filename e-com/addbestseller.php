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
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

// Get the JSON data
$data = json_decode(file_get_contents('php://input'), true);
$productID = isset($data['productID']) ? intval($data['productID']) : 0;

if ($productID <= 0) {
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Invalid product ID']);
    exit;
}

// Get the current highest display_order
$orderQuery = "SELECT MAX(display_order) as max_order FROM tb_bestsellers";
$orderResult = $conn->query($orderQuery);
$maxOrder = $orderResult->fetch_assoc()['max_order'] ?? 0;
$newOrder = $maxOrder + 1;

// Insert the new best seller
$query = "INSERT INTO tb_bestsellers (productID, display_order) VALUES (?, ?)";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $productID, $newOrder);

if ($stmt->execute()) {
    $bestseller_id = $conn->insert_id;
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'bestseller_id' => $bestseller_id]);
} else {
    // Check if the error is due to a duplicate entry
    if ($conn->errno === 1062) { // 1062 is the MySQL error code for duplicate entry
        header('Content-Type: application/json');
        http_response_code(409);
        echo json_encode(['success' => false, 'error' => 'This product is already in the Best Sellers list']);
    } else {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => 'Failed to add best seller: ' . $stmt->error]);
    }
}

$stmt->close();
$conn->close();
exit;
?>
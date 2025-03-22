<?php
// Suppress display of errors to browser (log them instead)
ini_set('display_errors', 0); // Change to 1 for debugging, then check logs
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

require_once 'auth_check.php';
header('Content-Type: application/json');
require_once '../conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$productID = $data['productID'] ?? '';

if (!$productID) {
    echo json_encode(['success' => false, 'error' => 'Product ID required']);
    exit;
}

// Get the next display_order value
$orderQuery = "SELECT IFNULL(MAX(display_order), 0) + 1 AS next_order FROM tb_bestsellers";
$orderResult = $conn->query($orderQuery);
if ($orderResult === false) {
    echo json_encode(['success' => false, 'error' => 'Failed to determine display order: ' . $conn->error]);
    exit;
}
$nextOrder = $orderResult->fetch_assoc()['next_order'];

// Insert the new best seller
$stmt = $conn->prepare("INSERT INTO tb_bestsellers (productID, display_order, created_at, updated_at) VALUES (?, ?, NOW(), NOW())");
$stmt->bind_param("ii", $productID, $nextOrder);

if ($stmt->execute()) {
    $bestsellerId = $conn->insert_id;
    echo json_encode(['success' => true, 'bestseller_id' => $bestsellerId]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
exit;
<?php
header('Content-Type: application/json');
require_once '../conn.php';

$data = json_decode(file_get_contents('php://input'), true);
$productID = $data['productID'] ?? '';

if (!$productID) {
    echo json_encode(['success' => false, 'error' => 'Product ID required']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO tb_bestsellers (productID) VALUES (?)");
$stmt->bind_param("i", $productID);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
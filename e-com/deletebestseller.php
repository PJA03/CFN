<?php
require_once 'auth_check.php';
?>
<?php
header('Content-Type: application/json');
require_once '../conn.php';

$productID = $_GET['id'] ?? '';

if (!$productID) {
    echo json_encode(['success' => false, 'error' => 'Product ID required']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM tb_bestsellers WHERE productID = ?");
$stmt->bind_param("i", $productID);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
?>
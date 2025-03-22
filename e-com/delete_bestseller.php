<?php
require_once 'auth_check.php';
header('Content-Type: application/json');
require_once '../conn.php';

$bestsellerId = $_GET['id'] ?? '';

if (!$bestsellerId) {
    echo json_encode(['success' => false, 'error' => 'Bestseller ID required']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM tb_bestsellers WHERE bestseller_id = ?");
$stmt->bind_param("i", $bestsellerId);

if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'No best seller found with that ID']);
    }
} else {
    echo json_encode(['success' => false, 'error' => $conn->error]);
}

$stmt->close();
$conn->close();
exit; // Ensure no further output
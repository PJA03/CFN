<?php
require_once 'auth_check.php';
// No whitespace or characters before this line
ob_start(); // Start output buffering
header('Content-Type: application/json'); // Set JSON header immediately
require_once '../conn.php';

// Clear any output that might have occurred during require
ob_clean();

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$voucherID = $_GET['id'] ?? '';

if (empty($voucherID)) {
    echo json_encode(['success' => false, 'error' => 'Voucher ID required']);
    exit;
}

$stmt = $conn->prepare("SELECT * FROM tb_vouchers WHERE voucherID = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $voucherID);
$stmt->execute();
$result = $stmt->get_result();
$voucher = $result->fetch_assoc();

if ($voucher) {
    echo json_encode($voucher);
} else {
    echo json_encode(['success' => false, 'error' => 'Voucher not found']);
}

$stmt->close();
$conn->close();
exit; // Ensure no further output
?>
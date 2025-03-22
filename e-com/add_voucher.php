<?php

// Suppress HTML error output
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/CFN/admin/php_errors.log');

header('Content-Type: application/json');
require_once '../conn.php';

// Check connection
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No valid JSON data received']);
    exit;
}

$discount = $data['discount'] ?? '';
$details = $data['details'] ?? '';
$valid_until = $data['valid_until'] ?? '';
$code = $data['code'] ?? '';

if (empty($discount) || empty($details) || empty($valid_until) || empty($code)) {
    echo json_encode(['success' => false, 'error' => 'All fields are required']);
    exit;
}

if (!is_numeric($discount)) {
    echo json_encode(['success' => false, 'error' => 'Discount must be a number']);
    exit;
}

$stmt = $conn->prepare("INSERT INTO tb_vouchers (discount, details, valid_until, code) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("dsss", $discount, $details, $valid_until, $code);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
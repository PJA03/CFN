<?php
require_once 'auth_check.php';
?>
<?php

ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'C:/xampp/htdocs/CFN/admin/php_errors.log');

header('Content-Type: application/json');
require_once '../conn.php';

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'error' => 'Database connection failed: ' . $conn->connect_error]);
    exit;
}

$voucherID = $_GET['id'] ?? '';

if (empty($voucherID)) {
    echo json_encode(['success' => false, 'error' => 'Voucher ID required']);
    exit;
}

$stmt = $conn->prepare("DELETE FROM tb_vouchers WHERE voucherID = ?");
if (!$stmt) {
    echo json_encode(['success' => false, 'error' => 'Prepare failed: ' . $conn->error]);
    exit;
}

$stmt->bind_param("i", $voucherID);

if ($stmt->execute()) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => 'Execute failed: ' . $stmt->error]);
}

$stmt->close();
$conn->close();
?>
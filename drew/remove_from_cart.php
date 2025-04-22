<?php
session_start();
require_once '../conn.php';

header('Content-Type: application/json');
$response = ['success' => false, 'message' => ''];

if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please log in.';
    echo json_encode($response);
    exit;
}

$input = json_decode(file_get_contents('php://input'), true);
$cart_id = filter_var($input['cart_id'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

if ($cart_id <= 0) {
    $response['message'] = 'Invalid input.';
    echo json_encode($response);
    exit;
}

$query = "DELETE FROM tb_cart WHERE cart_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
if ($stmt->execute()) {
    $response['success'] = true;
} else {
    $response['message'] = 'Failed to remove item: ' . $stmt->error;
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>
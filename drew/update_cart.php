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
$quantity = filter_var($input['quantity'] ?? 0, FILTER_SANITIZE_NUMBER_INT);

if ($cart_id <= 0 || $quantity <= 0) {
    $response['message'] = 'Invalid input.';
    echo json_encode($response);
    exit;
}

// Verify stock
$query = "SELECT v.stock 
          FROM tb_cart c 
          JOIN tb_productvariants v ON c.variant_id = v.variant_id 
          WHERE c.cart_id = ? AND c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $cart_id, $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
    $response['message'] = 'Cart item not found.';
    echo json_encode($response);
    exit;
}
$stock = $result->fetch_assoc()['stock'];
$stmt->close();

if ($quantity > $stock) {
    $response['message'] = 'Requested quantity exceeds available stock.';
    echo json_encode($response);
    exit;
}

// Update quantity
$query = "UPDATE tb_cart SET quantity = ? WHERE cart_id = ? AND user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("iii", $quantity, $cart_id, $_SESSION['user_id']);
if ($stmt->execute()) {
    $response['success'] = true;
    $response['quantity'] = $quantity;
    $response['item_total'] = $quantity * (float)$stmt->get_result()->fetch_assoc()['price'];
} else {
    $response['message'] = 'Failed to update cart: ' . $stmt->error;
}

$stmt->close();
$conn->close();
echo json_encode($response);
?>
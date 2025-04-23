<?php
session_start();
require_once '../conn.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['cart' => []]);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart items from tb_cart
$query = "SELECT productID, variant_id, quantity 
          FROM tb_cart 
          WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart = [];
while ($row = $result->fetch_assoc()) {
    $cart[] = [
        'productID' => $row['productID'],
        'variant_id' => $row['variant_id'],
        'quantity' => $row['quantity']
    ];
}

$stmt->close();
$conn->close();

echo json_encode(['cart' => $cart]);
exit();
?>
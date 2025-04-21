<?php
require_once '../conn.php';

header('Content-Type: application/json');

$orderID = isset($_GET['orderID']) ? (int)$_GET['orderID'] : 0;

if ($orderID <= 0) {
    echo json_encode(['error' => 'Invalid Order ID']);
    exit;
}

$stmt = $conn->prepare("
    SELECT 
        order_item_id,
        productID,
        product_name,
        quantity,
        unit_price,
        item_total
    FROM tb_order_items
    WHERE orderID = ?
");
$stmt->bind_param("i", $orderID);
$stmt->execute();
$result = $stmt->get_result();
$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();
$conn->close();

echo json_encode(['items' => $items]);
?>
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
        oi.order_item_id,
        oi.productID,
        oi.variant_id,
        oi.product_name,
        oi.quantity,
        oi.unit_price,
        oi.item_total,
        p.category,
        (SELECT v.variant_name FROM tb_productvariants v WHERE v.variant_id = oi.variant_id LIMIT 1) as variant_name
    FROM tb_order_items oi
    JOIN tb_products p ON oi.productID = p.productID
    WHERE oi.orderID = ?
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
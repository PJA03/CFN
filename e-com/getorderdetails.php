<?php
require_once '../conn.php';

header('Content-Type: application/json');

$orderID = isset($_GET['orderID']) ? (int)$_GET['orderID'] : 0;

if ($orderID <= 0) {
    echo json_encode(['error' => 'Invalid Order ID']);
    exit;
}

// Fetch order details
$stmt = $conn->prepare("
    SELECT 
        orderID,
        order_date,
        user_id,
        email,
        first_name,
        last_name,
        status,
        payment_option,
        payment_proof,
        isApproved,
        price_total AS total_amount,
        trackingLink
    FROM tb_orders
    WHERE orderID = ?
");
$stmt->bind_param("i", $orderID);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo json_encode(['error' => 'Order not found']);
    exit;
}

// Fetch items
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
$items_result = $stmt->get_result();
$items = [];
while ($row = $items_result->fetch_assoc()) {
    $items[] = $row;
}
$stmt->close();

$conn->close();

echo json_encode([
    'order_date' => $order['order_date'],
    'user_id' => $order['user_id'],
    'email' => $order['email'],
    'first_name' => $order['first_name'],
    'last_name' => $order['last_name'],
    'status' => $order['status'],
    'payment_option' => $order['payment_option'],
    'payment_proof' => $order['payment_proof'],
    'isApproved' => $order['isApproved'],
    'total_amount' => $order['total_amount'],
    'trackingLink' => $order['trackingLink'],
    'items' => $items
]);
?>
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
        o.orderID,
        o.order_date,
        o.user_id,
        u.email,
        o.status,
        o.payment_option,
        o.payment_proof,
        o.isApproved,
        o.price_total,
        o.trackingLink
    FROM tb_orders o
    JOIN tb_user u ON o.user_id = u.user_id
    WHERE o.orderID = ?
");
$stmt->bind_param("i", $orderID);
$stmt->execute();
$order = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$order) {
    echo json_encode(['error' => 'Order not found']);
    $conn->close();
    exit;
}

// Fetch items
$stmt = $conn->prepare("
    SELECT 
        oi.order_item_id,
        oi.productID,
        oi.variant_id,
        oi.quantity,
        oi.unit_price,
        (oi.quantity * oi.unit_price) AS item_total,
        p.product_name,
        p.category,
        pv.variant_name
    FROM tb_order_items oi
    JOIN tb_products p ON oi.productID = p.productID
    LEFT JOIN tb_productvariants pv ON oi.variant_id = pv.variant_id
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
    'status' => $order['status'],
    'payment_option' => $order['payment_option'],
    'payment_proof' => $order['payment_proof'],
    'isApproved' => $order['isApproved'],
    'total_amount' => $order['price_total'], // Map to total_amount for frontend
    'trackingLink' => $order['trackingLink'],
    'items' => $items
]);
?>
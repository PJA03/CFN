<?php
require_once '../conn.php';

header('Content-Type: application/json');
$response = ['items' => []];

$order_id = filter_input(INPUT_GET, 'orderID', FILTER_SANITIZE_NUMBER_INT);

if (!$order_id) {
    echo json_encode(['error' => 'Invalid order ID']);
    exit();
}

// Fetch order items
$query = "SELECT oi.order_item_id, oi.orderID, oi.productID, oi.product_name, oi.variant_id, oi.quantity, oi.unit_price,
                 (oi.quantity * oi.unit_price) as item_total,
                 p.category,
                 (SELECT v.variant_name FROM tb_productvariants v WHERE v.variant_id = oi.variant_id LIMIT 1) as variant_name
          FROM tb_order_items oi
          LEFT JOIN tb_products p ON oi.productID = p.productID
          WHERE oi.orderID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $order_id);
$stmt->execute();
$result = $stmt->get_result();

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = [
        'productID' => $row['productID'],
        'product_name' => $row['product_name'],
        'variant_id' => $row['variant_id'],
        'quantity' => $row['quantity'],
        'unit_price' => $row['unit_price'],
        'item_total' => $row['item_total'],
        'category' => $row['category'],
        'variant_name' => $row['variant_name']
    ];
}

// Log the raw data for debugging
file_put_contents('../debug_log.txt', "Order ID: $order_id\n" . print_r($items, true) . "\n\n", FILE_APPEND);

$response['items'] = $items;
echo json_encode($response);
$stmt->close();
$conn->close();
exit();
?>
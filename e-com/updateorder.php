<?php
header("Content-Type: application/json");
include 'conn.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['orderID'])) {
    $orderID = $data['orderID'];
    $status = $data['status'];
    // Cast isApproved to integer: true becomes 1, false becomes 0
    $isApproved = $data['isApproved'] ? 1 : 0;
    $trackingLink = $data['trackingLink'];

    // Update the table name if necessary (using tb_orders as in your code)
    $sql = "UPDATE tb_orders SET status = ?, isApproved = ?, trackingLink = ? WHERE orderID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["success" => false, "message" => "Prepare failed: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("sisi", $status, $isApproved, $trackingLink, $orderID);
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Order updated successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to update order."]);
    }
}
?>

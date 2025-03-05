<?php
header("Content-Type: application/json");

include 'conn.php';

if (isset($_GET['orderID'])) {
    $orderID = $_GET['orderID'];
    $sql = "SELECT * FROM tb_orders WHERE orderID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        echo json_encode(["error" => "Prepare failed: " . $conn->error]);
        exit;
    }
    $stmt->bind_param("i", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();
    $order = $result->fetch_assoc();
    
    if ($order) {
        echo json_encode($order);
    } else {
        echo json_encode(["error" => "Order not found"]);
    }
} else {
    echo json_encode(["error" => "No orderID specified"]);
}
?>

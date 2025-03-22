<?php
require_once 'auth_check.php';
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit();
}

$orderID = isset($_GET['orderID']) ? intval($_GET['orderID']) : 0;
$sql = "SELECT * FROM tb_orders WHERE orderID = $orderID";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    echo json_encode($row);
} else {
    echo json_encode(['error' => 'Order not found']);
}

$conn->close();
?>
<?php
require_once 'auth_check.php';

// Disable error display to prevent HTML output
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$conn->begin_transaction();

try {
    // Check if the request method is POST
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Invalid request method.");
    }

    // Get the JSON data from the request body
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['orderID'])) {
        throw new Exception("Invalid or missing orderID.");
    }

    $orderID = intval($data['orderID']);

    // Verify that the order exists and is in "Cancelled" status
    $stmt = $conn->prepare("SELECT status FROM tb_orders WHERE orderID = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $orderID);
    if (!$stmt->execute()) {
        throw new Exception("Execute failed: " . $stmt->error);
    }
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        throw new Exception("Order not found.");
    }
    $order = $result->fetch_assoc();
    if ($order['status'] !== "Cancelled") {
        throw new Exception("Only cancelled orders can be deleted.");
    }
    $stmt->close();

    // Delete the order from tb_orders
    $stmt = $conn->prepare("DELETE FROM tb_orders WHERE orderID = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("i", $orderID);
    if (!$stmt->execute()) {
        throw new Exception("Failed to delete order: " . $stmt->error);
    }
    $stmt->close();

    // Commit the transaction
    $conn->commit();

    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['success' => true, 'message' => 'Order deleted successfully']);
} catch (Exception $e) {
    // Rollback the transaction on error
    $conn->rollback();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
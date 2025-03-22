<?php
require_once 'auth_check.php';
header('Content-Type: application/json');
require_once '../conn.php'; // Use your existing connection file

// Suppress HTML error output (for production, log errors instead)
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);
if (!$data || !isset($data['orderID'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid request data']);
    exit;
}

$orderID = intval($data['orderID']);
$status = $data['status'] ?? 'Waiting for Payment';
$isApproved = isset($data['isApproved']) ? ($data['isApproved'] ? 1 : 0) : 0;
$trackingLink = $data['trackingLink'] ?? null;

if ($orderID <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit;
}

try {
    // Prepare the update query with delivered_date logic
    $query = "UPDATE tb_orders 
              SET status = ?, 
                  isApproved = ?, 
                  trackingLink = ?,
                  delivered_date = CASE WHEN ? = 'Delivered' THEN NOW() ELSE delivered_date END
              WHERE orderID = ?";
    $stmt = $conn->prepare($query);
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }

    // Bind parameters: status, isApproved, trackingLink, status (for CASE), orderID
    $stmt->bind_param("sissi", $status, $isApproved, $trackingLink, $status, $orderID);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        // Optionally fetch user email for notification
        $emailQuery = "SELECT email FROM tb_orders WHERE orderID = ?";
        $emailStmt = $conn->prepare($emailQuery);
        $emailStmt->bind_param("i", $orderID);
        $emailStmt->execute();
        $result = $emailStmt->get_result();
        $email = $result->fetch_assoc()['email'] ?? '';

        // If status is "Delivered" and email exists, send notification (example)
        if ($status === "Delivered" && $email) {
            $subject = "Your Order Has Been Delivered!";
            $message = "Dear Customer,\n\nYour order (ID: $orderID) has been successfully delivered on " . date('Y-m-d H:i:s') . ".\nThank you for shopping with us!\n\nBest regards,\nNaturale Team";
            $headers = "From: no-reply@naturale.com";
            // mail($email, $subject, $message, $headers); // Uncomment and configure SMTP for production
        }

        echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No changes made or order not found']);
    }

    $stmt->close();
    if (isset($emailStmt)) $emailStmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}

$conn->close();
?>
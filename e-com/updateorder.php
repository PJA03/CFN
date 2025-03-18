<?php
header('Content-Type: application/json');

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Connection failed']);
    exit();
}

$data = json_decode(file_get_contents('php://input'), true);
$orderID = isset($data['orderID']) ? intval($data['orderID']) : 0;
$status = isset($data['status']) ? $conn->real_escape_string($data['status']) : '';
$isApproved = isset($data['isApproved']) ? ($data['isApproved'] ? 1 : 0) : 0;
$trackingLink = isset($data['trackingLink']) ? $conn->real_escape_string($data['trackingLink']) : '';

if ($orderID <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid order ID']);
    exit();
}

// Update approval_date if isApproved changes to true
$approvalDateUpdate = $isApproved ? ", approval_date = NOW()" : "";

// Update delivered_date if status changes to "Delivered"
$deliveredDateUpdate = ($status === "Delivered") ? ", delivered_date = NOW()" : "";

// Prepare the update query
$sql = "UPDATE tb_orders 
        SET status = '$status', 
            isApproved = $isApproved, 
            trackingLink = '$trackingLink'
            $approvalDateUpdate
            $deliveredDateUpdate
        WHERE orderID = $orderID";

if ($conn->query($sql) === TRUE) {
    // Optionally fetch user email for notification
    $sql = "SELECT email FROM tb_orders WHERE orderID = $orderID";
    $result = $conn->query($sql);
    $email = $result->fetch_assoc()['email'] ?? '';

    // If status is "Delivered" and email exists, send notification (example)
    if ($status === "Delivered" && $email) {
        // Example using PHP mail (configure SMTP for production)
        $subject = "Your Order Has Been Delivered!";
        $message = "Dear Customer,\n\nYour order (ID: $orderID) has been successfully delivered.\nThank you for shopping with us!\n\nBest regards,\nNaturale Team";
        $headers = "From: no-reply@naturale.com";
        // mail($email, $subject, $message, $headers); // Uncomment to enable (requires mail server setup)
    }

    echo json_encode(['success' => true, 'message' => 'Order updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update order: ' . $conn->error]);
}

$conn->close();
?>
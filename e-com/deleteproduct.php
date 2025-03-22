<?php
// deleteproduct.php
include 'conn.php';
require_once 'auth_check.php';

if ($_SERVER['REQUEST_METHOD'] === 'DELETE') {
    // Get the product id from the query string
    $productId = isset($_GET['id']) ? intval($_GET['id']) : 0;
    
    if ($productId > 0) {
        $stmt = $conn->prepare("DELETE FROM tb_products WHERE productID = ?");
        if (!$stmt) {
            echo json_encode(["success" => false, "error" => $conn->error]);
            exit;
        }
        $stmt->bind_param("i", $productId);
        if ($stmt->execute()) {
            echo json_encode(["success" => true]);
        } else {
            echo json_encode(["success" => false, "error" => $stmt->error]);
        }
        $stmt->close();
    } else {
        echo json_encode(["success" => false, "error" => "Invalid product ID."]);
    }
}

$conn->close();
?>

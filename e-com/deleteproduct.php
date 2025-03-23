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
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

try {
    // Check request method
    if ($_SERVER['REQUEST_METHOD'] !== 'DELETE') {
        throw new Exception('Invalid request method. DELETE method required.');
    }

    // Get the product ID from the query parameter
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        throw new Exception('Product ID is required.');
    }

    $product_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($product_id === false || $product_id <= 0) {
        throw new Exception('Invalid product ID: Must be a positive integer.');
    }

    // Begin transaction
    $conn->begin_transaction();

    // Check if the product exists
    $checkStmt = $conn->prepare("SELECT productID FROM tb_products WHERE productID = ?");
    if (!$checkStmt) {
        throw new Exception('Failed to prepare check statement: ' . $conn->error);
    }
    $checkStmt->bind_param("i", $product_id);
    $checkStmt->execute();
    $result = $checkStmt->get_result();
    if ($result->num_rows === 0) {
        $checkStmt->close();
        throw new Exception('Product with ID ' . $product_id . ' does not exist.');
    }
    $checkStmt->close();

    // Delete the product (variants will be deleted automatically due to ON DELETE CASCADE)
    $productStmt = $conn->prepare("DELETE FROM tb_products WHERE productID = ?");
    if (!$productStmt) {
        throw new Exception('Failed to prepare product delete statement: ' . $conn->error);
    }
    $productStmt->bind_param("i", $product_id);
    if (!$productStmt->execute()) {
        $productStmt->close();
        throw new Exception('Failed to delete product: ' . $productStmt->error);
    }
    $productStmt->close();

    // Commit transaction
    $conn->commit();

    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
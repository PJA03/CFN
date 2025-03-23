<?php
// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    echo json_encode(['cart' => []]);
    exit;
}
$user_id = $_SESSION['user_id'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch cart items from tb_cart
$query = "SELECT c.productID, c.quantity, c.price, p.product_name, p.product_image 
          FROM tb_cart c
          JOIN tb_products p ON c.productID = p.productID
          WHERE c.user_id = ?
          GROUP BY c.productID, c.price, p.product_name, p.product_image";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart = [];
while ($row = $result->fetch_assoc()) {
    $cart[] = [
        'id' => $row['productID'],
        'quantity' => $row['quantity'],
        'price' => $row['price'],
        'name' => $row['product_name'],
        'image' => $row['product_image']
    ];
}

// Return the cart as JSON
header('Content-Type: application/json');
echo json_encode(['cart' => $cart]);
exit;
?>
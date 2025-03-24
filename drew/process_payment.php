<?php
session_start();
require_once '../conn.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to complete your payment.'); window.location.href='../Registration_Page/registration.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_proof'])) {
    $file = $_FILES['payment_proof'];
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size && $file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/receipts/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true) or die("Failed to create directory");
        }
        $file_name = uniqid() . '-' . basename($file['name']);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Fetch user details
            $query = "SELECT email, first_name, last_name FROM tb_user WHERE user_id = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $user_result = $stmt->get_result();
            $user = $user_result->fetch_assoc();

            // Fetch cart items from session
            $cart_items = $_SESSION['order'] ?? [];
            $price_total = $_SESSION['total_price'] ?? 0;

            if (!empty($cart_items)) {
                $payment_option = $_SESSION['payment_option'] ?? 'unknown';

                // Insert order into tb_orders
                $query = "INSERT INTO tb_orders (order_date, productID, product_name, user_id, email, first_name, last_name, quantity, status, payment_option, payment_proof, isApproved, price_total, trackingLink) 
                          VALUES (NOW(), ?, ?, ?, ?, ?, ?, ?, 'Waiting for Payment', ?, ?, 0, ?, NULL)";
                $stmt = $conn->prepare($query);

                foreach ($cart_items as $item) {
                    $stmt->bind_param("isissisisd", 
                        $item['productID'], 
                        $item['product_name'], 
                        $user_id, 
                        $user['email'], 
                        $user['first_name'], 
                        $user['last_name'], 
                        $item['quantity'], 
                        $payment_option, 
                        $file_name, 
                        $price_total
                    );
                    $stmt->execute();
                }

                // Clear cart
                $query = "DELETE FROM tb_cart WHERE user_id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $user_id);
                $stmt->execute();

                // Clear session data
                unset($_SESSION['order']);
                unset($_SESSION['total_price']);
                unset($_SESSION['payment_option']);

                // Redirect to success page instead of showing modal
                header("Location: success_order.php");
                exit();
            } else {
                echo "<script>alert('Your order is empty.'); window.location.href='cart.php';</script>";
                exit();
            }
        } else {
            echo "<script>alert('Failed to upload receipt. Please check file permissions.'); window.location.href='upload_payment.php?type=" . htmlspecialchars($payment_option) . "';</script>";
            exit();
        }
    } else {
        $error_msg = "Invalid file: ";
        if (!in_array($file['type'], $allowed_types)) $error_msg .= "Type not allowed. ";
        if ($file['size'] > $max_size) $error_msg .= "File too large. ";
        if ($file['error'] !== UPLOAD_ERR_OK) $error_msg .= "Upload error code: " . $file['error'];
        echo "<script>alert('$error_msg'); window.location.href='upload_payment.php?type=" . htmlspecialchars($payment_option) . "';</script>";
        exit();
    }
} else {
    $payment_option = $_SESSION['payment_option'] ?? 'unknown';
    echo "<script>alert('No payment proof uploaded.'); window.location.href='upload_payment.php?type=" . htmlspecialchars($payment_option) . "';</script>";
    exit();
}
?>

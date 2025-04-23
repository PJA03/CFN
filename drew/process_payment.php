<?php
session_start();
require_once '../conn.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Processing Payment</title>
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php
if (!isset($_SESSION['user_id'])) {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'Login Required',
            text: 'Please log in to complete your payment.',
            confirmButtonColor: '#1f4529',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = '../Registration_Page/registration.php';
        });
    </script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$payment_option = $_SESSION['payment_option'] ?? 'unknown'; // Define early for error cases

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['payment_proof'])) {
    $file = $_FILES['payment_proof'];
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size && $file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../Uploads/receipts/';
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

           /// // Fetch cart items from session
          // Fetch cart items from session
$cart_items = $_SESSION['order'] ?? [];
$price_total = $_SESSION['total_price'] ?? 0;

if (!empty($cart_items)) {
    $total_quantity = array_sum(array_column($cart_items, 'quantity')); // Sum of all item quantities

    // Start transaction
    $conn->begin_transaction();

    try {
        // Insert order into tb_orders (single row)
        $query = "INSERT INTO tb_orders (order_date, user_id, email, first_name, last_name, quantity, status, payment_option, payment_proof, isApproved, price_total, trackingLink) 
                  VALUES (NOW(), ?, ?, ?, ?, ?, 'Waiting for Payment', ?, ?, 0, ?, NULL)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iississd", 
            $user_id, 
            $user['email'], 
            $user['first_name'], 
            $user['last_name'], 
            $total_quantity, 
            $payment_option, 
            $file_name, 
            $price_total
        );
        $stmt->execute();
        $order_id = $conn->insert_id;
        $stmt->close();

        // Insert items into tb_order_items
        $query = "INSERT INTO tb_order_items (orderID, productID, product_name, quantity, unit_price, item_total, variant_id) 
                  VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);

        foreach ($cart_items as $item) {
            // Validate that perfume products have a variant_id
            if (strtolower($item['category']) === 'perfume' && (!isset($item['variant_id']) || $item['variant_id'] === null)) {
                throw new Exception("No variant selected for perfume product ID: " . $item['productID']);
            }

            // Fetch unit_price from tb_productvariants
            $unit_price = $item['price'] ?? null; // Use price from cart_items
            if (!$unit_price) {
                if (isset($item['variant_id']) && $item['variant_id'] !== null) {
                    $price_query = "SELECT price FROM tb_productvariants WHERE productID = ? AND variant_id = ?";
                    $price_stmt = $conn->prepare($price_query);
                    $price_stmt->bind_param("ii", $item['productID'], $item['variant_id']);
                } else {
                    $price_query = "SELECT price FROM tb_products WHERE productID = ?";
                    $price_stmt = $conn->prepare($price_query);
                    $price_stmt->bind_param("i", $item['productID']);
                }
                $price_stmt->execute();
                $price_result = $price_stmt->get_result();
                $price_row = $price_result->fetch_assoc();
                $unit_price = $price_row['price'] ?? 0;
                $price_stmt->close();

                if ($unit_price == 0) {
                    throw new Exception("Price not found for product ID: " . $item['productID'] . (isset($item['variant_id']) ? ", variant ID: " . $item['variant_id'] : ""));
                }
            }

            $item_total = $item['quantity'] * $unit_price;
            $variant_id = $item['variant_id'] ?? null;

            $stmt->bind_param("iisiddi", 
                $order_id, 
                $item['productID'], 
                $item['product_name'], 
                $item['quantity'], 
                $unit_price, 
                $item_total,
                $variant_id
            );
            $stmt->execute();
        }
        $stmt->close();

        // Clear cart
        $query = "DELETE FROM tb_cart WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $stmt->close();

        // Commit transaction
        $conn->commit();

        // Clear session data
        unset($_SESSION['order']);
        unset($_SESSION['total_price']);
        unset($_SESSION['payment_option']);

        // Show success SweetAlert and redirect
        echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Order Placed',
                text: 'Your order has been placed successfully.',
                confirmButtonColor: '#1f4529',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'success_order.php';
            });
        </script>";
        exit();
    } catch (Exception $e) {
        // Rollback transaction on error
        $conn->rollback();
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Order Failed',
                text: 'Failed to process order: " . addslashes($e->getMessage()) . "',
                confirmButtonColor: '#1f4529',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'cart.php';
            });
        </script>";
        exit();
    }
}
             else {
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Empty Order',
                        text: 'Your order is empty.',
                        confirmButtonColor: '#1f4529',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'cart.php';
                    });
                </script>";
                exit();
            }
        } else {
            echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: 'Failed to upload receipt. Please check file permissions.',
                    confirmButtonColor: '#1f4529',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'upload_payment.php?type=" . htmlspecialchars($payment_option) . "';
                });
            </script>";
            exit();
        }
    } else {
        $error_msg = "Invalid file: ";
        if (!in_array($file['type'], $allowed_types)) $error_msg .= "Type not allowed. ";
        if ($file['size'] > $max_size) $error_msg .= "File too large. ";
        if ($file['error'] !== UPLOAD_ERR_OK) $error_msg .= "Upload error code: " . $file['error'];
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Invalid File',
                text: '" . addslashes($error_msg) . "',
                confirmButtonColor: '#1f4529',
                confirmButtonText: 'OK'
            }).then(() => {
                window.location.href = 'upload_payment.php?type=" . htmlspecialchars($payment_option) . "';
            });
        </script>";
        exit();
    }
} else {
    echo "<script>
        Swal.fire({
            icon: 'error',
            title: 'No File Uploaded',
            text: 'No payment proof uploaded.',
            confirmButtonColor: '#1f4529',
            confirmButtonText: 'OK'
        }).then(() => {
            window.location.href = 'upload_payment.php?type=" . htmlspecialchars($payment_option) . "';
        });
    </script>";
    exit();
}
?>
</body>
</html>
<?php
session_start();
require_once '../conn.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Registration_Page/registration.php');
    exit();
}

$user_id = $_SESSION['user_id'];
$totalPrice = $_SESSION['total_price'] ?? 0;

if (!isset($_SESSION['order']) || empty($_SESSION['order'])) {
    $_SESSION['payment_error'] = "No items in your order. Please return to cart.";
    header('Location: cart.php');
    exit();
}

// Get cart items from session
$cart_items = $_SESSION['order'];

// Fetch available QR codes from tb_payment_qr_codes
$query = "SELECT payment_type, qr_image FROM tb_payment_qr_codes";
$result = $conn->query($query);
$qr_codes = [];
while ($row = $result->fetch_assoc()) {
    $qr_codes[$row['payment_type']] = $row['qr_image'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="payment.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Select Payment Method</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="cfn_logo2.png" alt="Logo" class="logo-image" />
        </div>
        <div class="navbar">
            <input type="text" class="search-bar" placeholder="Search Product" />
            <div class="icons">
                <a href="../User_Profile_Page/UserProfile.php"><i class="far fa-user-circle fa-2x icon-profile"></i></a>
                <i class="fas fa-bars burger-menu"></i>
            </div>
        </div>
    </header>

    <main>
        <h1 class="cart-title">Payment Method</h1>
        <section class="cart-container">
            <div class="payment-method">
                <h2>SELECT PAYMENT METHOD</h2>
                <?php if (isset($_SESSION['payment_error'])): ?>
                    <div class="alert alert-danger">
                        <?php echo $_SESSION['payment_error']; unset($_SESSION['payment_error']); ?>
                    </div>
                <?php endif; ?>
                <?php if (empty($qr_codes)): ?>
                    <p>No payment methods available at this time.</p>
                <?php else: ?>
                    <?php foreach ($qr_codes as $type => $image): ?>
                        <div class="payment-option" style="width: 150px; height: 150px; background-color: #f0f0f0; border: 2px dashed #1F4529; display: flex; align-items: center; justify-content: center; border-radius: 10px;">
                            <a href="upload_payment.php?type=<?php echo htmlspecialchars($type); ?>" 
                               style="display: flex; flex-direction: column; align-items: center; justify-content: center; width: 100%; height: 100%; text-decoration: none; color: inherit;">
                                <img src="../uploads/qr/<?php echo htmlspecialchars($image); ?>" 
                                     alt="<?php echo ucfirst(str_replace('_', ' ', $type)); ?>" 
                                     class="payment-logo" 
                                     style="max-width: 60%; max-height: 60%; margin-bottom: 10px;">
                                <p style="margin: 0; font-weight: 500;"><?php echo ucfirst(str_replace('_', ' ', $type)); ?></p>
                            </a>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <div class="cart-summary">
                <h3>Order Summary</h3>
                <div class="order-items">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Quantity</th>
                                <th>Price</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['product_name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['quantity']); ?></td>
                                    <td>₱<?php echo number_format($item['price'] * $item['quantity'], 2); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <div class="price-breakdown">
                    <p>Total Price: <strong id="total-price">₱<?php echo number_format($totalPrice, 2); ?></strong></p>
                </div>
            </div>

            <div class="cart-actions">
                <a href="cart.php" class="btn cancel-btn">Back to Cart</a>
            </div>
        </section>
    </main>

    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <img src="cfn_logo.png" alt="Naturale Logo" class="footer-logo">
            </div>
            <div class="footer-right">
                <ul class="footer-nav">
                    <li><a href="#">ABOUT US</a></li>
                    <li><a href="#">PRODUCTS</a></li>
                    <li><a href="#">LOGIN</a></li>
                    <li><a href="#">SIGN UP</a></li>
                </ul>
            </div>
            <div class="social-icons">
                <p>SOCIALS</p>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-center">© COSMETICAS 2024</div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
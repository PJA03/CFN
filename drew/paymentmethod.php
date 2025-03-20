<?php
session_start();
include 'conn.php'; // Ensure this file connects to your database


// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    //TODO: Make it an alert tapos stay on the product details page
    header('Location: ../Registration_Page/registration.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$totalPrice = $_SESSION['total_price'] ?? 0;

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="payment.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Method</title>
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
                <i class="far fa-user-circle fa-2x icon-profile"></i>
                <i class="fas fa-bars burger-menu"></i>
            </div>
        </div>
    </header>

    <main>
        <h1 class="cart-title">Payment Method</h1>
        <section class="cart-container">
            <!-- Payment Method Section -->
            <div class="payment-method">
                <h2>SELECT PAYMENT METHOD</h2>
                
                <!-- Display error message if any -->
                <?php if (isset($_SESSION['payment_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['payment_error']; unset($_SESSION['payment_error']); ?>
                </div>
                <?php endif; ?>
                
                <div class="payment-options">
                    <div class="payment-option">
                        <a href="uploadpayment.php?type=gcash">
                            <img src="gcash_logo.png" alt="GCash" class="payment-logo">
                            <p>GCash</p>
                        </a>
                    </div>
                    
                    <div class="payment-option">
                        <a href="uploadpayment.php?type=paymaya">
                            <img src="paymaya_logo.png" alt="PayMaya" class="payment-logo">
                            <p>PayMaya</p>
                        </a>
                    </div>
                    
                    <div class="payment-option">
                        <a href="uploadpayment.php?type=instapay">
                            <img src="instapay.png" alt="Instapay" class="payment-logo">
                            <p>Instapay</p>
                        </a>
                    </div>
                </div>
                
                <div class="cart-summary">
                    <h3>Order Summary</h3>
                    <div class="price-breakdown">
                       
                        <p>Total Price: <strong id="total-price">₱<?php echo number_format($totalPrice, 2); ?></strong></p>
                    </div>
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
        <div class="footer-center">
            &copy; COSMETICAS 2024
        </div>
    </footer>

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
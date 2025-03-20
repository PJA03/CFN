<?php
session_start();

// Redirect if no successful order
if (!isset($_SESSION['order_success']) || $_SESSION['order_success'] !== true) {
    header('Location: cart.php');
    exit;
}

// Get the order ID
$order_id = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : 'Unknown';

// Clear the success flag but keep the order ID for reference
unset($_SESSION['order_success']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="success.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Success</title>
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
        <div class="success-container">
            <div class="success-card">
                <div class="success-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <h1>CHECKOUT SUCCESSFUL</h1>
                <p>
                    Thank you! Your payment has been successfully processed.<br><br>
                    Order #: <?php echo $order_id; ?><br><br>
                    A confirmation email/receipt has been sent to your provided email address. Please note that the seller will still verify the transaction before completing the process<br><br>
                </p>
                <div class="success-actions">
                    <a href="index.php" class="btn home-btn">Back to Homepage</a>
                    <a href="orders.php" class="btn orders-btn">View My Orders</a>
                </div>
            </div>
        </div>
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
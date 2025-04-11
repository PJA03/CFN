<?php
session_start();

// Redirect if order is empty or not set
if (!isset($_SESSION['order']) || empty($_SESSION['order'])) {
    header('Location: cart.php');
    exit;
}
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
            <a href = "../Home_Page/Home.php"><img src="../Home_Page/cfn_logo2.png" alt="Logo" class="logo-image"/></a>
        </div>
        <div class="navbar">
        <p class="usernamedisplay">Bonjour, <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>            
        <form action="../Home_Page/ProductScroll.php" method="GET" class="search-form" onsubmit="return validateSearch()">
            <input type="text" name="search" class="search-bar" id="searchBar" placeholder="Search Product">
        </form>            
        <div class="icons">
                <a href = "../Home_Page/Home.php"><i class="fa-solid fa-house home"></i></a>
                <a href ="../drew/cart.php"><i class="fa-solid fa-cart-shopping cart"></i></a>
                <a href="../User_Profile_Page/UserProfile.php"><i class ="far fa-user-circle fa-2x icon-profile"></i></a>
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
                        <?php
                        $netPrice = 0;
                        if (!empty($_SESSION['order'])) {
                            foreach ($_SESSION['order'] as $item) {
                                $netPrice += $item['price'] * $item['quantity'];
                            }
                        }
                        $vat = $netPrice * 0.12;
                        $deliveryFee = 40.00;
                        $totalPrice = $netPrice + $vat + $deliveryFee;
                        ?>
                        <p>Net Price: <span id="net-price">₱<?php echo number_format($netPrice, 2); ?></span></p>
                        <p>VAT: <span id="vat">₱<?php echo number_format($vat, 2); ?></span></p>
                        <p>Delivery Fee: <span id="delivery-fee">₱<?php echo number_format($deliveryFee, 2); ?></span></p>
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
                <img src="../Resources/cfn_logo.png" alt="Naturale Logo" class="footer-logo">
            </div>
            <div class="footer-right">
                <ul class="footer-nav">
                    <li><a href="../User_Profile_Page/aboutUs.php">About Us</a></li>
                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#ModalTerms">Terms and Conditions</a></li>
                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#ModalPrivacy">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="social-icons">
                <p>SOCIALS</p>
                <a href="https://www.facebook.com/share/1CRTizfAxP/?mibextid=wwXIfr" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/cosmeticasfraiche?igsh=ang2MHg1MW5qZHQw" target="_blank"><i class="fab fa-instagram"></i></a>
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
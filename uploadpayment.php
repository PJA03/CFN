<?php
session_start();

// Redirect if order is empty or not set
if (!isset($_SESSION['order']) || empty($_SESSION['order'])) {
    header('Location: cart.php');
    exit;
}

// QR code that was selected
$qr_type = isset($_GET['type']) ? $_GET['type'] : 'gcash';
$qr_image = 'gcashqr.jpg'; // Default

// Adjust image based on type
if ($qr_type == 'paymaya') {
    $qr_image = 'paymayaqr.jpg';
} elseif ($qr_type == 'instapay') {
    $qr_image = 'instapay.png';
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="uploadpayment.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Receipt</title>
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
        <section class="cart-container">
            <div class="upload-receipt">
                <h2>QR Payment</h2>
                <div class="qr-and-text">
                    <!-- QR Code Image -->
                    <div class="qr-code-placeholder">
                        <img src="gcashqr.jpg" alt="QR Code" class="qr-code-image">
                    </div>
                    <!-- Text Content -->
                    <div class="text-content">
                        <p>QR PH (GCASH/PAYMAYA)</p>
                        <p>Scan the QR code to make your payment</p>
                        <p>SCAN TO PAY HERE</p>
                        <p>Kindly upload your E-receipt to verify your payment</p>
                    </div>
                </div>

                <!-- Upload Button with Form -->
                <form action="process_payment.php" method="POST" enctype="multipart/form-data">
                    <div class="upload-container">
                        <label for="payment-proof" class="upload-btn">
                            <i class="fas fa-upload"></i> Upload E-Receipt
                        </label>
                        <input type="file" id="payment-proof" name="payment_proof" accept=".png, .jpeg, .jpg" required style="display: none;">
                        <p id="file-name"></p>
                    </div>
                    <div class="submit-container">
                        <button type="submit" class="btn submit-btn">Submit Payment</button>
                    </div>
                </form>
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
    <script>
        // Display selected filename
        document.getElementById('payment-proof').addEventListener('change', function() {
            var fileName = this.files[0] ? this.files[0].name : '';
            document.getElementById('file-name').textContent = fileName;
        });
    </script>
</body>

</html>
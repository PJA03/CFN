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

if (isset($_SESSION['email'])) {
    $user = [
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'first_name' => $_SESSION['first_name'],
        'last_name' => $_SESSION['last_name'],
        'contact_no' => $_SESSION['contact_no'],
        'address' => $_SESSION['address'],
        'profile_image' => $_SESSION['profile_image'],
    ];
} else {
    header('Location: ../Registration_Page/registration.php');
    exit();
}

$username = $_SESSION['username'];
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
    <link rel="stylesheet" href="payment.css?v=<?php echo time(); ?>">
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
        <a href="../Home_Page/Home.php"><img src="../Home_Page/cfn_logo2.png" alt="Logo" class="logo-image"/></a>
    </div>
    <div class="navbar">
        <p class="usernamedisplay">Bonjour, <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>            
        <form action="../Home_Page/ProductScroll.php" method="GET" class="search-form" onsubmit="return validateSearch()">
            <input type="text" name="search" class="search-bar" id="searchBar" placeholder="Search Product">
        </form>            
        <div class="icons">
            <a href="../Home_Page/Home.php"><i class="fa-solid fa-house home"></i></a>
            <a href="../drew/cart.php"><i class="fa-solid fa-cart-shopping cart"></i></a>
            <a href="../User_Profile_Page/UserProfile.php"><i class="far fa-user-circle fa-2x icon-profile"></i></a>
        </div>
    </div>
</header>

<main>
    <section class="cart-container">
        <!-- Modal Privacy Notice -->
        <div class="modal fade" id="ModalPrivacyNotice" tabindex="-1" role="dialog" aria-labelledby="privacyNoticeModalTitle" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header" style="background-color: #1F4529;">
                        <h5 class="modal-title" id="privacyNoticeModalTitle" style="font-weight: bold; color: white;">PRIVACY NOTICE</h5>
                    </div>
                    <div class="modal-body" style="text-align: justify;">
                        <p style="text-align: center; font-weight: bold;">We value your privacy and are committed to protecting your personal information.</p>
                        <p>When you upload your payment details, they are securely encrypted and used solely for processing your transactions. We do not store sensitive payment information such as card details beyond the necessary processing period, and all data is handled in compliance with applicable data protection laws.</p>
                        <p>If you have questions about how we handle your data, please <strong>contact us</strong> at cosmeticasfraichenaturale@gmail.com</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-success" data-bs-dismiss="modal">Accept</button>
                        <button type="button" class="btn btn-secondary" id="declinePrivacy">Decline</button>
                    </div>
                </div>
            </div>
        </div>

        <script>
            // Show privacy notice modal on page load
            document.addEventListener('DOMContentLoaded', function() {
                var privacyModal = new bootstrap.Modal(document.getElementById('ModalPrivacyNotice'));
                privacyModal.show();
                
                // Handle decline button click
                document.getElementById('declinePrivacy').addEventListener('click', function() {
                    window.location.href = 'cart.php';
                });
            });
        </script>

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
                                <td>
                                    <?php 
                                    echo htmlspecialchars($item['product_name']); 
                                    if (strtolower($item['category']) === 'perfume' && !empty($item['variant_name'])) {
                                        echo " (" . htmlspecialchars($item['variant_name']) . ")";
                                    }
                                    ?>
                                </td>
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

        <div class="payment-method">
            <h2>SELECT PAYMENT METHOD</h2>
            <?php if (isset($_SESSION['payment_error'])): ?>
                <div class="alert alert-danger">
                    <?php echo $_SESSION['payment_error']; unset($_SESSION['payment_error']); ?>
                </div>
            <?php endif; ?>
            <div class="payment-options-container">
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
        © COSMETICAS 2024
    </div>
</footer>

<!-- Modal Terms -->
<div class="modal fade" id="ModalTerms" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1F4529;">
                <h5 class="modal-title" id="exampleModalLongTitle" style="font-weight: bold;">CFN Naturale Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <b>1. Introduction</b><br>
                Welcome to Cosmeticas Fraiche Naturale. By accessing or using our website, you agree to comply with these Terms of Use. If you do not agree, please do not use our services.<br><br>
                <b>2. Use of Website</b><br>
                You must be at least 16 years old to use our website. You agree to use the website only for lawful purposes and in accordance with these terms.<br><br>
                <b>3. Account Registration</b><br>
                To make purchases, you may need to create an account. You are responsible for maintaining the confidentiality of your account and password.<br><br>
                <b>4. Orders and Payments</b><br>
                All prices are listed in Philippine Peso. We reserve the right to refuse or cancel orders at our discretion. Payments must be completed before orders are processed.<br><br>
                <b>5. Shipping and Cancellation of Orders</b><br>
                We strive to deliver products in a timely manner. All sales are final, and we do not accept returns or exchanges. As for cancellations, it is allowed as long as the orders are not confirmed yet.<br><br>
                <b>6. Intellectual Property</b><br>
                All content on this site, including logos, text, and images, is owned by Cosmeticas Fraiche Naturale and may not be used without permission.<br><br>
                <b>7. Limitation of Liability</b><br>
                We are not responsible for any indirect, incidental, or consequential damages arising from the use of our website or products.<br><br>
                <b>8. Changes to Terms</b><br>
                We may update these terms at any time. Continued use of the website means you accept the updated terms.<br><br>
                <b>9. Contact Information</b><br>
                For any questions, contact us at cosmeticasfraichenaturale@gmail.com.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Privacy Policy -->
<div class="modal fade" id="ModalPrivacy" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1F4529;">
                <h5 class="modal-title" id="exampleModalLongTitle" style="font-weight: bold;">CFN Naturale Privacy Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <b>1. Information We Collect</b><br>
                We collect personal information, such as your name, email, shipping address, and payment details when you make a purchase or create an account.<br><br>
                <b>2. How We Use Your Information</b><br>
                We use your information to process orders, improve our website, and communicate with you about promotions or support inquiries.<br><br>
                <b>3. Sharing of Information</b><br>
                We do not sell your personal information. However, we may share it with third-party service providers for payment processing or shipping.<br><br>
                <b>4. Cookies and Tracking</b><br>
                We use cookies to enhance your browsing experience. You can disable cookies in your browser settings, but some features may not function properly.<br><br>
                <b>5. Data Security</b><br>
                We implement security measures to protect your data but cannot guarantee complete security due to internet vulnerabilities.<br><br>
                <b>6. Your Rights</b><br>
                You have the right to access, update, or delete your personal information. Contact us at cosmeticasfraichenaturale@gmail.com for any requests.<br><br>
                <b>7. Changes to Privacy Policy</b><br>
                We may update this policy. Continued use of our services after updates means you accept the revised policy.<br><br>
                <b>8. Contact Information</b><br>
                For privacy-related concerns, contact us at cosmeticasfraichenaturale@gmail.com.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
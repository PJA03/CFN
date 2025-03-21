<?php
session_start();
require_once '../conn.php';

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please log in to proceed with payment.'); window.location.href='../Registration_Page/registration.php';</script>";
    exit();
}

$user_id = $_SESSION['user_id'];
$qr_type = isset($_GET['type']) ? $_GET['type'] : null;

if (!isset($_SESSION['order']) || empty($_SESSION['order'])) {
    echo "<script>alert('No items in your order. Please return to cart.'); window.location.href='cart.php';</script>";
    exit();
}

// Fetch QR code from tb_payment_qr_codes based on qr_type
if ($qr_type) {
    $query = "SELECT qr_image FROM tb_payment_qr_codes WHERE payment_type = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $qr_type);
    $stmt->execute();
    $result = $stmt->get_result();
    $qr_row = $result->fetch_assoc();

    if ($qr_row) {
        $qr_image = '../uploads/qr/' . htmlspecialchars($qr_row['qr_image']);
        $_SESSION['payment_option'] = $qr_type; // Store payment option in session
    } else {
        echo "<script>alert('Invalid payment method selected. Please choose a valid option.'); window.location.href='select_payment.php';</script>";
        exit();
    }
} else {
    echo "<script>alert('No payment method specified.'); window.location.href='select_payment.php';</script>";
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Payment Receipt</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="uploadpayment.css">
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
        <section class="cart-container">
            <div class="upload-receipt">
                <h2>QR Payment</h2>
                <div class="qr-and-text">
                    <div class="qr-code-placeholder">
                        <img src="<?php echo htmlspecialchars($qr_image); ?>" alt="QR Code" class="qr-code-image">
                    </div>
                    <div class="text-content">
                        <p>QR PH (<?php echo strtoupper(str_replace('_', ' ', $qr_type)); ?>)</p>
                        <p>Scan the QR code to make your payment</p>
                        <p>SCAN TO PAY HERE</p>
                        <p>Kindly upload your E-receipt to verify your payment</p>
                    </div>
                </div>

                <form action="process_payment.php" method="POST" enctype="multipart/form-data">
                    <div class="upload-container">
                        <label for="payment-proof" class="upload-btn">
                            <i class="fas fa-upload"></i> Upload E-Receipt
                        </label>
                        <input type="file" id="payment-proof" name="payment_proof" accept=".png, .jpeg, .jpg" required style="display: none;">
                        <p id="file-name"></p>
                    </div>
                    <div id="image-preview-container" style="margin-top: 10px;">
                        <img id="image-preview" src="" alt="Image Preview" style="max-width: 100%; max-height: 200px; display: none;">
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
        <div class="footer-center">© COSMETICAS 2024</div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('payment-proof').addEventListener('change', function () {
            const fileInput = this;
            const fileName = fileInput.files[0] ? fileInput.files[0].name : '';
            const file = fileInput.files[0];
            const previewImage = document.getElementById('image-preview');
            document.getElementById('file-name').textContent = fileName;
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function (e) {
                    previewImage.src = e.target.result;
                    previewImage.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewImage.src = '';
                previewImage.style.display = 'none';
            }
        });
    </script>
</body>
</html>
<?php
session_start();
require_once '../conn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Registration_Page/registration.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch order data from the database
$query = "SELECT o.orderID, o.order_date, o.product_name, o.quantity, o.price_total, o.status, o.trackingLink 
          FROM tb_orders o
          WHERE o.user_id = ?
          ORDER BY o.order_date DESC";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["orderID"])) {
    $orderID = $_POST["orderID"];

    // Update the order status to "Cancelled" if the status is "waiting for payment"
    $query = "UPDATE tb_orders SET status = 'Cancelled' WHERE orderID = ? AND status = 'waiting for payment'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $orderID);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Order cancelled successfully.";
    } else {
        $_SESSION['message'] = "Failed to cancel order.";
    }

    header("Location: orderlist.php"); // Redirect back to order list
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="orderlist.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.min.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href="../Home_Page/Home.php"><img src="../Home_Page/cfn_logo2.png" alt="Logo" class="logo-image"/></a>
        </div>
        <div class="navbar">
            <p class="usernamedisplay">Bonjour, <?php echo htmlspecialchars($_SESSION['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>
            <div class="icons">
                <a href="../Home_Page/Home.php"><i class="fa-solid fa-house home"></i></a>
                <a href="../drew/cart.php"><i class="fa-solid fa-cart-shopping cart"></i></a>
                <a href="../User_Profile_Page/UserProfile.php"><i class="far fa-user-circle fa-2x icon-profile"></i></a>
            </div>
        </div>
    </header>

    <main>
        <h1 class="cart-title">Order List</h1>
        <section class="cart-container">
            <div class="cart-content">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th class="date-header">Date</th>
                            <th class="order-list-header">Order List</th>
                            <th class="total-header">Total</th>
                            <th class="payment-method-header">Tracking Link</th>
                            <th class="status-header">Status</th>
                            <th class="tracking-link-header">Action</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
        <?php if (!empty($orders)): ?>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td><?php echo htmlspecialchars(date('M j, Y', strtotime($order['order_date']))); ?></td>
                    <td><?php echo htmlspecialchars($order['product_name']); ?> (x<?php echo $order['quantity']; ?>)</td>
                    <td>₱<?php echo number_format($order['price_total'], 2); ?></td>
                    <td><a href="<?= htmlspecialchars($order['trackingLink']) ?>"><?= htmlspecialchars($order['trackingLink']) ?></a></td>
                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                    <td>
                        <?php if ($order['status'] === 'Waiting for Payment'): ?>
                            <form action="" method="POST">
                                <input type="hidden" name="orderID" value="<?php echo $order['orderID']; ?>">
                                <button type="submit" style="background-color: red; color: white; border: none; padding: 5px 10px;">Cancel</button>
                            </form>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7">You have no orders yet.</td>
            </tr>
        <?php endif; ?>
    </tbody>

                </table>
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
                    <li><a href="#">About Us</a></li>
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

    <!-- Modal Terms and Conditions -->
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
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="cancelOrderModal" tabindex="-1" role="dialog" aria-labelledby="cancelOrderModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1F4529;">
                <h5 class="modal-title" id="cancelOrderModalTitle" style="font-weight: bold; color: white;">Confirm Cancellation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to cancel this order?
            </div>
            <div class="modal-footer">
                <input type="hidden" name="orderID" id="cancelOrderId" value="">
                <button type="button" class="btn btn-danger" style="background-color: #d34646 !important; color: white !important; border-color: #d34646 !important;">Yes</button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="background-color: #1f4529 !important; color: white !important; border-color: #1f4529 !important;">No</button>
                <form id="confirmCancelForm" action="" method="POST"></form>
            </div>
        </div>
    </div>
</div>

<style>
    #cancelOrderModal .btn-secondary:hover, 
    #cancelOrderModal .btn-secondary:focus {
        background-color: #1f4529 !important;
        color: white !important;
    }
    #cancelOrderModal .btn-danger:hover, 
    #cancelOrderModal .btn-danger:focus {
        background-color: #d34646 !important;
        color: white !important;
    }
</style>

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
document.addEventListener('DOMContentLoaded', function() {
    // Get all cancel buttons
    const cancelButtons = document.querySelectorAll('tr form button[type="submit"]');

    // Add click event to each cancel button
    cancelButtons.forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            
            // Get the orderID from the hidden input in the same form
            const orderID = this.closest('form').querySelector('input[name="orderID"]').value;
            
            // Show SweetAlert confirmation
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to cancel this order?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d34646',
                cancelButtonColor: '#1f4529',
                confirmButtonText: 'Yes, cancel it!',
                cancelButtonText: 'No, keep it'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Submit the form via AJAX
                    fetch('', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: `orderID=${orderID}`
                    })
                    .then(response => response.text())
                    .then(() => {
                        // Update the status in the table
                        const row = this.closest('tr');
                        row.querySelector('td:nth-child(5)').textContent = 'Cancelled';

                        // Disable the cancel button
                        this.disabled = true;
                        this.textContent = 'Cancelled';
                        this.style.backgroundColor = 'gray';
                    })
                    .catch(error => console.error('Error:', error));
                }
            });
        });
    });
});
</script>

</body>
</html>

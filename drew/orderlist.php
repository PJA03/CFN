<?php
session_start();
require_once '../conn.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Registration_Page/registration.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cart data from the database
$query = "SELECT c.productID, c.quantity, c.price, p.product_name, p.product_image 
          FROM tb_cart c
          JOIN tb_products p ON c.productID = p.productID
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orderItems = [];
$totalNetPrice = 0;

while ($row = $result->fetch_assoc()) {
    $orderItems[] = $row;
    $totalNetPrice += floatval($row['price']) * intval($row['quantity']);
}


// Calculate total price, VAT, and net price
$netPrice = 0;
$totalVAT = 0;
$totalPrice = 0;

if (!empty($orderItems)) {
    foreach ($orderItems as $item) {
        $itemTotalPrice = $item['price'] * $item['quantity']; // Total price for this item
        $itemVAT = $itemTotalPrice * 0.12; // 12% VAT for this item
        $itemNetPrice = $itemTotalPrice - $itemVAT; // Net price for this item (price minus VAT)

        $netPrice += $itemNetPrice; // Accumulate net price
        $totalVAT += $itemVAT; // Accumulate VAT
        $totalPrice += $itemTotalPrice; // Accumulate total price
    }
}

// Store the total price in the session
$_SESSION['total_price'] = $totalPrice;


// Handle order cancellation
if (isset($_POST['cancel_order'])) {
    unset($_SESSION['order']);
    header("Location: cart.php");
    exit();
}

// echo "<pre>";
// print_r($_SESSION);
// echo "</pre>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="orderlist.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>
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
             <a href = "../User_Profile_Page/UserProfile.php">   <i class="far fa-user-circle fa-2x icon-profile"></i> </a>
                <i class="fas fa-bars burger-menu"></i>
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
                            <th class="product-header">Product</th>
                            <th class="qty-header">Qty</th>
                            <th class="price-header">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php if (!empty($orderItems)): ?>
                        <?php foreach ($orderItems as $item): ?>
                            <tr>
                                <td class="product-info">
                                    <img src="<?php echo htmlspecialchars('../e-com/' . $item['product_image'], ENT_QUOTES, 'UTF-8'); ?>" 
                                        alt="<?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?>" 
                                        class="product-image">
                                    <span><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="product-quantity">
                                    <span class="quantity-value"><?php echo $item['quantity']; ?></span>
                                </td>
                                <td class="product-price">₱<span class="price-value"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">You have no orders yet.</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
                <div class="cart-summary">
                <div class="price-breakdown">
                    <p>Net Price: <span id="net-price">₱<?php echo number_format($netPrice, 2); ?></span></p>
                    <p>VAT (12%): <span id="vat">₱<?php echo number_format($totalVAT, 2); ?></span></p>
                    <p>Total Price: <strong id="total-price">₱<?php echo number_format($totalPrice, 2); ?></strong></p>
                </div>
                    <div class="delivery-note">
                        *Delivery fee is calculated by our third-party carrier.
                    </div>
                </div>
            </div>

            <div class="cart-actions">
                <a href="paymentmethod.php" class="btn payment-btn">Payment</a>
                
                <button class="btn cancel-btn" id="cancel-order-btn" data-bs-toggle="modal" data-bs-target="#cancelModal">Cancel Order</button>            </div>
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

    <!-- Cancel Order Confirmation Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Cancel Order</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to cancel your order?
                </div>
                <div class="modal-footer">
                    <form method="POST">
                        <input type="hidden" name="cancel_order" value="1">
                        <button type="submit" class="btn btn-danger" id="confirm-cancel-btn">Yes, Cancel</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            // Cancel order confirmation
            const cancelBtn = document.getElementById("cancel-order-btn");
            const cancelModal = document.getElementById("cancelModal");
            const cancelModalClose = document.getElementById("cancelModalClose");
            
            cancelBtn.addEventListener("click", function() {
                cancelModal.style.display = "flex";
            });
            
            cancelModalClose.addEventListener("click", function() {
                cancelModal.style.display = "none";
            });
        });
    </script>
</body>
</html>
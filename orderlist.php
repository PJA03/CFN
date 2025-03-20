<?php
session_start();

// Check if order data exists in session
if (isset($_SESSION['order']) && !empty($_SESSION['order'])) {
    $orderItems = $_SESSION['order'];
    
    // Calculate total price
    $totalNetPrice = 0;
    foreach ($orderItems as $item) {
        $totalNetPrice += floatval($item['price']) * intval($item['quantity']);
    }
    $vat = $totalNetPrice * 0.12;
    $deliveryFee = 40.00;
    $totalPrice = $totalNetPrice + $vat + $deliveryFee;
} else if (isset($_SESSION['cart']) && !empty($_SESSION['cart'])) {
    // If no order exists but cart does, use cart data
    $orderItems = $_SESSION['cart'];
    
    // Calculate total price
    $totalNetPrice = 0;
    foreach ($orderItems as $item) {
        $totalNetPrice += floatval($item['price']) * intval($item['quantity']);
    }
    $vat = $totalNetPrice * 0.12;
    $deliveryFee = 40.00;
    $totalPrice = $totalNetPrice + $vat + $deliveryFee;
} else {
    // Redirect to cart if no data is available
    header("Location: cart.php");
    exit();
}

// Handle order cancellation
if (isset($_POST['cancel_order'])) {
    unset($_SESSION['order']);
    header("Location: cart.php");
    exit();
}
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
                <i class="far fa-user-circle fa-2x icon-profile"></i>
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
                        <?php foreach ($orderItems as $id => $item): ?>
                        <tr>
                            <td class="product-info">
                                <?php if (isset($item['image']) && !empty($item['image'])): ?>
                                <img src="<?php echo $item['image']; ?>" alt="<?php echo $item['name']; ?>" class="product-image">
                                <?php else: ?>
                                <img src="description.png" alt="<?php echo $item['name']; ?>" class="product-image">
                                <?php endif; ?>
                                <span><?php echo $item['name']; ?></span>
                            </td>
                            <td class="product-quantity">
                                <span class="quantity-value"><?php echo $item['quantity']; ?></span>
                            </td>
                            <td class="product-price">₱<span class="price-value"><?php echo number_format($item['price'], 2); ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>

                <div class="cart-summary">
                    <div class="price-breakdown">
                        <p>Net Price: <span id="net-price">₱<?php echo number_format($totalNetPrice, 2); ?></span></p>
                        <p>VAT: <span id="vat">₱<?php echo number_format($vat, 2); ?></span></p>
                        <p>Delivery Fee: <span id="delivery-fee">₱<?php echo number_format($deliveryFee, 2); ?></span></p>
                        <p>Total Price: <strong id="total-price">₱<?php echo number_format($totalPrice, 2); ?></strong></p>
                    </div>
                    <div class="delivery-note">
                        *Delivery fee is calculated by our third-party carrier.
                    </div>
                </div>
            </div>

            <div class="cart-actions">
                <a href="paymentmethod.php" class="btn payment-btn">Payment</a>
                <button class="btn cancel-btn" id="cancel-order-btn">Cancel Order</button>
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

    <!-- Cancel Order Confirmation Modal -->
    <div id="cancelModal" class="modal-overlay">
        <div class="modal-content">
            <h2>CANCEL ORDER</h2>
            <p>Are you sure you want to cancel your order?</p>
            <div class="modal-buttons">
                <form method="POST">
                    <input type="hidden" name="cancel_order" value="1">
                    <button type="submit" class="btn yes-btn">Yes</button>
                </form>
                <button id="cancelModalClose" class="btn no-btn">No</button>
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
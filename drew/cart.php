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

// Fetch cart data from the database
$query = "SELECT c.productID, c.quantity, c.price, p.product_name, p.product_image 
          FROM tb_cart c
          JOIN tb_products p ON c.productID = p.productID
          WHERE c.user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

// Handle checkout
if (isset($_POST['confirm_checkout'])) {
    $_SESSION['order'] = $_SESSION['cart']; // Store cart data for orderlist
    header('Location: orderlist.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="cart.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cart</title>
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
        <h1 class="cart-title">Cart</h1>
        <section class="cart-container">
            <div class="cart-content">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Qty</th>
                            <th>Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cart_items)): ?>
                        <?php foreach ($cart_items as $item): ?>
                            <tr data-product-id="<?php echo $item['productID']; ?>">
                                <td class="product-info">
                                <img src="<?php echo htmlspecialchars('../e-com/' . $item['product_image'], ENT_QUOTES, 'UTF-8'); ?>" 
                                alt="<?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?>" 
                                        class="product-image">
                                    <span><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                </td>
                                <td class="product-quantity">
                                    <button class="quantity-btn minus-btn">-</button>
                                    <span class="quantity-value"><?php echo $item['quantity']; ?></span>
                                    <button class="quantity-btn plus-btn">+</button>
                                    <i class="fas fa-trash delete-icon"></i>
                                </td>
                                <td class="product-price">₱<span class="price-value"><?php echo number_format($item['price'], 2); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="3">Your cart is empty</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                </table>

                <div class="cart-summary">
                    <div class="price-breakdown">
                        <?php
                        $netPrice = 0;
                        $totalVAT = 0;
                        $totalPrice = 0;
                        
                        if (!empty($cart_items)) {
                            foreach ($cart_items as $item) {
                                $itemTotalPrice = $item['price'] * $item['quantity']; // Total price for this item
                                $itemVAT = $itemTotalPrice * 0.12; // 12% VAT for this item
                                $itemNetPrice = $itemTotalPrice - $itemVAT; // Net price for this item (price minus VAT)
                        
                                $netPrice += $itemNetPrice; // Accumulate net price
                                $totalVAT += $itemVAT; // Accumulate VAT
                                $totalPrice += $itemTotalPrice; // Accumulate total price
                            }
                        }
                        ?>
                        <p>Net Price: <span id="net-price">₱<?php echo number_format($netPrice, 2); ?></span></p>
                        <p>VAT (12%): <span id="vat">₱<?php echo number_format($totalVAT, 2); ?></span></p>
                        <p>Total Price: <strong id="total-price">₱<?php echo number_format($totalPrice, 2); ?></strong></p>
                    </div>
                    <div class="delivery-note">
                        *Delivery fee is calculated by our third-party carrier.
                    </div>
                </div>
            </div>
        </section>

        <div class="cart-actions">
            <a href="#" class="btn checkout-btn">Checkout</a>
            <!-- <button class="btn back-btn">Back to Homepage</button> -->
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
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            // Quantity and price management
            const minusBtn = document.querySelectorAll(".minus-btn");
            const plusBtn = document.querySelectorAll(".plus-btn");
            const deleteIcon = document.querySelectorAll(".delete-icon");
            
            function updateTotalPrice() {
                let netPrice = 0;
                document.querySelectorAll('.cart-table tbody tr').forEach(row => {
                    const qty = parseInt(row.querySelector('.quantity-value').textContent);
                    const price = parseFloat(row.querySelector('.price-value').textContent);
                    netPrice += qty * price;
                });
                
                const vat = netPrice * 0.12;
                const deliveryFee = 40.00;
                const totalPrice = netPrice + vat + deliveryFee;
                
                document.getElementById('net-price').textContent = `₱${netPrice.toFixed(2)}`;
                document.getElementById('vat').textContent = `₱${vat.toFixed(2)}`;
                document.getElementById('total-price').textContent = `₱${totalPrice.toFixed(2)}`;
            }
            
            function updateCartInDatabase(productId, quantity) {
                fetch('update_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=${quantity}`
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        console.error('Failed to update cart:', data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
            
            // Event listeners for plus buttons
            plusBtn.forEach(btn => {
                btn.addEventListener("click", function () {
                    const row = this.closest("tr");
                    const productId = row.getAttribute('data-product-id');
                    const quantityEl = this.parentElement.querySelector(".quantity-value");
                    let qty = parseInt(quantityEl.textContent);
                    qty++;
                    quantityEl.textContent = qty;
                    updateTotalPrice();
                    updateCartInDatabase(productId, qty);
                });
            });
            
            // Event listeners for minus buttons
            minusBtn.forEach(btn => {
                btn.addEventListener("click", function () {
                    const row = this.closest("tr");
                    const productId = row.getAttribute('data-product-id');
                    const quantityEl = this.parentElement.querySelector(".quantity-value");
                    let qty = parseInt(quantityEl.textContent);
                    if (qty > 1) {
                        qty--;
                        quantityEl.textContent = qty;
                        updateTotalPrice();
                        updateCartInDatabase(productId, qty);
                    }
                });
            });
            
            // Event listeners for delete icons
            deleteIcon.forEach(icon => {
                icon.addEventListener("click", function () {
                    const row = this.closest("tr");
                    const productId = row.getAttribute('data-product-id');
                    
                    if (!productId) {
                        console.error('No product ID found for this row');
                        return;
                    }
                    
                    // AJAX request to remove item from cart
                    fetch('remove_from_cart.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'product_id=' + productId
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            row.remove();
                            updateTotalPrice();
                            
                            // Check if cart is empty after removal
                            const remainingRows = document.querySelectorAll('.cart-table tbody tr').length;
                            if (remainingRows === 0) {
                                const tbody = document.querySelector('.cart-table tbody');
                                tbody.innerHTML = '<tr><td colspan="3">Your cart is empty</td></tr>';
                            }
                        } else {
                            console.error('Failed to remove item:', data.message);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
                });
            });

            // Checkout Modal Functionality
            const checkoutBtn = document.querySelector(".checkout-btn");
            const checkoutModal = document.getElementById("checkoutModal");
            const confirmCheckout = document.getElementById("confirmCheckout");
            const cancelCheckout = document.getElementById("cancelCheckout");

            // Show Modal on Checkout Click
            if (checkoutBtn) {
                checkoutBtn.addEventListener("click", function (event) {
                    event.preventDefault();
                    checkoutModal.style.display = "flex";
                });
            }

            // Redirect to Order List on Confirm
            if (confirmCheckout) {
                confirmCheckout.addEventListener("click", function () {
                    document.getElementById("checkoutForm").submit();
                });
            }

            // Hide Modal on Cancel
            if (cancelCheckout) {
                cancelCheckout.addEventListener("click", function () {
                    checkoutModal.style.display = "none";
                });
            }
        });
    </script>

    <!-- Checkout Confirmation Modal -->
    <div id="checkoutModal" class="modal-overlay">
        <div class="modal-content">
            <h2>CONFIRM CHECKOUT</h2>
            <p>Make sure to check your items before checking out</p>
            <div class="modal-buttons">
                <form id="checkoutForm" method="POST">
                    <input type="hidden" name="confirm_checkout" value="1">
                    <button id="confirmCheckout" type="button" class="btn yes-btn">Yes</button>
                </form>
                <button id="cancelCheckout" class="btn no-btn">No</button>
            </div>
        </div>
    </div>
</body>

</html>
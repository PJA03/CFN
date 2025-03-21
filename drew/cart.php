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
$user_id = $_SESSION['user_id'];

// Fetch cart data
$query = "SELECT c.productID, SUM(c.quantity) as quantity, c.price, p.product_name, p.product_image 
          FROM tb_cart c
          JOIN tb_products p ON c.productID = p.productID
          WHERE c.user_id = ?
          GROUP BY c.productID, c.price, p.product_name, p.product_image";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
if (!$stmt->execute()) {
    die("Query failed: " . $stmt->error);
}
$result = $stmt->get_result();

$cart_items = [];
while ($row = $result->fetch_assoc()) {
    $cart_items[] = $row;
}

// Calculate prices
$netPrice = 0;
$totalVAT = 0;
$totalPrice = 0;
if (!empty($cart_items)) {
    foreach ($cart_items as $item) {
        $itemTotalPrice = floatval($item['price']) * intval($item['quantity']);
        $itemVAT = $itemTotalPrice * 0.12;
        $itemNetPrice = $itemTotalPrice - $itemVAT;
        $netPrice += $itemNetPrice;
        $totalVAT += $itemVAT;
        $totalPrice += $itemTotalPrice;
    }
}
$_SESSION['total_price'] = $totalPrice;

// Add to cart
if (isset($_POST['add_to_cart'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "SELECT * FROM tb_products WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('i', $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    if ($product) {
        $query = "INSERT INTO tb_cart (user_id, productID, quantity, price) 
                  VALUES (?, ?, 1, ?) 
                  ON DUPLICATE KEY UPDATE quantity = quantity + 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("iid", $user_id, $product_id, $product['price']);
        if (!$stmt->execute()) {
            die("Insert failed: " . $stmt->error);
        }
    }
    header('Location: cart.php');
    exit();
}

// Quantity increase
if (isset($_POST['increase_qty'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "UPDATE tb_cart SET quantity = quantity + 1 WHERE user_id = ? AND productID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $product_id);
    if (!$stmt->execute()) {
        die("Update failed: " . $stmt->error);
    }
    header('Location: cart.php');
    exit();
}

// Quantity decrease
if (isset($_POST['decrease_qty'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "UPDATE tb_cart SET quantity = quantity - 1 WHERE user_id = ? AND productID = ? AND quantity > 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $product_id);
    if (!$stmt->execute()) {
        die("Update failed: " . $stmt->error);
    }
    header('Location: cart.php');
    exit();
}

// Remove item
if (isset($_POST['remove_item'])) {
    $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
    $query = "DELETE FROM tb_cart WHERE user_id = ? AND productID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ii", $user_id, $product_id);
    if (!$stmt->execute()) {
        die("Delete failed: " . $stmt->error);
    }
    header('Location: cart.php');
    exit();
}

// Checkout
if (isset($_POST['confirm_checkout'])) {
    $_SESSION['order'] = $cart_items;
    header('Location: select_payment.php');
    exit();
}

// Clear cart
if (isset($_POST['cancel_cart'])) {
    $query = "DELETE FROM tb_cart WHERE user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $user_id);
    if (!$stmt->execute()) {
        die("Clear cart failed: " . $stmt->error);
    }
    header("Location: cart.php");
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="cart.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping Cart</title>
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
                <a href="../User_Profile_Page/UserProfile.php"><i class="far fa-user-circle fa-2x icon-profile"></i></a>
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
                            <th class="product-header">Product</th>
                            <th class="qty-header">Qty</th>
                            <th class="price-header">Price</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($cart_items)): ?>
                            <?php foreach ($cart_items as $item): ?>
                                <tr data-product-id="<?php echo htmlspecialchars($item['productID']); ?>">
                                    <td class="product-info">
                                        <img src="<?php echo htmlspecialchars('../e-com/' . $item['product_image'], ENT_QUOTES, 'UTF-8'); ?>" 
                                             alt="<?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?>" 
                                             class="product-image">
                                        <span><?php echo htmlspecialchars($item['product_name'], ENT_QUOTES, 'UTF-8'); ?></span>
                                    </td>
                                    <td class="product-quantity">
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="product_id" value="<?php echo $item['productID']; ?>">
                                            <button type="submit" name="decrease_qty" class="quantity-btn minus-btn">-</button>
                                        </form>
                                        <span class="quantity-value"><?php echo $item['quantity']; ?></span>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="product_id" value="<?php echo $item['productID']; ?>">
                                            <button type="submit" name="increase_qty" class="quantity-btn plus-btn">+</button>
                                        </form>
                                        <form method="POST" style="display: inline;">
                                            <input type="hidden" name="product_id" value="<?php echo $item['productID']; ?>">
                                            <button type="submit" name="remove_item" class="delete-btn"><i class="fas fa-trash" style="color: red;"></i></button>
                                        </form>
                                    </td>
                                    <td class="product-price">₱<span class="price-value"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="3">Your cart is empty</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <div class="cart-summary">
                    <div class="price-breakdown">
                        <p>Net Price: <span id="net-price">₱<?php echo number_format($netPrice, 2); ?></span></p>
                        <p>VAT (12%): <span id="vat">₱<?php echo number_format($totalVAT, 2); ?></span></p>
                        <p>Total Price: <strong id="total-price">₱<?php echo number_format($totalPrice, 2); ?></strong></p>
                    </div>
                    <div class="delivery-note">*Delivery fee is calculated by our third-party carrier.</div>
                </div>
            </div>

            <div class="cart-actions">
                <a href="#" class="btn checkout-btn" data-bs-toggle="modal" data-bs-target="#checkoutModal">Proceed to Payment</a>
                <button class="btn cancel-btn" id="cancel-cart-btn" data-bs-toggle="modal" data-bs-target="#cancelModal">Clear Cart</button>
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

    <div class="modal fade" id="checkoutModal" tabindex="-1" aria-labelledby="checkoutModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="checkoutModalLabel">Confirm Checkout</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to proceed to payment?</div>
                <div class="modal-footer">
                    <form id="checkoutForm" method="POST">
                        <input type="hidden" name="confirm_checkout" value="1">
                        <button type="submit" class="btn btn-success" id="confirmCheckout">Yes</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">Clear Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">Are you sure you want to clear your cart?</div>
                <div class="modal-footer">
                    <form method="POST">
                        <input type="hidden" name="cancel_cart" value="1">
                        <button type="submit" class="btn btn-danger" id="confirm-cancel-btn">Yes, Clear</button>
                    </form>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
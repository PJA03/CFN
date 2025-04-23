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
    //TODO:add you are not yet registered alert
    header('Location: ../Registration_Page/registration.php');
    exit();
}

$username = $_SESSION['username'];

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax']) && $_POST['ajax'] === 'true') {
    header('Content-Type: application/json');
    $response = ['success' => false];
    
    // Quantity increase
    if (isset($_POST['increase_qty']) && isset($_POST['product_id'])) {
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
        $query = "UPDATE tb_cart SET quantity = quantity + 1 WHERE user_id = ? AND productID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $product_id);
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['message'] = "Update failed: " . $stmt->error;
        }
    }
    
    // Quantity decrease
    elseif (isset($_POST['decrease_qty']) && isset($_POST['product_id'])) {
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
        $query = "UPDATE tb_cart SET quantity = quantity - 1 WHERE user_id = ? AND productID = ? AND quantity > 1";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $product_id);
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['message'] = "Update failed: " . $stmt->error;
        }
    }
    
    // Remove item
    elseif (isset($_POST['remove_item']) && isset($_POST['product_id'])) {
        $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
        $query = "DELETE FROM tb_cart WHERE user_id = ? AND productID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ii", $user_id, $product_id);
        if ($stmt->execute()) {
            $response['success'] = true;
        } else {
            $response['message'] = "Delete failed: " . $stmt->error;
        }
    }
    
    // If successful, get updated cart data
    if ($response['success']) {
        // Get updated item quantity
        if (isset($_POST['product_id']) && !isset($_POST['remove_item'])) {
            $product_id = filter_input(INPUT_POST, 'product_id', FILTER_SANITIZE_NUMBER_INT);
            $query = "SELECT quantity, price FROM tb_cart WHERE user_id = ? AND productID = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ii", $user_id, $product_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                if ($row = $result->fetch_assoc()) {
                    $response['quantity'] = (int)$row['quantity'];
                    $response['item_total'] = (float)$row['price'] * (int)$row['quantity'];
                }
            }
        }
        
        // Check if cart is empty
        $query = "SELECT COUNT(*) as count FROM tb_cart WHERE user_id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("i", $user_id);
        if ($stmt->execute()) {
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $response['cart_empty'] = ($row['count'] == 0);
        }
        
        // Calculate totals
        if (!$response['cart_empty']) {
            $query = "SELECT c.productID, c.variant_id, SUM(c.quantity) as quantity, c.price, p.product_name, p.product_image, p.category,
                             (SELECT v.variant_name FROM tb_productvariants v 
                              WHERE v.productID = c.productID AND v.variant_id = c.variant_id LIMIT 1) as variant_name
                      FROM tb_cart c
                      JOIN tb_products p ON c.productID = p.productID
                      WHERE c.user_id = ?
                      GROUP BY c.productID, c.variant_id, c.price, p.product_name, p.product_image, p.category";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("i", $user_id);
            if ($stmt->execute()) {
                $result = $stmt->get_result();
                $cart_items = [];
                while ($row = $result->fetch_assoc()) {
                    $cart_items[] = $row;
                }
                
                // Calculate prices
                $netPrice = 0;
                $totalVAT = 0;
                $totalPrice = 0;
                $discountAmount = 0;
                
                foreach ($cart_items as $item) {
                    $itemTotalPrice = floatval($item['price']) * intval($item['quantity']);
                    $itemVAT = $itemTotalPrice * 0.12;
                    $itemNetPrice = $itemTotalPrice - $itemVAT;
                    $netPrice += $itemNetPrice;
                    $totalVAT += $itemVAT;
                    $totalPrice += $itemTotalPrice;
                }
                
                // Apply voucher discount if valid
                if (isset($_SESSION['voucher']) && isset($_SESSION['voucher']['discount'])) {
                    $voucherDiscount = $_SESSION['voucher']['discount'];
                    $discountAmount = ($totalPrice * $voucherDiscount) / 100;
                    $totalPrice -= $discountAmount;
                }
                
                $_SESSION['total_price'] = $totalPrice;
                
                $response['net_price'] = $netPrice;
                $response['total_vat'] = $totalVAT;
                $response['total_price'] = $totalPrice;
                $response['discount_amount'] = $discountAmount;
            }
        } else {
            // If cart is empty, set all values to 0
            $response['net_price'] = 0;
            $response['total_vat'] = 0;
            $response['total_price'] = 0;
            $response['discount_amount'] = 0;
            $_SESSION['total_price'] = 0;
        }
    }
    
    echo json_encode($response);
    exit;
}

// Original PHP code for non-AJAX requests continues below

// Fetch cart data
// Fetch cart data
$query = "SELECT c.productID, c.variant_id, SUM(c.quantity) as quantity, c.price, p.product_name, p.product_image, p.category,
                 (SELECT v.variant_name FROM tb_productvariants v 
                  WHERE v.productID = c.productID AND v.variant_id = c.variant_id LIMIT 1) as variant_name
          FROM tb_cart c
          JOIN tb_products p ON c.productID = p.productID
          WHERE c.user_id = ?
          GROUP BY c.productID, c.variant_id, c.price, p.product_name, p.product_image, p.category";
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

$voucherApplied = false;
$voucherCode = '';
$voucherDiscount = 0;
$voucherMessage = '';

// Handle voucher application
if (isset($_POST['apply_voucher'])) {
    $voucherCode = filter_input(INPUT_POST, 'voucher_code', FILTER_SANITIZE_STRING);
    
    // Clear previous messages
    unset($_SESSION['voucher_message']);
    unset($_SESSION['voucher_message_type']);

    if (empty($voucherCode)) {
        $_SESSION['voucher_message'] = "Please enter a voucher code";
        $_SESSION['voucher_message_type'] = 'error';
    } else {
        // Check if voucher exists and is valid
        $query = "SELECT * FROM tb_vouchers WHERE code = ? AND valid_until >= CURDATE()";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $voucherCode);
        
        if (!$stmt->execute()) {
            $_SESSION['voucher_message'] = "Error checking voucher: " . $stmt->error;
            $_SESSION['voucher_message_type'] = 'error';
        } else {
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                $voucher = $result->fetch_assoc();
                $voucherApplied = true;
                $voucherDiscount = floatval($voucher['discount']);
                $_SESSION['voucher_message'] = "Voucher applied: " . $voucher['details'];
                $_SESSION['voucher_message_type'] = 'success';
                
                // Store voucher in session
                $_SESSION['voucher'] = [
                    'code' => $voucherCode,
                    'discount' => $voucherDiscount,
                    'details' => $voucher['details']
                ];
            } else {
                $_SESSION['voucher_message'] = "Invalid or expired voucher code: " . htmlspecialchars($voucherCode);
                $_SESSION['voucher_message_type'] = 'error';
                // Clear any previously applied voucher
                unset($_SESSION['voucher']);
            }
        }
    }
    
    // Redirect to prevent form resubmission
    header('Location: cart.php');
    exit();
}

// Check for voucher messages in session
$voucherMessage = '';
$voucherMessageType = '';
if (isset($_SESSION['voucher_message'])) {
    $voucherMessage = $_SESSION['voucher_message'];
    $voucherMessageType = $_SESSION['voucher_message_type'];
    // Clear the message after displaying it
    unset($_SESSION['voucher_message']);
    unset($_SESSION['voucher_message_type']);
}

// Check if there's a voucher in session
if (isset($_SESSION['voucher'])) {
    $voucherApplied = true;
    $voucherCode = $_SESSION['voucher']['code'];
    $voucherDiscount = $_SESSION['voucher']['discount'];
    $voucherMessage = "Voucher applied: " . $_SESSION['voucher']['details'];
}

// Remove voucher if requested
if (isset($_POST['remove_voucher'])) {
    unset($_SESSION['voucher']);
    $voucherApplied = false;
    $voucherDiscount = 0;
    $voucherMessage = "Voucher removed.";
    
    // Redirect to prevent form resubmission
    header('Location: cart.php');
    exit();
}

// Calculate prices
$netPrice = 0;
$totalVAT = 0;
$totalPrice = 0;
$discountAmount = 0;

if (!empty($cart_items)) {
    foreach ($cart_items as $item) {
        $itemTotalPrice = floatval($item['price']) * intval($item['quantity']);
        $itemVAT = $itemTotalPrice * 0.12;
        $itemNetPrice = $itemTotalPrice - $itemVAT;
        $netPrice += $itemNetPrice;
        $totalVAT += $itemVAT;
        $totalPrice += $itemTotalPrice;
    }
    
    // Apply voucher discount if valid
    if ($voucherApplied && $voucherDiscount > 0) {
        $discountAmount = ($totalPrice * $voucherDiscount) / 100;
        $totalPrice -= $discountAmount;
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

// Keep these non-AJAX handlers for fallback and initial page load
// Quantity increase (for non-JS browsers)
if (isset($_POST['increase_qty']) && !isset($_POST['ajax'])) {
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

// Quantity decrease (for non-JS browsers)
if (isset($_POST['decrease_qty']) && !isset($_POST['ajax'])) {
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

// Remove item (for non-JS browsers)
if (isset($_POST['remove_item']) && !isset($_POST['ajax'])) {
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.min.css">
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
    <section class="cart-container">
        <div class="cart-content">
            <table class="cart-table">
                <thead>
                    <tr>
                        <th class="product-header">Product</th>
                        <th class="qty-header">Qty</th>
                        <th class="qty-header"></th>
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
                                    <?php if (strtolower($item['category']) === 'perfume' && !empty($item['variant_name'])): ?>
                                        <br><small>Scent: <?php echo htmlspecialchars($item['variant_name'], ENT_QUOTES, 'UTF-8'); ?></small>
                                    <?php endif; ?>
                                </td>
                                <td class="product-quantity">
                                    <!-- Modified buttons for AJAX -->
                                    <button type="button" class="quantity-btn minus-btn" data-product-id="<?php echo $item['productID']; ?>">-</button>
                                    <span class="quantity-value"><?php echo $item['quantity']; ?></span>
                                    <button type="button" class="quantity-btn plus-btn" data-product-id="<?php echo $item['productID']; ?>">+</button>
                                    
                                    <td class="delete-column">
                                        <button type="button" class="delete-btn" data-product-id="<?php echo $item['productID']; ?>">
                                            <i class="fas fa-trash" style="color: red;"></i>
                                        </button>
                                    </td>
                                </td>
                                
                                <td class="product-price">₱<span class="price-value"><?php echo number_format($item['price'] * $item['quantity'], 2); ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="4">Your cart is empty</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Flex container for Voucher & Summary -->
            <div class="voucher-summary-container">
                <div class="voucher-section">
                    <h3>Apply Voucher</h3>
                    <?php if (!empty($voucherMessage)): ?>
    <div class="alert alert-<?php echo $voucherMessageType === 'success' ? 'success' : 'danger'; ?>">
        <?php echo htmlspecialchars($voucherMessage); ?>
    </div>
<?php endif; ?>
                    
                    <?php if (!$voucherApplied): ?>
                        <form method="POST" class="voucher-form">
                            <div class="input-group mb-3" style="max-width: 300px;"> 
                                <input type="text" name="voucher_code" class="form-control" placeholder="Enter voucher code" required>
                                <button type="submit" name="apply_voucher" class="btn btn-outline-secondary">Apply</button>
                            </div>
                        </form>
                    <?php else: ?>
                        <div class="applied-voucher">
                            <span class="badge bg-success">
                                <?php echo htmlspecialchars($voucherCode); ?> (<?php echo $voucherDiscount; ?>% off)
                            </span>
                            <form method="POST" class="d-inline">
                                <button type="submit" name="remove_voucher" class="btn btn-sm btn-outline-danger">Remove</button>
                            </form>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="cart-summary">
                    <div class="price-breakdown">
                        <p>Net Price: <span id="net-price">₱<?php echo number_format($netPrice, 2); ?></span></p>
                        <p>VAT (12%): <span id="vat">₱<?php echo number_format($totalVAT, 2); ?></span></p>
                        
                        <?php if ($voucherApplied && $discountAmount > 0): ?>
                            <p class="discount-row">
                                Discount (<?php echo $voucherDiscount; ?>%): 
                                <span id="discount" class="text-success">-₱<?php echo number_format($discountAmount, 2); ?></span>
                            </p>
                        <?php endif; ?>
                        
                        <p>Total Price: <strong id="total-price">₱<?php echo number_format($totalPrice, 2); ?></strong></p>
                    </div>
                    <div class="delivery-note">*Delivery fee is calculated by our third-party carrier.</div>
                </div>  
            </div>

            <div class="cart-actions">
                <?php if (!empty($cart_items)): ?>
                    <a href="#" class="btn checkout-btn" data-bs-toggle="modal" data-bs-target="#checkoutModal">Proceed to Payment</a>
                <?php else: ?>
                    <button class="btn checkout-btn" disabled style="background-color: #a7a7a7; color: #ffffff; cursor: not-allowed;">Proceed to Payment</button>
                <?php endif; ?>
                <button class="btn cancel-btn" id="cancel-cart-btn" data-bs-toggle="modal" data-bs-target="#cancelModal" <?php echo empty($cart_items) ? 'disabled style="background-color: #a7a7a7; color: #ffffff; cursor: not-allowed;"' : ''; ?>>Clear Cart</button>
            </div>
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

<!-- Modal -->
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

<!-- Checkout Confirmation Modal -->
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

<!-- Delete Cart Item Confirmation Modal -->
<div class="modal fade" id="deleteItemModal" tabindex="-1" aria-labelledby="deleteItemModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteItemModalLabel">Remove Item from Cart</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Are you sure you want to remove this item from your cart?
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteItemBtn">Remove</button>
            </div>
        </div>
    </div>
</div>

<!-- Clear Cart Confirmation Modal -->
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



<!-- AJAX Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Function to update cart quantity with AJAX
    function updateCartQuantity(productId, action) {
        const formData = new FormData();
        formData.append('product_id', productId);
        formData.append(action, '1');
        formData.append('ajax', 'true'); // Flag for AJAX request
        
        fetch('cart.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update quantity display
                const row = document.querySelector(`tr[data-product-id="${productId}"]`);
                if (row) {
                    // Update quantity
                    const quantityElement = row.querySelector('.quantity-value');
                    if (quantityElement && data.quantity !== undefined) {
                        quantityElement.textContent = data.quantity;
                    }
                    
                    // Update price
                    const priceElement = row.querySelector('.price-value');
                    if (priceElement && data.item_total !== undefined) {
                        priceElement.textContent = data.item_total.toFixed(2);
                    }
                    
                    // Remove row if quantity is zero or action is remove
                    if (data.quantity === 0 || action === 'remove_item') {
                        row.remove();
                    }
                }
                
                // Update totals
                document.getElementById('net-price').textContent = '₱' + data.net_price.toFixed(2);
                document.getElementById('vat').textContent = '₱' + data.total_vat.toFixed(2);
                document.getElementById('total-price').textContent = '₱' + data.total_price.toFixed(2);
                
                // Update discount if applicable
                if (document.getElementById('discount')) {
                    document.getElementById('discount').textContent = '-₱' + data.discount_amount.toFixed(2);
                }
                
                // If cart is empty, disable buttons and show message
                if (data.cart_empty) {
                    const tbody = document.querySelector('.cart-table tbody');
                    tbody.innerHTML = '<tr><td colspan="4">Your cart is empty</td></tr>';
                    
                    document.querySelector('.checkout-btn').setAttribute('disabled', true);
                    document.querySelector('.checkout-btn').style.backgroundColor = '#a7a7a7';
                    document.querySelector('.checkout-btn').style.cursor = 'not-allowed';                    document.getElementById('cancel-cart-btn').setAttribute('disabled', true);
                    document.getElementById('cancel-cart-btn').style.backgroundColor = '#a7a7a7';
                    document.getElementById('cancel-cart-btn').style.cursor = 'not-allowed';
                }
            } else {
                console.error('Error:', data.message || 'Unknown error occurred');
            }
        })
        .catch(error => {
            console.error('Fetch error:', error);
        });
    }

    // Event listeners for quantity buttons
    document.querySelectorAll('.plus-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            updateCartQuantity(productId, 'increase_qty');
        });
    });

    document.querySelectorAll('.minus-btn').forEach(button => {
        button.addEventListener('click', function() {
            const productId = this.getAttribute('data-product-id');
            updateCartQuantity(productId, 'decrease_qty');
        });
    });

// Add to existing script
let currentDeleteProductId = null;
const deleteItemModal = new bootstrap.Modal(document.getElementById('deleteItemModal'));

// Modify existing delete button event listeners
document.querySelectorAll('.delete-btn').forEach(button => {
    button.addEventListener('click', function(e) {
        e.preventDefault(); // Prevent default behavior
        e.stopPropagation(); // Stop event bubbling
        
        // Store the product ID when delete button is clicked
        currentDeleteProductId = this.getAttribute('data-product-id');
        
        // Show the confirmation modal
        deleteItemModal.show();
    });
});

// Add event listener to confirm delete button in modal
document.getElementById('confirmDeleteItemBtn').addEventListener('click', function() {
    if (currentDeleteProductId) {
        // Call existing update function with remove action
        updateCartQuantity(currentDeleteProductId, 'remove_item');
        
        // Close the modal
        deleteItemModal.hide();
        
        // Reset the current product ID
        currentDeleteProductId = null;
    }
});

// Close modal when clicking No/Cancel
document.querySelector('#deleteItemModal .btn-secondary').addEventListener('click', function() {
    currentDeleteProductId = null;
    deleteItemModal.hide();
});


    // Handle form submissions to prevent default behavior for AJAX actions
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
            // Skip forms that shouldn't be handled by AJAX
            if (this.id === 'checkoutForm' || 
                this.querySelector('[name="apply_voucher"]') || 
                this.querySelector('[name="remove_voucher"]') ||
                this.querySelector('[name="cancel_cart"]')) {
                return true;
            }
            
            e.preventDefault();
            
            const formData = new FormData(this);
            formData.append('ajax', 'true');
            
            fetch('cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    window.location.reload();
                } else {
                    alert(data.message || 'Operation failed');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        });
    });

    // Additional UI enhancements
    const checkoutBtn = document.querySelector('.checkout-btn');
    if (checkoutBtn && checkoutBtn.disabled) {
        checkoutBtn.style.backgroundColor = '#a7a7a7';
        checkoutBtn.style.cursor = 'not-allowed';
    }

    const cancelBtn = document.getElementById('cancel-cart-btn');
    if (cancelBtn && cancelBtn.disabled) {
        cancelBtn.style.backgroundColor = '#a7a7a7';
        cancelBtn.style.cursor = 'not-allowed';
    }
});

// Bootstrap tooltip initialization
var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
    return new bootstrap.Tooltip(tooltipTriggerEl);
});

// Center modals properly
document.addEventListener('DOMContentLoaded', function() {
    // Override Bootstrap's modal positioning
    const modals = document.querySelectorAll('.modal');
    
    // Function to center modal on show
    function centerModal() {
        const modalDialogs = document.querySelectorAll('.modal-dialog');
        modalDialogs.forEach(dialog => {
            // Reset any custom positioning to let Bootstrap handle it
            dialog.style.position = '';
            dialog.style.top = '';
            dialog.style.left = '';
            dialog.style.transform = '';
            // Just keep the animation
            dialog.style.animation = 'modalPop 0.4s ease-out forwards';
        });
    }
    
    // Apply to all modals
    modals.forEach(modal => {
        modal.addEventListener('show.bs.modal', centerModal);
    });
    
    // Initial call for any visible modals
    centerModal();
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
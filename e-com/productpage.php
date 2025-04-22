<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
$user = isset($_SESSION['email']) ? [
    'username' => $_SESSION['username'] ?? 'Guest',
    'email' => $_SESSION['email'],
    'first_name' => $_SESSION['first_name'] ?? '',
    'last_name' => $_SESSION['last_name'] ?? '',
    'contact_no' => $_SESSION['contact_no'] ?? '',
    'address' => $_SESSION['address'] ?? '',
    'profile_image' => $_SESSION['profile_image'] ?? '../Resources/default_profile.png',
] : ['username' => 'Guest'];

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID and variant ID from URL
$productID = isset($_GET['id']) ? intval($_GET['id']) : 0;
$variantID = isset($_GET['variant_id']) ? intval($_GET['variant_id']) : null;
$selected_variant_id = null; // Initialize as null

// Initialize product variables with default values
$product_name = "Product Not Found";
$product_desc = "No description available";
$category = "";
$product_image = "../Resources/cfn_logo.png";
$price = 0;
$stock = 0;

// Fetch product details
if ($productID > 0) {
    // Fetch default or specified variant
    $sql = "SELECT p.product_name, p.product_desc, p.category, p.product_image, v.variant_id, v.variant_name, v.price, v.stock 
            FROM tb_products p 
            JOIN tb_productvariants v ON p.productID = v.productID 
            WHERE p.productID = ?";
    if ($variantID) {
        $sql .= " AND v.variant_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ii", $productID, $variantID);
    } else {
        $sql .= " AND v.is_default = 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $productID);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $product_name = $row['product_name'] ?? "Product Not Found";
        $product_desc = !empty($row['product_desc']) ? $row['product_desc'] : "No description available";
        $category = $row['category'] ?? "";
        $product_image = !empty($row['product_image']) ? $row['product_image'] : "../Resources/cfn_logo.png";
        $price = $row['price'] ?? 0;
        $stock = $row['stock'] ?? 0;
        $selected_variant_id = $row['variant_id'] ?? null;
    } else {
        error_log("No product found for productID: $productID");
    }
    $stmt->close();

    // If $selected_variant_id is still null, fetch the default variant explicitly
    if ($selected_variant_id === null) {
        $default_sql = "SELECT variant_id FROM tb_productvariants WHERE productID = ? AND is_default = 1 LIMIT 1";
        $default_stmt = $conn->prepare($default_sql);
        $default_stmt->bind_param("i", $productID);
        $default_stmt->execute();
        $default_result = $default_stmt->get_result();
        if ($default_result->num_rows > 0) {
            $default_row = $default_result->fetch_assoc();
            $selected_variant_id = $default_row['variant_id'];
        }
        $default_stmt->close();
    }

    // Debug: Log category
    error_log("Product category: '$category'");

    // Fetch variants for perfumes only
    $variants = [];
    if (strtolower($category) === 'perfume') { // Case-insensitive check
        $variant_sql = "SELECT variant_id, variant_name, price, stock FROM tb_productvariants WHERE productID = ?";
        $variant_stmt = $conn->prepare($variant_sql);
        $variant_stmt->bind_param("i", $productID);
        $variant_stmt->execute();
        $variant_result = $variant_stmt->get_result();
        while ($variant = $variant_result->fetch_assoc()) {
            $variants[] = [
                'variant_id' => $variant['variant_id'],
                'variant_name' => $variant['variant_name'],
                'price' => $variant['price'],
                'stock' => $variant['stock']
            ];
        }
        $variant_stmt->close();
        // Debug: Log number of variants found
        error_log("Variants found for productID $productID: " . count($variants));
    } else {
        error_log("Not a perfume product, skipping variant fetch");
    }

    // Fetch similar products (same category)
    $similarProductsArray = [];
    if (!empty($category)) {
        $similar_sql = "SELECT p.productID, p.product_name, p.category, p.product_image, v.price 
                        FROM tb_products p 
                        JOIN tb_productvariants v ON p.productID = v.productID 
                        WHERE p.category = ? AND p.productID != ? AND v.is_default = 1 LIMIT 4";
        $similar_stmt = $conn->prepare($similar_sql);
        $similar_stmt->bind_param("si", $category, $productID);
        $similar_stmt->execute();
        $similar_result = $similar_stmt->get_result();
        while ($similar = $similar_result->fetch_assoc()) {
            $similarProductsArray[] = [
                'id' => $similar['productID'],
                'name' => $similar['product_name'],
                'category' => $similar['category'],
                'price' => "₱" . number_format($similar['price'], 2),
                'image' => !empty($similar['product_image']) ? $similar['product_image'] : "../Resources/cfn_logo.png"
            ];
        }
        $similar_stmt->close();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail - <?php echo htmlspecialchars($product_name); ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="productpage.css?v=<?php echo time(); ?>">
    <style>
        .variant-selector {
            margin: 15px 0;
        }
        .variant-selector label {
            font-family: 'Be Vietnam Pro', sans-serif;
            font-weight: bold;
            color: #1F4529;
            margin-bottom: 5px;
            display: block;
        }
        .variant-selector select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
            background-color: #fff;
            transition: border-color 0.3s;
        }
        .variant-selector select:focus {
            border-color: #1F4529;
            outline: none;
            box-shadow: 0 0 5px rgba(31, 69, 41, 0.3);
        }
        .variant-selector select option[disabled] {
            color: #999;
        }
        .product-price {
            font-size: 1.5rem;
            color: #1F4529;
            margin: 10px 0;
        }
        .out-of-stock {
            color: #dc3545;
            font-size: 0.9rem;
            margin-top: 5px;
        }
        .add-to-cart-btn:disabled {
            background-color: #6c757d;
            cursor: not-allowed;
        }
        .debug-message {
            color: #ff0000;
            font-size: 0.9rem;
            margin: 10px 0;
            background-color: #ffe6e6;
            padding: 5px;
            border-radius: 4px;
        }
    </style>
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

    <!-- Add to Cart Confirmation Modal -->
    <div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #1F4529; color: white;">
                    <h5 class="modal-title" id="addToCartModalLabel">Added to Cart</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-check-circle text-success me-3" style="font-size: 24px;"></i>
                        <span id="addToCartMessage"></span>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Continue Shopping</button>
                    <a href="../drew/cart.php" class="btn btn-success">View Cart</a>
                </div>
            </div>
        </div>
    </div>

    <div class="product-detail">
        <div class="product-image-container">
            <img src="<?php echo htmlspecialchars($product_image); ?>" alt="<?php echo htmlspecialchars($product_name); ?>" class="product-image">
        </div>
        <div class="product-info">
            <h1 class="product-name"><?php echo htmlspecialchars($product_name); ?></h1>
            <form action="add_to_cart.php" method="POST" id="addToCartForm">
                <input type="hidden" name="productID" value="<?php echo $productID; ?>">
                <?php if (strtolower($category) === 'perfume' && !empty($variants)): ?>
                    <div class="variant-selector">
                        <label for="variant">Select Scent</label>
                        <select name="variant_id" id="variant" required>
                            <?php foreach ($variants as $variant): ?>
                                <option value="<?php echo $variant['variant_id']; ?>" 
                                        data-price="<?php echo $variant['price']; ?>" 
                                        data-stock="<?php echo $variant['stock']; ?>" 
                                        <?php echo $variant['variant_id'] == $selected_variant_id ? 'selected' : ''; ?>
                                        <?php echo $variant['stock'] == 0 ? 'disabled' : ''; ?>>
                                    <?php echo htmlspecialchars($variant['variant_name']) . " - ₱" . number_format($variant['price'], 2) . ($variant['stock'] == 0 ? " (Out of Stock)" : ""); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?php if ($stock == 0): ?>
                            <p class="out-of-stock">This scent is currently out of stock.</p>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <input type="hidden" name="variant_id" value="<?php echo $selected_variant_id ?? 0; ?>">
                    <?php if ($category === 'perfume' && empty($variants)): ?>
                        <p class="debug-message">No variants found for this perfume.</p>
                    <?php endif; ?>
                <?php endif; ?>
                <div class="quantity-selector">
                    <button type="button" class="quantity-btn">-</button>
                    <span class="quantity-value">1</span>
                    <button type="button" class="quantity-btn">+</button>
                </div>
                <input type="hidden" id="quantity-input" name="quantity" value="1">
                <input type="hidden" id="price-input" name="price" value="<?php echo $price; ?>">
                <div class="product-price">₱<?php echo number_format($price, 2); ?></div>
                <div class="product-description">
                    <?php echo htmlspecialchars($product_desc); ?>
                </div>
                <button type="submit" class="add-to-cart-btn" <?php echo $stock == 0 ? 'disabled' : ''; ?>>Add to Cart</button>
            </form>
        </div>
    </div>

    <div class="similar-products">
        <h2 class="similar-title">SIMILAR TO THIS</h2>
        <div class="product-grid">
            <?php foreach ($similarProductsArray as $similar): ?>
                <div class="product-card" data-product-id="<?php echo $similar['id']; ?>">
                    <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($similar['image']); ?>');"></div>
                    <div class="card-info">
                        <h3 class="card-name"><?php echo htmlspecialchars($similar['name']); ?></h3>
                        <p class="card-category"><?php echo htmlspecialchars($similar['category']); ?></p>
                        <p class="card-price"><?php echo $similar['price']; ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if (empty($similarProductsArray)): ?>
                <?php for ($i = 0; $i < 4; $i++): ?>
                    <div class="product-card">
                        <div class="card-image"></div>
                        <div class="card-info">
                            <h3 class="card-name">PRODUCT NAME</h3>
                            <p class="card-category">Product Category</p>
                            <p class="card-price">₱₱₱</p>
                        </div>
                    </div>
                <?php endfor; ?>
            <?php endif; ?>
        </div>
    </div>

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
    <div class="modal fade" id="ModalTerms" tabindex="-1" aria-labelledby="ModalTermsLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #1F4529;">
                    <h5 class="modal-title" id="ModalTermsLabel" style="font-weight: bold;">CFN Naturale Terms and Conditions</h5>
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
    <div class="modal fade" id="ModalPrivacy" tabindex="-1" aria-labelledby="ModalPrivacyLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #1F4529;">
                    <h5 class="modal-title" id="ModalPrivacyLabel" style="font-weight: bold;">CFN Naturale Privacy Policy</h5>
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Variant selection (for perfumes only)
            const variantSelect = document.getElementById('variant');
            if (variantSelect) {
                const priceDisplay = document.querySelector('.product-price');
                const priceInput = document.getElementById('price-input');
                const addToCartBtn = document.querySelector('.add-to-cart-btn');
                const outOfStockMsg = document.querySelector('.out-of-stock');

                variantSelect.addEventListener('change', function() {
                    const selectedOption = this.options[this.selectedIndex];
                    const price = parseFloat(selectedOption.getAttribute('data-price'));
                    const stock = parseInt(selectedOption.getAttribute('data-stock'));
                    priceDisplay.textContent = `₱${price.toFixed(2)}`;
                    priceInput.value = price;
                    addToCartBtn.disabled = stock === 0;
                    if (outOfStockMsg) {
                        outOfStockMsg.style.display = stock === 0 ? 'block' : 'none';
                    }
                });
            }

            // Quantity control
            const minusBtn = document.querySelector('.quantity-btn:first-child');
            const plusBtn = document.querySelector('.quantity-btn:last-child');
            const quantityEl = document.querySelector('.quantity-value');
            const quantityInput = document.getElementById('quantity-input');
            
            minusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                let qty = parseInt(quantityEl.textContent);
                if (qty > 1) {
                    qty--;
                    quantityEl.textContent = qty;
                    quantityInput.value = qty;
                }
            });
            
            plusBtn.addEventListener('click', function(e) {
                e.preventDefault();
                let qty = parseInt(quantityEl.textContent);
                qty++;
                quantityEl.textContent = qty;
                quantityInput.value = qty;
            });

            // Add to cart form handler
            const addToCartForm = document.getElementById('addToCartForm');
            addToCartForm.addEventListener('submit', function(e) {
                e.preventDefault();
                const isGuest = <?php echo isset($_SESSION['email']) ? 'false' : 'true'; ?>;
                if (isGuest) {
                    window.location.href = '../Registration_Page/registration.php';
                    return;
                }

                const formData = new FormData(addToCartForm);
                // Debug: Log the form data being sent
                for (let [key, value] of formData.entries()) {
                    console.log(`${key}: ${value}`);
                }
                fetch('add_to_cart.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! Status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.success) {
                        const modalMessage = document.getElementById('addToCartMessage');
                        modalMessage.textContent = `<?php echo addslashes($product_name); ?> has been added to your cart!`;
                        const addToCartModal = new bootstrap.Modal(document.getElementById('addToCartModal'));
                        addToCartModal.show();
                        updateCartIndicator();
                    } else {
                        alert('Error adding to cart: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error adding to cart.');
                });
            });

            // Update cart indicator
            function updateCartIndicator() {
                fetch('get_cart.php')
                    .then(response => response.json())
                    .then(data => {
                        const cart = data.cart || [];
                        const totalItems = cart.reduce((sum, item) => sum + item.quantity, 0);
                        let cartIndicator = document.querySelector('.cart-indicator');
                        if (!cartIndicator) {
                            const cartIcon = document.querySelector('.fa-cart-shopping').parentElement;
                            cartIndicator = document.createElement('span');
                            cartIndicator.className = 'cart-indicator';
                            cartIcon.appendChild(cartIndicator);
                        }
                        cartIndicator.textContent = totalItems;
                        cartIndicator.style.display = totalItems > 0 ? 'flex' : 'none';
                    })
                    .catch(error => console.error('Error fetching cart:', error));
            }

            // Similar products click handler
            document.querySelectorAll('.product-card').forEach(card => {
                card.addEventListener('click', function() {
                    const productId = this.getAttribute('data-product-id');
                    window.location.href = `productpage.php?id=${productId}`;
                });
            });

            // Search form validation
            function validateSearch() {
                const searchInput = document.getElementById('searchBar').value.trim();
                return searchInput.length > 0;
            }
        });
    </script>
</body>
</html>
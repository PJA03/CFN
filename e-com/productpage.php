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
] : ['username' => 'Guest']; // Allow guests to browse without redirection

// Database connection
$servername = "localhost";
$username = "root";  // Adjust if needed
$password = "";      // Adjust if needed
$dbname = "db_cfn";

// Create connection using MySQLi
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from URL parameter
$productID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize product variables with default values
$product_name = "Product Not Found";
$product_desc = "No description available";
$category = "";
$product_image = "../Resources/default_product.jpg"; // Default image
$price = 0;

if ($productID > 0) {
    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("SELECT p.*, v.price 
                            FROM tb_products p 
                            JOIN tb_productvariants v ON p.productID = v.productID 
                            WHERE p.productID = ? AND v.is_default = 1");
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $product_name = $row['product_name'] ?? "Product Not Found";
        $product_desc = !empty($row['product_desc']) ? $row['product_desc'] : "No description available";
        $category = $row['category'] ?? "";
        $product_image = !empty($row['product_image']) ? $row['product_image'] : "../Resources/cfn_logo.png";
        $price = $row['price'] ?? 0;
    }
    $stmt->close();

    // Fetch similar products (same category)
    $similarProductsArray = [];
    if (!empty($category)) {
        $similarProductsQuery = $conn->prepare("SELECT p.productID, p.product_name, p.category, p.product_image, v.price 
            FROM tb_products p
            JOIN tb_productvariants v ON p.productID = v.productID
            WHERE p.category = ? 
            AND p.productID != ? 
            AND v.is_default = 1
            LIMIT 4");
        $similarProductsQuery->bind_param("si", $category, $productID);
        $similarProductsQuery->execute();
        $similarProducts = $similarProductsQuery->get_result();

        while ($similarProduct = $similarProducts->fetch_assoc()) {
            $similarProductsArray[] = [
                'id' => $similarProduct['productID'],
                'name' => $similarProduct['product_name'],
                'category' => $similarProduct['category'],
                'price' => "₱" . number_format($similarProduct['price'], 2),
                'image' => !empty($similarProduct['product_image']) ? $similarProduct['product_image'] : "../Resources/cfn_logo.png"
            ];
        }
        $similarProductsQuery->close();
    }
}

// Close database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Detail</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="productpage.css?v=<?php echo time(); ?>">
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
    
    <!-- Add to Cart Confirmation Modal -->
<div class="modal fade" id="addToCartModal" tabindex="-1" aria-labelledby="addToCartModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #1F4529; color: white;">
                <h5 class="modal-title" id="addToCartModalLabel">Added to Cart</h5>
                <button type="button" class="btn-close btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
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
                <input type="hidden" name="price" value="<?php echo $price; ?>">
                <div class="quantity-selector">
                    <button type="button" class="quantity-btn">-</button>
                    <span class="quantity-value">1</span>
                    <button type="button" class="quantity-btn">+</button>
                </div>
                <input type="hidden" id="quantity-input" name="quantity" value="1">
                <div class="product-price">₱<?php echo number_format($price, 2); ?></div>
                <div class="product-description">
                    <?php echo htmlspecialchars($product_desc); ?>
                </div>
                
                <button type="submit" class="add-to-cart-btn">Add to Cart</button>
            </form>
        </div>
    </div>
    
    <div class="similar-products">
        <h2 class="similar-title">SIMILAR TO THIS</h2>
        
        <div class="product-grid">
            <?php
            if (isset($similarProducts) && $similarProducts->num_rows > 0) {
                while ($similarProduct = $similarProducts->fetch_assoc()) {
                    $similarImage = !empty($similarProduct['product_image']) ? $similarProduct['product_image'] : "../Resources/cfn_logo.png";
                    ?>
                    <div class="product-card" data-product-id="<?php echo $similarProduct['productID']; ?>">
                        <div class="card-image" style="background-image: url('<?php echo htmlspecialchars($similarImage, ENT_QUOTES, 'UTF-8'); ?>'); background-size: cover; background-position: center;"></div>
                        <div class="card-info">
                            <h3 class="card-name"><?php echo htmlspecialchars($similarProduct['product_name'], ENT_QUOTES, 'UTF-8'); ?></h3>
                            <p class="card-category"><?php echo htmlspecialchars($similarProduct['category'], ENT_QUOTES, 'UTF-8'); ?></p>
                            <p class="card-price">₱<?php echo number_format($similarProduct['price'], 2); ?></p>
                        </div>
                    </div>
                    <?php
                }
            } else {
                // Display placeholder cards if no similar products found
                for ($i = 0; $i < 4; $i++) {
                    ?>
                    <div class="product-card">
                        <div class="card-image"></div>
                        <div class="card-info">
                            <h3 class="card-name">PRODUCT NAME</h3>
                            <p class="card-category">Product Category</p>
                            <p class="card-price">₱₱₱</p>
                        </div>
                    </div>
                    <?php
                }
            }
            ?>
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
            &copy; COSMETICAS 2024
        </div>
</footer>

       <!-- Modal -->
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
</div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const similarProducts = <?php echo json_encode($similarProductsArray); ?>;
    </script>
    <script>
    document.addEventListener('DOMContentLoaded', function() {
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
            e.preventDefault(); // Prevent default submission
            
            // Check if user is logged in (not guest)
            const isGuest = <?php echo isset($_SESSION['email']) ? 'false' : 'true'; ?>;
            
            if (isGuest) {
                // Redirect to registration page for guests
                window.location.href = '../Registration_Page/registration.php';
                return;
            }
        
            const productID = <?php echo $productID; ?>;
            const productName = "<?php echo addslashes($product_name); ?>";
            const quantity = parseInt(quantityEl.textContent);
        
            // Check if the product is already in the cart
            fetch(`check_cart.php?productID=${productID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.in_cart) {
                        if (confirmAddMore) {
                            submitForm(quantity, productName);
                        }
                    } else {
                        submitForm(quantity, productName);
                    }
                })
                .catch(error => {
                    console.error('Error checking cart:', error);
                    submitForm(quantity, productName);
                });
        });

        function submitForm(quantity, productName) {
    const formData = new FormData(addToCartForm);
    
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
            // Show success modal
            const modalMessage = document.getElementById('addToCartMessage');
            modalMessage.textContent = `${productName} has been added to your cart successfully!`;
            const addToCartModal = new bootstrap.Modal(document.getElementById('addToCartModal'));
            addToCartModal.show();
            
            // Update cart indicator
            updateCartIndicator();
        } else {
            if (data.message === 'User not logged in') {
                // This should theoretically never happen since we check before
                window.location.href = '../Registration_Page/registration.php';
            } else {
                alert('Error adding to cart: ' + data.message);
            }
        }
    })
}
        // Function to update cart indicator
        function updateCartIndicator() {
            let cartIndicator = document.querySelector('.cart-indicator');
            
            if (!cartIndicator) {
                const cartIcon = document.querySelector('.cart-icon');
                cartIndicator = document.createElement('span');
                cartIndicator.className = 'cart-indicator';
                cartIcon.appendChild(cartIndicator);
            }
            
            // Get cart items from the server
            fetch('get_cart.php')
                .then(response => response.json())
                .then(data => {
                    const cart = data.cart || [];
                    const totalItems = cart.reduce((total, item) => total + item.quantity, 0);
                    
                    // Update localStorage to sync with server-side cart
                    localStorage.setItem('shoppingCart', JSON.stringify(cart));
                    
                    if (totalItems > 0) {
                        cartIndicator.textContent = totalItems;
                        cartIndicator.style.display = 'flex';
                    } else {
                        cartIndicator.style.display = 'none';
                    }
                })
                .catch(error => {
                    console.error('Error fetching cart:', error);
                });
        }
        
        // Load similar products dynamically
        function loadSimilarProducts() {
            const productGrid = document.querySelector('.product-grid');
            
            if (typeof similarProducts !== 'undefined' && similarProducts.length > 0) {
                productGrid.innerHTML = ''; // Clear existing products
                
                similarProducts.forEach(product => {
                    const productCard = document.createElement('div');
                    productCard.className = 'product-card';
                    productCard.dataset.productId = product.id;
                    
                    productCard.innerHTML = `
                        <div class="card-image" style="background-image: url('${product.image}'); background-size: cover; background-position: center;"></div>
                        <div class="card-info">
                            <h3 class="card-name">${product.name}</h3>
                            <p class="card-category">${product.category}</p>
                            <p class="card-price">${product.price}</p>
                        </div>
                    `;
                    
                    productCard.addEventListener('click', function() {
                        window.location.href = `productpage.php?id=${product.id}`;
                    });
                    
                    productGrid.appendChild(productCard);
                });
            }
        }
        
        // Call function to load similar products
        loadSimilarProducts();
        
        // Initialize cart indicator on page load
        updateCartIndicator();
        
        // Add CSS for toast notification and cart indicator
        const style = document.createElement('style');
        style.textContent = `
            .cart-toast {
                position: fixed;
                bottom: 20px;
                right: 20px;
                background-color: #1F4529;
                color: white;
                padding: 12px 20px;
                border-radius: 4px;
                box-shadow: 0 4px 8px rgba(0,0,0,0.1);
                transform: translateY(100px);
                opacity: 0;
                transition: all 0.3s ease;
                z-index: 1000;
            }
            
            .show-toast {
                transform: translateY(0);
                opacity: 1;
            }
            
            .toast-content {
                display: flex;
                align-items: center;
                gap: 10px;
            }
            
            .cart-indicator {
                position: absolute;
                top: -5px;
                right: -5px;
                background-color: #ff7f50;
                color: white;
                width: 18px;
                height: 18px;
                border-radius: 50%;
                display: flex;
                justify-content: center;
                align-items: center;
                font-size: 10px;
                font-weight: bold;
            }
            
            .cart-icon {
                position: relative;
                margin-left: 20px;
            }
        `;
        document.head.appendChild(style);
    });
    </script>
</body>
</html>

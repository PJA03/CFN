<?php
// Start the session (if needed)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Registration_Page/registration.php');
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";  // Adjust if needed
$password = "";      // Adjust if needed
$dbname = "db_cfn";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get product ID from URL parameter
$productID = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Initialize variables with default values
$product_name = "Product Not Found";
$product_desc = "No description available";
$brand = "";
$category = "";
$product_image = "product-image.jpg"; // Default image
$price = 0;

if ($productID > 0) {
    // Query to get product details
    $sql = "SELECT p.*, v.price 
            FROM tb_products p 
            JOIN tb_productvariants v ON p.productID = v.productID 
            WHERE p.productID = $productID AND v.is_default = 1";
    
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $product_name = $row['product_name'];
        $product_desc = $row['product_desc'] ?: "No description available";
        $category = $row['category'];
        $product_image = !empty($row['product_image']) ? $row['product_image'] : "../Resources/cfn_logo.png";        
        $price = $row['price'];
    }
    
    // Get similar products (same category)
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

    $similarProductsArray = [];
    if ($similarProducts->num_rows > 0) {
        while ($similarProduct = $similarProducts->fetch_assoc()) {
            $similarProductsArray[] = [
                'id' => $similarProduct['productID'],
                'name' => $similarProduct['product_name'],
                'category' => $similarProduct['category'],
                'price' => "₱" . number_format($similarProduct['price'], 2),
                'image' => !empty($similarProduct['product_image']) ? $similarProduct['product_image'] : "../Resources/cfn_logo.png"
            ];
        }
    }
}
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
    <link rel="stylesheet" href="productpage.css">
</head>
<body>
    <header>
        <div class="logo">
            <img src="../Home_Page/cfn_logo2.png" alt="Logo" class="logo-image" />
        </div>
        <div class="navbar">
            <input type="text" class="search-bar" placeholder="Search Product" />
            <div class="icons">
                <a href="../User_Profile_Page/UserProfile.php">
                    <i class="far fa-user-circle fa-2x icon-profile"></i>
                </a>
                <a href="../drew/cart.php" class="cart-icon">
                    <i class="fas fa-shopping-cart fa-2x"></i>
                </a>
                <i class="fas fa-bars burger-menu"></i>
            </div>
        </div>
    </header>
    
    <div class="product-category">
        <i class="fas fa-angle-right"></i> PRODUCT CATEGORY
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
        <div class="footer-content">
            <img src="../Resources/cfn_logo.png" alt="Naturale Logo" class="footer-logo">
            
            <div class="footer-links">
                <a href="#">ABOUT US</a>
                <a href="#">PRODUCTS</a>
                <a href="#">LOGIN</a>
                <a href="#">SIGN UP</a>
            </div>
            
            <div class="social-links">
                <p>SOCIALS</p>
                <div class="social-icons">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                </div>
            </div>
        </div>
        
        <div class="copyright">
            © COSMETICAS 2024
        </div>
    </footer>
    
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
            e.preventDefault(); // Prevent default submission until we check the cart

            const productID = <?php echo $productID; ?>;
            const productName = "<?php echo addslashes($product_name); ?>";
            const quantity = parseInt(quantityEl.textContent);

            // Check if the product is already in the cart
            fetch(`check_cart.php?productID=${productID}`)
                .then(response => response.json())
                .then(data => {
                    if (data.in_cart) {
                        // Product is already in the cart, show confirmation prompt
                        const confirmAddMore = confirm(`${productName} is already in your cart (Quantity: ${data.quantity}). Do you want to add ${quantity} more?`);
                        if (confirmAddMore) {
                            // User confirmed, submit the form
                            submitForm(quantity, productName);
                        }
                    } else {
                        // Product is not in the cart, submit the form
                        submitForm(quantity, productName);
                    }
                })
                .catch(error => {
                    console.error('Error checking cart:', error);
                    // In case of error, proceed with adding to cart
                    submitForm(quantity, productName);
                });
        });

        // Function to submit the form and show toast notification
        function submitForm(quantity, productName) {
            const formData = new FormData(addToCartForm);
            
            fetch('add_to_cart.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                // Check if the response is OK
                if (!response.ok) {
                    throw new Error(`HTTP error! Status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show toast notification
                    const confirmationMessage = data.message;
                    
                    const toast = document.createElement('div');
                    toast.className = 'cart-toast';
                    toast.innerHTML = `
                        <div class="toast-content">
                            <i class="fas fa-check-circle"></i>
                            <span>${confirmationMessage}</span>
                        </div>
                    `;
                    document.body.appendChild(toast);
                    
                    // Show toast
                    setTimeout(() => {
                        toast.classList.add('show-toast');
                    }, 100);
                    
                    // Hide toast after 3 seconds
                    setTimeout(() => {
                        toast.classList.remove('show-toast');
                        setTimeout(() => {
                            document.body.removeChild(toast);
                        }, 300);
                    }, 3000);
                    
                    // Update cart indicator
                    updateCartIndicator();
                } else {
                    // Handle specific error messages
                    if (data.message === 'User not logged in') {
                        alert('Please log in to add items to your cart.');
                        window.location.href = '../Registration_Page/registration.php';
                    } else {
                        alert('Error adding to cart: ' + data.message);
                    }
                }
            })
            .catch(error => {
                console.error('Error adding to cart:', error);
                alert('Failed to add to cart. Please try again.');
            });
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
<?php

// Start the session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
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
    $user = ['username' => 'Guest'];
}

// Include database connection
include '../conn.php';

// Get search and category filters from the URL
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$category = isset($_GET['category']) ? $conn->real_escape_string($_GET['category']) : '';

// Base SQL query
$sql = "SELECT p.productID, p.product_name, p.category, p.product_image, v.price, v.stock
        FROM tb_products p
        JOIN tb_productvariants v ON p.productID = v.productID
        WHERE v.is_default = 1";

// Apply search filter
if (!empty($search)) {
    $sql .= " AND (p.product_name LIKE '%$search%' OR p.category LIKE '%$search%')";
}

// Apply category filter
if (!empty($category)) {
    $sql .= " AND p.category = '$category'";
}

// Execute query
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Store</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="ProductScroll.css">
    <link rel="stylesheet" href="product-scroll-styles.css">
</head>
<body>
    <header>
        <div class="logo">
            <a href = "../Home_Page/home.php"><img src="../Home_Page/cfn_logo2.png" alt="Logo" class="logo-image"/></a>
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

    <div class="container mt-4">
        <div class="filter-container">
            <i class="fas fa-filter"></i>
            <span>Search Filter</span>
            <select class="category-dropdown" id="categoryFilter">
                <option value="">All Categories</option>
                <option value="skin" <?php echo (isset($_GET['category']) && $_GET['category'] === 'skin') ? 'selected' : ''; ?>>Skin</option>
                <option value="hair" <?php echo (isset($_GET['category']) && $_GET['category'] === 'hair') ? 'selected' : ''; ?>>Hair</option>
                <option value="face" <?php echo (isset($_GET['category']) && $_GET['category'] === 'face') ? 'selected' : ''; ?>>Face</option>
                <option value="perfume" <?php echo (isset($_GET['category']) && $_GET['category'] === 'perfume') ? 'selected' : ''; ?>>Perfume</option>
            </select>
        </div>

        <div class="product-grid" id="product-grid">
    <?php
    define('BASE_PATH', '/CFN-main/');

    if (!$result) {
        echo "<p>Error fetching products: " . $conn->error . "</p>";
    } elseif ($result->num_rows > 0) {
        while ($product = $result->fetch_assoc()) {
            $stockStatus = ($product['stock'] >= 50) ? "In Stock" : (($product['stock'] > 0) ? "Low Stock" : "Out of Stock");
            $productImage = !empty($product['product_image']) ? str_replace('uploads/', '', $product['product_image']) : '';
            $imgSrc = !empty($productImage) ? BASE_PATH . "e-com/uploads/" . $productImage : BASE_PATH . "e-com/images/cfn_logo.png";
            $fallbackImgSrc = BASE_PATH . "e-com/images/cfn_logo.png";
    ?>
        <a href="../e-com/productpage.php?id=<?= $product['productID']; ?>" class="product-card-link">
            <div class="product-card"
                 data-product-id="<?= $product['productID']; ?>"
                 data-price="<?= $product['price']; ?>"
                 data-stock="<?= $product['stock']; ?>"
                 data-category="<?= strtolower($product['category']); ?>">
                <img src="<?= $imgSrc; ?>" alt="Product Image" 
                     data-fallback="<?= $fallbackImgSrc; ?>" 
                     onload="this.removeAttribute('data-fallback');" 
                     onerror="if(this.src !== this.getAttribute('data-fallback')) { this.src = this.getAttribute('data-fallback'); } else { console.log('Fallback failed: <?= $fallbackImgSrc; ?>'); }">
                <h5><?= htmlspecialchars($product['product_name']); ?></h5>
                <p>₱<?= number_format($product['price'], 2); ?> - <?= $stockStatus; ?></p>
                <div class="variant-options">
                    <?php
                    $prodID = $product['productID'];
                    $variant_sql = "SELECT variant_id, variant_name, price, stock FROM tb_productvariants WHERE productID = ?";
                    $variant_stmt = $conn->prepare($variant_sql);
                    $variant_stmt->bind_param("i", $prodID);
                    $variant_stmt->execute();
                    $variant_result = $variant_stmt->get_result();
                    if ($variant_result && $variant_result->num_rows > 1) {
                        echo '<select class="variant-select" name="variant_' . $prodID . '" onclick="event.stopPropagation();">';
                        while ($variant = $variant_result->fetch_assoc()) {
                            $variantStock = ($variant['stock'] > 0) ? "In Stock" : "Out of Stock";
                            $selected = ($variant['price'] == $product['price'] && $variant['stock'] == $product['stock']) ? 'selected' : '';
                            echo "<option value='{$variant['variant_id']}' data-price='{$variant['price']}' data-stock='{$variant['stock']}' $selected>";
                            echo htmlspecialchars($variant['variant_name']) . " - ₱" . number_format($variant['price'], 2) . " ($variantStock)";
                            echo "</option>";
                        }
                        echo '</select>';
                    }
                    ?>
                </div>
            </div>
        </a>
    <?php
        }
    } else {
        echo "<p>No products found.</p>";
    }
    $conn->close();
    ?>
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
        // Variant selection handling
        document.querySelectorAll('.variant-select').forEach(select => {
            select.addEventListener('change', function() {
                const selectedOption = this.options[this.selectedIndex];
                const price = selectedOption.getAttribute('data-price');
                const stock = selectedOption.getAttribute('data-stock');
                const card = this.closest('.product-card');
                const priceStockText = card.querySelector('p');
                priceStockText.textContent = `₱${parseFloat(price).toFixed(2)} - ${stock > 0 ? 'In Stock' : 'Out of Stock'}`;
            });
        });

        // Filter products by category and search
        function filterProducts() {
            const category = document.getElementById('categoryFilter').value.toLowerCase();
            const searchText = document.getElementById('searchBar').value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            const noProductsMessage = document.getElementById('no-products-message');
            let visibleProducts = 0;

            products.forEach(product => {
                const productCategory = product.getAttribute('data-category');
                const productName = product.querySelector('h5').textContent.toLowerCase();
                const matchesCategory = !category || productCategory === category;
                const matchesSearch = productName.includes(searchText);

                if (matchesCategory && matchesSearch) {
                    product.style.display = '';
                    visibleProducts++;
                } else {
                    product.style.display = 'none';
                }
            });

            noProductsMessage.style.display = (visibleProducts === 0 && category) ? 'block' : 'none';
        }

        document.getElementById('searchBar').addEventListener('keypress', function(event) {
            if (event.key === "Enter") {
                event.preventDefault();
                const searchText = this.value.trim();
                const urlParams = new URLSearchParams(window.location.search);
                urlParams.set('search', searchText);
                window.location.search = urlParams.toString();
            }
        });

        document.getElementById('categoryFilter').addEventListener('change', function() {
    filterProducts();
    const newCategory = this.value;
    const url = new URL(window.location);
    if (newCategory) {
        url.searchParams.set('category', newCategory);
    } else {
        url.searchParams.delete('category');
    }
    window.history.pushState({}, '', url);
});

document.getElementById('categoryFilter').addEventListener('change', function() {
    const selectedCategory = this.value;
    const url = new URL(window.location);

    // Set category in the URL
    if (selectedCategory) {
        url.searchParams.set('category', selectedCategory);
    } else {
        url.searchParams.delete('category');
    }

    // Remove search query and clear the search bar
    url.searchParams.delete('search');
    document.getElementById('searchBar').value = "";

    // Reload page with updated filters
    window.location.href = url.toString();
});


        document.getElementById('searchBar').addEventListener('input', filterProducts);

        window.onload = function() {
            filterProducts();
        };
    </script>
</body>
</html>

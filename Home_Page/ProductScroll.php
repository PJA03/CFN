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
    <style>
        /* Ensure the page takes up full height */
        html, body {
            height: 100%;
            margin: 0;
        }

        /* Flexbox to push footer to bottom */
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .container {
            flex: 1 0 auto; /* Grow to fill space, but don't shrink */
        }

        footer {
            flex-shrink: 0; /* Prevent footer from shrinking */
        }

        /* No products message styling */
        #no-products-message {
            display: none; /* Hidden by default */
            text-align: center;
            margin-top: 20px;
            font-size: 1.2rem;
            color: #FF8666; /* Match your theme */
            font-family: "Bebas Neue", serif;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="cfn_logo2.png" alt="Logo" class="logo-image" />
        </div>
        <div class="navbar">
            <input type="text" class="search-bar" id="searchBar" placeholder="Search Product" />
            <div class="icons">
                <a href="../Home_Page/home.php"><i class="fa-solid fa-house"></i></a>
                <i class="fas fa-shopping-cart"></i>
                <a href="../User_Profile_Page/UserProfile.php">
                    <i class="far fa-user-circle fa-2x icon-profile"></i>
                </a>
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
        define('BASE_PATH', '/CFN/');

        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "db_cfn";

        $conn = new mysqli($servername, $username, $password, $dbname);
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        $sql = "SELECT p.productID, p.product_name, p.category, p.product_image, v.price, v.stock, 
                       (SELECT COUNT(*) FROM tb_productvariants WHERE productID = p.productID) as variant_count
                FROM tb_products p
                JOIN tb_productvariants v ON p.productID = v.productID
                WHERE v.is_default = 1";
        
        $result = $conn->query($sql);

        if (!$result) {
            echo "<p>Error fetching products: " . $conn->error . "</p>";
        } elseif ($result->num_rows > 0) {
            while ($product = $result->fetch_assoc()) {
                $stockStatus = ($product['stock'] >= 50) ? "In Stock" : (($product['stock'] > 0) ? "Low Stock" : "Out of Stock");
                $productImage = !empty($product['product_image']) ? str_replace('uploads/', '', $product['product_image']) : '';
                $imgSrc = !empty($productImage) ? BASE_PATH . "e-com/uploads/" . $productImage : BASE_PATH . "e-com/images/cfn_logo.png";
                $fallbackImgSrc = BASE_PATH . "e-com/images/cfn_logo.png";
                ?>
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
                            echo '<select class="variant-select" name="variant_' . $prodID . '">';
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
                    <button class="add-to-cart" data-product-id="<?= $product['productID']; ?>" onclick="window.location.href='../e-com/productpage.php?id=<?= $product['productID']; ?>'">View Product</button>
                </div>
                <?php
            }
        } else {
            echo "<p>No products found in the database.</p>";
        }
        $conn->close();
        ?>
        </div>
        <div id="no-products-message">No Product is under this Category</div>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <img src="cfn_logo.png" alt="Naturale Logo" class="footer-logo">
            </div>
            <div class="footer-right">
                <ul class="footer-nav">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Terms and Conditions</a></li>
                    <li><a href="#">Products</a></li>
                </ul>
            </div>
            <div class="social-icons">
                <p>SOCIALS</p>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>            
        </div>
        <div class="footer-center">
            © COSMETICAS 2024
        </div>
    </footer>

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

            // Show/hide "No Product" message
            noProductsMessage.style.display = (visibleProducts === 0 && category) ? 'block' : 'none';
        }

        // Add event listeners for filters
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
        document.getElementById('searchBar').addEventListener('input', filterProducts);

        // Apply filter on page load based on URL parameter
        window.onload = function() {
            filterProducts(); // Trigger filter to respect pre-selected category
        };
    </script>
</body>
</html>
<?php
// Debugging: Check if search value is received
if (isset($_GET['search'])) {
    echo "Search term received: " . htmlspecialchars($_GET['search']);
} else {
    echo "No search term received.";
}



// Start the session (if needed)
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
    $user = ['username' => 'Guest']; // Default for non-logged-in users
}


// Include database connection
include '../conn.php'; 

// Check if search term exists in the URL
$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Base SQL query to get all products
$sql = "SELECT p.productID, p.product_name, p.category, p.product_image, v.price, v.stock
        FROM tb_products p
        JOIN tb_productvariants v ON p.productID = v.productID
        WHERE v.is_default = 1";

// If search term is provided, filter products by name
if (!empty($search)) {
    $sql .= " AND p.product_name LIKE '%$search%'";
}

// Execute query
$result = $conn->query($sql);

// Debugging: Check if search value is received
// echo "Search term: " . $search;
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
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Be Vietnam Pro', sans-serif;
            background-color: #f1f2d8;
        }
        .logo { display: flex; align-items: center; gap: 10px; }
        .logo-image { height: 40px; width: auto; }
        header { background-color: #1F4529; color: #EED3B1; padding: 20px; display: flex; justify-content: space-between; align-items: center; width: 100%; box-sizing: border-box; position: relative; }
        .navbar { display: flex; justify-content: flex-end; align-items: center; padding: 10px 20px; }
        .search-bar { padding: 8px 12px; border-radius: 25px; border: none; outline: none; font-size: 1rem; width: 300px; background-color: #FFFFFF; margin-right: 20px; }
        .icons { display: flex; align-items: center; gap: 15px; }
        .icon-profile, .burger-menu, .fa-shopping-cart { font-size: 1.8rem; color: #EED3B1; cursor: pointer; }
        .container-fluid { margin-top: 20px; }
        .filter-container { display: flex; align-items: center; gap: 10px; margin-bottom: 20px; }
        .filter-container i { font-size: 1.5rem; cursor: pointer; }
        .category-dropdown { background-color: #1F4529; color: white; border: none; padding: 8px 12px; border-radius: 5px; cursor: pointer; font-size: 1rem; }
        .product-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(200px, 1fr)); gap: 15px; }
        .product-card { border: 1px solid #e0e0e0; border-radius: 8px; background-color: #fff; padding: 15px; text-align: center; box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .product-card img { width: 100%; max-height: 150px; object-fit: cover; border-radius: 5px; }
        .variant-options { margin-top: 10px; }
        .variant-select { width: 100%; padding: 5px; margin-top: 5px; border-radius: 5px; border: 1px solid #e0e0e0; }
        .add-to-cart { background-color: #1F4529; color: #fff; border: none; border-radius: 5px; padding: 8px 15px; cursor: pointer; margin-top: 10px; }
        .add-to-cart:hover { background-color: #15432b; }
        footer { background-color: #1F4529; color: white; padding: 20px 50px; display: flex; flex-direction: column; width: 100%; }
        .footer-container { display: flex; justify-content: space-between; align-items: center; width: 100%; max-width: 1200px; }
        .footer-logo { height: 200px; margin-left: 100px; }
        .footer-nav { list-style: none; padding: 0; margin-bottom: 40px; }
        .footer-nav li { font-family: "Bebas Neue", serif; font-size: 20px; }
        .footer-nav a { color: white; text-decoration: none; }
        .social-icons { flex-direction: column; align-items: flex-start; margin-bottom: 100px; }
        .social-icons a { color: white; font-size: 18px; text-decoration: none; gap: 10px; margin-left: 4px; }
        .social-icons p { color: white; font-size: 20px; font-family: "Bebas Neue", serif; margin-bottom: 0; margin-top: 0; }
        .footer-center { text-align: center; width: 100%; font-size: 14px; margin-top: 10px; }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <img src="../Home_Page/cfn_logo2.png" alt="Logo" class="logo-image"/>
        </div>
        <div class="navbar">
                <p class="usernamedisplay">Bonjour, <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>
                <input type="text" class="search-bar" id="searchBar" placeholder="Search Product" />            
                <div class="icons">
                <a href="../Home_Page/home.php">
                    <i class="fa-solid fa-house home"></i>
                </a>
                <a href="../drew/cart.php">
                    <i class="fa-solid fa-cart-shopping cart"></i>
                </a>
                <a href="../User_Profile_Page/UserProfile.php">
                    <i class="far fa-user-circle fa-2x icon-profile"></i>
                </a>    
            </div>
        </div>
    </header>


    <?php
    // Get category from URL or default to all products
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $query = "SELECT * FROM products";
    
        if (!empty($category)) {
            $query .= " WHERE category = '$category'";
        }

    // Fetch products and display them
    ?>


    <div class="container mt-4">
        <div class="filter-container">
            <i class="fas fa-filter"></i>
            <span>Search Filter</span>
            <select class="category-dropdown" id="categoryFilter" onchange="filterCategory()">
                <option value="">All Products</option>
                <option value="skin">Skin</option>
                <option value="hair">Hair</option>
                <option value="face">Face</option>
                <option value="perfume">Perfume</option>
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

    // Get selected category from URL
    $categoryFilter = isset($_GET['category']) ? $_GET['category'] : '';

    // Modify SQL query based on category filter
    $sql = "SELECT p.productID, p.product_name, p.category, p.product_image, v.price, v.stock,
           (SELECT COUNT(*) FROM tb_productvariants WHERE productID = p.productID) as variant_count
            FROM tb_products p
            JOIN tb_productvariants v ON p.productID = v.productID
            WHERE v.is_default = 1";

    if (!empty($categoryFilter)) {
        $sql .= " AND p.category = '$categoryFilter'";
    }

    $result = $conn->query($sql);



            if (!$result) {
                echo "<p>Error fetching products: " . $conn->error . "</p>";
            } elseif ($result->num_rows > 0) {
                while ($product = $result->fetch_assoc()) {
                    $stockStatus = ($product['stock'] >= 50) ? "In Stock" : (($product['stock'] > 0) ? "Low Stock" : "Out of Stock");
                    // Correctly construct image paths
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
                            $variant_sql = "SELECT variant_id, variant_name, price, stock FROM tb_productvariants WHERE productID = $prodID";
                            $variant_result = $conn->query($variant_sql);
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
                        <button class="add-to-cart" data-product-id="<?= $product['productID']; ?>" onclick="window.location.href='../e-com/productpage.php?id=<?= $product['productID']; ?>'">Add to Cart</button>
                    </div>
                    <?php
                }
            } else {
                echo "<p>No products found.</p>";
            }
            $conn->close();
            ?>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <img src="/CFN/e-com/images/cfn_logo.png" alt="Naturale Logo" class="footer-logo">
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

        // Add to cart handling
        document.querySelectorAll('.add-to-cart').forEach(button => {
            button.addEventListener('click', function() {
                const productId = this.getAttribute('data-product-id');
                const variantSelect = this.previousElementSibling.querySelector('.variant-select');
                const variantId = variantSelect ? variantSelect.value : null;
                console.log(`Added to cart: Product ID ${productId}, Variant ID ${variantId || 'default'}`);
                // Add cart logic here (e.g., AJAX call to cart script)
            });
        });

        document.addEventListener("DOMContentLoaded", function () {
    var categoryDropdown = document.getElementById('categoryFilter');

    // Get category from URL and set the dropdown value
    var urlParams = new URLSearchParams(window.location.search);
    var currentCategory = urlParams.get('category');
    if (currentCategory) {
        categoryDropdown.value = currentCategory;
    }

    // Function to change category and reload the page
    window.filterCategory = function () {
        var selectedCategory = categoryDropdown.value;
        window.location.href = "../Home_Page/ProductScroll.php?category=" + selectedCategory;

    };
});


        // Filter products by category and search
        function filterProducts() {
            const category = document.getElementById('categoryFilter').value.toLowerCase();
            const searchText = document.getElementById('searchBar').value.toLowerCase();
            const products = document.querySelectorAll('.product-card');

            products.forEach(product => {
                const productCategory = product.getAttribute('data-category');
                const productName = product.querySelector('h5').textContent.toLowerCase();
                const matchesCategory = !category || productCategory === category;
                const matchesSearch = productName.includes(searchText);

                product.style.display = (matchesCategory && matchesSearch) ? '' : 'none';
            });
        }

        function filterCategory() {
    var selectedCategory = document.getElementById('categoryFilter').value;
    window.location.href = "ProductsScroll.php?category=" + selectedCategory;
}

document.getElementById('searchBar').addEventListener('keypress', function(event) {
    if (event.key === "Enter") {
        event.preventDefault(); // Prevent form submission
        const searchText = this.value.trim();
        const urlParams = new URLSearchParams(window.location.search);
        urlParams.set('search', searchText);
        window.location.search = urlParams.toString(); // Reload page with search query
    }
});



        // Add event listeners for filters
        document.getElementById('categoryFilter').addEventListener('change', filterProducts);
        document.getElementById('searchBar').addEventListener('input', filterProducts);
    </script>
</body>
</html>
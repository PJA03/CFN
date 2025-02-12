<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>

    <!-- Bootstrap & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style2.css">
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar d-flex flex-column p-3">
                <img src="images/cfn_logo.png" alt="Naturale Logo" class="img-fluid mb-3">
                <nav class="nav flex-column">
                    <a class="nav-link" href="manageproductsA.php">Products</a>
                    <a class="nav-link" href="managecontentA.php">Content</a>
                    <a class="nav-link" href="manageordersA.php">Orders</a>
                    <a class="nav-link" href="analytics.php">Analytics</a>
                </nav>

                <div class="mt-auto">
                    <hr>
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                        <span>Admin User</span>
                    </div>
                    <a href="#" class="text-white text-decoration-none mt-3">Log Out</a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4 main-content">
                <h1>Welcome Back, Admin!</h1>
                <h3 class="mt-4 text-center">Products Table</h3>

                <!-- Filter Section -->
                <div class="row mb-3">
                    <div class="col-md-3">
                        <label for="categoryFilter">Category:</label>
                        <select id="categoryFilter" class="form-control">
                            <option value="all">All</option>
                            <option value="skincare">Skincare</option>
                            <option value="makeup">Makeup</option>
                            <option value="fragrance">Fragrance</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="minPriceFilter">Min Price:</label>
                        <input type="range" id="minPriceFilter" class="form-range" min="0" max="500" value="0">
                        <span id="minPriceValue">₱0</span>
                    </div>
                    <div class="col-md-3">
                        <label for="priceFilter">Max Price:</label>
                        <input type="range" id="priceFilter" class="form-range" min="0" max="2000" value="2000">
                        <span id="priceValue">₱2000</span>
                    </div>
                    <div class="col-md-3">
                        <label for="stockFilter">Stock Level:</label>
                        <select id="stockFilter" class="form-control">
                            <option value="all">All</option>
                            <option value="high">High Stock (50+)</option>
                            <option value="low">Low Stock (1-50)</option>
                            <option value="out-of-stock">Out of Stock</option>
                        </select>
                    </div>
                </div>

                <div class="d-flex justify-content-end align-items-center mb-3">
                    <input type="text" class="form-control w-25 me-2" id="searchProduct" placeholder="Search Product">
                    <button id="addProductBtn" class="btn btn-success">+ Add Product</button>
                </div>

                <!-- Products Grid -->
                <div class="bg-white p-4 rounded shadow-sm">
                    <div class="product-grid" id="productList">
                        <?php
                        $products = [
                            ["name" => "Clarifying Shampoo Bar", "price" => 100, "stock" => 65, "category" => "skincare"],
                            ["name" => "Luxury Makeup Kit", "price" => 300, "stock" => 5, "category" => "makeup"],
                            ["name" => "Organic Perfume", "price" => 200, "stock" => 0, "category" => "fragrance"],
                            ["name" => "Moisturizing Cream", "price" => 150, "stock" => 80, "category" => "skincare"],
                        ];

                        foreach ($products as $product): 
                            $stockLevel = ($product['stock'] > 50) ? "high" : (($product['stock'] > 0) ? "low" : "out-of-stock");
                        ?>
                            <div class="product-card"
                                data-category="<?= $product['category']; ?>"
                                data-price="<?= $product['price']; ?>"
                                data-stock="<?= $stockLevel; ?>">
                                <img src="images/image.png" alt="Placeholder">
                                <h5><?= $product['name']; ?></h5>
                                <p>₱<?= $product['price']; ?> - <?= $product['stock']; ?> left</p>
                                <div class="actions">
                                    <i class="bi bi-pencil-square edit-icon" onclick="redirectToEdit()"></i>
                                    <i class="bi bi-trash delete-icon" onclick="removeItem(this)"></i>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Redirect to addproduct.php when the "Add Product" button is clicked
        document.getElementById("addProductBtn").addEventListener("click", function() {
            window.location.href = "addproduct.php";
        });

        // Redirect to editproduct.php when the pencil icon is clicked
        function redirectToEdit() {
            window.location.href = "editproduct.php";
        }

        // Remove product when trash icon is clicked
        function removeItem(element) {
            if (confirm("Are you sure you want to delete this item?")) {
                element.closest('.product-card').remove();
            }
        }

        // Filtering Logic
        document.addEventListener("DOMContentLoaded", function () {
            const categoryFilter = document.getElementById("categoryFilter");
            const minPriceFilter = document.getElementById("minPriceFilter");
            const priceFilter = document.getElementById("priceFilter");
            const minPriceValue = document.getElementById("minPriceValue");
            const priceValue = document.getElementById("priceValue");
            const stockFilter = document.getElementById("stockFilter");
            const searchProduct = document.getElementById("searchProduct");

            // Update min price label dynamically
            minPriceFilter.addEventListener("input", function () {
                minPriceValue.textContent = "₱" + minPriceFilter.value;
                filterProducts();
            });

            // Update max price label dynamically
            priceFilter.addEventListener("input", function () {
                priceValue.textContent = "₱" + priceFilter.value;
                filterProducts();
            });

            // Event listeners for filtering changes
            categoryFilter.addEventListener("change", filterProducts);
            stockFilter.addEventListener("change", filterProducts);
            searchProduct.addEventListener("input", filterProducts);

            function filterProducts() {
                const selectedCategory = categoryFilter.value;
                const minPrice = parseInt(minPriceFilter.value);
                const maxPrice = parseInt(priceFilter.value);
                const selectedStock = stockFilter.value;
                const searchText = searchProduct.value.toLowerCase();

                document.querySelectorAll(".product-card").forEach(product => {
                    const productCategory = product.getAttribute("data-category");
                    const productPrice = parseInt(product.getAttribute("data-price"));
                    const productStock = product.getAttribute("data-stock");
                    const productName = product.querySelector("h5").textContent.toLowerCase();

                    const matchesCategory = selectedCategory === "all" || productCategory === selectedCategory;
                    const matchesPrice = productPrice >= minPrice && productPrice <= maxPrice;
                    const matchesStock = selectedStock === "all" || productStock === selectedStock;
                    const matchesSearch = productName.includes(searchText);

                    product.style.display = matchesCategory && matchesPrice && matchesStock && matchesSearch ? "block" : "none";
                });
            }
        });
    </script>
</body>
</html>

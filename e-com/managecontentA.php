<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;700&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="content.css"> <!-- Custom styles -->
    <title>Admin Analytics</title>
    <style>
        /* Modal Styling */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5); /* Semi-transparent background */
            display: none; /* Initially hidden */
            align-items: center;
            justify-content: center;
            z-index: 1050; /* Ensure modal is on top */
        }

        .modal-content {
            background-color: white;
            padding: 2rem;
            border-radius: 8px;
            width: 400px;
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }

        .modal-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 20px;
        }

       

    </style>
</head>

<body>
    <div class="d-flex flex-wrap">
        <!-- Sidebar -->
        <div class="col-12 col-md-2 sidebar p-3">
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
            <h1 class="welcome-text">Welcome Back, Admin!</h1>
            <h3 class="dashboard-title text-center">Content Dashboard</h3>

            <!-- Stat Cards -->
            <div class="bg-white p-4 shadow-sm rounded stat-container">
                <div class="row mb-4 justify-content-center">
                    <div class="col-md-2 stat-card">
                        <div class="stat-title">Pending Orders</div>
                        <div class="value">35</div>
                    </div>
                    <div class="col-md-2 stat-card">
                        <div class="stat-title">Top Selling</div>
                        <div class="value">Clarifying shampoo bar</div>
                    </div>
                    <div class="col-md-2 stat-card">
                        <div class="stat-title">Month Revenue</div>
                        <div class="value">&#8369;15,000</div>
                    </div>
                    <div class="col-md-2 stat-card">
                        <div class="stat-title">Total Shipped</div>
                        <div class="value">78</div>
                    </div>
                    <div class="col-md-2 stat-card">
                        <div class="stat-title">Total Orders</div>
                        <div class="value">78</div>
                    </div>
                </div>
            </div>

            <!-- Promo Codes Section -->
            <div class="bg-white p-4 shadow-sm rounded stat-container">
                <h2 class="text-center">Promo Codes</h2>
                <div class="row text-center">
                    <div class="col-md-4">
                        <h3>5%</h3>
                        <ul class="list-unstyled">
                            <li>Lorem Ipsum</li>
                            <li>Lorem Ipsum</li>
                            <li>Lorem Ipsum</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h3>10%</h3>
                        <ul class="list-unstyled">
                            <li>Lorem Ipsum</li>
                            <li>Lorem Ipsum</li>
                            <li>Lorem Ipsum</li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h3>20%</h3>
                        <ul class="list-unstyled">
                            <li>Lorem Ipsum</li>
                            <li>Lorem Ipsum</li>
                            <li>Lorem Ipsum</li>
                        </ul>
                    </div>
                </div>
                <div class="promo-btn-container">
                    <button class="btn btn-success promo-btn">Edit Promo Codes</button>
                </div>
            </div>

            <!-- Best Sellers Section -->
            <!-- Add Best Seller Section -->
            <div class="bg-white p-4 shadow-sm rounded stat-container">
                <h2 class="text-center">Best Sellers</h2>
                <div class="product-slider">
                    <i class="bi bi-arrow-left-circle slider-arrow"></i>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="images/image.png" alt="Product Image" class="img-fluid">
                        </div>
                        <div class="product-info">
                            <h5 class="product-name">PRODUCT NAME</h5>
                            <p class="product-category">PRODUCT CATEGORY</p>
                            <div class="product-icons">
                                <i class="bi bi-pencil-square edit-icon"></i>
                                <i class="bi bi-trash delete-icon"></i>
                            </div>
                        </div>
                    </div>
                    <i class="bi bi-arrow-right-circle slider-arrow"></i>
                </div>
                <div class="promo-btn-container">
                    <button class="btn btn-success promo-btn" onclick="openModal()">Add Best Seller</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Add Best Seller Modal -->
    <div id="addBestSellerModal" class="modal-overlay">
        <div class="modal-content">
            <h3 class="text-center">Select a Product to Add as Best Seller</h3>
            <select id="productDropdown" class="form-select">
                <option value="" disabled selected>Select a product</option>
                <option value="product1">Clarifying Shampoo Bar</option>
                <option value="product2">Moisturizing Lotion</option>
                <option value="product3">Essential Oil Set</option>
                <option value="product4">Facial Cleanser</option>
            </select>
            <div class="modal-buttons">
                <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                <button class="btn btn-success" onclick="confirmBestSeller()">Confirm</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Open Modal function
        function openModal() {
            // Show the modal overlay
            document.getElementById("addBestSellerModal").style.display = "flex";
        }

        // Close Modal function
        function closeModal() {
            // Hide the modal overlay
            document.getElementById("addBestSellerModal").style.display = "none";
        }

        // Confirm Best Seller function
        function confirmBestSeller() {
            let selectedProduct = document.getElementById("productDropdown").value;
            if (!selectedProduct) {
                alert("Please select a product.");
                return;
            }
            alert("Product added as Best Seller: " + selectedProduct);
            closeModal(); // Close the modal after confirming
        }
    </script>
</body>

</html>

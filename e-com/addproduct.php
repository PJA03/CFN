<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <title>Admin Dashboard - Add Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="editproduct.css">
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
                <h3 class="mt-4 text-center">Add Product</h3>

                <!-- Empty White Container -->
                <div class="bg-white p-4 rounded shadow-sm">
                    <div class="product-grid">
                        <!-- Product Image Upload -->
                        <div class="image-container text-center">
                            <input type="file" id="imageUpload" accept="image/*" class="d-none">
                            <button type="button" class="btn btn-primary upload-image-btn" onclick="document.getElementById('imageUpload').click();">
                                Upload Product Image
                            </button>
                            <div id="imagePreviewContainer" class="mt-3 d-none">
                                <img id="imagePreview" src="" alt="Product Image" class="product-preview">
                            </div>
                        </div>

                        <!-- Product Form -->
                        <form id="productForm">
                            <!-- Name Field -->
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" placeholder="Enter product name" id="productName">
                            </div>

                            <!-- Price & Stocks -->
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Price</label>
                                    <input type="number" class="form-control" placeholder="Enter product price" id="productPrice">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Stocks</label>
                                    <input type="number" class="form-control" placeholder="Enter available stocks" id="productStocks">
                                </div>
                            </div>

                            <!-- Description Field -->
                            <div class="mb-3 mt-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="4" placeholder="Enter product description" id="productDescription"></textarea>
                            </div>

                            <!-- Buttons -->
                            <div class="form-buttons">
                                <button type="button" class="btn btn-secondary" onclick="handleCancel()">Cancel</button>
                                <button type="submit" class="btn btn-success" onclick="handleSave(event)">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>

        </div>
        <!-- End of White Container -->
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- JavaScript for Live Image Preview -->
    <script>
        // Handle live image preview
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('imagePreview').src = e.target.result;
                    document.getElementById('imagePreviewContainer').classList.remove('d-none');
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle Cancel Button
        function handleCancel() {
            let form = document.getElementById("productForm");
            let originalValues = {
                name: "",
                price: "",
                stocks: "",
                description: "",
                image: ""
            };

            let currentValues = {
                name: document.getElementById("productName").value,
                price: document.getElementById("productPrice").value,
                stocks: document.getElementById("productStocks").value,
                description: document.getElementById("productDescription").value,
                image: document.getElementById("imagePreview").src
            };

            // If any field is modified, confirm reset
            if (JSON.stringify(originalValues) !== JSON.stringify(currentValues)) {
                if (confirm("You have unsaved changes. Do you want to reset and leave the page?")) {
                    form.reset(); // Reset form fields to default
                    document.getElementById('imagePreview').src = ""; // Clear image preview
                    document.getElementById('imagePreviewContainer').classList.add('d-none'); // Hide image preview container
                    window.location.href = "manageproductsA.php"; // Redirect to manageproductsA.php
                }
            } else {
                window.location.href = "manageproductsA.php"; // No changes made, just navigate away
            }
        }

        // Handle Save Button
        function handleSave(event) {
            event.preventDefault(); // Prevent form from submitting normally

            let confirmSave = confirm("Are you sure you want to save changes?");
            if (confirmSave) {
                // Simulate saving data (you can add an API call here)
                alert("Product has been saved!");
                window.location.href = "manageproductsA.php"; // Redirect to manageproductsA.php after saving
            }
        }
    </script>
</body>
</html>

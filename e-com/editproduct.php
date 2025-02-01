<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
  
    <title>Admin Dashboard</title>
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
                <h3 class="mt-4 text-center">Edit Product</h3>

                <!-- Empty White Container -->
                <div class="bg-white p-4 rounded shadow-sm">
                    <div class="product-grid">
                        <!-- Product Image -->
                        <div class="image-container text-center">
                            <img id="productImage" src="images/image.png" alt="Product Image" class="product-preview">
                            <div class="d-flex justify-content-end mt-2">
                                <input type="file" id="imageUpload" accept="image/*" class="d-none">
                                <button type="button" class="btn btn-danger edit-image-btn" onclick="document.getElementById('imageUpload').click();">
                                    Edit Image
                                </button>
                            </div>
                        </div>

                        <!-- Product Form -->
                        <form id="productForm">
                            <!-- Name Field -->
                            <div class="mb-3">
                                <label class="form-label">Name</label>
                                <input type="text" class="form-control" value="CLARIFYING SHAMPOO BAR" id="productName">
                            </div>

                            <!-- Price & Stocks -->
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label">Price</label>
                                    <input type="number" class="form-control" value="100.00" id="productPrice">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label">Stocks</label>
                                    <input type="number" class="form-control" value="65" id="productStocks">
                                </div>
                            </div>

                            <!-- Description Field -->
                            <div class="mb-3 mt-3">
                                <label class="form-label">Description</label>
                                <textarea class="form-control" rows="4" id="productDescription">
Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt at nisl a ultrices. Donec bibendum mollis purus, quis fermentum dolor rutrum sed.
                                </textarea>
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
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Handle Cancel Button
        function handleCancel() {
            // Get the form and input values
            let form = document.getElementById("productForm");
            let originalValues = {
                name: "CLARIFYING SHAMPOO BAR",
                price: "100.00",
                stocks: "65",
                description: `Lorem ipsum dolor sit amet, consectetur adipiscing elit. Ut tincidunt at nisl a ultrices. Donec bibendum mollis purus, quis fermentum dolor rutrum sed.`
            };
            
            // Check if any values have been modified
            let currentValues = {
                name: document.getElementById("productName").value,
                price: document.getElementById("productPrice").value,
                stocks: document.getElementById("productStocks").value,
                description: document.getElementById("productDescription").value
            };

            // If the values have changed, ask user to confirm reset
            if (JSON.stringify(originalValues) !== JSON.stringify(currentValues)) {
                if (confirm("You have unsaved changes. Do you want to reset and leave the page?")) {
                    form.reset(); // Reset form to original values
                    window.location.href = "manageproductsA.php"; // Navigate to the page after reset
                }
            } else {
                // If no changes were made, simply go back to manageproductsA.php
                window.location.href = "manageproductsA.php";
            }
        }

        // Handle Save Button
        function handleSave(event) {
            event.preventDefault(); // Prevent the form from submitting the usual way

            // Ask for confirmation before navigating away
            let confirmSave = confirm("Are you sure you want to save changes?");
            if (confirmSave) {
                // Here you would save the changes (e.g., via an API call or form submission)
                alert("Changes have been saved!");
                window.location.href = "manageproductsA.php"; // Redirect to manageproductsA.php after saving
            }
        }
    </script>
</body>
</html>

<?php

require_once 'auth_check.php';

ini_set('display_errors', 1);
error_reporting(E_ALL);


if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    $errorMessage = "Product ID is required.";
    $alertType = "danger";
} else {
    $product_id = intval($_GET['id']);

    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "db_cfn";

    $conn = new mysqli($servername, $username, $password, $dbname);
    if ($conn->connect_error) {
        $errorMessage = "Connection failed: " . $conn->connect_error;
        $alertType = "danger";
    } else {
        $sql = "SELECT * FROM tb_products WHERE productID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $product = $result->fetch_assoc();
            // Ensure the image path is relative to the web root
            if (!empty($product['product_image']) && strpos($product['product_image'], 'uploads/') === 0) {
                $product['product_image'] = $product['product_image']; // Already relative
            } elseif (!empty($product['product_image'])) {
                // Adjust if the path is absolute or malformed
                $product['product_image'] = 'uploads/' . basename($product['product_image']);
            }
        } else {
            $errorMessage = "Product not found.";
            $alertType = "danger";
        }
        $stmt->close();

        $variants = [];
        $sql = "SELECT * FROM tb_productvariants WHERE productID = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $variants[] = $row;
        }
        $stmt->close();
        $conn->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Edit Product</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="editproduct.css">
    <style>
        .variant-row { margin-bottom: 10px; }
        .variant-row .remove-variant { margin-top: 32px; }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
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

            <div class="col-md-10 p-4 main-content">
                <!-- Alert Container -->
                <div id="alert-message" class="alert alert-<?php echo isset($alertType) ? $alertType : ''; ?> alert-dismissible" role="alert" style="<?php echo isset($errorMessage) || (isset($_GET['update']) && $_GET['update'] === 'success') ? 'display: block;' : 'display: none;'; ?>">
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    <span id="alert-text">
                        <?php 
                        if (isset($errorMessage)) {
                            echo $errorMessage;
                        } elseif (isset($_GET['update']) && $_GET['update'] === 'success') {
                            echo "Product updated successfully.";
                        }
                        ?>
                    </span>
                </div>

                <?php if (!isset($errorMessage)): // Only show form if no fatal error ?>
                <h3 class="mt-4 text-center">Edit Product</h3>
                <div class="bg-white p-4 rounded shadow-sm">
                    <form id="productForm" action="update_product.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="productID" value="<?= htmlspecialchars($product['productID']); ?>">

                        <div class="image-container text-center mb-3">
                            <img id="productImage" src="<?= !empty($product['product_image']) ? '/CFN/e-com/' . htmlspecialchars($product['product_image']) : 'images/cfn_logo.png'; ?>" alt="Product Image" class="product-preview" style="max-width:300px;">
                            <div class="d-flex justify-content-end mt-2">
                                <input type="file" id="imageUpload" name="productImage" accept="image/*" class="d-none">
                                <button type="button" class="btn btn-danger edit-image-btn" onclick="document.getElementById('imageUpload').click();">Edit Image</button>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Product Name</label>
                            <input type="text" class="form-control" value="<?= htmlspecialchars($product['product_name']); ?>" name="productName" required>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-12">
                                <label class="form-label">Category</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($product['category']); ?>" name="category" required>
                            </div>
                        </div>

                        <div class="mb-3 mt-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" rows="4" name="productDescription" required><?= htmlspecialchars($product['product_desc']); ?></textarea>
                        </div>

                        <!-- Variants Section (always shown) -->
                        <h4>Product Variants</h4>
                        <div id="variantsContainer">
                            <?php foreach ($variants as $index => $variant): ?>
                                <div class="variant-row row mb-2">
                                    <input type="hidden" name="variant_id[]" value="<?= $variant['variant_id']; ?>">
                                    <div class="col-md-2">
                                        <input type="text" class="form-control" name="variant_name[]" value="<?= htmlspecialchars($variant['variant_name']); ?>" placeholder="Variant Name" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control" name="price[]" value="<?= $variant['price']; ?>" placeholder="Price" step="0.01" required>
                                    </div>
                                    <div class="col-md-2">
                                        <input type="number" class="form-control" name="stock[]" value="<?= $variant['stock']; ?>" placeholder="Stock" required>
                                    </div>
                                    <div class="col-md-3">
                                        <input type="text" class="form-control" name="sku[]" value="<?= htmlspecialchars($variant['sku']); ?>" placeholder="SKU (optional)">
                                    </div>
                                    <div class="col-md-1 d-flex align-items-center">
                                        <input type="radio" name="defaultVariant" value="<?= $index; ?>" <?= $variant['is_default'] ? 'checked' : ''; ?>>
                                    </div>
                                    <div class="col-md-2">
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeVariantRow(this)">Remove</button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <button type="button" class="btn btn-secondary mb-3" onclick="addVariantRow()">+ Add Another Variant</button>

                        <div class="form-buttons">
                            <button type="button" class="btn btn-secondary" onclick="handleCancel()">Cancel</button>
                            <button type="submit" class="btn btn-success">Save Changes</button>
                        </div>
                    </form>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Function to show styled alerts
        function showAlert(message, type) {
            const alertText = document.getElementById("alert-text");
            const alertMessage = document.getElementById("alert-message");
            alertText.innerHTML = message;
            alertMessage.className = "alert alert-" + type + " alert-dismissible";
            alertMessage.style.display = "block";
        }

        // Image upload preview
        document.getElementById('imageUpload').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('productImage').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle cancel with styled alert
        function handleCancel() {
            if (confirm("Are you sure you want to cancel? Unsaved changes will be lost.")) {
                window.location.href = "manageproductsA.php";
            }
        }

        // Remove variant row with styled alert
        function removeVariantRow(button) {
            if (confirm("Are you sure you want to remove this variant?")) {
                button.closest('.variant-row').remove();
                showAlert("Variant removed successfully.", "success");
            }
        }

        // Add new variant row
        let nextVariantIndex = <?= count($variants); ?>;
        function addVariantRow() {
            const container = document.getElementById('variantsContainer');
            const row = document.createElement('div');
            row.classList.add('variant-row', 'row', 'mb-2');
            row.innerHTML = `
                <div class="col-md-2">
                    <input type="hidden" name="variant_id[]" value="">
                    <input type="text" class="form-control" name="variant_name[]" placeholder="Variant Name" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="price[]" placeholder="Price" step="0.01" required>
                </div>
                <div class="col-md-2">
                    <input type="number" class="form-control" name="stock[]" placeholder="Stock" required>
                </div>
                <div class="col-md-3">
                    <input type="text" class="form-control" name="sku[]" placeholder="SKU (optional)">
                </div>
                <div class="col-md-1 d-flex align-items-center">
                    <input type="radio" name="defaultVariant" value="${nextVariantIndex}">
                </div>
                <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeVariantRow(this)">Remove</button>
                </div>
            `;
            container.appendChild(row);
            nextVariantIndex++;
            showAlert("New variant added.", "success");
        }

        // Show success alert if redirected from update
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('update') === 'success') {
                showAlert("Product updated successfully.", "success");
                // Optionally auto-hide the alert after a few seconds
                setTimeout(() => {
                    const alert = document.getElementById("alert-message");
                    alert.style.display = 'none';
                }, 3000);
            }
        });
    </script>
</body>
</html>
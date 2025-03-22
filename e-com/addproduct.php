<?php
require_once 'auth_check.php';
?>
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
  <style>
    .variant-row { margin-bottom: 10px; }
    #variantsContainer, #addVariantButton { display: none; }
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
          <a href="/CFN/logout.php" class="btn btn-danger">Logout</a>
        </div>
      </div>

      <div class="col-md-10 p-4 main-content">
        <h3 class="mt-4 text-center">Add Product</h3>
        <div class="bg-white p-4 rounded shadow-sm">
          <form id="productForm" action="processproduct.php" method="POST" enctype="multipart/form-data">
            <div class="image-container text-center mb-3">
              <input type="file" id="imageUpload" name="productImage" accept="image/*" class="d-none">
              <button type="button" class="btn btn-primary" onclick="document.getElementById('imageUpload').click();">
                Upload Product Image
              </button>
              <div id="imagePreviewContainer" class="mt-3 d-none">
                <img id="imagePreview" src="" alt="Product Image" class="product-preview" style="max-width: 300px;">
              </div>
            </div>

            <div class="mb-3">
              <label class="form-label">Product Name</label>
              <input type="text" class="form-control" name="productName" required>
            </div>

            <div class="mb-3">
              <label class="form-label">Category</label>
              <input type="text" class="form-control" name="category" required>
            </div>

            <div class="row">
              <div class="col-md-6">
                <label class="form-label">Base Price</label>
                <input type="number" class="form-control" name="basePrice" step="0.01">
              </div>
              <div class="col-md-6">
                <label class="form-label">Base Stocks</label>
                <input type="number" class="form-control" name="baseStock">
              </div>
            </div>

            <div class="mb-3 mt-3">
              <label class="form-label">Product Description</label>
              <textarea class="form-control" rows="4" name="productDescription" required></textarea>
            </div>

            <div class="mb-3">
              <input type="checkbox" id="hasVariants" name="hasVariants" onclick="toggleVariants()">
              <label for="hasVariants">This product has variants</label>
            </div>

            <div id="variantsContainer">
              <h4>Product Variants</h4>
            </div>

            <button type="button" id="addVariantButton" class="btn btn-secondary mb-3" onclick="addVariantRow()">+ Add Another Variant</button>

            <div class="form-buttons">
              <button type="button" class="btn btn-secondary" onclick="handleCancel()">Cancel</button>
              <button type="submit" class="btn btn-success">Save Changes</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    document.getElementById("imageUpload").addEventListener("change", function(event) {
      const file = event.target.files[0];
      if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
          document.getElementById("imagePreview").src = e.target.result;
          document.getElementById("imagePreviewContainer").classList.remove("d-none");
        };
        reader.readAsDataURL(file);
      }
    });

    function toggleVariants() {
      const variantsContainer = document.getElementById("variantsContainer");
      const addVariantButton = document.getElementById("addVariantButton");
      const hasVariants = document.getElementById("hasVariants").checked;
      variantsContainer.style.display = hasVariants ? 'block' : 'none';
      addVariantButton.style.display = hasVariants ? 'block' : 'none';
    }

    function addVariantRow() {
      const variantsContainer = document.getElementById("variantsContainer");
      const newRow = document.createElement("div");
      newRow.classList.add("variant-row", "row", "mb-2");
      newRow.innerHTML = `
        <div class="col-md-3">
          <input type="text" class="form-control" name="variantName[]" placeholder="Variant Name" required>
        </div>
        <div class="col-md-3">
          <input type="number" class="form-control" name="variantPrice[]" placeholder="Price" step="0.01" required>
        </div>
        <div class="col-md-3">
          <input type="number" class="form-control" name="variantStock[]" placeholder="Stock" required>
        </div>
        <div class="col-md-3">
          <input type="text" class="form-control" name="variantSKU[]" placeholder="SKU (optional)">
        </div>
      `;
      variantsContainer.appendChild(newRow);
    }

    function handleCancel() {
      if (confirm("Are you sure you want to cancel?")) {
        window.location.href = "manageproductsA.php";
      }
    }
  </script>
</body>
</html>
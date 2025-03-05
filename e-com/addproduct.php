<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Add Product</title>
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <!-- Custom CSS for this page -->
  <link rel="stylesheet" href="editproduct.css">
  <style>
    .variant-row { margin-bottom: 10px; }
    /* Optional: styling for variant section header */
    #variantsContainer h5 {
      font-weight: bold;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar (same as before) -->
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
        <div class="bg-white p-4 rounded shadow-sm">
          <div class="product-grid">
            <!-- Product Form -->
            <!-- 
              When the form is submitted (to processproduct.php),
              your back‑end should:
                1. Insert the main product data into tb_products.
                2. Retrieve the generated product_id.
                3. Loop through the variant arrays (variantName[], variantPrice[], etc.)
                   and insert each row into tb_productvariants linked with product_id.
              If a product does not have multiple variants, fill in one row with "Default" as the variant name.
            -->
            <form id="productForm" action="processproduct.php" method="POST" enctype="multipart/form-data">
              <!-- Product Image Upload -->
              <div class="image-container text-center mb-3">
                <input type="file" id="imageUpload" name="productImage" accept="image/*" class="d-none">
                <button type="button" class="btn btn-primary upload-image-btn" onclick="document.getElementById('imageUpload').click();">
                  Upload Product Image
                </button>
                <div id="imagePreviewContainer" class="mt-3 d-none">
                  <img id="imagePreview" src="" alt="Product Image" class="product-preview" style="max-width: 300px;">
                </div>
              </div>

              <!-- Main Product Details -->
              <div class="mb-3">
                <label class="form-label">Product Name</label>
                <input type="text" class="form-control" placeholder="Enter product name" id="productName" name="productName" required>
              </div>
              
              <!-- Brand & Category -->
              <div class="row mb-3">
                <div class="col-md-6">
                  <label class="form-label">Brand</label>
                  <input type="text" class="form-control" placeholder="Enter product brand" id="brand" name="brand" required>
                </div>
                <div class="col-md-6">
                  <label class="form-label">Category</label>
                  <input type="text" class="form-control" placeholder="Enter product category" id="category" name="category" required>
                </div>
              </div>
              
              <!-- Base Price & Stocks (for default variant or fallback) -->
              <div class="row">
                <div class="col-md-6">
                  <label class="form-label">Base Price (optional)</label>
                  <input type="number" class="form-control" placeholder="Enter base price" id="productPrice" name="productPrice" step="0.01">
                </div>
                <div class="col-md-6">
                  <label class="form-label">Base Stocks (optional)</label>
                  <input type="number" class="form-control" placeholder="Enter available stocks" id="productStocks" name="productStocks">
                </div>
              </div>
              
              <!-- Description Field -->
              <div class="mb-3 mt-3">
                <label class="form-label">Product Description</label>
                <textarea class="form-control" rows="4" placeholder="Enter product description" id="productDescription" name="productDescription" required></textarea>
              </div>

              <!-- Variants Section -->
              <h4>Product Variants</h4>
              <p class="text-muted">For products without multiple variants, fill in one row with "Default" as the variant name.</p>
              <div id="variantsContainer">
                <!-- One default variant row -->
                <div class="variant-row row mb-2">
                  <div class="col-md-3">
                    <input type="text" class="form-control" name="variantName[]" placeholder="Variant Name (e.g., 'Default' or '85ml - Confident')" required>
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
                </div>
              </div>
              <button type="button" class="btn btn-secondary mb-3" onclick="addVariantRow()">+ Add Another Variant</button>

              <!-- Form Buttons -->
              <div class="form-buttons">
                <button type="button" class="btn btn-secondary" onclick="handleCancel()">Cancel</button>
                <button type="submit" class="btn btn-success">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <!-- End of Container -->
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Live image preview for product image upload
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
      if (confirm("You have unsaved changes. Do you really want to cancel?")) {
        form.reset();
        document.getElementById('imagePreview').src = "";
        document.getElementById('imagePreviewContainer').classList.add('d-none');
        window.location.href = "manageproductsA.php";
      }
    }

    // Dynamically add a new variant row
    function addVariantRow() {
      const container = document.getElementById('variantsContainer');
      const row = document.createElement('div');
      row.classList.add('variant-row', 'row', 'mb-2');
      row.innerHTML = `
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
      container.appendChild(row);
    }
  </script>
</body>
</html>

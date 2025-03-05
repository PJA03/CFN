<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure the product ID is passed in the URL
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("Product ID is required.");
}
$product_id = intval($_GET['id']);

// Database connection parameters
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "db_cfn";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch main product details from tb_products
$sql = "SELECT * FROM tb_products WHERE productID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $product = $result->fetch_assoc();
} else {
    die("Product not found.");
}
$stmt->close();

// Fetch default variant (for price and stock) from tb_productvariants
$sql = "SELECT price, stock FROM tb_productvariants WHERE productID = ? AND is_default = 1";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $product_id);
$stmt->execute();
$stmt->bind_result($defaultPrice, $defaultStock);
$stmt->fetch();
$stmt->close();

// Merge default price and stock into the product array for convenience
$product['price'] = $defaultPrice;
$product['stock'] = $defaultStock;

// Fetch all product variants for this product
$variants = array();
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
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - Edit Product</title>
  <!-- Bootstrap & Icons -->
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
        <div class="bg-white p-4 rounded shadow-sm">
          <form id="productForm" action="update_product.php" method="POST" enctype="multipart/form-data">
            <!-- Hidden field for product ID -->
            <input type="hidden" name="productID" value="<?= htmlspecialchars($product['productID']); ?>">
            
            <!-- Product Image -->
            <div class="image-container text-center mb-3">
              <img id="productImage" src="<?= !empty($product['product_image']) ? $product['product_image'] : 'images/image.png'; ?>" alt="Product Image" class="product-preview" style="max-width:300px;">
              <div class="d-flex justify-content-end mt-2">
                <input type="file" id="imageUpload" name="productImage" accept="image/*" class="d-none">
                <button type="button" class="btn btn-danger edit-image-btn" onclick="document.getElementById('imageUpload').click();">Edit Image</button>
              </div>
            </div>

            <!-- Main Product Details -->
            <div class="mb-3">
              <label class="form-label">Product Name</label>
              <input type="text" class="form-control" value="<?= htmlspecialchars($product['product_name']); ?>" id="productName" name="productName" required>
            </div>

            <!-- Brand & Category -->
            <div class="row mb-3">
              <div class="col-md-6">
                <label class="form-label">Brand</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($product['brand']); ?>" id="brand" name="brand" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Category</label>
                <input type="text" class="form-control" value="<?= htmlspecialchars($product['category']); ?>" id="category" name="category" required>
              </div>
            </div>

            <!-- Price & Stocks -->
            <div class="row">
              <div class="col-md-6">
                <label class="form-label">Price</label>
                <input type="number" class="form-control" value="<?= $product['price']; ?>" id="productPrice" name="productPrice" step="0.01" required>
              </div>
              <div class="col-md-6">
                <label class="form-label">Stocks</label>
                <input type="number" class="form-control" value="<?= $product['stock']; ?>" id="productStocks" name="productStocks" required>
              </div>
            </div>

            <!-- Product Description -->
            <div class="mb-3 mt-3">
              <label class="form-label">Description</label>
              <textarea class="form-control" rows="4" id="productDescription" name="productDescription" required><?= htmlspecialchars($product['product_desc']); ?></textarea>
            </div>

            <!-- Variants Section -->
            <h4>Product Variants</h4>
            <p class="text-muted">Edit existing variants or add new ones. Mark one variant as default.</p>
            <div id="variantsContainer">
              <?php if(!empty($variants)): ?>
                <?php foreach($variants as $variant): ?>
                  <div class="variant-row row mb-2">
                    <!-- Hidden field for variant ID -->
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
                    <div class="col-md-2">
                      <button type="button" class="btn btn-danger btn-sm" onclick="removeVariantRow(this)">Remove</button>
                    </div>
                  </div>
                <?php endforeach; ?>
              <?php else: ?>
                <div class="variant-row row mb-2">
                  <div class="col-md-2">
                    <input type="hidden" name="variant_id[]" value="">
                    <input type="text" class="form-control" name="variant_name[]" placeholder="Variant Name" value="Default" required>
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
                    <input type="radio" name="defaultVariant" value="" checked>
                  </div>
                  <div class="col-md-2">
                    <button type="button" class="btn btn-danger btn-sm" onclick="removeVariantRow(this)">Remove</button>
                  </div>
                </div>
              <?php endif; ?>
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

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Handle Cancel Button
    function handleCancel() {
      if (confirm("Are you sure you want to cancel? Unsaved changes will be lost.")) {
        window.location.href = "manageproductsA.php";
      }
    }

    // Live image preview for editing image
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

    // Remove variant row
    function removeVariantRow(button) {
      if (confirm("Are you sure you want to remove this variant?")) {
        button.closest('.variant-row').remove();
      }
    }

    // Add new variant row
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
          <input type="radio" name="defaultVariant" value="">
        </div>
        <div class="col-md-2">
          <button type="button" class="btn btn-danger btn-sm" onclick="removeVariantRow(this)">Remove</button>
        </div>
      `;
      container.appendChild(row);
    }
  </script>
</body>
</html>

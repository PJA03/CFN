<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Products - Admin Dashboard</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- Google Fonts -->
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
  <!-- Bootstrap Icons -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <!-- Custom CSS (Assuming style2.css exists; if not, this can be removed or replaced) -->
  <link rel="stylesheet" href="style2.css">
  <style>
    .product-card {
      background-color: #f8f9fa;
      border-radius: 8px;
      padding: 15px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
    .product-card img {
      width: 100%;
      max-height: 150px;
      object-fit: cover;
      border-radius: 5px;
    }
    .btn-manage {
      background-color: #1F4529;
      color: #fff;
      border: none;
      font-size: 0.6rem;
      padding: 3px 8px;
    }
    .btn-manage:hover {
      background-color: #15432b;
    }
    .variant-table th, .variant-table td {
      vertical-align: middle;
    }
    .collapse-variants {
      margin-top: 10px;
      background: #ffffff;
      border: 1px solid #dee2e6;
      padding: 10px;
      border-radius: 5px;
    }
    .filter-section {
      margin-bottom: 20px;
      background: #fff;
      padding: 15px;
      border-radius: 8px;
      box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }
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
          <a href="/CFN/e-com/logout.php" class="btn btn-danger">Logout</a>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 p-4 main-content">
        <!-- Alert Container -->
        <?php
        $message = '';
        $alertType = '';
        if (isset($_GET['update']) && $_GET['update'] == 'success') {
          $message = 'Product updated successfully.';
          $alertType = 'success';
        } elseif (isset($_GET['add']) && $_GET['add'] == 'success') {
          $message = 'Product added successfully.';
          $alertType = 'success';
        } // Add more conditions as needed, e.g., for errors
        ?>
        <div id="alert-message" class="alert alert-<?php echo $alertType; ?> alert-dismissible" role="alert" style="<?php echo $message ? 'display: block;' : 'display: none;'; ?>">
          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
          <span id="alert-text"><?php echo $message; ?></span>
        </div>

        <h1 class="text-center display-4 mb-4">Welcome Back, Admin!</h1>
        <h3 class="mt-4 text-center">Products Table</h3>

        <!-- Filter Section -->
        <div class="row mb-3 filter-section">
          <div class="col-md-4">
            <label for="minPriceFilter">Min Price:</label>
            <input type="range" id="minPriceFilter" class="form-range" min="0" max="500" value="0">
            <span id="minPriceValue">₱0</span>
          </div>
          <div class="col-md-4">
            <label for="priceFilter">Max Price:</label>
            <input type="range" id="priceFilter" class="form-range" min="0" max="4000" value="4000">
            <span id="priceValue">₱4000</span>
          </div>
          <div class="col-md-4">
            <label for="stockFilter">Stock Level:</label>
            <select id="stockFilter" class="form-control">
              <option value="all">All</option>
              <option value="high">High Stock (50+)</option>
              <option value="low">Low Stock (1-50)</option>
              <option value="out-of-stock">Out of Stock</option>
            </select>
          </div>
        </div>

        <!-- Search & Add Product Row -->
        <div class="d-flex justify-content-between align-items-center mb-3">
          <input type="text" class="form-control" id="searchProduct" style="max-width:300px;" placeholder="Search Product">
          <button id="addProductBtn" class="btn btn-success">+ Add Product</button>
        </div>

        <!-- Products Grid -->
        <div class="bg-white p-4 rounded shadow-sm">
          <div class="product-grid" id="productList">
            <p id="noProductsMessage" class="text-center text-muted" style="display: none;">No products match the selected filters.</p>

            <?php
            // Database connection
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "db_cfn";

            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
            }

            // SQL query to fetch products with default variant and variant count
            $sql = "SELECT p.productID, p.product_name, p.category, p.product_image, v.price, v.stock, 
                           (SELECT COUNT(*) FROM tb_productvariants WHERE productID = p.productID) as variant_count
                    FROM tb_products p
                    JOIN tb_productvariants v ON p.productID = v.productID
                    WHERE v.is_default = 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
              while ($product = $result->fetch_assoc()) {
                $stockLevel = ($product['stock'] >= 50) ? "high" : (($product['stock'] > 0) ? "low" : "out-of-stock");
                $imgSrc = !empty($product['product_image']) ? $product['product_image'] : "images/cfn_logo.png";
                ?>
                <div class="product-card"
                     data-product-id="<?= $product['productID']; ?>"
                     data-price="<?= $product['price']; ?>"
                     data-stock="<?= $stockLevel; ?>"
                     data-category="<?= strtolower($product['category']); ?>">
                  <img src="<?= $imgSrc; ?>" alt="Product Image">
                  <h5><?= htmlspecialchars($product['product_name']); ?></h5>
                  <p>₱<?= number_format($product['price'], 2); ?> - <?= $product['stock']; ?> left</p>
                  <div class="actions">
                    <i class="bi bi-pencil-square edit-icon" onclick="redirectToEdit(<?= $product['productID']; ?>)"></i>
                    <i class="bi bi-trash delete-icon" onclick="removeItem(<?= $product['productID']; ?>, this.closest('.product-card'))"></i>
                    <?php if ($product['variant_count'] > 1): ?>
                      <button class="btn btn-manage btn-sm mt-2" data-bs-toggle="collapse" data-bs-target="#variants-<?= $product['productID']; ?>">
                        View Variants
                      </button>
                    <?php endif; ?>
                  </div>
                  <div class="collapse collapse-variants mt-2" id="variants-<?= $product['productID']; ?>">
                    <?php
                    $prodID = $product['productID'];
                    $variant_sql = "SELECT variant_id, variant_name, price, stock FROM tb_productvariants WHERE productID = $prodID";
                    $variant_result = $conn->query($variant_sql);
                    if ($variant_result->num_rows > 0) {
                      echo '<table class="table variant-table">';
                      echo '<thead><tr><th>Variant</th><th>Price</th><th>Stock</th></tr></thead><tbody>';
                      while ($variant = $variant_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($variant['variant_name']) . "</td>";
                        echo "<td>₱" . number_format($variant['price'], 2) . "</td>";
                        echo "<td>" . $variant['stock'] . "</td>";
                        echo "</tr>";
                      }
                      echo '</tbody></table>';
                    } else {
                      echo "<p>No variants found.</p>";
                    }
                    ?>
                  </div>
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
      </div>
    </div>
  </div>

  <!-- Bootstrap JS Bundle -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Redirect to Add Product page
    document.getElementById("addProductBtn").addEventListener("click", function() {
      window.location.href = "addproduct.php";
    });

    // Redirect to Edit Product page
    function redirectToEdit(productId) {
      window.location.href = "editproduct.php?id=" + productId;
    }

    // Function to show styled alerts
    function showAlert(message, type) {
      const alertText = document.getElementById("alert-text");
      const alertMessage = document.getElementById("alert-message");
      alertText.innerHTML = message;
      alertMessage.className = "alert alert-" + type + " alert-dismissible";
      alertMessage.style.display = "block";
    }

    // Delete product with confirmation and styled alert
    function removeItem(productId, productCard) {
      if (confirm("Are you sure you want to delete this product? This action cannot be undone.")) {
        fetch("deleteproduct.php?id=" + productId, {
          method: "DELETE"
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            showAlert("Product deleted successfully.", "success");
            productCard.remove();
          } else {
            showAlert("Error deleting product: " + (data.error || "Unknown error"), "danger");
          }
        })
        .catch(error => {
          console.error("Error:", error);
          showAlert("Error deleting product.", "danger");
        });
      }
    }

    // Filter products based on price, stock, and search
    function filterProducts() {
      const minPrice = parseFloat(document.getElementById("minPriceFilter").value) || 0;
      const maxPrice = parseFloat(document.getElementById("priceFilter").value) || Infinity;
      document.getElementById("minPriceValue").textContent = "₱" + minPrice;
      document.getElementById("priceValue").textContent = "₱" + maxPrice;
      const selectedStock = document.getElementById("stockFilter").value;
      const searchText = document.getElementById("searchProduct").value.toLowerCase();
      const productCards = document.querySelectorAll(".product-card");
      let visibleCount = 0;

      productCards.forEach(card => {
        const price = parseFloat(card.getAttribute("data-price")) || 0;
        const stock = card.getAttribute("data-stock") || "";
        const name = card.querySelector("h5").textContent.toLowerCase();

        const matchesPrice = (price >= minPrice && price <= maxPrice);
        const matchesStock = selectedStock === "all" || stock === selectedStock;
        const matchesSearch = name.includes(searchText);

        if (matchesPrice && matchesStock && matchesSearch) {
          card.style.display = "block";
          visibleCount++;
        } else {
          card.style.display = "none";
        }
      });

      document.getElementById("noProductsMessage").style.display = visibleCount === 0 ? "block" : "none";
    }

    // Event listeners for filters
    document.getElementById("minPriceFilter").addEventListener("input", filterProducts);
    document.getElementById("priceFilter").addEventListener("input", filterProducts);
    document.getElementById("stockFilter").addEventListener("change", filterProducts);
    document.getElementById("searchProduct").addEventListener("input", filterProducts);
  </script>
</body>
</html>
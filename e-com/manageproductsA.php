<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manage Products - Admin Dashboard</title>
  <!-- Bootstrap & Icons -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
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
    /* Reduced size for variant button */
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
      white-space: normal;
      word-wrap: break-word;
    }
    .collapse-variants {
      margin-top: 10px;
      background: #ffffff;
      border: 1px solid #dee2e6;
      padding: 10px;
      border-radius: 5px;
    }
    /* Filter section styling */
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
          <a href="#" class="text-white text-decoration-none mt-3">Log Out</a>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 p-4 main-content">
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
            <input type="range" id="priceFilter" class="form-range" min="0" max="2000" value="2000">
            <span id="priceValue">₱2000</span>
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
            <?php
            // Database connection parameters
            $servername = "localhost";
            $username = "root";
            $password = "";
            $dbname = "db_cfn";

            // Create connection
            $conn = new mysqli($servername, $username, $password, $dbname);
            if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
            }

            // Query products from tb_products and default variant from tb_productvariants
            $sql = "SELECT 
                        p.productID, 
                        p.product_name, 
                        p.category, 
                        p.product_image, 
                        v.price, 
                        v.stock 
                    FROM tb_products p
                    JOIN tb_productvariants v ON p.productID = v.productID
                    WHERE v.is_default = 1";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
              while ($product = $result->fetch_assoc()) {
                // Determine stock level for filtering
                $stockLevel = ($product['stock'] >= 50) ? "high" : (($product['stock'] > 0) ? "low" : "out-of-stock");
                $imgSrc = !empty($product['product_image']) ? $product['product_image'] : "images/cfn_logo.png";
                ?>
                <!-- Added data-product-id attribute -->
                <div class="product-card"
                     data-product-id="<?= $product['productID']; ?>"
                     data-price="<?= $product['price']; ?>"
                     data-stock="<?= $stockLevel; ?>"
                     data-category="<?= strtolower($product['category']); ?>">
                  <img src="<?= $imgSrc; ?>" alt="Product Image">
                  <h5><?= $product['product_name']; ?></h5>
                  <p>₱<?= $product['price']; ?> - <?= $product['stock']; ?> left</p>
                  <div class="actions">
                    <i class="bi bi-pencil-square edit-icon" onclick="redirectToEdit(<?= $product['productID']; ?>)"></i>
                    <i class="bi bi-trash delete-icon" onclick="removeItem(this)"></i>
                    <!-- Button to toggle variants collapse -->
                    <button class="btn btn-manage btn-sm mt-2" data-bs-toggle="collapse" data-bs-target="#variants-<?= $product['productID']; ?>">
                      View Variants
                    </button>
                  </div>
                  <!-- Collapsible section for product variants -->
                  <div class="collapse collapse-variants mt-2" id="variants-<?= $product['productID']; ?>">
                    <?php
                    // Query variants for this product from tb_productvariants
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

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    // Redirect to addproduct.php when the "Add Product" button is clicked
    document.getElementById("addProductBtn").addEventListener("click", function() {
      window.location.href = "addproduct.php";
    });

    // Redirect to editproduct.php when the pencil icon is clicked
    function redirectToEdit(productId) {
      window.location.href = "editproduct.php?id=" + productId;
    }

    // Delete product via AJAX when the trash icon is clicked
    function removeItem(element) {
      if (confirm("Are you sure you want to delete this product? This action cannot be undone.")) {
        let productCard = element.closest('.product-card');
        let productId = productCard.getAttribute("data-product-id");
        // Send DELETE request to deleteproduct.php
        fetch("deleteproduct.php?id=" + productId, {
          method: "DELETE"
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert("Product deleted successfully.");
            productCard.remove();
          } else {
            alert("Error deleting product: " + (data.error || "Unknown error"));
          }
        })
        .catch(error => {
          console.error("Error deleting product:", error);
          alert("Error deleting product.");
        });
      }
    }

    // Filtering Logic with live price updates
    function filterProducts() {
      const minPrice = parseFloat(document.getElementById("minPriceFilter").value) || 0;
      const maxPrice = parseFloat(document.getElementById("priceFilter").value) || Infinity;
      
      // Update displayed slider values
      document.getElementById("minPriceValue").textContent = "₱" + minPrice;
      document.getElementById("priceValue").textContent = "₱" + maxPrice;
      
      const selectedStock = document.getElementById("stockFilter").value;
      const searchText = document.getElementById("searchProduct").value.toLowerCase();

      document.querySelectorAll(".product-card").forEach(card => {
        const price = parseFloat(card.getAttribute("data-price")) || 0;
        const stock = card.getAttribute("data-stock") || "";
        const name = card.querySelector("h5").textContent.toLowerCase();

        const matchesPrice = (price >= minPrice && price <= maxPrice);
        const matchesStock = selectedStock === "all" || stock === selectedStock;
        const matchesSearch = name.includes(searchText);

        card.style.display = (matchesPrice && matchesStock && matchesSearch) ? "block" : "none";
      });
    }

    document.getElementById("minPriceFilter").addEventListener("input", filterProducts);
    document.getElementById("priceFilter").addEventListener("input", filterProducts);
    document.getElementById("stockFilter").addEventListener("change", filterProducts);
    document.getElementById("searchProduct").addEventListener("input", filterProducts);
  </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;700&family=Bebas+Neue&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="content.css" /> <!-- Custom styles -->
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
    
    .promo-section {
      margin: 2rem 0;
      border-radius: 8px;
    }
    .promo-card {
      background-color: #f7f7f7;
      border-radius: 8px;
      transition: transform 0.2s, box-shadow 0.2s;
    }
    .promo-card:hover {
      transform: translateY(-5px);
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    }
    .promo-card h2 {
      font-size: 2rem;
      margin-bottom: 1rem;
    }
    .promo-card ul li {
      font-size: 0.9rem;
      margin-bottom: 0.5rem;
      color: #555;
    }
    .promo-btn-container button {
      padding: 10px 20px;
      font-size: 1rem;
    }
    
    /* Best Sellers Section Styling */
    .product-slider {
      display: flex;
      align-items: center;
      justify-content: center;
      margin-top: 20px;
    }
    .slider-arrow {
      font-size: 2rem;
      cursor: pointer;
      color: #343a40;
      margin: 0 10px;
    }
    /* Container for best seller cards */
    #bestSellersContainer {
      display: flex;
      gap: 1rem;
      overflow-x: auto;
      padding: 10px;
    }
    .product-card {
      background-color: #fff;
      border-radius: 8px;
      padding: 10px;
      text-align: center;
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
      min-width: 200px;
    }
    .product-card .product-image img {
      width: 100%;
      border-radius: 4px;
    }
    .product-info {
      margin-top: 10px;
    }
    .product-name {
      font-size: 1.1rem;
      font-weight: 100;
      color: #343a40;
    }
    .product-category {
      font-size: 0.9rem;
      color: #6c757d;
    }
    .product-icons i {
      font-size: 1.2rem;
      margin: 0 5px;
      cursor: pointer;
      color: #adb5bd;
      transition: color 0.2s;
    }
    .product-icons i:hover {
      color: #343a40;
    }
  </style>
</head>
<body>
  <div class="d-flex flex-wrap">
    <!-- Sidebar -->
    <div class="col-12 col-md-2 sidebar p-3">
      <img src="images/cfn_logo.png" alt="Naturale Logo" class="img-fluid mb-3" />
      <nav class="nav flex-column">
        <a class="nav-link" href="manageproductsA.php">Products</a>
        <a class="nav-link" href="managecontentA.php">Content</a>
        <a class="nav-link" href="manageordersA.php">Orders</a>
        <a class="nav-link" href="analytics.php">Analytics</a>
      </nav>
      <div class="mt-auto">
        <hr />
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
      <div class="promo-section bg-white p-4 shadow-sm rounded stat-container">
        <h2 class="text-center mb-4">Promo Codes</h2>
        <div class="row">
          <div class="col-md-4 mb-3">
            <div class="promo-card p-3 h-100">
              <h2 class="text-center">5%</h2>
              <ul class="list-unstyled">
                <li>Save on all skincare products</li>
                <li>Valid until 30/09</li>
                <li>Use code: SKIN5</li>
              </ul>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="promo-card p-3 h-100">
              <h2 class="text-center">10%</h2>
              <ul class="list-unstyled">
                <li>Discount on makeup collections</li>
                <li>Valid until 15/10</li>
                <li>Use code: MAKE10</li>
              </ul>
            </div>
          </div>
          <div class="col-md-4 mb-3">
            <div class="promo-card p-3 h-100">
              <h2 class="text-center">20%</h2>
              <ul class="list-unstyled">
                <li>Exclusive offer on best sellers</li>
                <li>Valid until 31/10</li>
                <li>Use code: BEST20</li>
              </ul>
            </div>
          </div>
        </div>
        <div class="promo-btn-container text-center mt-3">
          <!-- Button to trigger the modal -->
          <button class="btn btn-success promo-btn" data-bs-toggle="modal" data-bs-target="#editPromoModal">
            Edit Promo Codes
          </button>
        </div>
      </div>

      <!-- Edit Promo Codes Modal -->
      <div class="modal fade" id="editPromoModal" tabindex="-1" aria-labelledby="editPromoModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <form id="promoForm">
              <div class="modal-header">
                <h5 class="modal-title" id="editPromoModalLabel">Edit Promo Codes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <!-- Promo Code 1 -->
                <div class="mb-3">
                  <label for="promo1Discount" class="form-label">Discount (%)</label>
                  <input type="number" class="form-control" id="promo1Discount" value="5" />
                </div>
                <div class="mb-3">
                  <label for="promo1Details" class="form-label">Details</label>
                  <input type="text" class="form-control" id="promo1Details" value="Save on all skincare products" />
                </div>
                <div class="mb-3">
                  <label for="promo1Valid" class="form-label">Valid Until</label>
                  <input type="date" class="form-control" id="promo1Valid" value="2023-09-30" />
                </div>
                <div class="mb-3">
                  <label for="promo1Code" class="form-label">Promo Code</label>
                  <input type="text" class="form-control" id="promo1Code" value="SKIN5" />
                </div>
                <hr />
                <!-- Promo Code 2 -->
                <div class="mb-3">
                  <label for="promo2Discount" class="form-label">Discount (%)</label>
                  <input type="number" class="form-control" id="promo2Discount" value="10" />
                </div>
                <div class="mb-3">
                  <label for="promo2Details" class="form-label">Details</label>
                  <input type="text" class="form-control" id="promo2Details" value="Discount on makeup collections" />
                </div>
                <div class="mb-3">
                  <label for="promo2Valid" class="form-label">Valid Until</label>
                  <input type="date" class="form-control" id="promo2Valid" value="2023-10-15" />
                </div>
                <div class="mb-3">
                  <label for="promo2Code" class="form-label">Promo Code</label>
                  <input type="text" class="form-control" id="promo2Code" value="MAKE10" />
                </div>
                <hr />
                <!-- Promo Code 3 -->
                <div class="mb-3">
                  <label for="promo3Discount" class="form-label">Discount (%)</label>
                  <input type="number" class="form-control" id="promo3Discount" value="20" />
                </div>
                <div class="mb-3">
                  <label for="promo3Details" class="form-label">Details</label>
                  <input type="text" class="form-control" id="promo3Details" value="Exclusive offer on best sellers" />
                </div>
                <div class="mb-3">
                  <label for="promo3Valid" class="form-label">Valid Until</label>
                  <input type="date" class="form-control" id="promo3Valid" value="2023-10-31" />
                </div>
                <div class="mb-3">
                  <label for="promo3Code" class="form-label">Promo Code</label>
                  <input type="text" class="form-control" id="promo3Code" value="BEST20" />
                </div>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-success">Save Changes</button>
              </div>
            </form>
          </div>
        </div>
      </div>

      <!-- Best Sellers Section -->
<div class="bg-white p-4 shadow-sm rounded stat-container">
  <h2 class="text-center">Best Sellers</h2>
  <div class="product-slider">
    <!-- Container for best seller cards -->
    <div id="bestSellersContainer" class="d-flex gap-3">
      <?php
      // Connect to the database and fetch best seller products
      $conn = new mysqli("localhost", "root", "", "db_cfn");
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }

      // Query best sellers by joining best_sellers table with tb_products
      $sql = "SELECT bs.bestseller_id, p.productID, p.product_name, p.category, p.product_image 
              FROM tb_bestsellers bs
              JOIN tb_products p ON bs.productID = p.productID
              ORDER BY bs.display_order ASC";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $imgSrc = !empty($row['product_image']) ? $row['product_image'] : "images/image.png";
              ?>
              <div class="product-card" data-product-id="<?= $row['productID']; ?>">
                <div class="product-image">
                  <img src="<?= $imgSrc; ?>" alt="<?= htmlspecialchars($row['product_name']); ?>" class="img-fluid" />
                </div>
                <div class="product-info">
                  <h5 class="product-name"><?= htmlspecialchars($row['product_name']); ?></h5>
                  <p class="product-category"><?= htmlspecialchars($row['category']); ?></p>
                  <div class="product-icons">
                    <i class="bi bi-trash delete-icon" onclick="deleteBestSeller(this)"></i>
                  </div>
                </div>
              </div>
              <?php
          }
      } else {
          echo "<p class='text-center'>No best sellers found.</p>";
      }
      $conn->close();
      ?>
    </div>
  </div>
  <div class="promo-btn-container mt-3 text-center">
    <button class="btn btn-success promo-btn" onclick="openModal()">Add Best Seller</button>
  </div>
</div>

<!-- Add Best Seller Modal -->
<div id="addBestSellerModal" class="modal-overlay" style="display:none;">
  <div class="modal-content">
    <h3 class="text-center">Select a Product to Add as Best Seller</h3>
    <select id="productDropdown" class="form-select">
      <option value="" disabled selected>Select a product</option>
      <?php
      // Dynamically load product options from tb_products
      $conn = new mysqli("localhost", "root", "", "db_cfn");
      if ($conn->connect_error) {
          die("Connection failed: " . $conn->connect_error);
      }
      $sql = "SELECT productID, product_name FROM tb_products";
      $result = $conn->query($sql);
      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              echo '<option value="' . $row['productID'] . '">' . htmlspecialchars($row['product_name']) . '</option>';
          }
      }
      $conn->close();
      ?>
    </select>
    <div class="modal-buttons mt-3">
      <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
      <button class="btn btn-success" onclick="confirmBestSeller()">Confirm</button>
    </div>
  </div>
</div>

<!-- JavaScript for Best Seller Functionality -->
<script>
  // Open the Best Seller Modal
  function openModal() {
    document.getElementById("addBestSellerModal").style.display = "flex";
  }
  // Close the Best Seller Modal
  function closeModal() {
    document.getElementById("addBestSellerModal").style.display = "none";
  }
  // Confirm Best Seller: Send a POST request to add the best seller and update the DOM
  function confirmBestSeller() {
    let productDropdown = document.getElementById("productDropdown");
    let selectedValue = productDropdown.value;
    if (!selectedValue) {
      alert("Please select a product.");
      return;
    }
    let selectedText = productDropdown.options[productDropdown.selectedIndex].text;
    
    // Send AJAX request to add the best seller (endpoint: add_bestseller.php)
    fetch("addbestseller.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({ productID: selectedValue })
    })
    .then(response => response.json())
    .then(data => {
      if (data.success) {
        // Create a new best seller card dynamically
        let card = document.createElement("div");
        card.className = "product-card";
        card.setAttribute("data-product-id", selectedValue);
        card.innerHTML = `
          <div class="product-image">
            <img src="images/image.png" alt="${selectedText}" class="img-fluid" />
          </div>
          <div class="product-info">
            <h5 class="product-name">${selectedText}</h5>
            <p class="product-category">Default Category</p>
            <div class="product-icons">
              <i class="bi bi-trash delete-icon" onclick="deleteBestSeller(this)"></i>
            </div>
          </div>
        `;
        document.getElementById("bestSellersContainer").appendChild(card);
        productDropdown.selectedIndex = 0;
        closeModal();
      } else {
        alert("Error adding best seller: " + (data.error || "Unknown error"));
      }
    })
    .catch(error => {
      console.error("Error adding best seller:", error);
      alert("Error adding best seller.");
    });
  }

  // Delete Best Seller via AJAX when the trash icon is clicked
  function deleteBestSeller(element) {
    if (confirm("Are you sure you want to delete this best seller?")) {
      let card = element.closest('.product-card');
      let productId = card.getAttribute("data-product-id");
      fetch("delete_bestseller.php?id=" + productId, {
        method: "DELETE"
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          alert("Best seller deleted successfully.");
          card.remove();
        } else {
          alert("Error deleting best seller: " + (data.error || "Unknown error"));
        }
      })
      .catch(error => {
        console.error("Error deleting best seller:", error);
        alert("Error deleting best seller.");
      });
    }
  }
</script>


</body>
</html>

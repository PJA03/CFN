<?php
require_once 'auth_check.php'; // Ensures user is logged in with either admin or superadmin role
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;700&family=Bebas+Neue&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
  <link rel="stylesheet" href="content.css" />
  <title>Admin Content Management</title>
</head>
<body>
  <div class="d-flex flex-wrap">
    <!-- Sidebar -->
    <div class="col-md-2 sidebar d-flex flex-column p-3 d-none d-md-flex" id="sidebar">
      <img src="images/cfn_logo.png" alt="Naturale Logo" class="img-fluid mb-3">
      <nav class="nav flex-column">
        <a class="nav-link" href="manageproductsA.php">Products</a>
        <a class="nav-link" href="managecontentA.php">Content</a>
        <a class="nav-link" href="manageordersA.php">Orders</a>
        <a class="nav-link" href="analytics.php">Analytics</a>
        <a class="nav-link" href="manageuser.php">Users</a>
      </nav>
      <div class="mt-auto">
        <hr>
        <div class="admin-name d-flex align-items-center">
          <i class="bi bi-person-circle fs-4 me-2"></i>
          <div class="d-flex align-items-center gap-2">
            <span class="adminuser"><?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?> User</span>
            <a href="/CFN/e-com/logout.php" class="btn btn-danger btn-sm" id="logout">Logout</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Mobile Menu -->
    <div class="collapse navbar-collapse d-md-none bg-dark text-white p-3" id="mobileSidebar">
      <img src="images/cfn_logo.png" alt="Naturale Logo" class="img-fluid mb-3" style="max-width: 100px;">
      <nav class="nav flex-column">
        <a class="nav-link" href="manageproductsA.php">Products</a>
        <a class="nav-link" href="managecontentA.php">Content</a>
        <a class="nav-link" href="manageordersA.php">Orders</a>
        <a class="nav-link" href="analytics.php">Analytics</a>
        <a class="nav-link" href="manageuser.php">Users</a>
      </nav>
      <hr class="bg-white">
      <div class="d-flex align-items-center mb-3">
        <i class="bi bi-person-circle fs-4 me-2"></i>
        <div class="d-flex align-items-center gap-2">
          <span class="adminuser"><?php echo htmlspecialchars(ucfirst($_SESSION['role'])); ?> User</span>
          <a href="/CFN/e-com/logout.php" class="btn btn-danger btn-sm" id="logout">Logout</a>
        </div>
      </div>
    </div>

    <!-- Main Content -->
    <div class="col-md-10 p-4 main-content">
      <h1 class="welcome-text">Welcome Back, Superadmin!</h1>
      <h3 class="dashboard-title text-center">Content Management Dashboard</h3>

      <!-- Stat Cards -->
      <?php
      require_once '../conn.php';

      // Pending Orders (updated to use AND instead of OR)
      $pendingQuery = "SELECT COUNT(*) as pending FROM tb_orders WHERE status = 'Waiting for Payment' AND isApproved = 0";
      $pendingResult = $conn->query($pendingQuery);
      $pendingOrders = $pendingResult->fetch_assoc()['pending'] ?? 0;

      // Top Selling Product with Variants
      $topSellingQuery = "SELECT oi.product_name, oi.variant_id, pv.variant_name, SUM(oi.quantity) as total_sold 
      FROM tb_order_items oi
      JOIN tb_orders o ON oi.orderID = o.orderID
      LEFT JOIN tb_productvariants pv ON oi.productID = pv.productID AND oi.variant_id = pv.variant_id
      WHERE o.status != 'Cancelled' 
      AND o.isApproved = 1 
      GROUP BY oi.productID, oi.product_name, oi.variant_id, pv.variant_name 
      ORDER BY total_sold DESC 
      LIMIT 1";
      $topSellingResult = $conn->query($topSellingQuery);
      $topSelling = $topSellingResult->fetch_assoc();
      $topSellingProduct = $topSelling ? ($topSelling['product_name'] . ($topSelling['variant_name'] ? " ({$topSelling['variant_name']})" : "")) : 'N/A';

      // Month Revenue (updated to only count approved payments)
      $monthRevenueQuery = "SELECT SUM(price_total) as revenue 
                            FROM tb_orders 
                            WHERE status != 'Cancelled' 
                            AND isApproved = 1 
                            AND MONTH(order_date) = MONTH(CURDATE()) 
                            AND YEAR(order_date) = YEAR(CURDATE())";
      $monthRevenueResult = $conn->query($monthRevenueQuery);
      $monthRevenue = $monthRevenueResult->fetch_assoc()['revenue'] ?? 0;
      $monthRevenueFormatted = '₱' . number_format($monthRevenue, 2);

      // All Delivered Orders
      $deliveredQuery = "SELECT COUNT(*) as delivered 
                         FROM tb_orders 
                         WHERE status = 'Delivered'";
      $deliveredResult = $conn->query($deliveredQuery);
      $totalDelivered = $deliveredResult->fetch_assoc()['delivered'] ?? 0;

      // Total Orders
      $totalOrdersQuery = "SELECT COUNT(*) as total FROM tb_orders";
      $totalOrdersResult = $conn->query($totalOrdersQuery);
      $totalOrders = $totalOrdersResult->fetch_assoc()['total'] ?? 0;
      ?>

      <div class="bg-white p-4 shadow-sm rounded stat-container">
        <div class="row mb-4 justify-content-center">
          <div class="col-md-2 stat-card">
            <div class="stat-title">Pending Orders</div>
            <div class="value"><?php echo $pendingOrders; ?></div>
          </div>
          <div class="col-md-2 stat-card">
            <div class="stat-title">Top Selling</div>
            <div class="value"><?php echo htmlspecialchars($topSellingProduct); ?></div>
          </div>
          <div class="col-md-2 stat-card">
            <div class="stat-title">Month Revenue</div>
            <div class="value"><?php echo $monthRevenueFormatted; ?></div>
          </div>
          <div class="col-md-2 stat-card">
            <div class="stat-title">All Delivered</div>
            <div class="value"><?php echo $totalDelivered; ?></div>
          </div>
          <div class="col-md-2 stat-card">
            <div class="stat-title">Total Orders</div>
            <div class="value"><?php echo $totalOrders; ?></div>
          </div>
        </div>
      </div>

      <!-- Promo Codes Section -->
      <div class="promo-section bg-white p-4 shadow-sm rounded stat-container">
        <h2 class="text-center mb-4">Promo Codes</h2>
        <div class="row" id="voucherContainer">
          <?php
          $voucherQuery = "SELECT * FROM tb_vouchers";
          $voucherResult = $conn->query($voucherQuery);
          if ($voucherResult->num_rows > 0) {
            while ($voucher = $voucherResult->fetch_assoc()) {
              ?>
              <div class="col-md-4 mb-3">
                <div class="promo-card p-3 h-100" data-voucher-id="<?php echo $voucher['voucherID']; ?>">
                  <h2 class="text-center"><?php echo number_format($voucher['discount'], 0); ?>%</h2>
                  <ul class="list-unstyled">
                    <li><?php echo htmlspecialchars($voucher['details']); ?></li>
                    <li>Valid until <?php echo date('d/m/Y', strtotime($voucher['valid_until'])); ?></li>
                    <li>Use code: <?php echo htmlspecialchars($voucher['code']); ?></li>
                  </ul>
                  <div class="text-center">
                    <button class="btn btn-primary btn-sm edit-voucher-btn" onclick="editVoucher(<?php echo $voucher['voucherID']; ?>)">Edit</button>
                    <button class="btn btn-danger btn-sm delete-voucher-btn" onclick="deleteVoucher(<?php echo $voucher['voucherID']; ?>)">Delete</button>
                  </div>
                </div>
              </div>
              <?php
            }
          } else {
            echo '<p class="text-center">No vouchers available.</p>';
          }
          ?>
        </div>
        <div class="promo-btn-container text-center mt-3">
          <button class="btn btn-success promo-btn" onclick="openAddVoucherModal()">Add Voucher</button>
        </div>
      </div>

      <!-- Add/Edit Voucher Modal -->
      <div id="voucherModal" class="modal-overlay" style="display:none;">
        <div class="modal-content">
          <h3 class="modal-title" id="voucherModalTitle">Add New Voucher</h3>
          <form id="voucherForm">
            <input type="hidden" id="voucherID" name="voucherID">
            <div class="modal-field">
              <label for="discount" class="form-label">Discount (%)</label>
              <input type="number" class="form-control" id="discount" name="discount" min="1" max="100" required>
            </div>
            <div class="modal-field">
              <label for="details" class="form-label">Details</label>
              <input type="text" class="form-control" id="details" name="details" required>
            </div>
            <div class="modal-field">
              <label for="validUntil" class="form-label">Valid Until</label>
              <input type="date" class="form-control" id="validUntil" name="validUntil" required>
            </div>
            <div class="modal-field">
              <label for="code" class="form-label">Voucher Code</label>
              <input type="text" class="form-control" id="code" name="code" required>
            </div>
            <div class="modal-buttons">
              <button type="submit" class="btn btn-success modal-btn">Save</button>
              <button type="button" class="btn btn-secondary modal-btn" onclick="closeVoucherModal()">Cancel</button>
            </div>
          </form>
        </div>
      </div>

      <!-- Best Sellers Section -->
      <div class="bg-white p-4 shadow-sm rounded stat-container">
        <h2 class="text-center">Best Sellers</h2>
        <div class="product-slider">
          <div id="bestSellersContainer" class="d-flex gap-3">
            <?php
            $sql = "SELECT bs.bestseller_id, p.productID, p.product_name, p.product_image, p.category 
            FROM tb_bestsellers bs
            JOIN tb_products p ON bs.productID = p.productID
            ORDER BY bs.display_order ASC";
             $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                $imgSrc = !empty($row['product_image']) ? "../e-com/{$row['product_image']}" : "../e-com/uploads/default.png";
                ?>
                <div class="product-card" data-bestseller-id="<?= $row['bestseller_id']; ?>" data-product-id="<?= $row['productID']; ?>">
                  <div class="product-image">
                    <img src="<?= $imgSrc; ?>" alt="<?= htmlspecialchars($row['product_name']); ?>" class="img-fluid" onerror="this.src='../e-com/uploads/default.png';" />
                  </div>
                  <div class="product-info">
                    <h5 class="product-name"><?= htmlspecialchars($row['product_name']); ?></h5>
                    <p class="product-category"><?= htmlspecialchars($row['category']); ?></p>
                    <div class="product-icons">
                      <i class="bi bi-trash delete-icon" onclick="deleteBestSeller(<?= $row['bestseller_id']; ?>)"></i>
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
          <h3 class="modal-title">Select a Product to Add as Best Seller</h3>
          <select id="productDropdown" class="form-select">
            <option value="" disabled selected>Select a product</option>
            <?php
            $conn = new mysqli("localhost", "root", "", "db_cfn");
            if ($conn->connect_error) {
              die("Connection failed: " . $conn->connect_error);
            }
            $sql = "SELECT productID, product_name, product_image, category FROM tb_products";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                echo '<option value="' . $row['productID'] . '" data-image="' . htmlspecialchars($row['product_image']) . '" data-category="' . htmlspecialchars($row['category']) . '">' . htmlspecialchars($row['product_name']) . '</option>';
              }
            }
            $conn->close();
            ?>
          </select>
          <div class="modal-buttons mt-3">
            <button class="btn btn-success modal-btn" onclick="confirmBestSeller()">Confirm</button>
            <button class="btn btn-secondary modal-btn" onclick="closeModal()">Cancel</button>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <!-- SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <!-- JavaScript for Promo Codes and Best Sellers -->
  <script>

<?php if ($_SESSION['role'] !== 'superadmin'): ?>
      document.addEventListener('DOMContentLoaded', function() {
        Swal.fire({
          icon: 'error',
          title: 'Access Denied',
          text: 'You are not authorized to access this page!',
          confirmButtonText: 'OK',
          allowOutsideClick: false
        }).then(() => {
          window.location.href = 'manageordersA.php'; // Redirect to an admin-accessible page
        });
      });
    <?php endif; ?>
    // Voucher Modal Functions
    function openAddVoucherModal() {
      console.log("Opening voucher modal");
      const modal = document.getElementById('voucherModal');
      modal.style.display = 'flex';
      modal.classList.add('show');
      document.getElementById('voucherModalTitle').textContent = 'Add New Voucher';
      document.getElementById('voucherID').value = '';
      document.getElementById('discount').value = '';
      document.getElementById('details').value = '';
      document.getElementById('validUntil').value = '';
      document.getElementById('code').value = '';
    }

    function closeVoucherModal() {
      console.log("Closing voucher modal");
      const modal = document.getElementById('voucherModal');
      modal.classList.remove('show');
      setTimeout(() => { modal.style.display = 'none'; }, 300);
    }

    function editVoucher(voucherID) {
      console.log("Editing voucher with ID:", voucherID);
      fetch(`get_voucher.php?id=${voucherID}`)
        .then(response => {
          console.log("Response status:", response.status);
          console.log("Response headers:", [...response.headers.entries()]);
          if (!response.ok) {
            return response.text().then(text => {
              throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
            });
          }
          return response.text();
        })
        .then(text => {
          console.log("Raw response:", text);
          try {
            const data = JSON.parse(text);
            console.log("Parsed voucher data:", data);
            if (!data.voucherID) {
              throw new Error("Invalid voucher data: voucherID missing");
            }
            const modal = document.getElementById('voucherModal');
            document.getElementById('voucherModalTitle').textContent = 'Edit Voucher';
            document.getElementById('voucherID').value = data.voucherID;
            document.getElementById('discount').value = data.discount;
            document.getElementById('details').value = data.details;
            document.getElementById('validUntil').value = data.valid_until;
            document.getElementById('code').value = data.code;
            modal.style.display = 'flex';
            modal.classList.add('show');
            console.log("Modal should be visible now");
          } catch (e) {
            throw new Error(`JSON parse error: ${e.message}, Raw response: ${text}`);
          }
        })
        .catch(error => {
          console.error('Error fetching voucher:', error.message);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load voucher data: ' + error.message,
            confirmButtonText: 'OK'
          });
        });
    }

    function deleteVoucher(voucherID) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to delete this voucher?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'No, cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`delete_voucher.php?id=${voucherID}`, { method: 'DELETE' })
            .then(response => response.json())
            .then(data => {
              if (data.success) {
                document.querySelector(`.promo-card[data-voucher-id="${voucherID}"]`).parentElement.remove();
                if (document.querySelectorAll('.promo-card').length === 0) {
                  document.getElementById('voucherContainer').innerHTML = '<p class="text-center">No vouchers available.</p>';
                }
                Swal.fire({
                  icon: 'success',
                  title: 'Deleted',
                  text: 'Voucher deleted successfully!',
                  confirmButtonText: 'OK'
                });
              } else {
                Swal.fire({
                  icon: 'error',
                  title: 'Error',
                  text: 'Error deleting voucher: ' + (data.error || 'Unknown error'),
                  confirmButtonText: 'OK'
                });
              }
            })
            .catch(error => {
              console.error('Error deleting voucher:', error);
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to delete voucher: ' + error.message,
                confirmButtonText: 'OK'
              });
            });
        }
      });
    }

    document.getElementById('voucherForm').addEventListener('submit', function(e) {
      e.preventDefault();
      console.log("Form submitted");
      const voucherID = document.getElementById('voucherID').value;
      const data = {
        voucherID: voucherID || null,
        discount: document.getElementById('discount').value,
        details: document.getElementById('details').value,
        valid_until: document.getElementById('validUntil').value,
        code: document.getElementById('code').value
      };
      console.log("Sending data:", data);
      const url = voucherID ? 'update_voucher.php' : 'add_voucher.php';
      fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      })
      .then(response => {
        console.log("Response status:", response.status);
        return response.text();
      })
      .then(text => {
        console.log("Raw response:", text);
        try {
          const data = JSON.parse(text);
          console.log("Parsed data:", data);
          if (data.success) {
            Swal.fire({
              icon: 'success',
              title: 'Success',
              text: 'Voucher saved successfully!',
              confirmButtonText: 'OK'
            }).then(() => {
              location.reload();
            });
          } else {
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Error saving voucher: ' + (data.error || 'Unknown error'),
              confirmButtonText: 'OK'
            });
          }
        } catch (e) {
          console.error("Parse error:", e);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Invalid response from server: ' + text,
            confirmButtonText: 'OK'
          });
        }
      })
      .catch(error => {
        console.error('Error saving voucher:', error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to save voucher: ' + error.message,
          confirmButtonText: 'OK'
        });
      });
    });

    // Best Seller Functions
    function openModal() {
      console.log("Opening best seller modal");
      const modal = document.getElementById("addBestSellerModal");
      modal.style.display = "flex";
      modal.classList.add('show');
    }

    function closeModal() {
      console.log("Closing best seller modal");
      const modal = document.getElementById("addBestSellerModal");
      modal.classList.remove('show');
      setTimeout(() => { modal.style.display = 'none'; }, 300);
    }

    function confirmBestSeller() {
      let productDropdown = document.getElementById("productDropdown");
      let selectedValue = productDropdown.value;
      if (!selectedValue) {
        Swal.fire({
          icon: 'warning',
          title: 'Warning',
          text: 'Please select a product.',
          confirmButtonText: 'OK'
        });
        return;
      }
      let selectedText = productDropdown.options[productDropdown.selectedIndex].text;
      let selectedImage = productDropdown.options[productDropdown.selectedIndex].getAttribute("data-image");
      let selectedCategory = productDropdown.options[productDropdown.selectedIndex].getAttribute("data-category");
      let imgSrc = selectedImage ? `../e-com/${selectedImage}` : "../e-com/uploads/default.png";

      console.log("Checking if product with productID:", selectedValue, "is already in best sellers");

      // Check if the product is already in the best sellers
      fetch(`check_bestseller.php?productID=${selectedValue}`)
        .then(response => {
          if (!response.ok) {
            return response.text().then(text => {
              throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
            });
          }
          return response.json();
        })
        .then(data => {
          if (!data.success) {
            throw new Error(data.message || 'Failed to check best seller status');
          }

          if (data.in_best_sellers) {
            // Product is already in best sellers, show error message and stop
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: `${selectedText} is already in the Best Sellers list. Please select a different product.`,
              confirmButtonText: 'OK'
            });
            return; // Do not proceed with adding
          }

          // Proceed with adding the best seller
          console.log("Adding best seller with productID:", selectedValue);
          fetch("addbestseller.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ productID: selectedValue })
          })
          .then(response => {
            console.log("Response status:", response.status);
            if (!response.ok) {
              return response.text().then(text => {
                throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
              });
            }
            return response.text();
          })
          .then(text => {
            console.log("Raw response from addbestseller.php:", text);
            let data;
            try {
              data = JSON.parse(text);
              console.log("Parsed JSON:", data);
            } catch (e) {
              console.error("Failed to parse JSON:", e.message);
              throw new Error(`JSON parse error: ${e.message}, Raw response: ${text}`);
            }
            if (data.success) {
              let card = document.createElement("div");
              card.className = "product-card";
              card.setAttribute("data-bestseller-id", data.bestseller_id || Date.now());
              card.setAttribute("data-product-id", selectedValue);
              card.innerHTML = `
                <div class="product-image">
                  <img src="${imgSrc}" alt="${selectedText}" class="img-fluid" onerror="this.src='../e-com/uploads/default.png';">
                </div>
                <div class="product-info">
                  <h5 class="product-name">${selectedText}</h5>
                  <p class="product-category">${selectedCategory || 'Default Category'}</p>
                  <div class="product-icons">
                    <i class="bi bi-trash delete-icon" onclick="deleteBestSeller(${data.bestseller_id || Date.now()})"></i>
                  </div>
                </div>
              `;
              document.getElementById("bestSellersContainer").appendChild(card);
              productDropdown.selectedIndex = 0;
              closeModal();
              Swal.fire({
                icon: 'success',
                title: 'Success',
                text: 'Best seller added successfully!',
                confirmButtonText: 'OK'
              });
              console.log("Best seller added successfully to UI");
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error adding best seller: ' + (data.error || 'Unknown server error'),
                confirmButtonText: 'OK'
              });
            }
          })
          .catch(error => {
            console.error("Fetch error in confirmBestSeller:", error.message);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Failed to add best seller: ' + error.message,
              confirmButtonText: 'OK'
            });
          });
        })
        .catch(error => {
          console.error("Error checking best seller:", error.message);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to check best seller status: ' + error.message,
            confirmButtonText: 'OK'
          });
        });
    }

    function deleteBestSeller(bestsellerId) {
      Swal.fire({
        title: 'Are you sure?',
        text: 'Do you want to delete this best seller?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'No, cancel'
      }).then((result) => {
        if (result.isConfirmed) {
          console.log("Deleting best seller with ID:", bestsellerId);
          fetch(`delete_bestseller.php?id=${bestsellerId}`, {
            method: "DELETE"
          })
          .then(response => {
            console.log("Response status:", response.status);
            if (!response.ok) {
              return response.text().then(text => {
                throw new Error(`HTTP error! Status: ${response.status}, Response: ${text}`);
              });
            }
            return response.text();
          })
          .then(text => {
            console.log("Raw response:", text);
            let data;
            try {
              data = JSON.parse(text);
            } catch (e) {
              if (text.includes('"success":true')) {
                data = { success: true };
              } else {
                throw new Error(`JSON parse error: ${e.message}`);
              }
            }
            if (data.success) {
              document.querySelector(`.product-card[data-bestseller-id="${bestsellerId}"]`).remove();
              if (!document.querySelectorAll('.product-card').length) {
                document.getElementById('bestSellersContainer').innerHTML = '<p class="text-center">No best sellers found.</p>';
              }
              Swal.fire({
                icon: 'success',
                title: 'Deleted',
                text: 'Best seller deleted successfully!',
                confirmButtonText: 'OK'
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error deleting best seller: ' + (data.error || 'Unknown error'),
                confirmButtonText: 'OK'
              });
            }
          })
          .catch(error => {
            console.error("Error deleting best seller:", error);
            if (!document.querySelector(`.product-card[data-bestseller-id="${bestsellerId}"]`)) {
              console.log("Deletion succeeded despite JSON error");
              Swal.fire({
                icon: 'success',
                title: 'Deleted',
                text: 'Best seller deleted successfully!',
                confirmButtonText: 'OK'
              });
            } else {
              Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Failed to delete best seller: ' + error.message,
                confirmButtonText: 'OK'
              });
            }
          });
        }
      });
    }
  </script>
</body>
</html>
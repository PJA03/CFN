<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Manage Orders</title>

  <!-- Bootstrap CSS -->
  <link
    href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
    rel="stylesheet"
  />

  <!-- Additional Fonts/Icons -->
  <link
    href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap"
    rel="stylesheet"
  />
  <link
    rel="stylesheet"
    href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css"
  />

  <!-- Optional Custom CSS -->
  <link rel="stylesheet" href="style2.css" />

  <style>
    /* ----- POPUP STYLES (Modal-like) ----- */
    .popup, .zoom-popup {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      display: none; /* hidden by default */
      align-items: center; 
      justify-content: center;
      background-color: rgba(0, 0, 0, 0.5);
      z-index: 9999;
    }
    .popup-content {
      background-color: #fff;
      border-radius: 8px;
      padding: 1.5rem;
      max-width: 900px;
      width: 90%;
      position: relative;
    }
    .popup-content .close {
      position: absolute;
      top: 1rem; right: 1rem;
      font-size: 1.5rem;
      cursor: pointer;
    }

    /* For table scrolling if needed */
    .table-container {
      overflow-x: auto;
      margin-top: 1rem;
    }
    .table-container table {
      width: 100%;
      table-layout: auto;
    }
    .table-container th,
    .table-container td {
      min-width: 100px;
      white-space: normal;
      word-wrap: break-word;
      text-align: left;
    }

    /* Receipt & Zoom popups */
    #receiptPopup .popup-content {
      max-width: 600px;
    }
    #zoomPopup {
      z-index: 10000; /* place above receipt popup if both appear */
    }
    #zoomPopup img {
      max-width: 90%; 
      max-height: 90%;
      object-fit: contain;
    }
    /* Subtle styling for the "Review Payment" link */
    a#reviewPayment {
      color: #0d6efd;
      text-decoration: none;
      font-weight: 500;
    }
    a#reviewPayment:hover {
      text-decoration: underline;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
      <!-- Sidebar -->
      <div class="col-md-2 sidebar d-flex flex-column p-3">
        <img src="images/cfn_logo.png" alt="Naturale Logo" class="img-fluid mb-3" />
        <nav class="nav flex-column">
          <a class="nav-link" href="manageproductsA.php">Products</a>
          <a class="nav-link" href="managecontentA.php">Content</a>
          <a class="nav-link active" href="manageordersA.php">Orders</a>
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
        <h3 class="mt-4 text-center">Orders Table</h3>
        <div class="d-flex justify-content-end align-items-center mb-3">
          <!-- Search Input -->
          <input type="text" id="searchOrder" class="form-control w-25 me-2" placeholder="Search Order" />
        </div>

        <!-- Orders Table -->
        <div class="bg-white p-4 rounded shadow-sm">
          <table class="table table-bordered text-center">
            <thead>
              <tr class="table-success">
                <th>Order ID</th>
                <th>Number of Items</th>
                <th>Total</th>
                <th>Status</th>
                <th>Tracking Link</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="ordersTable">
              <!-- Will be loaded via AJAX from fetchorders.php -->
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ORDER DETAILS POPUP -->
  <div id="orderPopup" class="popup">
    <div class="popup-content">
      <span class="close" onclick="closePopup()">&times;</span>
      <h4>Order Details</h4>
      <div class="table-container">
        <table class="table table-bordered">
          <thead class="table-success">
            <tr>
              <th>Order Date</th>
              <th>Product ID</th>
              <th>Product Name</th>
              <th>User ID</th>
              <th>Email</th>
              <th>Quantity</th>
              <th>Payment Option</th>
              <th>Status</th>
              <th>Total</th>
              <th>Approved</th>
              <th>Tracking Link</th>
            </tr>
          </thead>
          <tbody>
            <tr>
              <!-- Plain text display instead of input boxes -->
              <td><div id="orderDateDisplay" class="form-control-plaintext"></div></td>
              <td><div id="productIDDisplay" class="form-control-plaintext"></div></td>
              <td><div id="productNameDisplay" class="form-control-plaintext"></div></td>
              <td><div id="userIDDisplay" class="form-control-plaintext"></div></td>
              <td><div id="emailDisplay" class="form-control-plaintext"></div></td>
              <td><div id="quantityDisplay" class="form-control-plaintext"></div></td>
              <td><div id="paymentOptionDisplay" class="form-control-plaintext"></div></td>
              <td>
                <select id="status" name="status" class="form-select">
                  <option value="invalidate">Invalidate</option>
                  <option value="pending">Pending</option>
                  <option value="shipped">Shipped</option>
                  <option value="delivered">Delivered</option>
                </select>
              </td>
              <td><div id="totalDisplay" class="form-control-plaintext"></div></td>
              <td>
                <div>
                  <input type="checkbox" id="confirmPayment" />
                  <label for="confirmPayment">Approved</label>
                  <br />
                  <a href="#" id="reviewPayment" class="btn btn-link p-0" style="font-size: 0.9rem;">Review Payment</a>
                </div>
              </td>
              <td>
                <input type="text" id="trackingLink" name="trackingLink" placeholder="Enter tracking link" class="form-control" />
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="mt-3 text-end">
        <button type="button" class="btn btn-primary" onclick="saveChanges()">Save changes</button>
        <button type="button" class="btn btn-secondary" onclick="discardChanges()">Discard changes</button>
      </div>
    </div>
  </div>

  <!-- RECEIPT POPUP -->
  <div id="receiptPopup" class="popup">
    <div class="popup-content">
      <span class="close" onclick="closeReceiptPopup()">&times;</span>
      <h4>Payment Receipt</h4>
      <div class="text-center">
        <img id="receiptImage" src="images/gkas.jpeg" alt="Receipt Image" class="img-fluid" style="max-width: 100%; max-height: 500px; cursor: pointer;" onclick="openZoomedImage()" />
      </div>
    </div>
  </div>

  <!-- ZOOMED IMAGE POPUP -->
  <div id="zoomPopup" class="zoom-popup">
    <span class="close" onclick="closeZoomedImage()">&times;</span>
    <img id="zoomedImage" src="" alt="Zoomed Image" />
  </div>

  <!-- Bootstrap & SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    let isChanged = false;

    // 1. Load orders from fetchorders.php
    function loadOrders(query = "") {
      document.getElementById("ordersTable").innerHTML =
        "<tr><td colspan='6'>Loading...</td></tr>";
      fetch(`fetchorders.php?search=${query}`)
        .then(response => response.text())
        .then(data => {
          document.getElementById("ordersTable").innerHTML = data;
        })
        .catch(error => {
          console.error("Error loading orders:", error);
          document.getElementById("ordersTable").innerHTML =
            "<tr><td colspan='6' class='text-danger'>Failed to load data</td></tr>";
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
      loadOrders();
      document.getElementById("searchOrder").addEventListener("input", function () {
        loadOrders(this.value);
      });
    });

    // 2. Open the details popup
    function openPopup(orderID) {
      fetch(`getorderdetails.php?orderID=${orderID}`)
        .then(response => {
          if (!response.ok) {
            throw new Error("Network response was not ok");
          }
          return response.json();
        })
        .then(data => {
          // Populate plain text fields using textContent
          document.getElementById("orderDateDisplay").textContent = data.order_date || "";
          document.getElementById("productIDDisplay").textContent = data.productID || "";
          document.getElementById("productNameDisplay").textContent = data.product_name || "";
          document.getElementById("userIDDisplay").textContent = data.user_id || "";
          document.getElementById("emailDisplay").textContent = data.email || "";
          document.getElementById("quantityDisplay").textContent = data.quantity || "";
          document.getElementById("paymentOptionDisplay").textContent = data.payment_option || "";
          document.getElementById("totalDisplay").textContent = "₱" + (data.price_total || "");
          
          // Editable fields
          document.getElementById("status").value = data.status || "pending";
          document.getElementById("confirmPayment").checked = (data.isApproved == 1);
          document.getElementById("trackingLink").value = data.trackingLink || "";

          isChanged = false;
          document.getElementById("orderPopup").style.display = "flex";
        })
        .catch(error => {
          console.error("Error fetching order details:", error);
        });
    }

    // 3. Close the details popup
    function closePopup() {
      if (isChanged) {
        const confirmClose = confirm("You have unsaved changes. Do you want to discard them?");
        if (!confirmClose) return;
      }
      document.getElementById("orderPopup").style.display = "none";
    }

    function discardChanges() {
      const confirmDiscard = confirm("Are you sure you want to discard all changes?");
      if (confirmDiscard) {
        isChanged = false;
        closePopup();
      }
    }

    // Mark changes to detect unsaved modifications
    document.getElementById("status").addEventListener("change", () => { isChanged = true; });
    document.getElementById("confirmPayment").addEventListener("change", () => { isChanged = true; });
    document.getElementById("trackingLink").addEventListener("input", () => { isChanged = true; });

    // 4. Receipt popup
    document.getElementById("reviewPayment").addEventListener("click", (event) => {
      event.preventDefault();
      openReceiptPopup();
    });

    function openReceiptPopup() {
      document.getElementById("receiptPopup").style.display = "flex";
    }
    function closeReceiptPopup() {
      document.getElementById("receiptPopup").style.display = "none";
    }

    // 5. Zoomed image
    function openZoomedImage() {
      const receiptImageSrc = document.getElementById("receiptImage").src;
      document.getElementById("zoomedImage").src = receiptImageSrc;
      document.getElementById("zoomPopup").style.display = "flex";
    }
    function closeZoomedImage() {
      document.getElementById("zoomPopup").style.display = "none";
    }

    // 6. Save changes to the order
    function saveChanges() {
      const status = document.getElementById("status").value;
      const isPaymentConfirmed = document.getElementById("confirmPayment").checked;
      const trackingLink = document.getElementById("trackingLink").value;

      fetch("updateorder.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          // If your backend still needs orderID, store it somewhere hidden or in a global variable.
          status: status,
          isApproved: isPaymentConfirmed,
          trackingLink: trackingLink,
        }),
      })
        .then(response => response.json())
        .then(result => {
          if (result.success) {
            Swal.fire({
              title: "Success!",
              text: result.message,
              icon: "success",
              confirmButtonText: "OK",
            });
            // Optionally reload orders
          } else {
            Swal.fire({
              title: "Error",
              text: result.message,
              icon: "error",
              confirmButtonText: "OK",
            });
          }
        })
        .catch(error => {
          console.error("Error updating order:", error);
          Swal.fire({
            title: "Error",
            text: "An error occurred while updating the order.",
            icon: "error",
            confirmButtonText: "OK",
          });
        });

      isChanged = false;
      closePopup();
    }
  </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Manage Orders</title>

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />

  <!-- Additional Fonts/Icons -->
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />

  <!-- Optional Custom CSS -->
  <link rel="stylesheet" href="style2.css" />

  <style>
    .popup, .zoom-popup {
      position: fixed;
      top: 0; left: 0;
      width: 100%; height: 100%;
      display: none;
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
    #receiptPopup .popup-content {
      max-width: 600px;
    }
    #zoomPopup {
      z-index: 10000;
    }
    #zoomPopup img {
      max-width: 90%; 
      max-height: 90%;
      object-fit: contain;
    }
    a#reviewPayment {
      color: #0d6efd;
      text-decoration: none;
      font-weight: 500;
    }
    a#reviewPayment:hover {
      text-decoration: underline;
    }
    th.sortable {
      cursor: pointer;
    }
    th.sortable:hover {
      background-color: #f0f0f0;
    }
    th.sortable:after {
      content: " ↕";
      opacity: 0.5;
    }
    th.sorted-asc:after {
      content: " ↑";
    }
    th.sorted-desc:after {
      content: " ↓";
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
        <div class="d-flex justify-content-between align-items-center mb-3">
          <a href="manage_qr_codes.php" class="btn btn-primary">Manage QR Codes</a> <!-- Added here -->
          <div class="d-flex align-items-center">
            <select id="filterStatus" class="form-select me-2" style="width: auto;">
              <option value="">All Statuses</option>
              <option value="Waiting for Payment">Waiting for Payment</option>
              <option value="Processing">Processing</option>
              <option value="Shipped">Shipped</option>
              <option value="Delivered">Delivered</option>
              <option value="Cancelled">Cancelled</option>
            </select>
            <input type="text" id="searchOrder" class="form-control w-25 me-2" placeholder="Search Order" />
          </div>
        </div>

        <div class="bg-white p-4 rounded shadow-sm">
          <table class="table table-bordered text-center">
            <thead>
              <tr class="table-success">
                <th>Order ID</th>
                <th>Number of Items</th>
                <th class="sortable" onclick="sortTable('total')">Total</th>
                <th class="sortable" onclick="sortTable('status')">Status</th>
                <th>Tracking Link</th>
                <th>Address</th> <!-- New column header -->
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="ordersTable"></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>

  <!-- ORDER DETAILS POPUP -->
  <div id="orderPopup" class="popup">
    <div class="popup-content">
      <span class="close" onclick="closePopup()">×</span>
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
              <td><div id="orderDateDisplay" class="form-control-plaintext"></div></td>
              <td><div id="productIDDisplay" class="form-control-plaintext"></div></td>
              <td><div id="productNameDisplay" class="form-control-plaintext"></div></td>
              <td><div id="userIDDisplay" class="form-control-plaintext"></div></td>
              <td><div id="emailDisplay" class="form-control-plaintext"></div></td>
              <td><div id="quantityDisplay" class="form-control-plaintext"></div></td>
              <td><div id="paymentOptionDisplay" class="form-control-plaintext"></div></td>
              <td>
                <select id="status" name="status" class="form-select">
                  <option value="Waiting for Payment">Waiting for Payment</option>
                  <option value="Processing">Processing</option>
                  <option value="Shipped">Shipped</option>
                  <option value="Delivered">Delivered</option>
                  <option value="Cancelled">Cancelled</option>
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
      <span class="close" onclick="closeReceiptPopup()">×</span>
      <h4>Payment Receipt</h4>
      <div class="text-center">
        <img id="receiptImage" src="" alt="Receipt Image" class="img-fluid" style="max-width: 100%; max-height: 500px; cursor: pointer;" onclick="openZoomedImage()" />
      </div>
    </div>
  </div>

  <!-- ZOOMED IMAGE POPUP -->
  <div id="zoomPopup" class="zoom-popup">
    <span class="close" onclick="closeZoomedImage()">×</span>
    <img id="zoomedImage" src="" alt="Zoomed Image" />
  </div>

  <!-- Bootstrap & SweetAlert2 JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <script>
    let isChanged = false;
    let currentOrderID = null;
    let sortField = '';
    let sortOrder = 'asc';
    let originalStatus = ''; // To store the original status of the order

    // 1. Load orders from fetchorders.php
    function loadOrders(query = "", filterStatus = "", sort = '', order = 'asc') {
      document.getElementById("ordersTable").innerHTML = "<tr><td colspan='6'>Loading...</td></tr>";
      const url = `fetchorders.php?search=${encodeURIComponent(query)}&filter=${encodeURIComponent(filterStatus)}&sort=${encodeURIComponent(sort)}&order=${encodeURIComponent(order)}`;
      fetch(url)
        .then(response => response.text())
        .then(data => {
          document.getElementById("ordersTable").innerHTML = data;
          document.querySelectorAll('th.sortable').forEach(th => th.classList.remove('sorted-asc', 'sorted-desc'));
          if (sort) {
            const th = document.querySelector(`th.sortable[onclick="sortTable('${sort}')"]`);
            if (th) th.classList.add(sortOrder === 'asc' ? 'sorted-asc' : 'sorted-desc');
          }
        })
        .catch(error => {
          console.error("Error loading orders:", error);
          document.getElementById("ordersTable").innerHTML = "<tr><td colspan='6' class='text-danger'>Failed to load data</td></tr>";
        });
    }

    document.addEventListener("DOMContentLoaded", () => {
      loadOrders();
      document.getElementById("searchOrder").addEventListener("input", function () {
        loadOrders(this.value, document.getElementById("filterStatus").value, sortField, sortOrder);
      });
      document.getElementById("filterStatus").addEventListener("change", function () {
        loadOrders(document.getElementById("searchOrder").value, this.value, sortField, sortOrder);
      });
    });

    // 2. Sort and filter table
    function sortTable(field) {
      if (sortField === field) {
        sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        sortField = field;
        sortOrder = 'asc';
      }
      loadOrders(document.getElementById("searchOrder").value, document.getElementById("filterStatus").value, sortField, sortOrder);
    }

    // 3. Open the details popup
    function openPopup(orderID) {
      currentOrderID = orderID;
      fetch(`getorderdetails.php?orderID=${orderID}`)
        .then(response => {
          if (!response.ok) throw new Error("Network response was not ok");
          return response.json();
        })
        .then(data => {
          document.getElementById("orderDateDisplay").textContent = data.order_date || "";
          document.getElementById("productIDDisplay").textContent = data.productID || "";
          document.getElementById("productNameDisplay").textContent = data.product_name || "";
          document.getElementById("userIDDisplay").textContent = data.user_id || "";
          document.getElementById("emailDisplay").textContent = data.email || "";
          document.getElementById("quantityDisplay").textContent = data.quantity || "";
          document.getElementById("paymentOptionDisplay").textContent = data.payment_option || "";
          document.getElementById("totalDisplay").textContent = "₱" + (data.price_total || "");
          
          document.getElementById("status").value = data.status || "Waiting for Payment";
          originalStatus = data.status || "Waiting for Payment"; // Store original status
          document.getElementById("confirmPayment").checked = (data.isApproved == 1);
          document.getElementById("trackingLink").value = data.trackingLink || "";
          document.getElementById("receiptImage").src = data.payment_proof || "images/placeholder.jpg";

          isChanged = false;
          document.getElementById("orderPopup").style.display = "flex";
        })
        .catch(error => {
          console.error("Error fetching order details:", error);
          Swal.fire({
            title: "Error",
            text: "Failed to load order details.",
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    }

    // 4. Close the details popup
    function closePopup() {
      if (isChanged) {
        const confirmClose = confirm("You have unsaved changes. Do you want to discard them?");
        if (!confirmClose) return;
      }
      document.getElementById("orderPopup").style.display = "none";
      currentOrderID = null;
      originalStatus = '';
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

    // 5. Receipt popup
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

    // 6. Zoomed image
    function openZoomedImage() {
      const receiptImageSrc = document.getElementById("receiptImage").src;
      document.getElementById("zoomedImage").src = receiptImageSrc;
      document.getElementById("zoomPopup").style.display = "flex";
    }
    function closeZoomedImage() {
      document.getElementById("zoomPopup").style.display = "none";
    }

    // 7. Save changes to the order
    function saveChanges() {
      let status = document.getElementById("status").value;
      const isPaymentConfirmed = document.getElementById("confirmPayment").checked;
      const trackingLink = document.getElementById("trackingLink").value.trim();

      // Auto-update status based on conditions
      if (isPaymentConfirmed && status === "Waiting for Payment") {
        status = "Processing";
        document.getElementById("status").value = status;
      }
      if (trackingLink && (status === "Processing" || status === "Waiting for Payment")) {
        status = "Shipped";
        document.getElementById("status").value = status;
      }

      // Prevent setting to "Delivered" unless the order is "Shipped" and has a tracking link
      if (status === "Delivered" && originalStatus !== "Shipped") {
        Swal.fire({
          title: "Invalid Status Change",
          text: "Order must be in 'Shipped' status before marking as 'Delivered'.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }
      if (status === "Delivered" && !trackingLink) {
        Swal.fire({
          title: "Missing Tracking Link",
          text: "A tracking link is required before marking an order as 'Delivered'.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

      fetch("updateorder.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          orderID: currentOrderID,
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
            }).then(() => {
              loadOrders(document.getElementById("searchOrder").value, document.getElementById("filterStatus").value, sortField, sortOrder);
              closePopup();
            });
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
    }
  </script>
</body>
</html>
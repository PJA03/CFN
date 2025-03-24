<?php
require_once 'auth_check.php';
?>
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

  <!-- SweetAlert2 CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />

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
    /* Style for the Search Order textbox */
    #searchOrder {
      width: 200px; /* Fixed width for better layout */
      max-width: 100%; /* Responsive on smaller screens */
    }
    @media (max-width: 576px) {
      #searchOrder {
        width: 100%; /* Full width on small screens */
      }
      .d-flex.align-items-center {
        flex-direction: column; /* Stack elements vertically on small screens */
        gap: 10px; /* Add spacing between elements */
      }
      #filterStatus {
        width: 100%; /* Full width for the filter dropdown on small screens */
      }
    }
    /* Ensure SweetAlert2 appears on top */
    .swal2-container {
      z-index: 10001 !important;
    }
    /* Style for the address container */
    .address-container {
        display: flex;
        justify-content: space-between;
        align-items: center;
        width: 100%;
        gap: 10px; /* Space between address and icon */
    }
    /* Style for the address text */
    .address-text {
        flex: 1; /* Take up remaining space */
        word-wrap: break-word; /* Ensure long addresses wrap */
    }
    /* Style for the copy icon */
    .copy-icon {
        cursor: pointer;
        font-size: 1.2rem; /* Size of the icon */
        color: #6c757d; /* Bootstrap's secondary color */
        transition: color 0.2s; /* Smooth color transition on hover */
    }
    .copy-icon:hover {
        color: #0d6efd; /* Bootstrap's primary color on hover */
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
          <a href="/CFN/e-com/logout.php" class="btn btn-danger">Logout</a>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 p-4 main-content">
        <h3 class="mt-4 text-center">Orders Table</h3>
        <div class="d-flex justify-content-between align-items-center mb-3">
          <a href="manage_qr_codes.php" class="btn btn-primary">Manage QR Codes</a>
          <div class="d-flex align-items-center">
            <select id="filterStatus" class="form-select me-2" style="width: auto;">
              <option value="">All Statuses</option>
              <option value="Waiting for Payment">Waiting for Payment</option>
              <option value="Processing">Processing</option>
              <option value="Shipped">Shipped</option>
              <option value="Delivered">Delivered</option>
              <option value="Cancelled">Cancelled</option>
            </select>
            <input type="text" id="searchOrder" class="form-control" placeholder="Search Order" />
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
                <th>Address</th>
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
    let originalStatus = '';

    // 1. Load orders from fetchorders.php
    function loadOrders(query = "", filterStatus = "", sort = '', order = 'asc') {
      document.getElementById("ordersTable").innerHTML = "<tr><td colspan='7'>Loading...</td></tr>";
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
          document.getElementById("ordersTable").innerHTML = "<tr><td colspan='7' class='text-danger'>Failed to load data</td></tr>";
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load orders: ' + error.message,
            confirmButtonText: 'OK'
          });
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
          document.getElementById("totalDisplay").textContent = "₱" + (data.price_total || "0.00");
          
          document.getElementById("status").value = data.status || "Waiting for Payment";
          originalStatus = data.status || "Waiting for Payment";
          document.getElementById("confirmPayment").checked = (data.isApproved == 1);
          document.getElementById("trackingLink").value = data.trackingLink || "";
          
          // Fix the receipt image path
          const receiptPath = data.payment_proof ? `../uploads/receipts/${data.payment_proof}` : "images/placeholder.jpg";
          document.getElementById("receiptImage").src = receiptPath;

          isChanged = false;
          document.getElementById("orderPopup").style.display = "flex";
        })
        .catch(error => {
          console.error("Error fetching order details:", error);
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'Failed to load order details: ' + error.message,
            confirmButtonText: 'OK'
          });
        });
    }

    // 4. Close the details popup
    function closePopup() {
      if (isChanged) {
        Swal.fire({
          title: 'Unsaved Changes',
          text: 'You have unsaved changes. Do you want to discard them?',
          icon: 'warning',
          showCancelButton: true,
          confirmButtonColor: '#d33',
          cancelButtonColor: '#3085d6',
          confirmButtonText: 'Yes, discard',
          cancelButtonText: 'No, keep editing'
        }).then((result) => {
          if (result.isConfirmed) {
            document.getElementById("orderPopup").style.display = "none";
            currentOrderID = null;
            originalStatus = '';
            isChanged = false;
          }
        });
      } else {
        document.getElementById("orderPopup").style.display = "none";
        currentOrderID = null;
        originalStatus = '';
      }
    }

    function discardChanges() {
      Swal.fire({
        title: 'Discard Changes',
        text: 'Are you sure you want to discard all changes?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, discard',
        cancelButtonText: 'No, keep editing'
      }).then((result) => {
        if (result.isConfirmed) {
          isChanged = false;
          closePopup();
        }
      });
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
      const isPaymentConfirmed = document.getElementById("confirmPayment").checked ? 1 : 0;
      const trackingLink = document.getElementById("trackingLink").value.trim();

      if (isPaymentConfirmed && status === "Waiting for Payment") {
        status = "Processing";
        document.getElementById("status").value = status;
      }
      if (trackingLink && (status === "Processing" || status === "Waiting for Payment")) {
        status = "Shipped";
        document.getElementById("status").value = status;
      }

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
      .then(response => {
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
        return response.json();
      })
      .then(result => {
        if (result.success) {
          // Close the modal first
          document.getElementById("orderPopup").style.display = "none";
          currentOrderID = null;
          originalStatus = '';
          isChanged = false;

          // Then show the SweetAlert2 notification
          Swal.fire({
            title: "Success!",
            text: result.message,
            icon: "success",
            confirmButtonText: "OK",
          }).then(() => {
            loadOrders(document.getElementById("searchOrder").value, document.getElementById("filterStatus").value, sortField, sortOrder);
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: result.message,
            confirmButtonText: 'OK'
          });
        }
      })
      .catch(error => {
        console.error("Error updating order:", error);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to update order: ' + error.message,
          confirmButtonText: 'OK'
        });
      });

      isChanged = false;
    }

    // 8. Copy to Clipboard function for the address
    function copyToClipboard(address) {
      // Create a temporary textarea element to copy the text
      const textarea = document.createElement('textarea');
      textarea.value = address;
      document.body.appendChild(textarea);
      textarea.select();
      try {
        document.execCommand('copy');
        Swal.fire({
          icon: 'success',
          title: 'Copied!',
          text: 'Address copied to clipboard.',
          timer: 1500,
          showConfirmButton: false
        });
      } catch (err) {
        console.error('Failed to copy:', err);
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'Failed to copy address to clipboard.',
          confirmButtonText: 'OK'
        });
      } finally {
        document.body.removeChild(textarea);
      }
    }
  </script>
</body>
</html>
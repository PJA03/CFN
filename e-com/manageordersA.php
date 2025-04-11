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

  <!-- Custom CSS -->
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
      display: flex;
      flex-direction: column; /* Stack elements vertically */
      align-items: center; /* Center horizontally */
    }
    .popup-content .close {
      position: absolute;
      top: 1rem; right: 1rem;
      font-size: 1.5rem;
      cursor: pointer;
    }
    .order-details-title {
      text-align: center;
      margin: 0 0 1rem 0; /* Space below title, no top margin */
      padding-top: 0.5rem; /* Slight padding to align with close button */
      font-family: "Bebas Neue", serif;
      font-size: 2rem;
      color: #1F4529;
      width: 100%;
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
    #searchOrder {
      width: 600px;
      max-width: 100%;
    }
    .swal2-container {
      z-index: 10001 !important;
    }
    .address-container {
      display: flex;
      justify-content: space-between;
      align-items: center;
      width: 100%;
      gap: 10px;
    }
    .address-text {
      flex: 1;
      word-wrap: break-word;
    }
    .copy-icon {
      cursor: pointer;
      font-size: 1.2rem;
      color: #6c757d;
      transition: color 0.2s;
    }
    .copy-icon:hover {
      color: #0d6efd;
    }
    #cancelledMessage {
      display: none;
      color: #dc3545;
      font-weight: bold;
      margin-bottom: 1rem;
    }
    /* Change blue buttons to green */
    .btn-primary {
      background-color: #1F4529;
      border-color: #1F4529;
      color: white;
    }
    .btn-primary:hover {
      background-color: #17361f;
      border-color: #17361f;
      color: white;
    }
    .btn-primary:focus, .btn-primary:active {
      background-color: #1F4529;
      border-color: #1F4529;
      box-shadow: 0 0 0 0.25rem rgba(31, 69, 41, 0.5);
    }
    .btn-primary:disabled {
      background-color: #4a6b52;
      border-color: #4a6b52;
      opacity: 0.65;
    }
  </style>
</head>

<body>
  <div class="container-fluid">
    <div class="row">
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
              <span class="adminuser">Admin User</span>
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
        </nav>
        <hr class="bg-white">
        <div class="d-flex align-items-center mb-3">
          <i class="bi bi-person-circle fs-4 me-2"></i>
          <div class="d-flex align-items-center gap-2">
            <span class="adminuser">Admin User</span>
            <a href="/CFN/e-com/logout.php" class="btn btn-danger btn-sm" id="logout">Logout</a>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 col-12 p-4 main-content">
        <h3 class="mt-4 text-center">Orders Table</h3>
        <div class="d-flex justify-content-between align-items-center mb-3 flex-column flex-md-row gap-2">
          <a href="manage_qr_codes.php" class="btn btn-primary w-100 w-md-auto">Manage QR Codes</a>
          <div class="d-flex align-items-center flex-column flex-md-row gap-2 w-100 w-md-auto">
            <select id="filterStatus" class="form-select" style="width: 100%; max-width: 200px;">
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
          <div class="table-responsive">
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
  </div>

  <!-- ORDER DETAILS POPUP -->
  <div id="orderPopup" class="popup">
    <div class="popup-content">
      <span class="close" onclick="closePopup()">×</span>
      <h4 class="order-details-title">Order Details</h4>
      <div id="cancelledMessage">This order is cancelled and cannot be modified.</div>
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
      <div class="mt-3 text-end d-flex justify-content-end gap-2 flex-column flex-md-row">
        <button type="button" class="btn btn-danger" id="deleteOrderButton" onclick="deleteOrder()" style="display: none;">Delete Order</button>
        <button type="button" class="btn btn-primary" id="saveChangesButton" onclick="saveChanges()">Save changes</button>
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

    // Load orders from fetchorders.php
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

    // Sort and filter table
    function sortTable(field) {
      if (sortField === field) {
        sortOrder = sortOrder === 'asc' ? 'desc' : 'asc';
      } else {
        sortField = field;
        sortOrder = 'asc';
      }
      loadOrders(document.getElementById("searchOrder").value, document.getElementById("filterStatus").value, sortField, sortOrder);
    }

    // Open the details popup
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
          
          const receiptPath = data.payment_proof ? `../uploads/receipts/${data.payment_proof}` : "images/placeholder.jpg";
          document.getElementById("receiptImage").src = receiptPath;

          const isCancelled = data.status === "Cancelled";
          const statusSelect = document.getElementById("status");
          const confirmPaymentCheckbox = document.getElementById("confirmPayment");
          const trackingLinkInput = document.getElementById("trackingLink");
          const saveChangesButton = document.getElementById("saveChangesButton");
          const deleteOrderButton = document.getElementById("deleteOrderButton");
          const cancelledMessage = document.getElementById("cancelledMessage");

          if (isCancelled) {
            cancelledMessage.style.display = "block";
            statusSelect.disabled = true;
            confirmPaymentCheckbox.disabled = true;
            trackingLinkInput.disabled = true;
            saveChangesButton.disabled = true;
            saveChangesButton.classList.add("disabled");
            deleteOrderButton.style.display = "inline-block";
          } else {
            cancelledMessage.style.display = "none";
            statusSelect.disabled = false;
            confirmPaymentCheckbox.disabled = false;
            trackingLinkInput.disabled = false;
            saveChangesButton.disabled = false;
            saveChangesButton.classList.remove("disabled");
            deleteOrderButton.style.display = "none";
          }

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

    // Close the details popup
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

    // Receipt popup
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

    // Zoomed image
    function openZoomedImage() {
      const receiptImageSrc = document.getElementById("receiptImage").src;
      document.getElementById("zoomedImage").src = receiptImageSrc;
      document.getElementById("zoomPopup").style.display = "flex";
    }
    function closeZoomedImage() {
      document.getElementById("zoomPopup").style.display = "none";
    }

    // Save changes to the order
    function saveChanges() {
      let status = document.getElementById("status").value;
      const isPaymentConfirmed = document.getElementById("confirmPayment").checked ? 1 : 0;
      const trackingLink = document.getElementById("trackingLink").value.trim();

      if (status === "Cancelled" && ["Processing", "Shipped", "Delivered"].includes(originalStatus)) {
        Swal.fire({
          title: "Cannot Cancel Order",
          text: "This order cannot be cancelled because it is currently in '" + originalStatus + "' status. Cancellation is only allowed for orders in 'Waiting for Payment' status.",
          icon: "warning",
          confirmButtonText: "OK",
        });
        return;
      }

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
          document.getElementById("orderPopup").style.display = "none";
          currentOrderID = null;
          originalStatus = '';
          isChanged = false;

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

    // Delete the order (only available for cancelled orders)
    function deleteOrder() {
      Swal.fire({
        title: 'Are you sure?',
        text: 'Do you really want to delete this cancelled order? This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'No, keep it'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch("deleteorder.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              orderID: currentOrderID
            }),
          })
          .then(response => {
            if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);
            return response.json();
          })
          .then(result => {
            if (result.success) {
              document.getElementById("orderPopup").style.display = "none";
              currentOrderID = null;
              originalStatus = '';
              isChanged = false;

              Swal.fire({
                title: "Deleted!",
                text: "The order has been deleted successfully.",
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
            console.error("Error deleting order:", error);
            Swal.fire({
              icon: 'error',
              title: 'Error',
              text: 'Failed to delete order: ' + error.message,
              confirmButtonText: 'OK'
            });
          });
        }
      });
    }

    // Copy to Clipboard function for the address
    function copyToClipboard(address) {
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
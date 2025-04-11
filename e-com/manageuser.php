<?php
require_once 'auth_check.php';
require_once '../conn.php';

// Fetch users from tb_user
$sql = "SELECT user_id, email, first_name, last_name FROM tb_user";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin Dashboard - Manage User</title>

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
    #searchUser {
      width: 200px;
      max-width: 100%;
    }
    .swal2-container {
      z-index: 10001 !important;
    }
    .action-btn {
      padding: 5px 10px;
      font-size: 0.9rem;
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
              <span class="adminuser">Super Admin User</span>
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
            <span class="adminuser">Super Admin User</span>
            <a href="/CFN/e-com/logout.php" class="btn btn-danger btn-sm" id="logout">Logout</a>
          </div>
        </div>
      </div>

      <!-- Main Content -->
      <div class="col-md-10 col-12 p-4 main-content">
        <h3 class="mt-4 text-center">Users Table</h3>
        <div class="d-flex justify-content-between align-items-center mb-3 flex-column flex-md-row gap-2">
          <input type="text" id="searchUser" class="form-control" placeholder="Search Users" />
        </div>

        <div class="bg-white p-4 rounded shadow-sm">
          <div class="table-container">
            <table class="table table-bordered">
              <thead>
                <tr class="table-success">
                  <th class="sortable" onclick="sortTable('user_id')">User ID</th>
                  <th class="sortable" onclick="sortTable('email')">Email</th>
                  <th class="sortable" onclick="sortTable('first_name')">First Name</th>
                  <th class="sortable" onclick="sortTable('last_name')">Last Name</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="usersTable">
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['email'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['first_name'] ?? 'N/A') . "</td>";
                        echo "<td>" . htmlspecialchars($row['last_name'] ?? 'N/A') . "</td>";
                        echo "<td>";
                        echo "<button class='btn btn-primary btn-sm action-btn' onclick='openEditModal(" . $row['user_id'] . ", \"" . htmlspecialchars($row['email'] ?? '', ENT_QUOTES) . "\")'>Edit</button>";
                        echo "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='5'>No users found</td></tr>";
                }
                ?>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Email Modal -->
  <div class="modal fade" id="editEmailModal" tabindex="-1" aria-labelledby="editEmailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="editEmailModalLabel">Edit User Email</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editEmailForm" action="update_email.php" method="POST">
            <input type="hidden" id="editUserId" name="user_id">
            <div class="mb-3">
              <label for="editEmail" class="form-label">Email</label>
              <input type="email" class="form-control" id="editEmail" name="email" required>
            </div>
            <button type="submit" class="btn btn-primary">Save Changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and SweetAlert2 -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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
    
    // Search functionality
    document.getElementById('searchUser').addEventListener('input', function() {
      const searchValue = this.value.toLowerCase();
      const rows = document.querySelectorAll('#usersTable tr');

      rows.forEach(row => {
        const cells = row.querySelectorAll('td');
        const rowText = Array.from(cells).map(cell => cell.textContent.toLowerCase()).join(' ');
        row.style.display = rowText.includes(searchValue) ? '' : 'none';
      });
    });

    // Sorting functionality
    function sortTable(column) {
      const table = document.getElementById('usersTable');
      const rows = Array.from(table.querySelectorAll('tr'));
      const ths = document.querySelectorAll('th.sortable');
      let isAsc = true;

      // Update sortable icons
      ths.forEach(th => {
        th.classList.remove('sorted-asc', 'sorted-desc');
        if (th.textContent.toLowerCase().includes(column)) {
          if (th.classList.contains('sorted-asc')) {
            isAsc = false;
            th.classList.add('sorted-desc');
          } else {
            th.classList.add('sorted-asc');
          }
        }
      });

      // Sort rows
      rows.sort((a, b) => {
        let aValue = a.querySelector(`td:nth-child(${getColumnIndex(column)})`).textContent;
        let bValue = b.querySelector(`td:nth-child(${getColumnIndex(column)})`).textContent;

        if (column === 'user_id') {
          aValue = parseInt(aValue) || 0;
          bValue = parseInt(bValue) || 0;
          return isAsc ? aValue - bValue : bValue - aValue;
        } else {
          return isAsc ? aValue.localeCompare(bValue) : bValue.localeCompare(aValue);
        }
      });

      // Re-append sorted rows
      table.innerHTML = '';
      rows.forEach(row => table.appendChild(row));
    }

    function getColumnIndex(column) {
      const headers = ['user_id', 'email', 'first_name', 'last_name'];
      return headers.indexOf(column) + 1;
    }

    // Open Edit Modal
    function openEditModal(userId, email) {
      document.getElementById('editUserId').value = userId;
      document.getElementById('editEmail').value = email;
      const modal = new bootstrap.Modal(document.getElementById('editEmailModal'));
      modal.show();
    }

    // Form submission with validation
    document.getElementById('editEmailForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const email = document.getElementById('editEmail').value;
      const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

      if (!emailRegex.test(email)) {
        Swal.fire({
          icon: 'error',
          title: 'Invalid Email',
          text: 'Please enter a valid email address.',
        });
        return;
      }

      const form = this;
      const formData = new FormData(form);

      fetch(form.action, {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            icon: 'success',
            title: 'Success',
            text: 'Email updated successfully!',
          }).then(() => {
            location.reload(); // Refresh to show updated email
          });
        } else {
          Swal.fire({
            icon: 'error',
            title: 'Error',
            text: data.message || 'Failed to update email.',
          });
        }
      })
      .catch(() => {
        Swal.fire({
          icon: 'error',
          title: 'Error',
          text: 'An error occurred while updating the email.',
        });
      });
    });
  </script>
</body>
</html>
<?php
$conn->close();
?>
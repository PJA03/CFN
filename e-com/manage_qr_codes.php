<?php
require_once '../conn.php';
require_once 'auth_check.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch the username of the logged-in admin from the session
$logged_in_user = isset($_SESSION['username']) ? $_SESSION['username'] : 'Unknown';

// Fetch existing QR codes (without uploaded_by since we don't need to display it)
$query = "SELECT id, payment_type, qr_image FROM tb_payment_qr_codes";
$result = $conn->query($query);
$qr_codes = [];
while ($row = $result->fetch_assoc()) {
    $qr_codes[] = $row;
}

// Handle QR code upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['qr_image']) && !isset($_POST['edit_id'])) {
    $payment_type = $_POST['payment_type'];
    $file = $_FILES['qr_image'];
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size && $file['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../uploads/qr/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $file_name = $payment_type . '_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
        $file_path = $upload_dir . $file_name;

        if (move_uploaded_file($file['tmp_name'], $file_path)) {
            // Insert the new QR code with the uploaded_by field
            $query = "INSERT INTO tb_payment_qr_codes (payment_type, qr_image, uploaded_by) 
                      VALUES (?, ?, ?) 
                      ON DUPLICATE KEY UPDATE qr_image = ?, upload_date = NOW(), uploaded_by = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssss", $payment_type, $file_name, $logged_in_user, $file_name, $logged_in_user);
            $stmt->execute();
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'QR code uploaded successfully!',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href='manage_qr_codes.php';
                        });
                    });
                  </script>";
        } else {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to upload QR code.',
                            confirmButtonText: 'OK'
                        });
                    });
                  </script>";
        }
    } else {
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Invalid file type or size. Only PNG, JPEG, JPG files up to 5MB are allowed.',
                        confirmButtonText: 'OK'
                    });
                });
              </script>";
    }
}

// Handle QR code edit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'])) {
    $edit_id = $_POST['edit_id'];
    $payment_type = $_POST['payment_type'];
    $file = $_FILES['qr_image'];
    $allowed_types = ['image/png', 'image/jpeg', 'image/jpg'];
    $max_size = 5 * 1024 * 1024; // 5MB

    // Check if a new file is uploaded
    if ($file['size'] > 0 && $file['error'] === UPLOAD_ERR_OK) {
        if (in_array($file['type'], $allowed_types) && $file['size'] <= $max_size) {
            $upload_dir = '../uploads/qr/';
            $file_name = $payment_type . '_' . uniqid() . '.' . pathinfo($file['name'], PATHINFO_EXTENSION);
            $file_path = $upload_dir . $file_name;

            if (move_uploaded_file($file['tmp_name'], $file_path)) {
                // Delete the old image
                $query = "SELECT qr_image FROM tb_payment_qr_codes WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("i", $edit_id);
                $stmt->execute();
                $result = $stmt->get_result();
                $old_image = $result->fetch_assoc()['qr_image'];
                if ($old_image && file_exists($upload_dir . $old_image)) {
                    unlink($upload_dir . $old_image);
                }

                // Update the database with the new image and uploaded_by
                $query = "UPDATE tb_payment_qr_codes SET payment_type = ?, qr_image = ?, upload_date = NOW(), uploaded_by = ? WHERE id = ?";
                $stmt = $conn->prepare($query);
                $stmt->bind_param("sssi", $payment_type, $file_name, $logged_in_user, $edit_id);
                $stmt->execute();
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: 'QR code updated successfully!',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.href='manage_qr_codes.php';
                            });
                        });
                      </script>";
            } else {
                echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Failed to upload new QR code.',
                                confirmButtonText: 'OK'
                            });
                        });
                      </script>";
            }
        } else {
            echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Invalid file type or size for new QR code. Only PNG, JPEG, JPG files up to 5MB are allowed.',
                            confirmButtonText: 'OK'
                        });
                    });
                  </script>";
        }
    } else {
        // Update only the payment type and uploaded_by if no new file is uploaded
        $query = "UPDATE tb_payment_qr_codes SET payment_type = ?, upload_date = NOW(), uploaded_by = ? WHERE id = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ssi", $payment_type, $logged_in_user, $edit_id);
        $stmt->execute();
        echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Payment type updated successfully!',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href='manage_qr_codes.php';
                    });
                });
              </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Manage QR Codes</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Additional Fonts/Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- Optional Custom CSS -->
    <link rel="stylesheet" href="style2.css">

    <style>
        .qr-preview { 
            max-width: 150px; 
            max-height: 150px; 
            cursor: pointer; 
        }
        .table-container {
            overflow-x: auto;
            margin-top: 1rem;
        }
        /* Popup Styling */
        .popup {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
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
            max-width: 500px;
            width: 90%;
            position: relative;
        }
        .modal-title {
            margin-bottom: 1rem;
        }
        .modal-field {
            margin-bottom: 1rem;
        }
        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }
        .btn-discard {
            background-color: #6c757d;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }
        .btn-save {
            background-color: #28a745;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
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
                    <a class="nav-link" href="manageordersA.php">Orders</a>
                    <a class="nav-link" href="analytics.php">Analytics</a>
                </nav>
                <div class="mt-auto">
                    <hr />
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                        <span>Admin User</span>
                    </div>
                    <a href="logout.php" class="btn btn-danger">Logout</a>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-10 p-4 main-content">
                <h3 class="mt-4 text-center">Manage QR Codes</h3>
                <div class="bg-white p-4 rounded shadow-sm">
                    <h4 class="mb-3">Upload New QR Code</h4>
                    <form action="manage_qr_codes.php" method="POST" enctype="multipart/form-data" class="mb-4">
                        <div class="mb-3">
                            <label for="payment_type" class="form-label">Payment Method</label>
                            <select name="payment_type" id="payment_type" class="form-select" required>
                                <option value="gcash">GCash</option>
                                <option value="paymaya">PayMaya</option>
                                <option value="instapay">Instapay</option>
                                <option value="bank_transfer_bpi">Bank Transfer (BPI)</option>
                                <option value="bank_transfer_pnb">Bank Transfer (PNB)</option>
                                <option value="bank_transfer_bdo">Bank Transfer (BDO)</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="qr_image" class="form-label">QR Code Image (PNG, JPEG, JPG, max 5MB)</label>
                            <input type="file" name="qr_image" id="qr_image" class="form-control" accept=".png,.jpeg,.jpg" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Upload QR Code</button>
                    </form>

                    <h4 class="mb-3">Current QR Codes</h4>
                    <div class="table-container">
                        <table class="table table-bordered text-center">
                            <thead class="table-success">
                                <tr>
                                    <th>Payment Method</th>
                                    <th>QR Code</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($qr_codes as $qr): ?>
                                    <tr>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $qr['payment_type'])); ?></td>
                                        <td>
                                            <img src="../uploads/qr/<?php echo htmlspecialchars($qr['qr_image']); ?>" 
                                                 alt="<?php echo ucfirst($qr['payment_type']); ?>" 
                                                 class="qr-preview" 
                                                 onclick="window.open(this.src, '_blank')">
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning me-2" onclick="openEditModal(<?php echo $qr['id']; ?>, '<?php echo htmlspecialchars($qr['payment_type']); ?>')">Edit</button>
                                            <button class="btn btn-sm btn-danger" onclick="deleteQRCode(<?php echo $qr['id']; ?>)">Delete</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($qr_codes)): ?>
                                    <tr>
                                        <td colspan="3">No QR codes uploaded yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Edit QR Code Popup -->
    <div id="editQRPopup" class="popup" style="display:none;">
        <div class="popup-content">
            <h3 class="modal-title">Edit QR Code</h3>
            <form id="editQRForm" action="manage_qr_codes.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" id="edit_id" name="edit_id">
                <div class="modal-field">
                    <label for="edit_payment_type" class="form-label">Payment Method</label>
                    <select name="payment_type" id="edit_payment_type" class="form-select" required>
                        <option value="gcash">GCash</option>
                        <option value="paymaya">PayMaya</option>
                        <option value="instapay">Instapay</option>
                        <option value="bank_transfer_bpi">Bank Transfer (BPI)</option>
                        <option value="bank_transfer_pnb">Bank Transfer (PNB)</option>
                        <option value="bank_transfer_bdo">Bank Transfer (BDO)</option>
                    </select>
                </div>
                <div class="modal-field">
                    <label for="edit_qr_image" class="form-label">New QR Code Image (Optional, PNG, JPEG, JPG, max 5MB)</label>
                    <input type="file" name="qr_image" id="edit_qr_image" class="form-control" accept=".png,.jpeg,.jpg">
                </div>
                <div class="actions">
                    <button type="button" class="btn-discard" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- JavaScript for Popup and Delete -->
    <script>
        function openEditModal(id, paymentType) {
            const popup = document.getElementById('editQRPopup');
            popup.style.display = 'flex';
            document.getElementById('edit_id').value = id;
            document.getElementById('edit_payment_type').value = paymentType;
        }

        function closeEditModal() {
            const popup = document.getElementById('editQRPopup');
            popup.style.display = 'none';
        }

        function deleteQRCode(id) {
            Swal.fire({
                title: 'Are you sure?',
                text: 'Do you want to delete this QR code?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it',
                cancelButtonText: 'No, cancel'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('delete_qr_code.php', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ id: id })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted',
                                text: 'QR code deleted successfully!',
                                confirmButtonText: 'OK'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Error deleting QR code: ' + (data.error || 'Unknown error'),
                                confirmButtonText: 'OK'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error deleting QR code:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Failed to delete QR code: ' + error.message,
                            confirmButtonText: 'OK'
                        });
                    });
                }
            });
        }
    </script>
</body>
</html>
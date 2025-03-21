<?php
require_once '../conn.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Fetch existing QR codes
$query = "SELECT payment_type, qr_image FROM tb_payment_qr_codes";
$result = $conn->query($query);
$qr_codes = [];
while ($row = $result->fetch_assoc()) {
    $qr_codes[$row['payment_type']] = $row['qr_image'];
}

// Handle QR code upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['qr_image'])) {
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
            $query = "INSERT INTO tb_payment_qr_codes (payment_type, qr_image) 
                      VALUES (?, ?) 
                      ON DUPLICATE KEY UPDATE qr_image = ?, upload_date = NOW()";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sss", $payment_type, $file_name, $file_name);
            $stmt->execute();
            echo "<script>alert('QR code uploaded successfully!'); window.location.href='manage_qr_codes.php';</script>";
        } else {
            echo "<script>alert('Failed to upload QR code.');</script>";
        }
    } else {
        echo "<script>alert('Invalid file type or size. Only PNG, JPEG, JPG files up to 5MB are allowed.');</script>";
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
                    <a class="nav-link active" href="manage_qr_codes.php">Manage QR Codes</a>
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
                                <option value="bank_transfer_bdo">Bank Transfer (BPI)</option>
                                <!-- Add more options as needed -->
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
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($qr_codes as $type => $image): ?>
                                    <tr>
                                        <td><?php echo ucfirst(str_replace('_', ' ', $type)); ?></td>
                                        <td>
                                            <img src="../uploads/qr/<?php echo htmlspecialchars($image); ?>" 
                                                 alt="<?php echo ucfirst($type); ?>" 
                                                 class="qr-preview" 
                                                 onclick="window.open(this.src, '_blank')">
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($qr_codes)): ?>
                                    <tr>
                                        <td colspan="2">No QR codes uploaded yet.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
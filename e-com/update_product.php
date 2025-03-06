<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Ensure the request method is POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Invalid request.");
}

// Database connection parameters
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve and sanitize POST data for main product
$productID = intval($_POST['productID']);
$productName = $conn->real_escape_string($_POST['productName']);
$productDescription = $conn->real_escape_string($_POST['productDescription']);
$brand = isset($_POST['brand']) ? $conn->real_escape_string($_POST['brand']) : '';
$category = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : '';

$productImagePath = "";
if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === 0) {
    $targetDir = "uploads/";
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $fileName = basename($_FILES["productImage"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        die("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
    }
    if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFilePath)) {
        $productImagePath = $targetFilePath;
    } else {
        die("Error uploading image.");
    }
} else {
    // No new image uploaded: fetch current image from database
    $sql = "SELECT product_image FROM tb_products WHERE productID = ?";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $stmt->bind_param("i", $productID);
        $stmt->execute();
        $stmt->bind_result($currentImage);
        $stmt->fetch();
        $productImagePath = $currentImage;
        $stmt->close();
    } else {
        die("Prepare failed: " . $conn->error);
    }
}

// Update main product record in tb_products
$sql = "UPDATE tb_products 
        SET product_name = ?, product_desc = ?, brand = ?, category = ?, product_image = ?, updated_at = NOW() 
        WHERE productID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("sssssi", $productName, $productDescription, $brand, $category, $productImagePath, $productID);
if (!$stmt->execute()) {
    echo "Error updating product: " . $stmt->error;
    exit;
}
$stmt->close();

// Handle product variants
if (isset($_POST['variant_id']) && is_array($_POST['variant_id'])) {
    $submittedVariantIDs = $_POST['variant_id'];
    $variantNames = $_POST['variant_name'];
    $variantPrices = $_POST['price'];
    $variantStocks = $_POST['stock'];
    $variantSKUs = isset($_POST['sku']) ? $_POST['sku'] : [];

    // Get existing variant IDs from the database
    $sql = "SELECT variant_id FROM tb_productvariants WHERE productID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $existingVariantIDs = [];
    while ($row = $result->fetch_assoc()) {
        $existingVariantIDs[] = $row['variant_id'];
    }
    $stmt->close();

    // Prepare statements for updating and inserting variants
    $updateStmt = $conn->prepare("UPDATE tb_productvariants SET variant_name = ?, price = ?, stock = ?, sku = ?, updated_at = NOW() WHERE variant_id = ? AND productID = ?");
    $insertStmt = $conn->prepare("INSERT INTO tb_productvariants (productID, variant_name, price, stock, sku, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");

    for ($i = 0; $i < count($submittedVariantIDs); $i++) {
        $v_id = !empty($submittedVariantIDs[$i]) ? intval($submittedVariantIDs[$i]) : 0;
        $v_name = $conn->real_escape_string($variantNames[$i]);
        $v_price = floatval($variantPrices[$i]);
        $v_stock = intval($variantStocks[$i]);
        $v_sku = isset($variantSKUs[$i]) ? $conn->real_escape_string($variantSKUs[$i]) : "";

        if ($v_id > 0) {
            // Update existing variant
            $updateStmt->bind_param("sdisii", $v_name, $v_price, $v_stock, $v_sku, $v_id, $productID);
            if (!$updateStmt->execute()) {
                echo "Variant update error: " . $updateStmt->error;
                exit;
            }
        } else {
            // Insert new variant
            $insertStmt->bind_param("isdis", $productID, $v_name, $v_price, $v_stock, $v_sku);
            if (!$insertStmt->execute()) {
                echo "Variant insert error: " . $insertStmt->error;
                exit;
            }
        }
    }

    // Delete variants that were removed from the form
    $submittedIDsFiltered = array_filter($submittedVariantIDs, 'is_numeric');
    $variantsToDelete = array_diff($existingVariantIDs, $submittedIDsFiltered);
    if (!empty($variantsToDelete)) {
        $deleteStmt = $conn->prepare("DELETE FROM tb_productvariants WHERE variant_id = ? AND productID = ?");
        foreach ($variantsToDelete as $deleteID) {
            $deleteStmt->bind_param("ii", $deleteID, $productID);
            $deleteStmt->execute();
        }
        $deleteStmt->close();
    }

    $updateStmt->close();
    $insertStmt->close();
}

$conn->close();

header("Location: manageproductsA.php?update=success");
exit;
?>

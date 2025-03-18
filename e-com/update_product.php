<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("Invalid request.");
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$productID = intval($_POST['productID']);
$productName = $conn->real_escape_string($_POST['productName']);
$productDescription = $conn->real_escape_string($_POST['productDescription']);
$category = $conn->real_escape_string($_POST['category']);

// Optional: Limit description length (e.g., 10,000 characters)
$maxDescriptionLength = 10000;
if (strlen($productDescription) > $maxDescriptionLength) {
    die("Description exceeds maximum length of $maxDescriptionLength characters.");
}

// Handle image upload if provided
$productImagePath = "";
$sql = "SELECT product_image FROM tb_products WHERE productID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $productID);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();
$existingImagePath = $product['product_image'] ?? '';

if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
    $uploadDir = 'uploads/'; // Relative to the script's directory
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    $fileName = basename($_FILES["productImage"]["name"]);
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    $allowedExts = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($fileExt, $allowedExts)) {
        die("Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed.");
    }
    // Generate a unique filename to avoid overwriting
    $newFileName = uniqid() . '_' . time() . '.' . $fileExt;
    $targetFilePath = $uploadDir . $newFileName;
    if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFilePath)) {
        $productImagePath = $uploadDir . $newFileName; // Store the relative path
        // Optionally delete the old image if it exists and is different
        if (!empty($existingImagePath) && file_exists($existingImagePath) && $existingImagePath != $targetFilePath) {
            unlink($existingImagePath);
        }
    } else {
        die("Error uploading image.");
    }
} else {
    $productImagePath = $existingImagePath; // Retain the existing image path
}

// Update main product record
$sql = "UPDATE tb_products SET product_name = ?, product_desc = ?, category = ?, product_image = ?, updated_at = NOW() WHERE productID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("ssssi", $productName, $productDescription, $category, $productImagePath, $productID);
if (!$stmt->execute()) {
    die("Update failed: " . $stmt->error);
}
$stmt->close();

// Handle product variants
if (isset($_POST['variant_id']) && is_array($_POST['variant_id'])) {
    $submittedVariantIDs = array_map('intval', $_POST['variant_id']);
    $variantNames = $_POST['variant_name'];
    $variantPrices = $_POST['price'];
    $variantStocks = $_POST['stock'];
    $variantSKUs = $_POST['sku'] ?? [];
    $defaultVariantIndex = isset($_POST['defaultVariant']) ? intval($_POST['defaultVariant']) : -1;

    // Get existing variant IDs
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

    $updateStmt = $conn->prepare("UPDATE tb_productvariants SET variant_name = ?, price = ?, stock = ?, sku = ?, is_default = ?, updated_at = NOW() WHERE variant_id = ? AND productID = ?");
    $insertStmt = $conn->prepare("INSERT INTO tb_productvariants (productID, variant_name, price, stock, sku, is_default, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 0, NOW(), NOW())");

    for ($i = 0; $i < count($variantNames); $i++) {
        $v_id = $submittedVariantIDs[$i] ?? 0;
        $v_name = $conn->real_escape_string($variantNames[$i]);
        $v_price = floatval($variantPrices[$i]);
        $v_stock = intval($variantStocks[$i]);
        $v_sku = $conn->real_escape_string($variantSKUs[$i] ?? "");
        $isDefault = ($i == $defaultVariantIndex) ? 1 : 0;

        if ($v_id > 0) {
            $updateStmt->bind_param("sdissii", $v_name, $v_price, $v_stock, $v_sku, $isDefault, $v_id, $productID);
            $updateStmt->execute();
        } else {
            $insertStmt->bind_param("isdssi", $productID, $v_name, $v_price, $v_stock, $v_sku, $isDefault);
            $insertStmt->execute();
            $v_id = $conn->insert_id;
        }

        // Ensure only one default variant
        if ($isDefault) {
            $setDefaultStmt = $conn->prepare("UPDATE tb_productvariants SET is_default = 0 WHERE productID = ? AND variant_id != ?");
            $setDefaultStmt->bind_param("ii", $productID, $v_id);
            $setDefaultStmt->execute();
            $setDefaultStmt->close();
        }
    }

    $variantsToDelete = array_diff($existingVariantIDs, array_filter($submittedVariantIDs));
    if (!empty($variantsToDelete)) {
        $deleteStmt = $conn->prepare("DELETE FROM tb_productvariants WHERE variant_id = ? AND projectID = ?");
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
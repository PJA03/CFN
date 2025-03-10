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
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

$productID = intval($_POST['productID']);
$productName = $_POST['productName']; // Removed real_escape_string
$productDescription = $_POST['productDescription']; // Removed real_escape_string
$category = $_POST['category']; // Removed real_escape_string

// Optional: Limit description length (e.g., 10,000 characters)
$maxDescriptionLength = 10000;
if (strlen($productDescription) > $maxDescriptionLength) {
    die(json_encode(["status" => "error", "message" => "Description exceeds maximum length of $maxDescriptionLength characters."]));
}

// Handle image upload if provided
$productImagePath = "";
if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
    $targetDir = __DIR__ . "/uploads/"; // Use absolute path
    if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
    }
    $fileName = basename($_FILES["productImage"]["name"]);
    $targetFilePath = $targetDir . $fileName;
    $imageFileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));
    $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
    if (!in_array($imageFileType, $allowedTypes)) {
        die(json_encode(["status" => "error", "message" => "Invalid file type. Only JPG, JPEG, PNG, and GIF files are allowed."]));
    }
    if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFilePath)) {
        $productImagePath = $targetFilePath;
    } else {
        die(json_encode(["status" => "error", "message" => "Error uploading image."]));
    }
} else {
    $sql = "SELECT product_image FROM tb_products WHERE productID = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productID);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $productImagePath = $product['product_image'] ?? '';
    $stmt->close();
}

// Update main product record
$sql = "UPDATE tb_products SET product_name = ?, product_desc = ?, category = ?, product_image = ?, updated_at = NOW() WHERE productID = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die(json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]));
}
$stmt->bind_param("ssssi", $productName, $productDescription, $category, $productImagePath, $productID);
if (!$stmt->execute()) {
    die(json_encode(["status" => "error", "message" => "Update failed: " . $stmt->error]));
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

    $updateStmt = $conn->prepare("UPDATE tb_productvariants SET variant_name = ?, price = ?, stock = ?, sku = ?, updated_at = NOW() WHERE variant_id = ? AND productID = ?");
    $insertStmt = $conn->prepare("INSERT INTO tb_productvariants (productID, variant_name, price, stock, sku, is_default, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 0, NOW(), NOW())");

    for ($i = 0; $i < count($variantNames); $i++) {
        $v_id = $submittedVariantIDs[$i] ?? 0;
        $v_name = $variantNames[$i];
        $v_price = floatval($variantPrices[$i]);
        $v_stock = intval($variantStocks[$i]);
        $v_sku = $variantSKUs[$i] ?? "";

        if ($v_id > 0) {
            $updateStmt->bind_param("sdisii", $v_name, $v_price, $v_stock, $v_sku, $v_id, $productID);
            $updateStmt->execute();
        } else {
            $insertStmt->bind_param("isdsi", $productID, $v_name, $v_price, $v_stock, $v_sku);
            $insertStmt->execute();
            $v_id = $conn->insert_id;
        }

        $isDefault = ($i == $defaultVariantIndex) ? 1 : 0;
        $setDefaultStmt = $conn->prepare("UPDATE tb_productvariants SET is_default = ? WHERE variant_id = ? AND productID = ?");
        $setDefaultStmt->bind_param("iii", $isDefault, $v_id, $productID);
        $setDefaultStmt->execute();
        $setDefaultStmt->close();
    }

    $variantsToDelete = array_diff($existingVariantIDs, array_filter($submittedVariantIDs));
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

<script>
    // Optional: Handle response if you switch to JSON
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('update') === 'success') {
        alert('Product updated successfully.');
        window.location.href = 'manageproductsA.php';
    }
</script>
<?php
require_once 'auth_check.php';

// Disable error display to prevent HTML output
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(E_ALL);

// Log errors to a file
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}

$conn->begin_transaction();

try {
    if ($_SERVER["REQUEST_METHOD"] != "POST") {
        throw new Exception("Invalid request method.");
    }

    // Validate required form fields
    $requiredFields = ['productName', 'category', 'basePrice', 'baseStock', 'productDescription'];
    foreach ($requiredFields as $field) {
        if (!isset($_POST[$field]) || $_POST[$field] === '') {
            throw new Exception("Missing required field: $field");
        }
    }

    // Get form data
    $product_name = $conn->real_escape_string($_POST['productName']);
    $product_desc = $conn->real_escape_string($_POST['productDescription']);
    $category = $conn->real_escape_string($_POST['category']);
    $base_price = floatval($_POST['basePrice']);
    $base_stock = intval($_POST['baseStock']);

    // Additional validation for basePrice and baseStock
    if ($base_price < 0) {
        throw new Exception("Base Price must be a non-negative number.");
    }
    if ($base_stock < 0) {
        throw new Exception("Base Stocks must be a non-negative number.");
    }

    // Handle file upload
    $product_img = '';
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] === UPLOAD_ERR_OK) {
        $targetDir = 'uploads/';
        if (!file_exists($targetDir)) {
            if (!mkdir($targetDir, 0755, true)) {
                throw new Exception('Failed to create upload directory');
            }
        }
        if (!is_writable($targetDir)) {
            throw new Exception('Upload directory is not writable');
        }

        $fileName = basename($_FILES['productImage']['name']);
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid('img_', true) . '.' . $fileExt;
        $targetFilePath = $targetDir . $newFileName;
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
        if (!in_array($fileExt, $allowedTypes)) {
            throw new Exception('Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.');
        }
        if (!move_uploaded_file($_FILES['productImage']['tmp_name'], $targetFilePath)) {
            throw new Exception('Error uploading image.');
        }
        $product_img = $targetFilePath;
    }

    // Insert product into tb_products
    $stmt = $conn->prepare("INSERT INTO tb_products (product_name, product_desc, category, base_price, base_stock, product_image) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssdis", $product_name, $product_desc, $category, $base_price, $base_stock, $product_img);
    if (!$stmt->execute()) {
        throw new Exception("SQL Error on product insert: " . $stmt->error);
    }
    $product_id = $conn->insert_id;
    if ($product_id <= 0) {
        throw new Exception("Failed to generate a valid product ID. Insert may have failed or auto-increment is not set on productID.");
    }
    $stmt->close();

    // Log the product ID for debugging
    error_log("Inserted product with ID: " . $product_id);

    // Prepare statement for inserting variants
    $variantStmt = $conn->prepare("INSERT INTO tb_productvariants (productID, variant_name, price, stock, sku, is_default) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$variantStmt) {
        throw new Exception("Variant prepare failed: " . $conn->error);
    }

    // Check if additional variants are provided
    $hasVariants = isset($_POST['variantName']) && is_array($_POST['variantName']) && count($_POST['variantName']) > 0;
    if ($hasVariants) {
        $variantNames = $_POST['variantName'];
        $variantPrices = $_POST['variantPrice'];
        $variantStocks = $_POST['variantStock'];
        $variantSKUs = isset($_POST['variantSKU']) ? $_POST['variantSKU'] : [];

        if (count($variantNames) !== count($variantPrices) || count($variantNames) !== count($variantStocks)) {
            throw new Exception('Mismatch in variant data');
        }

        for ($i = 0; $i < count($variantNames); $i++) {
            $vName = $conn->real_escape_string($variantNames[$i]);
            $vPrice = isset($variantPrices[$i]) && $variantPrices[$i] !== "" ? floatval($variantPrices[$i]) : 0;
            $vStock = isset($variantStocks[$i]) && $variantStocks[$i] !== "" ? intval($variantStocks[$i]) : 0;
            $vSKU = isset($variantSKUs[$i]) ? $conn->real_escape_string($variantSKUs[$i]) : "";
            $is_default = ($i == 0) ? 1 : 0;

            // Validate variant fields
            if (empty($vName)) {
                throw new Exception("Variant Name is required for variant #" . ($i + 1));
            }
            if ($vPrice < 0) {
                throw new Exception("Price must be a non-negative number for variant #" . ($i + 1));
            }
            if ($vStock < 0) {
                throw new Exception("Stock must be a non-negative number for variant #" . ($i + 1));
            }

            $variantStmt->bind_param("isdisi", $product_id, $vName, $vPrice, $vStock, $vSKU, $is_default);
            if (!$variantStmt->execute()) {
                throw new Exception("Variant SQL Error: " . $variantStmt->error);
            }

            // Get the variant_id of the inserted variant
            $variant_id = $conn->insert_id;
            if ($variant_id <= 0) {
                throw new Exception("Failed to generate a valid variant ID for variant '$vName'. Auto-increment may not be set on variant_id.");
            }
            error_log("Inserted variant with ID: " . $variant_id . " for product ID: " . $product_id);
        }
    } else {
        // Insert default variant
        $default_variant_name = "Default";
        $default_sku = "";
        $is_default = 1;
        $variantStmt->bind_param("isdisi", $product_id, $default_variant_name, $base_price, $base_stock, $default_sku, $is_default);
        if (!$variantStmt->execute()) {
            throw new Exception("Default variant insert failed: " . $variantStmt->error);
        }

        // Get the variant_id of the default variant
        $variant_id = $conn->insert_id;
        if ($variant_id <= 0) {
            throw new Exception("Failed to generate a valid variant ID for default variant. Auto-increment may not be set on variant_id.");
        }
        error_log("Inserted default variant with ID: " . $variant_id . " for product ID: " . $product_id);
    }
    $variantStmt->close();

    // Commit transaction
    $conn->commit();

    // Return success response
    header('Content-Type: application/json');
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    header('Content-Type: application/json');
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
} finally {
    $conn->close();
}
?>
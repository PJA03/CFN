<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

// processproduct.php

// Database connection parameters
$servername = "localhost";
$username   = "root";
$password   = "";
$name       = "db_cfn";

// Create connection
$conn = new mysqli($servername, $username, $password, $name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Process form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize and retrieve main product data
    $product_name  = $conn->real_escape_string($_POST['productName']);
    $product_desc  = $conn->real_escape_string($_POST['productDescription']);
    $brand         = isset($_POST['brand']) ? $conn->real_escape_string($_POST['brand']) : "";
    $category      = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : "";

    // Handle image upload
    $product_img = "";
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
        $targetDir = "uploads/";
        // Ensure the uploads directory exists
        if (!file_exists($targetDir)) {
            mkdir($targetDir, 0755, true);
        }
        // Generate a unique filename to avoid collisions
        $fileName    = basename($_FILES["productImage"]["name"]);
        $fileExt     = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $newFileName = uniqid('img_', true) . '.' . $fileExt;
        $targetFilePath = $targetDir . $newFileName;
        
        // Optionally, add validation for file type and size here
        
        if (move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFilePath)) {
            // Save the file path into the database
            $product_img = $targetFilePath;
        } else {
            echo "Error uploading image.";
            exit;
        }
    }
    
    // Insert the main product into tb_products.
    // Note: The column product_image is in tb_products.
    $stmt = $conn->prepare("INSERT INTO tb_products (product_name, product_desc, brand, category, product_image) VALUES (?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("sssss", $product_name, $product_desc, $brand, $category, $product_img);
    
    if ($stmt->execute()) {
        // Get the newly inserted product_id
        $product_id = $conn->insert_id;
    } else {
        echo "SQL Error: " . $stmt->error;
        exit;
    }
    $stmt->close();

    // Process product variants
    if (isset($_POST['variantName']) && is_array($_POST['variantName']) && count($_POST['variantName']) > 0) {
        $variantNames  = $_POST['variantName'];
        $variantPrices = $_POST['variantPrice'];
        $variantStocks = $_POST['variantStock'];
        $variantSKUs   = isset($_POST['variantSKU']) ? $_POST['variantSKU'] : [];

        // Prepare the statement to insert variants into tb_productvariants
        $variantStmt = $conn->prepare("INSERT INTO tb_productvariants (productID, variant_name, price, stock, sku, is_default) VALUES (?, ?, ?, ?, ?, ?)");
        if (!$variantStmt) {
            die("Variant prepare failed: " . $conn->error);
        }
        
        for ($i = 0; $i < count($variantNames); $i++) {
            $vName  = $conn->real_escape_string($variantNames[$i]);
            $vPrice = (float)$variantPrices[$i];
            $vStock = (int)$variantStocks[$i];
            $vSKU   = isset($variantSKUs[$i]) ? $conn->real_escape_string($variantSKUs[$i]) : "";
            $isDefault = ($i == 0) ? 1 : 0;
            
            $variantStmt->bind_param("isdisi", $product_id, $vName, $vPrice, $vStock, $vSKU, $isDefault);
            if (!$variantStmt->execute()) {
                echo "Variant SQL Error: " . $variantStmt->error;
                exit;
            }
        }
        $variantStmt->close();
    } else {
        // No variants provided: insert a default variant using base product price and stocks.
        $defaultPrice  = isset($_POST['productPrice']) ? (float)$_POST['productPrice'] : 0;
        $defaultStock  = isset($_POST['productStocks']) ? (int)$_POST['productStocks'] : 0;
        $stmtDefault = $conn->prepare("INSERT INTO tb_productvariants (productID, variant_name, price, stock, is_default) VALUES (?, 'Default', ?, ?, 1)");
        if (!$stmtDefault) {
            die("Default variant prepare failed: " . $conn->error);
        }
        $stmtDefault->bind_param("iii", $product_id, $defaultPrice, $defaultStock);
        if (!$stmtDefault->execute()) {
            echo "Default variant SQL Error: " . $stmtDefault->error;
            exit;
        }
        $stmtDefault->close();
    }

    // Redirect back to manage products page after successful insertion
    header("Location: manageproductsA.php");
    exit;
}

$conn->close();
?>

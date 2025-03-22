
<?php
require_once 'auth_check.php';
ini_set('display_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$conn->begin_transaction();

try {
  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['productName']) || empty($_POST['productName'])) {
      throw new Exception("Product name is required.");
    }

    $product_name = $conn->real_escape_string($_POST['productName']);
    $product_desc = $conn->real_escape_string($_POST['productDescription']);
    $category = isset($_POST['category']) ? $conn->real_escape_string($_POST['category']) : "";
    $base_price = isset($_POST['basePrice']) ? floatval($_POST['basePrice']) : 0;
    $base_stock = isset($_POST['baseStock']) ? intval($_POST['baseStock']) : 0;

    $product_img = "";
    if (isset($_FILES['productImage']) && $_FILES['productImage']['error'] == 0) {
      $targetDir = "uploads/";
      if (!file_exists($targetDir)) {
        mkdir($targetDir, 0755, true);
      }
      $fileName = basename($_FILES["productImage"]["name"]);
      $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
      $newFileName = uniqid('img_', true) . '.' . $fileExt;
      $targetFilePath = $targetDir . $newFileName;
      $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
      if (!in_array($fileExt, $allowedTypes)) {
        throw new Exception("Invalid file type. Only JPG, JPEG, PNG, and GIF allowed.");
      }
      if (!move_uploaded_file($_FILES["productImage"]["tmp_name"], $targetFilePath)) {
        throw new Exception("Error uploading image.");
      }
      $product_img = $targetFilePath;
    }

    $stmt = $conn->prepare("INSERT INTO tb_products (product_name, product_desc, category, product_image) VALUES (?, ?, ?, ?)");
    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
    $stmt->bind_param("ssss", $product_name, $product_desc, $category, $product_img);
    if (!$stmt->execute()) throw new Exception("SQL Error: " . $stmt->error);
    $product_id = $conn->insert_id;
    $stmt->close();

    $variantStmt = $conn->prepare("INSERT INTO tb_productvariants (productID, variant_name, price, stock, sku, is_default) VALUES (?, ?, ?, ?, ?, ?)");
    if (!$variantStmt) throw new Exception("Variant prepare failed: " . $conn->error);

    $hasVariants = isset($_POST['variantName']) && is_array($_POST['variantName']) && count($_POST['variantName']) > 0;
    if ($hasVariants) {
      $variantNames = $_POST['variantName'];
      $variantPrices = $_POST['variantPrice'];
      $variantStocks = $_POST['variantStock'];
      $variantSKUs = isset($_POST['variantSKU']) ? $_POST['variantSKU'] : [];

      for ($i = 0; $i < count($variantNames); $i++) {
        $vName = $conn->real_escape_string($variantNames[$i]);
        $vPrice = isset($variantPrices[$i]) && $variantPrices[$i] !== "" ? floatval($variantPrices[$i]) : 0;
        $vStock = isset($variantStocks[$i]) && $variantStocks[$i] !== "" ? intval($variantStocks[$i]) : 0;
        $vSKU = isset($variantSKUs[$i]) ? $conn->real_escape_string($variantSKUs[$i]) : "";
        $is_default = ($i == 0) ? 1 : 0;

        $variantStmt->bind_param("isdisi", $product_id, $vName, $vPrice, $vStock, $vSKU, $is_default);
        if (!$variantStmt->execute()) throw new Exception("Variant SQL Error: " . $variantStmt->error);
      }
    } else {
      $default_variant_name = "Default";
      $default_sku = "";
      $is_default = 1;
      $variantStmt->bind_param("isdisi", $product_id, $default_variant_name, $base_price, $base_stock, $default_sku, $is_default);
      if (!$variantStmt->execute()) throw new Exception("Default variant insert failed: " . $variantStmt->error);
    }
    $variantStmt->close();

    $conn->commit();
    echo "<script>alert('Product and variants successfully added.'); window.location.href = 'manageproductsA.php';</script>";
    exit;
  }
} catch (Exception $e) {
  $conn->rollback();
  echo "<script>alert('Error: " . addslashes($e->getMessage()) . "'); window.history.back();</script>";
}

$conn->close();
?>
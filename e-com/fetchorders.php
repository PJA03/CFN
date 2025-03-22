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
    die(json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]));
}

$search = isset($_GET['search']) ? $_GET['search'] : '';
$filterStatus = isset($_GET['filter']) ? $_GET['filter'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'orderID';
$order = isset($_GET['order']) ? $_GET['order'] : 'asc';

// Validate sort and order parameters
$validSortFields = ['orderID', 'quantity', 'price_total', 'status', 'trackingLink']; // Add valid fields
$validOrder = ['asc', 'desc'];
$sort = in_array($sort, $validSortFields) ? $sort : 'orderID';
$order = in_array($order, $validOrder) ? $order : 'asc';

$whereClause = [];
$params = [];
$types = '';

if ($search) {
    $whereClause[] = "o.orderID LIKE ?";
    $params[] = "%$search%";
    $types .= 's';
}
if ($filterStatus) {
    $whereClause[] = "o.status = ?";
    $params[] = $filterStatus;
    $types .= 's';
}

$sql = "SELECT o.orderID, o.quantity, o.price_total, o.status, o.trackingLink, u.address 
        FROM tb_orders o 
        LEFT JOIN tb_user u ON o.user_id = u.user_id";
if (!empty($whereClause)) {
    $sql .= " WHERE " . implode(" AND ", $whereClause);
}
$sql .= " ORDER BY o.{$sort} {$order}";

$stmt = $conn->prepare($sql);
if (!empty($params) && !empty($whereClause)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

echo '<tbody>';
while ($row = $result->fetch_assoc()) {
    // Handle null address by defaulting to empty string
    $address = $row['address'] ?? '';
    $truncatedAddress = strlen($address) > 20 ? substr($address, 0, 20) . '...' : $address;
    echo "<tr>
            <td>{$row['orderID']}</td>
            <td>{$row['quantity']}</td>
            <td>₱{$row['price_total']}</td>
            <td>{$row['status']}</td>
            <td><a href='{$row['trackingLink']}' target='_blank'>{$row['trackingLink']}</a></td>
            <td>
                <span title='{$address}'>$truncatedAddress</span>
                <button class='btn btn-secondary btn-sm mt-1' onclick=\"copyToClipboard('{$address}')\">Copy Address</button>
            </td>
            <td>
                <button class='btn btn-info btn-sm' onclick='openPopup({$row['orderID']})'>View Details</button>
            </td>
          </tr>";
}
echo '</tbody>';

$stmt->close();
$conn->close();
?>

<script>
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        alert('Address copied to clipboard!');
    }).catch(err => {
        console.error('Failed to copy: ', err);
    });
}
</script>
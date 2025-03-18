<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';
$filterStatus = isset($_GET['filter']) ? $conn->real_escape_string($_GET['filter']) : '';
$sort = isset($_GET['sort']) ? $conn->real_escape_string($_GET['sort']) : '';
$order = isset($_GET['order']) ? $conn->real_escape_string($_GET['order']) : 'asc';

$sql = "SELECT orderID, quantity, price_total, status, trackingLink 
        FROM tb_orders 
        WHERE (orderID LIKE '%$search%' OR product_name LIKE '%$search%')";
if (!empty($filterStatus) && $filterStatus !== 'All Statuses') {
    $sql .= " AND status = '$filterStatus'";
}
if (!empty($sort)) {
    $sql .= " ORDER BY $sort $order";
} else {
    $sql .= " ORDER BY order_date DESC";
}
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $trackingLink = !empty($row['trackingLink']) ? $row['trackingLink'] : 'Not yet shipped';
        echo "<tr>
                <td>{$row['orderID']}</td>
                <td>{$row['quantity']}</td>
                <td>₱{$row['price_total']}</td>
                <td>{$row['status']}</td>
                <td><a href='{$trackingLink}' target='_blank'>{$trackingLink}</a></td>
                <td><button class='btn btn-primary btn-sm' onclick='openPopup({$row['orderID']})'>View</button></td>
              </tr>";
    }
} else {
    echo "<tr><td colspan='6'>No orders found.</td></tr>";
}

$conn->close();
?>
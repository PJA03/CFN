<?php
// fetchorders.php
include 'conn.php'; // Your DB connection file

$search = isset($_GET['search']) ? $conn->real_escape_string($_GET['search']) : '';

// Use correct column names: 'price_total' instead of 'total'
$sql = "SELECT orderID, quantity, price_total, status, trackingLink
        FROM tb_orders
        WHERE orderID LIKE '%$search%' 
           OR status LIKE '%$search%'
        ORDER BY orderID DESC";

$result = $conn->query($sql);

if ($result->num_rows > 0) {
  while($row = $result->fetch_assoc()) {
    echo "<tr onclick=\"openPopup({$row['orderID']})\">";
    echo "<td>{$row['orderID']}</td>";
    echo "<td>{$row['quantity']}</td>";
    echo "<td>₱{$row['price_total']}</td>";
    echo "<td>{$row['status']}</td>";
    echo "<td>{$row['trackingLink']}</td>";
    echo "<td><button class='btn btn-sm btn-primary'>Manage</button></td>";
    echo "</tr>";
  }
} else {
  echo "<tr><td colspan='6'>No orders found.</td></tr>";
}
$conn->close();
?>

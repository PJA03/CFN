<?php
require_once '../conn.php';

$search = isset($_GET['search']) ? $_GET['search'] : '';
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'order_date';
$order = isset($_GET['order']) ? $_GET['order'] : 'desc';

$whereClauses = [];
$params = [];
$types = '';

if ($search) {
    $whereClauses[] = "(o.orderID LIKE ? OR u.email LIKE ? OR u.address LIKE ?)";
    $searchTerm = "%$search%";
    $params = [$searchTerm, $searchTerm, $searchTerm];
    $types = str_repeat('s', count($params));
}

if ($filter) {
    $whereClauses[] = "o.status = ?";
    $params[] = $filter;
    $types .= 's';
}

$where = $whereClauses ? 'WHERE ' . implode(' AND ', $whereClauses) : '';
$sortColumn = in_array($sort, ['order_date', 'price_total', 'status']) ? $sort : 'order_date';
$sortOrder = $order === 'asc' ? 'ASC' : 'DESC';

// Query to get orders with item count and email
$query = "
    SELECT 
        o.orderID,
        o.order_date,
        o.status,
        o.price_total,
        o.trackingLink,
        u.address,
        u.email,
        COUNT(oi.order_item_id) AS item_count
    FROM tb_orders o
    LEFT JOIN tb_order_items oi ON o.orderID = oi.orderID
    JOIN tb_user u ON o.user_id = u.user_id
    $where
    GROUP BY o.orderID
    ORDER BY $sortColumn $sortOrder
";

$stmt = $conn->prepare($query);
if ($params) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $address = htmlspecialchars($row['address'] ?? 'No address');
        $trackingLink = !empty($row['trackingLink']) ? "<a href='" . htmlspecialchars($row['trackingLink']) . "' target='_blank'>Track</a>" : "-";
        echo "<tr>";
        echo "<td>" . htmlspecialchars($row['orderID']) . "</td>";
        echo "<td><a href='#' class='items-count' onclick='openItemsPopup(" . $row['orderID'] . ")'>" . $row['item_count'] . "</a></td>";
        echo "<td>" . htmlspecialchars($row['email']) . "</td>";
        echo "<td>₱" . number_format($row['price_total'], 2) . "</td>";
        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
        echo "<td>" . $trackingLink . "</td>";
        echo "<td class='address-container'>
                <span class='address-text'>" . $address . "</span>
                <i class='bi bi-clipboard copy-icon' onclick='copyToClipboard(\"" . $address . "\")'></i>
              </td>";
        echo "<td><button class='btn btn-primary btn-sm' onclick='openPopup(" . $row['orderID'] . ")'>View</button></td>";
        echo "</tr>";
    }
} else {
    echo "<tr><td colspan='8'>No orders found</td></tr>";
}

$stmt->close();
$conn->close();
?>
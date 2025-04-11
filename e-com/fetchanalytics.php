
<?php
header('Content-Type: application/json');
require_once 'auth_check.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "db_cfn";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    echo json_encode(['error' => 'Connection failed']);
    exit();
}

$startDate = isset($_GET['start']) ? $conn->real_escape_string($_GET['start']) : '';
$endDate = isset($_GET['end']) ? $conn->real_escape_string($_GET['end']) : '';

if (empty($startDate) || empty($endDate)) {
    echo json_encode(['error' => 'Invalid date range']);
    exit();
}

// Calculate the difference in days for chart granularity
$start = new DateTime($startDate);
$end = new DateTime($endDate);
$interval = $start->diff($end);
$daysDiff = $interval->days;

// 1. Total Sales (only "Delivered" orders)
$sql = "SELECT SUM(price_total) as total_sales 
        FROM tb_orders 
        WHERE order_date BETWEEN '$startDate' AND '$endDate 23:59:59' 
        AND status = 'Delivered'";
$result = $conn->query($sql);
$totalSales = $result->fetch_assoc()['total_sales'] ?? 0;

// 2. New Users (users who placed their first order in the date range)
$sql = "SELECT COUNT(DISTINCT user_id) as new_users 
        FROM tb_orders o1 
        WHERE order_date BETWEEN '$startDate' AND '$endDate 23:59:59'
        AND order_date = (
            SELECT MIN(order_date) 
            FROM tb_orders o2 
            WHERE o2.user_id = o1.user_id
        )";
$result = $conn->query($sql);
$newUsers = $result->fetch_assoc()['new_users'] ?? 0;

// 3. Repeat Purchase Percentage
// Step 1: Count total orders in the date range
$sql = "SELECT COUNT(*) as total_orders 
        FROM tb_orders 
        WHERE order_date BETWEEN '$startDate' AND '$endDate 23:59:59'";
$result = $conn->query($sql);
$totalOrders = $result->fetch_assoc()['total_orders'] ?? 0;

// Step 2: Count orders by users who have more than one order
$sql = "SELECT COUNT(*) as repeat_orders 
        FROM (
            SELECT user_id 
            FROM tb_orders 
            WHERE order_date BETWEEN '$startDate' AND '$endDate 23:59:59'
            GROUP BY user_id 
            HAVING COUNT(*) > 1
        ) as repeat_users";
$result = $conn->query($sql);
$repeatOrders = $result->fetch_assoc()['repeat_orders'] ?? 0;

$repeatPurchase = ($totalOrders > 0) ? ($repeatOrders / $totalOrders) * 100 : 0;

// 4. Chart Data (Daily, Weekly, or Monthly)
$chartLabels = [];
$chartData = [];

if ($daysDiff >= 5 && $daysDiff <= 60) {
    // Daily (5 to 60 days)
    $sql = "SELECT DATE(order_date) as period, SUM(price_total) as total 
            FROM tb_orders 
            WHERE order_date BETWEEN '$startDate' AND '$endDate 23:59:59' 
            AND status = 'Delivered'
            GROUP BY DATE(order_date)";
    $result = $conn->query($sql);

    $currentDate = new DateTime($startDate);
    $endDateTime = new DateTime($endDate);
    while ($currentDate <= $endDateTime) {
        $chartLabels[] = $currentDate->format('Y-m-d');
        $chartData[] = 0; // Default to 0
        $currentDate->modify('+1 day');
    }

    while ($row = $result->fetch_assoc()) {
        $index = array_search($row['period'], $chartLabels);
        if ($index !== false) {
            $chartData[$index] = (float)$row['total'];
        }
    }
} elseif ($daysDiff > 60 && $daysDiff < 365) {
    // Weekly (61 days to less than a year)
    $sql = "SELECT YEARWEEK(order_date, 1) as period, SUM(price_total) as total 
            FROM tb_orders 
            WHERE order_date BETWEEN '$startDate' AND '$endDate 23:59:59' 
            AND status = 'Delivered'
            GROUP BY YEARWEEK(order_date, 1)";
    $result = $conn->query($sql);

    $currentDate = new DateTime($startDate);
    $endDateTime = new DateTime($endDate);
    while ($currentDate <= $endDateTime) {
        $weekStart = clone $currentDate;
        $weekStart->modify('Monday this week');
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');
        if ($weekEnd > $endDateTime) $weekEnd = $endDateTime;
        $label = $weekStart->format('Y-m-d') . ' to ' . $weekEnd->format('Y-m-d');
        $chartLabels[] = $label;
        $chartData[] = 0; // Default to 0
        $currentDate->modify('+1 week');
    }

    while ($row = $result->fetch_assoc()) {
        $yearWeek = $row['period'];
        $year = substr($yearWeek, 0, 4);
        $week = substr($yearWeek, 4);
        $weekStart = (new DateTime())->setISODate($year, $week, 1);
        $weekEnd = clone $weekStart;
        $weekEnd->modify('+6 days');
        $label = $weekStart->format('Y-m-d') . ' to ' . $weekEnd->format('Y-m-d');
        $index = array_search($label, $chartLabels);
        if ($index !== false) {
            $chartData[$index] = (float)$row['total'];
        }
    }
} elseif ($daysDiff >= 365) {
    // Monthly (1 year or more)
    $sql = "SELECT DATE_FORMAT(order_date, '%Y-%m') as period, SUM(price_total) as total 
            FROM tb_orders 
            WHERE order_date BETWEEN '$startDate' AND '$endDate 23:59:59' 
            AND status = 'Delivered'
            GROUP BY DATE_FORMAT(order_date, '%Y-%m')";
    $result = $conn->query($sql);

    $currentDate = new DateTime($startDate);
    $endDateTime = new DateTime($endDate);
    while ($currentDate <= $endDateTime) {
        $chartLabels[] = $currentDate->format('Y-m');
        $chartData[] = 0; // Default to 0
        $currentDate->modify('+1 month');
    }

    while ($row = $result->fetch_assoc()) {
        $index = array_search($row['period'], $chartLabels);
        if ($index !== false) {
            $chartData[$index] = (float)$row['total'];
        }
    }
}

// 5. Top Selling Products
$sql = "SELECT product_name, SUM(quantity) as total_quantity 
        FROM tb_orders 
        WHERE order_date BETWEEN '$startDate' AND '$endDate 23:59:59' 
        AND status = 'Delivered'
        GROUP BY productID, product_name 
        ORDER BY total_quantity DESC 
        LIMIT 10";
$result = $conn->query($sql);
$topProducts = [];
while ($row = $result->fetch_assoc()) {
    $topProducts[] = [
        'product_name' => $row['product_name'],
        'quantity' => (int)$row['total_quantity']
    ];
}

$sql = "SELECT 
            orderID,
            status,
            price_total as total_amount,
            order_date
        FROM tb_orders
        WHERE order_date BETWEEN '$startDate' AND '$endDate 23:59:59'";
$result = $conn->query($sql);
$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = [
        'orderID' => $row['orderID'],
        // 'customer_name' => $row['customer_name'],
        'status' => $row['status'],
        'total_amount' => (float)$row['total_amount'],
        'order_date' => $row['order_date']
    ];
}

$response = [
    'totalSales' => (float)$totalSales,
    'newUsers' => (int)$newUsers,
    'repeatPurchase' => (float)$repeatPurchase,
    'chartLabels' => $chartLabels,
    'chartData' => $chartData,
    'topProducts' => $topProducts
    ,
    'orders' => $orders
];

echo json_encode($response);

$conn->close();
?>
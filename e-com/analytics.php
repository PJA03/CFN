<?php
require_once 'auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="style1.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin Analytics</title>
    <style>
        .chart-container {
            max-width: 800px;
            margin: 30px auto;
            display: none;
        }
        .loading-spinner {
            display: inline-block;
            width: 1.5rem;
            height: 1.5rem;
            border: 2px solid #1F4529;
            border-top: 2px solid transparent;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 0.5rem;
        }
        @keyframes spin {
            to { transform: rotate(360deg); }
        }
        .stat-card {
            text-align: center;
            padding: 1.5rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            margin: 1rem;
        }
        .stat-title {
            font-size: 1rem;
            color: #666;
        }
        .value {
            font-size: 1.5rem;
            font-weight: bold;
            color: #1F4529;
        }
        .main-content {
            position: relative;
            padding-bottom: 4rem;
        }
        .export-btn-container {
            position: absolute;
            bottom: 1rem;
            right: 1rem;
        }
        .orders-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 1rem;
        }
        .orders-heading {
            font-family: 'Be Vietnam Pro', sans-serif;
            font-size: 1.5rem;
            margin: 0;
        }
        .table-responsive {
            margin: 1.5rem 0;
        }
    </style>
</head>
<body>
    <div class="d-flex flex-wrap">
        <!-- Sidebar -->
        <div class="col-12 col-md-2 sidebar d-flex flex-column p-3">
            <img src="images/cfn_logo.png" alt="Naturale Logo" class="img-fluid mb-3">
            <nav class="nav flex-column">
                <a class="nav-link" href="manageproductsA.php">Products</a>
                <a class="nav-link" href="managecontentA.php">Content</a>
                <a class="nav-link" href="manageordersA.php">Orders</a>
                <a class="nav-link active" href="analytics.php">Analytics</a>
                <a class="nav-link" href="manageuser.php">Users</a>
            </nav>
            <div class="date-picker mt-4">
                <label for="start">Start Date:</label>
                <input type="date" id="start" class="form-control mb-3" value="2025-03-01">
                <label for="end">End Date:</label>
                <input type="date" id="end" class="form-control" value="2025-03-31">
                <button id="filterBtn" class="btn btn-success mt-2 w-100">Filter Data <span id="filterSpinner" class="loading-spinner" style="display: none;"></span></button>
            </div>
            <div class="mt-auto">
                <hr>
                <div class="admin-name d-flex align-items-center">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <div class="d-flex align-items-center gap-2">
                        <span class="adminuser">Admin User</span>
                        <a href="/CFN/e-com/logout.php" class="btn btn-danger btn-sm" id="logout">Logout</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-12 col-md-10 p-4 main-content">
            <h3 class="mt-4 text-center">Analytics/Report</h3>
            <div class="bg-white p-4 shadow-sm rounded">
                <div class="row mb-4 justify-content-center">
                    <div class="col-md-3 stat-card">
                        <div class="stat-title">Total Sales</div>
                        <div class="value" id="totalSales">₱0.00</div>
                    </div>
                    <div class="col-md-3 stat-card">
                        <div class="stat-title">No. of New Users</div>
                        <div class="value" id="newUsers">0</div>
                    </div>
                    <div class="col-md-3 stat-card">
                        <div class="stat-title">Repeat Purchase %</div>
                        <div class="value" id="repeatPurchase">0%</div>
                    </div>
                </div>
            </div>

            <div class="chart-container bg-white p-4 shadow-sm rounded" id="chartContainer">
                <canvas id="salesChart"></canvas>
            </div>

            <div class="table-responsive">
                <table class="table bg-white shadow-sm" id="salesTable">
                    <thead>
                        <tr>
                            <th style="width: 20%;">No. of Sales</th>
                            <th style="width: 80%;">Top Selling Products</th>
                        </tr>
                    </thead>
                    <tbody id="topProductsTable">
                        <!-- Dynamically populated -->
                    </tbody>
                </table>
            </div>

            <div class="table-responsive mt-4">
                <div class="orders-header">
                    <h5 class="orders-heading">Orders by Status</h5>
                    <select id="statusFilter" class="form-select" style="width: auto;">
                        <option value="all">All Statuses</option>
                        <option value="waiting for payment">Waiting for Payment</option>
                        <option value="processing">Processing</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>
                <table class="table bg-white shadow-sm" id="ordersTable">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Status</th>
                            <th>Total Amount</th>
                            <th>Order Date</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody">
                        <!-- Dynamically populated -->
                    </tbody>
                </table>
            </div>

            <div class="export-btn-container">
                <button class="btn btn-success" id="exportPDFBtn">Export as PDF <span id="pdfSpinner" class="loading-spinner" style="display: none;"></span></button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let salesChart = null;
        let allOrders = [];

        function getDaysDifference(startDate, endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        }

        function updateChart(labels, data) {
            const ctx = document.getElementById('salesChart').getContext('2d');
            if (salesChart) {
                salesChart.destroy();
            }
            salesChart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Sales (₱)',
                        data: data,
                        fill: false,
                        borderColor: '#1F4529',
                        borderWidth: 2,
                        tension: 0.1,
                        pointRadius: 4,
                        pointBackgroundColor: '#1F4529'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        x: { title: { display: true, text: 'Date' } },
                        y: { beginAtZero: true, title: { display: true, text: 'Total Sales (₱)' } }
                    },
                    plugins: {
                        legend: { display: true, position: 'top' },
                        title: { display: true, text: 'Total Sales Over Time' }
                    }
                }
            });
        }

        function updateOrdersTable(orders) {
            const ordersTableBody = document.getElementById('ordersTableBody');
            const statusFilter = document.getElementById('statusFilter').value;
            
            ordersTableBody.innerHTML = '';
            if (!orders || orders.length === 0) {
                ordersTableBody.innerHTML = '<tr><td colspan="4">No orders available for this period.</td></tr>';
                return;
            }

            const filteredOrders = statusFilter === 'all' 
                ? orders 
                : orders.filter(order => order.status.toLowerCase() === statusFilter);

            if (filteredOrders.length === 0) {
                ordersTableBody.innerHTML = '<tr><td colspan="4">No orders found for this status.</td></tr>';
                return;
            }

            filteredOrders.forEach(order => {
                const row = `<tr>
                    <td>${order.orderID}</td>
                    <td>${order.status}</td>
                    <td>₱${parseFloat(order.total_amount).toFixed(2)}</td>
                    <td>${new Date(order.order_date).toLocaleDateString()}</td>
                </tr>`;
                ordersTableBody.insertAdjacentHTML('beforeend', row);
            });
        }

        function fetchAnalyticsData(startDate, endDate) {
            const filterBtn = document.getElementById('filterBtn');
            const filterSpinner = document.getElementById('filterSpinner');
            const chartContainer = document.getElementById('chartContainer');
            filterBtn.disabled = true;
            filterSpinner.style.display = 'inline-block';

            fetch(`fetchanalytics.php?start=${startDate}&end=${endDate}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    document.getElementById('totalSales').textContent = `₱${data.totalSales.toFixed(2)}`;
                    document.getElementById('newUsers').textContent = data.newUsers;
                    document.getElementById('repeatPurchase').textContent = `${data.repeatPurchase.toFixed(1)}%`;

                    const daysDiff = getDaysDifference(startDate, endDate);
                    if (daysDiff < 5) {
                        chartContainer.style.display = 'none';
                        Swal.fire({
                            icon: 'info',
                            title: 'Date Range Too Short',
                            text: 'The date range is less than 5 days. The chart will not be displayed.',
                            confirmButtonText: 'OK'
                        });
                    } else {
                        chartContainer.style.display = 'block';
                        updateChart(data.chartLabels, data.chartData);
                    }

                    const topProductsTable = document.getElementById('topProductsTable');
                    topProductsTable.innerHTML = '';
                    if (data.topProducts.length === 0) {
                        topProductsTable.innerHTML = '<tr><td colspan="2">No sales data available for this period.</td></tr>';
                    } else {
                        data.topProducts.forEach(product => {
                            const row = `<tr><td>${product.quantity}</td><td>${product.product_name}</td></tr>`;
                            topProductsTable.insertAdjacentHTML('beforeend', row);
                        });
                    }

                    allOrders = data.orders || [];
                    updateOrdersTable(allOrders);

                    document.getElementById('exportPDFBtn').dataset.analytics = JSON.stringify(data);

                    filterBtn.disabled = false;
                    filterSpinner.style.display = 'none';

                    Swal.fire({
                        icon: 'success',
                        title: 'Data Loaded',
                        text: 'Analytics data has been successfully loaded.',
                        timer: 1500,
                        showConfirmButton: false
                    });
                })
                .catch(error => {
                    console.error('Error fetching analytics:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load analytics data: ' + error.message,
                        confirmButtonText: 'OK'
                    });
                    filterBtn.disabled = false;
                    filterSpinner.style.display = 'none';
                });
        }

        function exportAsPDF() {
            const exportBtn = document.getElementById('exportPDFBtn');
            const pdfSpinner = document.getElementById('pdfSpinner');
            const startDate = document.getElementById('start').value;
            const endDate = document.getElementById('end').value;
            const analyticsData = JSON.parse(exportBtn.dataset.analytics || '{}');
            const statusFilter = document.getElementById('statusFilter').value;

            if (!startDate || !endDate || !analyticsData.totalSales) {
                Swal.fire({
                    icon: 'warning',
                    title: ' ülkeninData',
                    text: 'Please fetch analytics data before exporting.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            const filteredOrders = statusFilter === 'all' 
                ? allOrders 
                : allOrders.filter(order => order.status.toLowerCase() === statusFilter);

            exportBtn.disabled = true;
            pdfSpinner.style.display = 'inline-block';

            fetch('export_pdf.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    startDate: startDate,
                    endDate: endDate,
                    totalSales: analyticsData.totalSales,
                    newUsers: analyticsData.newUsers,
                    repeatPurchase: analyticsData.repeatPurchase,
                    topProducts: analyticsData.topProducts,
                    orders: filteredOrders,
                    statusFilter: statusFilter
                })
            })
            .then(response => {
                if (!response.ok) throw new Error('Failed to generate PDF');
                return response.blob();
            })
            .then(blob => {
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = `Analytics_Report_${startDate}_to_${endDate}${statusFilter !== 'all' ? '_' + statusFilter : ''}.pdf`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                exportBtn.disabled = false;
                pdfSpinner.style.display = 'none';

                Swal.fire({
                    icon: 'success',
                    title: 'PDF Exported',
                    text: 'The analytics report has been successfully exported as a PDF.',
                    timer: 1500,
                    showConfirmButton: false
                });
            })
            .catch(error => {
                console.error('Error exporting PDF:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Failed to export PDF: ' + error.message,
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                exportBtn.disabled = false;
                pdfSpinner.style.display = 'none';
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            // Check user role
            const userRole = '<?php echo $_SESSION['role']; ?>';
            if (userRole !== 'superadmin') {
                Swal.fire({
                    icon: 'error',
                    title: 'Access Denied',
                    text: 'You are not authorized to access this page!',
                    confirmButtonText: 'OK',
                    allowOutsideClick: false,
                    allowEscapeKey: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'manageordersA.php';
                    }
                });
                return; // Stop further execution
            }

            // Proceed with analytics if authorized
            const startDate = document.getElementById('start').value;
            const endDate = document.getElementById('end').value;
            fetchAnalyticsData(startDate, endDate);

            document.getElementById('filterBtn').addEventListener('click', function() {
                const startDate = document.getElementById('start').value;
                const endDate = document.getElementById('end').value;
                if (startDate && endDate && startDate <= endDate) {
                    fetchAnalyticsData(startDate, endDate);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Date Range',
                        text: 'Please ensure Start Date is not after End Date.',
                        confirmButtonText: 'OK',
                        confirmButtonColor: '#3085d6'
                    });
                }
            });

            document.getElementById('exportPDFBtn').addEventListener('click', exportAsPDF);

            document.getElementById('statusFilter').addEventListener('change', function() {
                updateOrdersTable(allOrders);
            });
        });
    </script>
</body>
</html>
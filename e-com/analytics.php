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
    <!-- SweetAlert2 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="style1.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin Analytics</title>
    <style>
        .chart-container {
            max-width: 800px;
            margin: 20px auto;
            display: none; /* Initially hidden */
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
            padding: 1rem;
            border-radius: 8px;
            background-color: #f8f9fa;
            margin: 0.5rem;
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
            </nav>
            <div class="date-picker mt-4">
                <label for="start">Start Date:</label>
                <input type="date" id="start" class="form-control mb-3" value="2024-10-01">
                <label for="end">End Date:</label>
                <input type="date" id="end" class="form-control" value="2024-10-31">
                <button id="filterBtn" class="btn btn-success mt-2 w-100">Filter Data <span id="filterSpinner" class="loading-spinner" style="display: none;"></span></button>
            </div>
            <div class="mt-auto">
                <hr>
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle fs-4 me-2"></i>
                    <span>Admin User</span>
                </div>
                <a href="logout.php" class="btn btn-danger">Logout</a>
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

            <!-- Chart Container -->
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
            <button class="btn btn-success mt-4" id="exportPDFBtn">Export as PDF <span id="pdfSpinner" class="loading-spinner" style="display: none;"></span></button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        let salesChart = null;

        // Function to calculate the difference in days between two dates
        function getDaysDifference(startDate, endDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const diffTime = Math.abs(end - start);
            return Math.ceil(diffTime / (1000 * 60 * 60 * 24));
        }

        // Function to initialize or update the chart
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
                        x: {
                            title: {
                                display: true,
                                text: 'Date'
                            }
                        },
                        y: {
                            beginAtZero: true,
                            title: {
                                display: true,
                                text: 'Total Sales (₱)'
                            }
                        }
                    },
                    plugins: {
                        legend: {
                            display: true,
                            position: 'top'
                        },
                        title: {
                            display: true,
                            text: 'Total Sales Over Time'
                        }
                    }
                }
            });
        }

        // Function to fetch analytics data and update the page
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
                    // Update stat cards
                    document.getElementById('totalSales').textContent = `₱${data.totalSales.toFixed(2)}`;
                    document.getElementById('newUsers').textContent = data.newUsers;
                    document.getElementById('repeatPurchase').textContent = `${data.repeatPurchase.toFixed(1)}%`;

                    // Update chart based on date range
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

                    // Update top selling products table
                    const topProductsTable = document.getElementById('topProductsTable');
                    topProductsTable.innerHTML = '';
                    if (data.topProducts.length === 0) {
                        topProductsTable.innerHTML = '<tr><td colspan="2">No sales data available for this period.</td></tr>';
                    } else {
                        data.topProducts.forEach(product => {
                            const row = `<tr>
                                <td>${product.quantity}</td>
                                <td>${product.product_name}</td>
                            </tr>`;
                            topProductsTable.insertAdjacentHTML('beforeend', row);
                        });
                    }

                    // Store the data in a hidden input for PDF export
                    document.getElementById('exportPDFBtn').dataset.analytics = JSON.stringify(data);

                    filterBtn.disabled = false;
                    filterSpinner.style.display = 'none';

                    // Show success message after data is loaded
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

        // Function to export as PDF
        function exportAsPDF() {
            const exportBtn = document.getElementById('exportPDFBtn');
            const pdfSpinner = document.getElementById('pdfSpinner');
            const startDate = document.getElementById('start').value;
            const endDate = document.getElementById('end').value;
            const analyticsData = JSON.parse(exportBtn.dataset.analytics || '{}');

            if (!startDate || !endDate || !analyticsData.totalSales) {
                Swal.fire({
                    icon: 'warning',
                    title: 'No Data',
                    text: 'Please fetch analytics data before exporting.',
                    confirmButtonText: 'OK',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

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
                    topProducts: analyticsData.topProducts
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
                a.download = `Analytics_Report_${startDate}_to_${endDate}.pdf`;
                document.body.appendChild(a);
                a.click();
                a.remove();
                window.URL.revokeObjectURL(url);

                exportBtn.disabled = false;
                pdfSpinner.style.display = 'none';

                // Show success message after PDF export
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

        // Initialize on page load with default dates
        document.addEventListener('DOMContentLoaded', function() {
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
        });
    </script>
</body>
</html>
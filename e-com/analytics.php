<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style1.css"> <!-- Custom styles -->
    <!-- Chart.js CDN -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <title>Admin Analytics</title>
    <style>
        .chart-container {
            max-width: 800px;
            margin: 20px auto;
        }
        /* Loading Spinner */
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
                <a class="nav-link" href="analytics.php">Analytics</a>
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
                <a href="#" class="text-white text-decoration-none mt-3">Log Out</a>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-12 col-md-10 p-4 main-content">
            <h3 class="mt-4 text-center">Analytics/Report</h3>
            <div class="bg-white p-4 shadow-sm rounded">
                <div class="row mb-4 justify-content-center">
                    <div class="col-md-2 stat-card">
                        <div class="stat-title">Total Sales</div>
                        <div class="value">₱12,000.00</div>
                    </div>
                    <div class="col-md-2 stat-card">
                        <div class="stat-title">Cart Abandonment</div>
                        <div class="value">37%</div>
                    </div>
                    <div class="col-md-2 stat-card">
                        <div class="stat-title">No. of New Users</div>
                        <div class="value">74</div>
                    </div>
                    <div class="col-md-2 stat-card">
                        <div class="stat-title">Repeat Purchase %</div>
                        <div class="value">15%</div>
                    </div>
                    <div class="col-md-2 stat-card">
                        <div class="stat-title">No. of Refunds</div>
                        <div class="value">47</div>
                    </div>
                </div>
            </div>

            <!-- Chart Container -->
            <div class="chart-container bg-white p-4 shadow-sm rounded">
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
                    <tbody>
                        <tr>
                            <td>245</td>
                            <td>Product A</td>
                        </tr>
                        <tr>
                            <td>215</td>
                            <td>Product B</td>
                        </tr>
                        <tr>
                            <td>210</td>
                            <td>Product C</td>
                        </tr>
                        <tr>
                            <td>180</td>
                            <td>Product D</td>
                        </tr>
                        <tr>
                            <td>176</td>
                            <td>Product E</td>
                        </tr>
                        <tr>
                            <td>150</td>
                            <td>Product F</td>
                        </tr>
                        <tr>
                            <td>138</td>
                            <td>Product G</td>
                        </tr>
                        <tr>
                            <td>115</td>
                            <td>Product H</td>
                        </tr>
                        <tr>
                            <td>90</td>
                            <td>Product I</td>
                        </tr>
                        <tr>
                            <td>78</td>
                            <td>Product J</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <button class="btn btn-success mt-4" onclick="location.href='export_pdf.php'">Export as PDF</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let salesChart = null;

        // Function to initialize or update the chart
        function updateChart(labels, data) {
            const ctx = document.getElementById('salesChart').getContext('2d');
            if (salesChart) {
                salesChart.destroy(); // Destroy previous chart instance
            }
            salesChart = new Chart(ctx, {
                type: 'line', // Changed to line graph
                data: {
                    labels: labels, // Dates or time periods
                    datasets: [{
                        label: 'Total Sales (₱)',
                        data: data, // Simulated or fetched sales data
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

        // Initial dummy data for line graph (replace with real data)
        function loadInitialChart() {
            const dummyLabels = [
                '2024-10-01', '2024-10-02', '2024-10-03', '2024-10-04', '2024-10-05',
                '2024-10-06', '2024-10-07', '2024-10-08', '2024-10-09', '2024-10-10'
            ];
            const dummyData = [1000, 1200, 1150, 1300, 1250, 1400, 1350, 1450, 1300, 1500];
            updateChart(dummyLabels, dummyData);
        }

        // Fetch and update chart based on date range
        function fetchChartData(startDate, endDate) {
            const filterBtn = document.getElementById('filterBtn');
            const filterSpinner = document.getElementById('filterSpinner');
            filterBtn.disabled = true;
            filterSpinner.style.display = 'inline-block';

            fetch(`fetchanalytics.php?start=${startDate}&end=${endDate}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network response was not ok');
                    return response.json();
                })
                .then(data => {
                    const labels = data.labels || [];
                    const salesData = data.sales || [];
                    updateChart(labels, salesData);
                    filterBtn.disabled = false;
                    filterSpinner.style.display = 'none';
                })
                .catch(error => {
                    console.error('Error fetching analytics:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load analytics data. Please try again.',
                        confirmButtonText: 'OK'
                    });
                    filterBtn.disabled = false;
                    filterSpinner.style.display = 'none';
                });
        }

        // Initialize chart on page load
        document.addEventListener('DOMContentLoaded', function() {
            loadInitialChart();
            document.getElementById('filterBtn').addEventListener('click', function() {
                const startDate = document.getElementById('start').value;
                const endDate = document.getElementById('end').value;
                if (startDate && endDate && startDate <= endDate) {
                    fetchChartData(startDate, endDate);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Invalid Date Range',
                        text: 'Please ensure Start Date is not after End Date.',
                        confirmButtonText: 'OK'
                    });
                }
            });
        });
    </script>
</body>
</html>
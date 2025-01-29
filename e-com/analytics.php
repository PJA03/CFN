    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
        <link rel="stylesheet" href="style1.css"> <!-- Custom styles -->
        <title>Admin Analytics</title>
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
                <input type="date" id="start" class="form-control mb-3" value="2024-10-10">
                <label for="end">End Date:</label>
                <input type="date" id="end" class="form-control">
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
                        <div class="value">&#8369;12,000.00</div>
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

            <div class="table-responsive">
        <table class="table bg-white shadow-sm">
            <thead>
                <tr>
                    <th style="width: 20%;">No. of Sales</th>
                    <th style="width: 80%;">Top Selling Products</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>245</td>
                    <td>Product Name</td>
                </tr>
                <tr>
                    <td>215</td>
                    <td>Product Name</td>
                </tr>
                <tr>
                    <td>210</td>
                    <td>Product Name</td>
                </tr>
                <tr>
                    <td>180</td>
                    <td>Product Name</td>
                </tr>
                <tr>
                    <td>176</td>
                    <td>Product Name</td>
                </tr>
                <tr>
                    <td>150</td>
                    <td>Product Name</td>
                </tr>
                <tr>
                    <td>138</td>
                    <td>Product Name</td>
                </tr>
                <tr>
                    <td>115</td>
                    <td>Product Name</td>
                </tr>
                <tr>
                    <td>90</td>
                    <td>Product Name</td>
                </tr>
                <tr>
                    <td>78</td>
                    <td>Product Name</td>
                </tr>
            </tbody>
        </table>

            </div>
            <button class="btn btn-success mt-4" onclick="location.href='export_pdf.php'">Export as PDF</button>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    </body>
    </html>

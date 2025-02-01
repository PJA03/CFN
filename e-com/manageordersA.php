<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
    <link rel="stylesheet" href="style2.css">
    <style>
        /* General Body Styling */
        body {
            font-family: "Be Vietnam Pro", sans-serif;
            background-color: #E8ECD7;
            margin: 0;
            padding: 0;
        }

        /* Sidebar Styling */
        .sidebar {
            height: 100vh;
            position: sticky;
            top: 0;
            display: flex;
            flex-direction: column;
            background-color: #1F4529;
            color: white;
            font-size: 1rem;
        }

        .sidebar h2 {
            font-family: "Bebas Neue", sans-serif;
            font-size: 2.5rem;
            margin-bottom: 2rem;
        }

        .sidebar nav .nav-link {
            font-family: "Bebas Neue", serif;
            font-weight: 400;
            font-size: 2rem;
            font-style: normal;
            text-decoration: none;
            color: #EED3B1;
            display: block;
            transition: all 0.3s ease-in-out;
        }

        .sidebar nav .nav-link:hover {
            color: #b4d644;
            text-decoration: underline;
        }

        .nav-link:visited {
            font-family: "Be Vietnam Pro", serif;
            font-size: 1.1rem;
            color: #1F4529;
            font-weight: bold;
        }

        .sidebar hr {
            margin: 2rem 0;
            border-color: white;
        }

        .sidebar .mt-auto {
            margin-top: auto;
        }

        /* Main Content Styling */
        .main-content h1 {
            font-size: 1.5rem;
            font-family: "Bebas Neue", serif;
            color: #1F4529;
            margin-bottom: 1rem;
            text-align: left;
        }

        /* Main Content Styling for h3 */
        .main-content h3 {
            font-size: 2.5rem;
            font-family: "Bebas Neue", serif;
            color: #1F4529;
            margin-bottom: 1rem;
            text-align: center;
        }

        .btn-green {
            background-color: #1F4529;
            color: white;
        }

        /* Styles for the popup */
        .popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .popup-content {
            position: relative;
            background-color: #fff;
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 900px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            animation: fadeIn 0.3s ease-in-out;
            overflow-x: auto;
        }

        /* Align close button to the top-right corner */
        .popup-content .close {
            position: absolute;
            top: 15px;
            right: 20px;
            font-size: 20px;
            cursor: pointer;
        }

        .table-container {
            max-height: 400px;
            overflow-y: auto;
        }

        .table {
            width: 100%;
            margin: 0;
            border-collapse: collapse;
            background-color: #f8f9fa;
        }

        .table th,
        .table td {
            padding: 10px;
            text-align: center;
            border: 1px solid #dee2e6;
        }

        .table td:first-child, .table th:first-child {
    white-space: nowrap; /* Prevent wrapping */
    width: 150px; /* Adjust the width */
}


        .table th {
            background-color: #e9ecef;
            font-weight: bold;
        }

        .table td select {
            width: 100%;
            padding: 5px;
        }

        .table td input[type="text"] {
            width: 100%;
            padding: 5px;
        }

        .table td ul {
            list-style: none;
            padding: 0;
            margin: 0;
            text-align: left;
        }

        .table td ul li {
            margin-bottom: 5px;
        }

        .actions {
            display: flex;
            justify-content: flex-end;
            gap: 15px;
        }

        .btn-save,
        .btn-discard {
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            border: none;
            font-size: 1rem;
        }

        .btn-save {
            background-color: #1F4529;
            color: white;
        }

        .btn-discard {
            background-color: #dc3545;
            color: white;
        }

        /* Styles for Zoomed Image Popup */
        .zoom-popup {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            z-index: 2000;
            justify-content: center;
            align-items: center;
        }

        .zoom-popup img {
            max-width: 90%;
            max-height: 90%;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(255, 255, 255, 0.2);
        }

        .zoom-popup .close {
            position: absolute;
            top: 20px;
            right: 30px;
            font-size: 30px;
            color: white;
            cursor: pointer;
        }

        /* For dropdown and input text box to flex */
.table td select,
.table td input[type="text"] {
    flex: 1; /* Allows them to grow within the flex container */
    min-width: 150px; /* Minimum width to prevent shrinking too much */
    padding: 8px; /* Consistent padding for better UI */
}


    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-2 sidebar d-flex flex-column p-3">
                <img src="images/cfn_logo.png" alt="Naturale Logo" class="img-fluid mb-3">
                <nav class="nav flex-column">
    <a class="nav-link" href="manageproductsA.php">Products</a>
    <a class="nav-link" href="managecontentA.php">Content</a>
    <a class="nav-link" href="manageordersA.php">Orders</a>
    <a class="nav-link" href="analytics.php">Analytics</a>
</nav>

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
            <div class="col-md-10 p-4 main-content">
                <h3 class="mt-4 text-center">Orders Table</h3>

                <div class="d-flex justify-content-end align-items-center mb-3">
                    <input type="text" class="form-control w-25 me-2" placeholder="Search Order">
                </div>

                <!-- Orders Table -->
                <div class="bg-white p-4 rounded shadow-sm">
                    <table class="table table-bordered text-center">
                        <thead>
                            <tr class="table-success">
                                <th>Order ID</th>
                                <th>Date</th>
                                <th>Number of Items</th>
                                <th>Total</th>
                                <th>Status</th>
                                <th>Tracking Link</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>11/12/24</td>
                                <td>4</td>
                                <td>500.00</td>
                                <td>Pending</td>
                                <td>P2024ABC</td>
                                <td>
                                    <a href="#" class="btn btn-sm btn-green" onclick="openPopup()">Manage Order</a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <!-- End of Orders Table -->
            </div>
        </div>
    </div>

   <!-- Modal (inside #orderPopup) -->
<div id="orderPopup" class="popup">
    <div class="popup-content">
        <span class="close" onclick="closePopup()">&times;</span>
        <h3>Order Details</h3>
        <div class="table-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Order ID</th>
                        <th>Date</th>
                        <th>Items</th>
                        <th>Total</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Tracking Link</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1</td>
                        <td>11/12/2024</td>
                        <td>
                        <div style="text-align: left; display: inline-block;">
                        <span>2 Creams</span><br />
                        <span>1 Night Cream</span>
                        </div>

                    </td>

                        <td>500.00</td>
                        <td>
                            <!-- Flex container for dropdown -->
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <select id="status" name="status" class="form-control" style="flex: 1; min-width: 150px;">
                                <option value="invalidate">Invalidate</option>
                                    <option value="pending">Pending</option>
                                    <option value="shipped">Shipped</option>
                                    <option value="delivered">Delivered</option>
                                   
                                </select>
                            </div>
                        </td>
                        <td>
                        <div style="display: flex; align-items: center; gap: 10px;">
    <input type="checkbox" id="confirmPayment" />
    <label for="confirmPayment">Approved</label>
</div>
<a href="#" id="reviewPayment">Review Payment</a>

                        </td>
                        <td>
                            <!-- Flex container for text box -->
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <input type="text" id="trackingLink" name="trackingLink" 
                                       placeholder="Enter tracking link" 
                                       class="form-control" style="flex: 1; min-width: 200px;">
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <div class="actions mt-3">
            <button type="button" class="btn-save" onclick="saveChanges()">Save changes</button>
            <button type="button" class="btn-discard" onclick="discardChanges()">Discard changes</button>
        </div>
    </div>
</div>


    <!-- Receipt Popup -->
    <div id="receiptPopup" class="popup">
        <div class="popup-content">
            <span class="close" onclick="closeReceiptPopup()">&times;</span>
            <h3>Payment Receipt</h3>
            <div class="text-center">
                <!-- Clickable Receipt Image -->
                <img id="receiptImage" src="images/gkas.jpeg" alt="Receipt Image" class="img-fluid" style="max-width: 100%; max-height: 500px;" onclick="openZoomedImage()">
            </div>
        </div>
    </div>

    <!-- Zoomed Image Popup -->
    <div id="zoomPopup" class="zoom-popup">
        <span class="close" onclick="closeZoomedImage()">&times;</span>
        <img id="zoomedImage" src="" alt="Zoomed Image">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let isChanged = false;

        function openPopup() {
            document.getElementById('status').value = 'pending';
            document.getElementById('confirmPayment').checked = false;
            document.getElementById('trackingLink').value = '';
            document.getElementById('orderPopup').style.display = 'flex';
            isChanged = false;
        }

        function closePopup() {
            if (isChanged) {
                const confirmClose = confirm('You have unsaved changes. Do you want to discard them?');
                if (!confirmClose) return;
            }
            document.getElementById('orderPopup').style.display = 'none';
        }

        function saveChanges() {
            const status = document.getElementById('status').value;
            const isPaymentConfirmed = document.getElementById('confirmPayment').checked;
            const trackingLink = document.getElementById('trackingLink').value;

            console.log(`Order status updated to: ${status}`);
            console.log(`Payment confirmed: ${isPaymentConfirmed}`);
            console.log(`Tracking link: ${trackingLink}`);
            alert('Changes saved successfully!');
            isChanged = false;
            closePopup();
        }

        function discardChanges() {
            const confirmDiscard = confirm('Are you sure you want to discard all changes?');
            if (confirmDiscard) {
                isChanged = false;
                closePopup();
            }
        }

        document.getElementById('status').addEventListener('change', () => {
            isChanged = true;
        });
        document.getElementById('confirmPayment').addEventListener('change', () => {
            isChanged = true;
        });
        document.getElementById('trackingLink').addEventListener('input', () => {
            isChanged = true;
        });

        function openReceiptPopup() {
            document.getElementById('receiptPopup').style.display = 'flex';
        }

        function closeReceiptPopup() {
            document.getElementById('receiptPopup').style.display = 'none';
        }

        document.getElementById('reviewPayment').addEventListener('click', (event) => {
            event.preventDefault();
            openReceiptPopup();
        });

        function openZoomedImage() {
            const receiptImageSrc = document.getElementById('receiptImage').src;
            document.getElementById('zoomedImage').src = receiptImageSrc;
            document.getElementById('zoomPopup').style.display = 'flex';
        }

        function closeZoomedImage() {
            document.getElementById('zoomPopup').style.display = 'none';
        }
    </script>
</body>
</html>

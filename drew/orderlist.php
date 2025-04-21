<?php
include '../conn.php'; 
include '../cookie_handler.php';

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ../Registration_Page/registration.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch order data from the view
$query = "
    SELECT 
        o.orderID, 
        o.order_date, 
        o.price_total, 
        o.status, 
        o.trackingLink,
        COUNT(oi.order_item_id) AS item_count
    FROM tb_orders o
    LEFT JOIN tb_order_items oi ON o.orderID = oi.orderID
    WHERE o.user_id = ?
    GROUP BY o.orderID
    ORDER BY o.order_date DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$orders = [];
while ($row = $result->fetch_assoc()) {
    $orders[] = $row;
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["orderID"])) {
    $orderID = $_POST["orderID"];

    // Update the order status to "Cancelled" if the status is "Waiting for Payment"
    $query = "UPDATE tb_orders SET status = 'Cancelled' WHERE orderID = ? AND status = 'Waiting for Payment'";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $orderID);
    
    if ($stmt->execute()) {
        $_SESSION['message'] = "Order cancelled successfully.";
    } else {
        $_SESSION['message'] = "Failed to cancel order.";
    }

    header("Location: orderlist.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="orderlist.css?v=<?php echo time(); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.min.css">
    <style>
        .items-popup {
            position: fixed;
            top: 0; left: 0;
            width: 100%; height: 100%;
            display: none;
            align-items: center; 
            justify-content: center;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 9999;
        }
        .items-popup .popup-content {
            background-color: #fff;
            border-radius: 8px;
            padding: 1.5rem;
            max-width: 600px;
            width: 90%;
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .items-popup .popup-content .close {
            position: absolute;
            top: 1rem; right: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
        }
        .items-popup .order-items-title {
            text-align: center;
            margin: 0 0 1rem 0;
            padding-top: 0.5rem;
            font-family: "Bebas Neue", serif;
            font-size: 2rem;
            color: #1F4529;
            width: 100%;
        }
        .items-popup .table-container {
            overflow-x: auto;
            margin-top: 1rem;
            width: 100%;
        }
        .items-popup .table-container table {
            width: 100%;
            table-layout: auto;
        }
        .items-popup .table-container th,
        .items-popup .table-container td {
            min-width: 100px;
            white-space: normal;
            word-wrap: break-word;
            text-align: left;
        }
        .items-count {
            color: #0d6efd;
            text-decoration: underline;
            cursor: pointer;
        }
        .items-count:hover {
            color: #0056b3;
        }
    </style>
</head>
<body>
    <header>
        <div class="logo">
            <a href="../Home_Page/Home.php"><img src="../Home_Page/cfn_logo2.png" alt="Logo" class="logo-image"/></a>
        </div>
        <div class="navbar">
            <p class="usernamedisplay">Bonjour, <?php echo htmlspecialchars($_SESSION['username'] ?? 'User', ENT_QUOTES, 'UTF-8'); ?>!</p>            
            <form action="../Home_Page/ProductScroll.php" method="GET" class="search-form" onsubmit="return validateSearch()">
                <input type="text" name="search" class="search-bar" id="searchBar" placeholder="Search Product">
            </form>            
            <div class="icons">
                <a href="../Home_Page/Home.php"><i class="fa-solid fa-house home"></i></a>
                <a href="../drew/cart.php"><i class="fa-solid fa-cart-shopping cart"></i></a>
                <a href="../User_Profile_Page/UserProfile.php"><i class="far fa-user-circle fa-2x icon-profile"></i></a>
            </div>
        </div>
    </header>

    <main>
        <section class="cart-container">
            <div class="cart-content">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th class="date-header">Date</th>
                            <th class="order-list-header">Order List</th>
                            <th class="total-header">Total</th>
                            <th class="tracking-link-header">Tracking Link</th>
                            <th class="status-header">Status</th>
                            <th class="action-header">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($orders)): ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars(date('M j, Y', strtotime($order['order_date']))); ?></td>
                                    <td><a href="#" class="items-count" onclick="openItemsPopup(<?php echo $order['orderID']; ?>)"><?php echo $order['item_count']; ?> Item<?php echo $order['item_count'] != 1 ? 's' : ''; ?></a></td>
                                    <td>₱<?php echo number_format($order['price_total'], 2); ?></td>
                                    <td><?php echo $order['trackingLink'] ? '<a href="' . htmlspecialchars($order['trackingLink']) . '" target="_blank">' . htmlspecialchars($order['trackingLink']) . '</a>' : '-'; ?></td>
                                    <td><?php echo htmlspecialchars($order['status']); ?></td>
                                    <td>
                                        <?php if ($order['status'] === 'Waiting for Payment'): ?>
                                            <form action="" method="POST">
                                                <input type="hidden" name="orderID" value="<?php echo $order['orderID']; ?>">
                                                <button type="submit" class="cancel-btn">Cancel</button>
                                            </form>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="6">You have no orders yet.</td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <!-- ITEMS LIST MODAL -->
    <div id="itemsPopup" class="items-popup">
        <div class="popup-content">
            <span class="close" onclick="closeItemsPopup()">×</span>
            <h4 class="order-items-title">Order Items</h4>
            <div class="table-container">
                <table class="table table-bordered">
                    <thead class="table-success">
                        <tr>
                            <th>Product Name</th>
                            <th>Quantity</th>
                            <th>Unit Price</th>
                            <th>Total</th>
                        </tr>
                    </thead>
                    <tbody id="itemsListTable"></tbody>
                </table>
            </div>
        </div>
    </div>

    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <img src="../Resources/cfn_logo.png" alt="Naturale Logo" class="footer-logo">
            </div>
            <div class="footer-right">
                <ul class="footer-nav">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#ModalTerms">Terms and Conditions</a></li>
                    <li><a href="#" data-bs-toggle="modal" data-bs-target="#ModalPrivacy">Privacy Policy</a></li>
                </ul>
            </div>
            <div class="social-icons">
                <p>SOCIALS</p>
                <a href="https://www.facebook.com/share/1CRTizfAxP/?mibextid=wwXIfr" target="_blank"><i class="fab fa-facebook"></i></a>
                <a href="https://www.instagram.com/cosmeticasfraiche?igsh=ang2MHg1MW5qZHQw" target="_blank"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
        <div class="footer-center">
            © COSMETICAS 2024
        </div>
    </footer>

    <!-- Modal Terms and Conditions -->
    <div class="modal fade" id="ModalTerms" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #1F4529;">
                    <h5 class="modal-title" id="exampleModalLongTitle" style="font-weight: bold;">CFN Naturale Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <b>1. Introduction</b><br>
                    Welcome to Cosmeticas Fraiche Naturale. By accessing or using our website, you agree to comply with these Terms of Use. If you do not agree, please do not use our services.<br><br>
                    <b>2. Use of Website</b><br>
                    You must be at least 16 years old to use our website. You agree to use the website only for lawful purposes and in accordance with these terms.<br><br>
                    <b>3. Account Registration</b><br>
                    To make purchases, you may need to create an account. You are responsible for maintaining the confidentiality of your account and password.<br><br>
                    <b>4. Orders and Payments</b><br>
                    All prices are listed in Philippine Peso. We reserve the right to refuse or cancel orders at our discretion. Payments must be completed before orders are processed.<br><br>
                    <b>5. Shipping and Cancellation of Orders</b><br>
                    We strive to deliver products in a timely manner. All sales are final, and we do not accept returns or exchanges. As for cancellations, it is allowed as long as the orders are not confirmed yet.<br><br>
                    <b>6. Intellectual Property</b><br>
                    All content on this site, including logos, text, and images, is owned by Cosmeticas Fraiche Naturale and may not be used without permission.<br><br>
                    <b>7. Limitation of Liability</b><br>
                    We are not responsible for any indirect, incidental, or consequential damages arising from the use of our website or products.<br><br>
                    <b>8. Changes to Terms</b><br>
                    We may update these terms at any time. Continued use of the website means you accept the updated terms.<br><br>
                    <b>9. Contact Information</b><br>
                    For any questions, contact us at cosmeticasfraichenaturale@gmail.com.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Privacy Policy -->
    <div class="modal fade" id="ModalPrivacy" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #1F4529;">
                    <h5 class="modal-title" id="exampleModalLongTitle" style="font-weight: bold;">CFN Naturale Privacy Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <b>1. Information We Collect</b><br>
                    We collect personal information, such as your name, email, shipping address, and payment details when you make a purchase or create an account.<br><br>
                    <b>2. How We Use Your Information</b><br>
                    We use your information to process orders, improve our website, and communicate with you about promotions or support inquiries.<br><br>
                    <b>3. Sharing of Information</b><br>
                    We do not sell your personal information. However, we may share it with third-party service providers for payment processing or shipping.<br><br>
                    <b>4. Cookies and Tracking</b><br>
                    We use cookies to enhance your browsing experience. You can disable cookies in your browser settings, but some features may not function properly.<br><br>
                    <b>5. Data Security</b><br>
                    We implement security measures to protect your data but cannot guarantee complete security due to internet vulnerabilities.<br><br>
                    <b>6. Your Rights</b><br>
                    You have the right to access, update, or delete your personal information. Contact us at cosmeticasfraichenaturale@gmail.com for any requests.<br><br>
                    <b>7. Changes to Privacy Policy</b><br>
                    We may update this policy. Continued use of our services after updates means you accept the revised policy.<br><br>
                    <b>8. Contact Information</b><br>
                    For privacy-related concerns, contact us at cosmeticasfraichenaturale@gmail.com.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        function openItemsPopup(orderID) {
            fetch(`../e-com/getorderitems.php?orderID=${orderID}`)
                .then(response => {
                    if (!response.ok) throw new Error("Network response was not ok");
                    return response.json();
                })
                .then(data => {
                    const itemsTable = document.getElementById("itemsListTable");
                    itemsTable.innerHTML = "";
                    if (data.items && data.items.length > 0) {
                        data.items.forEach(item => {
                            const row = document.createElement("tr");
                            row.innerHTML = `
                                <td>${item.product_name}</td>
                                <td>${item.quantity}</td>
                                <td>₱${parseFloat(item.unit_price).toFixed(2)}</td>
                                <td>₱${parseFloat(item.item_total).toFixed(2)}</td>
                            `;
                            itemsTable.appendChild(row);
                        });
                    } else {
                        itemsTable.innerHTML = "<tr><td colspan='4'>No items found</td></tr>";
                    }
                    document.getElementById("itemsPopup").style.display = "flex";
                })
                .catch(error => {
                    console.error("Error fetching items:", error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Failed to load items: ' + error.message,
                        confirmButtonText: 'OK'
                    });
                });
        }

        function closeItemsPopup() {
            document.getElementById("itemsPopup").style.display = "none";
        }

        document.addEventListener('DOMContentLoaded', function() {
            const cancelButtons = document.querySelectorAll('.cancel-btn');
            cancelButtons.forEach(button => {
                button.addEventListener('click', function(e) {
                    e.preventDefault();
                    const orderID = this.closest('form').querySelector('input[name="orderID"]').value;
                    Swal.fire({
                        title: 'Are you sure?',
                        text: 'Do you want to cancel this order?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d34646',
                        cancelButtonColor: '#1f4529',
                        confirmButtonText: 'Yes, cancel it!',
                        cancelButtonText: 'No, keep it'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            fetch('', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `orderID=${orderID}`
                            })
                            .then(response => response.text())
                            .then(() => {
                                const row = this.closest('tr');
                                row.querySelector('td:nth-child(5)').textContent = 'Cancelled';
                                this.disabled = true;
                                this.textContent = 'Cancelled';
                                this.style.backgroundColor = 'gray';
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Cancelled!',
                                    text: 'Your order has been cancelled.',
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                            })
                            .catch(error => {
                                console.error('Error:', error);
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to cancel order: ' + error.message,
                                    confirmButtonText: 'OK'
                                });
                            });
                        }
                    });
                });
            });

            // Display session message if set
            <?php if (isset($_SESSION['message'])): ?>
                Swal.fire({
                    icon: '<?php echo strpos($_SESSION['message'], 'successfully') !== false ? 'success' : 'error'; ?>',
                    title: '<?php echo strpos($_SESSION['message'], 'successfully') !== false ? 'Success' : 'Error'; ?>',
                    text: '<?php echo $_SESSION['message']; ?>',
                    confirmButtonText: 'OK'
                });
                <?php unset($_SESSION['message']); ?>
            <?php endif; ?>
        });
    </script>
</body>
</html>
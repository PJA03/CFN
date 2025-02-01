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
    <h1>Welcome Back, Admin!</h1>
    <h3 class="mt-4 text-center">Products Table</h3>

    <div class="d-flex justify-content-end align-items-center mb-3">
        <input type="text" class="form-control w-25 me-2" placeholder="Search Product">
        <button id="addProductBtn" class="btn btn-success">+ Add Product</button>
    </div>

    <!-- Empty White Container -->
    <div class="bg-white p-4 rounded shadow-sm">
    <div class="product-grid">
        <!-- 15 Placeholder Gray Containers -->
        <?php for ($i = 0; $i < 15; $i++): ?>
            <div class="product-card">
                <img src="images/image.png" alt="Placeholder">
                <h5>Clarifying Shampoo Bar</h5>
                <p>₱100.00 - 65 left</p>
                <div class="actions">
    <i class="bi bi-pencil-square edit-icon" onclick="redirectToEdit()"></i>
    <i class="bi bi-trash delete-icon" onclick="removeItem(this)"></i>
</div>

            </div>
        <?php endfor; ?>
    </div>
</div>
</div>

    </div>
    <!-- End of White Container -->
</div>

<script>
    // Redirect to addproduct.php when clicking the "Add Product" button
    document.getElementById("addProductBtn").addEventListener("click", function() {
        window.location.href = "addproduct.php";
    });

    // Redirect to editproduct.php when clicking the pencil icon
    function redirectToEdit() {
        window.location.href = "editproduct.php";
    }

    // Remove the item when clicking the trash icon
    function removeItem(element) {
        if (confirm("Are you sure you want to delete this item?")) {
            element.closest('.product-card').remove(); // Removes only the clicked product
        }
    }
</script>

</body>
</html>
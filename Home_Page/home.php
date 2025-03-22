<?php
// Start the session (if needed)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if the user is logged in
if (isset($_SESSION['email'])) {
    $user = [
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'first_name' => $_SESSION['first_name'],
        'last_name' => $_SESSION['last_name'],
        'contact_no' => $_SESSION['contact_no'],
        'address' => $_SESSION['address'],
        'profile_image' => $_SESSION['profile_image'],
    ];
} else {
    $user = ['username' => 'Guest']; // Default for non-logged-in users
}

// Include database connection
include '../conn.php'; 

// Check if the search query is set
if (isset($_GET['search'])) {
    $search = "%" . $_GET['search'] . "%";

    // Prepare the SQL query
    $stmt = $conn->prepare("SELECT * FROM tb_products WHERE product_name LIKE ? OR product_category LIKE ?");
    $stmt->bind_param("ss", $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch results
    $products = [];
    while ($row = $result->fetch_assoc()) {
        $products[] = $row;
    }

    $stmt->close();
} else {
    $products = []; // Default to empty if no search query
}

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - Cosmeticas Fraiche Naturale</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="main.js" defer></script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
</head>

<body>
<header>
        <div class="logo">
            <img src="../Home_Page/cfn_logo2.png" alt="Logo" class="logo-image"/>
        </div>
        <div class="navbar">
                <p class="usernamedisplay">Bonjour, <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>
                <form action="../Home_Page/ProductScroll.php" method="GET">
                    <input type="text" class="search-bar" id="searchBar" name="search" placeholder="Search Product">
                </form>
                <div class="icons">
                <a href="../Home_Page/home.php">
                    <i class="fa-solid fa-house home"></i>
                </a>
                <a href="../drew/cart.php">
                    <i class="fa-solid fa-cart-shopping cart"></i>
                </a>
                <a href="../User_Profile_Page/UserProfile.php">
                    <i class="far fa-user-circle fa-2x icon-profile"></i>
                </a>    
            </div>
        </div>
    </header>

    <!-- Main Banner -->
    <div class="main-banner d-flex justify-content-center align-items-center" 
        style="height: 400px; width: 100%; background-image: url('banner.png'); 
        background-size: cover; background-position: center; text-align: center;">
        <div>
            <h1>Cosmeticas</h1>
            <h3>Just Like Nature Intended</h3>
            <a href="../Home_Page/ProductScroll.php">
                <button>SHOP NOW</button>
            </a>    
        </div>
    </div>

    <section>
        <h2 class="section-title">Our Best Sellers</h2>
        <div class="carousel-wrapper">
            <div class="product-card">
                <div class="product-image">
                    <img src="https://via.placeholder.com/250" alt="Product Image">
                </div>
                <div class="product-info">
                    <h4 class="product-name">PRODUCT NAME</h4>
                    <p class="product-category">PRODUCT CATEGORY</p>
                    <div class="product-footer">
                        <span class="price">PPP</span>
                        <button class="cart-btn">🛒</button>
                    </div>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-image">
                    <img src="https://via.placeholder.com/250" alt="Product Image">
                </div>
                <div class="product-info">
                    <h4 class="product-name">PRODUCT NAME</h4>
                    <p class="product-category">PRODUCT CATEGORY</p>
                    <div class="product-footer">
                        <span class="price">PPP</span>
                        <button class="cart-btn">🛒</button>
                    </div>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-image">
                    <img src="https://via.placeholder.com/250" alt="Product Image">
                </div>
                <div class="product-info">
                    <h4 class="product-name">PRODUCT NAME</h4>
                    <p class="product-category">PRODUCT CATEGORY</p>
                    <div class="product-footer">
                        <span class="price">PPP</span>
                        <button class="cart-btn">🛒</button>
                    </div>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-image">
                    <img src="https://via.placeholder.com/250" alt="Product Image">
                </div>
                <div class="product-info">
                    <h4 class="product-name">PRODUCT NAME</h4>
                    <p class="product-category">PRODUCT CATEGORY</p>
                    <div class="product-footer">
                        <span class="price">PPP</span>
                        <button class="cart-btn">🛒</button>
                    </div>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-image">
                    <img src="https://via.placeholder.com/250" alt="Product Image">
                </div>
                <div class="product-info">
                    <h4 class="product-name">PRODUCT NAME</h4>
                    <p class="product-category">PRODUCT CATEGORY</p>
                    <div class="product-footer">
                        <span class="price">PPP</span>
                        <button class="cart-btn">🛒</button>
                    </div>
                </div>
            </div>            

            <div class="product-card">
                <div class="product-image">
                    <img src="https://via.placeholder.com/250" alt="Product Image">
                </div>
                <div class="product-info">
                    <h4 class="product-name">PRODUCT NAME</h4>
                    <p class="product-category">PRODUCT CATEGORY</p>
                    <div class="product-footer">
                        <span class="price">PPP</span>
                        <button class="cart-btn">🛒</button>
                    </div>
                </div>
            </div>
            
            <div class="product-card">
                <div class="product-image">
                    <img src="https://via.placeholder.com/250" alt="Product Image">
                </div>
                <div class="product-info">
                    <h4 class="product-name">PRODUCT NAME</h4>
                    <p class="product-category">PRODUCT CATEGORY</p>
                    <div class="product-footer">
                        <span class="price">PPP</span>
                        <button class="cart-btn">🛒</button>
                    </div>
                </div>
            </div>
            <!-- Add more product cards as needed -->
        </div>
    </section>
    
    <section>
        <div class="description-container">
            <div class="description-text">
                <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</h3>
            </div>
            <div class="description-image">
                <img src="description.png" alt="Description Image" class="description">
            </div>
        </div>
    </section>
    
<!-- Category Grid Section -->
<section>
    <h2 class="section-title">SHOP BY CATEGORY</h2>
    <div class="category-grid">
        <a href="skin.html" class="category-card skin">
            SKIN
        </a>
        <a href="hair.html" class="category-card hair">
            HAIR
        </a>
        <a href="face.html" class="category-card face">
            FACE
        </a>
        <a href="perfume.html" class="category-card perfume">
            PERFUME
        </a>
    </div>
</section>


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
        <div class="footer-center">&copy; COSMETICAS 2024</div>
    </footer>

       <!-- Modal -->
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
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
    
    
    
    

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>

</html>

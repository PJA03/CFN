<?php
// Start the session (if needed)
session_start();

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
    $stmt = $conn->prepare("SELECT * FROM tb_products WHERE product_name LIKE ? OR category LIKE ?");
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

$query = "SELECT p.* FROM tb_bestsellers b
          JOIN tb_products p ON b.productID = p.productID"; 

$result = $conn->query($query);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="home.css?v=<?php echo time(); ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>


<script>
  var swiper = new Swiper(".swiper-container", {
    slidesPerView: 3,
    spaceBetween: 20,
    loop: true,
    pagination: {
      el: ".swiper-pagination",
      clickable: true,
    },
  });
</script>

</head>

<body>
    <header>
        <div class="logo">
            <a href = "../Home_Page/home.php"><img src="../Home_Page/cfn_logo2.png" alt="Logo" class="logo-image"/></a>
        </div>
        <div class="navbar">
        <p class="usernamedisplay">Bonjour, <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>
        <form action="../Home_Page/ProductScroll.php" method="GET" class="search-form" onsubmit="return validateSearch()">
            <input type="text" name="search" class="search-bar" id="searchBar" placeholder="Search Product">
        </form>            
        <div class="icons">
                <a href = "../Home_Page/Home.php"><i class="fa-solid fa-house home"></i></a>
                <a href ="../drew/cart.php"><i class="fa-solid fa-cart-shopping cart"></i></a>
                <a href="../User_Profile_Page/UserProfile.php"><i class ="far fa-user-circle fa-2x icon-profile"></i></a>
            </div>
        </div>
    </header>

    <!-- Main Banner -->
    <div class="main-banner d-flex justify-content-center align-items-center" 
         style="height: 400px; width: 100%; background-image: url('banner.png'); 
         background-size: cover; background-position: center; text-align: center;">
        <div class="banner-content">
            <h1 class="banner-title">Cosmeticas</h1>
            <h3 class="banner-subtitle">Just Like Nature Intended</h3>
            <a href="ProductScroll.php"><button class="banner-btn">SHOP NOW</button></a>
        </div>
    </div>

    <section data-animate="fade-in">
    <h2 class="section-title-best">Our Best Sellers</h2>
    <div class="swiper-container">
        <div class="swiper-wrapper">
            <?php 
            $displayedProducts = []; // Track displayed product IDs
            
            while ($row = $result->fetch_assoc()) { 
                if (in_array($row['productID'], $displayedProducts)) {
                    continue; // Skip duplicates
                }
                $displayedProducts[] = $row['productID']; // Store displayed productID
            ?>
                <a href="../e-com/productpage.php?id=<?= $row['productID']; ?>" 
                   class="swiper-slide product-card" data-animate="fade-in" 
                   style="text-decoration: none; color: inherit;">
                   
                    <div class="product-image">
                        <?php
                        if (!defined('BASE_PATH')) {
                            define('BASE_PATH', '/CFN/'); // Prevent redeclaration error
                        }

                        $productImage = !empty($row['product_image']) ? str_replace('uploads/', '', $row['product_image']) : '';
                        $imgSrc = !empty($productImage) ? BASE_PATH . "e-com/uploads/" . $productImage : BASE_PATH . "e-com/images/cfn_logo.png";
                        $fallbackImgSrc = BASE_PATH . "e-com/images/cfn_logo.png";
                        ?>
                        <img src="<?= $imgSrc; ?>" alt="<?= htmlspecialchars($row['product_name']); ?>" 
                             data-fallback="<?= $fallbackImgSrc; ?>" 
                             onload="this.removeAttribute('data-fallback');" 
                             onerror="if(this.src !== this.getAttribute('data-fallback')) { 
                                         this.src = this.getAttribute('data-fallback'); 
                                     } else { 
                                         console.log('Fallback failed: <?= $fallbackImgSrc; ?>'); 
                                     }">
                    </div>
                    
                    <div class="product-info">
                        <h4 class="product-name"><?= htmlspecialchars($row['product_name']); ?></h4>
                        <p class="product-category"><?= htmlspecialchars($row['category']); ?></p>
                    </div>
                </a>
            <?php } ?>
        </div>
        <div class="swiper-pagination"></div>
    </div>
</section>



    <section class="description-section" data-animate="fade-in">
        <div class="description-container">
            <div class="left-container">
                <div class="description-text">
                    <h3>Explore our all-natural ingredients, eco-friendly packaging, and innovative formulas designed to enhance your beauty while nourishing your skin—just as nature intended.</h3>
                </div>
                <h2 class="section-title">SHOP BY CATEGORY</h2>
                <div class="category-grid">
                    <a href="ProductScroll.php?category=skin" class="category-card-skin" data-animate="fade-in-scale">SKIN</a>
                    <a href="ProductScroll.php?category=hair" class="category-card-hair" data-animate="fade-in-scale">HAIR</a>
                    <a href="ProductScroll.php?category=face" class="category-card-face" data-animate="fade-in-scale">FACE</a>
                    <a href="ProductScroll.php?category=perfume" class="category-card-perfume" data-animate="fade-in-scale">PERFUME</a>
                </div>
            </div>
            <div class="description-image">
                <img src="description.png" alt="Description Image" class="description">
            </div>
        </div>
    </section>

    <footer>
        <div class="footer-container">
            <div class="footer-left">
                <img src="../Resources/cfn_logo.png" alt="Naturale Logo" class="footer-logo">
            </div>
            <div class="footer-right">
                <ul class="footer-nav">
                    <li><a href="../User_Profile_Page/aboutUs.php">About Us</a></li>
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
            &copy; COSMETICAS 2024
        </div>
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>
    
    <script>
        var swiper = new Swiper('.swiper-container', {
            slidesPerView: 4,
            spaceBetween: 20,
            pagination: {
                el: '.swiper-pagination',
                clickable: true,
            },
            autoplay: {
                delay: 3000,
                disableOnInteraction: false,
            },
            breakpoints: {
                1024: {
                    slidesPerView: 4,
                },
                768: {
                    slidesPerView: 2,
                },
                480: {
                    slidesPerView: 1,
                }
            }
        });

        function validateSearch() {
            let searchInput = document.getElementById("searchBar").value.trim();
            if (searchInput === "") {
                alert("Please enter a search term.");
                return false;
            }
            return true;
        }       
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>

</html>
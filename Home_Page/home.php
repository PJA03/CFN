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
    <link rel="stylesheet" href="main.js">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="home.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>
</head>

<body>
    <header>
        <div class="logo">
            <img src="cfn_logo2.png" alt="Logo" class="logo-image" />
        </div>
        <div class="navbar">
            
            <div class="icons">
                <p class="usernamedisplay">Bonjour, <?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>!</p>
                <form action="../Home_Page/ProductScroll.php" method="GET" class="search-form" onsubmit="return validateSearch()">
                    <input type="text" name="search" class="search-bar" id="searchBar" placeholder="Search Product or Category">
                </form>
                <div class="icons">
                <a href="../Home_Page/home.php"><i class="fa-solid fa-house"></i></a>
                <a href="../drew/cart.php">
                    <i class="fas fa-shopping-cart"></i>
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
            <a href="ProductScroll.php"><button>SHOP NOW</button></a>
        </div>
    </div>

    <section>
        <h2 class="section-title-best">Our Best Sellers</h2>
        <div class="swiper-container">
            <div class="swiper-wrapper">
                <div class="swiper-slide product-card">
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
                <div class="swiper-slide product-card">
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
                <div class="swiper-slide product-card">
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
                <div class="swiper-slide product-card">
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
                <div class="swiper-slide product-card">
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
                <div class="swiper-slide product-card">
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
                <div class="swiper-slide product-card">
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
            </div>
            <div class="swiper-pagination"></div>
        </div>
    </section>

    <section class="description-section">
        <div class="description-container">
            <div class="left-container">
                <div class="description-text">
                    <h3>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.</h3>
                </div>
                <h2 class="section-title">SHOP BY CATEGORY</h2>
                <div class="category-grid">
                    <a href="ProductScroll.php?category=skin" class="category-card-skin">SKIN</a>
                    <a href="ProductScroll.php?category=hair" class="category-card-hair">HAIR</a>
                    <a href="ProductScroll.php?category=face" class="category-card-face">FACE</a>
                    <a href="ProductScroll.php?category=perfume" class="category-card-perfume">PERFUME</a>
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
                <img src="cfn_logo.png" alt="Naturale Logo" class="footer-logo">
            </div>
            <div class="footer-right">
                <ul class="footer-nav">
                    <li><a href="#">About Us</a></li>
                    <li><a href="#">Terms and Conditions</a></li>
                    <li><a href="#">Products</a></li>
                </ul>
            </div>
            <div class="social-icons">
                <p>SOCIALS</p>
                <a href="#"><i class="fab fa-facebook"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
            </div>            
        </div>
        <div class="footer-center">
            © COSMETICAS 2024
        </div>
    </footer>
    
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
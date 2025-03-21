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
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="main.js">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.css">
    <script src="https://cdn.jsdelivr.net/npm/swiper/swiper-bundle.min.js"></script>

    
<style>
    /* General Reset */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: "Be Vietnam Pro", serif !important;
    font-weight: 400;
    font-style: normal;
    min-height: 100vh;
    width: 100%;
    background-color: #E8ECD7 !important;
    line-height: 1.6;
}

/* Headings styling */
h1 {
    font-family: "Bebas Neue", serif !important;
    font-size: 100px !important;  /* Ensure font size applies */
    color: #FF8666 !important;    /* Use !important for overriding */
    font-style: normal;
}

h2 {
    font-family: "Bebas Neue", serif !important;
    font-size: 50px !important;  /* Apply font size */
    font-style: normal;
    color: #FF8666 !important;    /* Override any conflicts */
}

h3 {
    font-family: "Bebas Neue", serif !important;
    font-size: 30px !important;  /* Apply font size */
    font-style: normal;
    color: #FF8666 !important;    /* Override any conflicts */
}

/* Logo */
.logo {
    display: flex;
    align-items: center;
    gap: 10px; /* Adjust spacing between the image and text */
}

.logo-image {
    height: 40px; /* Adjust size as needed */
    width: auto; /* Keep aspect ratio */
}

/*NAVBAR*/
.logo {
    display: flex;
    align-items: center;
    gap: 10px;
}

.logo-image {
    height: 40px;
    width: auto;
}

header {
    background-color: #1F4529;
    color: #EED3B1;
    padding: 20px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    box-sizing: border-box;
    position: relative;
}

.logo img {
    height: 40px;
}

.navbar {
    display: flex;
    justify-content: flex-end;
    align-items: center;
    padding: 10px 20px;
}

.search-bar {
    padding: 8px 12px;
    border-radius: 25px;
    border: none;
    outline: none;
    font-size: 1rem;
    width: 300px;
    background-color: #FFFFFF;
    margin-right: 20px;
}

.icons {
    display: flex;
    align-items: center;
    gap: 15px;
}

.icon-profile {
    font-size: 1.5rem;
    color: #EED3B1;
    cursor: pointer;
}

.burger-menu {
    font-size: 1.6rem;
    color: #EED3B1;
    cursor: pointer;
}

.fa-shopping-cart {
    font-size: 1.8rem;
    color: #EED3B1;
    cursor: pointer;
}


/* Main Banner */
.main-banner {
    background-image: url('image.png');
    background-size: cover;
    background-position: center;
    text-align: center;
    color: white;
    padding: 80px 20px;
}

.main-banner h1 {
    font-family: 'Bebas Neue', cursive;
    font-size: 3rem;
    margin: 0; /* Remove all margins */
    line-height: 1; /* Adjust line-height to avoid extra space */
}

.main-banner h3 {
    font-family: 'Bebas Neue', cursive;
    font-size: 2rem;
    margin: 0; /* Remove all margins */
    line-height: 1; /* Ensure the lines are snug */
}

.main-banner button {
    background-color: #1F4529;
    color: #FFFFFF;
    font-family: 'Bebas Neue', cursive;
    font-size: 2rem;
    border: none;
    padding: 5px 10px;
    border-radius: 5px;
    cursor: pointer;
    margin-top: 20px;
}

.main-banner button:hover {
    background-color: #EED3B1;
    color: #1F4529;
}

/*bestsellers*/
.carousel-wrapper {
    display: flex;
    overflow-x: auto;
    scroll-behavior: smooth;
    gap: 20px;
    padding: 20px;
    white-space: nowrap;
}

/* Ensure scrollbar appears */
.carousel-wrapper::-webkit-scrollbar {
    height: 8px;
    background: #f0f0f0;
}

.carousel-wrapper::-webkit-scrollbar-thumb {
    background-color: #b0b0b0;
    border-radius: 10px;
}

.product-card {
    flex: 0 0 auto; /* Ensures cards stay inline without affecting scroll */
    width: 250px;
    height: 350px;
    background-color: #C0D171;
    padding: 15px;
    border-radius: 2px;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    white-space: normal; /* Allow text to wrap properly */
}

.product-image {
    width: 105%;
    height: 300px;
    background-color: white;
    display: flex;
    justify-content: center;
    align-items: center;
}

.product-image img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

.product-info {
    width: 100%;
    text-align: left; 
    font-family: "Bebas Neue", serif !important;
    margin-top: 0; /* Ensure no extra space on top */
}

.product-info h4 {
    margin-bottom: 0px !important; /* Reduce space between name and category */
    margin-top: 10px;
}

.product-info p {
    margin-top: 0 !important;
    margin-bottom: 10px !important; /* Slightly reduce spacing below category */
}

/* Ensure pricing and buttons align properly */
.product-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    width: 100%;
    height: 5px;
}

.price {
    font-weight: bold;
}

.cart-btn {
    background: none;
    border: none;
    font-size: 18px;
    cursor: pointer;
}


/*description*/
.description-container {
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.description-text {
    flex: 1; 
    padding-right: 20px;
    text-align: left;
    margin-left: 50px;
}

.description-container .description-text h3 {
    color: black !important;
}


.description-image {
    flex: 1;
}

.description {
    max-width: 80%; 
    height: auto; 
}


/* Section */
section {
    padding: 40px 20px;
    text-align: center;
}

.section-title h2 {
    font-size: 30px;
    margin-bottom: 20px;
    font-family: "Bebas Neue", serif;
}

/* Category Grid */
.container {
    width: 100%;
    margin: 0 auto;
    display: flex;
    flex-wrap: wrap;
    gap: 40px;
}

.category-grid {
    display: flex;
    justify-content: center;
    flex-wrap: wrap;
    gap: 20px;
    margin: 20px 0;
}

.category-grid a {
    text-decoration: none;
    font-size: 60px;
    font-family: "Bebas Neue", cursive;
}

.category-card-skin, .category-card-hair, .category-card-face, .category-card-perfume  {
    width: 310px;
    height: 200px;
    display: flex;
    justify-content: center;
    align-items: center;
    border-radius: 5px;
    color: white;
    font-weight: bold;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.category-card-skin:hover, .category-card-hair:hover, .category-card-face:hover, .category-card-perfume:hover {
    transform: scale(1.05); 
    box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.2);
    cursor: pointer; 
}

.category-card-skin { background-color: #8E98F0; }
.category-card-hair { background-color: #FF8666; }
.category-card-face { background-color: #A6D492; }
.category-card-perfume { background-color: #f6c893;}

.header-fixed {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    background-color: #1F4529;
}


/* Footer */
footer {
    background-color: #1F4529;
    color: white;
    padding: 40px 60px;
    display: flex;
    flex-direction: column;
    align-items: center;
    width: 100%;
}

.footer-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    width: 100%;
    max-width: 1200px;
    flex-wrap: wrap;
    gap: 20px;
}

/* Footer Left */
.footer-left {
    display: flex;
    align-items: center;
    gap: 20px;
}

.footer-logo {
    height: 150px;
}

.footer-logo img {
    max-width: 100%;
    max-height: 100%;
    object-fit: contain;
}

/* Footer Right */
.footer-right {
    display: flex;
    flex-direction: column;
    text-align: left;
    gap: 20px;
}

/* Navigation */
.footer-nav {
    list-style: none;
    padding: 0;
}

.footer-nav li {
    font-family: "Bebas Neue", serif;
    font-size: 20px;
    margin-bottom: 8px;
}

.footer-nav a {
    color: white;
    text-decoration: none;
    transition: color 0.3s ease;
}

.footer-nav a:hover {
    color: #EED3B1;
}

/* Social Icons */
.social-icons {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
}

.social-icons p {
    font-size: 20px;
    font-family: "Bebas Neue", serif;
    margin: 0;
}

.social-icons a {
    color: white;
    font-size: 18px;
    text-decoration: none;
    transition: transform 0.3s ease, color 0.3s ease;
}

.social-icons a:hover {
    transform: scale(1.1);
    color: #EED3B1;
}

/* Footer Center (Copyright) */
.footer-center {
    text-align: center;
    width: 100%;
    font-size: 14px;
    margin-top: 20px;
    opacity: 0.8;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .footer-container {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
    
    .footer-left,
    .footer-right {
        justify-content: center;
        align-items: center;
    }
}


.description-section {
    display: flex;
    justify-content: center;
    width: 100%;
    padding: 40px 20px;
}

.description-container {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    width: 100%;
    max-width: 1610px;
    gap: 40px;
}

/* Left side (Description + Categories) */
.left-container {
    display: flex;
    flex-direction: column;
    width: 50%;
    gap: 20px; /* Space between description and category */
}

.description-text {
    padding: 20px;
    border-radius: 5px;
}

.section-title {
    margin-top: 10px;
    font-size: 1.5rem;
}

.category-grid {
    display: flex;
    flex-wrap: wrap;
    gap: 15px;
}

.category-card {
    width: 48%;
    padding: 15px;
    text-align: center;
    background: #ddd;
    border-radius: 5px;
    font-weight: bold;
}

/* Right side (Image) */
.description-image {
    width: 100%;
}

.description-image img {
    width: 100%;
    height: auto;
    border-radius: 5px;
}

/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 6px; /* Thin scrollbar */
    height: 6px; /* Thin scrollbar for horizontal scrolling */
}

::-webkit-scrollbar-track {
    background: #E8ECD7; /* Light background */
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: #FF8666; /* Soft green thumb */
    border-radius: 10px;
    transition: background 0.3s ease;
}

::-webkit-scrollbar-thumb:hover {
    background: #1F4529; /* Darker green when hovered */
}

.swiper-container {
        width: 100%;
        max-width: 1500px;
        margin: auto;
        position: relative;
        padding-bottom: 40px; /* Ensures pagination stays in place */
        overflow: hidden;
    }
    .swiper-wrapper {
        display: flex;
    }
    .swiper-slide {
        display: flex;
        justify-content: center;
    }
    .product-card {
        width: 250px; /* Adjusted width to fit 3 products */
        height: 350px; /* Adjusted height */
        background-color: #C0D171;
        padding: 10px;
        border-radius: 8px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        text-align: center;
    }
    .product-image img {
        max-width: 100%;
        height: auto;
    }
    .swiper-pagination {
    position: absolute;
    bottom: -25px; /* Adjust as needed */
    left: 100%;
    transform: translateX(-1%);
    display: flex;
    justify-content: center;
    width: 100%;
}
    .swiper-pagination-bullet {
        background: #000 !important; /* Make bullets visible */
    }
    body {
        overflow-x: hidden; /* Prevents horizontal scrolling */
    }

</style>


</head>

<body>
    <header>
        <div class="logo">
            <img src="cfn_logo2.png" alt="Logo" class="logo-image" />
        </div>
        <div class="navbar">
            <input type="text" class="search-bar" placeholder="Search Product" />
            <div class="icons">
                <a href="../User_Profile_Page/UserProfile.php">
        <i class="far fa-user-circle fa-2x icon-profile"></i>
    </a>
                <i class="fas fa-bars burger-menu"></i>
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
            <a href = "ProductScroll.php"><button>SHOP NOW</button> </a>
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

            <!-- Add more slides as needed -->
        </div>
        <!-- Pagination Dots -->
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
                <a href="skin.html" class="category-card-skin">SKIN</a>
                <a href="hair.html" class="category-card-hair">HAIR</a>
                <a href="face.html" class="category-card-face">FACE</a>
                <a href="perfume.html" class="category-card-perfume">PERFUME</a>
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
            &copy; COSMETICAS 2024
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
            delay: 3000, // Adjust the delay as needed
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
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>

</html>

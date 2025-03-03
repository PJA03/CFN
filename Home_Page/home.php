<?php
// Start the session (if needed)
if (session_status() == PHP_SESSION_NONE) {
    session_start();
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
            <button>SHOP NOW</button>
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
                <img src="cfn_logo.png" alt="Naturale Logo" class="footer-logo">
            </div>
            <div class="footer-right">
                <ul class="footer-nav">
                    <li><a href="#">ABOUT US</a></li>
                    <li><a href="#">PRODUCTS</a></li>
                    <li><a href="#">LOGIN</a></li>
                    <li><a href="#">SIGN UP</a></li>
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
    
    
    
    

    <!-- Bootstrap JS (with Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="main.js"></script>
</body>

</html>

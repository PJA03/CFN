<?php
require_once "../conn.php";
session_start();



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
    //TODO:add you are not yet registered alert
    header('Location: ../Registration_Page/registration.php');
    exit();
}

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ../Home_Page/Home.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Account Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <header>
        <div class="logo">
            <img src="../Home_Page/cfn_logo2.png" alt="Logo" class="logo-image" />
        </div>
        <div class="navbar">
            <!-- <input type="text" class="search-bar" placeholder="Search Product" /> -->
            <p class = "usernamedisplay">Bonjour, <?php echo isset($user['username']) ? $user['username'] : ''; ?>!</p>
            <div class="icons">
                <i class="fa-solid fa-house home"></i>
                <i class="fa-solid fa-cart-shopping cart"></i>
                <i class="far fa-user-circle fa-2x icon-profile"></i>
            </div>
        </div>
    </header>

    <div class="main mb-5">
        <div class="row">
            <div class="col-md-4 left-panel">
                <!-- profile picture -->
                <img src="<?php echo isset($user['profile_image']) && !empty($user['profile_image']) ? $user['profile_image'] : '../Resources/profile.png'; ?>" alt="Profile Icon" name="icon" id="icon" class="profile-icon" width="150" style="margin: 10px;"/><br>

                <h3>My Account</h3>
                <a href="UserProfile.php" class="active">Profile</a>
                <form method="post" action="">
                    <button class="transparent-button" name="logout">Logout</button>
                </form>
            </div>
            <div class="col-md-8 right-panel">
                <div class="row">
                        <h2 class="lead">Profile Details </h2>
                        <div class="row">
                            <div class="col-md-5">
                                <b>Username: </b>
                            </div>
                            <div class="col-md-7">
                                <span id="username" class="editable"><?php echo isset($user['username']) ? $user['username'] : ''; ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <b>Email:</b>
                            </div>
                            <div class="col-md-7">
                                <span id="email" class="editable"><?php echo isset($user['email']) ? $user['email'] : ''; ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <b>First Name:</b>
                            </div>
                            <div class="col-md-7">
                                <span id="first_name" class="editable"><?php echo isset($user['first_name']) ? $user['first_name'] : ''; ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <b>Last Name: </b>
                            </div>
                            <div class="col-md-7">
                                <span id="last_name" class="editable"><?php echo isset($user['last_name']) ? $user['last_name'] : ''; ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <b>Contact No.:</b>
                            </div>
                            <div class="col-md-7">
                                <span id="contact_no" class="editable"><?php echo isset($user['contact_no']) ? $user['contact_no'] : ''; ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-5">
                                <b>Delivery Address: </b>
                            </div>
                            <div class="col-md-7 text-wrap">
                                <span id="address" class="editable"><?php echo isset($user['address']) ? $user['address'] : ''; ?></span>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col text-end">
                            <form action="editUserProfile.php">
                                <button class="Edit" style="color: black;" id="edit-button" href="editUserProfile.php">
                                    Edit 
                                    <i class="bi bi-pen"></i>
                                </button>
                            </form>
                            </div>
                        </div>
                </div>
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

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        const logoutButton = document.querySelector('button[name="logout"]');
        const logoutForm = logoutButton.closest('form');

        logoutButton.addEventListener('click', function (e) {
            e.preventDefault(); // Prevent default form submission
            Swal.fire({
            title: 'Are you sure?',
            text: "You will be logged out of your account.",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#1F4529',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'logout.php'; // Call the PHP session destroy
            }
        })
        });

    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    
</body>

</html>

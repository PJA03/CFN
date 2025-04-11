<?php   
    session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFN - Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap');
    </style>
</head>
<body>
    <div class="container m-0 p-0">
        <div class="box">
            <!-- contains the regis forms -->
            <div class="form sign_in">
             <div class="col p-5 rounded mx-auto w-100" style="background-color: #ffffff;">
                <br><br>
                    <h1>Login</h1>
                    <br><br>
                    <form action="registration.php" method="post" id="loginForm">
                        <div class="mb-3">
                            <input type="email" class="form-control" id="logemail" name="logemail" placeholder="Email" required>
                        </div>
                        <div class="mb-3 password-section">
                            <div class="input-group">
                                <input type="password" class="form-control" id="logpassword" name="logpassword" placeholder="Password" required>
                                <span id="togglepassword" class="input-group-text">
                                    <img src="eye-close.jpg" id="eyeicon-login" class="eyeicon">
                                </span>
                            </div>
                        </div>
                        <p><a href="passrecov.php" class="btn-noBG">Forgot password?</a></p>
                        <br><br>
                        <div class="button">
                            <input type="submit" class="btn bkg" name="login" value = "Login">
                        </div>
                    </form><br><br><br>
                    <!-- swap button -->
                    <p>Don't have an account yet? Create one <button class="swipe btn-noBG btnSign-up"> here.</button></p>
                    <br>
                </div>
            </div>
            <!-- sign up -->
            <div class="form log_in">
                <div class="col p-5 rounded" style="background-color: #ffffff;">
                    <br>
                    <h1>Register</h1>
                    <br>
                    <form action="registration.php" method="post" id="registrationForm">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                            <div id="usernameFeedback" class="invalid-feedback d-none">
                                
                            </div>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" id="regisemail" name="regisemail" placeholder="Email" required>
                        </div>
                        <div class="mb-3 password-section">
                        <div class="input-group">   
                            <input type="password" class="form-control" id="regispassword" name="regispassword" placeholder="Password"
                            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$" 
                            title="Password must contain at least 8 characters, including an uppercase letter, a lowercase letter, a number, and a special character."
                            required>
                            <span class="input-group-text">
                                <img src="eye-close.jpg" id="eyeicon-regis" class="eyeicon">
                            </span>
                        </div>
                        </div>
                        <div class="mb-3">
                            <small class="text-body-secondary">Password must contain at least 8 characters, including an uppercase letter, a lowercase letter, 
                                a number, and a special character.
                            </small>
            
                        </div>
                        <input type="checkbox" id="accept-terms" required>
                        <span class="label mt-3">To create an account, kindly read and accept the
                            <button type="button" class="btn-noBG" data-bs-toggle="modal" data-bs-target="#ModalTerms">
                            Terms
                        </button>
                         and
                        <button type="button" class="btn-noBG" data-bs-toggle="modal" data-bs-target="#ModalPrivacy">
                            Privacy Policy.
                        </button>

                        </span>
                        <br>
                        <div class="button d-flex justify-content-center align-items-center mt-3">
                            <input type="submit" class="btn bkg" name="signup" id="signup" value = "Register Account">
                        </div>
                    </form>
                    <br><br><br>
                   <p>Already have an account? <button class="swipe btnSign-in btn-noBG">Login</button>. </p>
                </div>
            </div>
        </div>
        <!-- contains overlay banner -->
        <div class="overlay">
            <div class="page page_signIn p-5 rounded text-center" style="background-color: #1F4529; color: #EED3B1;">
                <h1 class="display-1">Bonjour, ma belles!</h1>
                <H6 class="lead fst-italic" style=color:#ffffff;>Cosmeticas Fraiche Naturale</H6>
            </div>
            <div class="page page_signUp p-5 rounded text-left" style="background-color: #1F4529; color: #EED3B1;">
                <h1 class="display-1">Cosmeticas Fraiche Naturale</h1>
                <H6 class="lead fst-italic" style="color:#ffffff; text-align: left; margin-left: 4%;">Just Like Nature Intended</H6>
            </div>
        </div>
    </div>

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
                <button type="button" class="btn" id="deny-terms" data-bs-dismiss="modal">Close</button>
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
                    <button type="button" class="btn" id="deny-privacy" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <?php
        require_once "../conn.php";
        require_once "emailver.php";
        
        //account registration
        $registrationFailed = false;
        if (isset($_POST['signup'])) {
            $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
            $email = filter_var(trim($_POST['regisemail']), FILTER_SANITIZE_EMAIL);
            $password = password_hash(trim($_POST['regispassword']), PASSWORD_BCRYPT);
            $validated = 0;
            
            //TODO: change back to user role when done testing
            $role = "user";
            $token = rand(000000,999999);
            $token_created_at = date("Y-m-d H:i:s");

            // Check if email is already registered
            $check_email = "SELECT * FROM tb_user WHERE email = ?";
            $stmt_email = $conn->prepare($check_email);
            $stmt_email->bind_param("s", $email);
            $stmt_email->execute();
            $result_email = $stmt_email->get_result();

            // Check if username is already taken
            $check_username = "SELECT * FROM tb_user WHERE username = ?";
            $stmt = $conn->prepare($check_username);
            $stmt->bind_param("s", $username);
            $stmt->execute();
            $result_username = $stmt->get_result();

            if ($result_username->num_rows > 0) {
                $registrationFailed = true;
                ?>
                <script>
                    Swal.fire({
                        position: "center",    
                        icon: "error",
                        title: "Username is already taken",
                        text: "Please choose a different username.",
                        showConfirmButton: false,
                        timer: 3000  
                    });
                </script>
                <?php
            } 


            if ($result_email->num_rows > 0) {
                $registrationFailed = true;
                ?>
                <script>
                    Swal.fire({
                    position: "center",    
                    icon: "error",
                    title: "Email is already registered",
                    text: "Please use a different email.",
                    showConfirmButton:false,
                    timer: 3000  
                    });
                </script>
                <?php
            } elseif ($result_username->num_rows > 0) {
                $registrationFailed = true;
                ?>
                <script>
                    Swal.fire({
                    position: "center",    
                    icon: "error",
                    title: "Username is already taken",
                    text: "Please choose a different username.",
                    showConfirmButton:false,
                    timer: 3000  
                    });
                </script>
                <?php
            } else {
                // Insert new user into the database
                $signup = "INSERT INTO tb_user (username, email, pass, validated, role, token, token_created_at) 
                            VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt_signup = $conn->prepare($signup);
                $stmt_signup->bind_param("sssssss", $username, $email, $password, $validated, $role, $token, $token_created_at);
                $result = $stmt_signup->execute();
                
                if ($result == true) {
                    // Call email verification function
                    send_verification($email, $token); 
                    ?>
                    <script>
                        Swal.fire({
                        position: "center",    
                        icon: "success",
                        title: "Check your email to validate",
                        text: "Please verify your email to use your account. If you did not receive an email, please check your spam folder.",
                        showConfirmButton:false,
                        timer: 3000  
                        });
                    </script>
                    <?php
                } else {
                    $registrationFailed = true;
                    echo $conn->error;
                }
            }
        }

        // Login account
        if (isset($_POST['login'])) {
            $email = $_POST['logemail'];
            $password = $_POST['logpassword'];
            $_SESSION['email'] = $email;
        
            // Query to fetch user data using prepared statements
            $loginsql = "SELECT * FROM tb_user WHERE email = ?";
            $stmt = $conn->prepare($loginsql);
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
        
            if ($result === false) {
                die("Error in SELECT query: " . $conn->error);
            }
        
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
        
                // Verify password
                if (password_verify($password, $user['pass'])) {
                    if ($user['validated'] == 1) { // Check if account is validated
                        // Store user data in session
                        $_SESSION['user_id'] = $user['user_id'];
                        $_SESSION['username'] = $user['username'];
                        $_SESSION['first_name'] = $user['first_name'];
                        $_SESSION['last_name'] = $user['last_name'];
                        $_SESSION['contact_no'] = $user['contact_no'];
                        $_SESSION['address'] = $user['address'];
                        $_SESSION['profile_image'] = $user['profile_image'];
                        $_SESSION['role'] = $user['role'];
                        
                        // Redirect based on role
if ($user['role'] == 'admin') {
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.location.href = '../e-com/manageordersA.php'; // Redirect admin to manage orders
    </script>
    <?php
    session_regenerate_id(true);
} elseif ($user['role'] == 'superadmin') {
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.location.href = '../e-com/manageproductsA.php'; // Redirect superadmin to manage orders
    </script>
    <?php
    session_regenerate_id(true);
} else {
    ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        window.location.href = '../Home_Page/home.php'; // Redirect others to home page
    </script>
    <?php
    session_regenerate_id(true);
}
                    } else {
                        // Email not validated
                        ?>
                        <script>
                            Swal.fire({
                                position: "center",
                                icon: "error",
                                title: "Email not validated",
                                showConfirmButton: true
                            });
                        </script>
                        <?php
                    }
                } else {
                    // Incorrect password
                    ?>
                    <script>
                        Swal.fire({
                            position: "center",
                            icon: "error",
                            title: "Login failed",
                            text: "Incorrect email or password.",
                            showConfirmButton: true
                        });
                    </script>
                    <?php
                }
            } else {
                // Incorrect email
                ?>
                <script>
                    Swal.fire({
                        position: "center",
                        icon: "error",
                        title: "Login failed",
                        text: "Incorrect email or password.",
                        showConfirmButton: true
                    });
                </script>
                <?php
            }
        }
    ?>
    <!-- link script -->
    <script src="main.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener("DOMContentLoaded", function () {
    var usernameInput = document.getElementById('username');
    var feedback = document.getElementById('usernameFeedback');
    var form = document.querySelector("form[action='registration.php']");

    usernameInput.addEventListener('input', function () {
        var username = this.value;
        if (username.length > 0) {
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'check_username.php', true);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
            xhr.onreadystatechange = function () {
                if (xhr.readyState == 4 && xhr.status == 200) {
                    if (xhr.responseText == 'taken') {
                        feedback.textContent = 'Username is already taken';
                        feedback.classList.add('d-block');
                        feedback.classList.remove('d-none');
                        usernameInput.classList.add('is-invalid');
                        form.querySelector("[name='signup']").disabled = true;
                    } else {
                        feedback.textContent = '';
                        feedback.classList.add('d-none');
                        feedback.classList.remove('d-block');
                        usernameInput.classList.remove('is-invalid');
                        form.querySelector("[name='signup']").disabled = false;
                    }
                }
            };
            xhr.send('username=' + encodeURIComponent(username));
        } else {
            feedback.textContent = '';
            feedback.classList.add('d-none');
            feedback.classList.remove('d-block');
            usernameInput.classList.remove('is-invalid');
            form.querySelector("[name='signup']").disabled = false;
        }
    });
});
    </script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const togglePassword = document.getElementById("eyeicon-login"); // Target the toggle icon
    const passwordInput = document.getElementById("logpassword"); // Target the password input field

    togglePassword.addEventListener("click", function () {
        // Toggle the type attribute between "password" and "text"
        const type = passwordInput.getAttribute("type") === "password" ? "text" : "password";
        passwordInput.setAttribute("type", type);

        // Toggle the icon image
        if (type === "password") {
            togglePassword.src = "eye-close.jpg"; // Set to "eye-close" icon
        } else {
            togglePassword.src = "eye-open.jpg"; // Set to "eye-open" icon
        }
    });
});

document.addEventListener("DOMContentLoaded", function () {
    const togglePasswordRegis = document.getElementById("eyeicon-regis"); // Target the toggle icon
    const passwordInputRegis = document.getElementById("regispassword"); // Target the password input field

    togglePasswordRegis.addEventListener("click", function () {
        // Toggle the type attribute between "password" and "text"
        const type = passwordInputRegis.getAttribute("type") === "password" ? "text" : "password";
        passwordInputRegis.setAttribute("type", type);

        // Toggle the icon image
        if (type === "password") {
            togglePasswordRegis.src = "eye-close.jpg"; // Set to "eye-close" icon
        } else {
            togglePasswordRegis.src = "eye-open.jpg"; // Set to "eye-open" icon
        }
    });
});
</script>


<script>
    document.addEventListener("DOMContentLoaded", function () {
    const registrationForm = document.getElementById("registrationForm");
    const acceptTermsCheckbox = document.getElementById("accept-terms");
    const registerButton = document.querySelector("input[name='signup']");

    // Disable the button by default
    registerButton.disabled = true;

    // Enable or disable the button based on the checkbox state
    acceptTermsCheckbox.addEventListener("change", function () {
        registerButton.disabled = !this.checked;
    });

    // Prevent form submission if the checkbox is not checked
    registrationForm.addEventListener("submit", function (event) {
        if (!acceptTermsCheckbox.checked) {
            event.preventDefault(); // Prevent form submission
            Swal.fire({
                icon: "error",
                title: "Terms Not Accepted",
                text: "You must accept the Terms and Privacy Policy to register.",
                confirmButtonColor: "#1F4529"
            });
        }
    });
});
</script>
<script>
        document.addEventListener("DOMContentLoaded", function () {
        // Login Form Validation
        const loginForm = document.getElementById("loginForm");
        const logemailInput = document.getElementById("logemail");
        const emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/; // Regex for email validation

        loginForm.addEventListener("submit", function (event) {
            const emailValue = logemailInput.value.trim();

            if (!emailPattern.test(emailValue)) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: "error",
                    title: "Invalid Email",
                    text: "Please enter a valid email address.",
                    confirmButtonColor: '#1F4529'
                });
            }
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
    // Registration Form Validation
    const registrationForm = document.getElementById("registrationForm");
    const regisemailInput = document.getElementById("regisemail");
    const emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/; // Regex for email validation

    registrationForm.addEventListener("submit", function (event) {
        const emailValue = regisemailInput.value.trim();

        if (!emailPattern.test(emailValue)) {
            event.preventDefault(); // Prevent form submission
            Swal.fire({
                icon: "error",
                title: "Invalid Email",
                text: "Please enter a valid email address.",
                confirmButtonColor: '#1F4529'
            });
        }
    });
});
 
    </script>   

    <style>
    #eyeicon {
        width: 20px; 
        height: 20px; 
        cursor: pointer;
    }

    .form-control {
    width: 100%; /* Ensures uniform width */
    height: 40px; /* Adjust height to match other inputs */
    }

    .input-group-text img {
    width: 18px; 
    height: 18px;
    }
</style>
</body>
</html>
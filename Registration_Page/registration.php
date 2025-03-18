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
                    <form action="registration.php" method="post">
                        <div class="mb-3">
                            <input type="email" class="form-control" id="logemail" name="logemail" placeholder="Email" required>
                        </div>
                        <div class="mb-3 password-section">
                            <div class="input-group">
                                <input type="password" class="form-control" id="logpassword" name="logpassword" placeholder="Password" required>
                                <span class="input-group-text">
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
    
            <div class="form log_in">
                <div class="col p-5 rounded" style="background-color: #ffffff;">
                    <br>
                    <h1>Register</h1>
                    <br>
                    <form action="registration.php" method="post">
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

                        <span class="label mt-3">By creating an account, you agree to our 
                            <button type="button" class="btn-noBG" data-bs-toggle="modal" data-bs-target="#ModalTerms">
                            Terms
                        </button>
                         and acknowledge our
                        <button type="button" class="btn-noBG" data-bs-toggle="modal" data-bs-target="#ModalPrivacy">
                            Privacy Policy.
                        </button>

                        </span>
                        <br>
                        <div class="button d-flex justify-content-center align-items-center mt-3">
                            <input type="submit" class="btn bkg" name="signup" value = "Register Account">
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
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EED3B1;">
                    <h5 class="modal-title" id="exampleModalLongTitle" style="font-weight: bold;">CFN Naturale Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Terms terms terms
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Privacy Policy -->
    <div class="modal fade" id="ModalPrivacy" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #EED3B1;">
                    <h5 class="modal-title" id="exampleModalLongTitle" style="font-weight: bold;">CFN Naturale Privacy Policy</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    PeePee PeePee PeePee
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn" data-bs-dismiss="modal">Close</button>
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
            $username = $_POST['username'];
            $email = $_POST['regisemail'];
            $password = password_hash($_POST['regispassword'], PASSWORD_BCRYPT);
            $validated = 0;
            
            //TODO: change back to user role when done testing
            $role = "admin";
            $token = rand(000000,999999);
            $token_created_at = date("Y-m-d H:i:s");

            // Check if email is already registered
            $check_email = "SELECT * FROM tb_user WHERE email = '$email'";
            $result_email = $conn->query($check_email);

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
                // Insert statement to register account
                $signup = "INSERT INTO tb_user(username, email, pass, validated, role, token, token_created_at) values ('$username','$email','$password', '$validated', '$role', '$token', '$token_created_at')";
                $result = $conn->query($signup);
                
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

    // Query to fetch user data
    $loginsql = "SELECT * FROM tb_user WHERE email='$email'";
    $result = $conn->query($loginsql);

    if ($result === FALSE) {
        die("Error in SELECT query: " . $conn->error);
    }

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Verify password
        if (password_verify($password, $user['pass'])) {
            if ($user['validated'] == 1) { // Check if account is validated
                // Store user data in session
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
                        window.location.href = '../e-com/manageproductsA.php'; // Redirect to admin page
                    </script>
                    <?php
                } else {
                    ?>
                    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
                    <script>
                        window.location.href = '../Home_Page/home.php'; // Redirect to home page
                    </script>
                    <?php
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
  $(document).ready(function () {
  
     //  Toggle  Password Start
     $("#togglePassword").removeClass("fa fa-eye").addClass("fa fa-eye-slash");
     $("#togglePassword").click(function() {
        const passwordInput = $("#logpassword");
        const type = passwordInput.attr("type");

        if (type === "password") {
            passwordInput.attr("type", "text");
            $("#togglePassword").removeClass("fa fa-eye-slash").addClass("fa fa-eye");
        } else {
            passwordInput.attr("type", "password");
            $("#togglePassword").removeClass("fa fa-eye").addClass("fa fa-eye-slash");
        }
    });
    //  Toggle  Password End
    
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

<script>
    document.addEventListener("DOMContentLoaded", function () {
    function togglePassword(eyeIconId, passwordInputId) {
        var eyeicon = document.getElementById(eyeIconId);
        var passwordInput = document.getElementById(passwordInputId);

        eyeicon.addEventListener("click", function () {
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                eyeicon.src = "eye-open.jpg";
            } else {
                passwordInput.type = "password";
                eyeicon.src = "eye-close.jpg";
            }
        });
    }

    togglePassword("eyeicon-login", "logpassword");
    togglePassword("eyeicon-regis", "regispassword");
});
</script>
</body>
</html>

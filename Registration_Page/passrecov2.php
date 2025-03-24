<?php
session_start();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFN - Password Recovery</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap');
    </style>
</head>
<body>
        <div class="card ">
            <img src="../Resources/cfn_logo.png" class="logo" alt="Naturale">
            <h1 class="h1 text-center">Password Reset</h1>
            <form action="#" method="post">
                <div class="password-section">
                    <input type="password" name="password1" id="password1" class="form-control" placeholder="Enter new password" 
                            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$" 
                            title="Password must contain at least 8 characters, including an uppercase letter, a lowercase letter, a number, and a special character."
                            required>
                <span id="togglepassword" class="input-group-text">
                    <img src="eye-close.jpg" id="eyeicon-login1" class="eyeicon">
                </span>            
                <input type="password" name="password2" id="password2" class="form-control" placeholder="Confirm new password" 
                            pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&#])[A-Za-z\d@$!%*?&#]{8,}$" 
                            title="Password must contain at least 8 characters, including an uppercase letter, a lowercase letter, a number, and a special character."
                            required>
                <span id="togglepassword" class="input-group-text">
                    <img src="eye-close.jpg" id="eyeicon-login2" class="eyeicon">
                </span>
                </div>
                
                <br>
                <p style="width: 60%; text-align:justify; line-height: 20px; font-size: 13px; margin-top: 8px;" class="be-vietnam-pro-thin-italic">Password must contain a mix of numbers, letters, and special characters.</p> 
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="reset" name="reset">Reset Password</button>
                </div>    
                <br>        
            </form>
        </div>
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                const togglePassword = document.getElementById("eyeicon-login1"); // Target the toggle icon
                const passwordInput = document.getElementById("password1"); // Target the password input field

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
                const togglePassword = document.getElementById("eyeicon-login2"); // Target the toggle icon
                const passwordInput = document.getElementById("password2"); // Target the password input field

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



        </script>
</body>
</html>

<!-- 1/28/25: fixed spacings, added hover -->

<?php
    
    require_once "../conn.php";
    require_once "emailver.php";

    $email = $_SESSION['email1'];

    if (isset($_POST["reset"])) {
        // TODO: add hashing to the password
        $password1 = $_POST["password1"];
        $password2 = $_POST["password2"];
        
        if ($password1 == $password2) {
            $passwordHashed = password_hash($_POST['password1'], PASSWORD_BCRYPT);
            $sql = "UPDATE tb_user SET pass = '$passwordHashed' WHERE email = '$email'";
            $result = $conn->query($sql);
            
            if ($result === TRUE && $conn->affected_rows > 0) {
            ?>
                <script>
                    Swal.fire({
                        position: "center",    
                        icon: "success",
                        title: "Password Reset Successful. Continue to login account.",
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'http://localhost/CFN/Registration_Page/registration.php?signin=true';
                        
                        }
                    });
                </script>
                <?php

            } else {
                ?>
                    <script>
                        Swal.fire({
                        position: "center",    
                        icon: "error",
                        title: "Password Reset Failed. Invalid Email",
                        showConfirmButton:false,
                        timer: 1500  
                        });
                    </script>
                
                    <?php
            }
        } else {
            ?>
                    <script>
                        Swal.fire({
                        position: "center",    
                        icon: "error",
                        title: "Password Reset Failed. Passwords do not match.",
                        showConfirmButton:false,
                        timer: 1500  
                        });
                    </script>
                
                    <?php
        }
    }


?>

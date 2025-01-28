<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CFN - Registration Form</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">

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
                    <form action="#" method="post">
                        <div class="mb-3">
                            <input type="email" class="form-control" id="logemail" name="logemail" placeholder="Email" required>
                        </div>
                        <div class="mb-3">      
                            <input type="password" class="form-control" id="logpassword" name="logpassword" placeholder="Password" required>
                        </div>
                        <p><a href="passrecov.php">Forgot password?</a></p>
                        <br><br>
                        <div class="button">
                            <input type="submit" class="btn bkg" name="login" value = "Login">
                        </div>
                    </form><br><br><br>
                    <!-- swap button -->
                    <p>Don't have an account yet? Create one <button class="swipe text-primary btnSign-up"> here.</button></p>
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
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" id="regisemail" name="regisemail" placeholder="Email" required>
                        </div>
                        <div class="mb-3">      
                            <input type="password" class="form-control" id="regispassword" name="regispassword" placeholder="Password" required>
                        </div>
                        <br>
                        <p class="text-center mt-3">By creating an account, you agree to our <span class="link-primary">Terms</span> and acknowledge our <span class="link-primary">Privacy Policy</span>.</p>
                        <br>
                        <div class="button d-flex justify-content-center align-items-center">
                            <input type="submit" class="btn bkg" name="signup" value = "Register Account">
                        </div>
                    </form>
                    <br><br>
                   <p>Already have an account? <button class="text-primary swipe btnSign-in">Login</button>. </p>
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


  











    <?php
        require_once "../conn.php";
        require_once "emailver.php";
        
        //account registration
        if (isset($_POST['signup'])) {
          $username = $_POST['username'];
          $email = $_POST['regisemail'];
          //change hashing technique
          $password = md5($_POST['regispassword']);
          $validated = 0;
          $token = rand(000000,999999);


          
          $signup = "INSERT INTO tb_user(username, email, pass, validated, token) values ('$username','$email','$password', '$validated', '$token')";
          $result = $conn -> query($signup);
        
          if ($result == true) {
            //call email verification function, 
            send_verification( $email, $token); 
            ?>
            <script>
                Swal.fire({
                position: "center",    
                icon: "success",
                title: "Check your email to validate",
                text: "Please verify your email before logging in.",
                showConfirmButton:false,
                timer: 1500  
                });
            </script>
        
            <?php
          } else {
            echo $conn -> error;
          }
        }




        //login account
        if (isset($_POST['login'])) {
            $email = $_POST['logemail'];
            //change hashing technique
            $password = md5($_POST['logpassword']);
            
            // Query to check if the email and password match a record in the database
            $loginsql = "SELECT * FROM tb_user WHERE email='$email' AND pass='$password'";
            $result = $conn->query($loginsql);
            
            if ($result->num_rows > 0) {
                $user = $result->fetch_assoc();
                if ($user['validated'] == 1) {
                    // Email is validated
                    ?>
                    <script>
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: "Login successful",
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = 'dashboard.php'; // Redirect to the dashboard or any other page
                        });
                    </script>
                    <?php
                } else {
               // Email is not validated
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
            // Email or password is incorrect
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
</body>
</html>
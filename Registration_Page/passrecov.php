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

<style>
    @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap');
    </style>
</head>
<body>
        <div class="card">
            <img src="../Resources/cfn_logo.png" class="logo" alt="Naturale">
            <h1 class="h1 text-center">Password Reset</h1>
            <br>
            <p class="lead" style="width:75%";> Please enter the email address associated with your account and click on the button below to receive a reset link.</p>
                <br>
            <form action="#" method="post">
            <div class ="input-group">
                <input type="text" name="email1" id="email1" class="form-control" placeholder="Email" required>
            </div>
                <br>
                <div class="d-flex justify-content-center align-items-center">
                    <button type="submit" class="reset" name="verify">Send Email</button>
                </div>     
                <br>       
            </form>
        </div>

        <?php
            session_start();
            require_once "../conn.php";
            require_once "emailver.php";
             
             //account registration
            if (isset($_POST['verify'])) {
                $email1 = $_POST['email1'];
                $_SESSION['email1'] = $email1;
                $token = rand(000000,999999);

                $sql = "SELECT * FROM tb_user WHERE email = '$email1' AND validated=1";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {

                    // Insert the token into the database
                    $updateTokenSql = "UPDATE tb_user SET token = '$token' WHERE email = '$email1'";
                    $conn->query($updateTokenSql);

                    //call email verification function
                    pass_recov( $email1, $token); 
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
                    //email is not registered
                    ?>
                    <script>
                        Swal.fire({
                        position: "center",    
                        icon: "error",
                        title: "Email is not registered into any account",
                        showConfirmButton:false,
                        timer: 1500  
                        });
                    </script>
                
                    <?php
                }    
            }

        ?>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        document.querySelector("form").addEventListener("submit", function (event) {
            const emailInput = document.getElementById("email1").value.trim();
            const emailPattern = /^[^\s@]+@[^\s@]+\.[a-zA-Z]{2,}$/; // Regex for email validation

            if (!emailPattern.test(emailInput)) {
                event.preventDefault(); // Prevent form submission
                Swal.fire({
                    icon: "error",
                    title: "Invalid Email",
                    text: "Please enter a valid email address.",
                });
            }
        });
    </script>
    <style>
        .reset{
            width: 150px;
            height: 50px;
            align-items: center;
            justify-content: center;
            display: flex;
            margin: 0px 10px;
        }
        .input-group, .form-control {
            width: 250px;
            height: 50px;
            align-items: center;
            justify-content: center;
            display: flex;
            margin: 0px 10px;
            }
    </style>
</body>
</html>

<!-- 1/28/25: fixed spacings, added hover, removed last line (insert nalang yung message sa confirmation email)  -->
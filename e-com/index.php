<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <link rel="stylesheet" href="style.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">

<style>
    @import url('https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap');
    </style>
<body>
    <div class="container m-0 p-0">
        <div class="box">
            <!-- contains the regis forms -->
            <div class="form sign_in">
                <div class="col p-5 rounded" style="background-color: #ffffff;">
                    <h1>Login</h1>
                    <form action="#" id="form_input" method="post">
                        <div class="mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email"required>
                        </div>
                        <div class="mb-3">      
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <div class="button d-flex justify-content-center align-items-center">
                            <button type="submit" class="btn bkg">Login</button>
                        </div>
                    </form><br><br><br><br><br><br><br><br><br><br><br>
                    <!-- swap button -->
                    <button class="swipe text-primary btnSign-up"> Create an account here.</button>
                    <p><a href="#">Forget password?</a></p>
                </div>
            </div>
    
            <div class="form log_in">
                <div class="col p-5 rounded" style="background-color: #ffffff;">
                    <h1>Register</h1>
                    <form action="#" method="post">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="username" name="username" placeholder="Username" required>
                        </div>
                        <div class="mb-3">
                            <input type="email" class="form-control" id="email" name="email" placeholder="Email"required>
                        </div>
                        <div class="mb-3">      
                            <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        </div>
                        <p class="text-center mt-3">By creating an account, you agree to our <span class="link-primary">Terms</span> and acknowledge our <span class="link-primary">Privacy Policy</span>.</p>
                        <div class="button d-flex justify-content-center align-items-center">
                            <button type="submit" class="btn bkg">Register Account</button>
                        </div>
                    </form><br><br><br><br><br><br><br>
                    <span>Already have an account?  <button class=" text-primary swipe btnSign-in">Login</button>. </span>
                </div>
            </div>
        </div>
        <!-- contains overlay banner -->
        <div class="overlay">
            <div class="page page_signIn p-5 rounded text-center" style="background-color: #1F4529; color: #EED3B1;">
                <h1 class="banner display-1">Bonjour, ma belles! </h1>
                <H6 class="lead fst-italic">Cosmeticas Fraiche Natural</H6>
            </div>
            <div class="page page_signUp p-5 rounded text-center" style="background-color: #1F4529; color: #EED3B1;">
                <h1 class="display-1" style="font-size: 7rem;">Cosmeticas Fraiche Naturale</h3>
                <h6 class="lead fst-italic">Just Like Nature Intended</p>
            </div>
        </div>
    </div>

    <!-- link script -->
    <script src="main.js"></script>
</body>
</html>
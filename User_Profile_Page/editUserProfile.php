<?php
require_once "../conn.php";
session_start();

if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $sql = "SELECT username, email, first_name, last_name, contact_no, address FROM tb_user WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
    } else {
        $user = ['error' => 'User not found'];
    }
} else {
    header('Location: ../Registration_Page/registration.php');
    exit();
}

if (isset($_POST['logout'])) {
    session_destroy();
    header('Location: ../Home_Page/Home.html');
    exit();
}

if (isset($_POST['save'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $first_name = $_POST['first_name'];
    $last_name = $_POST['last_name'];
    $contact_no = $_POST['contact_no'];
    $address = $_POST['address'];

    $sql = "UPDATE tb_user SET username = '$username', email = '$email', first_name = '$first_name', last_name = '$last_name', contact_no = '$contact_no', address = '$address' WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result) {
        $user = ['username' => $username, 'email' => $email, 'first_name' => $first_name, 'last_name' => $last_name, 'contact_no' => $contact_no, 'address' => $address];
        header('Location: UserProfile.php');
    } else {
        $user = ['error' => 'Failed to update user'];
    }
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
    <title>Edit Account Profile</title>
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
            <input type="text" class="search-bar" placeholder="Search Product" />
            <div class="icons">
                <i class="far fa-user-circle fa-2x icon-profile"></i>
                <i class="fas fa-bars burger-menu"></i>
            </div>
        </div>
    </header>

    <div class="main">
        <div class="row">
            <div class="col-md-2 left-panel">
                <h3>My Account</h3>
                <a href="UserProfile.html" class="active">Profile</a>
                <br>
                <form method="post" action="">
                    <button class="transparent-button" name="logout">Logout</button>
                </form>
            </div>
            <div class="col-md-10 right-panel container">
                <button class="transparent-button" style="color: white;" id="edit-button">
                    <h2>Profile Details<p class="lead"><i class="bi bi-pen"></i></p></h2>
                </button>
                <br> 
                <form method="post" action="">
                    <div class="row">
                        <div class="col-md-2">
                            <p>Username: </p>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="username" class="editable form-control profile-input" value="<?php echo isset($user['username']) ? $user['username'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <p>Email:</p>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="email" class="editable form-control profile-input" value="<?php echo isset($user['email']) ? $user['email'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <p>First Name:</p>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="first_name" class="editable form-control profile-input" value="<?php echo isset($user['first_name']) ? $user['first_name'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <p>Last Name: </p>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="last_name" class="editable form-control profile-input" value="<?php echo isset($user['last_name']) ? $user['last_name'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <p>Contact No.:</p>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="contact_no" class="editable form-control profile-input" value="<?php echo isset($user['contact_no']) ? $user['contact_no'] : ''; ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2">
                            <p>Delivery Address: </p>
                        </div>
                        <div class="col-md-10">
                            <input type="text" name="address" class="editable form-control profile-input" value="<?php echo isset($user['address']) ? $user['address'] : ''; ?>">
                        </div>
                    </div>
                    <button type="submit" name="save" class="save-button">Save changes</button>
                </form>
            </div>
        </div>
    </div>
</body>

</html>
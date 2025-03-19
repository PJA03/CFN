<?php
require_once "../conn.php";
session_start();

// Regenerate session ID
session_regenerate_id(true);


if (isset($_SESSION['email'])) {
    $email = $_SESSION['email'];

    $stmt = $conn->prepare("SELECT username, email, first_name, last_name, contact_no, address, profile_image FROM tb_user WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

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
    <title>Edit Account Profile</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro&family=Bebas+Neue&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.3/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
                <div class="row">
                    <div class="col-md-7">
                        <button class="transparent-button" style="color: white;" id="edit-button">
                            <h2>Profile Details<p class="lead"><i class="bi bi-pen"></i></p></h2>
                        </button>
                        <br> 
                        <form method="post" action="" id="profileForm" enctype="multipart/form-data">
                            <div class="row">
                                <div class="col-md-3">
                                    <p>Username: </p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="username" id="username" class="editable form-control profile-input" value="<?php echo htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <p>Email:</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="email" name="email" class="editable form-control profile-input" value="<?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?>" required pattern="^[^\s@]+@[^\s@]+\.[^\s@]+$" title="Please enter a valid email address">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <p>First Name:</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="first_name" class="editable form-control profile-input" value="<?php echo htmlspecialchars($user['first_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <p>Last Name: </p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="last_name" class="editable form-control profile-input" value="<?php echo htmlspecialchars($user['last_name'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <p>Contact No.:</p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="contact_no" class="editable form-control profile-input" value="<?php echo htmlspecialchars($user['contact_no'], ENT_QUOTES, 'UTF-8'); ?>" required pattern="^\d{11}$" title="Please enter a valid 11-digit phone number">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <p>Delivery Address: </p>
                                </div>
                                <div class="col-md-9">
                                    <input type="text" name="address" class="editable form-control profile-input" value="<?php echo htmlspecialchars($user['address'], ENT_QUOTES, 'UTF-8'); ?>" required>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <p>Profile Image: </p>
                                </div>
                                <div class="col-md-9">
                                    <input type="file" id="profile_image" name="profile_image" style="margin: 8px;">
                                </div>
                            </div>
                            <button type="submit" name="save" id="save" class="button save-button">Save changes</button>
                            <button type="button" name="cancel" id="cancel" class="button cancel-button">Cancel</button>

                        </form>
                    </div>
                    <div class="col-md-5 text-center">
                        <img src="<?php echo htmlspecialchars(isset($user['profile_image']) && !empty($user['profile_image']) ? $user['profile_image'] : '../Resources/profile.png', ENT_QUOTES, 'UTF-8'); ?>" alt="Profile Icon" name="icon" id="icon" class="profile-icon" width="100" style="margin: 10px;"/>
                    </div>
                </div>    
            </div>
        </div>
    </div>

    <?php
        $updateSuccess = false;
        $noChanges = false;        

        if (isset($_POST['save'])) {
            $username = htmlspecialchars(trim($_POST['username']), ENT_QUOTES, 'UTF-8');
            $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
            $first_name = htmlspecialchars(trim($_POST['first_name']), ENT_QUOTES, 'UTF-8');
            $last_name = htmlspecialchars(trim($_POST['last_name']), ENT_QUOTES, 'UTF-8');
            $contact_no = htmlspecialchars(trim($_POST['contact_no']), ENT_QUOTES, 'UTF-8');
            $address = htmlspecialchars(trim($_POST['address']), ENT_QUOTES, 'UTF-8');
            
            // Handle file upload
            $profile_image = $user['profile_image'];
            if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == UPLOAD_ERR_OK) {
                $fileTmpPath = $_FILES['profile_image']['tmp_name'];
                $fileName = $_FILES['profile_image']['name'];
                $fileSize = $_FILES['profile_image']['size'];
                $fileType = $_FILES['profile_image']['type'];
                $fileNameCmps = explode(".", $fileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $allowedfileExtensions = array('jpg', 'jpeg', 'png');
                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $uploadFileDir = '../uploads/';
                    $dest_path = $uploadFileDir . $fileName;

                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                        $profile_image = $dest_path;
                    } else {
                        $user = ['error' => 'There was an error moving the uploaded file.'];
                    }
                } else {
                    $user = ['error' => 'Upload failed. Allowed file types: ' . implode(',', $allowedfileExtensions)];
                }
            }


        
             // Check if username exists and is not the current user's
             $checkUsername = $conn->prepare("SELECT * FROM tb_user WHERE username = ? AND email != ?");
             $checkUsername->bind_param("ss", $username, $_SESSION['email']);
             $checkUsername->execute();
             $result = $checkUsername->get_result();
        
             if ($result->num_rows > 0) {
                // Username is taken, show an error and prevent saving
                echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Username Taken',
                        text: 'Please choose a different username.'
                    });
                </script>";
                exit();
            }
        
         
            
        
            if ($username == $user['username'] && $email == $user['email'] && $first_name == $user['first_name'] && $last_name == $user['last_name'] && $contact_no == $user['contact_no'] && $address == $user['address'] && !isset($_FILES['profile_image'])) {
                $noChanges = true;
            } else {
                if ($result->num_rows > 0) {
                    echo "<script>
                        Swal.fire({
                            icon: 'error',
                            title: 'Username Taken',
                            text: 'Please choose a different username.'
                        });
                    </script>";
               } else {
                   
               }
        
                
        
            }    
            $sql = "UPDATE tb_user SET username = ?, email = ?, first_name = ?, last_name = ?, contact_no = ?, address = ?, profile_image = ? WHERE email = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssssssss", $username, $email, $first_name, $last_name, $contact_no, $address, $profile_image, $_SESSION['email']); // Use session email to match user
            $updateSuccess = $stmt->execute();
        
            if ($updateSuccess) {
                // session_destroy();
                // session_start();
                // session_regenerate_id(true);
                $_SESSION['email'] = $email;
                $_SESSION['username'] = $username;
                $_SESSION['first_name'] = $first_name;
                $_SESSION['last_name'] = $last_name;
                $_SESSION['contact_no'] = $contact_no;
                $_SESSION['address'] = $address;
                $_SESSION['profile_image'] = $profile_image;
            
                echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Profile Updated',
                        text: 'Your profile has been updated successfully.'
                    }).then(() => {
                        window.location.href = 'UserProfile.php';
                    });
                </script>";
            }
        }
    ?>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    <script>
document.addEventListener("DOMContentLoaded", function() {
    document.getElementById("username").addEventListener("input", function() {
        let username = this.value.trim();

        if (username !== "") {
            fetch("check_username.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "username=" + encodeURIComponent(username)
            })
            .then(response => response.text())
            .then(data => {
                if (data === "taken") {
                    Swal.fire({
                        icon: "error",
                        title: "Username Taken",
                        text: "Please choose a different username."
                    });
                }
            })
            .catch(error => console.error("Error:", error));
        }
    });
});
</script>

   <script>
    
    //username duplicates check
    document.addEventListener("DOMContentLoaded", function () {
    const usernameInput = document.getElementById("username");
    const saveButton = document.getElementById("save");

    usernameInput.addEventListener("input", function () {
        const username = this.value.trim();

        if (username !== "") {
            fetch("check_username.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/x-www-form-urlencoded"
                },
                body: "username=" + encodeURIComponent(username)
            })
                .then(response => response.text())
                .then(data => {
                    if (data === "taken") {
                        Swal.fire({
                            icon: "error",
                            title: "Username Taken",
                            text: "Please choose a different username."
                        });
                        saveButton.disabled = true; // Disable the save button
                    } else {
                        saveButton.disabled = false; // Enable the save button
                    }
                })
                .catch(error => console.error("Error:", error));
        } else {
            saveButton.disabled = false; // Enable the save button if the input is empty
        }
    });
});





        // image preview
        document.getElementById('profile_image').addEventListener('change', function(event) {
            var reader = new FileReader();
            reader.onload = function() {
                var output = document.getElementById('icon');
                output.src = reader.result;
            };
            reader.readAsDataURL(event.target.files[0]);
        });

        document.addEventListener('DOMContentLoaded', function() {
            // Store original values
            const originalValues = {
                username: document.querySelector('input[name="username"]').value,
                email: document.querySelector('input[name="email"]').value,
                first_name: document.querySelector('input[name="first_name"]').value,
                last_name: document.querySelector('input[name="last_name"]').value,
                contact_no: document.querySelector('input[name="contact_no"]').value,
                address: document.querySelector('input[name="address"]').value
            };

            let changesMade = false;

            // Function to check for changes
            function checkForChanges() {
                changesMade = (
                    document.querySelector('input[name="username"]').value !== originalValues.username ||
                    document.querySelector('input[name="email"]').value !== originalValues.email ||
                    document.querySelector('input[name="first_name"]').value !== originalValues.first_name ||
                    document.querySelector('input[name="last_name"]').value !== originalValues.last_name ||
                    document.querySelector('input[name="contact_no"]').value !== originalValues.contact_no ||
                    document.querySelector('input[name="address"]').value !== originalValues.address ||
                    document.querySelector('input[name="profile_image"]').files.length > 0
                );
            }
        
            // Add event listeners to input fields
            document.querySelectorAll('input').forEach(input => {
                input.addEventListener('input', checkForChanges);
            });

            document.getElementById('profile_image').addEventListener('change', checkForChanges);

            document.getElementById('profileForm').addEventListener('submit', function(event) {
                var email = document.querySelector('input[name="email"]').value;
                var contact_no = document.querySelector('input[name="contact_no"]').value;
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                var phonePattern = /^\d{11}$/;

                if (!emailPattern.test(email)) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Email',
                        text: 'Please enter a valid email address.'
                    });
                    return;
                }

                if (!phonePattern.test(contact_no)) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'error',
                        title: 'Invalid Phone Number',
                        text: 'Please enter a valid 11-digit phone number.'
                    });
                    return;
                }

                if (!changesMade) {
                    event.preventDefault();
                    Swal.fire({
                        icon: 'info',
                        title: 'No Changes',
                        text: 'No changes were made to your profile.'
                    }).then(() => {
                    window.location.href = 'UserProfile.php';
                });
                }
            });

            document.getElementById('cancel').addEventListener('click', function() {
                Swal.fire({
                    title: 'Discard Changes?',
                    text: "You will lose any unsaved changes",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#1F4529',
                    cancelButtonColor: '#D34646',
                    confirmButtonText: 'Yes'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'UserProfile.php';
                    }
                });
            });

            // Show success alert if update was successful
            <?php if ($updateSuccess): ?>
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Your profile has been updated successfully.'
                }).then(() => {
                    window.location.href = 'UserProfile.php';
                });
            <?php endif; ?>
        });

</script>
</body>

</html>
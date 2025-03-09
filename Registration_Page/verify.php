<?php
require_once "../conn.php";

// // Check if a session is already started before calling session_start()
// if (session_status() == PHP_SESSION_NONE) {
//     session_start();
// }

if (isset($_SESSION['email']) && isset($_SESSION['token'])) {
    $email = $_SESSION['email'];
    $token = $_SESSION['token'];

    $check_sql = "SELECT * FROM tb_user WHERE email = '$email' AND token = '$token'";
    $check_result = $conn->query($check_sql);

    if ($check_result === FALSE) {
        die("Error in SELECT query: " . $conn->error);
    }

    if ($check_result->num_rows > 0) {
        $user = $check_result->fetch_assoc();
        $token_created_at = strtotime($user['token_created_at']);
        $current_time = time();
        $token_expiration_time = $token_created_at + (10 * 60); // 10 minutes

        if ($current_time <= $token_expiration_time) {
            // Token is valid
            $sql = "UPDATE tb_user SET validated = 1, token = '', token_created_at = NULL WHERE email = '$email' AND token = '$token'";
            $result = $conn->query($sql);

            if ($result === TRUE && $conn->affected_rows > 0) {
                $message = "Email Verified. Your email has been successfully verified.";
                $alertType = "success";
            } else {
                $message = "Verification Failed. Invalid verification link or token. Error: " . $conn->error;
                $alertType = "error";
            }
        } else {
            // Token has expired
            $message = "Verification Failed. The token has expired.";
            $alertType = "error";
        }
    } else {
        $message = "Verification Failed. Invalid verification link or token.";
        $alertType = "error";
    }

    // Clear session variables after verification
    unset($_SESSION['email']);
    unset($_SESSION['token']);
} else {
    $message = "Verification Failed. Invalid verification link.";
    $alertType = "error";
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Email Verification</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <script>
        Swal.fire({
            icon: '<?php echo $alertType; ?>',
            title: '<?php echo $message; ?>',
            showConfirmButton: true
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'http://localhost/CFN/Registration_Page/registration.php?signin=true';
            }
        });
    </script>
</body>
</html>
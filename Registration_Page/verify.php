<?php
require_once '../conn.php';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Check if the email and token exist in the database
    $check_sql = "SELECT * FROM tb_user WHERE email = '$email' AND token = '$token'";
    $check_result = $conn->query($check_sql);

    if ($check_result === FALSE) {
        die("Error in SELECT query: " . $conn->error);
    }

    if ($check_result->num_rows > 0) {
        // Verify the token and update the database
        $sql = "UPDATE tb_user SET validated = 1 WHERE email = '$email' AND token = '$token'";
        $result = $conn->query($sql);

        if ($result === TRUE && $conn->affected_rows > 0) {
            $message = "Email Verified. Your email has been successfully verified.";
            $alertType = "success";
        } else {
            $message = "Verification Failed. Invalid verification link or token. Error: " . $conn->error;
            $alertType = "error";
        }
    } else {
        $message = "Verification Failed. Invalid verification link or token.";
        $alertType = "error";
    }
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
                window.location.href = 'http://localhost/CFN/Registration_Page/registration.php'; // Redirect to login page or any other page
            }
        });
    </script>
</body>
</html>
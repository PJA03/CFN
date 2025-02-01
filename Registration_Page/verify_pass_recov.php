<?php
require_once '../conn.php';

if (isset($_GET['email']) && isset($_GET['token'])) {
    $email = $_GET['email'];
    $token = $_GET['token'];

    // Debugging: Print email and token
    echo "Email: $email<br>";
    echo "Token: $token<br>";

    // Check if the email and token exist in the database
    // $check_sql = "SELECT * FROM tb_user WHERE email = '$email' AND token = '$token'";
    $check_sql = "SELECT * FROM tb_user";

    echo "Query: $check_sql<br>"; // Debugging: Print the query
    $check_result = $conn->query($check_sql);

    if ($check_result === FALSE) {
        die("Error in SELECT query: " . $conn->error);
    }

    echo "Number of rows: " . $check_result->num_rows . "<br>"; // Debugging: Print the number of rows

    if ($check_result->num_rows > 0) {
        // Verify the token and update the database
        $sql = "UPDATE tb_user SET validated = 1, token = '' WHERE email = '$email' AND token = '$token'";
        echo "Update Query: $sql<br>"; // Debugging: Print the update query
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
                window.location.href = 'http://localhost/CFN/Registration_Page/passrecov2.php';     
            }
        });
    </script>
</body>
</html>
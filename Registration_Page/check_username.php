<?php
header('Content-Type: text/plain'); // Ensures plain text output

session_start();
require_once "../conn.php";

if (isset($_POST['username']) && isset($_POST['current_email'])) {
    $username = trim($_POST['username']);
    $currentEmail = trim($_POST['current_email']);

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND email != ?");
    $stmt->bind_param("ss", $username, $currentEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo 'taken';
    } else {
        echo 'available';
    }
    exit;
} else {
    echo 'error'; // In case POST variables are missing
}
?>

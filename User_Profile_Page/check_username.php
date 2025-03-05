<?php
require_once "../conn.php";

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $checkUsername = "SELECT * FROM tb_user WHERE username = ?";
    $stmt = $conn->prepare($checkUsername);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->get_result();

    if ($stmt->num_rows > 0) {
        echo 'taken';
    } else {
        echo 'available';
    }
    $stmt -> close();
}

?>
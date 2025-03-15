<?php
require_once "../conn.php";

if(isset($_POST['username']) && isset($_POST['current_email'])){
    $username = $_POST['username'];
    $currentEmail = $_POST['current_email'];

    $stmt = $conn->prepare("SELECT * FROM tb_user WHERE username = ? AND email != ?");
    $stmt->bind_param("ss", $username, $currentEmail);
    $stmt->execute();
    $result = $stmt->get_result();

    header("Content-Type: text/plain");
    echo ($result->num_rows > 0) ? 'taken' : 'available';
    exit();
}

?>
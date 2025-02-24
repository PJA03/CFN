<?php
require_once "../conn.php";

if (isset($_POST['username'])) {
    $username = $_POST['username'];

    $query = "SELECT * FROM tb_user WHERE username = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    echo ($result->num_rows > 0) ? 'taken' : 'available';
}
?>

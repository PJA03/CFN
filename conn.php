<?php
$servername = "localhost";
$username = "trpkocuj_cosmeticasfraichenaturale";
$password = "CFN#2024!";
$name = "trpkocuj_db_cfn";

$conn = new mysqli($servername, $username, $password, $name);

// Check connection
if ($conn->connect_error) {
    header('HTTP/1.1 500 Internal Server Error');
    echo json_encode(['success' => false, 'error' => 'Connection failed: ' . $conn->connect_error]);
    exit;
}
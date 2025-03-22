<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: /CFN/Registration_Page/registration.php");
    exit;
}
?>
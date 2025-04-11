<?php
session_start();

// Allow access only for admin and superadmin roles
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: /CFN/Registration_Page/registration.php");
    exit;
}
?>
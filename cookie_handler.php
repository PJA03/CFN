<?php
session_start();
$cookie_expire = time() + (86400 * 30);

// Handle user session and cookies
if (isset($_SESSION['email'])) {
    $user = [
        'username' => $_SESSION['username'],
        'email' => $_SESSION['email'],
        'first_name' => $_SESSION['first_name'],
        'last_name' => $_SESSION['last_name'],
        'contact_no' => $_SESSION['contact_no'],
        'address' => $_SESSION['address'],
        'profile_image' => $_SESSION['profile_image'],
    ];
    if (isset($_COOKIE['cookie_consent']) && $_COOKIE['cookie_consent'] === 'all') {
        setcookie('username', $user['username'], $cookie_expire, "/");
    }
} else {
    $user = ['username' => 'Guest'];
    setcookie('username', 'Guest', $cookie_expire, "/");
}

// Logout logic
if (isset($_GET['logout'])) {
    setcookie('username', '', time() - 3600, "/");
    setcookie('last_visit', '', time() - 3600, "/");
    setcookie('first_visit', '', time() - 3600, "/");
    session_destroy();
    header("Location: home.php");
    exit;
}
?>
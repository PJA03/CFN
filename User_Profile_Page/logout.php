<?php
session_start();
session_destroy();
header('Location: ../Home_Page/Home.php');
exit();
?>

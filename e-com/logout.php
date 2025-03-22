<?php
session_start();
session_destroy();
header("Location: /CFN/Registration_Page/registration.php");
exit;
?>
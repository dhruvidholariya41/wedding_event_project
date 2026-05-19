<?php
session_start();

/* destroy session */
session_unset();
session_destroy();

/* redirect to login page */
header("Location: ../user/pages/login.php");
exit();
?>
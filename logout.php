<?php
session_start();

// All session destroy
session_unset();
session_destroy();

// Redirect to login page
header("Location: login.php");
exit;
?>
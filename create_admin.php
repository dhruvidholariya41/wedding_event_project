<?php
include("db_connect.php");

$username = "admin";
$email = "admin@gmail.com";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$role = "admin";

$sql = "INSERT INTO users (username, email, password, role) 
        VALUES ('$username', '$email', '$password', '$role')";

if (mysqli_query($conn, $sql)) {
    echo "Admin Created Successfully!";
} else {
    echo "Error: " . mysqli_error($conn);
}
?>
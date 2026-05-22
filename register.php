<?php
include("../pages/db_connect.php");
session_start();

$errors = [];

// Form submission logic
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $cpass = $_POST["confirm_password"];
    $phone = trim($_POST["phone"]);
    $address = trim($_POST["address"]);
    $dob = $_POST["dob"];
    $gender = $_POST["gender"];

    // Validation 
    if (!preg_match("/^[a-zA-Z\s]+$/", $username)) { $errors[] = "Name should contain only letters."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Invalid email format."; }
    if ($password !== $cpass) { $errors[] = "Passwords do not match."; }
    if (strlen($password) < 6) { $errors[] = "Password must be at least 6 characters long."; }

    // Check duplicate email 
    $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) { $errors[] = "Email already exists!"; }

    if (empty($errors)) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $sql = "INSERT INTO users (username, email, password, phone, address, dob, gender) VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssss", $username, $email, $hashedPassword, $phone, $address, $dob, $gender);
        if ($stmt->execute()) {
            echo "<script>alert('Registration successful!'); window.location.href = 'login.php';</script>";
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Wedding Event - Register</title>
    <link rel="stylesheet" href="./event.css">
</head>
<body>
    <?php include('../navbar/header.php'); ?>

    <div class="video-container">
        <video autoplay muted loop playsinline id="bgVideo">
            <source src="../../image/reg_loging_back.mp4" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>

    <main class="auth-container">
        <div class="register-card">
            <h2>Wedding Event Registration</h2>

            <?php if (!empty($errors)): ?>
                <div class="error-box">
                    <?php foreach ($errors as $err): ?>
                        <p>⚠️ <?php echo $err; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="register-form">
                <input type="text" name="username" placeholder="Full Name" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Password" required>
                <input type="password" name="confirm_password" placeholder="Confirm Password" required>
                <input type="text" name="phone" placeholder="Phone Number" required>
                <input type="text" name="address" placeholder="Address" required>

                <div style="text-align: left; margin-bottom: 5px; font-size: 14px; color: #ffe1ea;">Date of Birth:</div>
                <input type="date" name="dob" required>

                <div class="gender-group">
                    <label>Gender:</label>
                    <label><input type="radio" name="gender" value="Male" required> Male</label>
                    <label><input type="radio" name="gender" value="Female"> Female</label>
                </div>

                <button type="submit">Register Now</button>
            </form>

            <p class="login-link">
                Already have an account? <a href="login.php">Login</a>
            </p>
        </div>
    </main>

    <?php include('../navbar/footer.php'); ?>
</body>
</html>
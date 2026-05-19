<?php
session_start();

include("db_connect.php");

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {

    if (!empty($_POST['email']) && !empty($_POST['password'])) {

        $email = mysqli_real_escape_string($conn, trim($_POST['email']));
        $password = $_POST['password'];

        $query = "SELECT * FROM users WHERE email='$email' LIMIT 1";
        $result = mysqli_query($conn, $query);

        if ($result && mysqli_num_rows($result) == 1) {

            $row = mysqli_fetch_assoc($result);

            // ✅ Verify Password
            if (password_verify($password, $row['password'])) {

                $_SESSION['user_id']  = $row['id'];
                $_SESSION['username'] = $row['username'];
                
                // ✅ Safe Role Handling (IMPORTANT STEP 3)
                $role = isset($row['role']) ? $row['role'] : 'user';
                $_SESSION['role'] = $role;

                // ✅ Role Based Redirect
                if ($role === 'admin') {

                    header("Location: ../../admin/dashboard.php");
                    exit();

                // } elseif ($role === 'staff') {

                //     header("Location: ../staff/staff_dashboard.php");
                //     exit();

                } else {

                    header("Location: home.php");
                    exit();
                }

            } else {
                $error = "❌ Wrong password!";
            }

        } else {
            $error = "❌ Email not registered!";
        }

    } else {
        $error = "❌ Please fill all fields!";
    }
}
?>

<?php include('../navbar/header.php'); ?>
<link rel="stylesheet" href="../pages/event.css">

<section class="auth-bg">
  <div class="auth-overlay"></div>

  <div class="auth-card">
    <h2>Welcome Back</h2>
    <p class="auth-sub">Login to manage your wedding bookings</p>

    <?php if ($error != "") { ?>
      <p style="color:red; text-align:center;"><?= $error ?></p>
    <?php } ?>

    <form method="POST" action="">
      <div class="form-group">
        <label>Email</label>
        <input type="email" name="email" required>
      </div>

      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" required>
      </div>

      <button type="submit" name="login" class="auth-btn">Login</button>

      <p class="auth-text">
        Don't have an account? <a href="register.php">Create Account</a>
      </p>
    </form>
  </div>
</section>

<?php include('../navbar/footer.php'); ?>
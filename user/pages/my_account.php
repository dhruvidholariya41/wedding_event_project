<?php
session_start();
include('db_connect.php'); 

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if(isset($_POST['update_profile'])){
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $address = mysqli_real_escape_string($conn, $_POST['address']);
    $update_query = "UPDATE users SET phone='$phone', address='$address' WHERE id='$user_id'";
    if(mysqli_query($conn, $update_query)){
        echo "<script>alert('Profile Updated!'); window.location.href='my_account.php';</script>";
        exit();
    }
}

$user = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM users WHERE id='$user_id'"));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Account - Golden Promise</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body { 
            background: #fdf2f8; 
            background-image: radial-gradient(circle at 20% 20%, #fce7f3 0%, transparent 40%), radial-gradient(circle at 80% 80%, #fdf2f8 0%, transparent 40%);
            font-family: 'Poppins', sans-serif; min-height: 100vh; display: flex; align-items: center; justify-content: center; margin: 0;
        }

        /* મેઈન કન્ટેનર */
        .profile-wrapper {
            display: flex; width: 850px; background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(15px); border-radius: 30px; overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.05); border: 1px solid rgba(255,255,255,0.5);
        }

        /* ડાબી બાજુનું પિંક સેક્શન */
        .profile-side {
            width: 35%; background: linear-gradient(135deg, #ec4899, #be185d);
            padding: 40px; color: white; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center;
        }
        .profile-side i { font-size: 100px; margin-bottom: 20px; filter: drop-shadow(0 10px 15px rgba(0,0,0,0.1)); }
        .profile-side h2 { margin: 10px 0 5px; font-size: 22px; }
        .profile-side p { font-size: 13px; opacity: 0.8; margin: 0; }

        /* જમણી બાજુનું ફોર્મ સેક્શન */
        .profile-main { width: 65%; padding: 45px; background: white; }
        .profile-main h3 { margin: 0 0 25px; color: #1e293b; font-size: 20px; border-bottom: 2px solid #fce7f3; display: inline-block; padding-bottom: 5px; }

        .input-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        .form-group { margin-bottom: 20px; }
        .form-group.full { grid-column: span 2; }
        
        label { display: block; font-size: 11px; font-weight: 700; color: #ec4899; text-transform: uppercase; margin-bottom: 8px; letter-spacing: 1px; }
        
        input {
            width: 100%; padding: 12px 15px; border: 1.5px solid #f1f5f9; border-radius: 12px;
            font-size: 14px; background: #f8fafc; transition: 0.3s; box-sizing: border-box;
        }
        input:focus { border-color: #ec4899; outline: none; background: white; box-shadow: 0 0 0 4px rgba(236, 72, 153, 0.1); }
        input[readonly] { background: #f1f5f9; cursor: not-allowed; color: #64748b; }

        .btn-save {
            background: #ec4899; color: white; border: none; padding: 14px 35px;
            border-radius: 15px; font-weight: bold; cursor: pointer; transition: 0.3s;
            box-shadow: 0 10px 20px rgba(236, 72, 153, 0.2); width: 100%; margin-top: 10px;
        }
        .btn-save:hover { background: #be185d; transform: translateY(-2px); box-shadow: 0 15px 25px rgba(236, 72, 153, 0.3); }

        .home-link { display: block; text-align: center; margin-top: 20px; color: #64748b; text-decoration: none; font-size: 13px; font-weight: 500; }
        .home-link:hover { color: #ec4899; }

        @media (max-width: 800px) {
            .profile-wrapper { flex-direction: column; width: 90%; }
            .profile-side { width: 100%; padding: 30px; box-sizing: border-box; }
            .profile-main { width: 100%; padding: 30px; box-sizing: border-box; }
            .input-grid { grid-template-columns: 1fr; }
            .form-group.full { grid-column: span 1; }
        }
    </style>
</head>
<body>

<div class="profile-wrapper">
    <div class="profile-side">
        <i class="fa-solid fa-circle-user"></i>
        <h2><?= htmlspecialchars($user['username']); ?></h2>
        <p><?= htmlspecialchars($user['email']); ?></p>
        <p style="margin-top: 20px; font-style: italic; opacity: 0.7;">Member since <?= date('Y', strtotime($user['dob'])); ?></p>
    </div>

    <div class="profile-main">
        <h3>Account Settings</h3>
        <form method="POST">
            <div class="input-grid">
                <div class="form-group">
                    <label>User ID</label>
                    <input type="text" value="#<?= $user['id']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label>Gender</label>
                    <input type="text" value="<?= ucfirst($user['gender']); ?>" readonly>
                </div>
                <div class="form-group full">
                    <label>Phone Number</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($user['phone']); ?>" required>
                </div>
                <div class="form-group full">
                    <label>Current Address</label>
                    <input type="text" name="address" value="<?= htmlspecialchars($user['address']); ?>" required>
                </div>
                <div class="form-group">
                    <label>Birth Date</label>
                    <input type="text" value="<?= date('d M, Y', strtotime($user['dob'])); ?>" readonly>
                </div>
            </div>

            <button type="submit" name="update_profile" class="btn-save">Update Profile</button>
            <a href="home.php" class="home-link"><i class="fa-solid fa-house"></i> Return to Home</a>
        </form>
    </div>
</div>

</body>
</html>
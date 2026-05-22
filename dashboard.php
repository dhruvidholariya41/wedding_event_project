<?php
session_start();
include("../admin/db_connect.php");

// ✅ Only Admin Access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}

/* ===== STATISTICS LOGIC ===== */
// બધી જ 6 ઇવેન્ટના ટેબલ અંહી સામેલ છે
$events = ["mehndi_bookings", "haldi_bookings", "mandap_muhrat_bookings", "sangeet_bookings", "wedding_bookings", "reception_bookings"];
$total_bookings = 0; 
$total_revenue = 0; 
$total_pending = 0;

foreach($events as $table){
    $checkTable = mysqli_query($conn,"SHOW TABLES LIKE '$table'");
    if($checkTable && mysqli_num_rows($checkTable) == 1){
        // Total Count
        $countRes = mysqli_query($conn,"SELECT COUNT(*) as total FROM $table");
        if($countRes) {
            $countRow = mysqli_fetch_assoc($countRes);
            $total_bookings += $countRow['total'] ?? 0;
        }

        // Revenue & Pending Calculation
        $res = mysqli_query($conn, "SELECT SUM(total_amount) as rev, SUM(total_amount - payable_amount) as pen FROM $table");
        if($res){
            $row = mysqli_fetch_assoc($res);
            $total_revenue += $row['rev'] ?? 0;
            $total_pending += $row['pen'] ?? 0;
        }
    }
}

// User Count
$user_res = mysqli_query($conn,"SELECT COUNT(*) as total FROM users");
$user_count = ($user_res) ? mysqli_fetch_assoc($user_res)['total'] : 0;

// SAFE MESSAGE COUNT
$checkCol = mysqli_query($conn, "SHOW COLUMNS FROM contact_messages LIKE 'status'");
if(mysqli_num_rows($checkCol) > 0) {
    $msg_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM contact_messages WHERE status = 'unread'");
    $msg_count = ($msg_res) ? mysqli_fetch_assoc($msg_res)['total'] : 0;
} else {
    $msg_res = mysqli_query($conn, "SELECT COUNT(*) as total FROM contact_messages");
    $msg_count = ($msg_res) ? mysqli_fetch_assoc($msg_res)['total'] : 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Golden Promise - Admin Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --sidebar-bg: #111827; --accent: #ec4899; --main-bg: #f8fafc; }
        body { margin:0; font-family: 'Segoe UI', Arial; background: var(--main-bg); display: flex; }

        /* Sidebar Style */
        .sidebar { width: 250px; height: 100vh; background: var(--sidebar-bg); position: fixed; color: white; transition: 0.3s; overflow-y: auto; }
        .sidebar h2 { text-align: center; padding: 25px 10px; color: var(--accent); border-bottom: 1px solid #1f2937; margin: 0; }
        .sidebar a { display: block; color: #9ca3af; padding: 14px 25px; text-decoration: none; font-size: 14px; transition: 0.3s; }
        .sidebar a i { margin-right: 12px; width: 18px; text-align: center; }
        .sidebar a:hover, .sidebar a.active { background: #1f2937; color: white; border-left: 4px solid var(--accent); }

        /* Main Area */
        .main { margin-left: 250px; width: calc(100% - 250px); padding: 40px; }
        .header { margin-bottom: 35px; }
        .header h1 { font-size: 26px; color: #1e293b; margin: 0; }
        .header p { color: #64748b; margin: 5px 0 0; }

        /* Statistics Cards */
        .card-container { display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 25px; }
        .card { background: white; padding: 25px; border-radius: 16px; display: flex; align-items: center; box-shadow: 0 4px 12px rgba(0,0,0,0.03); border: 1px solid #f1f5f9; transition: 0.3s; }
        .card:hover { transform: translateY(-5px); box-shadow: 0 12px 20px rgba(0,0,0,0.08); }
        .card-icon { width: 55px; height: 55px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 24px; margin-right: 20px; }
        .card-info h3 { margin: 0; font-size: 13px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
        .card-info p { margin: 6px 0 0; font-size: 24px; font-weight: 800; color: #0f172a; }

        .bg-users { background: #e0e7ff; color: #4338ca; }
        .bg-bookings { background: #fae8ff; color: #a21caf; }
        .bg-revenue { background: #dcfce7; color: #15803d; }
        .bg-pending { background: #fef2f2; color: #b91c1c; }

        .info-section { margin-top: 45px; display: grid; grid-template-columns: 1fr 1fr; gap: 25px; }
        .info-box { background: white; padding: 25px; border-radius: 16px; box-shadow: 0 4px 10px rgba(0,0,0,0.02); }
        .info-box h4 { margin: 0 0 15px 0; display: flex; align-items: center; gap: 10px; color: #334155; }
        .btn-link { display: inline-block; margin-top: 15px; padding: 10px 20px; background: var(--accent); color: white; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 14px; transition: 0.3s; }
        .btn-link:hover { opacity: 0.9; transform: scale(1.02); }
    </style>
</head>
<body>

<div class="sidebar">
    <h2>GP Admin</h2>
    <a href="dashboard.php" class="active"><i class="fas fa-chart-line"></i> Dashboard</a>
    <a href="admin_mehndi.php"><i class="fas fa-hand-holding-heart"></i> Mehndi</a>
    <a href="admin_haldi.php"><i class="fas fa-sun"></i> Haldi</a>
    <a href="admin_mandap_muhrat.php"><i class="fas fa-om"></i> Mandap Muhurat</a>
    <a href="admin_sangeet.php"><i class="fas fa-music"></i> Sangeet Night</a>
    <a href="admin_wedding.php"><i class="fas fa-ring"></i> Wedding</a>
    <a href="admin_reception.php"><i class="fas fa-glass-cheers"></i> Reception</a>
    <hr style="border: 0.5px solid #1f2937; margin: 10px 20px;">
    <a href="admin_users.php"><i class="fas fa-users-cog"></i> Manage Users</a>
    <a href="admin_reviews.php"><i class="fas fa-star"></i> Manage Reviews</a>
    <a href="admin_messages.php"><i class="fas fa-envelope"></i> Inquiries (<?= $msg_count ?>)</a>
    <a href="admin_cancelled_booking.php"><i class="fa fa-times-circle"></i> Cancelled Bookings</a>
    <a href="admin_manage_staff.php"><i class="fas fa-user-tie"></i> Manage Staff</a>
    <a href="logout.php" style="margin-top: 20px; color: #ef4444;"><i class="fas fa-power-off"></i> Logout</a>
</div>

<div class="main">
    <div class="header">
        <h1>Welcome Back, <?= htmlspecialchars($_SESSION['username']); ?>! 👋</h1>
        <p>Here is what's happening with Golden Promise today, <?= date('d M, Y'); ?></p>
    </div>

    <div class="card-container">
        <div class="card">
            <div class="card-icon bg-users"><i class="fas fa-user-friends"></i></div>
            <div class="card-info">
                <h3>Total Users</h3>
                <p><?= number_format($user_count); ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon bg-bookings"><i class="fas fa-calendar-alt"></i></div>
            <div class="card-info">
                <h3>Total Bookings</h3>
                <p><?= number_format($total_bookings); ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon bg-revenue"><i class="fas fa-coins"></i></div>
            <div class="card-info">
                <h3>Total Revenue</h3>
                <p>₹<?= number_format($total_revenue); ?></p>
            </div>
        </div>

        <div class="card">
            <div class="card-icon bg-pending"><i class="fas fa-wallet"></i></div>
            <div class="card-info">
                <h3>Pending Payment</h3>
                <p>₹<?= number_format($total_pending); ?></p>
            </div>
        </div>
    </div>

    <div class="info-section">
        <div class="info-box">
            <h4><i class="fas fa-comment-dots" style="color: #ec4899;"></i> New Inquiries</h4>
            <p style="color: #64748b;">You have <b><?= $msg_count ?></b> messages waiting for your reply.</p>
            <a href="admin_messages.php" class="btn-link">View Messages</a>
        </div>
        <div class="info-box">
            <h4><i class="fas fa-star" style="color: #fbbf24;"></i> Latest Reviews</h4>
            <p style="color: #64748b;">Check out the latest feedback from your happy customers.</p>
            <a href="admin_reviews.php" class="btn-link">View Reviews</a>
        </div>
    </div>
</div>

</body>
</html>
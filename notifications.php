<?php 
include('../navbar/header.php'); 
include('db_connect.php');

if(!isset($_SESSION['user_id'])){ 
    header("Location: login.php"); 
    exit(); 
}

$user_id = $_SESSION['user_id'];
$user_email = "";

$u_res = mysqli_query($conn, "SELECT email FROM users WHERE id = '$user_id'");
if($u_row = mysqli_fetch_assoc($u_res)) { 
    $user_email = $u_row['email']; 
}

/* 1. Fetch all event booking statuses */
$booking_sql = "
    (SELECT 'Haldi' as event_name, status, event_date FROM haldi_bookings WHERE user_id = '$user_id')
    UNION
    (SELECT 'Mehndi' as event_name, status, event_date FROM mehndi_bookings WHERE user_id = '$user_id')
    UNION
    (SELECT 'Sangeet' as event_name, status, event_date FROM sangeet_bookings WHERE user_id = '$user_id')
    UNION
    (SELECT 'Wedding' as event_name, status, event_date FROM wedding_bookings WHERE user_id = '$user_id')
    UNION
    (SELECT 'Reception' as event_name, status, event_date FROM reception_bookings WHERE user_id = '$user_id')
    ORDER BY event_date DESC";

$booking_res = mysqli_query($conn, $booking_sql);

/* 2. Fetch admin message replies */
$msg_res = mysqli_query($conn, "SELECT * FROM contact_messages WHERE email = '$user_email' AND admin_reply IS NOT NULL ORDER BY replied_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Notifications - Golden Promise</title>
    <link rel="stylesheet" href="event.css">
    <style>
        .noti-main-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .noti-grid {
            display: grid;
            grid-template-columns: 1fr 1fr; /* બે સમાન ભાગમાં વહેંચણી */
            gap: 30px;
            align-items: start;
        }

        .noti-title { 
            color: #ec4899; 
            margin-bottom: 20px; 
            border-bottom: 2px solid #fce7f3; 
            padding-bottom: 10px; 
            font-size: 24px;
            font-weight: 600;
        }

        .noti-card { 
            background: #fff; 
            padding: 20px; 
            border-radius: 15px; 
            box-shadow: 0 5px 15px rgba(0,0,0,0.05); 
            margin-bottom: 15px; 
            border-left: 5px solid #ec4899;
        }

        /* Status Badge */
        .status-badge {
            padding: 3px 10px;
            border-radius: 15px;
            font-size: 11px;
            font-weight: bold;
            float: right;
            text-transform: uppercase;
        }
        .bg-approved { background: #d1fae5; color: #065f46; }
        .bg-pending { background: #fef3c7; color: #92400e; }

        /* Admin Reply Box */
        .admin-reply-box {
            background: #fdf2f8; 
            padding: 12px;
            border-radius: 10px;
            margin-top: 8px;
            border: 1px dashed #ec4899; 
        }

        .reply-text { color: #be185d; margin: 0; font-size: 14px; }
        .timestamp { display: block; margin-top: 8px; color: #888; font-size: 11px; }

        @media (max-width: 900px) {
            .noti-grid { grid-template-columns: 1fr; } /* મોબાઈલમાં એકની નીચે એક આવશે */
        }
    </style>
</head>
<body>

<div class="noti-main-container">
    <div class="noti-grid">
        
        <div class="noti-left">
            <h2 class="noti-title">💬 Admin Messages</h2>
            <?php if(mysqli_num_rows($msg_res) > 0): ?>
                <?php while($msg = mysqli_fetch_assoc($msg_res)): ?>
                    <div class="noti-card">
                        <p style="margin:0; font-size: 15px; color: #444;"><b>Your Inquiry:</b> <?php echo htmlspecialchars($msg['message']); ?></p>
                        <div class="admin-reply-box">
                            <p class="reply-text"><b>Admin:</b> <?php echo htmlspecialchars($msg['admin_reply']); ?></p>
                        </div>
                        <small class="timestamp">Date: <?php echo $msg['replied_at']; ?></small>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #888;">No messages from admin yet.</p>
            <?php endif; ?>
        </div>

        <div class="noti-right">
            <h2 class="noti-title">📅 Booking Updates</h2>
            <?php if(mysqli_num_rows($booking_res) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($booking_res)): ?>
                    <div class="noti-card">
                        <span class="status-badge <?php echo ($row['status'] == 'Approved') ? 'bg-approved' : 'bg-pending'; ?>">
                            <?php echo $row['status']; ?>
                        </span>
                        <p style="margin:0; font-size: 15px; color: #444;">
                            Booking: <b><?php echo $row['event_name']; ?></b><br>
                            <small style="color: #777;">Date: <?php echo $row['event_date']; ?></small>
                        </p>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p style="color: #888;">No booking history found.</p>
            <?php endif; ?>
        </div>

    </div>
</div>

<?php include('../navbar/footer.php'); ?>
</body>
</html>
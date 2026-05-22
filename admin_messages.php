<?php 
session_start();
include('db_connect.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') { 
    header("Location: ../pages/login.php"); 
    exit(); 
}

if (isset($_POST['send_reply'])) {
    $id = intval($_POST['msg_id']);
    $reply = mysqli_real_escape_string($conn, $_POST['reply_text']);
    
    // admin_reply અને replied_at બંને અપડેટ થશે
    $update_sql = "UPDATE contact_messages SET admin_reply = '$reply', replied_at = NOW() WHERE id = '$id'";
    mysqli_query($conn, $update_sql);
    echo "<script>alert('Reply sent!'); window.location='admin_messages.php';</script>";
}

$result = mysqli_query($conn, "SELECT * FROM contact_messages ORDER BY id DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - User Messages</title>
    <style>
        body { background-color: #f9fafb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; padding: 15px; }
        .admin-container { max-width: 800px; margin: 20px auto; }
        .header-section { text-align: center; margin-bottom: 25px; }
        .back-btn { display: inline-block; background:#007bff;; color: white; padding: 6px 25px; border-radius: 6px; text-decoration: none; font-size: 13px; transition: 0.2s; }
        .back-btn:hover {  background:#007bff; }

        .msg-card { 
            background: #fff; 
            padding: 15px; 
            margin-bottom: 15px; 
            border-radius: 10px; 
            box-shadow: 0 2px 6px rgba(0,0,0,0.05); 
            border: 1px solid #eee;
            border-left: 4px solid #ec4899; 
        }

        .user-info { font-size: 14px; color: #374151; margin-bottom: 8px; display: flex; justify-content: space-between; }
        .user-info b { color: #ec4899; }
        
        .user-msg { 
            background: #fdf2f8; 
            padding: 10px; 
            border-radius: 6px; 
            color: #4b5563; 
            font-size: 13.5px;
            margin: 5px 0;
            line-height: 1.4;
        }

        .reply-section { margin-top: 12px; padding-top: 10px; border-top: 1px solid #f3f4f6; }
        
        textarea { width: 100%; padding: 8px; border: 1px solid #d1d5db; border-radius: 6px; font-size: 13px; min-height: 60px; box-sizing: border-box; }

        .send-btn { background: #ec4899; color: white; border: none; padding: 7px 15px; border-radius: 6px; cursor: pointer; font-size: 13px; font-weight: 600; margin-top: 8px; }

        .admin-replied { 
            background: #f0fdf4; 
            border: 1px solid #dcfce7; 
            padding: 10px; 
            border-radius: 6px; 
            color: #166534; 
            font-size: 13px;
        }
        
        /* Time બતાવવા માટેની સ્ટાઇલ */
        .reply-time {
            display: block;
            margin-top: 5px;
            font-size: 11px;
            color: #68d391;
            font-weight: normal;
        }
    </style>
</head>
<body>

<div class="admin-container">
    <div class="header-section">
        <h3 style="margin: 0 0 10px 0;"> Message Inquiries List</h3>
        <br>
        <a href="dashboard.php" class="back-btn">← Dashboard</a>
    </div>

    <?php if(mysqli_num_rows($result) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($result)): ?>
            <div class="msg-card">
                <div class="user-info">
                    <span><b>From:</b> <?= htmlspecialchars($row['name']) ?></span> 
                    <span style="font-size: 12px; color: #9ca3af;"><?= htmlspecialchars($row['email']) ?></span>
                </div>
                
                <div class="user-msg">"<?= htmlspecialchars($row['message']) ?>"</div>
                
                <div class="reply-section">
                    <?php if($row['admin_reply']): ?>
                        <div class="admin-replied">
                            <b>✓ Reply:</b> <?= htmlspecialchars($row['admin_reply']) ?>
                            <span class="reply-time">Replied at: <?= date('d M, Y | h:i A', strtotime($row['replied_at'])) ?></span>
                        </div>
                    <?php else: ?>
                        <form method="POST">
                            <input type="hidden" name="msg_id" value="<?= $row['id'] ?>">
                            <textarea name="reply_text" placeholder="Write reply..." required></textarea>
                            <button type="submit" name="send_reply" class="send-btn">Send Reply</button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p style="text-align: center; color: #9ca3af; font-size: 14px;">No inquiries found.</p>
    <?php endif; ?>
</div>

</body>
</html>
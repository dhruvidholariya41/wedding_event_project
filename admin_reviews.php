<?php 
session_start();
include('db_connect.php'); 

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../pages/login.php");
    exit();
}

if (isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $res = mysqli_query($conn, "SELECT image FROM reviews WHERE id = $id");
    $row = mysqli_fetch_assoc($res);
    
    if (!empty($row['image'])) {
        $file_path = "../image/" . $row['image']; 
        if (file_exists($file_path)) {
            unlink($file_path);
        }
    }
    
    mysqli_query($conn, "DELETE FROM reviews WHERE id = $id");
    echo "<script>alert('Review deleted!'); window.location='admin_reviews.php';</script>";
}

$all_reviews = mysqli_query($conn, "SELECT * FROM reviews ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin - Review Management</title>
    <link rel="stylesheet" href="../user/pages/event.css">
    <style>
        body { background-color: #f8f9fa; font-family: 'Poppins', sans-serif; }
        
        .admin-review-wrapper { 
            max-width: 900px; 
            margin: 50px auto; 
            padding: 20px;
        }

        .page-header {
            text-align: center;
            margin-bottom: 30px;
        }

        .back-btn {
            display: inline-block;
            background:#007bff;;
            color: white;
            padding: 8px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 500;
            font-size: 14px;
            transition: 0.3s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .back-btn:hover { background:#007bff;; transform: translateY(-1px); }

        /* રિવ્યુ બોક્સ/કાર્ડ સ્ટાઇલ */
        .review-card {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            border: 1px solid #b2b3b5;
            transition: transform 0.2s;
        }
        .review-card:hover { transform: scale(1.01); box-shadow: 0 4px 15px rgba(0,0,0,0.1); }

        .review-content { flex: 1; }
        
        .user-info { font-weight: 700; color: #1a202c; font-size: 16px; display: block; margin-bottom: 4px; }
        .stars { color: #fbbf24; font-size: 18px; margin-bottom: 8px; display: block; }
        .comment-text { color: #4a5568; line-height: 1.5; margin: 0; }

        .review-image-box { margin: 0 20px; }
        .review-img-preview { 
            width: 90px; 
            height: 70px; 
            object-fit: cover; 
            border-radius: 8px; 
            border: 1px solid #e2e8f0;
        }

        .del-btn { 
            background: #fee2e2; 
            color: #dc2626; 
            padding: 8px 16px; 
            border-radius: 8px; 
            text-decoration: none; 
            font-weight: 600; 
            font-size: 13px;
            transition: 0.3s;
        }
        .del-btn:hover { background: #dc2626; color: white; }

        .no-data { text-align: center; color: #718096; padding: 40px; }
    </style>
</head>
<body>

<div class="admin-review-wrapper">
    <div class="page-header">
        <h2 style="color: #1a202c; margin-bottom: 15px;">Customer Feedback Management</h2>
        <a href="dashboard.php" class="back-btn">← Back to Dashboard</a>
    </div>

    <?php if(mysqli_num_rows($all_reviews) > 0): ?>
        <?php while($row = mysqli_fetch_assoc($all_reviews)): ?>
            <div class="review-card">
                <div class="review-content">
                    <span class="stars"><?php for($i=1; $i<=5; $i++) echo ($i <= $row['rating']) ? "★" : "☆"; ?></span>
                    <span class="user-info">By: <?php echo htmlspecialchars($row['user_name']); ?></span>
                    <p class="comment-text"><?php echo htmlspecialchars($row['comment']); ?></p>
                    <small style="color: #a0aec0; font-size: 11px;"><?php echo date('M d, Y', strtotime($row['created_at'])); ?></small>
                </div>

                <?php if(!empty($row['image'])): ?>
                    <div class="review-image-box">
                        <img src="../image/<?php echo $row['image']; ?>" class="review-img-preview" alt="Review Image">
                    </div>
                <?php endif; ?>

                <div class="action-box">
                    <a href="admin_reviews.php?delete_id=<?php echo $row['id']; ?>" class="del-btn" onclick="return confirm('Confirm Delete?')">Delete</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <div class="no-data">No reviews found in the database.</div>
    <?php endif; ?>
</div>

</body>
</html>
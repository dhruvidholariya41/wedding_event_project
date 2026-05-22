<?php 
include('../navbar/header.php'); 
include('db_connect.php'); 

// 1. રિવ્યુ સબમિટ કરવાનું લોજિક
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $user_name = $_SESSION['username'];
    $rating = $_POST['rating'];
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    
    $image_name = "";
    if (!empty($_FILES['review_image']['name'])) {
        $image_name = time() . '_' . $_FILES['review_image']['name'];
        $target_path = "../../image/" . $image_name; 
        move_uploaded_file($_FILES['review_image']['tmp_name'], $target_path);
    }

    $sql = "INSERT INTO reviews (user_id, user_name, rating, comment, image) VALUES ('$user_id', '$user_name', '$rating', '$comment', '$image_name')";
    mysqli_query($conn, $sql);
    echo "<script>alert('Review shared successfully!'); window.location='reviews.php';</script>";
}

// 2. યુઝર પોતાનો રિવ્યુ ડિલીટ કરી શકે તે માટેનું લોજિક
if (isset($_GET['delete_id']) && isset($_SESSION['user_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $u_id = $_SESSION['user_id'];

    $check = mysqli_query($conn, "SELECT image FROM reviews WHERE id = $delete_id AND user_id = $u_id");
    if ($row = mysqli_fetch_assoc($check)) {
        if (!empty($row['image'])) {
            $file_path = "../../image/" . $row['image'];
            if (file_exists($file_path)) unlink($file_path);
        }
        mysqli_query($conn, "DELETE FROM reviews WHERE id = $delete_id");
        echo "<script>alert('Your review has been deleted.'); window.location='reviews.php';</script>";
    }
}

$all_reviews = mysqli_query($conn, "SELECT * FROM reviews ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Experiences - Golden Promise</title>
    <link rel="stylesheet" href="event.css">
    <style>
        .contact-hero h1 {
  font-size: 42px;
  margin-bottom: 10px;
  color: #ec4899;
}

.contact-hero p {
  max-width: 700px;
  margin: 0 auto;
  font-size: 18px;
  color: #4b5563;
  line-height: 1.6;
}
        .review-container { max-width: 1100px; margin: 50px auto; padding: 0 20px; display: grid; grid-template-columns: 1fr 1.5fr; gap: 40px; }
        .review-form-box { background: #fff; padding: 25px; border-radius: 15px; box-shadow: 0 8px 20px rgba(0,0,0,0.06); border-top: 5px solid #ec4899; height: fit-content; }
        .review-form-box h3 { color: #ec4899; margin-bottom: 20px; }
        .review-form-box input, .review-form-box textarea, .review-form-box select { 
            width: 100%; padding: 12px; margin-bottom: 15px; border: 1px solid #ddd; border-radius: 8px; box-sizing: border-box; font-family: inherit;
        }

        .review-card { background: #fff; padding: 20px; border-radius: 15px; box-shadow: 0 5px 15px rgba(0,0,0,0.05); border-left: 6px solid #ec4899; margin-bottom: 25px; position: relative; }
        .stars { color: #fbbf24; font-size: 18px; margin-bottom: 5px; }
        
        .img-holder { width: 150px; height: 110px; margin-top: 10px; border-radius: 8px; overflow: hidden; border: 1px solid #fce7f3; }
        .review-photo { width: 100%; height: 100%; object-fit: cover; }
        
        .user-delete-btn {
            position: absolute; top: 15px; right: 15px; color: #ff4d4d; text-decoration: none; font-size: 11px; font-weight: bold; border: 1px solid #ff4d4d; padding: 3px 8px; border-radius: 5px;
        }

        @media (max-width: 850px) { .review-container { grid-template-columns: 1fr; } }
    </style>
</head>
<body>


<section class="contact-hero">
    <div class>
     <h1>Customer Reviews</h1>
    <p>Read what our happy couples have to say about their special day.</p>
    </div>
  </section>

<div class="review-container">
    <div class="review-left">
        <div class="review-form-box">
            <h3>Share Your Day</h3>
            <form method="POST" enctype="multipart/form-data">
                <label>How was your experience?</label>
                <select name="rating" required>
                    <option value="5">⭐⭐⭐⭐⭐ (Excellent)</option>
                    <option value="4">⭐⭐⭐⭐ (Very Good)</option>
                    <option value="3">⭐⭐⭐ (Average)</option>
                    <option value="2">⭐⭐ (Fair)</option>
                    <option value="1">⭐ (Poor)</option>
                </select>

                <label>Your Feedback</label>
                <textarea name="comment" rows="4" placeholder="Describe your experience with us..." required></textarea>
                
                <label>Upload a Photo</label>
                <input type="file" name="review_image" accept="image/*">
                
                <button type="submit" style="width:100%; background:#ec4899; color:white; border:none; padding:14px; border-radius:30px; cursor:pointer; font-weight:bold; font-size:16px;">Submit Review</button>
            </form>
        </div>
    </div>

    <div class="review-right">
        <h2 style="margin-bottom:25px; color:#333;">What our clients say</h2>
        <?php while($row = mysqli_fetch_assoc($all_reviews)): ?>
            <div class="review-card">
                <?php if(isset($_SESSION['user_id']) && $_SESSION['user_id'] == $row['user_id']): ?>
                    <a href="reviews.php?delete_id=<?php echo $row['id']; ?>" class="user-delete-btn" onclick="return confirm('Delete your review?')">X Delete</a>
                <?php endif; ?>

                <div class="stars">
                    <?php for($i=1; $i<=5; $i++) echo ($i <= $row['rating']) ? "★" : "☆"; ?>
                </div>
                <span style="font-weight:bold; color: #333;"><?php echo htmlspecialchars($row['user_name']); ?></span>
                <p style="color: #555; margin: 10px 0; font-size: 15px;"><?php echo htmlspecialchars($row['comment']); ?></p>
                
                <?php if(!empty($row['image'])): ?>
                    <div class="img-holder">
                        <img src="../../image/<?php echo $row['image']; ?>" class="review-photo">
                    </div>
                <?php endif; ?>
                
                <span style="font-size:11px; color:#aaa; display:block; margin-top:12px;">Posted on: <?php echo date('d M, Y', strtotime($row['created_at'])); ?></span>
            </div>
        <?php endwhile; ?>
    </div>
</div>

<?php include('../navbar/footer.php'); ?>
</body>
</html>
<?php
session_start();
include("../admin/db_connect.php");

// ✅ Only Admin Access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}

$message = "";

// --- INSERT STAFF ASSIGNMENT LOGIC ---
if(isset($_POST['assign'])){
    $event_type = mysqli_real_escape_string($conn, $_POST['event_type']);
    $staff_id = mysqli_real_escape_string($conn, $_POST['staff_id']);
    $duty = mysqli_real_escape_string($conn, $_POST['duty']);

    // Insert into event_staff table
    $query = "INSERT INTO event_staff (event_type, staff_id, duty) VALUES ('$event_type', '$staff_id', '$duty')";
    
    if(mysqli_query($conn, $query)){
        $message = "<div class='alert success'>Staff Assigned Successfully!</div>";
    } else {
        $message = "<div class='alert error'>Error: " . mysqli_error($conn) . "</div>";
    }
}

// Fetch all staff members for the dropdown
$staff_result = mysqli_query($conn, "SELECT id, name, role FROM staff ORDER BY name ASC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Assign Staff Duty - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root { --accent: #ec4899; --bg: #f8fafc; }
        body { font-family: 'Segoe UI', Arial; background: var(--bg); margin: 0; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .assign-box { background: white; padding: 40px; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.05); width: 100%; max-width: 450px; border: 1px solid #f1f5f9; }
        h2 { text-align: center; color: #1e293b; margin-bottom: 30px; font-size: 24px; }
        .form-group { margin-bottom: 20px; }
        label { display: block; margin-bottom: 8px; font-weight: 600; color: #475569; font-size: 14px; }
        select, input { width: 100%; padding: 12px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-size: 15px; outline: none; transition: 0.3s; box-sizing: border-box; }
        select:focus, input:focus { border-color: var(--accent); }
        .btn-assign { width: 100%; background: var(--accent); color: white; padding: 13px; border: none; border-radius: 8px; font-size: 16px; font-weight: 700; cursor: pointer; transition: 0.3s; margin-top: 10px; }
        .btn-assign:hover { opacity: 0.9; transform: translateY(-2px); }
        .alert { padding: 12px; border-radius: 8px; margin-bottom: 20px; text-align: center; font-size: 14px; }
        .success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
        .error { background: #fef2f2; color: #b91c1c; border: 1px solid #fee2e2; }
        .back-link { display: block; text-align: center; margin-top: 20px; text-decoration: none; color: #64748b; font-size: 14px; }
    </style>
</head>
<body>

<div class="assign-box">
    <h2><i class="fas fa-tasks" style="color: var(--accent);"></i> Assign Staff Duty</h2>
    
    <?php echo $message; ?>

    <form method="POST">
        <div class="form-group">
            <label>Select Event Type</label>
            <select name="event_type" required>
                <option value="" disabled selected>Choose Event</option>
                <option value="haldi">Haldi Event</option>
                <option value="mehndi">Mehndi Event</option>
                <option value="sangeet">Sangeet Night</option>
                <option value="wedding">Wedding Ceremony</option>
                <option value="reception">Reception Party</option>
                <option value="mandap">Mandap Muhurat</option>
            </select>
        </div>

        <div class="form-group">
            <label>Select Staff Member</label>
            <select name="staff_id" required>
                <option value="" disabled selected>Choose Staff</option>
                <?php while($staff = mysqli_fetch_assoc($staff_result)) { ?>
                    <option value="<?= $staff['id']; ?>">
                        <?= htmlspecialchars($staff['name']); ?> (<?= htmlspecialchars($staff['role']); ?>)
                    </option>
                <?php } ?>
            </select>
        </div>

        <div class="form-group">
            <label>Assign Specific Duty</label>
            <input type="text" name="duty" placeholder="e.g. Stage Decor, Food Manager" required>
        </div>

        <button type="submit" name="assign" class="btn-assign">Assign Staff Now</button>
        
        <a href="dashboard.php" class="back-link"><i class="fas fa-arrow-left"></i> Back to Dashboard</a>
    </form>
</div>

</body>
</html>
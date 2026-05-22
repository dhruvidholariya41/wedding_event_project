<?php
session_start();
include("../admin/db_connect.php");

// ✅ Only Admin Access
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}

// --- ADD NEW STAFF LOGIC ---
if(isset($_POST['add_staff'])){
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $role = mysqli_real_escape_string($conn, $_POST['role']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);

    $insert_query = "INSERT INTO staff (name, role, phone) VALUES ('$name', '$role', '$phone')";
    if(mysqli_query($conn, $insert_query)){
        echo "<script>alert('New staff added successfully!'); window.location='admin_manage_staff.php';</script>";
    }
}

// --- DELETE STAFF ASSIGNMENT LOGIC ---
if(isset($_GET['delete_assign_id'])){
    $assign_id = $_GET['delete_assign_id'];
    mysqli_query($conn, "DELETE FROM event_staff WHERE id = '$assign_id'");
    echo "<script>alert('Assignment removed!'); window.location='admin_manage_staff.php';</script>";
}

// --- DELETE MAIN STAFF LOGIC ---
if(isset($_GET['delete_id'])){
    $id = $_GET['delete_id'];
    mysqli_query($conn, "DELETE FROM staff WHERE id = '$id'");
    echo "<script>alert('Staff removed successfully'); window.location='admin_manage_staff.php';</script>";
}

// Fetch all staff members
$staff_list = mysqli_query($conn, "SELECT * FROM staff ORDER BY id DESC");

// Fetch assigned duties (JOIN Query)
$assigned_sql = "SELECT es.id as assign_id, s.name, es.event_type, es.duty 
                 FROM event_staff es 
                 JOIN staff s ON es.staff_id = s.id 
                 ORDER BY es.id DESC";
$assigned_list = mysqli_query($conn, $assigned_sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Staff & Duties - Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Segoe UI', Arial; background: #f8fafc; margin: 10px; }
        .container { background: white; padding: 25px; border-radius: 12px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); max-width: 1250px; margin: auto; }
        h2 { color: #1e293b; margin: 15px 0 10px; border-bottom: 2px solid #ec4899; display: inline-block; padding-bottom: 3px; font-size: 20px; }
        
        .add-staff-form { background: #f1f5f9; padding: 15px; border-radius: 8px; margin-bottom: 20px; display: flex; gap: 10px; align-items: flex-end; flex-wrap: wrap; }
        .form-group { display: flex; flex-direction: column; gap: 5px; }
        .form-group input { padding: 8px; border: 1px solid #cbd5e1; border-radius: 6px; }
        .btn-submit { background: #ec4899; color: white; padding: 8px 18px; border: none; border-radius: 6px; cursor: pointer; font-weight: bold; }

        /* Side-by-Side Layout Style */
        .tables-wrapper { display: flex; gap: 20px; align-items: flex-start; }
        .table-section { flex: 1; min-width: 450px; background: #fff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 15px; }

        table { width: 100%; border-collapse: collapse; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #f1f5f9; font-size: 14px; }
        th { background: #f8fafc; color: #64748b; font-size: 12px; text-transform: uppercase; }
        .btn-delete { color: #ef4444; text-decoration: none; }
        .badge { padding: 3px 8px; border-radius: 15px; font-size: 11px; font-weight: 600; background: #e0e7ff; color: #4338ca; }
    </style>
</head>
<body>

<div class="container">
    <div style="display: flex; justify-content: space-between; align-items: center;">
        <a href="dashboard.php" style="text-decoration: none; color: #64748b;"><i class="fas fa-arrow-left"></i> Dashboard</a>
        <a href="admin_assign_staff.php" style="background: #ec4899; color: white; padding: 8px 15px; text-decoration: none; border-radius: 6px; font-size: 13px;">Assign New Duty</a>
    </div>

    <h2><i class="fas fa-user-plus"></i> Add Staff</h2>
    <form method="POST" class="add-staff-form">
        <div class="form-group"><input type="text" name="name" placeholder="Name" required></div>
        <div class="form-group"><input type="text" name="role" placeholder="Role (e.g. DJ)" required></div>
        <div class="form-group"><input type="text" name="phone" placeholder="Phone" required></div>
        <button type="submit" name="add_staff" class="btn-submit">Add</button>
    </form>

    <div class="tables-wrapper">
        
        <div class="table-section">
            <h2 style="font-size: 18px;"><i class="fas fa-user-tie"></i> Registered Staff</h2>
            <table>
                <thead><tr><th>Name</th><th>Role</th><th>Action</th></tr></thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($staff_list)) { ?>
                    <tr>
                        <td><b><?= htmlspecialchars($row['name']); ?></b><br><small><?= htmlspecialchars($row['phone']); ?></small></td>
                        <td><?= htmlspecialchars($row['role']); ?></td>
                        <td><a href="admin_manage_staff.php?delete_id=<?= $row['id']; ?>" class="btn-delete" onclick="return confirm('Delete Staff?')"><i class="fas fa-trash"></i></a></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>

        <div class="table-section">
            <h2 style="font-size: 18px; border-color: #ec4899;"><i class="fas fa-clipboard-list"></i> Current Assignments</h2>
            <table>
                <thead><tr><th>Staff</th><th>Event</th><th>Duty</th><th>X</th></tr></thead>
                <tbody>
                    <?php if(mysqli_num_rows($assigned_list) > 0) { 
                        while($assign = mysqli_fetch_assoc($assigned_list)) { ?>
                        <tr>
                            <td><b><?= htmlspecialchars($assign['name']); ?></b></td>
                            <td><span class="badge"><?= ucfirst(htmlspecialchars($assign['event_type'])); ?></span></td>
                            <td><?= htmlspecialchars($assign['duty']); ?></td>
                            <td><a href="admin_manage_staff.php?delete_assign_id=<?= $assign['assign_id']; ?>" class="btn-delete" title="Remove Assignment"><i class="fas fa-times-circle"></i></a></td>
                        </tr>
                    <?php } } else { echo "<tr><td colspan='4' style='text-align:center;'>No active duties.</td></tr>"; } ?>
                </tbody>
            </table>
        </div>

    </div> </div>

</body>
</html>
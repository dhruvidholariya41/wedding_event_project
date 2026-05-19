<?php
session_start();
include("../admin/db_connect.php");

// Only Admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}

/* ================= APPROVE ================= */

if(isset($_GET['approve'])){

$id = $_GET['approve'];

$booking = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM sangeet_bookings WHERE id='$id'"));

mysqli_query($conn,"UPDATE sangeet_bookings SET status='Approved' WHERE id='$id'");

/* OLD STAFF DELETE */
mysqli_query($conn,"DELETE FROM event_staff WHERE booking_id='$id'");

/* DJ */
if($booking['dj_type'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','sangeet','2','DJ')");
}

/* STAGE */
if($booking['stage_theme'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','sangeet','4','Stage Decoration')");
}

/* LIGHT */
if($booking['light_theme'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','sangeet','4','Lighting')");
}

/* ENTRY */
if($booking['entry_theme'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','sangeet','4','Entry Decoration')");
}

/* PHOTO */
if($booking['photo_package'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','sangeet','3','Photography')");
}

/* FOOD */
if($booking['food_type'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','sangeet','5','Food Management')");
}

header("Location: admin_sangeet.php");
exit();
}

/* ================= PENDING ================= */

if(isset($_GET['pending'])){
$id = intval($_GET['pending']);
mysqli_query($conn,"UPDATE sangeet_bookings SET status='Pending' WHERE id=$id");
header("Location: admin_sangeet.php");
exit();
}

/* ================= DELETE ================= */

if(isset($_GET['delete'])){
$id = intval($_GET['delete']);
mysqli_query($conn,"DELETE FROM sangeet_bookings WHERE id=$id");
header("Location: admin_sangeet.php");
exit();
}

$result = mysqli_query($conn,"SELECT * FROM sangeet_bookings ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Sangeet Bookings</title>

<style>

body{
font-family: Arial;
background:#f4f6f9;
padding:15px;
}

h2{
margin-bottom:15px;
font-size:20px;
}

table{
width:100%;
border-collapse:collapse;
background:#ffffff;
font-size:12px;
box-shadow:0 3px 8px rgba(0,0,0,0.08);
}

th, td{
padding:6px 8px;
border:1px solid #e5e7eb;
text-align:center;
}

th{
background:#1f2937;
color:#fff;
}

tr:nth-child(even){
background:#f9fafb;
}

.staff-table{
width:60%;
margin:auto;
margin-top:10px;
border-collapse:collapse;
background:#fafafa;
}

.staff-table th,
.staff-table td{
border:1px solid #ccc;
padding:5px;
font-size:12px;
}

.staff-table th{
background:#555;
color:white;
}

button{
padding:4px 8px;
border:none;
border-radius:4px;
cursor:pointer;
}

.approve{background:#16a34a;color:white;}
.pending{background:#f59e0b;color:white;}
.delete{background:#dc2626;color:white;}

</style>

</head>

<body>

<h2>Sangeet Bookings Management</h2>

<br>

<a href="dashboard.php">
<button style="
background:#007bff;
color:white;
padding:8px 15px;
border:none;
border-radius:5px;
cursor:pointer;">
⬅ Back to Dashboard
</button>
</a>

<br><br>

<table>

<tr>

<th>ID</th>
<th>User</th>
<th>Date</th>
<th>Time</th>
<th>Address</th>

<th>DJ Required</th>
<th>DJ Type</th>
<th>DJ Total</th>

<th>Stage Required</th>
<th>Stage Theme</th>
<th>Stage Total</th>

<th>Lighting Required</th>
<th>Lighting Theme</th>
<th>Lighting Total</th>

<th>Entry Required</th>
<th>Entry Theme</th>
<th>Entry Total</th>

<th>Photo Required</th>
<th>Photo Package</th>
<th>Photo Total</th>

<th>Food Required</th>
<th>Food Type</th>
<th>Guests</th>
<th>Food Total</th>

<th>Total Amount</th>
<th>Payable Amount</th>

<th>Payment Method</th>
<th>Payment Option</th>

<th>Status</th>
<th>Actions</th>


</tr>

<?php
if(mysqli_num_rows($result)>0){

while($row = mysqli_fetch_assoc($result)){
?>

<tr>

<td><?php echo $row['id']; ?></td>
<td><?php echo $row['username']; ?></td>
<td><?php echo $row['event_date']; ?></td>
<td><?php echo $row['event_time']; ?></td>

<td><?php echo $row['event_address']; ?></td>

<td><?php echo $row['dj_required']; ?></td>
<td><?php echo $row['dj_type']; ?></td>
<td>₹<?php echo $row['dj_total']; ?></td>

<td><?php echo $row['stage_required']; ?></td>
<td><?php echo $row['stage_theme']; ?></td>
<td>₹<?php echo $row['stage_total']; ?></td>

<td><?php echo $row['lighting_required']; ?></td>
<td><?php echo $row['light_theme']; ?></td>
<td>₹<?php echo $row['light_total']; ?></td>

<td><?php echo $row['entry_required']; ?></td>
<td><?php echo $row['entry_theme']; ?></td>
<td>₹<?php echo $row['entry_total']; ?></td>

<td><?php echo $row['photo_required']; ?></td>
<td><?php echo $row['photo_package']; ?></td>
<td>₹<?php echo $row['photo_total']; ?></td>

<td><?php echo $row['food_required']; ?></td>
<td><?php echo $row['food_type']; ?></td>
<td><?php echo $row['guest_count']; ?></td>
<td>₹<?php echo $row['food_total']; ?></td>

<td>₹<?php echo $row['total_amount']; ?></td>
<td>₹<?php echo $row['payable_amount']; ?></td>

<td><?php echo $row['payment_method']; ?></td>
<td><?php echo $row['payment_option']; ?></td>

<td><?php echo $row['status'] ?? 'Pending'; ?></td>

<td>

<a href="?approve=<?php echo $row['id']; ?>">
<button class="approve">Approve</button>
</a>

<a href="?pending=<?php echo $row['id']; ?>">
<button class="pending">Pending</button>
</a>

<a href="?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete booking?')">
<button class="delete">Delete</button>
</a>

</td>

</tr>

<!-- STAFF TABLE MIDDLE -->

<tr>
<td colspan="32">

<b>Assigned Staff</b>

<table class="staff-table">

<tr>
<th>Booking ID</th>
<th>Staff Name</th>
<th>Duty</th>
</tr>

<?php

$bid = $row['id'];

$staff_sql = "SELECT DISTINCT e.booking_id,s.name,e.duty
FROM event_staff e
JOIN staff s ON e.staff_id = s.id
WHERE e.booking_id='$bid'
AND e.event_type='sangeet'";

$staff_result = mysqli_query($conn,$staff_sql);

if(mysqli_num_rows($staff_result)>0){

while($staff=mysqli_fetch_assoc($staff_result)){
?>

<tr>

<td><?php echo $staff['booking_id']; ?></td>
<td><?php echo $staff['name']; ?></td>
<td><?php echo $staff['duty']; ?></td>

</tr>

<?php
}
}else{
?>

<tr>
<td colspan="3">No Staff Assigned</td>
</tr>

<?php } ?>

</table>

</td>
</tr>

<?php
}

}else{

echo "<tr><td colspan='32'>No Bookings Found</td></tr>";

}
?>

</table>

<br>



</body>
</html>
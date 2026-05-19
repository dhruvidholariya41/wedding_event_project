<?php
session_start();
include("db_connect.php");

/* ================= APPROVE ================= */

if(isset($_GET['approve'])){

$id = $_GET['approve'];

/* booking data */
$booking = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM reception_bookings WHERE id='$id'"));

/* approve booking */
mysqli_query($conn,"UPDATE reception_bookings SET status='Approved' WHERE id='$id'");

/* delete old staff */
mysqli_query($conn,"DELETE FROM event_staff WHERE booking_id='$id' AND event_type='reception'");

/* DECORATION */
if($booking['decoration_theme'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','reception','4','Decoration')");
}

/* ENTRY PROPS */
if($booking['entry_props'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','reception','4','Entry Props')");
}

/* STAGE */
if($booking['stage_theme'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','reception','4','Stage Decoration')");
}

/* DJ */
if($booking['dj_type'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','reception','2','DJ')");
}

/* SEATING */
if($booking['seating_type'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','reception','4','Seating Arrangement')");
}

/* PHOTO */
if($booking['photo_package'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','reception','3','Photography')");
}

/* FOOD */
if($booking['food_type'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','reception','5','Food Management')");
}

header("Location: admin_reception.php");
exit();
}

/* ================= PENDING ================= */

if(isset($_GET['pending'])){
$id=$_GET['pending'];
mysqli_query($conn,"UPDATE reception_bookings SET status='Pending' WHERE id='$id'");
header("Location: admin_reception.php");
exit();
}

/* ================= DELETE ================= */

if(isset($_GET['delete'])){
$id=$_GET['delete'];
mysqli_query($conn,"DELETE FROM reception_bookings WHERE id='$id'");
header("Location: admin_reception.php");
exit();
}

/* ================= FETCH BOOKINGS ================= */

$result = mysqli_query($conn,"SELECT * FROM reception_bookings ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>

<title>Reception Bookings</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:20px;
}

table{
width:100%;
border-collapse:collapse;
background:white;
}

th,td{
padding:8px;
border:1px solid #ddd;
text-align:center;
font-size:13px;
}

th{
background:#333;
color:white;
}

button{
padding:5px 10px;
border:none;
border-radius:4px;
cursor:pointer;
color:white;
font-size:12px;
}

.approve{background:green;}
.pending{background:orange;}
.delete{background:red;}

.staff-table{
width:60%;
margin:auto;
margin-top:5px;
border-collapse:collapse;
background:#f9f9f9;
}

.staff-table th{
background:#2c3e50;
color:white;
font-size:12px;
padding:6px;
}

.staff-table td{
padding:6px;
font-size:12px;
border:1px solid #ddd;
}

</style>

</head>

<body>

<h2>Reception Bookings Management</h2>


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
<tr>

<th>ID</th>
<th>User</th>
<th>Date</th>
<th>Time</th>
<th>Address</th>

<th>Decoration Required</th>
<th>Decoration Theme</th>
<th>Decoration Total</th>

<th>Props Required</th>
<th>Entry Props</th>
<th>Props Total</th>

<th>Stage Required</th>
<th>Stage Theme</th>
<th>Stage Total</th>

<th>DJ Required</th>
<th>DJ Type</th>
<th>DJ Total</th>

<th>Seating Required</th>
<th>Seating Type</th>
<th>Guests</th>
<th>Seating Total</th>

<th>Photography Required</th>
<th>Photo Package</th>
<th>Photo Total</th>

<th>Food Required</th>
<th>Food Type</th>
<th>Guest Count</th>
<th>Food Total</th>

<th>Total</th>
<th>Payable</th>
<th>Payment Method</th>
<th>Payment Option</th>

<th>Status</th>
<th>Action</th>

</tr>
</tr>

<?php
while($row=mysqli_fetch_assoc($result)){
?>

<tr>

<td><?php echo $row['id']; ?></td>
<td><?php echo $row['username']; ?></td>
<td><?php echo $row['event_date']; ?></td>
<td><?php echo $row['event_time']; ?></td>
<td><?php echo $row['event_address']; ?></td>

<td><?php echo $row['decoration_required']; ?></td>
<td><?php echo $row['decoration_theme']; ?></td>
<td>₹<?php echo $row['decoration_total']; ?></td>

<td><?php echo $row['props_required']; ?></td>
<td><?php echo $row['entry_props']; ?></td>
<td>₹<?php echo $row['props_total']; ?></td>

<td><?php echo $row['stage_required']; ?></td>
<td><?php echo $row['stage_theme']; ?></td>
<td>₹<?php echo $row['stage_total']; ?></td>

<td><?php echo $row['dj_required']; ?></td>
<td><?php echo $row['dj_type']; ?></td>
<td>₹<?php echo $row['dj_total']; ?></td>

<td><?php echo $row['seating_required']; ?></td>
<td><?php echo $row['seating_type']; ?></td>
<td><?php echo $row['guest_number']; ?></td>
<td>₹<?php echo $row['seating_total']; ?></td>

<td><?php echo $row['photography_required']; ?></td>
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

<td><?php echo isset($row['status']) ? $row['status'] : 'Pending'; ?></td>
<td>

<a href="admin_reception.php?approve=<?php echo $row['id']; ?>">
<button class="approve">Approve</button>
</a>

<a href="admin_reception.php?pending=<?php echo $row['id']; ?>">
<button class="pending">Pending</button>
</a>

<a href="admin_reception.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete booking?')">
<button class="delete">Delete</button>
</a>

</td>

</tr>

<!-- STAFF TABLE -->

<tr>
<td colspan="35">

<table class="staff-table">

<tr>
<th>User ID</th>
<th>Staff Name</th>
<th>Duty</th>
</tr>

<?php

$bid=$row['id'];

$staff_sql="SELECT e.booking_id,s.name,e.duty
FROM event_staff e
JOIN staff s ON e.staff_id=s.id
WHERE e.booking_id='$bid' AND e.event_type='reception'";

$staff_result=mysqli_query($conn,$staff_sql);

if($staff_result && mysqli_num_rows($staff_result)>0){
while($staff=mysqli_fetch_assoc($staff_result)){
?>

<tr>
<td><?php echo $staff['booking_id']; ?></td>
<td><?php echo $staff['name']; ?></td>
<td><?php echo $staff['duty']; ?></td>
</tr>

<?php } } ?>

</table>

</td>
</tr>

<?php } ?>

</table>

<br>



</body>
</html>
<?php
session_start();
include("../admin/db_connect.php");

/* ONLY ADMIN */
if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
header("Location: ../pages/login.php");
exit();
}

/* ================= APPROVE ================= */

if(isset($_GET['approve'])){

$id = $_GET['approve'];

/* GET BOOKING */
$booking = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM wedding_bookings WHERE id='$id'"));

/* UPDATE STATUS */
mysqli_query($conn,"UPDATE wedding_bookings SET status='Approved' WHERE id='$id'");

/* DELETE OLD STAFF */
mysqli_query($conn,"DELETE FROM event_staff WHERE booking_id='$id'");

/* DECORATION */
if($booking['decoration_theme']!=""){
mysqli_query($conn,"INSERT INTO event_staff (booking_id,staff_id,duty,event_type)
VALUES('$id','1','Decoration','wedding')");
}

/* BRIDE ENTRY */
if($booking['bride_entry']!=""){
mysqli_query($conn,"INSERT INTO event_staff (booking_id,staff_id,duty,event_type)
VALUES('$id','2','Bride Entry','wedding')");
}

/* GROOM ENTRY */
if($booking['groom_entry']!=""){
mysqli_query($conn,"INSERT INTO event_staff (booking_id,staff_id,duty,event_type)
VALUES('$id','3','Groom Entry','wedding')");
}

/* VARMALA */
if($booking['varmala_stage']!=""){
mysqli_query($conn,"INSERT INTO event_staff (booking_id,staff_id,duty,event_type)
VALUES('$id','4','Varmala Stage','wedding')");
}

/* DJ */
if($booking['dj_type']!=""){
mysqli_query($conn,"INSERT INTO event_staff (booking_id,staff_id,duty,event_type)
VALUES('$id','5','DJ','wedding')");
}

/* SEATING */
if($booking['seating_type']!=""){
mysqli_query($conn,"INSERT INTO event_staff (booking_id,staff_id,duty,event_type)
VALUES('$id','6','Seating Arrangement','wedding')");
}

/* PHOTOGRAPHY */
if($booking['photo_package']!=""){
mysqli_query($conn,"INSERT INTO event_staff (booking_id,staff_id,duty,event_type)
VALUES('$id','7','Photography','wedding')");
}

/* FOOD */
if($booking['food_type']!=""){
mysqli_query($conn,"INSERT INTO event_staff (booking_id,staff_id,duty,event_type)
VALUES('$id','8','Food Management','wedding')");
}

header("Location: admin_wedding.php");
exit();
}
/* ================= PENDING ================= */

if(isset($_GET['pending'])){
$id=$_GET['pending'];
mysqli_query($conn,"UPDATE wedding_bookings SET status='Pending' WHERE id='$id'");
header("Location: admin_wedding.php");
exit();
}

/* ================= DELETE ================= */

if(isset($_GET['delete'])){

$id=$_GET['delete'];

mysqli_query($conn,"DELETE FROM wedding_bookings WHERE id='$id'");
mysqli_query($conn,"DELETE FROM event_staff WHERE booking_id='$id'");

header("Location: admin_wedding.php");
exit();
}

/* ================= FETCH BOOKINGS ================= */

$result = mysqli_query($conn,"SELECT * FROM wedding_bookings ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>

<title>Wedding Bookings</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:15px;
}

h2{
margin-bottom:15px;
}

table{
width:100%;
border-collapse:collapse;
background:white;
font-size:12px;
}

th,td{
padding:6px;
border:1px solid #ddd;
text-align:center;
}

th{
background:#1f2937;
color:white;
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
}

.staff-table th{
background:#555;
color:white;
}

button{
padding:5px 8px;
border:none;
border-radius:4px;
cursor:pointer;
color:white;
}

.approve{background:#16a34a;}
.pending{background:#f59e0b;}
.delete{background:#dc2626;}

</style>

</head>

<body>

<h2>Wedding Bookings Management</h2>

<a href="dashboard.php">
<button style="background:#007bff;">⬅ Back to Dashboard</button>
</a>

<br><br>

<table>

<tr>

<th>ID</th>
<th>User</th>
<th>Date</th>
<th>Time</th>
<th>Address</th>

<th>Decoration Required</th>
<th>Decoration</th>
<th>Decoration Total</th>

<th>Bride Entry Required</th>
<th>Bride Entry</th>
<th>Bride Total</th>

<th>Groom Entry Required</th>
<th>Groom Entry</th>
<th>Groom Total</th>

<th>Varmala Required</th>
<th>Varmala</th>
<th>Varmala Total</th>

<th>Props Required</th>
<th>Entry Props</th>
<th>Props Total</th>

<th>DJ Required</th>
<th>DJ</th>
<th>DJ Total</th>

<th>Seating Required</th>
<th>Seating</th>
<th>Guests</th>
<th>Seating Total</th>

<th>Photography Required</th>
<th>Photography</th>
<th>Photo Total</th>

<th>Food Required</th>
<th>Food</th>
<th>Food Guests</th>
<th>Food Total</th>

<th>Total Amount</th>
<th>Payment Method</th>
<th>Payment Option</th>
<th>Payable Amount</th>

<th>Status</th>
<th>Actions</th>

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
<td><?php echo $row['decoration_required'] ?? 'No'; ?></td>
<td><?php echo $row['decoration_theme'] ?? 'No'; ?></td>
<td>₹<?php echo $row['decoration_total'] ?? 0; ?></td>


<td><?php echo $row['bride_entry_required'] ?? 'No'; ?></td>
<td><?php echo $row['bride_entry'] ?? 'No'; ?></td>
<td>₹<?php echo $row['bride_total'] ?? 0; ?></td>

<td><?php echo $row['groom_entry_required'] ?? 'No'; ?></td>
<td><?php echo $row['groom_entry'] ?? 'No'; ?></td>
<td>₹<?php echo $row['groom_total'] ?? 0; ?></td>

<td><?php echo $row['varmala_required'] ?? 'No'; ?></td>
<td><?php echo $row['varmala_stage'] ?? 'No'; ?></td>
<td>₹<?php echo $row['varmala_total'] ?? 0; ?></td>

<td><?php echo $row['props_required'] ?? 'No'; ?></td>
<td><?php echo $row['entry_props'] ?? 'No'; ?></td>
<td>₹<?php echo $row['props_total'] ?? 0; ?></td>

<td><?php echo $row['dj_required'] ?? 'No'; ?></td>
<td><?php echo $row['dj_type'] ?? 'No'; ?></td>
<td>₹<?php echo $row['dj_total'] ?? 0; ?></td>

<td><?php echo $row['seating_required'] ?? 'No'; ?></td>
<td><?php echo $row['seating_type'] ?? 'No'; ?></td>
<td><?php echo $row['guest_number'] ?? 0; ?></td>
<td>₹<?php echo $row['seating_total'] ?? 0; ?></td>
<td><?php echo $row['photo_required'] ?? 'No'; ?></td>
<td><?php echo $row['photo_package'] ?? 'No'; ?></td>
<td>₹<?php echo $row['photo_total'] ?? 0; ?></td>

<td><?php echo $row['food_required'] ?? 'No'; ?></td>
<td><?php echo $row['food_type'] ?? 'No'; ?></td>
<td><?php echo $row['guest_count'] ?? 0; ?></td>
<td>₹<?php echo $row['food_total'] ?? 0; ?></td>

<td>₹<?php echo $row['total_amount'] ?? 0; ?></td>

<td><?php echo $row['payment_method'] ?? '-'; ?></td>
<td><?php echo $row['payment_option'] ?? '-'; ?></td>
<td>₹<?php echo $row['payable_amount'] ?? 0; ?></td>




<td><?php echo $row['status'] ?? 'Pending'; ?></td>
</tr>
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

<tr>
<td colspan="15">

<b>Assigned Staff</b>

<table class="staff-table">

<tr>
<th>Staff Name</th>
<th>Duty</th>
</tr>

<?php

$bid=$row['id'];

$staff_sql="SELECT s.name,e.duty
FROM event_staff e
JOIN staff s ON e.staff_id=s.id
WHERE e.booking_id='$bid'
AND e.event_type='wedding'";

$staff_result=mysqli_query($conn,$staff_sql);

if(mysqli_num_rows($staff_result)>0){

while($staff=mysqli_fetch_assoc($staff_result)){
?>

<tr>
<td><?php echo $staff['name']; ?></td>
<td><?php echo $staff['duty']; ?></td>
</tr>

<?php
}

}else{
?>

<tr>
<td colspan="2">No Staff Assigned</td>
</tr>

<?php } ?>

</table>

</td>
</tr>

<?php } ?>

</table>

</body>
</html>
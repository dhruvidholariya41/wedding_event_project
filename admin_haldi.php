<?php
session_start();
include("db_connect.php");

if(!isset($_SESSION['role']) || $_SESSION['role']!='admin'){
header("Location: ../pages/login.php");
exit();
}

/* APPROVE BOOKING */

if(isset($_GET['approve'])){

$id = intval($_GET['approve']);

$booking = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM haldi_bookings WHERE id='$id'"));

mysqli_query($conn,"UPDATE haldi_bookings SET status='Approved' WHERE id='$id'");

/* REMOVE OLD STAFF */
mysqli_query($conn,"DELETE FROM event_staff WHERE booking_id='$id' AND event_type='haldi'");

/* MUSIC STAFF */
if(!empty($booking['music_type'])){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','haldi','1','Music')");
}

/* PHOTO STAFF */
if(!empty($booking['photo_package'])){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','haldi','2','Photography')");
}

/* DECOR STAFF */
if(!empty($booking['decoration_theme'])){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','haldi','3','Decoration')");
}

/* ANCHOR STAFF */
if(!empty($booking['anchor_type'])){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','haldi','4','Anchor')");
}

/* FOOD STAFF */
if(!empty($booking['food_type'])){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','haldi','5','Food Management')");
}

header("Location: admin_haldi.php");
exit();
}

/* PENDING */

if(isset($_GET['pending'])){
$id=intval($_GET['pending']);
mysqli_query($conn,"UPDATE haldi_bookings SET status='Pending' WHERE id='$id'");
header("Location: admin_haldi.php");
exit();
}

/* DELETE */

if(isset($_GET['delete'])){
$id=intval($_GET['delete']);
mysqli_query($conn,"DELETE FROM haldi_bookings WHERE id='$id'");
header("Location: admin_haldi.php");
exit();
}

/* FETCH BOOKINGS */

$result=mysqli_query($conn,"SELECT * FROM haldi_bookings ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>



<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:20px;
}

h2{
margin-bottom:20px;
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
background:#1f2937;
color:white;
}

tr:nth-child(even){
background:#f9fafb;
}

button{
padding:5px 10px;
border:none;
border-radius:4px;
cursor:pointer;
font-size:12px;
}

.approve{
background:#16a34a;
color:white;
}

.pending{
background:#f59e0b;
color:white;
}

.delete{
background:#dc2626;
color:white;
}

.staff-table{
width:50%;
margin:auto;
margin-top:10px;
border-collapse:collapse;
}

.staff-table th{
background:#374151;
color:white;
}

.back{
background:#2563eb;
color:white;
padding:8px 15px;
border:none;
border-radius:5px;
cursor:pointer;
}

</style>

</head>

<body>

<h2>Haldi Bookings Management</h2>

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

<th>Music Required</th>
<th>Music Type</th>
<th>Music Total</th>

<th>Photo Required</th>
<th>Photo Package</th>
<th>Photo Total</th>

<th>Decoration Required</th>
<th>Decoration Theme</th>
<th>Decoration Total</th>

<th>Anchor Required</th>
<th>Anchor Type</th>
<th>Anchor Total</th>

<th>Food Required</th>
<th>Food Type</th>
<th>Guest Count</th>
<th>Food Total</th>

<th>Total Amount</th>
<th>Payable Amount</th>

<th>Payment Method</th>
<th>Payment Option</th>

<th>Status</th>
<th>Action</th>
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

<td><?php echo $row['music_required']; ?></td>
<td><?php echo $row['music_type']; ?></td>
<td>₹ <?php echo $row['music_total']; ?></td>

<td><?php echo $row['photo_required']; ?></td>
<td><?php echo $row['photo_package']; ?></td>
<td>₹ <?php echo $row['photo_total']; ?></td>

<td><?php echo $row['decoration_required']; ?></td>
<td><?php echo $row['decoration_theme']; ?></td>
<td>₹ <?php echo $row['decoration_total']; ?></td>

<td><?php echo $row['anchor_required']; ?></td>
<td><?php echo $row['anchor_type']; ?></td>
<td>₹ <?php echo $row['anchor_total']; ?></td>

<td><?php echo $row['food_required']; ?></td>
<td><?php echo $row['food_type']; ?></td>
<td><?php echo $row['guest_count']; ?></td>
<td>₹ <?php echo $row['food_total']; ?></td>

<td>₹ <?php echo $row['total_amount']; ?></td>
<td>₹ <?php echo $row['payable_amount']; ?></td>

<td><?php echo $row['payment_method']; ?></td>
<td><?php echo $row['payment_option']; ?></td>
<td><?php echo $row['status']; ?></td>

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
<td colspan="28">

<b>Assigned Staff</b>

<table class="staff-table">

<tr>
<th>Staff Name</th>
<th>Duty</th>
</tr>

<?php

$bid=$row['id'];

$sql="SELECT staff.name,event_staff.duty
FROM event_staff
JOIN staff ON staff.id=event_staff.staff_id
WHERE event_staff.booking_id='$bid' AND event_staff.event_type='haldi'";

$res=mysqli_query($conn,$sql);

if(mysqli_num_rows($res)>0){

while($staff=mysqli_fetch_assoc($res)){
?>

<tr>
<td><?php echo $staff['name']; ?></td>
<td><?php echo $staff['duty']; ?></td>
</tr>

<?php }

}else{

echo "<tr><td colspan='2'>No Staff Assigned</td></tr>";

}
?>

</table>

</td>
</tr>

<?php } ?>

</table>

<br>



</body>
</html>
<?php
session_start();
include("../admin/db_connect.php");

// Only Admin
if(!isset($_SESSION['role']) || $_SESSION['role'] != 'admin'){
    header("Location: ../pages/login.php");
    exit();
}

// ================= APPROVE =================

if(isset($_GET['approve'])){

$id = intval($_GET['approve']);

// booking data
$booking = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM mehndi_bookings WHERE id='$id'"));

// booking approve
mysqli_query($conn,"UPDATE mehndi_bookings SET status='Approved' WHERE id='$id'");

// delete old staff
mysqli_query($conn,"DELETE FROM event_staff WHERE booking_id='$id' AND event_type='mehndi'");

// MEHNDI ARTIST
if($booking['bride_package'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','mehndi','1','Mehndi Artist')");
}

// MUSIC
if($booking['music_type'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','mehndi','2','Music')");
}

// PHOTO
if($booking['photo_package'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','mehndi','3','Photography')");
}

// DECORATION
if($booking['decoration_theme'] != ""){
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','mehndi','4','Decoration')");
}

// FOOD
mysqli_query($conn,"INSERT INTO event_staff(booking_id,event_type,staff_id,duty)
VALUES('$id','mehndi','5','Food Management')");

header("Location: admin_mehndi.php");
exit();
}


// ================= PENDING =================

if(isset($_GET['pending'])){
$id = intval($_GET['pending']);
mysqli_query($conn,"UPDATE mehndi_bookings SET status='Pending' WHERE id='$id'");
header("Location: admin_mehndi.php");
exit();
}


// ================= DELETE =================

if(isset($_GET['delete'])){
$id = intval($_GET['delete']);
mysqli_query($conn,"DELETE FROM mehndi_bookings WHERE id='$id'");
header("Location: admin_mehndi.php");
exit();
}


// ================= FETCH BOOKINGS =================

$result = mysqli_query($conn,"SELECT * FROM mehndi_bookings ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
<title>Manage Mehndi Bookings</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:15px;
}

h2{
margin-bottom:15px;
}

.staff-btn{
background:#2563eb;
color:white;
padding:6px 10px;
text-decoration:none;
border-radius:4px;
font-size:12px;
}

.staff-btn:hover{
background:#1d4ed8;
}

table{
width:100%;
border-collapse:collapse;
background:#ffffff;
font-size:12px;
box-shadow:0 3px 8px rgba(0,0,0,0.08);
}

th,td{
padding:6px 8px;
border:1px solid #e5e7eb;
text-align:center;
}

th{
background:#1f2937;
color:white;
}

tr:nth-child(even){
background:#f9fafb;
}

button{
padding:3px 6px;
font-size:11px;
border:none;
border-radius:4px;
cursor:pointer;
}

.approve{background:#16a34a;color:white;}
.pending{background:#f59e0b;color:white;}
.delete{background:#dc2626;color:white;}

.staff-table{
width:60%;
margin:auto;
margin-top:5px;
border-collapse:collapse;
background:#f9f9f9;
}

.staff-table th{
background:#374151;
color:white;
}

</style>

</head>

<body>

<h2>Mehndi Bookings Management</h2>

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
<th>Mehndi Required</th>
<th>Mehndi Artist</th>
<th>Contact</th>
<th>Bride Package</th>
<th>Sider Count</th>
<th>Mehndi Total</th>
<th>Music Required</th>
<th>Music Type</th>
<th>Music Total</th>
<th>Photo Required</th>
<th>Photo Package</th>
<th>Photo Total</th>


<th>Decoration Required</th>
<th>Decoration Theme</th>
<th>Decoration Total</th>

<th>Food Required</th>
<th>Food Type</th>
<th>Guest Count</th>
<th>Food Total</th>
<th>Payment Method</th>
<th>Payment Option</th>



<th>Total</th>
<th>Payable</th>

<th>Status</th>
<th>Payment</th>

<th>Actions</th>

</tr>

<?php
if(mysqli_num_rows($result) > 0){

while($row = mysqli_fetch_assoc($result)){
?>

<tr>

<td><?php echo $row['id']; ?></td>
<td><?php echo $row['username']; ?></td>
<td><?php echo $row['event_date']; ?></td>
<td><?php echo $row['event_time']; ?></td>
<td><?php echo $row['event_address']; ?></td>

<td><?php echo $row['mehndi_required']; ?></td>
<td><?php echo $row['mehndi_name']; ?></td>
<td><?php echo $row['mehndi_contact']; ?></td>
<td><?php echo $row['bride_package']; ?></td>
<td><?php echo $row['sider_count']; ?></td>
<td>₹ <?php echo $row['mehndi_total']; ?></td>

<td><?php echo $row['music_required']; ?></td>
<td><?php echo $row['music_type']; ?></td>
<td>₹ <?php echo $row['music_total']; ?></td>

<td><?php echo $row['photo_required']; ?></td>
<td><?php echo $row['photo_package']; ?></td>
<td>₹ <?php echo $row['photo_total']; ?></td>

<td><?php echo $row['decoration_required']; ?></td>
<td><?php echo $row['decoration_theme']; ?></td>
<td>₹ <?php echo $row['decoration_total']; ?></td>


<td><?php echo $row['food_required']; ?></td>
<td><?php echo $row['food_type']; ?></td>
<td><?php echo $row['guest_count']; ?></td>
<td>₹ <?php echo $row['food_total']; ?></td>

<td><?php echo $row['payment_method']; ?></td>
<td><?php echo $row['payment_option']; ?></td>
<td>₹ <?php echo $row['total_amount']; ?></td>
<td>₹ <?php echo $row['payable_amount']; ?></td>






<td><?php echo $row['status']; ?></td>
<td><?php echo $row['payment_status']; ?></td>

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


<!-- STAFF TABLE -->

<tr>
<td colspan="23">

<b>Assigned Staff</b>

<table class="staff-table">

<tr>
<th>Booking ID</th>
<th>Staff Name</th>
<th>Duty</th>
</tr>

<?php

$bid = $row['id'];

$staff_sql = "SELECT e.booking_id, s.name, e.duty
FROM event_staff e
JOIN staff s ON e.staff_id = s.id
WHERE e.booking_id='$bid' AND e.event_type='mehndi'";

$staff_result = mysqli_query($conn,$staff_sql);

if($staff_result && mysqli_num_rows($staff_result)>0){

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

echo "<tr><td colspan='3'>No Staff Assigned</td></tr>";

}
?>

</table>

</td>
</tr>

<?php
}

}else{

echo "<tr><td colspan='30'>No Bookings Found</td></tr>";

}
?>

</table>

<br>



</body>
</html>
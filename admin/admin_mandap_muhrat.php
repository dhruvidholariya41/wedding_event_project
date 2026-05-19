<?php
include("db_connect.php");

/* ================= APPROVE ================= */

if(isset($_GET['approve'])){
$id = $_GET['approve'];
mysqli_query($conn,"UPDATE mandap_muhrat_bookings SET status='Approved' WHERE id='$id'");
header("Location: admin_mandap_muhrat.php");
exit();
}

/* ================= PENDING ================= */

if(isset($_GET['pending'])){
$id = $_GET['pending'];
mysqli_query($conn,"UPDATE mandap_muhrat_bookings SET status='Pending' WHERE id='$id'");
header("Location: admin_mandap_muhrat.php");
exit();
}

/* ================= DELETE ================= */

if(isset($_GET['delete'])){
$id = $_GET['delete'];
mysqli_query($conn,"DELETE FROM mandap_muhrat_bookings WHERE id='$id'");
header("Location: admin_mandap_muhrat.php");
exit();
}

/* ================= FETCH BOOKINGS ================= */

$result = mysqli_query($conn,"SELECT * FROM mandap_muhrat_bookings ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>

<title>Mandap Muhurat Bookings</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:20px;
}

h2{
margin-bottom:20px;
}

/* BOOKING TABLE */

.main-table{
width:100%;
border-collapse:collapse;
background:white;
margin-bottom:10px;
}

.main-table th,
.main-table td{
padding:8px;
border:1px solid #ddd;
text-align:center;
font-size:13px;
}

.main-table th{
background:#333;
color:white;
}

/* STAFF TABLE */

.staff-table{
width:60%;
border-collapse:collapse;
margin:10px auto;
background:#fafafa;
}

.staff-table th,
.staff-table td{
padding:6px;
border:1px solid #ccc;
font-size:12px;
text-align:center;
}

.staff-table th{
background:#555;
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

</style>

</head>

<body>

<h2>Mandap Muhurat Bookings Management</h2>


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

<?php
while($row = mysqli_fetch_assoc($result)){
?>

<table class="main-table">

<tr>

<th>ID</th>
<th>User</th>
<th>Date</th>
<th>Time</th>
<th>Address</th>

<th>Pooja Required</th>
<th>Pooja Package</th>
<th>Pooja Total</th>

<th>Pandit Required</th>
<th>Pandit Name</th>
<th>Pandit Phone</th>
<th>Pandit Total</th>

<th>Decoration Required</th>
<th>Decoration Theme</th>
<th>Decoration Total</th>

<th>Photography Required</th>
<th>Photography Package</th>
<th>Photography Total</th>

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

<tr>

<td><?php echo $row['id']; ?></td>
<td><?php echo $row['username']; ?></td>
<td><?php echo $row['event_date']; ?></td>
<td><?php echo $row['event_time']; ?></td>
<td><?php echo $row['event_address']; ?></td>

<td><?php echo $row['pooja_required']; ?></td>
<td><?php echo $row['pooja_package']; ?></td>
<td>₹<?php echo $row['pooja_total']; ?></td>

<td><?php echo $row['pandit_required']; ?></td>
<td><?php echo $row['pandit_name']; ?></td>
<td><?php echo $row['pandit_phone']; ?></td>
<td>₹<?php echo $row['pandit_total']; ?></td>

<td><?php echo $row['decoration_required']; ?></td>
<td><?php echo $row['decoration_theme']; ?></td>
<td>₹<?php echo $row['decoration_total']; ?></td>

<td><?php echo $row['photography_required']; ?></td>
<td><?php echo $row['photography_package']; ?></td>
<td>₹<?php echo $row['photography_total']; ?></td>

<td><?php echo $row['food_required']; ?></td>
<td><?php echo $row['food_type']; ?></td>
<td><?php echo $row['guest_count']; ?></td>
<td>₹<?php echo $row['food_total']; ?></td>

<td>₹<?php echo $row['total_amount']; ?></td>
<td>₹<?php echo $row['payable_amount']; ?></td>

<td><?php echo $row['payment_method']; ?></td>
<td><?php echo $row['payment_option']; ?></td>
<td><?php echo $row['status']; ?></td>

<td>

<a href="admin_mandap_muhrat.php?approve=<?php echo $row['id']; ?>">
<button class="approve">Approve</button>
</a>

<a href="admin_mandap_muhrat.php?pending=<?php echo $row['id']; ?>">
<button class="pending">Pending</button>
</a>

<a href="admin_mandap_muhrat.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete booking?')">
<button class="delete">Delete</button>
</a>

</td>

</tr>

</table>

<!-- STAFF TABLE -->

<center><b>Assigned Staff</b></center>

<table class="staff-table">

<tr>
<th>Booking ID</th>
<th>Staff Name</th>
<th>Duty</th>
</tr>

<?php

$bid = $row['id'];

/* FILTER STAFF */

$staff_sql = "SELECT DISTINCT e.booking_id, s.name, e.duty
FROM event_staff e
JOIN staff s ON e.staff_id = s.id
WHERE e.booking_id='$bid'
AND e.duty NOT LIKE '%Mehndi%'";

$staff_result = mysqli_query($conn,$staff_sql);

$music_shown = false;

if($staff_result && mysqli_num_rows($staff_result) > 0){

while($staff = mysqli_fetch_assoc($staff_result)){

$duty = $staff['duty'];

/* DJ / MUSIC ONLY ONE */

if($duty == "DJ" || $duty == "Music"){

if($music_shown){
continue;
}

$duty = "Music";
$music_shown = true;

}

?>

<tr>

<td><?php echo $staff['booking_id']; ?></td>
<td><?php echo $staff['name']; ?></td>
<td><?php echo $duty; ?></td>

</tr>

<?php
}
}
else{
?>

<tr>
<td colspan="3">No Staff Assigned</td>
</tr>

<?php } ?>

</table>

<br>

<?php } ?>

<br>


</body>
</html>


<!-- <td>
<?php echo $row['food_type']; ?>
<br>
Guests: <?php echo $row['guest_count']; ?>
</td> -->
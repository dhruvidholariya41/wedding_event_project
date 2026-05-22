<?php
include("db_connect.php");

/* ========= DELETE USER ========= */

if(isset($_GET['delete'])){
$id = $_GET['delete'];

mysqli_query($conn,"DELETE FROM users WHERE id='$id'");

header("Location: admin_users.php");
exit();
}

/* ========= FETCH USERS ========= */

$result = mysqli_query($conn,"SELECT * FROM users ORDER BY id DESC");

?>

<!DOCTYPE html>
<html>
<head>

<title>User Management</title>

<style>

body{
font-family:Arial;
background:#f4f6f9;
padding:20px;
}

h2{
margin-bottom:10px;
}

table{
width:100%;
border-collapse:collapse;
background:white;
}

th,td{
padding:10px;
border:1px solid #ddd;
text-align:center;
font-size:14px;
}

th{
background:#333;
color:white;
}

button{
padding:5px 10px;
border:none;
border-radius:4px;
color:white;
cursor:pointer;
font-size:12px;
}

.delete{
background:red;
}

.back{
background:#2563eb;
padding:6px 12px;
text-decoration:none;
color:white;
border-radius:4px;
}

</style>

</head>

<body>

<h2>User Management</h2>
<br>

<a href="dashboard.php" class="back">⬅ Back to Dashboard</a>
<br>

<br>



<br><br>

<table>

<tr>
<th>ID</th>
<th>Name</th>
<th>Email</th>
<th>Phone</th>
<th>Gender</th>
<th>Action</th>
</tr>

<?php
while($row = mysqli_fetch_assoc($result)){
?>

<tr>

<td><?php echo $row['id']; ?></td>

<td><?php echo $row['username']; ?></td>

<td><?php echo $row['email']; ?></td>

<td><?php echo $row['phone']; ?></td>

<td><?php echo $row['gender']; ?></td>

<td>

<a href="admin_users.php?delete=<?php echo $row['id']; ?>"
onclick="return confirm('Delete this user?')">

<button class="delete">Delete</button>

</a>

</td>

</tr>

<?php } ?>

</table>


</body>
</html>
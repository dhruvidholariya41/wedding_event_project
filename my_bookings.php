<?php
session_start();
include("../pages/db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// ડેટા ફેચિંગ - ટેબલના સાચા નામ સાથે
$haldi = mysqli_query($conn,"SELECT *, 'haldi_bookings' as t_name, 'Haldi' as event_name FROM haldi_bookings WHERE user_id='$user_id'");
$mehndi = mysqli_query($conn,"SELECT *, 'mehndi_bookings' as t_name, 'Mehndi' as event_name FROM mehndi_bookings WHERE user_id='$user_id'");
$sangeet = mysqli_query($conn,"SELECT *, 'sangeet_bookings' as t_name, 'Sangeet' as event_name FROM sangeet_bookings WHERE user_id='$user_id'");
$wedding = mysqli_query($conn,"SELECT *, 'wedding_bookings' as t_name, 'Wedding' as event_name FROM wedding_bookings WHERE user_id='$user_id'");
$reception = mysqli_query($conn,"SELECT *, 'reception_bookings' as t_name, 'Reception' as event_name FROM reception_bookings WHERE user_id='$user_id'");
$mandap = mysqli_query($conn,"SELECT *, 'mandap_muhrat_bookings' as t_name, 'Mandap' as event_name FROM mandap_muhrat_bookings WHERE user_id='$user_id'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>My Bookings</title>
    <link rel="stylesheet" href="./event.css">
    <style>
        .back-btn{ display:block; width:120px; margin:20px auto; padding:10px; background:#ec4899; color:white; border-radius:25px; text-decoration:none; text-align:center; }
        .bookings-wrapper { display: flex; flex-wrap: wrap; gap: 20px; justify-content: center; padding: 20px; }
        .booking-card { border: 1px solid #ddd; padding: 20px; border-radius: 12px; width: 320px; background: #fff; box-shadow: 0 5px 15px rgba(0,0,0,0.05); position: relative; }
        
        /* Status Styles */
        .status-tag { position: absolute; top: 15px; right: 15px; padding: 4px 10px; border-radius: 20px; font-size: 11px; font-weight: bold; text-transform: uppercase; }
        .pending { background: #fef3c7; color: #92400e; }
        .approve { background: #dcfce7; color: #166534; }
        .cancelled { background: #fee2e2; color: #991b1b; }

        .cancel-btn { background: #e91e63; color: white; border: none; padding: 10px; border-radius: 6px; cursor: pointer; width: 100%; margin-top: 15px; font-weight: bold; }
        .cancel-btn:hover { background: #c2185b; }
        .disabled-btn { background: #ccc !important; cursor: not-allowed; color: #666; }
        .refund-info { font-size: 11px; color: #777; text-align: center; margin-top: 8px; line-height: 1.4; }
    </style>
</head>
<body>

<h2 style="text-align:center; margin-top:30px; color:#be185d;">My Event Bookings</h2>
<a href="home.php" class="back-btn">⬅ Back Home</a>

<div class="bookings-wrapper">
<?php
function showBookings($result){
    if($result && mysqli_num_rows($result) > 0){
        while($row = mysqli_fetch_assoc($result)){
            $status = $row['status'] ?? 'Pending';
            $event_date = $row['event_date'];
            $today = date('Y-m-d');
            
            // ૨૪ કલાકની કન્ડિશન (ઇવેન્ટની તારીખ આવતીકાલ પછીની હોવી જોઈએ)
            $is_cancellable = (strtotime($event_date) > strtotime($today . ' +1 day'));
            
            // સ્ટેટસ મુજબ કલર ક્લાસ
            $status_class = strtolower($status);
            
            echo "<div class='booking-card'>
                    <span class='status-tag $status_class'>$status</span>
                    <h3 style='color:#ec4899;'>".$row['event_name']."</h3>
                    <p><b>Date:</b> ".date('d-M-Y', strtotime($event_date))."</p>
                    <p><b>Venue:</b> ".$row['event_address']."</p>
                    <p><b>Amount Paid:</b> ₹".$row['payable_amount']."</p>";

            if($status != 'Cancelled'){
                if($is_cancellable){
                    echo "<button class='cancel-btn' onclick='confirmCancel(".$row['id'].", \"".$row['t_name']."\")'>Cancel Booking</button>
                          <p class='refund-info'>* Get 80% refund on cancellation</p>";
                } else {
                    echo "<button class='cancel-btn disabled-btn'>Locked</button>
                          <p class='refund-info' style='color:#d32f2f;'>Cannot cancel within 24 hours of event</p>";
                }
            } else {
                echo "<button class='cancel-btn disabled-btn'>Cancelled</button>
                      <p class='refund-info' style='font-weight:bold; color:green;'>Refund has been initiated</p>";
            }
            echo "</div>";
        }
    }
}

showBookings($haldi);
showBookings($mehndi);
showBookings($sangeet);
showBookings($wedding);
showBookings($reception);
showBookings($mandap);
?>
</div>

<script>
function confirmCancel(id, type) {
    let text = "DO YOU WANT TO CANCEL THIS BOOKING?\n\n- You will get 80% money back.\n- 20% will be charged as cancellation fee.\n\nThis action cannot be undone.";
    if(confirm(text)) {
        window.location.href = "cancel_booking.php?id=" + id + "&type=" + type;
    }
}
</script>

</body>
</html>
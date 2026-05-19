<?php

session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../pages/db_connect.php");

/* ================= CHECK SESSION ================= */

if (!isset($_SESSION['booking_data']) || !isset($_SESSION['event_type'])) {
    die("No booking data found");
}

$data = $_SESSION['booking_data'];
$event_type = $_SESSION['event_type'];

/* ================= USER INFO ================= */

$user_id = $_SESSION['user_id'] ?? '';
$username = $_SESSION['username'] ?? '';

/* ================= EVENT INFO ================= */

$event_date = $data['event_date'] ?? '';
$event_time = $data['event_time'] ?? '';
$event_address = $data['event_address'] ?? '';

$mehndi_required = $data['mehndi_required'] ?? 'No';
$mehndi_name = $data['mehndi_name'] ?? '';
$mehndi_contact = $data['mehndi_contact'] ?? '';
$bride_package = $data['bride_package'] ?? '';
$sider_count = intval($data['sider_count'] ?? 0);
$mehndi_total = $data['mehndi_total'] ?? 0;

/* ================= DJ ================= */

$dj_required = $data['dj_required'] ?? '';
$dj_type = $data['dj_type'] ?? '';
$dj_total = $data['dj_total'] ?? 0;

/* ================= STAGE ================= */

$stage_required = $data['stage_required'] ?? '';
$stage_theme = $data['stage_theme'] ?? '';
$stage_total = $data['stage_total'] ?? 0;

/* ================= LIGHT ================= */

$lighting_required = $data['lighting_required'] ?? '';
$light_theme = $data['light_theme'] ?? '';
$light_total = $data['light_total'] ?? 0;

/* ================= ENTRY ================= */

$entry_required = $data['entry_required'] ?? '';
$entry_theme = $data['entry_theme'] ?? '';
$entry_total = $data['entry_total'] ?? 0;

/* ================= MUSIC ================= */

$music_required = $data['music_required'] ?? '';
$music_type = $data['music_type'] ?? '';
$music_total = $data['music_total'] ?? 0;

/* ================= PHOTOGRAPHY ================= */

$photo_required = $data['photo_required'] ?? '';
$photo_package = $data['photo_package'] ?? '';
$photo_total = $data['photo_total'] ?? 0;

$photography_required = $data['photography_required'] ?? '';
$photography_package = $data['photography_package'] ?? '';
$photography_total = $data['photography_total'] ?? 0;

/* ================= DECORATION ================= */

$decoration_required = $data['decoration_required'] ?? '';
$decoration_theme = $data['decoration_theme'] ?? '';
$decoration_total = $data['decoration_total'] ?? 0;

/* ================= ANCHOR ================= */

$anchor_required = $data['anchor_required'] ?? '';
$anchor_type = $data['anchor_type'] ?? '';
$anchor_total = $data['anchor_total'] ?? 0;

/* ================= FOOD ================= */

$food_required = $data['food_required'] ?? '';
$food_type = $data['food_type'] ?? '';
$guest_count = $data['guest_count'] ?? 0;
$food_total = $data['food_total'] ?? 0;

/* ================= PAYMENT ================= */

$payment_method = $data['payment_method'] ?? '';
$payment_option = $data['payment_option'] ?? '';
$payable_amount = $data['payable_amount'] ?? 0;
$total_amount = $data['total_amount'] ?? 0;

/* ================= WEDDING OPTIONS ================= */

$bride_entry_required = $data['bride_entry_required'] ?? '';
$bride_entry = $data['bride_entry'] ?? '';
$bride_total = $data['bride_total'] ?? 0;

$groom_entry_required = $data['groom_entry_required'] ?? '';
$groom_entry = $data['groom_entry'] ?? '';
$groom_total = $data['groom_total'] ?? 0;

$varmala_required = $data['varmala_required'] ?? '';
$varmala_stage = $data['varmala_stage'] ?? '';
$varmala_total = $data['varmala_total'] ?? 0;

$props_required = $data['props_required'] ?? '';
$entry_props = $data['entry_props'] ?? '';
$props_total = $data['props_total'] ?? 0;

/* ================= SEATING ================= */

$seating_required = $data['seating_required'] ?? '';
$seating_type = $data['seating_type'] ?? '';
$guest_number = $data['guest_number'] ?? 0;
$seating_total = $data['seating_total'] ?? 0;

/* ================= MANDAP ================= */

$pooja_required = $data['pooja_required'] ?? '';
$pooja_package = $data['pooja_package'] ?? '';
$pooja_total = $data['pooja_total'] ?? 0;

$pandit_required = $data['pandit_required'] ?? '';
$pandit_name = $data['pandit_name'] ?? '';
$pandit_phone = $data['pandit_phone'] ?? '';
$pandit_total = $data['pandit_total'] ?? 0;
/* ================= EVENT TYPE CONDITION ================= */

if ($event_type == "haldi") {

$sql = "INSERT INTO haldi_bookings
(user_id, username, event_date, event_time, event_address,
music_required, music_type, music_total,
photo_required, photo_package, photo_total,
decoration_required, decoration_theme, decoration_total,
anchor_required, anchor_type, anchor_total,
food_required, food_type, guest_count, food_total,
payment_method, payment_option, payable_amount, total_amount)

VALUES
('$user_id','$username','$event_date','$event_time','$event_address',
'$music_required','$music_type','$music_total',
'$photo_required','$photo_package','$photo_total',
'$decoration_required','$decoration_theme','$decoration_total',
'$anchor_required','$anchor_type','$anchor_total',
'$food_required','$food_type','$guest_count','$food_total',
'$payment_method','$payment_option','$payable_amount','$total_amount')";

}

else if ($event_type == "mehndi") {

$sql = "INSERT INTO mehndi_bookings
    (user_id, username, event_date, event_time, event_address,
     
mehndi_required,mehndi_name, mehndi_contact, bride_package, sider_count, mehndi_total,
     music_required,music_type, music_total,
     photo_required,photo_package, photo_total,
     decoration_required,decoration_theme, decoration_total,
     food_required,food_type, guest_count, food_total,
     payment_method, payment_option, payable_amount, total_amount)

    VALUES
    ('$user_id','$username','$event_date','$event_time','$event_address',
     '$mehndi_required','$mehndi_name','$mehndi_contact','$bride_package','$sider_count','$mehndi_total',
     '$music_required','$music_type','$music_total',
     '$photo_required','$photo_package','$photo_total',
     '$decoration_required','$decoration_theme','$decoration_total',
     '$food_required','$food_type','$guest_count','$food_total',
     '$payment_method','$payment_option','$payable_amount','$total_amount')";

}

else if ($event_type == "mandap") {

$sql = "INSERT INTO mandap_muhrat_bookings (
    user_id, username,
    event_date, event_time, event_address,

    pooja_required, pooja_package, pooja_total,
    pandit_required, pandit_name, pandit_phone, pandit_total,
    decoration_required, decoration_theme, decoration_total,
    photography_required, photography_package, photography_total,
    food_required, food_type, guest_count, food_total,

    total_amount, payment_method, payment_option, payable_amount
) VALUES (
    '$user_id','$username',
    '$event_date','$event_time','$event_address',

    '$pooja_required','$pooja_package','$pooja_total',
    '$pandit_required','$pandit_name','$pandit_phone','$pandit_total',
    '$decoration_required','$decoration_theme','$decoration_total',
    '$photography_required','$photography_package','$photography_total',
    '$food_required','$food_type','$guest_count','$food_total',

    '$total_amount','$payment_method','$payment_option','$payable_amount'
)";
}

else if ($event_type == "sangeet") {

$sql = "INSERT INTO sangeet_bookings
(user_id, username, event_date, event_time, event_address,
dj_required, dj_type, dj_total,
stage_required, stage_theme, stage_total,
lighting_required, light_theme, light_total,
entry_required, entry_theme, entry_total,
photo_required, photo_package, photo_total,
food_required, food_type, guest_count, food_total,
payment_method, payment_option, payable_amount, total_amount)

VALUES
('$user_id','$username','$event_date','$event_time','$event_address',
'$dj_required','$dj_type','$dj_total',
'$stage_required','$stage_theme','$stage_total',
'$lighting_required','$light_theme','$light_total',
'$entry_required','$entry_theme','$entry_total',
'$photo_required','$photo_package','$photo_total',
'$food_required','$food_type','$guest_count','$food_total',
'$payment_method','$payment_option','$payable_amount','$total_amount')";
}

else if ($event_type == "wedding") {

$sql = "INSERT INTO wedding_bookings (
        user_id, username, event_date, event_time, event_address,
         decoration_required,decoration_theme, decoration_total,
        bride_entry_required,bride_entry, bride_total,
        groom_entry_required,groom_entry, groom_total,
        varmala_required,varmala_stage, varmala_total,
        props_required,entry_props, props_total,
        dj_required,dj_type, dj_total,
        seating_required,seating_type, guest_number, seating_total,
        photo_required,photo_package, photo_total,
        food_required,food_type, guest_count, food_total,
        total_amount, payment_method, payment_option, payable_amount
       
    ) VALUES (
        '$user_id','$username','$event_date','$event_time','$event_address',
        '$decoration_required','$decoration_theme','$decoration_total',
        '$bride_entry_required','$bride_entry','$bride_total',
        '$groom_entry_required','$groom_entry','$groom_total',
        '$varmala_required','$varmala_stage','$varmala_total',
        '$props_required','$entry_props','$props_total',    
        '$dj_required','$dj_type','$dj_total',
        '$seating_required','$seating_type',$guest_number,'$seating_total',
        '$photo_required','$photo_package','$photo_total',
     '$food_required','$food_type',$guest_count,'$food_total',
        '$total_amount','$payment_method','$payment_option','$payable_amount')";

}

$sql = "INSERT INTO reception_bookings (

user_id,username,
event_date,event_time,event_address,

decoration_required,decoration_theme,decoration_total,
props_required,entry_props,props_total,

stage_required,stage_theme,stage_total,

dj_required,dj_type,dj_total,

seating_required,seating_type,guest_number,seating_total,

photography_required,photo_package,photo_total,

food_required,food_type,guest_count,food_total,

total_amount,payable_amount,
payment_method,payment_option

)

VALUES(

'$user_id','$username',
'$event_date','$event_time','$event_address',

'$decoration_required','$decoration_theme','$decoration_total',

'$props_required','$entry_props','$props_total',

'$stage_required','$stage_theme','$stage_total',

'$dj_required','$dj_type','$dj_total',

'$seating_required','$seating_type','$guest_number','$seating_total',

'$photography_required','$photo_package','$photo_total',

'$food_required','$food_type','$guest_count','$food_total',

'$total_amount','$payable_amount',
'$payment_method','$payment_option'
)";



/* ================= QUERY EXECUTE ================= */

if(isset($sql)){

if(mysqli_query($conn,$sql)){

    unset($_SESSION['booking_data']);
    unset($_SESSION['event_type']);


}
else{

echo "Database Error: ".mysqli_error($conn);

}

}

?>

<!DOCTYPE html>
<html>

<head>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>

<title>Payment Success</title>

<link rel="stylesheet" href="event.css">

<style>

.receipt{
width:420px;
margin:30px auto;
background:white;
padding:20px;
border:1px solid #ccc;
border-radius:8px;
}

.receipt h2{
text-align:center;
margin-bottom:15px;
}

.row{
margin:6px 0;
}

.print-btn{
margin-top:15px;
padding:8px 15px;
background:#28a745;
color:white;
border:none;
cursor:pointer;
border-radius:5px;
}

@media print {
    .success-container, .print-btn, .home-btn {
        display: none !important;
    }
    .receipt {
        border: none;
        margin: 0;
        width: 100%;
    }
}



</style>

</head>

<body>

<div class="success-container">

<div class="success-box">

<h1>✅ Payment Successful</h1>

<p>Your event booking has been confirmed.</p>
 <a href="home.php" class="home-btn">Go to Home</a>

</div>

</div>

<!-- RECEIPT -->

<div class="receipt" id="invoice">
    <h2>Payment Receipt</h2>
    <div class="row"><b>Name:</b> <?php echo $username; ?></div>
    <div class="row"><b>Event Type:</b> <?php echo $event_type; ?></div>
    <div class="row"><b>Event Date:</b> <?php echo $event_date; ?></div>
    <div class="row"><b>Event Time:</b> <?php echo $event_time; ?></div>
    <div class="row"><b>Event Address:</b> <?php echo $event_address; ?></div>
    <div class="row"><b>Total Amount:</b> ₹<?php echo $total_amount; ?></div>
    <div class="row"><b>Paid Amount:</b> ₹<?php echo $payable_amount; ?></div>
    <div class="row"><b>Payment Method:</b> <?php echo $payment_method; ?></div>
    <div class="row"><b>Status:</b> Success</div>
</div>

<center>
    <!-- <button class="print-btn" onclick="downloadReceipt()">
        Download / Print Receipt
    </button> --><a href="home.php" class="home-btn">Download / Print Receipt</a>
</center>
<script>
async function autoDownloadPDF() {
    const { jsPDF } = window.jspdf;
    const invoice = document.getElementById('invoice');

    // html2canvas નો ઉપયોગ કરીને div ને image માં ફેરવવું
    html2canvas(invoice, { scale: 2 }).then(canvas => {
        const imgData = canvas.toDataURL('image/png');
        const pdf = new jsPDF('p', 'mm', 'a4');
        
        const imgProps = pdf.getImageProperties(imgData);
        const pdfWidth = pdf.internal.pageSize.getWidth();
        const pdfHeight = (imgProps.height * pdfWidth) / imgProps.width;

        // ઈમેજને PDF માં એડ કરવી
        pdf.addImage(imgData, 'PNG', 0, 10, pdfWidth, pdfHeight);
        
        // ફાઇલ ઓટોમેટિક સેવ થશે
        pdf.save("Receipt_<?php echo $username; ?>.pdf");
    });
}
</script>

</div>

</body>

</html>
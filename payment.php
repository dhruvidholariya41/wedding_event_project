    <!-- <?php
    session_start();

    if(!isset($_SESSION['booking_data'])){
        die("No booking data found. Please book event first.");
    }

    $data = $_SESSION['booking_data'];
    ?>

    <link rel="stylesheet" href="event.css">

    <section class="booking-section">
    <div class="booking-container">

    <h2>Online Payment</h2>

    <div class="form-group">
    <label>Total Amount</label>
    <input type="text" value="₹ <?php echo $data['total_amount']; ?>" readonly>
    </div>


        <form action="payment_success.php" method="post">

    <div class="form-group">
    <label>Payable Amount</label>
    <input type="text" value="₹ <?php echo $data['payable_amount']; ?>" readonly>
    </div>

    <form action="payment_success.php" method="post">

    <input type="hidden" name="booking_data" value="1">

    <button type="submit" class="booking-btn">
    Pay Now
    </button>

    </form>

    </form>

    </div>
    </section> -->


   <?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


if($_SERVER["REQUEST_METHOD"] == "POST"){

    $_SESSION['booking_data'] = $_POST;

    // event type session માં add
   $_SESSION['booking_data']['event_type'] = $_SESSION['event_type'] ?? '';
}

$data = $_SESSION['booking_data'];
?>

<link rel="stylesheet" href="event.css">

<section class="booking-section">
<div class="booking-container">

<h2>Online Payment</h2>

<div class="form-group">
<label>Total Amount</label>
<input type="text" value="₹ <?php echo $data['total_amount']; ?>" readonly>
</div>

<form action="payment_success.php" method="post">

<div class="form-group">
<label>Payable Amount</label>
<input type="text" value="₹ <?php echo $data['payable_amount']; ?>" readonly>
</div>

<button type="submit" class="booking-btn">
Pay Now
</button>

</form>

</div>
</section>
<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../navbar/header.php');
include("../pages/db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* --- STEP 1: COUNT PAST BOOKINGS (RE-FIXED LOGIC) --- */
$user_id = $_SESSION['user_id'];
$tables = ["mehndi_bookings", "haldi_bookings", "mandap_muhrat_bookings", "sangeet_bookings", "wedding_bookings", "reception_bookings"];
$total_past = 0;

foreach ($tables as $tbl) {
    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM $tbl WHERE user_id = '$user_id'");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $total_past += (int) $row['count'];
    }
}

// તારું લોજિક: 1st Book = 10%, 2nd Book = 0%, 3rd+ Book = 15%
$discount_percentage = 0;
$headline_msg = "";

if ($total_past == 0) {
    $discount_percentage = 10;
    $headline_msg = "Special Welcome Offer: 10% OFF on your 1st Booking!";
} elseif ($total_past == 1) {
    $discount_percentage = 0;
    $headline_msg = ""; // ૨જા બુકિંગ વખતે કંઈ નહીં દેખાય
} elseif ($total_past >= 2) {
    $discount_percentage = 15;
    $headline_msg = "VIP Loyalty Reward: 15% OFF Applied!";
}

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

      $user_id = $_SESSION['user_id'];
      $username = $_SESSION['username'];

      $event_date = $_POST['event_date'] ?? '';
      $event_time = $_POST['event_time'] ?? '';
      $event_address = $_POST['event_address'] ?? '';

      if (empty($event_date)) {
        die("Please select event date.");
      }

      if ($event_date < date('Y-m-d')) {
        die("Past date booking not allowed.");
      }

      $check = mysqli_query($conn, "SELECT id FROM reception_bookings WHERE event_date='$event_date'");

      if (mysqli_num_rows($check) > 0) {
        die("This date already booked.");
      }

      /* ===== FORM VALUES ===== */

      $decoration_required = $_POST['decoration_required'] ?? 'No';
      $decoration_theme = $_POST['decoration_theme'] ?? '';
      $decoration_total = $_POST['decoration_total'] ?? 0;

      $props_required = $_POST['props_required'] ?? 'No';
      $entry_props = $_POST['entry_props'] ?? '';
      $props_total = $_POST['props_total'] ?? 0;

      $stage_required = $_POST['stage_required'] ?? 'No';
      $stage_theme = $_POST['stage_theme'] ?? '';
      $stage_total = $_POST['stage_total'] ?? 0;

      $dj_required = $_POST['dj_required'] ?? 'No';
      $dj_type = $_POST['dj_type'] ?? '';
      $dj_total = $_POST['dj_total'] ?? 0;

      $seating_required = $_POST['seating_required'] ?? 'No';
      $seating_type = $_POST['seating'] ?? '';
      $guest_number = $_POST['guest_number'] ?? 0;
      $seating_total = $_POST['seating_total'] ?? 0;

      $photography_required = $_POST['photo_required'] ?? 'No';
      $photo_package = $_POST['photo_package'] ?? '';
      $photo_total = $_POST['photo_total'] ?? 0;

      $food_required = $_POST['food'] ?? 'No';
      $food_type = $_POST['food_type'] ?? '';
      $guest_count = isset($_POST['guest_count']) && $_POST['guest_count'] != "" ? $_POST['guest_count'] : 0;
      $food_total = $_POST['food_total'] ?? 0;

      $total_amount = $_POST['total_amount'] ?? 0;

      $payment_method = $_POST['payment_method'] ?? '';
      $payment_option = $_POST['payment_option'] ?? '';
      $payable_amount = $_POST['payable_amount'] ?? 0;

      /****online** */

      if ($payment_method == "Online") {

        if (empty($_POST['guest_count'])) {
          $_POST['guest_count'] = 0;
        }

        $_SESSION['booking_data'] = $_POST;
        $_SESSION['event_type'] = "reception_bookings";

        header("Location: payment.php");
        exit();
      }
      /* ===== INSERT QUERY ===== */

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


      /* ===== EXECUTE QUERY ===== */

      if (mysqli_query($conn, $sql)) {
        echo "<script>
alert('reception Booking Successful! Booking ID: $last_id');
window.location='home.php';
</script>";
        exit();

      } else {

        echo "SQL Error: " . mysqli_error($conn);

      }
    }

    ?>
    <link rel="stylesheet" href="./event.css">
<style>
    .pulse { animation: pulse-animation 2s infinite; }
    @keyframes pulse-animation {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }
</style>

<section class="booking-section">
  <div class="booking-container">
    <h2 style="text-align: center; color: #be185d;">Reception Booking</h2>

    <?php if ($headline_msg != ""): ?>
        <div class="offer-banner pulse" style="background: #fff5f8; border: 2px dashed #ec4899; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; color: #be185d;">
            <h3 style="margin: 0;">✨ <?= $headline_msg ?> ✨</h3>
        </div>
    <?php endif; ?>

    <form method="POST">


      <!-- Event Date -->
      <div class="form-group">
        <label>Event Date</label>
        <input type="date" name="event_date" required>
      </div>

      <div class="form-group">
        <label>Event Time</label>
        <select name="event_time" required>
          <option value="">Select</option>
          <option>Morning</option>
          <option>Evening</option>
          <option>Night</option>
        </select>
      </div>

      <!-- Event Address -->
      <div class="form-group">
        <label>Event Address</label>
        <textarea name="event_address" required></textarea>
      </div>

      <hr>


      <!-- DECORATION -->

      <div class="form-group">
        <label>You need Decoration?</label>
        <select id="decoration_required" name="decoration_required" onchange="toggleDecorationMain()" required>
          <option value="">Select</option>
          <option>No</option>
          <option>Yes</option>
        </select>
      </div>

      <div id="decoration_main_section" style="display:none;">

        <div class="form-group">
          <label>Select Decoration Theme</label>
          <select id="decoration_theme" name="decoration_theme" onchange="setDecorationPrice()">
            <option value="">Select Theme</option>
            <option data-price="50000">Traditional (₹50000)</option>
            <option data-price="80000">Royal (₹80000)</option>
            <option data-price="120000">Grand Luxury (₹120000)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Decoration Total</label>
          <input type="number" id="decoration_total" name="decoration_total" readonly>
        </div>

      </div>
      <hr>

      <!-- ENTRY PROPS -->


      <div class="form-group">
        <label>You need Entry Props?</label>
        <select id="props_required" name="props_required" onchange="toggleProps()" required>
          <option value="">Select</option>
          <option>No</option>
          <option>Yes</option>
        </select>
      </div>

      <div id="props_section" style="display:none;">

        <div class="form-group">
          <select id="entry_props" name="entry_props" onchange="setPropsPrice()">
            <option value="">Select</option>
            <option data-price="10000">Cold Fire (₹10000)</option>
            <option data-price="15000">Smoke + Lights (₹15000)</option>
            <option data-price="25000">Full Effects (₹25000)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Entry Props Total</label>
          <input type="number" id="props_total" name="props_total" readonly>
        </div>

      </div>
      <hr>

      <!-- ================= STAGE DECORATION ================= -->

      <div class="form-group">
        <label>You need Stage Decoration?</label>
        <select id="stage_required" name="stage_required" onchange="toggleDecoration()" required>
          <option value="">Select</option>
          <option>No</option>
          <option>Yes</option>
        </select>
      </div>

      <div id="decoration_section" style="display:none;">

        <div class="form-group">
          <label>Select Decoration Theme</label>
          <select id="stage_theme" name="stage_theme" onchange="setStagePrice()">
            <option value="">Select Theme</option>
            <option data-price="50000">Traditional (₹50000)</option>
            <option data-price="80000">Royal (₹80000)</option>
            <option data-price="120000">Grand Luxury (₹120000)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Decoration Total</label>
          <input type="number" id="stage_total" name="stage_total" readonly>
        </div>

      </div>

      <!-- DJ / SINGER -->

      <div class="form-group">
        <label>you need a DJ?</label>
        <select id="dj_required" name="dj_required" onchange="toggleDJ()" required>
          <option value="">select</option>
          <option>No</option>
          <option>Yes</option>
        </select>
      </div>

      <div id="dj_section" style="display:none;">
        <div class="form-group">
          <select id="dj_type" name="dj_type" onchange="setDJPrice()">
            <option value="">Select</option>
            <option data-price="30000">DJ (₹30000)</option>
            <option data-price="50000">Singer (₹50000)</option>
          </select>
        </div>

        <div class="form-group">
          <label>DJ Total</label>
          <input type="number" id="dj_total" name="dj_total" readonly>
        </div>
      </div>

      <hr>



      <!-- SEATING -->

      <div class="form-group">
        <label>You need Seating Arrangement?</label>
        <select id="seating_required" name="seating_required" onchange="toggleSeating()" required>
          <option value="">Select</option>
          <option>No</option>
          <option>Yes</option>
        </select>
      </div>

      <div id="seating_section" style="display:none;">

        <div class="form-group">
          <select id="seating" name="seating" onchange="setSeatingPrice()">
            <option value="">Select</option>
            <option data-price="200">Chair (₹200 per guest)</option>
            <option data-price="500">Sofa (₹500 per guest)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Total Guests for Seating</label>
          <input type="number" name="guest_number" onchange="setSeatingPrice()" min="1">
        </div>

        <div class="form-group">
          <label>Seating Total</label>
          <input type="number" id="seating_total" name="seating_total" readonly>
        </div>

      </div>

      <!-- PHOTOGRAPHY -->


      <div class="form-group">
        <label>You need Photography?</label>
        <select id="photo_required" name="photo_required" onchange="togglePhoto()" required>
          <option value="">Select</option>
          <option>No</option>
          <option>Yes</option>
        </select>
      </div>

      <div id="photo_section" style="display:none;">

        <div class="form-group">
          <select id="photo_package" name="photo_package" onchange="setPhotoPrice()">
            <option value="">Select</option>
            <option data-price="50000">Basic (₹50000)</option>
            <option data-price="80000">Premium (₹80000)</option>
            <option data-price="120000">Cinematic (₹120000)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Photography Total</label>
          <input type="number" id="photo_total" name="photo_total" readonly>
        </div>

      </div>
      <hr>

      <!-- ================= FOOD ================= -->

      <!-- FOOD -->
      <div class="form-group">
        <label>You need a food?</label>
        <select id="food" name="food_required" onchange="toggleFood()" required>
          <option value="">Select</option>
          <option>No</option>
          <option>Yes</option>
        </select>
      </div>

      <div id="food_section" style="display:none;">

        <div class="form-group">
          <label>Food Type</label>
          <select id="food_type" name="food_type" onchange="setFoodPrice()">
            <option value="">Select</option>
            <option value="Veg" data-price="400">Veg (₹400 per plate)</option>
            <option value="Non-Veg" data-price="500">Non-Veg (₹500 per plate)</option>
            <option value="Veg + NonVeg" data-price="600">Veg + NonVeg (₹600 per plate)</option>
          </select>
        </div>

        <div class="form-group">
          <label>Total Guests</label>
          <input type="number" id="guest_count" name="guest_count" min="1" max="9999" oninput="this.value=this.value.replace(/[^0-9]/g,'');
if(this.value.length>4)this.value=this.value.slice(0,4);
setFoodPrice();">
        </div>

        <div class="form-group">
          <label>Food Total</label>
          <input type="number" id="food_total" name="food_total" readonly>
        </div>

      </div>
      <hr>
      <!-- ================= PAYMENT METHOD ================= -->
      <div class="form-group">
        <label>Payment Method</label>
        <select id="payment_method" name="payment_method" required>
          <option value="">Select</option>
          <option>Cash</option>
          <option>Online</option>
        </select>
      </div>

      <!-- ================= PAYMENT OPTION ================= -->
      <div class="form-group">
        <label>Payment Option</label>
        <select id="payment_option" name="payment_option" required>
          <option value="">Select</option>
          <option value="Half">Half Now (50%) + Half After Event</option>
          <option value="Full">Full Payment</option>
        </select>
      </div>


      <?php if($discount_percentage > 0): ?>
    <div style="background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); border: 2px dashed #ec4899; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; gap: 15px; color: #be185d; clear: both;">
        <i class="fa-solid fa-gift" style="font-size: 24px; color: #ec4899;"></i>
        <span style="font-weight: bold; font-family: sans-serif;">
            <?= ($discount_percentage == 10) ? "Special Welcome Offer: 10% OFF on your First Booking!" : "Loyalty Reward: 15% OFF Applied for our VIP Member!" ?>
        </span>
    </div>
<?php endif; ?>

<div class="form-group">
    <label>Subtotal (Original Price)</label>
    <input type="number" id="sub_total" readonly style="background: #f8fafc;">
</div>

<div class="form-group">
    <label>Discount Amount (₹)</label>
    <input type="number" id="discount_amount" readonly style="color: #15803d; font-weight: bold; background: #f8fafc;">
</div>
      <!-- ================= PAYABLE AMOUNT ================= -->
      <div class="form-group">
        <label>Payable Amount (₹)</label>
        <input type="number" name="payable_amount" id="payable_amount" readonly>
      </div>

      <!-- ================= FINAL TOTAL ================= -->
      <div class="form-group">
        <label>Final Total Amount</label>
        <input type="number" name="total_amount" id="final_total" readonly>
      </div>

      <button type="submit" class="booking-btn">Confirm Booking</button>

    </form>
  </div>
</section>

<script>

  let stageTotal = 0,
    foodTotal = 0,
    decorationTotal = 0,
    propsTotal = 0,
    djTotal = 0,
    seatingTotal = 0,
    photoTotal = 0;

  /* ================= PRICE FUNCTIONS ================= */

  function getPrice(id, totalId) {

    let price = document.getElementById(id)
      .selectedOptions[0].getAttribute("data-price") || 0;

    document.getElementById(totalId).value = price;

    calculateFinal();

    return parseInt(price) || 0;

  }

  /* ================= DECORATION ================= */

  function toggleDecorationMain() {

    let val = document.getElementById("decoration_required").value;

    document.getElementById("decoration_main_section").style.display =
      (val === "Yes") ? "block" : "none";

    if (val !== "Yes") {
      decorationTotal = 0;
      document.getElementById("decoration_total").value = 0;
      calculateFinal();
    }

  }

  function setDecorationPrice() {
    decorationTotal = getPrice("decoration_theme", "decoration_total");
  }

  /* ================= ENTRY PROPS ================= */

  function toggleProps() {

    let val = document.getElementById("props_required").value;

    document.getElementById("props_section").style.display =
      (val === "Yes") ? "block" : "none";

    if (val !== "Yes") {
      propsTotal = 0;
      document.getElementById("props_total").value = 0;
      calculateFinal();
    }

  }

  function setPropsPrice() {
    propsTotal = getPrice("entry_props", "props_total");
  }

  /* ================= STAGE ================= */

  function toggleDecoration() {

    let val = document.getElementById("stage_required").value;

    document.getElementById("decoration_section").style.display =
      (val === "Yes") ? "block" : "none";

    if (val !== "Yes") {
      stageTotal = 0;
      document.getElementById("stage_total").value = 0;
      calculateFinal();
    }

  }

  function setStagePrice() {

    let price = document.getElementById("stage_theme")
      .selectedOptions[0].getAttribute("data-price") || 0;

    stageTotal = parseInt(price) || 0;

    document.getElementById("stage_total").value = stageTotal;

    calculateFinal();

  }

  /* ================= DJ ================= */

  function toggleDJ() {

    let val = document.getElementById("dj_required").value;

    document.getElementById("dj_section").style.display =
      (val === "Yes") ? "block" : "none";

    if (val !== "Yes") {
      djTotal = 0;
      document.getElementById("dj_total").value = 0;
      calculateFinal();
    }

  }

  function setDJPrice() {
    djTotal = getPrice("dj_type", "dj_total");
  }

  /* ================= SEATING ================= */

  function toggleSeating() {

    let val = document.getElementById("seating_required").value;

    document.getElementById("seating_section").style.display =
      (val === "Yes") ? "block" : "none";

    if (val !== "Yes") {
      seatingTotal = 0;
      document.getElementById("seating_total").value = 0;
      calculateFinal();
    }

  }

  function setSeatingPrice() {

    let price = document.getElementById("seating")
      .selectedOptions[0].getAttribute("data-price") || 0;

    let guests = document.querySelector("input[name='guest_number']").value || 0;

    seatingTotal = price * guests;

    document.getElementById("seating_total").value = seatingTotal;

    calculateFinal();

  }

  /* ================= PHOTO ================= */

  function togglePhoto() {

    let val = document.getElementById("photo_required").value;

    document.getElementById("photo_section").style.display =
      (val === "Yes") ? "block" : "none";

    if (val !== "Yes") {
      photoTotal = 0;
      document.getElementById("photo_total").value = 0;
      calculateFinal();
    }

  }

  function setPhotoPrice() {
    photoTotal = getPrice("photo_package", "photo_total");
  }

  /* FOOD */

  function toggleFood() {

    let val = document.getElementById("food").value;

    document.getElementById("food_section").style.display =
      (val === "Yes") ? "block" : "none";

    if (val !== "Yes") {
      foodTotal = 0;
      document.getElementById("food_total").value = 0;
      calculateFinal();
    }

  }

  function setFoodPrice() {

    let type = document.getElementById("food_type");

    let price = type.options[type.selectedIndex].getAttribute("data-price") || 0;

    let guests = parseInt(document.getElementById("guest_count").value) || 0;

    foodTotal = price * guests;

    document.getElementById("food_total").value = foodTotal;

    calculateFinal();

  }

  /* ================= FINAL TOTAL ================= */

  function calculateFinal() {
    // Reception ના બધા સર્વિસનો સરવાળો
    let subtotal = 
      (parseInt(decorationTotal) || 0) +
      (parseInt(stageTotal) || 0) +
      (parseInt(propsTotal) || 0) +
      (parseInt(djTotal) || 0) +
      (parseInt(seatingTotal) || 0) +
      (parseInt(photoTotal) || 0) +
      (parseInt(foodTotal) || 0);

    // PHP માંથી ડિસ્કાઉન્ટ ટકાવારી લો (10, 0, અથવા 15)
    let discPercent = <?= $discount_percentage ?>;
    let discAmt = Math.round((subtotal * discPercent) / 100);
    let final = subtotal - discAmt;

    document.getElementById("sub_total").value = subtotal;
    document.getElementById("discount_amount").value = discAmt;
    document.getElementById("final_total").value = final;

    calculatePayment();
  }

  function calculatePayment() {
    let finalTotal = parseInt(document.getElementById("final_total").value) || 0;
    let option = document.getElementById("payment_option").value;
    let payable = (option === "Half") ? Math.round(finalTotal * 0.5) : finalTotal;
    document.getElementById("payable_amount").value = payable;
  }


  /* ================= PAYMENT ================= */

  function calculatePayment() {

    let finalTotal = parseInt(document.getElementById("final_total").value) || 0;

    let option = document.getElementById("payment_option").value;

    let payable = 0;

    if (option === "Half") {
      payable = finalTotal * 0.5;
    }
    else if (option === "Full") {
      payable = finalTotal;
    }

    document.getElementById("payable_amount").value = payable;

  }

  document.getElementById("payment_option")
    .addEventListener("change", calculatePayment);

</script>

<?php include('../navbar/footer.php'); ?>
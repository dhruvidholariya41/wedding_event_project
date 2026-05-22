<?php
session_start();
$_SESSION['event_type'] = "wedding";

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please Login First!'); window.location='login.php';</script>";
    exit();
}
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../navbar/header.php');
include("../pages/db_connect.php");

/* --- STEP 1: COUNT PAST BOOKINGS --- */
$tables = ["mehndi_bookings", "haldi_bookings", "mandap_muhrat_bookings", "sangeet_bookings", "wedding_bookings", "reception_bookings"];
$total_past = 0;
foreach ($tables as $tbl) {
    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM $tbl WHERE user_id = '$user_id'");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $total_past += (int) $row['count'];
    }
}

// તારું લોજિક: 1st = 10%, 2nd = 0%, 3rd+ = 15%
$discount_percentage = 0;
$headline_msg = "";

if ($total_past == 0) {
    $discount_percentage = 10;
    $headline_msg = "Special Welcome Offer: 10% OFF on your 1st Booking!";
} elseif ($total_past == 1) {
    $discount_percentage = 0;
    $headline_msg = ""; // ૨જા બુકિંગમાં કંઈ નહીં દેખાય
} elseif ($total_past >= 2) {
    $discount_percentage = 15;
    $headline_msg = "VIP Loyalty Reward: 15% OFF Applied!";
}

        /* ================= FORM SUBMIT ================= */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $user_id = $_SESSION['user_id'];
            $username = $_SESSION['username'];
            $event_date = $_POST['event_date'];


            $event_time = $_POST['event_time'] ?? '';
            $event_address = $_POST['event_address'] ?? '';

            /* ================= DATE VALIDATION ================= */

            if (empty($event_date)) {
                die("Please select event date.");
            }

            if ($event_date < date('Y-m-d')) {
                die("Past date booking not allowed.");
            }

            $check = mysqli_query(
                $conn,
                "SELECT id FROM wedding_bookings WHERE event_date='$event_date'"
            );

            if (mysqli_num_rows($check) > 0) {
                die("This date is already booked for Wedding.");
            }

            /* ================= GUEST COUNT VALIDATION ================= */

            $guest_count = $_POST['guest_count'] ?? 0;

            if (!empty($guest_count)) {

                if (!ctype_digit($guest_count)) {
                    die("Guest count must be numeric only.");
                }

                if (strlen($guest_count) > 4) {
                    die("Guest count maximum 4 digits allowed.");
                }

                if ($guest_count <= 0) {
                    die("Guest count must be positive.");
                }
            }


            $event_date = $_POST['event_date'];
            $event_time = $_POST['event_time'];
            $event_address = $_POST['event_address'];

            $decoration_required = $_POST['decoration_required'];
            $decoration_theme = $_POST['decoration_theme'];
            $decoration_total = $_POST['decoration_total'] ?? 0;

            $bride_entry_required = $_POST['bride_entry_required'];
            $bride_entry = $_POST['bride_entry'] ?? '';
            $bride_total = $_POST['bride_total'];

            $groom_entry_required = $_POST['groom_entry_required'];
            $groom_entry = $_POST['groom_entry'] ?? '';
            $groom_total = $_POST['groom_total'];


            $varmala_required = $_POST['varmala_required'];
            $varmala_stage = $_POST['varmala_stage'];
            $varmala_total = $_POST['varmala_total'];

            $props_required = $_POST['props_required'];
            $entry_props = $_POST['entry_props'];
            $props_total = $_POST['props_total'];

            $dj_required = $_POST['dj_required'];
            $dj_type = $_POST['dj_type'];
            $dj_total = $_POST['dj_total'];

            $seating_required = $_POST['seating_required'];
            $seating_type = $_POST['seating_type'] ?? '';
            $guest_number = intval($_POST['guest_number'] ?? 0);
            $seating_total = $_POST['seating_total'];


            $photo_required = $_POST['photo_required'];
            $photo_package = $_POST['photo_package'];
            $photo_total = $_POST['photo_total'];

            $food_required = $_POST['food_required'];
            $food_type = $_POST['food_type'] ?? '';

            $guest_count = intval($_POST['guest_count'] ?? 0);

            $food_total = $_POST['food_total'] ?? 0;

            if ($food_required == "No") {
                $food_type = "";
                $guest_count = 0;
                $food_total = 0;
            }


            $total_amount = $_POST['total_amount'];
            $payment_method = $_POST['payment_method'];
            $payment_option = $_POST['payment_option'];
            $payable_amount = $_POST['payable_amount'];

            //     $payment_method = $_POST['payment_method'] ?? '';
// $bride_total = $_POST['bride_total'] ?? 0;
// $groom_total = $_POST['groom_total'] ?? 0;
// $varmala_total = $_POST['varmala_total'] ?? 0;
// $props_total = $_POST['props_total'] ?? 0;
// $dj_total = $_POST['dj_total'] ?? 0;
// $seating_total = $_POST['seating_total'] ?? 0;
// $photo_total = $_POST['photo_total'] ?? 0;
// $food_total = $_POST['food_total'] ?? 0;
        
            /***online */

            if ($payment_method == "Online") {

                if (empty($_POST['guest_count'])) {
                    $_POST['guest_count'] = 0;
                }

                $_SESSION['booking_data'] = $_POST;
                $_SESSION['event_type'] = "wedding_bookings";

                header("Location: payment.php");
                exit();
            }


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

            if (mysqli_query($conn, $sql)) {
    $last_id = mysqli_insert_id($conn); // આ લાઈન ઉમેરો
    echo "<script>
    alert('Wedding Booking Successful! Booking ID: $last_id');
    window.location='home.php';
    </script>";
    exit();

            } else {
                echo "Error: " . mysqli_error($conn);
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
        <h2 style="text-align: center; color: #be185d;">Wedding Booking</h2>

        <?php if ($headline_msg != ""): ?>
            <div class="offer-banner pulse" style="background: #fff5f8; border: 2px dashed #ec4899; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; color: #be185d;">
                <h3 style="margin: 0;">✨ <?= $headline_msg ?> ✨</h3>
            </div>
        <?php endif; ?>
        <form method="POST">

            <!-- CLIENT DETAILS -->

            <div class="form-group">
                <label>Event Date</label>
                <input type="date" name="event_date" min="<?= date('Y-m-d') ?>" required>
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
                <select name="decoration_required" id="decoration_required" onchange="toggleDecoration()" required>
                    <option value="">Select</option>
                    <option>Yes</option>
                    <option>No</option>
                </select>
            </div>

            <div id="decoration_section" style="display:none;">
                <div class="form-group">
                    <label>Theme</label>
                    <select id="decoration_theme" name="decoration_theme" onchange="setDecorationPrice()">
                        <option value="">Select Theme</option>
                        <option data-price="50000">Traditional - ₹50000</option>
                        <option data-price="80000">Royal - ₹80000</option>
                        <option data-price="120000">Grand Luxury - ₹120000</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Decoration Total</label>
                <input type="number" id="decoration_total" name="decoration_total" readonly>
            </div>

            <hr>

            <!-- ================= BRIDE ENTRY ================= -->

            <div class="form-group">
                <label>You need Bride Entry?</label>
                <select id="bride_entry_required" name="bride_entry_required" onchange="toggleBrideEntry()" required>
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="bride_entry_section" style="display:none;">
                <div class="form-group">
                    <label>Entry Type</label>
                    <select id="bride_entry" name="bride_entry" onchange="setBrideEntryPrice()">
                        <option value="">Select Entry</option>
                        <option data-price="10000">Flower Chadar - ₹10000</option>
                        <option data-price="15000">Doli Entry - ₹15000</option>
                        <option data-price="20000">Royal Entry - ₹20000</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Bride Entry Total</label>
                    <input type="number" id="bride_total" name="bride_total" readonly>
                </div>
            </div>

            <hr>

            <!-- ================= GROOM ENTRY ================= -->

            <div class="form-group">
                <label>You need Groom Entry?</label>
                <select id="groom_entry_required" name="groom_entry_required" onchange="toggleGroomEntry()" required>
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="groom_entry_section" style="display:none;">
                <div class="form-group">
                    <label>Entry Type</label>
                    <select id="groom_entry" name="groom_entry" onchange="setGroomEntryPrice()">
                        <option value="">Select Groom Entry</option>
                        <option data-price="5000">Simple Entry</option>
                        <option data-price="10000">Royal Entry</option>
                        <option data-price="15000">Vintage Car Entry</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Groom Entry Total</label>
                    <input type="text" id="groom_total" name="groom_total" readonly>
                </div>
            </div>

            <hr>

            <!-- ================= VARMALA ================= -->

            <div class="form-group">
                <label>You need Varmala Stage?</label>
                <select id="varmala_required" name="varmala_required" onchange="toggleVarmala()" required>
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="varmala_section" style="display:none;">
                <div class="form-group">
                    <select id="varmala_stage" name="varmala_stage" onchange="setVarmalaPrice()">
                        <option value="">Select</option>
                        <option data-price="40000">Classic Stage - ₹40000</option>
                        <option data-price="70000">Royal Stage - ₹70000</option>
                        <option data-price="100000">LED Luxury Stage - ₹100000</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Varmala Total</label>
                    <input type="number" id="varmala_total" name="varmala_total" readonly>
                </div>
            </div>

            <hr>

            <!-- ================= ENTRY PROPS ================= -->

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
                        <option data-price="10000">Cold Fire - ₹10000</option>
                        <option data-price="15000">Smoke + Lights - ₹15000</option>
                        <option data-price="25000">Full Effects - ₹25000</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Entry Props Total</label>
                    <input type="number" id="props_total" name="props_total" readonly>
                </div>
            </div>

            <hr>

            <!-- ================= DJ ================= -->

            <div class="form-group">
                <label>You need DJ?</label>
                <select id="dj_required" name="dj_required" onchange="toggleDJ()" required>
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="dj_section" style="display:none;">
                <div class="form-group">
                    <select id="dj_type" name="dj_type" onchange="setDJPrice()">
                        <option value="">Select</option>
                        <option data-price="30000">DJ - ₹30000</option>
                        <option data-price="50000">Singer - ₹50000</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>DJ Total</label>
                    <input type="number" id="dj_total" name="dj_total" readonly>
                </div>
            </div>

            <hr>

            <!-- ================= SEATING ================= -->

            <div class="form-group">
                <label>You need Seating?</label>
                <select id="seating_required" name="seating_required" onchange="toggleSeating()" required>
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="seating_section" style="display:none;">
                <div class="form-group">
                    <select id="seating_type" name="seating_type">
                        <option value="">Select</option>
                        <option data-price="200">Chair - ₹200 per guest</option>
                        <option data-price="500">Sofa - ₹500 per guest</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Total Guests</label>
                    <input type="number" id="guest_number" name="guest_number" value="0" onchange="setSeatingPrice()">
                </div>

                <div class="form-group">
                    <label>Seating Total</label>
                    <input type="number" id="seating_total" name="seating_total" readonly>
                </div>
            </div>

            <hr>

            <!-- ================= PHOTOGRAPHY ================= -->

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
                        <option data-price="50000">Basic - ₹50000</option>
                        <option data-price="80000">Premium - ₹80000</option>
                        <option data-price="120000">Cinematic - ₹120000</option>
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
                    <i class="fa-solid fa-gift pulse" style="font-size: 24px; color: #ec4899;"></i>
                    <span style="font-weight: bold; font-family: sans-serif;">
                        <?= ($discount_percentage == 10) ? "Special Welcome Offer: 10% OFF on your First Wedding Booking!" : "Loyalty Reward: 15% OFF Applied for our VIP Member!" ?>
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

    let decorationTotal = 0, brideTotal = 0, groomTotal = 0, varmalaTotal = 0,
        propsTotal = 0, djTotal = 0, seatingTotal = 0, photoTotal = 0, foodTotal = 0;

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


    /* ---------- DECORATION ---------- */
    function toggleDecoration() {
        let val = document.getElementById("decoration_required").value;

        document.getElementById("decoration_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val === "No") {
            decorationTotal = 0;
            document.getElementById("decoration_total").value = 0;
            calculateFinal();
        }
    }

    function setDecorationPrice() {
        let s = document.getElementById("decoration_theme");
        decorationTotal = parseInt(s.options[s.selectedIndex].dataset.price || 0);
        document.getElementById("decoration_total").value = decorationTotal;
        calculateFinal();
    }

    /* ---------- BRIDE ENTRY ---------- */
    function toggleBrideEntry() {
        let v = document.getElementById("bride_entry_required").value;

        document.getElementById("bride_entry_section").style.display =
            (v === "Yes") ? "block" : "none";

        if (v === "No") {
            brideTotal = 0;
            document.getElementById("bride_total").value = 0;
            calculateFinal();
        }
    }

    function setBrideEntryPrice() {
        let s = document.getElementById("bride_entry");
        brideTotal = parseInt(s.options[s.selectedIndex].dataset.price || 0);
        document.getElementById("bride_total").value = brideTotal;
        calculateFinal();
    }
    /* ---------- GROOM ENTRY ---------- */
    function toggleGroomEntry() {
        let v = document.getElementById("groom_entry_required").value;

        document.getElementById("groom_entry_section").style.display =
            (v === "Yes") ? "block" : "none";

        if (v === "No") {
            groomTotal = 0;
            document.getElementById("groom_total").value = 0;
            calculateFinal();
        }
    }

    function setGroomEntryPrice() {

        let s = document.getElementById("groom_entry");

        let price = parseInt(s.options[s.selectedIndex].dataset.price || 0);

        document.getElementById("groom_total").value = price;

        calculateFinal();

    }

    /* ---------- VARMALA ---------- */
    function toggleVarmala() {
        let v = document.getElementById("varmala_required").value;

        document.getElementById("varmala_section").style.display =
            (v === "Yes") ? "block" : "none";

        if (v === "No") {
            varmalaTotal = 0;
            document.getElementById("varmala_total").value = 0;
            calculateFinal();
        }
    }

    function setVarmalaPrice() {
        let s = document.getElementById("varmala_stage");
        varmalaTotal = parseInt(s.options[s.selectedIndex].dataset.price || 0);
        document.getElementById("varmala_total").value = varmalaTotal;
        calculateFinal();
    }

    /* ---------- ENTRY PROPS ---------- */
    function toggleProps() {
        let v = document.getElementById("props_required").value;

        document.getElementById("props_section").style.display =
            (v === "Yes") ? "block" : "none";

        if (v === "No") {
            propsTotal = 0;
            document.getElementById("props_total").value = 0;
            calculateFinal();
        }
    }

    function setPropsPrice() {
        let s = document.getElementById("entry_props");
        propsTotal = parseInt(s.options[s.selectedIndex].dataset.price || 0);
        document.getElementById("props_total").value = propsTotal;
        calculateFinal();
    }

    /* ---------- DJ ---------- */
    function toggleDJ() {
        let v = document.getElementById("dj_required").value;

        document.getElementById("dj_section").style.display =
            (v === "Yes") ? "block" : "none";

        if (v === "No") {
            djTotal = 0;
            document.getElementById("dj_total").value = 0;
            calculateFinal();
        }
    }

    function setDJPrice() {
        let s = document.getElementById("dj_type");
        djTotal = parseInt(s.options[s.selectedIndex].dataset.price || 0);
        document.getElementById("dj_total").value = djTotal;
        calculateFinal();
    }

    /* ---------- SEATING ---------- */
    function toggleSeating() {
        let v = document.getElementById("seating_required").value;

        document.getElementById("seating_section").style.display =
            (v === "Yes") ? "block" : "none";

        if (v === "No") {
            seatingTotal = 0;
            document.getElementById("seating_total").value = 0;
            calculateFinal();
        }
    }

    function setSeatingPrice() {
        let type = document.getElementById("seating_type");
        let guests = parseInt(document.getElementById("guest_number").value) || 0;
        let price = parseInt(type.options[type.selectedIndex].dataset.price || 0);

        seatingTotal = price * guests;

        document.getElementById("seating_total").value = seatingTotal;
        calculateFinal();
    }
    /* ---------- PHOTOGRAPHY ---------- */
    function togglePhoto() {
        let v = document.getElementById("photo_required").value;

        document.getElementById("photo_section").style.display =
            (v === "Yes") ? "block" : "none";

        if (v === "No") {
            photoTotal = 0;
            document.getElementById("photo_total").value = 0;
            calculateFinal();
        }
    }

    function setPhotoPrice() {
        let s = document.getElementById("photo_package");
        photoTotal = parseInt(s.options[s.selectedIndex].dataset.price || 0);
        document.getElementById("photo_total").value = photoTotal;
        calculateFinal();
    }

    /* ---------- FINAL TOTAL ---------- */
    function calculateFinal() {
        // બધી જ વેડિંગ સર્વિસનો સરવાળો (Subtotal)
        let subtotal = 
            (parseInt(decorationTotal) || 0) +
            (parseInt(brideTotal) || 0) +
            (parseInt(groomTotal) || 0) +
            (parseInt(varmalaTotal) || 0) +
            (parseInt(propsTotal) || 0) +
            (parseInt(djTotal) || 0) +
            (parseInt(seatingTotal) || 0) +
            (parseInt(photoTotal) || 0) +
            (parseInt(foodTotal) || 0);

        // PHP માંથી ડિસ્કાઉન્ટ ટકાવારી લેવી (10, 0, અથવા 15)
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

    /* ---------- PAYMENT ---------- */
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
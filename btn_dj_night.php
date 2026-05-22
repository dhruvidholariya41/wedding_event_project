<?php
session_start();
$_SESSION['event_type'] = "sangeet";

if (!isset($_SESSION['user_id'])) {
    echo "<script>alert('Please Login First!'); window.location='login.php';</script>";
    exit();
}

include('../navbar/header.php');
include("../pages/db_connect.php");

/* --- STEP 1: COUNT PAST BOOKINGS --- */
$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';
$tables = ["mehndi_bookings", "haldi_bookings", "mandap_muhrat_bookings", "sangeet_bookings", "wedding_bookings", "reception_bookings"];
$total_past = 0;

foreach ($tables as $tbl) {
    $res = mysqli_query($conn, "SELECT COUNT(*) as count FROM $tbl WHERE user_id = '$user_id'");
    if ($res) {
        $row = mysqli_fetch_assoc($res);
        $total_past += (int) $row['count'];
    }
}

// તારું નવું લોજિક: 1st = 10%, 2nd = 0%, 3rd+ = 15%
$discount_percentage = 0;
$headline_msg = "";

if ($total_past == 0) {
    $discount_percentage = 10;
    $headline_msg = "Special Welcome Offer: 10% OFF on your 1st Booking!";
} elseif ($total_past == 1) {
    $discount_percentage = 0;
    $headline_msg = ""; // બીજી વાર કોઈ હેડલાઇન નહીં
} elseif ($total_past >= 2) {
    $discount_percentage = 15;
    $headline_msg = "VIP Loyalty Reward: 15% OFF Applied!";
}

/* --- FORM SUBMIT LOGIC --- */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (તમારો બધો POST ડેટા અને INSERT ક્વેરી અંહી એમનેમ રહેશે)
    // ખાતરી કરો કે $last_id વ્યાખ્યાયિત છે: $last_id = mysqli_insert_id($conn);
}
        /* ================= FORM SUBMIT ================= */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $user_id = $_SESSION['user_id'];
            $username = $_SESSION['username'];

            $event_date = $_POST['event_date'] ?? '';
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
                "SELECT id FROM sangeet_bookings WHERE event_date='$event_date'"
            );

            if (mysqli_num_rows($check) > 0) {
                die("This date is already booked for Sangeet.");
            }


            /* ================= GUEST COUNT VALIDATION ================= */

            $guest_count = isset($_POST['guest_count']) ? trim($_POST['guest_count']) : '';

            if ($food_required == "Yes") {

                if ($guest_count === '') {
                    die("Please enter guest count.");
                }

                if (!ctype_digit($guest_count)) {
                    die("Guest count must be numeric.");
                }

                if ($guest_count <= 0) {
                    die("Guest count must be greater than 0.");
                }

                if ($guest_count > 9999) {
                    die("Maximum 9999 guests allowed.");
                }

            } else {
                $guest_count = 0;
            }

            $guest_count = intval($guest_count);
            /* ================= OTHER FIELDS ================= */
            $dj_required = $_POST['dj_required'] ?? 'No';
            $dj_type = $_POST['dj_type'] ?? '';
            $dj_total = $_POST['dj_total'] ?? 0;

            $stage_required = $_POST['stage_required'] ?? 'No';
            $stage_theme = $_POST['stage_theme'] ?? '';
            $stage_total = $_POST['stage_total'] ?? 0;

            $lighting_required = $_POST['lighting_required'] ?? 'No';
            $light_theme = $_POST['light_theme'] ?? '';
            $light_total = $_POST['light_total'] ?? 0;
            $entry_required = $_POST['entry_required'] ?? 'No';
            $entry_theme = $_POST['entry_theme'] ?? '';
            $entry_total = $_POST['entry_total'] ?? 0;
            $photo_required = $_POST['photo_required'] ?? 'No';
            $photo_package = $_POST['photo_package'] ?? '';
            $photo_total = $_POST['photo_total'] ?? 0;
            $food_required = $_POST['food_required'] ?? 'No';
            $food_type = $_POST['food_type'] ?? '';

            $guest_count = isset($_POST['guest_count']) ? trim($_POST['guest_count']) : '';

            if ($food_required == "Yes") {

                if ($guest_count === '') {
                    die("Please enter guest count.");
                }

                if (!ctype_digit($guest_count)) {
                    die("Guest count must be numeric.");
                }

                if ($guest_count <= 0) {
                    die("Guest count must be greater than 0.");
                }

            } else {
                $guest_count = 0;
            }

            $guest_count = intval($guest_count);

            $food_total = intval($_POST['food_total'] ?? 0);

            $payment_method = $_POST['payment_method'] ?? '';
            $payment_option = $_POST['payment_option'] ?? '';
            $payable_amount = $_POST['payable_amount'] ?? 0;
            $total_amount = $_POST['total_amount'] ?? 0;

            /* ================= ONLINE PAYMENT CHECK ================= */

            if ($payment_method == "Online") {

                if (empty($_POST['guest_count'])) {
                    $_POST['guest_count'] = 0;
                }

                $_SESSION['booking_data'] = $_POST;
                $_SESSION['event_type'] = "sangeet";

                header("Location: payment.php");
                exit();
            }

            /* ================= INSERT ================= */

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

            if (mysqli_query($conn, $sql)) {
                echo "<script>
alert('sangeet Booking Successful! Booking ID: $last_id');
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
        <h2 style="text-align: center; color: #be185d;">Sangeet Booking</h2>

        <?php if ($headline_msg != ""): ?>
            <div class="offer-banner pulse" style="background: #fff5f8; border: 2px dashed #ec4899; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; color: #be185d;">
                <h3 style="margin: 0;">✨ <?= $headline_msg ?> ✨</h3>
            </div>
        <?php endif; ?>



        <form method="POST">
            <input type="hidden" name="user_id" value="<?= $_SESSION['user_id']; ?>">
            <input type="hidden" name="username" value="<?= $_SESSION['username']; ?>">

            <!-- CLIENT DETAILS -->


            <div class="form-group">
                <label>Event Date</label>
                <input type="date" name="event_date" min="<?= date('Y-m-d') ?>" required>
            </div>

            <div class="form-group">
                <label>Event Time</label>
                <select name="event_time" required>
                    <option value="">Select Time</option>
                    <option>Evening</option>
                    <option>Night</option>
                </select>
            </div>

            <div class="form-group">
                <label>Event Address</label>
                <textarea name="event_address" rows="3" required></textarea>
            </div>



            <hr>

            <!-- DJ SECTION -->


            <div class="form-group">
                <label>You need a DJ?</label>
                <select id="dj_required" name="dj_required" onchange="toggleDJ()">
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="dj_section" style="display:none;">

                <div class="form-group">
                    <label>Select Type</label>
                    <select id="dj_type" name="dj_type" onchange="setDJPrice()">
                        <option value="">Select</option>
                        <option data-price="20000">DJ Basic (₹20000)</option>
                        <option data-price="35000">DJ Premium (₹35000)</option>
                        <option data-price="40000">Singer Live (₹40000)</option>
                        <option data-price="60000">Celebrity Singer (₹60000)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>DJ Total</label>
                    <input type="number" id="dj_total" name="dj_total" readonly>
                </div>

            </div>

            <hr>

            <!-- STAGE DECORATION -->


            <div class="form-group">
                <label>You need stage decoration?</label>
                <select id="stage_required" name="stage_required" onchange="toggleStage()">
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="stage_section" style="display:none;">

                <div class="form-group">
                    <label>Select Theme</label>
                    <select id="stage_theme" name="stage_theme" onchange="setStagePrice()">
                        <option value="">Select Theme</option>
                        <option data-price="25000">Royal Stage (₹25000)</option>
                        <option data-price="35000">LED Stage (₹35000)</option>
                        <option data-price="50000">Grand Celebrity Stage (₹50000)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Stage Total</label>
                    <input type="number" id="stage_total" name="stage_total" readonly>
                </div>

            </div>
            <hr>

            <!-- LIGHTING -->


            <div class="form-group">
                <label>You need lighting?</label>
                <select id="lighting_required" name="lighting_required" onchange="toggleLighting()">
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="lighting_section" style="display:none;">
                <div class="form-group">
                    <label>Select Lighting</label>
                    <select id="lighting_type" name="light_theme" onchange="setLightingPrice()">
                        <option value="">Select</option>
                        <option data-price="10000">Basic Lighting (₹10000)</option>
                        <option data-price="20000">LED Lighting (₹20000)</option>
                        <option data-price="35000">Premium Lighting (₹35000)</option>
                    </select>
                </div>
            </div>


            <div class="form-group">
                <label>lightning Total</label>
                <input type="number" id="light_total" name="light_total" readonly>

            </div>

            <hr>

            <!-- ENTRY THEME -->


            <div class="form-group">
                <label>You need entry theme?</label>
                <select id="entry_required" name="entry_required" onchange="toggleEntry()">
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="entry_section" style="display:none;">
                <div class="form-group">
                    <label>Select Entry</label>
                    <select id="entry_type" name="entry_theme" onchange="setEntryPrice()">
                        <option value="">Select</option>
                        <option data-price="8000">Royal Entry (₹8000)</option>
                        <option data-price="15000">Smoke Entry (₹15000)</option>
                        <option data-price="25000">Grand Celebrity Entry (₹25000)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>entry theme Total</label>
                <input type="number" id="entry_total" name="entry_total" readonly>

            </div>

            <hr>

            <!-- PHOTOGRAPHY -->


            <div class="form-group">
                <label>You need photography?</label>
                <select id="photo_required" name="photo_required" onchange="togglePhoto()">
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="photo_section" style="display:none;">
                <div class="form-group">
                    <label>Select Package</label>
                    <select id="photo_type" name="photo_package" onchange="setPhotoPrice()">
                        <option value="">Select</option>
                        <option data-price="20000">Basic Photo (₹20000)</option>
                        <option data-price="35000">Photo + Video (₹35000)</option>
                        <option data-price="60000">Cinematic Package (₹60000)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <input type="number" id="photo_total" name="photo_total" readonly>
            </div>

            <hr>

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
                </div>7

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
    <div style="background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%) !important; border: 2px dashed #ec4899 !important; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; gap: 15px; color: #be185d; clear: both;">
        <i class="fa-solid fa-gift pulse" style="font-size: 24px; color: #ec4899;"></i>
        <span style="font-weight: bold;">
            <?= ($discount_percentage == 10) ? "Special Welcome Offer: 10% Discount on First Booking!" : "Loyalty Reward: 15% Discount Applied!" ?>
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

    let djTotal = 0, stageTotal = 0, lightTotal = 0, entryTotal = 0, photoTotal = 0, foodTotal = 0;

    /* DJ */
    function toggleDJ() {
        let val = document.getElementById("dj_required").value;

        document.getElementById("dj_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val === "No") {
            djTotal = 0;
            document.getElementById("dj_total").value = 0;
            calculateFinal();
        }
    }

    function setDJPrice() {
        let price = document.getElementById("dj_type")
            .selectedOptions[0].dataset.price || 0;

        djTotal = parseInt(price);

        document.getElementById("dj_total").value = djTotal;

        calculateFinal();
    }

    /* STAGE */

    function toggleStage() {

        let val = document.getElementById("stage_required").value;

        document.getElementById("stage_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val === "No") {
            stageTotal = 0;
            document.getElementById("stage_total").value = 0;
            calculateFinal();
        }

    }

    function setStagePrice() {

        let price = document.getElementById("stage_theme")
            .selectedOptions[0].dataset.price || 0;

        stageTotal = parseInt(price);

        document.getElementById("stage_total").value = stageTotal;

        calculateFinal();

    }

    /* LIGHT */

    function toggleLighting() {

        let val = document.getElementById("lighting_required").value;

        document.getElementById("lighting_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val === "No") {
            lightTotal = 0;
            document.getElementById("light_total").value = lightTotal;
            calculateFinal();
        }

    }

    function setLightingPrice() {

        let price = document.getElementById("lighting_type")
            .selectedOptions[0].dataset.price || 0;

        lightTotal = parseInt(price);
        document.getElementById("light_total").value = lightTotal;

        calculateFinal();

    }

    /* ENTRY */

    function toggleEntry() {

        let val = document.getElementById("entry_required").value;

        document.getElementById("entry_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val === "No") {
            entryTotal = 0;
            document.getElementById("entry_total").value = 0;
            calculateFinal();
        }

    }

    function setEntryPrice() {

        let price = document.getElementById("entry_type")
            .selectedOptions[0].dataset.price || 0;

        entryTotal = parseInt(price);

        document.getElementById("entry_total").value = entryTotal;

        calculateFinal();

    }

    /* PHOTO */

    function togglePhoto() {

        let val = document.getElementById("photo_required").value;

        document.getElementById("photo_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val === "No") {
            photoTotal = 0;
            document.getElementById("photo_total").value = 0;
            calculateFinal();
        }

    }

    function setPhotoPrice() {

        let price = document.getElementById("photo_type")
            .selectedOptions[0].dataset.price || 0;

        photoTotal = parseInt(price);

        document.getElementById("photo_total").value = photoTotal;

        calculateFinal();

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
    /* FINAL TOTAL */

    function calculateFinal() {
    // Sangeet ના બધા જ ટોટલનો સરવાળો (Subtotal)
    let subtotal = (parseInt(djTotal) || 0) +
                   (parseInt(stageTotal) || 0) +
                   (parseInt(lightTotal) || 0) +
                   (parseInt(entryTotal) || 0) +
                   (parseInt(photoTotal) || 0) +
                   (parseInt(foodTotal) || 0);

    // PHP માંથી આવેલ ડિસ્કાઉન્ટ ટકાવારી લો
    let discPercent = <?= $discount_percentage ?>;
    let discAmt = Math.round((subtotal * discPercent) / 100);
    let final = subtotal - discAmt;

    // આંકડા બોક્સમાં સેટ કરો
    document.getElementById("sub_total").value = subtotal;
    document.getElementById("discount_amount").value = discAmt;
    document.getElementById("final_total").value = final; 

    calculatePayment(); 
}function calculateFinal() {
        // Sangeet ના બધા સર્વિસનો સરવાળો
        let subtotal = (parseInt(djTotal) || 0) + 
                       (parseInt(stageTotal) || 0) + 
                       (parseInt(lightTotal) || 0) + 
                       (parseInt(entryTotal) || 0) + 
                       (parseInt(photoTotal) || 0) + 
                       (parseInt(foodTotal) || 0);

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
    

    /* PAYMENT */

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
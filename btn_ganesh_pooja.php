<?php
session_start();
$_SESSION['event_type'] = "mandap";
include('../navbar/header.php');
include("../pages/db_connect.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

/* --- STEP 1: COUNT PAST BOOKINGS --- */
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

// તારું નવું લોજિક: 1st-10%, 2nd-0%, 3rd-15%
$discount_percentage = 0;
$headline_msg = "";

if ($total_past == 0) {
    $discount_percentage = 10;
    $headline_msg = "Special Welcome Offer: 10% OFF on your 1st Booking!";
} elseif ($total_past == 1) {
    $discount_percentage = 0;
    $headline_msg = ""; 
} elseif ($total_past >= 2) {
    $discount_percentage = 15;
    $headline_msg = "VIP Loyalty Reward: 15% OFF Applied!";
}

/* --- FORM SUBMIT LOGIC --- */
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... (તમારો બધો POST ડેટા પ્રોસેસિંગ અંહી એમનેમ રહેશે)
    $payment_method = $_POST['payment_method'] ?? '';
    if ($payment_method == "Online") {
        $_SESSION['booking_data'] = $_POST;
        $_SESSION['event_type'] = "mandap";
        header("Location: payment.php");
        exit();
    }
}

        /* ================= FORM SUBMIT ================= */
        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            $user_id = $_SESSION['user_id'];
            $username = $_SESSION['username'];

            $event_date = $_POST['event_date'] ?? '';
            $event_time = $_POST['event_time'] ?? '';
            $event_address = $_POST['event_address'] ?? '';

            /* ================= DATE VALIDATION ================= */
            if (empty($event_date))
                die("Please select event date.");
            if ($event_date < date('Y-m-d'))
                die("Past date booking not allowed.");

            $check_sangeet = mysqli_query($conn, "SELECT id FROM mandap_muhrat_bookings WHERE event_date='$event_date'");
            if (mysqli_num_rows($check_sangeet) > 0)
                die("This date is already booked.");

            // $check_mandap = mysqli_query($conn, "SELECT id FROM mandap_muhrat_bookings WHERE event_date='$event_date'");
            // if (mysqli_num_rows($check_mandap) > 0) die("This date already booked.");
        
            /* ================= FOOD ================= */

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

            /* ================= OPTIONS ================= */
            $pooja_required = $_POST['pooja_required'] ?? 'No';
            $pooja_package = $_POST['pooja_package'] ?? '';
            $pooja_total = intval($_POST['pooja_total'] ?? 0);

            $pandit_required = $_POST['pandit_required'] ?? 'No';
            $pandit_name = $_POST['pandit_name'] ?? '';
            $pandit_phone = $_POST['pandit_phone'] ?? '';
            $pandit_total = intval($_POST['pandit_total'] ?? 0);

            $decoration_required = $_POST['decoration_required'] ?? 'No';
            $decoration_theme = $_POST['decoration_theme'] ?? '';
            $decoration_total = intval($_POST['decoration_total'] ?? 0);

            $photography_required = $_POST['photography_required'] ?? 'No';
            $photography_package = $_POST['photography_package'] ?? '';
            $photography_total = intval($_POST['photography_total'] ?? 0);

            /* ================= FOOD ================= */
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
            $payable_amount = intval($_POST['payable_amount'] ?? 0);
            $total_amount = intval($_POST['total_amount'] ?? 0);

            /* ================= REDIRECT ONLINE PAYMENT ================= */
            if ($payment_method == "Online") {

                if (empty($_POST['guest_count'])) {
                    $_POST['guest_count'] = 0;
                }

                $_SESSION['booking_data'] = $_POST;
                $_SESSION['event_type'] = "mandap";

                header("Location: payment.php");
                exit();
            }

            /* ================= INSERT INTO DB ================= */
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
                '$user_id', '$username',
                '$event_date', '$event_time', '$event_address',
                '$pooja_required', '$pooja_package', '$pooja_total',
                '$pandit_required', '$pandit_name', '$pandit_phone', '$pandit_total',
                '$decoration_required', '$decoration_theme', '$decoration_total',
                '$photography_required', '$photography_package', '$photography_total',
                '$food_required', '$food_type', $guest_count, $food_total,
                '$total_amount', '$payment_method', '$payment_option', '$payable_amount'
            )";

            if (mysqli_query($conn, $sql)) {
                echo "<script>
alert('ganesh_pooja Booking Successful! Booking ID: $last_id');
window.location='home.php';
</script>";
                exit();
            } else {
                echo mysqli_error($conn);
            }
        }
        ?>
 
<<link rel="stylesheet" href="./event.css">
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
        <h2 style="text-align: center; color: #be185d;">Mandap Muhurat Booking</h2>

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
                    <option>Morning</option>
                    <option>Afternoon</option>
                    <option>Evening</option>
                </select>
            </div>

            <!-- Event Address -->
            <div class="form-group">
                <label>Event Address</label>
                <textarea name="event_address" required></textarea>
            </div>


            <hr>

            <!-- POOJA ITEMS -->

            <div class="form-group">
                <label>you need a Pooja Items ?</label>
                <select id="pooja" name="pooja_required" onchange="togglePooja()" required>
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="pooja_section" style="display:none;">
                <div class="form-group">
                    <label>Pooja Package</label>
                    <select id="pooja_package" name="pooja_package" onchange="setPoojaPrice()">
                        <option value="">Select</option>
                        <option data-price="5000">Basic Kit (₹5000)</option>
                        <option data-price="8000">Premium Kit (₹8000)</option>
                        <option data-price="12000">Complete Ritual (₹12000)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Pooja Total</label>
                    <input type="number" id="pooja_total" name="pooja_total" readonly>
                </div>
            </div>

            <hr>

            <!-- PANDIT -->


            <div class="form-group">
                <label>you need a Pandit Required?</label>
                <select id="pandit_required" name="pandit_required" onchange="togglePandit()" required>
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="pandit_section" style="display:none;">

                <div class="form-group">
                    <label>Select Pandit</label>
                    <select id="pandit_select" name="pandit_name" onchange="setPanditDetails()">
                        <option value="">Select Pandit</option>

                        <option data-phone="9876543210" data-price="7000">
                            Pandit Mahesh Shastri
                        </option>

                        <option data-phone="9123456780" data-price="9000">
                            Pandit Ramesh Joshi
                        </option>

                        <option data-phone="9988776655" data-price="11000">
                            Pandit Kiran Upadhyay
                        </option>

                        <option data-phone="9090909090" data-price="12000">
                            Pandit Harsh Tripathi
                        </option>

                    </select>
                </div>

                <div class="form-group">
                    <label>Pandit Contact Number</label>
                    <input type="text" id="pandit_phone" name="pandit_phone" readonly>
                </div>

                <div class="form-group">
                    <label>Pandit Package Amount</label>
                    <input type="number" id="pandit_total" name="pandit_total" readonly>
                </div>

            </div>

            <hr>

            <!-- DECORATION -->

            <div class="form-group">
                <label>you need a Decoration?</label>
                <select id="decoration" name="decoration_required" onchange="toggleDecoration()" required>
                    <option value="">select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="decoration_section" style="display:none;">
                <div class="form-group">
                    <label>Decoration Theme</label>
                    <select id="decoration_theme" name="decoration_theme" onchange="setDecorationPrice()">
                        <option value="">Select</option>
                        <option data-price="15000">Traditional (₹15000)</option>
                        <option data-price="25000">Floral (₹25000)</option>
                        <option data-price="40000">Royal (₹40000)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Decoration Total</label>
                    <input type="number" id="decoration_total" name="decoration_total" readonly>
                </div>
            </div>

            <hr>



            <!-- ================= PHOTOGRAPHY ================= -->
            <div class="form-group">
                <label>You need Photography?</label>
                <select id="photography_required" name="photography_required" onchange="togglePhotography()" required>
                    <option value="">Select</option>
                    <option>No</option>
                    <option>Yes</option>
                </select>
            </div>

            <div id="photography_section" style="display:none;">

                <div class="form-group">
                    <label>Photography Package</label>
                    <select id="photography_package" name="photography_package" onchange="setPhotographyPrice()">
                        <option value="">Select</option>
                        <option data-price="15000">Basic (₹15000)</option>
                        <option data-price="25000">Premium (₹25000)</option>
                        <option data-price="40000">Cinematic (₹40000)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Photography Total</label>
                    <input type="number" id="photography_total" name="photography_total" readonly>
                </div>

            </div>

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
    <div style="background: linear-gradient(135deg, #fce7f3 0%, #fbcfe8 100%); border: 2px dashed #ec4899; padding: 15px; border-radius: 12px; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; gap: 15px; color: #be185d;">
        <i class="fa-solid fa-gift pulse"></i>
        <span style="font-weight: bold;"><?= ($discount_percentage == 10) ? "Welcome Offer: 10% Discount Applied!" : "Loyalty Reward: 15% Discount Applied!" ?></span>
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

    let poojaTotal = 0, panditTotal = 0, decorationTotal = 0, foodTotal = 0, photographyTotal = 0;

    /* ================= POOJA ================= */
    function togglePooja() {
        document.getElementById("pooja_section").style.display =
            (document.getElementById("pooja").value === "Yes") ? "block" : "none";

        if (document.getElementById("pooja").value !== "Yes") {
            poojaTotal = 0;
            document.getElementById("pooja_total").value = 0;
            calculateFinal();
        }
    }

    function setPoojaPrice() {
        poojaTotal = document.getElementById("pooja_package")
            .selectedOptions[0].getAttribute("data-price") || 0;

        document.getElementById("pooja_total").value = poojaTotal;
        calculateFinal();
    }

    /* ================= PANDIT ================= */
    function togglePandit() {
        let val = document.getElementById("pandit_required").value;
        document.getElementById("pandit_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val !== "Yes") {
            panditTotal = 0;
            document.getElementById("pandit_total").value = 0;
            document.getElementById("pandit_phone").value = "";
            calculateFinal();
        }
    }

    function setPanditDetails() {
        let select = document.getElementById("pandit_select");

        panditTotal = select.selectedOptions[0].getAttribute("data-price") || 0;
        let phone = select.selectedOptions[0].getAttribute("data-phone") || "";

        document.getElementById("pandit_phone").value = phone;
        document.getElementById("pandit_total").value = panditTotal;

        calculateFinal();
    }

    /* ================= DECORATION ================= */
    function toggleDecoration() {
        document.getElementById("decoration_section").style.display =
            (document.getElementById("decoration").value === "Yes") ? "block" : "none";

        if (document.getElementById("decoration").value !== "Yes") {
            decorationTotal = 0;
            document.getElementById("decoration_total").value = 0;
            calculateFinal();
        }
    }

    function setDecorationPrice() {
        decorationTotal = document.getElementById("decoration_theme")
            .selectedOptions[0].getAttribute("data-price") || 0;

        document.getElementById("decoration_total").value = decorationTotal;
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


    /* ================= PHOTOGRAPHY ================= */
    function togglePhotography() {
        let val = document.getElementById("photography_required").value;

        document.getElementById("photography_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val !== "Yes") {
            photographyTotal = 0;
            document.getElementById("photography_total").value = 0;
            calculateFinal();
        }
    }

    function setPhotographyPrice() {
        photographyTotal = document.getElementById("photography_package")
            .selectedOptions[0].getAttribute("data-price") || 0;

        document.getElementById("photography_total").value = photographyTotal;
        calculateFinal();
    }

    /* ================= FINAL ================= */
    function calculateFinal() {
        // Mandap Muhurat ના બધા વેરીએબલ્સનો સરવાળો
        let subtotal = (parseInt(poojaTotal) || 0) + 
                       (parseInt(panditTotal) || 0) + 
                       (parseInt(decorationTotal) || 0) + 
                       (parseInt(foodTotal) || 0) + 
                       (parseInt(photographyTotal) || 0);

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
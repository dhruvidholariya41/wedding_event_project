<?php
session_start();
$_SESSION['event_type'] = "mehndi";
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
        $total_past += (int)$row['count'];
    }
}

// નવું લોજિક: 1st-10%, 2nd-0%, 3rd-15%
$discount_percentage = 0;
$headline_msg = "";
$offer_name = ""; // અંહી વ્યાખ્યાયિત કર્યું

if ($total_past == 0) {
    $discount_percentage = 10;
    $headline_msg = "Special Welcome Offer: 10% OFF on your 1st Booking!";
    $offer_name = "Welcome Offer (10% Off)";
} elseif ($total_past == 1) {
    $discount_percentage = 0;
    $headline_msg = ""; 
} elseif ($total_past >= 2) {
    $discount_percentage = 15;
    $headline_msg = "VIP Loyalty Reward: 15% OFF Applied!";
    $offer_name = "Loyalty Reward (15% Off)";
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // ... તમારો 600 લાઈનનો જૂનો POST ડેટા અને વેલિડેશન એમનેમ જ રાખવો ...

    // ================= GET POST DATA FIRST =================

    $event_date = $_POST['event_date'] ?? '';
    $event_time = $_POST['event_time'] ?? '';
    $event_address = $_POST['event_address'] ?? '';

    /* ================= MEHNDI ================= */

    $mehndi_required = $data['mehndi_required'] ?? 'No';
    $mehndi_name = $data['mehndi_name'] ?? '';
    $mehndi_contact = $data['mehndi_contact'] ?? '';
    $bride_package = $data['bride_package'] ?? '';
    $sider_count = $data['sider_count'] ?? 0;
    $mehndi_total = $data['mehndi_total'] ?? 0;

    $music_required = $_POST['music_required'] ?? 'No';
    $music_type = $_POST['music_type'] ?? '';
    $music_total = $_POST['music_total'] ?? 0;

    $photo_required = $_POST['photo_required'] ?? 'No';
    $photo_package = $_POST['photo_package'] ?? '';
    $photo_total = $_POST['photo_total'] ?? 0;

    $decoration_required = $_POST['decoration_required'] ?? 'No';
    $decoration_theme = $_POST['decoration_theme'] ?? '';
    $decoration_total = $_POST['decoration_total'] ?? 0;

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

    if ($_SERVER["REQUEST_METHOD"] == "POST") {

        $payment_method = $_POST['payment_method'];

        if ($payment_method == "Online") {

            if (empty($_POST['guest_count'])) {
                $_POST['guest_count'] = 0;
            }

            $_SESSION['booking_data'] = $_POST;
            $_SESSION['event_type'] = "mehndi";

            header("Location: payment.php");
            exit();
        }



    }

    // ================= DATE VALIDATION =================
    $check_date_query = "SELECT id FROM mehndi_bookings WHERE event_date = '$event_date'";
    $check_date_result = mysqli_query($conn, $check_date_query);

    if (mysqli_num_rows($check_date_result) > 0) {
        echo "<script>
            alert('This date is already booked. Please select another date.');
            window.history.back();
        </script>";
        exit();
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
    // ================= SIDER VALIDATION (1 to 99 allowed) =================
    if (!empty($sider_count)) {
        if (!preg_match('/^[1-9][0-9]?$/', $sider_count)) {
            die("Sider count must be between 1 and 99 only.");
        }
    }

    // ================= NUMERIC VALIDATION =================
    if (!is_numeric($guest_count) || $guest_count < 0) {
        die("Invalid guest count value.");
    }

    if (!is_numeric($sider_count) || $sider_count < 0) {
        die("Invalid sider count value.");
    }

    if (!is_numeric($mehndi_total) || $mehndi_total < 0) {
        die("Invalid mehndi total.");
    }

    if (!is_numeric($food_total) || $food_total < 0) {
        die("Invalid food total.");
    }

    if (!is_numeric($total_amount) || $total_amount < 0) {
        die("Invalid total amount.");
    }

    // ================= SESSION DATA =================
    $user_id = $_SESSION['user_id'];
    $username = $_SESSION['username'];

    // ================= INSERT QUERY =================
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

    if (mysqli_query($conn, $sql)) {

        $last_id = mysqli_insert_id($conn);

        echo "<script>
alert('Mehndi Booking Successful! Booking ID: $last_id');
window.location='home.php';
</script>";
        exit();

    } else {
        echo "Error: " . mysqli_error($conn);
    }
} ?>
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
        <h2 style="text-align: center; color: #be185d;">Mehndi Booking</h2>

        <?php if ($headline_msg != ""): ?>
            <div class="offer-banner pulse" style="background: #fff5f8; border: 2px dashed #ec4899; padding: 15px; border-radius: 10px; margin-bottom: 20px; text-align: center; color: #be185d;">
                <h3 style="margin: 0;">✨ <?= $headline_msg ?> ✨</h3>
            </div>
        <?php endif; ?>

        <form method="POST" class="booking-form">

            <input type="hidden" name="event_type" value="mehndi">
            <!-- Event Date -->
            <div class="form-group">
                <label>Event Date</label>
                <input type="date" name="event_date" min="<?= date('Y-m-d') ?>" required>
            </div>

            <!-- Event Time -->
            <div class="form-group">
                <label>Event Time</label>
                <select name="event_time" id="event_time" required>
                    <option value="">Select Time</option>
                    <option value="Morning (9AM - 12PM)">Morning (9AM - 12PM)</option>
                    <option value="Afternoon (1PM - 4PM)">Afternoon (1PM - 4PM)</option>
                    <option value="Evening (5PM - 7PM)">Evening (5PM - 7PM)</option>
                    <option value="Night (8PM - 10AM)">Night (8PM - 10AM)</option>
                </select>
            </div>

            <!-- Event Address -->
            <div class="form-group">
                <label>Event Address</label>
                <textarea name="event_address" required></textarea>
            </div>

            <hr>

            <!-- ================= MEHNDI ================= -->


            <?php
            $mehndi_query = mysqli_query($conn, "SELECT * FROM mehndi_artists");
            ?>
            <div class="form-group">
                <label>You need a mehndi artist?</label>
                <select id="mehndi" name="mehndi_required" onchange="toggleMehndi()" required>
                    <option value="">Select</option>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </div>

            <div id="mehndi_section" style="display:none;">

                <div class="form-group">
                    <select id="mehndi_artist" name="mehndi_name" onchange="setMehndiArtist()">

                        <option value="">Select Artist</option>

                        <?php
                        while ($row = mysqli_fetch_assoc($mehndi_query)) {
                            ?>

                            <option value="<?php echo $row['name']; ?>" data-contact="<?php echo $row['phone']; ?>">
                                <?php echo $row['name']; ?>
                            </option>

                        <?php } ?>

                    </select>
                </div>

                <div class="form-group">
                    <label>Contact Number</label>
                    <input type="text" id="mehndi_contact" name="mehndi_contact" readonly>
                </div>

                <div class="form-group">
                    <label>Bride Package</label>
                    <select id="bride_package" name="bride_package" onchange="calculateMehndi()">
                        <option value="">Select</option>
                        <option value="1500">Half Hand (₹1500)</option>
                        <option value="2500">Full Hand (₹2500)</option>
                        <option value="3500">Full Hand + Leg (₹3500)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>sider</label>
                    <input type="number" id="sider_count" name="sider_count" min="1" max="99" oninput="this.value=this.value.replace(/[^0-9]/g,''); 
if(this.value.length>2)this.value=this.value.slice(0,2); 
calculateMehndi();">
                </div>

                <div class="form-group">
                    <label>Mehndi Total</label>
                    <input type="number" id="mehndi_total" name="mehndi_total" readonly>
                </div>

            </div>

            <hr>

            <!-- ================= MUSIC ================= -->
            <div class="form-group">
                <label>You need a music?</label>
                <select id="music" name="music_required" onchange="toggleMusic()" required>
                    <option value="">Select</option>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </div>

            <div id="music_section" style="display:none;">
                <div class="form-group">
                    <label>Music Type</label>
                    <select id="music_type" name="music_type" onchange="setMusicPrice()">
                        <option value="">Select</option>
                        <option data-price="Dj 15000">DJ (₹15000)</option>
                        <option data-price="Singer 20000">Singer (₹20000)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Music Total</label>
                    <input type="number" id="music_total" name="music_total" readonly>
                </div>
            </div>

            <hr>

            <!-- ================= PHOTOGRAPHY ================= -->
            <div class="form-group">
                <label>You need a photography?</label>
                <select id="photo" name="photo_required" onchange="togglePhoto()" required>
                    <option value="">Select</option>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </div>

            <div id="photo_section" style="display:none;">
                <div class="form-group">
                    <label>Package</label>
                    <select id="photo_package" name="photo_package" onchange="setPhotoPrice()">
                        <option value="">Select</option>
                        <option data-price="Basic 8000">Basic (₹8000)</option>
                        <option data-price="Preminum 15000">Premium (₹15000)</option>
                        <option data-price="Cinamatic 25000">Cinematic (₹25000)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Photo Total</label>
                    <input type="number" id="photo_total" name="photo_total" readonly>
                </div>
            </div>

            <hr>

            <!-- ================= DECORATION ================= -->
            <div class="form-group">
                <label>You need a decoration?</label>
                <select id="decoration" name="decoration_required" onchange="toggleDecoration()" required>
                    <option value="">Select</option>
                    <option value="No">No</option>
                    <option value="Yes">Yes</option>
                </select>
            </div>

            <div id="decoration_section" style="display:none;">
                <div class="form-group">
                    <label>Theme</label>
                    <select id="decoration_theme" name="decoration_theme" onchange="setDecorationPrice()">
                        <option value="">Select</option>
                        <option data-price="Minimal Decoration 15000">Minimal (₹15000)</option>
                        <option data-price="Floral Decoration 25000">Floral (₹25000)</option>
                        <option data-price="Royal Decoration 40000">Royal (₹40000)</option>
                    </select>
                </div>
                <div class="form-group">
                    <label>Decoration Total</label>
                    <input type="number" id="decoration_total" name="decoration_total" readonly>
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
    <div style="background:#fff5f8; border:1px dashed #ec4899; padding:10px; border-radius:10px; margin-bottom:20px; color:#be185d; font-weight:bold; text-align:center;">
        ✨ <?= $offer_name ?> Applied!
    </div>
<?php endif; ?>

<div class="form-group">
    <label>Subtotal (Original)</label>
    <input type="number" id="sub_total" readonly>
</div>
<div class="form-group">
    <label>Discount Amount (₹)</label>
    <input type="number" id="discount_amount" readonly style="color:green;">
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

    let mehndiTotal = 0;
    let musicTotal = 0;
    let photoTotal = 0;
    let decorationTotal = 0;
    let foodTotal = 0;

    /* ================= MEHNDI ================= */
    function toggleMehndi() {
        let val = document.getElementById("mehndi").value;
        document.getElementById("mehndi_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val !== "Yes") {
            mehndiTotal = 0;
            document.getElementById("mehndi_total").value = 0;
            calculateFinal();
        }
    }

    function calculateMehndi() {
        let bride = parseInt(document.getElementById("bride_package").value) || 0;
        let sider = parseInt(document.getElementById("sider_count").value) || 0;

        mehndiTotal = bride + (sider * 1000);

        document.getElementById("mehndi_total").value = mehndiTotal;
        calculateFinal();
    }



    function setMehndiArtist() {

        let select = document.getElementById("mehndi_artist");

        let contact = select.options[select.selectedIndex].getAttribute("data-contact") || "";

        document.getElementById("mehndi_contact").value = contact;

    }

    /* ================= MUSIC ================= */
    function toggleMusic() {
        let val = document.getElementById("music").value;
        document.getElementById("music_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val !== "Yes") {
            musicTotal = 0;
            document.getElementById("music_total").value = 0;
            calculateFinal();
        }
    }

    function setMusicPrice() {
        let select = document.getElementById("music_type");
        let priceText = select.options[select.selectedIndex].getAttribute("data-price") || "0";
        let price = parseInt(priceText.replace(/\D/g, "")) || 0;

        musicTotal = price;
        document.getElementById("music_total").value = musicTotal;
        calculateFinal();
    }

    /* ================= PHOTO ================= */
    function togglePhoto() {
        let val = document.getElementById("photo").value;
        document.getElementById("photo_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val !== "Yes") {
            photoTotal = 0;
            document.getElementById("photo_total").value = 0;
            calculateFinal();
        }
    }

    function setPhotoPrice() {
        let select = document.getElementById("photo_package");
        let priceText = select.options[select.selectedIndex].getAttribute("data-price") || "0";
        let price = parseInt(priceText.replace(/\D/g, "")) || 0;

        photoTotal = price;
        document.getElementById("photo_total").value = photoTotal;
        calculateFinal();
    }

    /* ================= DECORATION ================= */
    function toggleDecoration() {
        let val = document.getElementById("decoration").value;
        document.getElementById("decoration_section").style.display =
            (val === "Yes") ? "block" : "none";

        if (val !== "Yes") {
            decorationTotal = 0;
            document.getElementById("decoration_total").value = 0;
            calculateFinal();
        }
    }

    function setDecorationPrice() {
        let select = document.getElementById("decoration_theme");
        let priceText = select.options[select.selectedIndex].getAttribute("data-price") || "0";
        let price = parseInt(priceText.replace(/\D/g, "")) || 0;

        decorationTotal = price;
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

    /* ================= FINAL ================= */
  function calculateFinal() {
        // બધી સર્વિસનો સરવાળો (MusicTotal પણ ઉમેર્યું)
        let subtotal = 
            (parseInt(mehndiTotal) || 0) + 
            (parseInt(musicTotal) || 0) + 
            (parseInt(photoTotal) || 0) + 
            (parseInt(decorationTotal) || 0) + 
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
    /* ================= PAYMENT ================= */
    function calculatePayment() {

        let finalTotal = parseInt(document.getElementById("final_total").value) || 0;
        let option = document.getElementById("payment_option").value;
        let payable = 0;

        if (option === "Half") {
            payable = Math.round(finalTotal * 0.5);
        }
        else if (option === "Full") {
            payable = finalTotal;
        }

        document.getElementById("payable_amount").value = payable;
    }

    /* Trigger payment calculation */
    document.getElementById("payment_option").addEventListener("change", calculatePayment);

</script>
<?php include('../navbar/footer.php'); ?>
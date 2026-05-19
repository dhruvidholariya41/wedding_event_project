<?php
session_start();
include("../pages/db_connect.php");

// લોગિન ચેક
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_GET['id']) && isset($_GET['type'])) {
    $booking_id = mysqli_real_escape_string($conn, $_GET['id']);
    $type = mysqli_real_escape_string($conn, $_GET['type']); // e.g. 'mehndi', 'haldi'
    $user_id = $_SESSION['user_id'];
    
    // ટેબલનું નામ નક્કી કરો (જો type માં '_bookings' ન હોય તો ઉમેરો)
    $table = (strpos($type, '_bookings') !== false) ? $type : $type . "_bookings";

    // ૧. બુકિંગની વિગતો મેળવો
    $query = "SELECT * FROM $table WHERE id = '$booking_id' AND user_id = '$user_id'";
    $result = mysqli_query($conn, $query);
    $booking = mysqli_fetch_assoc($result);

    if (!$booking) {
        echo "<script>alert('Booking not found!'); window.location='my_bookings.php';</script>";
        exit();
    }

    // જો સ્ટેટસ પહેલેથી 'Cancelled' હોય તો (Safety Check)
    if (isset($booking['status']) && $booking['status'] == 'Cancelled') {
        echo "<script>alert('This booking is already cancelled.'); window.location='my_bookings.php';</script>";
        exit();
    }

    // --- CONDITION 1: 24 HOURS CHECK ---
    $event_date = $booking['event_date'];
    $today = date('Y-m-d');
    
    // જો ઇવેન્ટની તારીખ અને આજની તારીખ વચ્ચે ૧ દિવસથી ઓછો તફાવત હોય
    if (strtotime($event_date) <= strtotime($today . ' +1 day')) {
        echo "<script>alert('Cancellation not allowed! You can only cancel 24 hours before the event date.'); window.location='my_bookings.php';</script>";
        exit();
    }

    // --- CONDITION 2: REFUND CALCULATION (20% CUT) ---
    $paid_amount = (float)$booking['payable_amount'];
    $refund_amount = $paid_amount * 0.80; // ૮૦% રિફંડ
    $charge = $paid_amount * 0.20; // ૨૦% પેનલ્ટી
    $username = $booking['username'];

    // --- PROCESS: INSERT INTO CANCELLED_BOOKINGS & DELETE FROM MAIN ---
    
    // ૨. કેન્સલ ટેબલમાં ડેટા ઇન્સર્ટ કરો (Admin Log માટે)
    $insert_cancel = "INSERT INTO cancelled_bookings (user_id, username, event_type, event_date, payable_amount) 
                      VALUES ('$user_id', '$username', '$type', '$event_date', '$paid_amount')";
    
    if (mysqli_query($conn, $insert_cancel)) {
        
        // ૩. હવે મેઈન ઇવેન્ટ ટેબલમાંથી રેકોર્ડ ડીલીટ કરો
        $delete_query = "DELETE FROM $table WHERE id = '$booking_id'";
        
        if (mysqli_query($conn, $delete_query)) {
            // ૪. સફળતાનો મેસેજ અને રિફંડ વિગતો બતાવો
            $msg = "Booking Cancelled Successfully!\\n\\nRefund Details:\\nPaid: ₹$paid_amount\\nCharge (20%): ₹$charge\\nRefund: ₹$refund_amount\\n\\nThis record is moved to Admin Cancel Log.";
            echo "<script>alert('$msg'); window.location='my_bookings.php';</script>";
            exit();
        } else {
            echo "Error deleting record: " . mysqli_error($conn);
        }
    } else {
        echo "Error moving to cancel log: " . mysqli_error($conn);
    }

} else {
    header("Location: my_bookings.php");
    exit();
}
?>
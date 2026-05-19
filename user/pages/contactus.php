<?php 
include('../navbar/header.php'); 
include('db_connect.php'); 

$success_msg = "";
$error_msg = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $message = mysqli_real_escape_string($conn, $_POST['message']);

    $sql = "INSERT INTO contact_messages (name, email, message) VALUES ('$name', '$email', '$message')";

    if (mysqli_query($conn, $sql)) {
        $success_msg = "✅ Your message has been sent successfully!";
    } else {
        $error_msg = "❌ Error: " . mysqli_error($conn);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Us - Golden Promise</title>
  <link rel="stylesheet" href="event.css">
</head>
<body>

  <section class="contact-hero">
    <div class>
      <h1>Contact Us</h1>
      <p>We’re here to help you plan the perfect wedding experience.</p>
    </div>
  </section>

  <div class="contact-container">
    <div class="contact-wrapper">
      
      <div class="contact-left">
        <h2>Send us a Message</h2>
        
        <?php if($success_msg != "") echo "<div class='msg-alert success'>$success_msg</div>"; ?>
        <?php if($error_msg != "") echo "<div class='msg-alert error'>$error_msg</div>"; ?>

        <form method="POST" action="" class="booking-form">
          <div class="form-group">
            <label>Full Name</label>
            <input type="text" name="name" placeholder="Your Name" required>
          </div>
          <div class="form-group">
            <label>Email Address</label>
            <input type="email" name="email" placeholder="Your Email" required>
          </div>
          <div class="form-group">
            <label>Message</label>
            <textarea name="message" rows="7" placeholder="How can we help?" required></textarea>
          </div>
          <button type="submit" class="booking-btn">Send Inquiry</button>
        </form>
      </div>

      <div class="contact-right">
        <div class="info-card">
          <h3 style="color:#e91e63; margin-top:0;">Get in Touch</h3>
          <p><b>📍 Location:</b> 5, Laxmi Society, Navsari</p>
          <p><b>📞 Phone:</b> +91 98765 43210</p>
          <p><b>✉️ Email:</b> info@goldenpromise.com</p>
        </div>

        <div class="map-box">
          <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d29808.53479520276!2d72.86718551083986!3d20.949830400000003!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3be0f78b962cc8c9%3A0xf746bb878b882c65!2sShree%20Ramji%20Mandir!5e0!3m2!1sen!2sin!4v1773731532054!5m2!1sen!2sin" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
      </div>

    </div>
  </div>

  <?php include('../navbar/footer.php'); ?>
</body>
</html>
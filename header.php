<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include('db_connect.php');

// Current page detect
$current_page = basename($_SERVER['PHP_SELF']);
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<link rel="stylesheet" href="../navbar/navbar.css">

<nav class="navbar-main">

    <div class="header-flex">
        <div class="logo-box">
            <img src="../../image/logo.jpeg" alt="Golden Promise Logo">
        </div>

        <div class="text-box">
            <h2 class="brand">Golden Promise</h2>
            <p class="tagline">Where Love Meets Celebration</p>
        </div>
    </div>

    <ul class="links">
        <li>
            <a href="../pages/home.php" class="<?= ($current_page == 'home.php') ? 'active' : '' ?>">Home</a>
        </li>

        <li class="dropdown">
            <a href="#" class="<?= in_array($current_page, ['mehndi.php','haldi.php','ganesh_pooja.php','dj_night.php','wedding.php','reception.php']) ? 'active' : '' ?>">
                Events ▾
            </a>
            <ul class="dropdown-menu">
                <li><a href="../pages/mehndi.php" class="<?= ($current_page == 'mehndi.php') ? 'active' : '' ?>">Mehndi</a></li>
                <li><a href="../pages/haldi.php" class="<?= ($current_page == 'haldi.php') ? 'active' : '' ?>">Haldi</a></li>
                <li><a href="../pages/ganesh_pooja.php" class="<?= ($current_page == 'ganesh_pooja.php') ? 'active' : '' ?>">Ganesh Pooja</a></li>
                <li><a href="../pages/dj_night.php" class="<?= ($current_page == 'dj_night.php') ? 'active' : '' ?>">Sangeet Night</a></li>
                <li><a href="../pages/wedding.php" class="<?= ($current_page == 'wedding.php') ? 'active' : '' ?>">Wedding</a></li>
                <li><a href="../pages/reception.php" class="<?= ($current_page == 'reception.php') ? 'active' : '' ?>">Reception</a></li>
            </ul>
        </li>

        <li>
            <a href="../pages/gallery.php" class="<?= ($current_page == 'gallery.php') ? 'active' : '' ?>">Gallery</a>
        </li>

        <li>
            <a href="../pages/contactus.php" class="<?= ($current_page == 'contactus.php') ? 'active' : '' ?>">Contact Us</a>
        </li>

        <li>
            <a href="../pages/reviews.php" class="<?= ($current_page == 'reviews.php') ? 'active' : '' ?>">Review</a>
        </li>

        <?php if (isset($_SESSION['user_id'])): ?>
            <?php 
                $u_email = $_SESSION['email'] ?? ''; 
                $unread_count = 0;

                if (!empty($u_email)) {
                    $noti_query = "SELECT COUNT(*) as total FROM contact_messages 
                                   WHERE email = '$u_email' 
                                   AND admin_reply IS NOT NULL 
                                   AND status = 'unread'";
                    $noti_res = mysqli_query($conn, $noti_query);
                    
                    if ($noti_res) {
                        $noti_data = mysqli_fetch_assoc($noti_res);
                        $unread_count = $noti_data['total'];
                    }
                }
            ?>
            <li class="nav-noti-item">
                <a href="../pages/notifications.php" class="updates-pill <?= ($current_page == 'notifications.php') ? 'active' : '' ?>">
                    <i class="fa-solid fa-bullhorn"></i> Updates 
                    <?php if ($unread_count > 0): ?>
                        <span class="pill-count"><?= $unread_count ?></span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endif; ?>
    </ul>

    <div class="nav-auth">
        <?php if (isset($_SESSION['user_id'])): ?>
        <div class="user-dropdown">
            <span class="user-btn">
                Hi, <?= htmlspecialchars($_SESSION['username']); ?> ▾
            </span>

            <div class="user-menu">
                <a href="../pages/my_account.php" class="<?= ($current_page == 'my_account.php') ? 'active' : '' ?>">My Account</a>
                <a href="../pages/my_bookings.php" class="<?= ($current_page == 'my_bookings.php') ? 'active' : '' ?>">My Bookings</a>
                <a href="../pages/logout.php">Logout</a>
            </div>
        </div>
        <?php else: ?>
        <a href="../pages/register.php" class="cta <?= ($current_page == 'register.php') ? 'active' : '' ?>">Register / Login</a>
        <?php endif; ?>
    </div>

</nav>
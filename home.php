<?php include('../navbar/header.php'); ?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
<link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
<link rel="stylesheet" href="./event.css">

<style>
/* --- FANTASTIC LOOK ENHANCEMENTS --- */

/* Hero Banner Animation */
.hero-overlay h1 {
    animation: fadeInDown 1.2s ease-out;
}
.hero-overlay p {
    animation: fadeInUp 1.5s ease-out;
}

/* Event Card Hover Effect */
.event-card {
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    overflow: hidden;
    border-radius: 15px;
    background: #fff;
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}
.event-card:hover {
    transform: translateY(-12px) scale(1.02);
    box-shadow: 0 20px 35px rgba(236, 72, 153, 0.2); 
}
.event-card img {
    transition: transform 0.6s ease;
}
.event-card:hover img {
    transform: scale(1.1);
}

/* Check Icons Animation */
.check-icon {
    background: #ec4899;
    color: white;
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    margin-right: 15px;
    transition: 0.3s;
}
.why-us-list li:hover .check-icon {
    transform: rotate(360deg);
}

/* --- ADVANCED FEATURES CSS --- */
.stats-section {
    display: flex;
    justify-content: space-around;
    padding: 60px 20px;
    background: #fff;
    text-align: center;
    flex-wrap: wrap;
    gap: 20px;
}
.stat-item h3 {
    font-size: 40px;
    color: #ec4899;
    margin-bottom: 5px;
    font-family: 'Arial Black', sans-serif;
}
.stat-item p {
    color: #64748b;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Custom Animations */
@keyframes fadeInDown {
    from { opacity: 0; transform: translateY(-30px); }
    to { opacity: 1; transform: translateY(0); }
}
@keyframes fadeInUp {
    from { opacity: 0; transform: translateY(30px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>

<section class="hero-banner">
  <div class="hero-overlay">
    <h1>Your Dream Event Starts Here</h1>
    <p>From weddings to celebrations, we plan every detail with perfection.</p>
    <a href="../pages/gallery.php" class="hero-btn">Explore Our Gallery <i class="fas fa-arrow-right"></i></a>
  </div>
</section>

<div class="promo-strip">
  <div class="promo-track">
    <div class="promo-items">
      <span>🎁 <b>Special Offer:</b> Get 10% Flat Discount on your 1st Booking!</span>
      <span>👑 <b>Loyalty Program:</b> Completed 3 bookings with us? Enjoy 15% OFF on your next celebration!</span>
    </div>
    <div class="promo-items">
      <span>🎁 <b>Special Offer:</b> Get 10% Flat Discount on your 1st Booking!</span>
      <span>👑 <b>Loyalty Program:</b> Completed 3 bookings with us? Enjoy 15% OFF on your next celebration!</span>
    </div>
  </div>
</div>

<section class="wedding-section">
  <h2 data-aos="fade-up">Our Wedding Events</h2>

  <div class="slider-wrapper">
    <button class="nav-btn prev" onclick="changeSlide(-1)">❮</button>

    <div class="slides">
      <div class="slide active">
        <div class="event-card" data-aos="zoom-in" data-aos-delay="100">
          <img src="../../image/mehndi_function.jpeg">
          <h3>Mehendi</h3>
          <a href="../pages/mehndi.php" class="book-btn">Book Now</a>
        </div>

        <div class="event-card" data-aos="zoom-in" data-aos-delay="200">
          <img src="../../image/haldi.jpeg">
          <h3>Haldi</h3>
          <a href="../pages/haldi.php" class="book-btn">Book Now</a>
        </div>

        <div class="event-card" data-aos="zoom-in" data-aos-delay="300">
          <img src="../../image/mandap_muhrat_function.jpg">
          <h3>Ganesh Pooja</h3>
          <a href="../pages/ganesh_pooja.php" class="book-btn">Book Now</a>
        </div>
      </div>

      <div class="slide">
        <div class="event-card">
          <img src="../../image/dj_function.jpg">
          <h3>Sangeet</h3>
          <a href="../pages/dj_night.php" class="book-btn">Book Now</a>
        </div>

        <div class="event-card">
          <img src="../../image/mrg_function.jpg">
          <h3>Wedding</h3>
          <a href="../pages/wedding.php" class="book-btn">Book Now</a>
        </div>

        <div class="event-card">
          <img src="../../image/reception.jpeg">
          <h3>Reception</h3>
          <a href="../pages/reception.php" class="book-btn">Book Now</a>
        </div>
      </div>
    </div>

    <button class="nav-btn next" onclick="changeSlide(1)">❯</button>
  </div>
</section>

<section class="stats-section" data-aos="fade-up">
    <div class="stat-item">
        <h3 class="counter" data-target="500">0</h3>
        <p>Events Planned</p>
    </div>
    <div class="stat-item">
        <h3 class="counter" data-target="150">0</h3>
        <p>Expert Staff</p>
    </div>
    <div class="stat-item">
        <h3 class="counter" data-target="100">0</h3>
        <p>Percent Satisfaction</p>
    </div>
</section>

<section class="why-us-section">
  <video autoplay muted loop playsinline class="bg-video">
    <source src="../../image/flower_petals_video.mp4" type="video/mp4">
  </video>
  
  <div class="why-us-container">
    <h2 class="why-title" data-aos="fade-down">Why Choose Us?</h2>

    <div class="why-content">
      <div class="why-image" data-aos="fade-right">
        <img src="../../image/home_sec3.jpg" alt="Why Choose Us" style="border-radius: 20px; border: 5px solid white;">
      </div>

      <div class="why-text" data-aos="fade-left">
        <ul class="why-us-list">
          <li>
            <span class="check-icon"><i class="fas fa-check"></i></span>
            <div>
              <h4>Experienced Wedding Planners</h4>
              <p>Professional team with years of successful event experience.</p>
            </div>
          </li>
          <li>
            <span class="check-icon"><i class="fas fa-check"></i></span>
            <div>
              <h4>Creative & Elegant Decorations</h4>
              <p>Unique themes and beautiful décor for every celebration.</p>
            </div>
          </li>
          <li>
            <span class="check-icon"><i class="fas fa-check"></i></span>
            <div>
              <h4>Complete Event Management</h4>
              <p>From planning to execution, everything handled smoothly.</p>
            </div>
          </li>
          <li>
            <span class="check-icon"><i class="fas fa-check"></i></span>
            <div>
              <h4>Affordable & Custom Packages</h4>
              <p>Flexible pricing plans designed as per your needs.</p>
            </div>
          </li>
        </ul>
      </div>
    </div>
  </div>
</section>

<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  // Initialize AOS
  AOS.init({
    duration: 1000,
    once: false
  });

  // Manual Slider Logic
  let current = 0;
  const slides = document.querySelectorAll(".slide");

  function changeSlide(direction) {
    slides[current].classList.remove("active");
    current += direction;
    if (current < 0) current = slides.length - 1;
    else if (current >= slides.length) current = 0;
    slides[current].classList.add("active");
  }

  // --- ADVANCED COUNTER LOGIC ---
  const counters = document.querySelectorAll('.counter');
  const speed = 200;

  const startCounters = () => {
    counters.forEach(counter => {
        const updateCount = () => {
            const target = +counter.getAttribute('data-target');
            const count = +counter.innerText.replace('%', '').replace('+', '');
            const inc = target / speed;

            if (count < target) {
                const nextCount = Math.ceil(count + inc);
                counter.innerText = nextCount;
                setTimeout(updateCount, 15);
            } else {
                counter.innerText = target + (target === 100 ? "%" : "+");
            }
        };
        updateCount();
    });
  };

  // Trigger counters when section enters viewport
  const observer = new IntersectionObserver((entries) => {
      if(entries[0].isIntersecting) {
          startCounters();
          observer.unobserve(entries[0].target);
      }
  }, { threshold: 0.5 });

  observer.observe(document.querySelector('.stats-section'));

</script>

<?php include('../navbar/footer.php'); ?>
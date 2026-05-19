<?php include('../navbar/header.php'); ?>
<link rel="stylesheet" href="../pages/event.css">

<style>
    /* Main container ne screen mujab set karva */
    .slidermain {
        position: relative;
        width: 100%;
        height: 100vh;
        overflow: hidden;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #000; /* Video load thaya pehla black screen rahe */
    }

    /* Background Video: Blur ane Jhakho karva mate */
    .bg-video {
        position: absolute;
        top: 50%;
        left: 50%;
        min-width: 100%;
        min-height: 100%;
        width: auto;
        height: auto;
        z-index: 0; 
        transform: translate(-50%, -50%);
        object-fit: cover;
        
        /* Blur ane Dark effect niche mujab chhe */
        filter: blur(10px) brightness(0.5); 
        opacity: 0.7; 
    }

    /* Slider Video ni upar rahe te mate */
    .coverflow-slider {
        position: relative;
        z-index: 10;
        width: 100%;
    }

    /* Slider images ni border ane shadow */
    .mahendi {
        width: 100%;
        height: auto;
        border-radius: 15px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.6);
        transition: transform 0.5s ease;
    }

    .book-wrapper {
        margin-top: 30px;
        text-align: center;
    }

    .book-btn {
        padding: 12px 35px;
        background: #e91e63;
        color: white;
        text-decoration: none;
        border-radius: 25px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(233, 30, 99, 0.4);
    }
</style>

<div class="slidermain">
    <video autoplay muted loop playsinline class="bg-video">
        <source src="../../image/btn_background.mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>

    <section class="coverflow-slider">
        <div class="slider">
            <div class="slide"><img class="mahendi" src="../../image/mahadi_event_1.jpeg"></div>
            <div class="slide"><img class="mahendi" src="../../image/mahedi_event_2.jpeg"></div>
            <div class="slide active"><img class="mahendi" src="../../image/mahedi_event_3.jpeg"></div>
            <div class="slide"><img class="mahendi" src="../../image/mahedi_event_4.jpeg"></div>
            <div class="slide"><img class="mahendi" src="../../image/mahedi_event_5.jpeg"></div>
        </div>

        <div class="book-wrapper">
            <a href="../pages/btn_mehndi.php" class="book-btn">Book Now</a>
        </div>
    </section>
</div>

<script>
    const slides = document.querySelectorAll(".slide");
    let current = 2; // Middle image active

    function updateSlider() {
        slides.forEach(slide => {
            slide.classList.remove("active", "prev", "next");
        });

        slides[current].classList.add("active");

        let prevIndex = (current - 1 + slides.length) % slides.length;
        let nextIndex = (current + 1) % slides.length;

        slides[prevIndex].classList.add("prev");
        slides[nextIndex].classList.add("next");
    }

    function autoSlide() {
        current = (current + 1) % slides.length;
        updateSlider();
    }

    // Slider auto transition
    setInterval(autoSlide, 3000);

    // Initial load
    updateSlider();
</script>

<?php include('../navbar/footer.php'); ?>
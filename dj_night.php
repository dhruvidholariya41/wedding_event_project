<?php include('../navbar/header.php'); ?>
<link rel="stylesheet" href="../pages/event.css">
<div class="slidermain">
  <section class="coverflow-slider">

    <div class="slider">

      <div class="slide">
        <img class="mahendi" src="../../image/sangeet_img1.jpeg">
      </div>

      <div class="slide">
        <img class="mahendi" src="../../image/sangeet_img2.jpeg">
      </div>

      <div class="slide active">
        <img class="mahendi" src="../../image/sangeet_img3.jpeg">
      </div>

      <div class="slide">
        <img class="mahendi" src="../../image/sangeet_img4.jpeg">
      </div>

      <div class="slide">
        <img class="mahendi" src="../../image/sangeet_img5.jpeg">
      </div>

    </div>

    <div class="book-wrapper">
      <a href="../pages/btn_dj_night.php" class="book-btn">Book</a>
    </div>



  </section>

</div>

<script>
  const slides = document.querySelectorAll(".slide");
  let current = 2; // middle image active

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

  setInterval(autoSlide, 3000); // 3 sec auto slide

  updateSlider();
</script>
<?php include('../navbar/footer.php'); ?>
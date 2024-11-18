/*
 ===== Swiper =====
*/
// Thumbnail Swiper
var thumbSwiper = new Swiper(".bg_slider-thumbs", {
  loop: false,
  initialSlide: 0,
  spaceBetween: 0,
  slidesPerView: 4,
  breakpoints: {
    // Mobil és tablet felbontásokhoz
    0: {
      slidesPerView: 2, // Mobil és kisebb kijelzők
    },
    1100: {
      slidesPerView: 3, // Tabletek
    },
    1600: {
      slidesPerView: 4, // Nagyobb kijelzők
    },
  },
  watchSlidesVisibility: true,
  watchSlidesProgress: true,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  on: {
    init: function () {
      this.slideTo(0, 0, false); // Kötelezően az 1. elemnél kezdődik
      this.update();
    },
    slideChange: function () {
      // Frissíti a csúszka pozícióját a slide váltásával
      const totalSlides = this.slides.length - 1;
      const progress = (this.realIndex / totalSlides) * 100;
      document.querySelector(".thumb-slider").value = progress;
    },
  },
});

// Main Swiper with thumbnails linked
var mainSwiper = new Swiper(".bg_slider", {
  loop: true,
  slidesPerView: 1,
  spaceBetween: 0,
  speed: 1000,
  thumbs: {
    swiper: thumbSwiper, // thumbnail swiper link
  },
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
    pauseOnMouseEnter: true,
  },
  watchSlidesVisibility: true,
  watchSlidesProgress: true,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  on: {
    slideChange: function () {
      const activeIndex = this.realIndex; // Aktuális index a loop miatt

      // Minden thumbSlide-ra alapértelmezett inaktív stílus
      thumbSwiper.slides.forEach((slide) => {
        slide.classList.remove("active");
        slide.classList.add("inactive");
      });
      this.update();

      // Az aktuális thumbSlide aktív stílusa
      const activeThumbSlide = thumbSwiper.slides[activeIndex];
      if (activeThumbSlide) {
        activeThumbSlide.classList.add("active");
        activeThumbSlide.classList.remove("inactive");
      }
    },
  },
});
var productSwiper = new Swiper(".topProduct_slider", {
  loop: true,
  centeredSlides: true,
  spaceBetween: 0,
  speed: 1000,

  autoplay: {
    delay: 3000,
    disableOnInteraction: false,
    pauseOnMouseEnter: true,
  },
  watchSlidesVisibility: true,
  watchSlidesProgress: true,
});

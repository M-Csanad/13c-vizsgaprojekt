/*
 ===== Swiper =====
 */
// Thumbnail Swiper
var thumbSwiper = new Swiper(".bg_slider-thumbs", {
  loop: false,
  initialSlide: 0,
  spaceBetween: 0,
  slidesPerView: 4,
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
  },
});

// Main Swiper with thumbnails linked
var mainSwiper = new Swiper(".bg_slider", {
  loop: true,
  slidesPerView: 1,
  spaceBetween: 0,
  speed: 2000,
  thumbs: {
    swiper: thumbSwiper, // Link the thumbnail swiper
  },
  autoplay: {
    delay: 4000,
    disableOnInteraction: false,
    pauseOnMouseEnter: true,
  },
  watchSlidesVisibility: true,
  watchSlidesProgress: true,
});

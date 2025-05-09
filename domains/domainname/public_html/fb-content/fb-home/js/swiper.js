/*
 ===== Swiper =====
*/

function $id(id) {
  return document.getElementById(id);
}

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
  watchOverflow: false,
  thumbs: {
    swiper: thumbSwiper, // thumbnail swiper link
  },
  autoplay: {
    delay: 4000,
    disableOnInteraction: true,
    pauseOnMouseEnter: true,
  },
  watchSlidesVisibility: true,
  watchSlidesProgress: true,
  navigation: {
    nextEl: ".swiper-button-next",
    prevEl: ".swiper-button-prev",
  },
  on: {
    init: function () {
      removeDisabledFromNavButtons();
    },
    slideChange: function () {
      const activeIndex = this.realIndex; // Aktuális index a loop miatt

      removeDisabledFromNavButtons();

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

// Add this function to generate stars based on rating
function generateStars() {
  const reviewStars = document.querySelectorAll(".review-stars");

  reviewStars.forEach((element) => {
    const rating = parseFloat(element.dataset.rating) || 0;
    const fullStars = Math.floor(rating);
    const halfStar = rating % 1 >= 0.5;
    const totalStars = 5;

    element.innerHTML = "";
    for (let i = 0; i < totalStars; i++) {
      const star = document.createElement("span");
      if (i < fullStars) {
        star.classList.add("filled");
      } else if (i === fullStars && halfStar) {
        star.classList.add("half");
      }
      element.appendChild(star);
    }
  });
}

document.addEventListener("DOMContentLoaded", function () {
  // Generate stars for ratings
  generateStars();
});

function removeDisabledFromNavButtons() {
  const nextButtons = document.querySelectorAll(".swiper-button-next");
  const prevButtons = document.querySelectorAll(".swiper-button-prev");

  // Minden .swiper-button-next gombon eltávolítjuk a disable-t
  nextButtons.forEach((btn) => {
    if (btn.classList.contains("swiper-button-disabled")) {
      btn.classList.remove("swiper-button-disabled");
      btn.removeAttribute("aria-disabled");
    }
  });

  // Minden .swiper-button-prev gombon eltávolítjuk a disable-t
  prevButtons.forEach((btn) => {
    if (btn.classList.contains("swiper-button-disabled")) {
      btn.classList.remove("swiper-button-disabled");
      btn.removeAttribute("aria-disabled");
    }
  });
}

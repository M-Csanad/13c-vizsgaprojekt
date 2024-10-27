document.addEventListener("DOMContentLoaded", function () {
  const thumbSlides = document.querySelectorAll(
    ".bg_slider-thumbs .swiper-slide"
  );
  const thumbContainer = document.querySelector(".bg_slider-thumbs");

  thumbSlides.forEach((slide) => {
    slide.addEventListener("click", function () {
      // Az összes slide-ra eltávolítjuk az 'active' osztályt
      thumbSlides.forEach((s) => {
        s.classList.remove("active");
        s.classList.add("inactive"); // Az összes többi inaktív lesz
      });
      // Hozzáadjuk az 'active' osztályt a kattintott elemhez
      slide.classList.add("active");
      slide.classList.remove("inactive");
    });
  });

  // Ha a konténeren kívülre kattintasz, minden visszatér az alapértelmezett állapotba
  document.addEventListener("click", function (event) {
    if (!thumbContainer.contains(event.target)) {
      thumbSlides.forEach((s) => {
        s.classList.remove("active", "inactive");
      });
    }
  });
});

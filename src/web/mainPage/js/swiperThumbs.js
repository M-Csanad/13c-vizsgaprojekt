document.addEventListener("DOMContentLoaded", function () {
  const thumbSlides = document.querySelectorAll(
    ".bg_slider-thumbs .swiper-slide"
  );
  const thumbContainer = document.querySelector(".bg_slider-thumbs");
  const thumbSlider = document.querySelector(".thumb-slider");
  thumbSlider.addEventListener("input", function (e) {
    const index = Math.round(
      (e.target.value / 100) * (thumbSwiper.slides.length - 1)
    );
    thumbSwiper.slideTo(index);
  });

  const tooltip = document.getElementById("thumb-slider_tooltip");

  thumbSlider.addEventListener("mouseenter", () => {
    console.log("mouseenter");
    tooltip.style.visibility = "visible";
    tooltip.style.opacity = "1";
  });

  thumbSlider.addEventListener("mousemove", (e) => {
    console.log("mousemove");
    tooltip.style.left = `${e.pageX + 15}px`;
    tooltip.style.top = `${e.pageY + 15}px`;
  });

  thumbSlider.addEventListener("mouseleave", () => {
    console.log("mouseleave");
    tooltip.style.visibility = "hidden";
    tooltip.style.opacity = "0";
  });

  //Halo generálás rész
  thumbSlides.forEach((slide) => {
    const point = slide.querySelector(".halo .point");

    // Véletlenszerű kezdőszög a forgatáshoz
    const randomStart = Math.random() * 360;
    point.style.animationDelay = `-${randomStart}s`;

    slide.addEventListener("click", function () {
      thumbSlides.forEach((s) => {
        s.classList.remove("active");
        s.classList.add("inactive"); // Az összes többi inaktív lesz
      });
      slide.classList.add("active");
      slide.classList.remove("inactive");
    });
  });

  document.addEventListener("click", function (event) {
    if (!thumbContainer.contains(event.target)) {
      thumbSlides.forEach((s) => {
        s.classList.remove("active", "inactive");
      });
    }
  });
});

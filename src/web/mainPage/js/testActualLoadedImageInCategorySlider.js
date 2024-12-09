window.addEventListener("resize", function () {
  const slides = document.querySelectorAll(".swiper-slide");

  slides.forEach((slide, index) => {
    // Keresd meg a picture elemen belüli img elemet
    const imgElement = slide.querySelector(".img-wrapper picture img");

    if (imgElement) {
      // Az aktuálisan használt kép URL-je
      const currentSrc = imgElement.currentSrc;

      // Írd ki az indexet és az aktuális képet a konzolra
      console.log(`Slide ${index + 1}:`, currentSrc);
    } else {
      console.log(`Slide ${index + 1}: Nincs kép`);
    }
  });
});

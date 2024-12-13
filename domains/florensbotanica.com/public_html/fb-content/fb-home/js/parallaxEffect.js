function createParallaxEffect(selector) {
  const elements = document.querySelectorAll(selector);

  function handleScroll() {
    elements.forEach((element) => {
      const rect = element.getBoundingClientRect();
      const speed = parseFloat(element.getAttribute("data-speed"));
      const translateY = rect.top * speed;
      element.style.transform = `translateY(${translateY}px)`;
    });
  }

  function optimizedScroll() {
    requestAnimationFrame(handleScroll);
  }

  window.addEventListener("scroll", optimizedScroll);
  handleScroll();
}

createParallaxEffect(".Parallax_box");

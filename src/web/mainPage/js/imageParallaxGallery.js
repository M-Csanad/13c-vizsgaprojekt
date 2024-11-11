const ImgGallery_container = document.querySelector(".ImgGallery_container");
let parallaxActive = false; // Változó, hogy nyomon kövessük az állapotot
let throttleMouseMove; // A throttle-olt eseményfigyelő helye

function shouldRunScript() {
  const isPortrait = window.matchMedia("(orientation: portrait)").matches;
  const isNarrowScreen = window.innerWidth < 1024;

  return !(isPortrait || isNarrowScreen);
}

function initializeParallax() {
  const images = document.querySelectorAll(".media_box");

  images.forEach((img) => {
    const randomSpeed = 1.2;
    img.setAttribute("data-speed", randomSpeed);
  });

  // Throttle függvény az esemény kezelés csökkentéséhez
  function throttle(callback, limit) {
    let waiting = false;
    return function (...args) {
      if (!waiting) {
        callback.apply(this, args);
        waiting = true;
        setTimeout(() => {
          waiting = false;
        }, limit);
      }
    };
  }

  throttleMouseMove = throttle((event) => {
    const rect = ImgGallery_container.getBoundingClientRect();
    const x = (event.clientX - rect.left) / rect.width;
    const y = (event.clientY - rect.top) / rect.height;

    images.forEach((img) => {
      const speed = parseFloat(img.getAttribute("data-speed"));
      let moveX = (x - 0.5) * -20 * speed * 2;
      let moveY = (y - 0.5) * -20 * speed * 2;

      const imgWidth = img.offsetWidth;
      const imgHeight = img.offsetHeight;
      const currentTransform = img.style.transform;

      // Ellenőrizzük a határokat
      if (parseFloat(img.style.left) + moveX < 0) {
        moveX = -parseFloat(img.style.left);
      } else if (
        parseFloat(img.style.left) + imgWidth + moveX >
        ImgGallery_container.clientWidth
      ) {
        moveX =
          ImgGallery_container.clientWidth -
          parseFloat(img.style.left) -
          imgWidth;
      }

      if (parseFloat(img.style.top) + moveY < 0) {
        moveY = -parseFloat(img.style.top);
      } else if (
        parseFloat(img.style.top) + imgHeight + moveY >
        ImgGallery_container.clientHeight
      ) {
        moveY =
          ImgGallery_container.clientHeight -
          parseFloat(img.style.top) -
          imgHeight;
      }

      // Csak akkor alkalmazzuk a transformot, ha tényleg változott az érték
      const newTransform = `translate(${moveX}px, ${moveY}px)`;
      if (newTransform !== currentTransform) {
        img.style.transform = newTransform;
      }
    });
  }, 15); // 15ms limit a throttle-hoz

  ImgGallery_container.addEventListener("mousemove", throttleMouseMove);
  parallaxActive = true;
}

function destroyParallax() {
  if (parallaxActive) {
    ImgGallery_container.removeEventListener("mousemove", throttleMouseMove);
    parallaxActive = false;
  }
}

// Figyeljük az ablak méretének változását
window.addEventListener("resize", () => {
  if (shouldRunScript()) {
    if (!parallaxActive) {
      initializeParallax();
      console.log("A szkript fut!");
    }
  } else {
    destroyParallax();
  }
});

// Első ellenőrzés az oldal betöltésekor
if (shouldRunScript()) {
  initializeParallax();
} else {
}

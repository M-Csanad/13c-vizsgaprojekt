const ImgGallery_container = document.querySelector(".ImgGallery_container");
const images = document.querySelectorAll(".media_box");

images.forEach((img) => {
  //const randomSpeed = (Math.random() * (0.24 - 0.12 + 1) + 0.12).toFixed(2);
  const randomSpeed = 1.2;
  img.setAttribute("data-speed", randomSpeed);
});

//Kép parallax

// Throttle függvény, hogy csökkentsük az esemény kezelését
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

ImgGallery_container.addEventListener(
  "mousemove",
  throttle((event) => {
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
  }, 15) // 15ms limit a throttle-hoz
);

ImgGallery_container.add;

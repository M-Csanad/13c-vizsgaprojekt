const video = document.getElementById("welcomeVideo");
const wrapper = document.querySelector(".videoScene_wrapper");
let timer; // Időzítő változó

wrapper.addEventListener("mousemove", (event) => {
  clearTimeout(timer); // Időzítő törlése, ha az egér mozog
  const rect = wrapper.getBoundingClientRect();
  const x = (event.clientX - rect.left) / rect.width;
  const y = (event.clientY - rect.top) / rect.height;

  const moveX = (x - 0.5) * -20; // X tengely elmozdulás
  const moveY = (y - 0.5) * -20; // Y tengely elmozdulás

  // A videó mozgatása és a nagyítás visszaállítása
  video.style.transition = "transform 0.4s ease-out"; // Automatikus nagyítás, ha az egér nem mozog
  video.style.transform = `translate(${moveX}%, ${moveY}%) scale(1.2)`;

  // Beállítunk egy 0.5 másodperces időzítőt
  timer = setTimeout(() => {
    video.style.transition = "transform 1s ease-out"; // Automatikus nagyítás, ha az egér nem mozog
    video.style.transform = "translate(0%, 0%) scale(1.3)"; // Automatikus nagyítás, ha az egér nem mozog
  }, 800);
});

wrapper.addEventListener("mouseleave", () => {
  clearTimeout(timer); // Időzítő törlése, ha az egér elhagyja a területet
  video.style.transition = "transform 0.4s ease-out"; // Automatikus nagyítás, ha az egér nem mozog
  video.style.transform = "translate(0%, 0%) scale(1.2)"; // Eredeti állapot visszaállítása
});

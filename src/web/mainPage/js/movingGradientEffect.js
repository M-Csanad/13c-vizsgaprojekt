const container = document.getElementById("brand-philosophy_container");

let lastMouseX = null;
let lastMouseY = null;
let scrollTimeout = null; // Időzítő a görgetés figyelésére

// Funkció a pozíciók frissítéséhez CSS változóban
function updatePosition(x, y) {
  container.style.setProperty("--x", `${x}px`);
  container.style.setProperty("--y", `${y}px`);
}

// Egér belépése a konténerbe
container.addEventListener("mouseenter", () => {
  container.classList.add("show-gradient"); // Gradient megjelenítése 2 mp alatt
});

// Egérmozgás figyelése és pozíciók mentése
container.addEventListener("mousemove", (event) => {
  const containerRect = container.getBoundingClientRect();
  lastMouseX = event.clientX - containerRect.left;
  lastMouseY = event.clientY - containerRect.top;

  console.log("Hover pozíció:", lastMouseX, lastMouseY);
  console.log("Container Rect Hover:", containerRect);
  updatePosition(lastMouseX, lastMouseY);
});

// Egér kilépése a konténerből
container.addEventListener("mouseleave", () => {
  container.classList.remove("show-gradient"); // Gradient eltűnése
});

// Görgetési esemény figyelése
window.addEventListener("scroll", () => {
  container.classList.remove("show-gradient"); // Gradient eltűnése scroll közben

  // Ha van aktív időzítő, töröljük azt
  if (scrollTimeout) {
    clearTimeout(scrollTimeout);
  }

  // Időzítő újraindítása a görgetés végén
  scrollTimeout = setTimeout(() => {
    container.classList.add("show-gradient"); // Gradient újra megjelenik, ha a görgetés megáll
  }, 500); // 500 ms késleltetés után
});

const notifBlob = document.getElementById("notifBlob");
let blobScrollTimeout = null;
let mouseMoveTimeout = null;

// Egér mozgásának figyelése és `notifBlob` pozíciójának frissítése
window.addEventListener("mousemove", (event) => {
  // `notifBlob` eltüntetése azonnal, amikor az egér mozog
  notifBlob.style.opacity = 0;

  // Frissítjük a `notifBlob` pozícióját az egér mellett
  notifBlob.style.left = `${event.clientX + 10}px`; // Kicsit eltolva az egér pozíciójától
  notifBlob.style.top = `${event.clientY + 10}px`;

  // Ha van aktív egér-időzítő, töröljük azt
  if (mouseMoveTimeout) {
    clearTimeout(mouseMoveTimeout);
  }

  // Beállítunk egy időzítőt, hogy megjelenjen a `notifBlob`, ha az egér 500 ms-ig nem mozog
  mouseMoveTimeout = setTimeout(() => {
    notifBlob.style.opacity = 1; // `notifBlob` megjelenik, ha az egér mozgása megáll
  }, 500); // 500 ms késleltetés után jelenik meg a `notifBlob`
});

// Görgetési esemény figyelése
window.addEventListener("scroll", () => {
  notifBlob.style.opacity = 0; // `notifBlob` eltűnik görgetés közben

  // Ha van aktív scroll-időzítő, töröljük azt
  if (blobScrollTimeout) {
    clearTimeout(blobScrollTimeout);
  }

  // Időzítő beállítása a görgetés végére
  blobScrollTimeout = setTimeout(() => {
    // Csak akkor jelenítjük meg újra a `notifBlob`-ot, ha az egér nem mozog
    if (!mouseMoveTimeout) {
      notifBlob.style.opacity = 1; // `notifBlob` megjelenik, ha a görgetés megáll és az egér nem mozog
    }
  }, 500); // 500 ms késleltetés után jelenik meg a `notifBlob`
});

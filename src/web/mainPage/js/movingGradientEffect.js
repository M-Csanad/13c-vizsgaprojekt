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

/*
 * Blob section
 */
const notifBlob = document.getElementById("notifBlob");
const brandPhilosophyContainer = document.getElementById(
  "brand-philosophy_container"
);
let blobScrollTimeout = null;
let mouseMoveTimeout = null;

// Funkció az egér pozíciójának ellenőrzésére a containeren belül
function isMouseInContainer(event) {
  const containerRect = brandPhilosophyContainer.getBoundingClientRect();
  if (!event) return false;
  const mouseX = event.clientX;
  const mouseY = event.clientY;

  return (
    mouseX >= containerRect.left &&
    mouseX <= containerRect.right &&
    mouseY >= containerRect.top &&
    mouseY <= containerRect.bottom
  );
}

// Egér mozgásának figyelése és `notifBlob` pozíciójának frissítése
window.addEventListener("mousemove", (event) => {
  if (isMouseInContainer(event)) {
    notifBlob.style.left = `${event.clientX + 10}px`;
    notifBlob.style.top = `${event.clientY + 10}px`;

    if (mouseMoveTimeout) {
      clearTimeout(mouseMoveTimeout);
    }

    mouseMoveTimeout = setTimeout(() => {
      notifBlob.style.opacity = 1;
    }, 500);
  } else {
    notifBlob.style.opacity = 0;
  }
});

// Görgetési esemény figyelése
window.addEventListener("scroll", () => {
  notifBlob.style.opacity = 0;

  if (blobScrollTimeout) {
    clearTimeout(blobScrollTimeout);
  }

  blobScrollTimeout = setTimeout(() => {
    const containerRect = brandPhilosophyContainer.getBoundingClientRect();
    const mouseX = parseInt(notifBlob.style.left || "0", 10) - 10;
    const mouseY = parseInt(notifBlob.style.top || "0", 10) - 10;

    if (
      mouseX >= containerRect.left &&
      mouseX <= containerRect.right &&
      mouseY >= containerRect.top &&
      mouseY <= containerRect.bottom
    ) {
      notifBlob.style.opacity = 1;
    }
  }, 500);
});

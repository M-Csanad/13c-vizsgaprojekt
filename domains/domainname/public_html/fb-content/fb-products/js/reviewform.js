import APIFetch from '/fb-content/assets/js/apifetch.js';
import Popup from '/fb-content/assets/js/popup.js';

const isMobile = (getComputedStyle(document.body).getPropertyValue("--is-mobile") == "1") || (('ontouchstart' in window) || (navigator.msMaxTouchPoints > 0));

const form = document.querySelector(".review-form");
const reviewStarsContainer = document.querySelector(".review-form-stars");
const reviewStars = document.querySelectorAll(".review-form .star");
let hiddenInput = null;
if (reviewStarsContainer) {
  hiddenInput = reviewStarsContainer.querySelector("[name=stars-input]");
}
const reviewSubmitter = document.querySelector(".review-form > .submit");

const sendButton = document.querySelector(".send-button");

if (sendButton) {
  sendButton.addEventListener("mouseenter", () => {
    sendButton.classList.add("hovered");
  })

  sendButton.addEventListener("mouseleave", () => {
    sendButton.classList.remove("hovered");
  })

  sendButton.addEventListener("click", () => {
    sendButton.classList.remove("hovered");
    sendButton.classList.add("sent");
  })
}

let isActive = false;
let isSet = false;
let didSubmit = false;

// Értékelés beküldésének kezelése
if (reviewSubmitter) {
  reviewSubmitter.addEventListener("click", async () => {
    if (didSubmit) return;

    let title = form.querySelector("input[type=text]");
    let body = form.querySelector("textarea");
    let rating = hiddenInput.value;

    if (rating == "null") {
      reviewStarsContainer.classList.add("invalid");
      return;
    }

    if (!title.value) {
      title.classList.add("invalid");
      return;
    }

    if (!body.value) {
      body.classList.add("invalid");
      return;
    }

    const response = await APIFetch("/api/review", "PUT",
      {
        "review-title": title.value,
        "review-body": body.value,
        "rating": rating
      },
    true);

    if (response.ok) {
      const result = await response.json();
      reviewSubmitter.classList.remove("unsuccessful");
      reviewSubmitter.classList.add("successful");

      const successText = reviewSubmitter.querySelector('.success .send-text');
      if (successText) {
        successText.textContent = "Sikeres küldés!";
      }
      didSubmit = true;

      // Oldal újratöltése rövid késleltetés után az új értékelés megjelenítéséhez
      setTimeout(() => {
        window.location.reload();
      }, 2000);
    }
    else {
      // Hibaüzenet megjelenítése
      try {
        const errorData = await response.json();
        const errorMessage = errorData.message || "Sikertelen küldés!";

        // Felugró ablak megjelenítése minden hibához
        try {
          const popup = new Popup(
            "Értékelés nem lehetséges",
            errorMessage,
            (confirmed) => {
              // Csak zárja be a felugró ablakot
            },
            "Rendben" // Egyéni gombszöveg
          );
          popup.open();
        } catch (popupError) {
          console.error("Hiba a felugró ablak megjelenítésekor:", popupError);
          // Visszalépés az eredeti hibajelzésre
          showDefaultErrorMessage(errorMessage);
        }
      } catch (e) {
        console.error("Hiba a válasz értelmezésekor:", e);
        showDefaultErrorMessage("Sikertelen küldés!");
      }
    }
  });
}

// Segédfüggvény az alapértelmezett hibaüzenet megjelenítéséhez a gombban
function showDefaultErrorMessage(message) {
  const errorText = reviewSubmitter.querySelector('.unsuccessful .send-text');
  if (errorText) {
    errorText.textContent = message;
  }
  reviewSubmitter.classList.remove("successful");
  reviewSubmitter.classList.add("unsuccessful");
}

if (reviewStarsContainer && reviewStars) {
  reviewStarsContainer.addEventListener("mouseleave", () => {
    if (hiddenInput.value && hiddenInput.value !== "null") {
      if (!isSet) {
        setStarsFromValue(parseFloat(hiddenInput.value)); // Biztosítjuk, hogy az érték float típusú
      }
    } else {
      resetStars(); // Alapállapotba állítja a csillagokat
    }
    isActive = false;
  });

  reviewStarsContainer.addEventListener("mouseenter", () => {
    isActive = true; // Aktiválja az állapotot, ha az egér belép a csillagba
  });

  reviewStars.forEach((star) => {
    if (!isMobile) {
      star.addEventListener("click", (e) => handleClick(e, star));
    }
    star.addEventListener("mousemove", (e) => {
      throttleHandleStarMove(star, e)
    });
    star.addEventListener("mouseenter", (e) => {
      if (!isSet) {
        resetStars();
      }
    });
  });
}

// Egérmozgás kezelése egy csillag fölött
function handleStarMove(star, mouseEvent) {
  const starRect = star.getBoundingClientRect();
  const starCenter = starRect.left + starRect.width / 2;

  const dx = mouseEvent.clientX - starCenter;
  const isInFirstHalf = dx <= 0;

  if (isInFirstHalf) {
    toggleHalfState(star); // Fél csillag állapot
  } else {
    toggleFullState(star); // Teljes csillag állapot
  }
}

let animationFrame = null;
function throttleHandleStarMove(star, mouseEvent) {
  if (animationFrame) {
    return; // Megakadályozza az egymást átfedő animációs kereteket
  }
  animationFrame = requestAnimationFrame(() => {
    handleStarMove(star, mouseEvent); // Kezeli az egérmozgást
    if (isMobile) handleClick(mouseEvent, star);
    animationFrame = null;
  });
}

// Csillagok alaphelyzetbe állítása (üres)
let resetFrame = null;
function resetStars() {
  if (resetFrame) return;

  resetFrame = true;
  reviewStars.forEach((star) => {
    star.querySelector(".full").classList.remove("active", "half-active"); // Eltávolítja az aktív és fél-aktív állapotokat
    star.querySelector(".empty").classList.add("active"); // Üres állapot hozzáadása
  });
  resetFrame = null;
}

// Fél csillag állapot beállítása
function toggleHalfState(star) {
  if (!isActive || isSet) return;

  resetStars(); // Alaphelyzetbe állítja a csillagokat
  star.querySelector(".empty").classList.remove("active"); // Eltávolítja az üres állapotot
  star.querySelector(".full").classList.add("half-active"); // Fél-aktív állapot hozzáadása

  const index = parseInt(star.dataset.index, 10);
  for (let i = 0; i < index; i++) {
    toggleFullState(reviewStars[i]); // Az előző csillagokat teljes állapotúvá teszi
  }
}

// Teljes csillag állapot beállítása
function toggleFullState(star) {
  if (!isActive || isSet) return;

  star.querySelector(".empty").classList.remove("active"); // Eltávolítja az üres állapotot
  star.querySelector(".full").classList.remove("half-active"); // Fél-aktív állapot eltávolítása
  star.querySelector(".full").classList.add("active"); // Aktív állapot hozzáadása

  const index = parseInt(star.dataset.index, 10);
  for (let i = 0; i < index; i++) {
    const previousStar = reviewStars[i];
    previousStar.querySelector(".empty").classList.remove("active"); // Az előző csillagokat teljes állapotúvá teszi
    previousStar.querySelector(".full").classList.add("active");
  }
}

// Csillag kattintásának kezelése
function handleClick(e, star) {

  let value = 0;

  reviewStars.forEach((star) => {
    const selectedStarClass = star.querySelector(".full").classList;

    switch (true) {
      case selectedStarClass.contains("active"):
        value++; // Növeli az értéket teljes csillag esetén
        break;

      case selectedStarClass.contains("half-active"):
        value += 0.5; // Fél csillag esetén 0.5 értéket ad hozzá
        break;

      default:
        break;
    }
  });

  if (hiddenInput.value == value) {
    hiddenInput.value = "null"; // Az érték null-ra állítása, ha már kiválasztották
    reviewStarsContainer.classList.add("grey"); // Szürke állapot beállítása
    isSet = false;

    if (!isMobile) handleStarMove(star, e);
    if (isMobile) resetStars();
  } else {
    hiddenInput.value = value; // Az új érték beállítása
    reviewStarsContainer.classList.remove("grey", "invalid"); // Szürke állapot eltávolítása
    isSet = true;
  }

  formChangeHandler();
}

// Csillagok állapotának beállítása egy adott érték alapján
function setStarsFromValue(value) {
  const fullStars = Math.floor(value); // Teljes csillagok száma
  const hasHalfStar = value % 1 !== 0; // Van-e fél csillag

  resetStars(); // Alaphelyzetbe állítja a csillagokat

  for (let i = 0; i < fullStars; i++) {
    toggleFullState(reviewStars[i]); // Teljes csillagok beállítása
  }

  if (hasHalfStar && fullStars < reviewStars.length) {
    toggleHalfState(reviewStars[fullStars]); // Fél csillag beállítása, ha van
  }
}

const requiredFields = ["stars-input", "review-title", "review-body"];
if (form) {
  for (let field of requiredFields) {
    const input = form.querySelector(`[name=${field}]`);
    if (input.getAttribute("type") != "hidden") {
      input.addEventListener("input", formChangeHandler);
    }
  }
}

function formChangeHandler() {
  let isValid = true;
  for (let field of requiredFields) {
    const input = form.querySelector(`[name=${field}]`);
    if (!input.value || input.getAttribute("type") == "hidden" && input.value == "null") isValid = false;
  }

  if (isValid) reviewSubmitter.disabled = false;
  else reviewSubmitter.disabled = true;
}

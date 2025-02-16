import AutofillForm from "./autofill-form.js";
import PasswordForm from "./password-form.js";
import PersonalDetailsForm from "./personal-form.js";

// ----------------------------
// Mobil / Érintőképernyős eszköz érzékelése
// ----------------------------

const isMobile = detectMobileDevice();

function detectMobileDevice() {
  const cssMobile = getComputedStyle(document.body).getPropertyValue("--is-mobile") === "1";
  const touchDevice = ("ontouchstart" in window) || (navigator.msMaxTouchPoints > 0);
  return cssMobile || touchDevice;
}

// ----------------------------
// Menü navigáció
// ----------------------------

let pageLinks;
let pages;

function initializePageNavigation() {
  pageLinks = document.querySelectorAll(".page");
  pages = document.querySelectorAll(".content-page");

  pageLinks.forEach((page) => {
    page.addEventListener("click", handlePageClick);
    page.addEventListener("keydown", handlePageKeyPress);
  });
}

function handlePageClick(event) {
  togglePage(parseInt(event.currentTarget.dataset.pageid));
}

function handlePageKeyPress(event) {
  if (event.code === "Space" || event.code === "Enter") {
    togglePage(parseInt(event.currentTarget.dataset.pageid));
  }
}

function togglePage(activePageId) {
  pageLinks.forEach((page, index) => {
    const isActive = index === activePageId;
    page.classList.toggle("active", isActive);
    pages[index].classList.toggle("active", isActive);
  });
}

// ----------------------------
// Dinamikus keret animációs rendszer
// ----------------------------

class BorderAnimator {
  constructor() {
    this.radiusAnimationTokens = new WeakMap();
    this.colorAnimationTokens = new WeakMap();
    this.animating = false;
    this.insideCustomStyleElement = false;
  }

  // Rádiusz vezérlő
  startRadiusAnimation(element, animationFunction) {
    const token = Symbol();
    this.radiusAnimationTokens.set(element, token);
    animationFunction(() => this.radiusAnimationTokens.get(element) === token);
  }

  // Szín vezérlő
  startColorAnimation(element, animationFunction) {
    const token = Symbol();
    this.colorAnimationTokens.set(element, token);
    animationFunction(() => this.colorAnimationTokens.get(element) === token);
  }

  animateRadius(element, startRadius, endRadius, duration, isValid) {
    const startTime = performance.now();

    const updateRadius = (timestamp) => {
      const elapsed = timestamp - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const currentRadius = startRadius + (endRadius - startRadius) * progress;

      element.style.setProperty("--radius", `${currentRadius}px`);

      if (progress < 1 && isValid()) {
        requestAnimationFrame(updateRadius);
      }
    };

    requestAnimationFrame(updateRadius);
  }

  animateColor(element, startColorHex, endColorHex, duration, isValid) {
    const startTime = performance.now();
    const startRGB = this.parseHexColor(startColorHex);
    const endRGB = this.parseHexColor(endColorHex);

    const updateColor = (timestamp) => {
      const elapsed = timestamp - startTime;
      const progress = Math.min(elapsed / duration, 1);
      const currentColor = this.interpolateColor(startRGB, endRGB, progress);

      element.style.setProperty(
        "--color",
        `rgb(${currentColor.r}, ${currentColor.g}, ${currentColor.b})`
      );

      if (progress < 1 && isValid()) {
        requestAnimationFrame(updateColor);
      }
    };

    requestAnimationFrame(updateColor);
  }

  parseHexColor(hex) {
    const hexStr = hex.replace("#", "");
    return {
      r: parseInt(hexStr.substring(0, 2), 16),
      g: parseInt(hexStr.substring(2, 4), 16),
      b: parseInt(hexStr.substring(4, 6), 16)
    };
  }

  interpolateColor(startRGB, endRGB, progress) {
    return {
      r: Math.round(startRGB.r + (endRGB.r - startRGB.r) * progress),
      g: Math.round(startRGB.g + (endRGB.g - startRGB.g) * progress),
      b: Math.round(startRGB.b + (endRGB.b - startRGB.b) * progress)
    };
  }
}

// ----------------------------
// Űrlapok kezelése
// ----------------------------

function initializeForms() {
  // Fő űrlapok
  new PersonalDetailsForm(document.querySelector("form[name=basic-info]"));
  new PasswordForm(document.querySelector(".password-form"));

  // Automatikus kitöltési űrlap
  document.querySelectorAll(".autofill-form").forEach((form) => {
    new AutofillForm(form.classList[0], form);
  });
}

// ----------------------------
// UI interakciók
// ----------------------------

function setupHoverEffects(animator, elements) {
  elements.forEach(({ element, hoverColor }) => {
    if (!element || !hoverColor || !/^#[A-Fa-f0-9]{6}$/.test(hoverColor)) return;

    element.addEventListener("mouseover", () => {
      animator.insideCustomStyleElement = true;
      animateBorderColorTo(animator, hoverColor);
    });

    element.addEventListener("mouseleave", () => {
      animateBorderColorTo(animator, "#4d4d4d");
      animator.insideCustomStyleElement = false;
    });
  });
}

function animateBorderColorTo(animator, targetColor) {
  document.querySelectorAll(".dynamic-border").forEach((borderElement) => {
    const currentColor = rgbToHex(
      getComputedStyle(borderElement).getPropertyValue("--color").trim()
    );
    animator.startColorAnimation(borderElement, (isValid) =>
      animator.animateColor(borderElement, currentColor, targetColor, 500, isValid)
    );
  });
}

function rgbToHex(rgbString) {
  const rgbValues = rgbString.match(/\d+/g);
  if (!rgbValues) return "#000000";
  return `#${rgbValues.map(num => parseInt(num).toString(16).padStart(2, "0")).join("")}`;
}

// ----------------------------
// Vissza gomb kezelése
// ----------------------------

function handleBackButton() {
  const referrer = document.referrer;
  const origin = window.location.origin;

  const outParams = {
    scaleY: 1,
    duration: 1,
    stagger: {
      each: 0.05,
      from: "start",
      grid: "auto",
      axis: "x"
    },
    ease: "power4.inOut"
  };

  animatePageTransition(outParams).then(() => {
    if (referrer && referrer.startsWith(origin)) {
      history.back();
    } else {
      window.location.href = "./";
    }
  });
}

// ----------------------------
// Asztali felület interakcióinak kezelése
// ----------------------------

function setupDesktopInteractions(main, borderElements, animator) {
  // Egér mozgás eseménykezelő
  main.addEventListener("mousemove", (e) => {
    if (borderElements[0].style.getPropertyValue("--radius") === "0px") {
      borderElements.forEach((element) => {
        animator.startRadiusAnimation(element, (isValid) =>
          animator.animateRadius(element, 0, 400, 500, isValid)
        );

        if (!animator.insideCustomStyleElement) {
          const currentColor = getComputedStyle(element)
            .getPropertyValue("--color")
            .trim();
          const startHex = rgbToHex(currentColor);
          animator.startColorAnimation(element, (isValid) =>
            animator.animateColor(element, startHex, "#4d4d4d", 500, isValid)
          );
        }
      });
    }

    if (!animator.animating) {
      animator.animating = true;
      requestAnimationFrame(() => {
        borderElements.forEach((element) => {
          const rect = element.getBoundingClientRect();
          const x = e.clientX - rect.left;
          const y = e.clientY - rect.top;

          element.style.setProperty("--mouse-x", `${x}px`);
          element.style.setProperty("--mouse-y", `${y}px`);
        });
        animator.animating = false;
      });
    }
  });

  // Egér kilépés eseménykezelő
  main.addEventListener("mouseleave", () => {
    borderElements.forEach((element) => {
      const currentRadiusStr = element.style.getPropertyValue("--radius");
      const currentRadius = Number(currentRadiusStr.replace("px", "")) || 0;
      animator.startRadiusAnimation(element, (isValid) =>
        animator.animateRadius(element, currentRadius, 0, 500, isValid)
      );
    });
  });

  // Egér belépés eseménykezelő
  main.addEventListener("mouseenter", () => {
    borderElements.forEach((element) => {
      const currentRadiusStr = element.style.getPropertyValue("--radius");
      const currentRadius = Number(currentRadiusStr.replace("px", "")) || 0;
      animator.startRadiusAnimation(element, (isValid) =>
        animator.animateRadius(element, currentRadius, 400, 500, isValid)
      );

      if (!animator.insideCustomStyleElement) {
        const currentColor = getComputedStyle(element)
          .getPropertyValue("--color")
          .trim();
        const startHex = rgbToHex(currentColor);
        animator.startColorAnimation(element, (isValid) =>
          animator.animateColor(element, startHex, "#4d4d4d", 500, isValid)
        );
      }
    });
  });
}

// ----------------------------
// Inicializálás
// ----------------------------

window.addEventListener("DOMContentLoaded", () => {
  // Fő elemek
  const main = document.querySelector(".main");
  const borderElements = document.querySelectorAll(".dynamic-border");
  const animator = new BorderAnimator();

  // Modulok meghívása
  initializePageNavigation();
  initializeForms();

  // Hover effektek definiálása
  setupHoverEffects(animator, [
    { element: document.querySelector(".logout"), hoverColor: "#b92424" },
    { element: document.querySelector(".dashboard"), hoverColor: "#2797ca" },
    { element: document.querySelector(".didyouknow"), hoverColor: "#5dbc55" },
    ...Array.from(document.querySelectorAll(".add-field")).map((element) => ({
      element,
      hoverColor: "#85f838"
    }))
  ]);

  // Vissza gomb eseménykezelése
  document.getElementById("back-button").addEventListener("click", handleBackButton);

  // Asztali eszköz effektjeinek inicializálása
  if (!isMobile) {
    setupDesktopInteractions(main, borderElements, animator);
  }
});

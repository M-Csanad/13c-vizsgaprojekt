import Cart from "./cart.js";

document.addEventListener("DOMContentLoaded", function () {
  const navLink = document.getElementById("fb-navlink-category");
  const subContentContainer = document.getElementById("fb-subcontentContainer");

  navLink.addEventListener("click", function () {
    resetAnimations();

    subContentContainer.classList.add("flex-block");
    subContentContainer.classList.remove("hidden");
    subContentContainer.style.display = "flex";
    subContentContainer.style.top = "100%";
    subContentContainer.style.opacity = "1";
    subContentContainer.style.position = "relative";

    let delay = 100;
    setTimeout(() => {
      document
        .querySelectorAll(".fb-nav-subcontent-frame")
        .forEach((frame, index) => {
          setTimeout(() => {
            frame.style.opacity = "1";
          }, delay * index);
        });
      document
        .querySelectorAll(".fb-nav-imgcontent-frame")
        .forEach((frame, index) => {
          setTimeout(() => {
            frame.style.opacity = "1";
          }, delay * (index + 2));
        });
    }, 500);
  });

  subContentContainer.addEventListener("mouseleave", function () {
    subContentContainer.style.top = "-100%";
    subContentContainer.style.opacity = "0";
    subContentContainer.style.transition = "all 0.4s ease-out";
    subContentContainer.style.position = "absolute";
    setTimeout(() => {
      subContentContainer.classList.remove("flex-block");
      subContentContainer.classList.add("hidden");
    }, 500);
  });

  function resetAnimations() {
    document
      .querySelectorAll(".fb-nav-subcontent-frame, .fb-nav-imgcontent-frame")
      .forEach((frame) => {
        frame.style.opacity = "0";
      });
  }

  subContentContainer.classList.remove("flex-block");
  subContentContainer.classList.add("hidden");
  subContentContainer.style.top = "-100%";
  subContentContainer.style.opacity = "0";
  subContentContainer.style.position = "absolute";
});

document.addEventListener("DOMContentLoaded", function () {
  const hamburgerMenu = document.querySelector(".hamburger-menu");
  const hamburgerIcon = document.querySelector(".hamburger-icon");

  // Hamburger ikon eseménykezelés – menü megjelenítése és ikon váltás
  hamburgerIcon.addEventListener("click", function () {
    hamburgerIcon.classList.toggle("active"); // Morph X ikonra és vissza
    hamburgerMenu.classList.toggle("hidden");
    hamburgerMenu.classList.toggle("active");
  });

  const cart = new Cart();
});

import Search from "./search.js";

document.addEventListener("DOMContentLoaded", function () {
  const categoryData = window.categoryData || [];
  const navLink = document.getElementById("fb-navlink-category");
  const subContentContainer = document.getElementById("fb-subcontentContainer");

  // Validációs a categoryData tömbre
  if (!Array.isArray(categoryData) || categoryData.length === 0) {
    console.warn("Navigációs hiba: Nincs elérhető kategória adat.");

    // Átirányítjuk a felhasználót a főoldalra a kategorychooser részhez
    if (navLink) {
      navLink.addEventListener("click", function (e) {
        e.preventDefault();
        console.log("Navigálás a főoldalra a kategória választóhoz");

        // Ellenőrizzük, hogy a főoldalon vagyunk-e
        const isHomePage =
          window.location.pathname === "/" ||
          window.location.pathname === "/index.php";

        if (isHomePage) {
          // Ha a főoldalon vagyunk, csak görgessünk a kategória választóhoz
          const categoryChooser = document.getElementById("categorychooser");
          if (categoryChooser) {
            categoryChooser.scrollIntoView({ behavior: "smooth" });
          }
        } else {
          window.location.href = "http://localhost/#categorychooser";
        }
      });
    }
  } else {
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
  }

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

  const search = new Search();
});

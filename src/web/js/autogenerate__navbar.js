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

    let delay = 200;
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
  const categoryContent = JSON.parse("<?= $category_json; ?>"); // PHP JSON adat importálása

  // Mobil menü tartalom generálása
  categoryContent.forEach((content) => {
    const categoryItem = document.createElement("div");
    categoryItem.classList.add("hamburger-menu-item");

    const categoryTitle = document.createElement("h3");
    categoryTitle.textContent = content.title;
    categoryItem.appendChild(categoryTitle);

    const subcategoryList = document.createElement("ul");
    content.subcategories.forEach((sub) => {
      const subcategoryItem = document.createElement("li");
      const subcategoryLink = document.createElement("a");
      subcategoryLink.href = sub.url;
      subcategoryLink.textContent = sub.name;
      subcategoryLink.classList.add("fb-link");
      subcategoryItem.appendChild(subcategoryLink);
      subcategoryList.appendChild(subcategoryItem);
    });

    categoryItem.appendChild(subcategoryList);
    hamburgerMenu.appendChild(categoryItem);
  });
});

// Hamburger menü megnyitása és bezárása
function toggleMobileMenu() {
  document.querySelector(".hamburger-menu").classList.toggle("active");
}

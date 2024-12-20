document.addEventListener("DOMContentLoaded", function () {
  const mainMenu = document.getElementById("main-menu");
  const menuHistory = [];
  document.querySelectorAll(".menu-item").forEach((item) => {
    item.addEventListener("click", () => {
      const target = document.getElementById(item.dataset.target);
      if (target) {
        const currentMenu = document.querySelector(".submenu.active");
        if (currentMenu) {
          menuHistory.push(currentMenu.id);
        }
        target.classList.add("active");
        setTimeout(() => {
          currentMenu.classList.remove("active");
        }, 400);
      }
    });
  });

  // Vissza gomb kezelése
  document.querySelectorAll("[data-back]").forEach((backBtn) => {
    backBtn.addEventListener("click", () => {
      const currentMenu = document.querySelector(".submenu.active");
      if (currentMenu) {
        setTimeout(() => {
          currentMenu.classList.remove("active");
        }, 400);

        const previousMenuId = menuHistory.pop();
        if (previousMenuId) {
          const previousMenu = document.getElementById(previousMenuId);
          if (previousMenu) {
            previousMenu.classList.add("active");
          }
        } else {
          mainMenu.classList.add("active");
        }
      }
    });
  });
});

const mainMenu = document.getElementById("main-menu");
const submenus = document.querySelectorAll(".submenu");

document.querySelectorAll(".menu-item").forEach((item) => {
  item.addEventListener("click", () => {
    const target = document.getElementById(item.dataset.target);
    if (target) {
      submenus.forEach((submenu) =>
        submenu.classList.remove("active", "exiting")
      );

      target.classList.add("active");
    }
  });
});

document.querySelectorAll("[data-back]").forEach((backBtn) => {
  backBtn.addEventListener("click", (e) => {
    const submenu = e.target.closest(".submenu");
    submenu.classList.add("exiting");
    submenu.addEventListener(
      "transitionend",
      () => {
        submenu.classList.remove("active", "exiting");
      },
      { once: true }
    );
  });
});

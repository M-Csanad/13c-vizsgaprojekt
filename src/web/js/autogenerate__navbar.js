document.addEventListener("DOMContentLoaded", function () {
  const navLink = document.getElementById("fb-navlink-category");
  const subContentContainer = document.getElementById("fb-subcontentContainer");

  navLink.addEventListener("click", function () {
    resetAnimations();
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
      subContentContainer.style.display = "none";
    }, 500);
  });

  function resetAnimations() {
    document
      .querySelectorAll(".fb-nav-subcontent-frame, .fb-nav-imgcontent-frame")
      .forEach((frame) => {
        frame.style.opacity = "0";
      });
  }

  subContentContainer.style.display = "none";
  subContentContainer.style.top = "-100%";
  subContentContainer.style.opacity = "0";
  subContentContainer.style.position = "absolute";
});

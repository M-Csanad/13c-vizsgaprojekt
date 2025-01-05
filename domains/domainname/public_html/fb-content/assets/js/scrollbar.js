const scrollStatusBar = document.getElementById("scrollStatus");

function scrollHandler(e) {
  maxScroll =
    document.documentElement.scrollHeight -
    document.documentElement.clientHeight;
  currentScrollTop = document.documentElement.scrollTop;
  scrollPercentage = (currentScrollTop / maxScroll) * 100;
  if (isNaN(scrollPercentage)) scrollPercentage = 0;
  if (document.documentElement.scrollTop > 500) {
    document.body.classList.add("top-show");
  } else {
    document.body.classList.remove("top-show");
  }
  scrollStatusBar.animate(
    {
      width: `${scrollPercentage}%`,
    },
    { duration: 500, fill: "forwards", easing: "ease" }
  );
}

window.addEventListener("load", () => {
  setTimeout(() => {
    scrollHandler();
    window.addEventListener("scroll", scrollHandler);
  }, 1000);
});

window.addEventListener("resize", scrollHandler);

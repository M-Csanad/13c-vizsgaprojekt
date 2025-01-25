(() => {
  const dragScroll = document.querySelector(".drag-scroll");
  let isDown = false;
  let startX;
  let scrollLeft;

  // Ha lenyomjuk az egérgombot
  dragScroll.addEventListener("mousedown", (e) => {
    isDown = true;
    startX = e.pageX - dragScroll.offsetLeft;
    scrollLeft = dragScroll.scrollLeft;
  });

  // Ha lecsúszunk az elejéről (mouseleave) vagy felengedjük az egeret (mouseup), leáll a "húzás"
  dragScroll.addEventListener("mouseleave", () => {
    isDown = false;
  });
  dragScroll.addEventListener("mouseup", () => {
    isDown = false;
  });

  // Egérmozgatás közben
  dragScroll.addEventListener("mousemove", (e) => {
    if (!isDown) return;
    e.preventDefault();
    const x = e.pageX - dragScroll.offsetLeft;
    // A "walk" (vagy delta) jelzi, hogy mennyit mozdultunk a kezdőponthoz képest
    const walk = x - startX;
    dragScroll.scrollLeft = scrollLeft - walk;
  });
})();

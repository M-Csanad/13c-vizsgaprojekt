document.addEventListener("DOMContentLoaded", function () {
  // Kiválasztjuk az összes <p>, <h1>, <h2>, <h3>, <h4>, <h5>, <h6> elemet a .videoContent_scrollable osztályon belül
  const scrollElements = document.querySelectorAll(
    ".videoContent_scrollable p, .videoContent_scrollable h1, .videoContent_scrollable h2, .videoContent_scrollable h3, .videoContent_scrollable h4, .videoContent_scrollable h5, .videoContent_scrollable h6"
  );

  // Az elem aktiválásához és elrejtéséhez szükséges eltolás százalékban
  const hideOffsetTopPercentage = 0.2; // Felső 20%
  const hideOffsetBottomPercentage = 0.2; // Alsó 20%

  // Ellenőrzi, hogy az elem a viewport látható részén belül van-e (a képernyőn)
  const elementInView = (el, offsetTop, offsetBottom) => {
    const elementTop = el.getBoundingClientRect().top;
    const viewportHeight =
      window.innerHeight || document.documentElement.clientHeight;

    // Ha az elem a képernyő alja vagy teteje közelében van
    return (
      elementTop <= viewportHeight * (1 - offsetBottom) &&
      elementTop >= viewportHeight * offsetTop
    );
  };

  // Megjeleníti az elemet azzal, hogy hozzáadja a .videoContent_SHOW osztályt és eltávolítja a .videoContent_HIDE osztályt
  const displayScrollElement = (element) => {
    element.classList.add("videoContent_SHOW");
    element.classList.remove("videoContent_HIDE");
  };

  // Elrejti az elemet azzal, hogy hozzáadja a .videoContent_HIDE osztályt és eltávolítja a .videoContent_SHOW osztályt
  const hideScrollElement = (element) => {
    element.classList.remove("videoContent_SHOW");
    element.classList.add("videoContent_HIDE");
  };

  // Végigmegy az összes kiválasztott elemeken, és megnézi, hogy éppen láthatóak-e a képernyőn, vagy éppen elhagyják azt
  const handleScrollAnimation = () => {
    scrollElements.forEach((el) => {
      if (
        elementInView(el, hideOffsetTopPercentage, hideOffsetBottomPercentage)
      ) {
        // Ha az elem a viewportban van, figyelembe véve az offsetet
        displayScrollElement(el);
      } else {
        // Ha az elem elhagyja a viewportot (felül vagy alul), elrejtjük
        hideScrollElement(el);
      }
    });
  };

  // Minden görgetésnél ellenőrizzük, hogy az elemek a viewportba kerültek-e vagy elhagyták-e
  window.addEventListener("scroll", () => {
    handleScrollAnimation();
  });

  // Amikor az oldal betöltődik, egyszer lefuttatjuk, hogy azok az elemek is megjelenjenek, amik eleve a viewportban vannak
  handleScrollAnimation();
});

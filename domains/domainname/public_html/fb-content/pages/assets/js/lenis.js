// Létrehoz egy új Lenis példányt
const lenis = new Lenis({
  smooth: true, // Engedélyezi a sima görgetést
  duration: 0.8, // Görgetés időtartama
  direction: "vertical", // Görgetési irány, lehet 'vertical' vagy 'horizontal'
  gestureDirection: "vertical", // A felhasználói gesztus iránya
  smoothTouch: false, // Simaság érintéses eszközökre
  touchMultiplier: 2, // Érintés érzékenysége

  //easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)), // Közepesen lassú és sima hatás
  easing: (t) => 1 - Math.pow(1 - t, 4), // Lassabb és simább hatás
  //easing: (t) => 0.5 * (1 - Math.cos(Math.PI * t)), // Lágy sinus függvény alapú görgetés
});

// Frissítés minden frame-en
function raf(time) {
  lenis.raf(time);
  requestAnimationFrame(raf);
}

requestAnimationFrame(raf);

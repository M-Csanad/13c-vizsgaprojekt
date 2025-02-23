import APIFetch from "./apifetch.js";
const randomId = () => "el-" + (Math.random() + 1).toString(36).substring(7);
class Search {
  isOpen = false;
  ease = "power2.inOut";
  ease2 = "power3.inOut";

  constructor() {
    // Fő filter ablak
    this.domElement = document.querySelector(".search");
    if (!this.domElement) throw new Error("Nincs cart osztályú elem.");

    // Id és selector
    this.domElement.id = randomId();
    this.selector = `#${this.domElement.id}`;

    this.background = document.querySelector(".modal-background");
    if (!this.background) throw new Error("Nincs háttér.");

    // Kinyitó gomb
    this.openButton = document.querySelector(".search-open");
    if (!this.openButton) throw new Error("Nincs kinyitó gomb.");

    // Bezáró gomb
    this.closeButton = this.domElement.querySelector(".search-close");
    if (!this.closeButton) throw new Error("Nincs becsukó gomb.");

    // GSAP ellenőrzés
    if (!gsap) throw new Error("A GSAP nem található");

    if (!lenis) throw new Error("A Lenis nem található");

    const searchInput = this.domElement.querySelector(
      "input[name='search_term']"
    );
    if (searchInput) {
      searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          this.search();
        }
      });
    }

    // Eseménykezelés
    this.openButton.addEventListener("click", this.open.bind(this));
    this.closeButton.addEventListener("click", this.close.bind(this));
  }

  // UI metódusok
  open() {
    lenis.stop();
    gsap.set(this.domElement, { visibility: "visible" });
    gsap.set(this.background, { visibility: "visible" });

    gsap.to(this.domElement, {
      opacity: 1,
      duration: 0.5,
      ease: this.ease,
    });
    gsap.to(this.background, {
      opacity: 1,
      duration: 0.5,
      ease: this.ease,
    });
  }

  close() {
    gsap.to(this.background, {
      opacity: 0,
      duration: 0.5,
      ease: this.ease,
    });
    gsap.to(this.domElement, {
      opacity: 0,
      duration: 0.5,
      ease: this.ease,
      onComplete: () => {
        lenis.start();
        gsap.set(this.domElement, { visibility: "hidden" });
        gsap.set(this.background, { visibility: "hidden" });
      },
    });
  }

  // Backend függvények
  search() {
    const inputField = this.domElement.querySelector("input[name='search_term']");
    if (!inputField) {
        console.error("Keresési input nem található.");
        return;
    }

    const searchTerm = inputField.value.trim();
    if (!searchTerm) {
        console.log("Üres keresési kifejezés.");
        return;
    }

    // Redirect to search page with query parameter
    window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
  }
}

export default Search;

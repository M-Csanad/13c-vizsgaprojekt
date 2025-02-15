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
    // Kereső input mező lekérése
    const inputField = this.domElement.querySelector(
      "input[name='search_term']"
    );
    if (!inputField) {
      console.error("Keresési input nem található.");
      return;
    }
    console.log(inputField.value.trim());
    const searchTerm = inputField.value.trim();
    if (!searchTerm) {
      console.log("Üres keresési kifejezés.");
      return;
    }

    // Indítjuk a fetch()-et a search.php felé (POST metódussal)
    APIFetch("/api/search", "POST", { query: searchTerm }, false)
      .then((response) => {
        if (!response.ok) {
          throw new Error("Hálózati hiba: " + response.status);
        }

        /* window.location = "/search"; */
        return response.json();
      })
      .then((data) => {
        if (data.error) {
          console.error("Keresési hiba:", data.error);
        } else {
          console.log("Keresési eredmények:", data.results);
        }
      })
      .catch((err) => {
        console.error("Keresési hiba:", err);
      });
  }
}

export default Search;

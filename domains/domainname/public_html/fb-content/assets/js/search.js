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

    // Search input és error message létrehozása
    this.searchInput = this.domElement.querySelector("input[name='search_term']");
    
    // Error message létrehozása
    this.errorMessage = document.createElement("div");
    this.errorMessage.className = "search-error-message";
    this.errorMessage.textContent = "A keresési kifejezés túl rövid. Legalább 4 karakter szükséges.";
    this.errorMessage.style.opacity = "0";
    this.errorMessage.style.display = "none";
    
    // Add error message to the DOM in the appropriate location
    const searchInputContainer = this.domElement.querySelector(".search-input");
    if (searchInputContainer) {
      // Add error message directly to the search modal element to allow absolute positioning
      this.domElement.appendChild(this.errorMessage);
    }

    if (this.searchInput) {
      this.searchInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") {
          this.search();
        }
      });
      
      // Input eseménykezelő a validáláshoz
      this.searchInput.addEventListener("input", this.validateInput.bind(this));
    }

    // Eseménykezelés
    this.openButton.addEventListener("click", this.open.bind(this));
    this.closeButton.addEventListener("click", this.close.bind(this));
  }
  
  // Input validálás
  validateInput() {
    if (!this.searchInput) return;
    
    const value = this.searchInput.value.trim();
    const searchInputContainer = this.domElement.querySelector(".search-input");
    
    if (value.length > 0 && value.length <= 3) {
      this.showInputError(true, searchInputContainer);
    } else {
      this.showInputError(false, searchInputContainer);
    }
  }
  
  // Error message megjelenítése/elrejtése animációval
  showInputError(show, container) {
    if (show) {
      container.classList.add("search-error");
      this.errorMessage.style.display = "block";
      gsap.to(this.errorMessage, { 
        opacity: 1, 
        y: 0, 
        duration: 0.3, 
        ease: "power2.out" 
      });
    } else {
      container.classList.remove("search-error");
      gsap.to(this.errorMessage, { 
        opacity: 0, 
        y: -10, 
        duration: 0.2, 
        ease: "power2.in",
        onComplete: () => {
          this.errorMessage.style.display = "none";
          gsap.set(this.errorMessage, { y: 0 });
        }
      });
    }
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
    
    if (searchTerm.length <= 3) {
        this.showInputError(true, this.domElement.querySelector(".search-input"));
        return;
    }

    window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
  }
}

export default Search;

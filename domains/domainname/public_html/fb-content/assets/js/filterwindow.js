const randomId = () => "el-" + (Math.random() + 1).toString(36).substring(7);

class FilterWindow {
    isOpen = false;
    ease = "power3.inOut";

    // A töréspont felett az animáció máshogy működik
    breakpoint = 950;
    

    constructor() {
        // Fő filter ablak
        this.domElement = document.querySelector(".filters");
        if (!this.domElement) throw new Error("Nincs filter osztályú elem.");

        // Id és selector
        this.domElement.id = randomId();
        this.selector = `#${this.domElement.id}`;

        // Kinyitó gomb
        this.openButton = document.querySelector(".filter-open");
        if (!this.openButton) throw new Error("Nincs kinyitó gomb.");

        // Bezáró gomb
        this.closeButton = this.domElement.querySelector(".filter-close");
        if (!this.closeButton) throw new Error("Nincs becsukó gomb.");

        // GSAP ellenőrzés
        if (!gsap) throw new Error("A GSAP nem található");

        // Eseménykezelés
        this.openButton.addEventListener("click", this.open.bind(this))
        this.closeButton.addEventListener("click", this.close.bind(this));
    }

    open() {
        if (this.isOpen) return;

        this.isOpen = true;

        gsap.killTweensOf(this.domElement);
        
        gsap.set(this.domElement, { visibility: "visible" });

        // Ablak bejön balról jobbra
        if (window.innerWidth >= this.breakpoint) {
            gsap.to(this.domElement, {
                marginLeft: "0px",
                x: "0px",
                duration: 0.8,
                ease: this.ease
            });
        }
        else {
            gsap.set(this.domElement, { marginLeft: "0px" });
            gsap.to(this.domElement, { 
                x: "0%",
                duration: 0.8,
                ease: this.ease
            });
        }

        // A filterek bejönnek balról jobbra
        gsap.fromTo(this.selector + " > .filter-group", { opacity: 0, x: "-100%" }, {
            opacity: 1,
            x: "0%",
            stagger: {
                each: 0.05,
                grid: "auto",
            },
            duration: 1,
            ease: this.ease
        });
    }

    close() {
        gsap.killTweensOf(this.domElement);
        
        // Filterek elhalványodnak
        gsap.to(this.selector + " > .filter-group", {
            opacity: 0,
            duration: 0.5,
            ease: this.ease
        });
        
        // Ablak kimegy jobbról balra
        if (window.innerWidth >= this.breakpoint) {
            gsap.to(this.domElement, { 
                marginLeft: "-400px",
                x: "-400px",
                duration: 0.8,
                ease: this.ease,
                onComplete: () => {
                    this.isOpen = false;
                    gsap.set(this.domElement, { visibility: "hidden" });
                    gsap.set(this.domElement, { marginLeft: "-400px" });
                }
            });
        }
        gsap.to(this.domElement, { 
            x: "-100%",
            duration: 0.8,
            ease: this.ease,
            onComplete: () => {
                this.isOpen = false;
                gsap.set(this.domElement, { visibility: "hidden" });
                gsap.set(this.domElement, { marginLeft: "-400px" });
            }
        });

    }
}

export default FilterWindow;
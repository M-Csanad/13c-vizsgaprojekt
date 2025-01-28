class AutofillForm {
    isOpen = false;
    ease = "power2.inOut";
    breakpoint = 991;

    constructor(type, dom) {
        if (type != "autofill-delivery" && type != "autofill-billing") throw new Error("Ismeretlen típus");
        if (!gsap) throw new Error("GSAP nem található");
        
        this.autofillType = type.split('-')[1];
        
        this.initDOM(dom);
        this.bindEvents();
    }

    initDOM(dom) {
        this.formDom = dom;
        this.formWrapper = dom.parentElement;
        
        this.cardsContainer = this.formWrapper.previousElementSibling;
        this.cards = Array.from(this.cardsContainer.children).filter(e => e.className != "add-field");
        this.openButton = this.cardsContainer.querySelector(".add-field")
        this.closeButton = this.formDom.querySelector(".form-close");

        if (!this.openButton) throw new Error("Nincs kinyitó gomb");
    }

    bindEvents() {
        this.openButton.addEventListener("click", this.open.bind(this));
        this.closeButton.addEventListener("click", this.close.bind(this));
    }

    open() {
        if (this.isOpen) return;
        
        this.isOpen = true;
        lenis.stop();

        gsap.set(this.formWrapper, {visibility: "visible"});
        gsap.to(this.formWrapper, {
            backgroundColor: "rgba(0,0,0,0.2)",
            ease: this.ease,
            duration: 0.6
        });
        gsap.to(this.formDom, {
            y: 0,
            ease: this.ease,
            duration: 0.6
        });
    }

    close() {
        gsap.to(this.formWrapper, {
            backgroundColor: "rgba(0,0,0,0)",
            ease: this.ease,
            duration: 0.6
        });
        gsap.to(this.formDom, {
            y: "100%",
            ease: this.ease,
            duration: 0.6,
            onComplete: () => {
                gsap.set(this.formWrapper, {visibility: "hidden"});
                this.isOpen = false;
                lenis.start();
            }
        });
    }

    save() {

    }

    cancel() {

    }
}

export default AutofillForm;
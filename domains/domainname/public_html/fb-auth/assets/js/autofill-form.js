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
        this.saveButton = this.formDom.querySelector(".form-save");
        this.cacelButton = this.formDom.querySelector(".form-cancel");

        if (!this.openButton) throw new Error("Nincs kinyitó gomb");
    }

    bindEvents() {
        this.openButton.addEventListener("click", this.open.bind(this));
        this.closeButton.addEventListener("click", this.close.bind(this));
        this.saveButton.addEventListener("click", this.save.bind(this));
        this.cacelButton.addEventListener("click", this.cancel.bind(this));

        this.keyListener = window.addEventListener("keydown", async (e) => {
            if (document.body.contains(this.domElement) && e.code == "Escape") {
                this.cancel();
            }
        });
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

        if (window.innerWidth > this.breakpoint) {
            gsap.set(this.formDom, {y: 0, scale: 0, opacity: 0})
            gsap.to(this.formDom, {
                scale: 1,
                opacity: 1,
                duration: 0.6,
                ease: this.ease
            })
        }
        else {
            gsap.set(this.formDom, {y: "100%", scale: 1, opacity: 1})
            gsap.to(this.formDom, {
                y: 0,
                ease: this.ease,
                duration: 0.6
            });
        }
    }

    close() {
        gsap.to(this.formWrapper, {
            backgroundColor: "rgba(0,0,0,0)",
            ease: this.ease,
            duration: 0.6
        });

        if (window.innerWidth > this.breakpoint) {
            gsap.to(this.formDom, {
                scale: 0,
                opacity: 0,
                ease: this.ease,
                duration: 0.6,
                onComplete: () => {
                    gsap.set(this.formDom, {y: 0, scale: 0, opacity: 0})
                    gsap.set(this.formWrapper, {visibility: "hidden"});
                    this.isOpen = false;
                    lenis.start();
                }
            });
        }
        else {
            gsap.to(this.formDom, {
                y: "100%",
                ease: this.ease,
                duration: 0.6,
                onComplete: () => {
                    gsap.set(this.formDom, {y: "100%", scale: 1, opacity: 1})
                    gsap.set(this.formWrapper, {visibility: "hidden"});
                    this.isOpen = false;
                    lenis.start();
                }
            });
        }
    }

    save() {
        console.log("save")
    }

    cancel() {
        console.log("cancel");
    }
}

export default AutofillForm;
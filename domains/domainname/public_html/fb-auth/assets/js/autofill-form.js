import APIFetch from "/fb-content/assets/js/apifetch.js";

class AutofillForm {
    isOpen = false;
    ease = "power2.inOut";
    breakpoint = 991;
    cards = [];

    constructor(type, dom) {
        if (type != "autofill-delivery" && type != "autofill-billing") throw new Error("Ismeretlen típus");
        if (!gsap) throw new Error("GSAP nem található");
        
        this.autofillType = type.split('-')[1];
        
        this.initDOM(dom);
        this.bindEvents();
        this.handleAutofillFocus();

        this.fetchContent();
    }

    initDOM(dom) {
        this.formDom = dom;
        this.formWrapper = dom.parentElement;

        this.form = {
            "autofillName": {
                "dom": this.formDom.querySelector("[name=autofill-name]"),
                "errorMessage": "Kérem ne hagyja üresen a címet",
                get value() { return this.dom?.value }
            },
            "zipCode": {
                "dom": this.formDom.querySelector("[name=zip]"),
                "errorMessage": "Kérem helyes irányítószámot adjon meg",
                get value() { return this.dom?.value }
            },
            "city": {
                "dom": this.formDom.querySelector("[name=city]"),
                "errorMessage": "Kérem adja meg helyesen a település nevét",
                get value() { return this.dom?.value }
            },
            "streetHouse": {
                "dom": this.formDom.querySelector("[name=street-house]"),
                "errorMessage": "Kérem a '[közterület neve] [közterület típusa] [házszám]' formátumnak megfelelően töltse ki",
                get value() { return this.dom?.value }
            },
        }

        this.validationRules = {
            autofillName: (e) => e.length > 0,
            zipCode: /^[1-9]{1}[0-9]{3}$/,
            city: /^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/,
            streetHouse: /^[A-ZÁÉÍÓÖŐÚÜŰ][a-záéíóöőúüű]+ [a-záéíóöőúüű]{2,} \d{1,}(?:\/[A-Z]+)?$/
        };
        
        this.cardsContainer = this.formWrapper.previousElementSibling;
        this.savedCardsContainer = this.cardsContainer?.querySelector(".saved-cards");

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

        // Beviteli mező fókusz eseményei
        document.querySelectorAll(".input-group").forEach((e) => this.handleInputGroupFocus(e));

        // Beviteli mezők validálási eseményei
        for (let field in this.form) {
            let element = this.form[field];
            if (element.noValidate) continue;

            element.dom.addEventListener("change", () => {
                const error = this.validateField(field);
                this.toggleFieldState(field, error);
            });
        }
    }

    handleAutofillFocus() {
        setTimeout(() => {
            document.querySelectorAll(".input-group").forEach(el => {
                const label = el.querySelector('label');
                const input = el.querySelector('input, select');
    
                if (!input) return;

                if (input.value) label.classList.add('focus');
            });
        }, 10);
    }

    dropFocus() {
        document.querySelectorAll(".input-group").forEach(el => {
            const label = el.querySelector('label');
            const input = el.querySelector('input, select');

            if (!input) return;

            label.classList.remove('focus');
        });
    }

    resetValidity() {
        for (let field in this.form) {
            let element = this.form[field];
            if (element.noValidate) continue;

            this.toggleFieldState(field, null);
        }
    }

    handleInputGroupFocus(e) {
        const inputGroup = e;
        const label = inputGroup.querySelector('label');
        const input = inputGroup.querySelector('input, select');

        input.addEventListener("focus", () => {
            if (input.nodeName !== "SELECT") label.classList.add('focus');
        });

        input.addEventListener("focusout", () => {
            if (input.value === "") label.classList.remove('focus');
        });
    }

    // Beviteli mező validálása
    validateField(name) {
        // A mező lekérdezése a paraméterek alapján
        const field = this.form[name] || undefined;
        if (!field) return false;

        // Validátor függvény, Aktuális érték és hibaüzenet megszerzése
        const validator = this.validationRules[name];
        const value = field.value;
        const error = field.errorMessage;

        // Változók ellenőrzése
        if (!validator) return null;
        if (!value) return error;

        // Érték validálása
        return (typeof validator == 'function') ? validator(value)?null:error : validator.test(value)?null:error;
    }

    toggleFieldState(name, error) {
        // A mező lekérdezése a paraméterek alapján
        const field = this.form[name] || undefined;
        if (!field) return false;

        const errorWrapper = field.dom.closest('.input-group').querySelector('.message-wrapper');
        const messageContainer = errorWrapper.querySelector(".error-message");
        const validity = error ? "invalid" : "valid";
        const oppositeValidity = error ? "valid" : "invalid";
        const didValidityChange = field.dom.classList.contains(oppositeValidity) || field.dom.classList.length == 0;

        if (!didValidityChange) return;

        field.dom.classList.add(validity);
        field.dom.classList.remove(oppositeValidity);
        
        if (error) {
            messageContainer.innerHTML = error;

            gsap.set(errorWrapper, {visibility: "visible"});
            gsap.to(errorWrapper, {
                height: 21,
                opacity: 1,
                ease: "power2.inOut",
                duration: 0.3
            });
        }
        else {
            gsap.to(errorWrapper, {
                height: 0,
                opacity: 0,
                ease: "power2.inOut",
                duration: 0.3,
                onComplete: () => {
                    gsap.set(errorWrapper, {visibility: "hidden"});
                }
            })
        }
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

    reset() {
        this.formDom.reset();
        this.close();
        this.dropFocus();
        this.resetValidity();
    }

    updateUI() {
        console.log(this.cards);
        this.savedCardsContainer.innerHTML = "";

        for (let card of this.cards) {
            this.savedCardsContainer.innerHTML += `
                <div class="card">
                    <div class="card-body">
                        <div class="card-title">${card.name}</div>
                        <div class="card-address">${card.zip}</div>
                    </div>
                </div>
            `;
        }
    }

    // Kártya metódusok
    addCard(card) {
        this.cards.push(card);
    }

    removeCard(cardId) {
        this.cards = this.cards.filter(e => e.id !== cardId);
    }

    updateCard(card) {

    }

    // Form validáció
    validateForm() {
        let valid = true;
        for (let field in this.form) {
            let element = this.form[field];
            if (element.noValidate) continue;

            const error = this.validateField(field);
            this.toggleFieldState(field, error);

            if (error) valid = false;
        }
        return valid;
    }

    // Backend metódusok
    async save(e, action = "add") {
        console.log(action)
        if (!this.validateForm()) return;

        const data = new FormData(this.formDom);
        data.append("type", this.autofillType);
        const result = await APIFetch(action == "add" ? "/api/autofill/add" : "/api/autofill/update", "POST", data, false);

        if (result.ok) {
            const card = await result.json();
            this.addCard(card);

            if (action == "add") {
                this.addCard();
            }
            else {
                this.updateCard();
            }

            this.reset();
        } else {
            console.log(result);
        }
    }

    async cancel() {
        this.reset();
    }

    async fetchContent() {
        const result = await APIFetch("/api/autofill/get", "GET", {type: this.autofillType});
        if (result.ok) {
            this.cards = await result.json();
            this.updateUI();
        } else {
            console.log(result);
        }
    }
}

export default AutofillForm;
import APIFetch from "/fb-content/assets/js/apifetch.js";

class AutofillForm {
    // Frontend tulajdonságok
    isOpen = false;
    ease = "power2.inOut";
    breakpoint = 991;

    // Backend tulajdonságok
    cards = []; // Az összes kártya (adatbázissal szinkronizálva)
    currentCardId = null; // Az aktuális kártya azonosítója (szerkesztés űrlaphoz)
    state = "add"; // Az űrlap aktuális feladatköre (add - új cím, modify - cím módosítása)

    constructor(type, dom) {
        if (type != "autofill-delivery" && type != "autofill-billing") throw new Error("Ismeretlen típus");
        if (!gsap) throw new Error("GSAP nem található");
        
        this.autofillType = type.split('-')[1];
        
        this.initDOM(dom);
        this.bindEvents();
        this.handleAutofillFocus();

        this.fetchContent();
    }

    // DOM elemek lekérdezése és eltárolása későbbi használathoz
    initDOM(dom) {
        this.formDom = dom;
        this.formWrapper = dom.parentElement;

        this.form = {
            "autofillName": {
                "dom": this.formDom.querySelector("[name=autofill-name]"),
                "errorMessage": "Kérem ne hagyja üresen a címet",
                get value() { return this.dom?.value },
                set value(val) { this.dom.value = val }
            },
            "zipCode": {
                "dom": this.formDom.querySelector("[name=zip]"),
                "errorMessage": "Kérem helyes irányítószámot adjon meg",
                get value() { return this.dom?.value },
                set value(val) { this.dom.value = val }
            },
            "city": {
                "dom": this.formDom.querySelector("[name=city]"),
                "errorMessage": "Kérem adja meg helyesen a település nevét",
                get value() { return this.dom?.value },
                set value(val) { this.dom.value = val }
            },
            "streetHouse": {
                "dom": this.formDom.querySelector("[name=street-house]"),
                "errorMessage": "Kérem a '[közterület neve] [közterület típusa] [házszám]' formátumnak megfelelően töltse ki",
                get value() { return this.dom?.value },
                set value(val) { this.dom.value = val }
            },
        }

        this.validationRules = {
            autofillName: (e) => e.length > 0,
            zipCode: /^[1-9]{1}[0-9]{3}$/,
            city: /^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/,
            streetHouse: /^[A-ZÁÉÍÓÖŐÚÜŰ][a-záéíóöőúüű]+ [a-záéíóöőúüű]{2,} \d{1,}(\.|(?:\/[A-Z]+(?: \d+\/\d+)?))$/
        };
        
        this.cardsContainer = this.formWrapper.previousElementSibling;
        this.savedCardsContainer = this.cardsContainer?.querySelector(".saved-cards");

        this.openButton = this.cardsContainer.querySelector(".add-field")
        this.closeButton = this.formDom.querySelector(".form-close");
        this.saveButton = this.formDom.querySelector(".form-save");
        this.cacelButton = this.formDom.querySelector(".form-cancel");

        if (!this.openButton) throw new Error("Nincs kinyitó gomb");
    }

    // Eseménykezelők hozzárendelése elemekhez
    bindEvents() {
        // Gombok kattintási eseményei
        this.savedCardsContainer.addEventListener("click", this.handleCardClick.bind(this));
        this.openButton.addEventListener("click", this.open.bind(this));
        this.closeButton.addEventListener("click", this.close.bind(this));
        this.saveButton.addEventListener("click", this.save.bind(this));
        this.cacelButton.addEventListener("click", this.cancel.bind(this));

        // Gyors kilépés az ESC gombbal
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

    open(card = null) {
        if (this.isOpen) return;
        
        this.isOpen = true;
        lenis.stop();

        if (this.state == "modify" && card) {
            this.form.autofillName.value = card.name;
            this.form.zipCode.value = card.zip;
            this.form.city.value = card.city;
            this.form.streetHouse.value = card.street_house;

            this.form.autofillName.dom.dispatchEvent(new Event("focus"));
            this.form.zipCode.dom.dispatchEvent(new Event("focus"));
            this.form.city.dom.dispatchEvent(new Event("focus"));
            this.form.streetHouse.dom.dispatchEvent(new Event("focus"));
        }

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

    // Kártya metódusok
    // Kártyák frissítése
    updateCardUI() {
        this.savedCardsContainer.innerHTML = "";

        for (let card of this.cards) {
            this.savedCardsContainer.innerHTML += this.getCardHTML(card);
        }
    }

    getCardFromElement(element) {
        const id = element.id.split('-')[1];
        return this.cards.find(e => e.id == id);
    }

    getCardElementFromId(id) {
        return this.savedCardsContainer.querySelector('#card-' + id);
    }

    handleCardClick(e) {
        if (e.target.closest('.action-edit')) {
            this.state = "modify";

            const element = e.target.closest('.card');
            const card = this.getCardFromElement(element);
            
            this.currentCardId = Number(card.id);
            this.open(card);
        }

        if (e.target.closest('.action-delete')) {
            this.state = "delete";

            const element = e.target.closest('.card');
            const card = this.getCardFromElement(element);
            
            this.remove(card, element);
        }
    }

    addCard(card) {
        this.state = "add";
        this.cards.push(card);
        this.savedCardsContainer.innerHTML += this.getCardHTML(card);
    }

    removeCard(card, dom) {
        this.cards = this.cards.filter(e => e.id !== card.id);
        dom.remove();
    }

    updateCard(id, data) {
        const currentCard = this.getCardElementFromId(id);
        const currentCardBackend = this.cards.filter(e => e.id == id)[0];

        // Háttér kártya frissítése
        currentCardBackend.name = data.name;
        currentCardBackend.zip = data.zip;
        currentCardBackend.city = data.city;
        currentCardBackend.street_house = data.street_house;


        // Frontend frissítése
        currentCard.querySelector(".card-title").innerHTML = data.name;
        currentCard.querySelector(".card-address").innerHTML = `${data.city} ${data.street_house}`;
    }

    getCardHTML(card) {
        return `
            <div class="card" id="card-${card.id}">
                <div class="card-body">
                    <div class="card-title">${card.name}</div>
                    <div class="card-address">${card.city} ${card.street_house}</div>
                    <div class="card-actions">
                        <button class="action-edit">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill edit" viewBox="0 0 16 16">
                                <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                            </svg>
                        </button>
                        <button class="action-delete">
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
        `;
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

    getFormData(json = false, additional = null) {
        const data = new FormData(this.formDom);
        data.append("type", this.autofillType);

        if (additional) {
            for (const [key, value] of Object.entries(additional)) {
                data.append(key, value);
            }              
        }
        
        if (json) {
            const dataJSON = {};
            data.forEach((value, key) => dataJSON[key] = value);
            return dataJSON;
        }
        return data;
    }

    // Backend metódusok
    async save(e) {
        if (!this.validateForm()) return;

        const isModify = this.state == "modify";
        const data = this.getFormData(isModify, isModify ? {id: this.currentCardId} : null); // Ha módosítunk, akkor JSON-ben küldjük az adatokat
        const result = await APIFetch(isModify ? "/api/autofill/update" : "/api/autofill/add", isModify ? "PUT" : "POST", data, isModify);

        if (result.ok) {
            const card = await result.json();

            if (isModify) {
                this.updateCard(this.currentCardId, card[0]);
            }
            else {
                this.addCard(card[0]);
            }

            this.reset();
        } else {
            console.log(result);
        }
    }

    cancel() {
        this.reset();
    }

    async remove(card, element) {
        const result = await APIFetch("/api/autofill/remove", "DELETE", {id: card.id, type: this.autofillType});

        if (result.ok) {
            this.removeCard(card, element);
        }
        else {
            console.log(result);
        }
    }

    async fetchContent() {
        const result = await APIFetch("/api/autofill/get", "GET", {type: this.autofillType});
        if (result.ok) {
            const adat = await result.json();
            if (adat.type == "EMPTY") {
                return;
            }
            else {
                this.cards = adat;
                this.updateCardUI();
            }
        } else {
            console.log(result);
        }
    }
}

export default AutofillForm;
import Confetti from "./confetti.js";
import APIFetch from "./apifetch.js";

// Segédfüggvény API kérésekhez


class Checkout {
    orderData;
    cartData;
    form;
    orderPlaced = false;
    overlayOpen = false;
    autofill = {};

    constructor() {
        if (!gsap) {
            throw new Error("Nincs GSAP");
        }

        this.init();
    }

    // UI metódusok
    async init() {
        try {
            // Kosár tartalom lekérése
            const fetchPromise = this.fetchAll();

            // DOM töltését megvárjuk
            await this.waitForLoad();

            // Inicializáljuk az elemeket és az eseménykezelőket
            this.initDOM();
            this.bindEvents();

            // Megvárjuk a tartalom letöltését
            await fetchPromise;

            this.generateAutofillOptions();
        } catch (err) {
            console.error(err);
        }
    }

    // Függvény, amelynek szerepe az oldal betöltésének megvárása
    async waitForLoad() {
        return new Promise((resolve) => {
            if (document.readyState === "complete") {
                resolve();
            } else {
                window.addEventListener("load", resolve);
            }
        });
    }

    // DOM elemek megkeresése
    initDOM() {
        this.resultOverlaySelector = '.checkout-result-overlay';
        this.resultOverlay = document.querySelector(this.resultOverlaySelector);
        this.confetti = new Confetti(this.resultOverlaySelector);

        this.errorOverlaySelector = '.checkout-error-overlay';
        this.errorOverlay = document.querySelector(this.errorOverlaySelector);
        this.errorOverlayCloser = this.errorOverlay.querySelector(".overlay-close");
        
        this.paymentButton = document.querySelector('.payment-button');
        
        this.formDOM = document.querySelector('.checkout-form');
        if (!this.formDOM) throw new Error("Nincs rendelési űrlap");
        
        this.validationRules = {
            email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
            zipCode: /^[1-9]{1}[0-9]{3}$/,
            name: /^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/,
            lastName: /^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/,
            firstName: /^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/,
            phone: /^(\+36|06)(\d{9})$/,
            taxNumber: /^\d{8}-\d{1,2}-\d{2}$/,
            city: /^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/,
            streetHouse: /^[A-ZÁÉÍÓÖŐÚÜŰ][a-záéíóöőúüű]+(?: [A-ZÁÉÍÓÖŐÚÜŰ][a-záéíóöőúüű]+)? [a-záéíóöőúüű]{2,} \d{1,}(\.?|(?:\/[A-Z]+(?: \d+\/\d+)?))$/
        };
        
        this.form = {
            "purchaseTypes": {
                "dom": this.formDOM.querySelector("#purchase-types"),
                "noValidate": true,
                get value() { return this.dom.querySelector(".checked").innerHTML || "Magánszemélyként rendelek"; }
            },
            "company": {
                "name": {
                    "dom": this.formDOM.querySelector("#company-name"),
                    "errorMessage": "Érvénytelen cégnév",
                    get value() { return this.dom?.value; }
                },
                "taxNumber": {
                    "dom": this.formDOM.querySelector("#tax-number"),
                    "errorMessage": "Érvénytelen adószám",
                    get value() { return this.dom?.value; }
                }
            },
            "customer": {
                "email": {
                    "dom": this.formDOM.querySelector('#email'),
                    "errorMessage": "Érvénytelen e-mail cím",
                    get value() { return this.dom?.value; }
                },
                "lastName": {
                    "dom": this.formDOM.querySelector("#last-name"),
                    "errorMessage": "Érvénytelen vezetéknév",
                    get value() { return this.dom?.value; }
                },
                "firstName": {
                    "dom": this.formDOM.querySelector("#first-name"),
                    "errorMessage": "Érvénytelen keresztnév",
                    get value() { return this.dom?.value; }
                },
                "phone": {
                    "dom": this.formDOM.querySelector("#phone"),
                    "errorMessage": "Érvénytelen telefonszám",
                    get value() { return this.dom?.value; }
                }
            },
        
            "delivery": {
                "autofill": {
                    "dom": this.formDOM.querySelector('#delivery-autofill'),
                    "noValidate": true,
                    get value() { return this.dom?.value }
                },
                "zipCode": {
                    "dom": this.formDOM.querySelector("#zip-code"),
                    "errorMessage": "Érvénytelen irányítószám.",
                    get value() { return this.dom?.value; }
                },
                "city": {
                    "dom": this.formDOM.querySelector("#city"),
                    "errorMessage": "A város mező nem lehet üres",
                    get value() { return this.dom?.value; }
                },
                "streetHouse": {
                    "dom": this.formDOM.querySelector("#street-house"),
                    "errorMessage": "Hibás utca és házszám",
                    get value() { return this.dom?.value; }
                }
            },
        
            "billing": {
                "sameAddress": {
                    "dom": this.formDOM.querySelector("#same-address"),
                    "noValidate": true,
                    get value() { return this.dom.checked || false; }
                },
                "autofill": {
                    "dom": this.formDOM.querySelector('#billing-autofill'),
                    "noValidate": true,
                    get value() { return this.dom?.value }
                },
                "zipCode": {
                    "dom": this.formDOM.querySelector("#billing-zip"),
                    "errorMessage": "Érvénytelen irányítószám",
                    get value() { return this.dom?.value; },
                },
                "city": {
                    "dom": this.formDOM.querySelector("#billing-city"),
                    "errorMessage": "A város mező nem lehet üres",
                    get value() { return this.dom?.value; }
                },
                "streetHouse": {
                    "dom": this.formDOM.querySelector("#billing-street-house"),
                    "errorMessage": "Hibás utca és házszám",
                    get value() { return this.dom?.value; }
                }
            }
        };

        this.handleAutofillFocus();
    }

    generateAutofillOptions() {
        if (!this.autofill) return;
        for (let type in this.autofill) {
            const select = this.form[type].autofill.dom;
            
            this.autofill[type].forEach(e => select.innerHTML += `<option value='${e.id}'>${e.name}</option>`);
        }
    }

    handleAutofill(type) {
        const section = this.form[type];
        const dom = section.autofill.dom;
        const selected = dom.value;
        const zip = section.zipCode.dom;
        const city = section.city.dom;
        const streetHouse = section.streetHouse.dom;
        
        if (selected == "def") {
            zip.value = "";
            city.value = "";
            streetHouse.value = "";

            zip.dispatchEvent(new Event("focusout"))
            city.dispatchEvent(new Event("focusout"))
            streetHouse.dispatchEvent(new Event("focusout"))

            this.toggleFieldState(type, "zipCode", null);
            this.toggleFieldState(type, "city", null);
            this.toggleFieldState(type, "streetHouse", null);
            return;
        }

        const data = this.autofill[type].filter(e => e.id == selected)[0];
        if (!data) return;

        zip.value = data.zip;
        city.value = data.city;
        streetHouse.value = data.street_house;

        zip.dispatchEvent(new Event("focus"))
        city.dispatchEvent(new Event("focus"))
        streetHouse.dispatchEvent(new Event("focus"))

        this.toggleFieldState(type, "zipCode", this.validateField(type, "zipCode"));
        this.toggleFieldState(type, "city", this.validateField(type, "city"));
        this.toggleFieldState(type, "streetHouse", this.validateField(type, "streetHouse"));
    }

    openResultOverlay(resultType = "success") {
        if (this.overlayOpen) return;
        
        this.overlayOpen = true;
        lenis.stop();

        if (resultType == "success") {
            gsap.set(this.resultOverlay, {opacity: 0, visibility: "visible"});
            gsap.to(this.resultOverlay, {
                opacity: 1,
                duration: 0.8,
                ease: "power2.inOut",
                onComplete: () => this.confetti.start()
            })

            gsap.to(this.resultOverlaySelector + " > .overlay-body > div, .overlay-body > p", {
                opacity: 1,
                stagger: 0.2,
                duration: 1,
                delay: 0.5,
                ease: "power2.inOut"
            });

            gsap.to(".back-to-home", {
                y: 0,
                duration: 0.8,
                delay: 0.5,
                ease: "power2.inOut"
            });

            gsap.to('#flower-1', {
                left: '-100px',
                ease: "power2.inOut",
                duration: 1,
                delay: 0.6
            });

            gsap.to('#flower-2', {
                right: '-200px',
                ease: "power2.inOut",
                duration: 1,
                delay: 0.8
            });

            gsap.to('#flower-3', {
                left: '-100px',
                ease: "power2.inOut",
                duration: 1,
                delay: 0.9
            });
        }
        else if (resultType == "error") {
            gsap.set(this.errorOverlay, {opacity: 0, visibility: "visible"});
            gsap.to(this.errorOverlay, {
                opacity: 1,
                duration: 0.8,
                ease: "power2.inOut",
            })

            gsap.to('#checkout-error-icon', {
                opacity: 1,
                scale: 1,
                duration: 0.5,
                ease: "power2.inOut",
                delay: 0.4
            });

            gsap.to(this.errorOverlaySelector + " > .overlay-body", {
                opacity: 1,
                duration: 0.5,
                ease: "power2.inOut",
                delay: 0.6
            });

            gsap.to(this.errorOverlaySelector + " > .overlay-close", {
                y: 0,
                duration: 0.5,
                ease: "back",
                delay: 0.8
            });
        }
    }

    closeErrorOverlay() {
        if (!this.overlayOpen) return;

        gsap.to(this.errorOverlaySelector, {
            opacity: 0,
            duration: 0.8,
            ease: "power2.inOut",
            onComplete: () => {
                lenis.start();

                gsap.set(this.errorOverlaySelector, {visibility: "hidden"});
                gsap.set(this.errorOverlaySelector + " > .overlay-close", {y:82});
                gsap.set(this.errorOverlaySelector + " > .overlay-body", {opacity: 0});
                gsap.set('#checkout-error-icon', {opacity: 0, scale: 0});
            }
        })

        this.overlayOpen = false;
        this.orderPlaced = false;
    }

    // Beviteli mező validálása
    validateField(section, name = null) {
        // A mező lekérdezése a paraméterek alapján
        const field = name ? this.form[section][name] || undefined : this.form[section] || undefined;
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

    toggleFieldState(section, name = null, error) {
        // A mező lekérdezése a paraméterek alapján
        const field = name ? this.form[section][name] || undefined : this.form[section] || undefined;
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
                height: "auto",
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

    // Eseménykezelők
    bindEvents() {
        // Kattintási események
        document.addEventListener("click", (e) => this.handleClickEvents(e));
        this.errorOverlayCloser.addEventListener("click", this.closeErrorOverlay.bind(this));

        // Automatikus kitöltési mezők eseményei
        this.form.delivery.autofill.dom?.addEventListener("input", () => this.handleAutofill("delivery"))
        this.form.billing.autofill.dom?.addEventListener("input", () => this.handleAutofill("billing"))

        // Beviteli mező fókusz eseményei
        document.querySelectorAll(".input-group").forEach((e) => this.handleInputGroupFocus(e));

        // Szállítási és számlázási cím megyegyezik
        this.form.billing.sameAddress.dom.addEventListener("input", this.handleSameAddressInput.bind(this));

        // Rendelés gomb kattintás
        this.paymentButton.addEventListener("click", async () => {
            if (this.orderPlaced) return;

            const result = await this.placeOrder();
        })

        // Beviteli mezők validálási eseményei
        for (let section in this.form) {
            if (this.form[section].dom) {
                let element = this.form[section];
                if (element.noValidate) continue;

                element.dom.addEventListener("change", () => {
                    const error = this.validateField(section);
                    this.toggleFieldState(section, null, error);
                });
            }
            else {
                for (let field in this.form[section]) {
                    let element = this.form[section][field];
                    if (element.noValidate) continue;

                    element.dom.addEventListener("change", () => {
                        const error = this.validateField(section, field);
                        this.toggleFieldState(section, field, error);
                    });
                }
            }
        }
    }
    
    handleClickEvents(e) {
        if (e.target.closest('.purchase-type-radios')) {
            this.handlePurchaseTypeRadios(e);
        }
    }

    handleAutofillFocus() {
        setTimeout(() => {
            document.querySelectorAll(".input-group").forEach(el => {
                const label = el.querySelector('label');
                const input = el.querySelector('input, select');
    
                if (!input) return;
                if (input.value !== "" && input.value !== "def") label.classList.add('focus');
            });
        }, 10);
    }

    handleInputGroupFocus(e) {
        const inputGroup = e;
        const label = inputGroup.querySelector('label');
        const input = inputGroup.querySelector('input, select');

        if (input.nodeName === "SELECT") {
            input.addEventListener("input", () => {
                if (input.value != "def") label.classList.add('focus');
                else label.classList.remove('focus');
            });
        }
        else {
            input.addEventListener("focus", () => {
                label.classList.add('focus');
            });
    
            input.addEventListener("focusout", () => {
                if (input.value === "") label.classList.remove('focus');
            });
        }
    }

    handlePurchaseTypeRadios(e) {
        if (this.isPurchaseRadioAnimating) return;

        const radio = e.target.closest('.radio');
        const group = radio.parentElement;

        const target = this.formDOM.querySelector(group.dataset.target);
        const inputs = target.querySelectorAll("input, select");

        const border = group.querySelector(".border");
        const index = Array.from(group.children).filter(el => el.className !== 'border').indexOf(radio);
        const left = `${index * 50}%`;

        if (getComputedStyle(border).getPropertyValue('--left') === left) return;

        group.querySelector('.checked')?.classList.remove('checked');
        radio.classList.add('checked');

        if (border && index >= 0) {
            this.isPurchaseRadioAnimating = true;
            gsap.to(border, {
                "--left": left,
                duration: 0.5,
                ease: "power3.inOut",
                onComplete: () => this.isPurchaseRadioAnimating = false
            });

            if (!target) return;

            if (index === 1) {
                inputs.forEach(e => e.disabled = false);
                gsap.to(target, {
                    height: "auto",
                    opacity: 1,
                    duration: 0.5,
                    ease: "power2.inOut"
                });
            }
            else {
                gsap.to(target, {
                    height: 0,
                    opacity: 0,
                    duration: 0.5,
                    ease: "power2.inOut",
                    onComplete: () => {
                        inputs.forEach(e => e.disabled = true);
                    }
                });
            }
        }
    }

    handleSameAddressInput(e) {
        const checked = this.form.billing.sameAddress.value;
        const parent = e.target.parentElement;
        const targetSelector = parent.dataset.target;
        const targetElement = targetSelector ? parent.closest('section').querySelector(targetSelector) : parent.nextElementSibling;

        const inputs = targetElement.querySelectorAll('input, select');

        if (checked) {
            gsap.to(targetElement, {
                opacity:0,
                height: 0,
                duration: 0.5,
                ease: 'power2.inOut',
                onComplete: () => {
                    inputs.forEach(e => e.disabled = true);
                }
            });
        }
        else {
            inputs.forEach(e => e.disabled = false);
            gsap.to(targetElement, {
                opacity: 1,
                height: 'auto',
                duration: 0.5,
                ease: 'power2.inOut'
            });
        }
    }

    // Form validáció
    validateForm() {
        let valid = true;
        for (let section in this.form) {
            if (section === "billing" && this.form.billing.sameAddress.value) {
                continue;
            }
    
            if (section === "company" && this.form.purchaseTypes.value == "Magánszemélyként rendelek") {
                continue;
            }
            if (this.form[section].dom) {
                let element = this.form[section];
                if (element.noValidate) continue;

                const error = this.validateField(section);
                this.toggleFieldState(section, null, error);

                if (error) valid = false;
            }
            else {
                for (let field in this.form[section]) {
                    let element = this.form[section][field];
                    if (element.noValidate) continue;

                    const error = this.validateField(section, field);
                    this.toggleFieldState(section, field, error);

                    if (error) valid = false;
                }
            }
        }
        return valid;
    }

    // Backend metódusok
    async placeOrder() {
        if (this.orderPlaced) return;

        const isValid = this.validateForm();
        if (!isValid) return;

        this.orderPlaced = true;

        const data = new FormData(this.formDOM);
        data.delete("same-address");
        data.append("same-address", this.form.billing.sameAddress.value);
        data.append("purchase-type", this.form.purchaseTypes.value);
        
        
        const result = await APIFetch("/api/order/place", "POST", data, false);
        
        if (result.ok) {
            const data = await result.json();
            this.openResultOverlay("success");
        } else {
            this.openResultOverlay('error');
        }
    }

    async fetchAll() {
        await this.validateCheckoutStocks();
        await this.fetchCartData();
        await this.fetchAutofill();
    }

    // Lekéri a kosár tartalmát
    async fetchCartData() {
        const result = await APIFetch("/api/cart/get", "GET");

        if (result.ok) {
            const data = await result.json();
            this.cartData = data.message;
            if (data.type == "SUCCESS") {
                this.cartData.totalPrice = this.cartData.reduce((a, b) => a + (b.unit_price * b.quantity), 0);
            }
        } else {
            throw new Error("Hiba történt a kosár lekérdezése során: " + await result.json());
        }
    }

    async validateCheckoutStocks() {
        const result = await APIFetch("/api/cart/check", "GET");
        
        if (!result.ok || (await result.json()).type === "ERROR") {
            window.location.href = '/';
        }
    }

    // Lekéri az automatikus kitöltési mezőket
    async fetchAutofill() {
        const result = await APIFetch("/api/autofill/get", "GET", {type: "all"});

        if (result.ok) {
            const data = await result.json();
            if (data.type && data.type == "EMPTY") {
                this.autofill = null;
            }
            else {
                this.autofill = data;
            }
        } else {
            throw new Error("Hiba történt az automatikus kitöltési mezők lekérdezése során: " + await result.json());
        }
    }
}

new Checkout();
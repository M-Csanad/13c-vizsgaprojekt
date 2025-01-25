import Confetti from "./confetti.js";

// Segédfüggvény API kérésekhez
const APIFetch = async (url, method, body = null, encode = true) => {
    try {
        const params = {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (body) params.body = (encode) ? JSON.stringify(body) : body;

        const response = await fetch(url, params);

        return response;
    } catch (e) {
        return e;
    }
};

class Checkout {
    orderData;
    cartData;
    form;
    orderPlaced = false;

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
            const fetchPromise = this.fetchCartData();

            // DOM töltését megvárjuk
            await this.waitForLoad();

            // Inicializáljuk az elemeket és az eseménykezelőket
            this.initDOM();
            this.bindEvents();

            // Megvárjuk a tartalom letöltését
            await fetchPromise;

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

    // DOM elemek2megkeresése
    initDOM() {
        this.resultOverlay = '.checkout-result-overlay';
        this.confetti = new Confetti(this.resultOverlay);
        
        this.paymentButton = document.querySelector('.payment-button');
        
        this.formDOM = document.querySelector('.checkout-form');
        if (!this.formDOM) throw new Error("Nincs rendelési űrlap");
        
        this.validationRules = {
            email: /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/,
            zipCode: /^[1-9]{1}[0-9]{3}$/,
            name: /^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/,
            phone: /^(\+36|06)(\d{9})$/,
            taxNumber: /^\d{8}-\d{1,2}-\d{2}$/,
            streetHouse: /^[A-ZÁÉÍÓÖŐÚÜŰ][a-záéíóöőúüű]+ [a-záéíóöőúüű]{2,} \d{1,}(?:\/[A-Z]+)?$/
        };
        
        this.form = {
            "autofill": {
                "dom": this.formDOM.querySelector('#autofill'),
                get value() { return this.dom.value || undefined }
            },
            "purchaseTypes": {
                "dom": this.formDOM.querySelector("#purchase-types"),
                "noValidate": true,
                get value() { return this.dom.querySelector(".checked").innerHTML || "Magánszemélyként rendelek"; }
            },
            "company": {
                "name": {
                    "dom": this.formDOM.querySelector("#company-name"),
                    "errorMessage": "Érvénytelen cégnév",
                    get value() { return this.dom.value || undefined; }
                },
                "taxNumber": {
                    "dom": this.formDOM.querySelector("#tax-number"),
                    "errorMessage": "Érvénytelen adószám",
                    get value() { return this.dom.value || undefined; }
                }
            },
            "customer": {
                "email": {
                    "dom": this.formDOM.querySelector('#email'),
                    "errorMessage": "Érvénytelen e-mail cím",
                    get value() { return this.dom.value || undefined; }
                },
                "lastName": {
                    "dom": this.formDOM.querySelector("#last-name"),
                    "errorMessage": "Érvénytelen vezetéknév",
                    get value() { return this.dom.value || undefined; }
                },
                "firstName": {
                    "dom": this.formDOM.querySelector("#first-name"),
                    "errorMessage": "Érvénytelen keresztnév",
                    get value() { return this.dom.value || undefined; }
                },
                "phone": {
                    "dom": this.formDOM.querySelector("#phone"),
                    "errorMessage": "Érvénytelen telefonszám",
                    get value() { return this.dom.value || undefined; }
                }
            },
        
            "delivery": {
                "zipCode": {
                    "dom": this.formDOM.querySelector("#zip-code"),
                    "errorMessage": "Érvénytelen irányítószám.",
                    get value() { return this.dom.value || undefined; }
                },
                "city": {
                    "dom": this.formDOM.querySelector("#city"),
                    "errorMessage": "A város mező nem lehet üres",
                    get value() { return this.dom.value || undefined; }
                },
                "streetHouse": {
                    "dom": this.formDOM.querySelector("#street-house"),
                    "errorMessage": "Hibás utca és házszám",
                    get value() { return this.dom.value || undefined; }
                }
            },
        
            "billing": {
                "sameAddress": {
                    "dom": this.formDOM.querySelector("#same-address"),
                    "noValidate": true,
                    get value() { return this.dom.checked || false; }
                },
                "zipCode": {
                    "dom": this.formDOM.querySelector("#billing-zip"),
                    "errorMessage": "Érvénytelen irányítószám",
                    get value() { return this.dom.value || undefined; },
                },
                "city": {
                    "dom": this.formDOM.querySelector("#billing-city"),
                    "errorMessage": "A város mező nem lehet üres",
                    get value() { return this.dom.value || undefined; }
                },
                "streetHouse": {
                    "dom": this.formDOM.querySelector("#billing-street-house"),
                    "errorMessage": "Hibás utca és házszám",
                    get value() { return this.dom.value || undefined; }
                }
            }
        };
        
        this.handleAutofillFocus();
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

    // Eseménykezelők
    bindEvents() {
        // Kattintási események
        document.addEventListener("click", (e) => this.handleClickEvents(e));

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

                element.dom.addEventListener("focusout", () => {
                    const valid = this.validateField(section);
                    console.log(section, valid);
                });
            }
            else {
                for (let field in this.form[section]) {
                    let element = this.form[section][field];
                    if (element.noValidate) continue;

                    element.dom.addEventListener("focusout", () => {
                        const valid = this.validateField(section, field);
                        console.log(section + " " + field, valid);
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
        document.querySelectorAll(".input-group").forEach(el => {
            const label = el.querySelector('label');
            const input = el.querySelector('input, select');

            if (!input) return;

            if (input.value !== "") label.classList.add('focus');
        });
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

    }

    // Backend metódusok
    async placeOrder() {
        this.orderPlaced = true;

        const data = new FormData(this.formDOM);
        data.append("same-address", this.form.billing.sameAddress.value);
        data.append("purchase-type", this.form.purchaseTypes.value);
        
        
        const result = await APIFetch("/api/order/place", "POST", data, false);
        
        this.orderPlaced = false;
        if (result.ok) {
            const data = await result.json();

            gsap.set(this.resultOverlay, {opacity: 0, visibility: "visible"});
            gsap.to(this.resultOverlay, {
                opacity: 1,
                duration: 0.8,
                ease: "power2.inOut",
                onComplete: () => this.confetti.start()
            })

            gsap.to(".overlay-body > div, .overlay-body > p", {
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
            // Konfetti
            console.log("Sikeres rendelés");
        } else {
            throw new Error("Hiba történt a kosár lekérdezése során: " + await result.json());
        }
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
}

new Checkout();
// Segédfüggvény API kérésekhez
const APIFetch = async (url, method, body = null) => {
    try {
        const params = {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        };

        if (body) params.body = JSON.stringify(body);

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

    // DOM elemek megkeresése
    initDOM() {

        this.formDOM = document.querySelector('.checkout-form');
        if (!this.formDOM) throw new Error("Nincs rendelési űrlap");

        this.form = {
            "sections": {
                "autofill": this.formDOM.querySelector('#autofill'),
                "customer": {
                    "email": this.formDOM.querySelector('#email'),
                    "lastName": this.formDOM.querySelector("#last-name"),
                    "firstName": this.formDOM.querySelector("#first-name"),
                    "phone": this.formDOM.querySelector("#phone")
                },
                "delivery": {
                    "zipCode": this.formDOM.querySelector("#zip-code"),
                    "city": this.formDOM.querySelector("#city"),
                    "street": this.formDOM.querySelector("#street"),
                    "houseNumber": this.formDOM.querySelector("#house-number"),
                },
                "billing": {
                    "sameAddress": this.formDOM.querySelector("#same-address"),
                    "purchaseTypes": this.formDOM.querySelector("#purchase-types"),
                    "name": this.formDOM.querySelector("#billing-name"),
                    "zipCode": this.formDOM.querySelector("#billing-zip"),
                    "city": this.formDOM.querySelector("#billing-city"),
                    "street": this.formDOM.querySelector("#billing-street"),
                    "houseNumber": this.formDOM.querySelector("#billing-house-number"),
                }
            }
        };

        this.handleAutofillFocus();
    }

    // Eseménykezelők
    bindEvents() {
        document.addEventListener("click", (e) => this.handleClickEvents(e));
    }
    
    handleClickEvents(e) {
        if (e.target.closest('.input-group')) {
            this.handleInputGroupFocus(e);
        }

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
        const inputGroup = e.target.closest('.input-group');
        const label = inputGroup.querySelector('label');
        const input = inputGroup.querySelector('input, select');

        if (input.nodeName !== "SELECT") label.classList.add('focus');

        inputGroup.addEventListener("focusout", () => {
            if (input.value === "") label.classList.remove('focus');
        });
    }

    handlePurchaseTypeRadios(e) {
        if (this.isPurchaseRadioAnimating) return;

        const radio = e.target.closest('.radio');
        const group = radio.parentElement;
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

            if (index === 0) {
                
            }
        }
    }

    // Form validáció
    validateForm() {

    }

    // Backend metódusok
    async placeOrder() {

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
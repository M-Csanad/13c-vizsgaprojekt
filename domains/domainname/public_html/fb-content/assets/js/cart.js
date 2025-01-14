import Popup from './popup.js';

const randomId = () => "el-" + (Math.random() + 1).toString(36).substring(7);
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

class Cart {
    isOpen = false;
    ease = "power2.inOut";
    ease2 = "power3.inOut";
    url = window.location.pathname;
    data = null;

    constructor() {
        this.init();
    }

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

            // Ha szükséges a kosarakat egyesíteni, akkor megkérdezzük a usert.
            if (this.data.type == "PROMPT") {
                const title = this.data.message.title;
                const description = this.data.message.description;
                const mergePrompt = new Popup(title, description, '/api/cart/merge');

                mergePrompt.open();
            }

            // UI frissítése
            await this.updateUI();
        } catch (err) {
            console.error(err);
        }
    }

    waitForLoad() {
        return new Promise((resolve) => {
            if (document.readyState === "complete") {
                resolve();
            } else {
                window.addEventListener("load", resolve);
            }
        });
    }

    initDOM() {
        // Kosár ablak
        this.domElement = document.querySelector(".cart");
        if (!this.domElement) throw new Error("Nincs cart osztályú elem.");

        // Id és selector
        this.domElement.id = randomId();
        this.selector = `#${this.domElement.id}`;

        this.background = document.querySelector('.modal-background');
        if (!this.background) throw new Error("Nincs háttér.");

        // Kinyitó gomb
        this.openButton = document.querySelector(".cart-open");
        if (!this.openButton) throw new Error("Nincs kinyitó gomb.");

        // Bezáró gomb
        this.closeButton = this.domElement.querySelector(".cart-close");
        if (!this.closeButton) throw new Error("Nincs becsukó gomb.");

        // Kosárba rakó gombok
        this.cartAddButtons = document.querySelectorAll(".add-to-cart");
        this.quantityInput = document.querySelector(".product-quantity");

        // GSAP ellenőrzés
        if (!gsap) throw new Error("A GSAP nem található");
        if (!lenis) throw new Error("A Lenis nem található");
    }

    
    // UI metódusok
    bindEvents() {
        this.openButton.addEventListener("click", this.open.bind(this));
        this.closeButton.addEventListener("click", this.close.bind(this));
        this.cartAddButtons.forEach((button) =>
            button.addEventListener("click", this.add.bind(this))
        );
    }

    open() {
        lenis.stop();
        gsap.set(this.domElement, { visibility: "visible" });
        gsap.set(this.background, { visibility: "visible" });
        gsap.to(this.domElement, {
            x: '0%',
            duration: 0.8,
            ease: this.ease2
        });
        gsap.to(this.background, {
            opacity: 1,
            duration: 0.8,
            ease: this.ease2
        });
    }

    close() {
        gsap.to(this.background, {
            opacity: 0,
            duration: 0.6,
            ease: this.ease,
        });
        gsap.to(this.domElement, {
            x: '100%',
            duration: 0.6,
            ease: this.ease,
            onComplete: () => {
                lenis.start();
                gsap.set(this.domElement, { visibility: "hidden" });
                gsap.set(this.background, { visibility: "hidden" });
            }
        });
    }

    // Backend metódusok
    async add() {
        const result = await APIFetch("/api/cart/add", "POST", {
            url: this.url,
            qty: this.quantityInput ? Number(this.quantityInput.value) : 1,
        });

        if (result.ok) {
            await this.fetchCartData();
            await this.updateUI();
        } else {
            console.log(result);
        }
    }

    async remove() {
        
    }

    async changeCount() {
        
    }

    async fetchCartData() {
        const result = await APIFetch("/api/cart/get", "GET");

        if (result.ok) {
            this.data = await result.json();
        } else {
            throw new Error("Hiba történt a kosár lekérdezése során: " + await result.json());
        }
    }

    async updateUI() {
        // console.log(this.data);
    }
}

export default Cart;

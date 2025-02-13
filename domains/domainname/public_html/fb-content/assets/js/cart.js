import Popup from './popup.js';
import APIFetch from './apifetch.js';

const randomId = () => "el-" + (Math.random() + 1).toString(36).substring(7);

// Segédfüggvény API kérésekhez

class Cart {
    isOpen = false;
    url = window.location.pathname;

    data = null;
    cartPrice = 0;
    lastFetchResultType = null;

    ease = "power2.inOut";
    ease2 = "power3.inOut";

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
            if (this.lastFetchResultType == "PROMPT") {
                const title = this.data.title;
                const description = this.data.description;

                // Létrehozzuk a felugró ablakot
                const mergePrompt = new Popup(title, description, async (response) => {
                    await this.handleCartMerge(response);
                    this.updateUI();
                });

                // Megnyitjuk a felugró ablakot
                mergePrompt.open();
            }
            else {
                // UI frissítése, ha nincs felugró ablak
                this.updateUI();
            }

        } catch (err) {
            console.error(err);
        }
    }

    // Függvény, amelynek szerepe az oldal betöltésének megvárása
    waitForLoad() {
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
        this.quickAddButtons = document.querySelectorAll(".quick-add");

        this.quantityInput = document.querySelector(".product-quantity");

        // Kosár mérete
        this.cartCount = this.domElement.querySelector(".cart-count");

        // Kosár elemek
        this.cartContainer = this.domElement.querySelector(".cart-items");

        // Üres kosár elem
        this.emptyMessage = this.domElement.querySelector(".cart-empty");

        // Összeg elem
        this.priceContainer = this.domElement.querySelector(".price > .value");

        // Vásárlás gomb
        this.checkoutButton = this.priceContainer.closest('a');

        // GSAP ellenőrzés
        if (!gsap) throw new Error("A GSAP nem található");
        if (!lenis) throw new Error("A Lenis nem található");
    }

    
    // UI metódusok
    // Eseménykezelők létrehozása
    bindEvents() {
        this.openButton.addEventListener("click", this.open.bind(this));
        this.closeButton.addEventListener("click", this.close.bind(this));

        this.cartAddButtons.forEach((button) =>
            button.addEventListener("click", this.add.bind(this))
        );

        this.quickAddButtons?.forEach(button => button.addEventListener("click", () => this.add(null, this.getUrlFromCard(button))));

        this.cartContainer.addEventListener("click", async (e) => {
            if (e.target.closest('.item-remove')) {
                const index = this.getProductDOMIndex(e);
                await this.remove(index);
                return;
            }
            
            if (e.target.closest('.number-field-add')) {
                e.stopPropagation();

                const index = this.getProductDOMIndex(e);
                const currentElement = e.target.closest('.number-field').querySelector('.product-quantity');

                await this.changeCount('+', index, currentElement.value, currentElement.getAttribute('max'));
                return;
            }

            if (e.target.closest('.number-field-subtract')) {
                e.stopPropagation();

                const index = this.getProductDOMIndex(e);
                const currentElement = e.target.closest('.number-field').querySelector('.product-quantity');

                await this.changeCount('-', index, currentElement.value, currentElement.getAttribute('max'));
                return;
            }
        })
    }

    // Segédfüggvény a termék indexének kinyeréséhez (index a kosár adatok tömbjében)
    getProductDOMIndex(clickEvent) {
        return Array.from(this.cartContainer.children).filter(e => e.nodeName != "HR").indexOf(clickEvent.target.closest('.cart-item'));
    }

    // Teljesen lefrissíti a kosár felhasználói felületét (Nincsen animálva)
    updateUI(flushContainer = true) {
        const isCartEmpty = this.data.length == 0;

        if (!isCartEmpty) this.priceContainer.innerHTML = this.cartPrice;

        this.cartCount.innerHTML = `${this.data.length} elem`;
        this.setElementVisibility(this.emptyMessage, isCartEmpty ? "visible" : "hidden");
        this.setElementVisibility(this.checkoutButton, isCartEmpty ? "hidden" : "visible");

        
        if (!flushContainer) return;

        this.cartContainer.innerHTML = "";
        this.data.forEach(product => {
            const thumbnail_uri = product.thumbnail_uri.split('.')[0];

            this.cartContainer.innerHTML += 
            `<div class="cart-item">
                <div class="wrapper">
                <div class="item-image">
                    <a href="http://localhost/${product.link_slug}">
                    <picture>
                        <source type="image/avif" srcset="${thumbnail_uri}-768px.avif 1x" media="(min-width: 768px)">
                        <source type="image/webp" srcset="${thumbnail_uri}-768px.webp 1x" media="(min-width: 768px)">
                        <source type="image/jpeg" srcset="${thumbnail_uri}-768px.jpg 1x" media="(min-width: 768px)">
                        <img 
                        src="${thumbnail_uri}.jpg" 
                        alt="${product.name}" 
                        loading="lazy">
                    </picture>
                    </a>
                </div>
                <div class="item-body">
                    <div class="item-info">
                        <div class="item-name">${product.name}</div>
                        <div class="item-price">
                            <div class="value">${product.unit_price}</div>
                            <div class="currency">Ft</div>
                        </div>
                        </div>
                    <div class="number-field">
                        <div class="number-field-subtract">-</div>
                        <input type="number" disabled name="product-quantity" class="product-quantity" placeholder="Darab" max="${product.stock}" min="1" value="${product.quantity}">
                        <div class="number-field-add">+</div>
                    </div>
                    <div class="item-remove">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                            <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                        </svg>
                    </div>
                </div>
                </div>
            </div>
            <hr>`;
        });
    }

    // Kinyitja a kosarat
    open() {
        if (this.isOpen) return;

        lenis.stop();
        this.isOpen = true;

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

    // Bezárja a kosarat
    close() {
        if (!this.isOpen) return;

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
                this.isOpen = false;
                gsap.set(this.domElement, { visibility: "hidden" });
                gsap.set(this.background, { visibility: "hidden" });
            }
        });
    }

    // A "Kosár üres" üzenet megjelenési állapotát változtatja
    setElementVisibility(element, visibility) {
        // Paraméterek ellenőrzése
        if (!element) throw new Error("Nincs elem megadva!");
        if (visibility != "hidden" && visibility != "visible") throw new Error(`Ismeretlen láthatóság (${visibility})`);

        // Ha már az az elem láthatósága, mint amit beadtunk, akkor nem történik semmi
        if (getComputedStyle(element).visibility == visibility) return;

        if (!this.isOpen) {
            if (visibility == "visible") {
                gsap.set(element, { opacity: 1, scale: 1, visibility: "visible" });
            }
            else {
                gsap.set(element, { opacity: 0, scale: 0, visibility: "hidden" });
            }
        }
        else {
            if (visibility == "visible") {
                gsap.set(element, { opacity: 0, scale: 0, visibility: "visible" });
                gsap.to(element, {
                    opacity: 1,
                    scale: 1,
                    ease: this.ease,
                    duration: 0.4
                });
            }
            else {
                gsap.to(element, {
                    opacity: 0,
                    scale: 0,
                    ease: this.ease,
                    duration: 0.4,
                    onComplete: () => {
                        element.style.visibility = "hidden"; // Itt nem GSAP-pal állítom, mert csak egy tulajdonságot állítok
                    }
                });
            }
        }
    }

    getUrlFromCard(card) {
        return this.url + "/" + card.id;
    }

    // Backend metódusok
    // Hozzáad egy terméket a kosárhoz
    async add(e, url = null) {
        const result = await APIFetch("/api/cart/add", "POST", {
            url: url ? url : this.url,
            qty: this.quantityInput ? Number(this.quantityInput.value) : 1,
        });

        if (result.ok) {
            await this.fetchCartData();
            this.updateUI();
            this.open();
        } else {
            console.log(result);
        }
    }

    // Kitöröl egy terméket a kosárból
    async remove(index) {
        const product = this.data[index];

        const result = await APIFetch("/api/cart/remove", "DELETE", { id: product.product_id });
        if (result.ok) {
            await this.fetchCartData();
            const removedProduct = Array.from(this.cartContainer.children).filter(e => e.nodeName != "HR")[index];
            const separator = removedProduct.nextElementSibling;
            
            gsap.to(separator, {
                marginTop: '-34px',
                opacity: 0,
                duration: 0.6,
                ease: this.ease
            });
            gsap.to(removedProduct, {
                height: 0,
                opacity: 0,
                duration: 0.6,
                ease: this.ease,
                onComplete: () => {
                    separator.remove();
                    removedProduct.remove();

                    this.updateUI(false); // Nem töröljük ki a kártyákat
                }
            });

        } else {
            throw new Error("Hiba történt a kosár lekérdezése során: " + await result.json());
        }
    }

    // Meglévő kosár elem mennyiségét változtatja
    async changeCount(operation = '+', index, currentValue, maxValue) {
        const product = this.data[index];
        const productElement = Array.from(this.cartContainer.children).filter(e=>e.nodeName!="HR")[index];
        
        if (operation != '+' && operation != '-') throw new Error("Ismeretlen művelet a changeCount függvényben: "+ operation)

        const change = 1 * (operation == '-' ? -1 : 1);
        if (Number(currentValue) + change > Number(maxValue) || Number(currentValue) + change < 1) return;

        const result = await APIFetch("/api/cart/update", "PUT", { operation: operation, product_id: product.product_id });
        if (result.ok) {
            const input = productElement.querySelector(".number-field-" + (operation == '+' ? 'add' : 'subtract'));
            handleQuantityChange(input, change);
            
            await this.fetchCartData();
            this.updateUI(false);
        }
        else {
            console.log(result)
        }
        
    }

    // Lekéri a kosár tartalmát
    async fetchCartData() {
        const result = await APIFetch("/api/cart/get", "GET");

        if (result.ok) {
            const data = await result.json();
            this.data = data.message;
            this.lastFetchResultType = data.type;
            if (data.type == "SUCCESS") {
                this.cartPrice = this.data.reduce((a, b) => a + (b.unit_price * b.quantity), 0);
            }
        } else {
            throw new Error("Hiba történt a kosár lekérdezése során: " + await result.json());
        }
    }

    // Egyesíti a kosarat (vendég -> felhasználó)
    async handleCartMerge(response) {
        const mergeResponse = await APIFetch("/api/cart/merge", "PUT", {response: response});

        if (mergeResponse.ok) {

            // Csak akkor kérjük le ismét az atadokat, ha azt meg is változtattuk
            await this.fetchCartData();
        }
        else {
            console.log(mergeResponse);
        }
    }
}

export default Cart;

import APIFetch from "./apifetch.js";
const randomId = () => "el-" + (Math.random() + 1).toString(36).substring(7);

class FilterWindow {
    isOpen = false;
    ease = "power3.inOut";
    breakpoint = 950;
    filters = {};
    products = [];
    url = '';

    constructor(products, onFilterUpdate) {
        if (!gsap) throw new Error("A GSAP nem található");
        
        this.products = products;
        this.onFilterUpdate = onFilterUpdate;
        this.url = window.location.pathname;

        this.initDOM();
        this.bindEvents();

        this.fetchProducts();
    }

    // DOM elemek inicializálása
    initDOM() {
        // Filter ablak inicializálása
        this.domElement = document.querySelector(".filters");
        if (!this.domElement) throw new Error("Nincs filter osztályú elem.");
        
        // Egyedi ID generálása a filter ablakhoz
        this.domElement.id = randomId();
        this.selector = `#${this.domElement.id}`;
        
        // Kinyitó gomb lekérése
        this.openButton = document.querySelector(".filter-open");
        if (!this.openButton) throw new Error("Nincs kinyitó gomb.");
        
        // Bezáró gomb lekérése a filter ablakon belül
        this.closeButton = this.domElement.querySelector(".filter-close");
        if (!this.closeButton) throw new Error("Nincs becsukó gomb.");

        this.applyButton = this.domElement.querySelector(".filter-apply");
        if (!this.applyButton) throw new Error("Nincs szűrő gomb.");

        this.clearButton = this.domElement.querySelector(".filter-clear");
        if (!this.clearButton) throw new Error("Nincs szűrő visszaállító gomb.");
        
        this.subcategoryId = document.querySelector('main').dataset.subcategoryId;
        
        // Szűrő elemek inicializálása
        this.initializeFilters();
    }

    // Események bindelése
    bindEvents() {
        this.openButton.addEventListener("click", this.open.bind(this));
        this.closeButton.addEventListener("click", this.close.bind(this));
        this.applyButton.addEventListener('click', () => this.applyFilters());
        this.clearButton.addEventListener('click', () => this.clearFilters());
    }

    // Szűrő elemek inicializálása
    initializeFilters() {
        const priceFilter = this.domElement.querySelector('[data-target="price"]');
        if (priceFilter) {
            const minInput = priceFilter.querySelector('#price_min');
            const maxInput = priceFilter.querySelector('#price_max');
            
            if (minInput && maxInput) {
                this.filters.price = {
                    min: null,
                    max: null,
                    inputs: { minInput, maxInput }
                };

                minInput.addEventListener('change', () => {
                    this.filters.price.min = minInput.value ? parseInt(minInput.value) : null;
                });
                maxInput.addEventListener('change', () => {
                    this.filters.price.max = maxInput.value ? parseInt(maxInput.value) : null;
                });
            }
        }
        
        const stockFilter = this.domElement.querySelector('[data-target="stock"]');
        if (stockFilter) {
            const inStockCheckbox = stockFilter.querySelector('#in_stock');
            if (inStockCheckbox) {
                this.filters.stock = {
                    value: false,
                    input: inStockCheckbox
                };

                inStockCheckbox.addEventListener('change', () => {
                    this.filters.stock.value = inStockCheckbox.checked;
                });
            }
        }
    }

    // Termékek lekérése az API segítségével
    async fetchProducts() {
        try {
            const response = await APIFetch('/api/subcategory/products', 'POST', {
                url: this.url
            });
    
            if (!response.ok) throw new Error('A hálózati kérés sikertelen');
    
            const data = await response.json();
            if (data.type !== 'ERROR') {
                this.products = data.message;
            } else {
                console.error('Nem sikerült lekérni a termékeket:', data.message);
            }
        } catch (error) {
            console.error('Hiba a termékek lekérésekor:', error);
        }
    }

    // Szűrés alkalmazása a termékekre
    applyFilters() {
        const filteredProducts = this.products.filter(product => {
            if (this.filters.price?.min && product.unit_price < this.filters.price.min) {
                return false;
            }
            if (this.filters.price?.max && product.unit_price > this.filters.price.max) {
                return false;
            }
            
            if (this.filters.stock?.value && product.stock <= 0) {
                return false;
            }
    
            return true;
        });
    
        this.updateProductDisplay({ 
            type: filteredProducts.length ? 'SUCCESS' : 'EMPTY',
            message: filteredProducts 
        });
    }

    // Szűrők törlése
    clearFilters() {
        if (this.filters.price) {
            this.filters.price.min = null;
            this.filters.price.max = null;
            this.filters.price.inputs.minInput.value = '';
            this.filters.price.inputs.maxInput.value = '';
        }
    
        if (this.filters.stock) {
            this.filters.stock.value = false;
            this.filters.stock.input.checked = false;
        }
    
        this.applyFilters();
    }

    // A megjelenített termékek frissítése a szűrés eredménye alapján
    updateProductDisplay(response) {
        const products = response.message;
        
        if (this.onFilterUpdate) {
            this.onFilterUpdate(products);
        }
    }

    // Filter ablak megnyitása animációval
    open() {
        if (this.isOpen) return;
    
        this.isOpen = true;
    
        gsap.killTweensOf(this.domElement);
        gsap.set(this.domElement, { visibility: "visible" });
    
        // Ablak megjelenítése: nagy képernyőn marginLeft és x animáció, kisebbnél x animáció
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
    
        // Filter csoportok animált megjelenítése
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

    // Filter ablak bezárása animációval
    close() {
        gsap.killTweensOf(this.domElement);
        
        // Filter csoportok elhalványítása
        gsap.to(this.selector + " > .filter-group", {
            opacity: 0,
            duration: 0.5,
            ease: this.ease
        });
        
        // Ablak elrejtése animációval, majd láthatatlanná tétele
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
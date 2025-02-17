import APIFetch from "./apifetch.js";
const randomId = () => "el-" + (Math.random() + 1).toString(36).substring(7);

class FilterWindow {
    isOpen = false;
    ease = "power3.inOut";
    breakpoint = 950;
    filters = {};
    products = [];
    url = '';

    constructor() {
        // Fő filter ablak
        this.domElement = document.querySelector(".filters");
        if (!this.domElement) throw new Error("Nincs filter osztályú elem.");

        // Id és selector
        this.domElement.id = randomId();
        this.selector = `#${this.domElement.id}`;

        // Kinyitó gomb
        this.openButton = document.querySelector(".filter-open");
        if (!this.openButton) throw new Error("Nincs kinyitó gomb.");

        // Bezáró gomb
        this.closeButton = this.domElement.querySelector(".filter-close");
        if (!this.closeButton) throw new Error("Nincs becsukó gomb.");

        // GSAP ellenőrzés
        if (!gsap) throw new Error("A GSAP nem található");

        // Eseménykezelés
        this.openButton.addEventListener("click", this.open.bind(this))
        this.closeButton.addEventListener("click", this.close.bind(this));

        this.applyButton = this.domElement.querySelector(".filter-apply");
        this.clearButton = this.domElement.querySelector(".filter-clear");

        this.initializeFilters();

        if (this.applyButton) {
            this.applyButton.addEventListener('click', () => this.applyFilters());
        }
        if (this.clearButton) {
            this.clearButton.addEventListener('click', () => this.clearFilters());
        }

        const pathSegments = window.location.pathname.split('/');
        this.subcategoryId = document.querySelector('main').dataset.subcategoryId;

        this.url = window.location.pathname;

        this.fetchProducts()
    }

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

    async fetchProducts() {
        try {
            const response = await APIFetch('/api/subcategory/products', 'POST', {
                url: this.url
            });

            if (!response.ok) throw new Error('Network response was not ok');

            const data = await response.json();
            if (data.type !== 'ERROR') {
                this.products = data.message;
            } else {
                console.error('Failed to fetch products:', data.message);
            }
        } catch (error) {
            console.error('Error fetching products:', error);
        }
    }

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

    updateProductDisplay(response) {
        const cardsContainer = document.querySelector('.cards');
        if (!cardsContainer) return;

        const products = response.message;
        
        if (response.type === 'EMPTY') {
            const noProductsMsg = cardsContainer.querySelector('.no-products') || 
                cardsContainer.appendChild(document.createElement('div'));
            noProductsMsg.className = 'no-products';
            noProductsMsg.textContent = 'Nincsenek termékek a megadott szűrési feltételekkel.';
            noProductsMsg.style.display = 'block';

            cardsContainer.querySelectorAll('.card').forEach(card => {
                card.style.display = 'none';
            });
        } else {
            const noProductsMsg = cardsContainer.querySelector('.no-products');
            if (noProductsMsg) {
                noProductsMsg.style.display = 'none';
            }

            cardsContainer.querySelectorAll('.card').forEach(card => {
                const productId = parseInt(card.dataset.productId);
                const isVisible = products.some(p => p.id === productId);
                card.style.display = isVisible ? 'flex' : 'none';
            });
        }
        
        const productCount = document.querySelector('.product-count');
        if (productCount) {
            productCount.textContent = `${products.length} termék`;
        }
    }

    open() {
        if (this.isOpen) return;

        this.isOpen = true;

        gsap.killTweensOf(this.domElement);
        
        gsap.set(this.domElement, { visibility: "visible" });

        // Ablak bejön balról jobbra
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

        // A filterek bejönnek balról jobbra
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

    close() {
        gsap.killTweensOf(this.domElement);
        
        // Filterek elhalványodnak
        gsap.to(this.selector + " > .filter-group", {
            opacity: 0,
            duration: 0.5,
            ease: this.ease
        });
        
        // Ablak kimegy jobbról balra
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
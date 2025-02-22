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

        this.domElement.querySelectorAll(".input-group").forEach(e => this.handleInputGroupFocus(e));
    }

    // Szűrő elemek inicializálása
    initializeFilters() {
        // Ár filter inicializálása
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
        
        // Raktárkészlet filter inicializálása
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

        // Címkék filter inicializálása
        const tagFilter = this.domElement.querySelector('[data-target="tags"]');
        if (tagFilter) {
            // Címkék összegyűjtése a termékekből
            const tagList = [];
            this.products.forEach(product => {
                if (product.tags) {
                    product.tags.split(',').forEach(tag => {
                        const [id, name] = tag.trim().split(':');
                        // Csak egyedi címkéket adunk hozzá
                        if (!tagList.find(t => t.id === id)) {
                            tagList.push({ id, name });
                        }
                    });
                }
            });

            // Címkék rendezése név szerint
            tagList.sort((a, b) => a.name.localeCompare(b.name));
            
            // Filter állapot inicializálása
            this.filters.tags = {
                values: new Set(),
                container: tagFilter,
            };

            // Az összes címke megjelenítése
            this.createTagCheckboxes(tagList, tagFilter);
        }
    }

    createTagCheckboxes(tags, container) {
        let tagsContainer = container.querySelector('.tags');
        if (!tagsContainer) {
            tagsContainer = document.createElement('div');
            tagsContainer.className = 'tags';
            container.appendChild(tagsContainer);
        }
        
        tags.forEach((e) => {
            const [id, name] = Object.values(e);
            const wrapper = document.createElement('div');
            wrapper.className = 'checkbox-input';

            const checkbox = document.createElement('input');
            checkbox.type = 'checkbox';
            checkbox.id = `tag-${id}`;
            checkbox.dataset.tagId = id;
            checkbox.addEventListener('change', () => {
                if (checkbox.checked) {
                    this.filters.tags.values.add(id);
                } else {
                    this.filters.tags.values.delete(id);
                }
            });

            const label = document.createElement('label');
            label.htmlFor = `tag-${id}`;
            label.textContent = name;

            wrapper.appendChild(checkbox);
            wrapper.appendChild(label);
            tagsContainer.appendChild(wrapper);
        });
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

            // Tag filter
            if (this.filters.tags?.values.size > 0) {
                const productTagIds = product.tags ? 
                    product.tags.split(',')
                        .map(t => t.trim().split(':')[0]) : 
                    [];
                const hasMatchingTag = Array.from(this.filters.tags.values)
                    .some(tagId => productTagIds.includes(tagId));
                if (!hasMatchingTag) return false;
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

        if (this.filters.tags) {
            this.filters.tags.values.clear();
            this.filters.tags.container.querySelectorAll('input[type="checkbox"]')
                .forEach(checkbox => checkbox.checked = false);
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
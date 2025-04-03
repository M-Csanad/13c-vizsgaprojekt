import FilterWindow from "../../assets/js/filterwindow.js";
import APIFetch from "../../assets/js/apifetch.js";
import SortDropdown from "../../assets/js/sortdropdown.js";

export default class SubcategorySite {
    // Osztály tulajdonságok
    productsPerPage = 9;
    currentPage = 1;
    allProducts = [];
    filteredProducts = [];
    pageTransitionInProgress = false;

    constructor(shouldInitialize = true) {
        // Mező, amely meghatározza, hogy a kereső oldal inicializálta-e vagy az alkategória oldal 
        this.externalCall = !shouldInitialize;
        
        this.initDOM();
        this.bindEvents();
        this.setupStarsObserver(); // Add this new method call

        // Csak akkor futtatjuk a lekéréseket, ha nem kívülről lett példányosítva az oldal
        if (!this.externalCall) {
            this.initialize();
        }
    }

    // Csillagok figyelő függvénye
    setupStarsObserver() {
        // Csillagokat generáló függvény
        const generateStars = (element) => {
            let rating = element.dataset.rating ?? 0;
            const fullStars = Math.floor(rating);
            const halfStar = rating % 1 >= 0.5;
            const totalStars = 5;
            
            for (let i = 0; i < totalStars; i++) {
                const star = document.createElement("span");
                if (i < fullStars) {
                    star.classList.add("filled");
                } else if (i === fullStars && halfStar) {
                    star.classList.add("half");
                }
                element.appendChild(star);
            }
        };

        // Létrehozzuk a mutációfigyelőt
        const observer = new MutationObserver(() => {
            document.querySelectorAll(".review-stars").forEach((el) => {
                if (!el.hasChildNodes()) {
                    generateStars(el);
                }
            });
        });

        // Hozzácsatoljuk a figyelőt a kártyák konténeréhez
        observer.observe(this.cardsContainer, { 
            childList: true,
            subtree: true 
        });
    }

    /**
     * Frontend Metódusok
     */

    // DOM elemek inicializálása
    initDOM() {
        this.cardsContainer = document.querySelector('.cards');
        this.pagination = document.querySelector('.pagination');
        this.productCount = document.querySelector('.product-count');
        this.filterButton = document.querySelector('.filter-open');
        this.sortButton = document.querySelector('.sort-open');

        if (!this.externalCall) {
            this.totalProductCount = document.querySelector('#total-product-count');
            if (!this.totalProductCount) throw new Error("Nem található az összes termék számláló");
        }
        
        if (!this.cardsContainer) throw new Error("Nem található a kártyák konténere");
        if (!this.pagination) throw new Error("Nem található a lapozó");
        if (!this.productCount) throw new Error("Nem található a termékszámláló");
    }

    // Eseménykezelők beállítása
    bindEvents() {
        const previousButton = this.pagination.querySelector('.prev-page');
        const nextButton = this.pagination.querySelector('.next-page');
        
        previousButton.addEventListener('click', () => this.changePage(this.currentPage - 1));
        nextButton.addEventListener('click', () => this.changePage(this.currentPage + 1));
        document.getElementById("top-button").addEventListener("click", () => lenis.scrollTo("top"));
    }

    // UI elemek frissítése
    updateUI() {
        const totalPages = Math.ceil(this.filteredProducts.length / this.productsPerPage);
        this.updatePaginationUI(totalPages);
        this.updateCards();
        this.updateProductCount();
    }

    // Lapozó számok frissítése
    updatePaginationUI(totalPages) {
        const numbers = this.pagination.querySelector('.page-numbers');
        numbers.innerHTML = '';
        
        for (let i = 1; i <= totalPages; i++) {
            const pageNumber = document.createElement('div');
            pageNumber.className = `page-number${i === this.currentPage ? ' active' : ''}`;
            pageNumber.textContent = i;
            pageNumber.addEventListener('click', () => this.changePage(i));
            numbers.appendChild(pageNumber);
        }

        const previousButton = this.pagination.querySelector('.prev-page');
        const nextButton = this.pagination.querySelector('.next-page');
        
        previousButton.disabled = this.currentPage === 1;
        nextButton.disabled = this.currentPage === totalPages;
    }

    // Termékkártyák megjelenítése
    async updateCards() {
        if (this.pageTransitionInProgress) return;
        this.pageTransitionInProgress = true;

        await this.fadeOutExistingCards();
        this.renderNewCards();
        await this.fadeInNewCards();

        this.pageTransitionInProgress = false;
    }

    // Meglévő kártyák elhalványítása
    async fadeOutExistingCards() {
        const existingCards = this.cardsContainer.querySelectorAll('.card');
        if (existingCards.length > 0) {
            await gsap.to(existingCards, {
                opacity: 0,
                y: 20,
                stagger: 0.1,
                duration: 0.3,
                ease: "power2.in"
            });
        }
    }

    // Új kártyák renderelése
    renderNewCards() {
        this.cardsContainer.innerHTML = '';
        
        const start = (this.currentPage - 1) * this.productsPerPage;
        const end = start + this.productsPerPage;
        const pageProducts = this.filteredProducts.slice(start, end);

        if (pageProducts.length === 0 && this.filteredProducts.length === 0) {
            console.log("rendered: no results");
            this.noResults();
            return;
        }

        pageProducts.forEach(product => {
            const card = this.createProductCard(product);
            card.style.opacity = "0";
            card.style.transform = "translateY(20px)";
            this.cardsContainer.appendChild(card);
        });

        console.log("rendered");
    }

    // Új kártyák animált megjelenítése
    async fadeInNewCards() {
        const newCards = this.cardsContainer.querySelectorAll('.card');
        await gsap.to(newCards, {
            opacity: 1,
            y: 0,
            stagger: 0.1,
            duration: 0.4,
            ease: "power2.out"
        });
    }

    // Termékkártya HTML létrehozása
    createProductCard(product) {
        const card = document.createElement('div');
        card.className = `card${product.stock > 0 ? '' : ' out-of-stock'}`;
        card.dataset.productId = product.id;

        card.innerHTML = `
            <div class="card-image">
                ${product.stock <= 0 ? `
                    <div class="out-of-stock-label">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Nem elérhető</span>
                    </div>
                ` : ''}
                <a href="/${product.link_slug}">
                    <picture>
                        <source type="image/webp" srcset="${product.thumbnail_image}-768px.webp">
                        <img src="${product.thumbnail_image}-768px.webp" alt="${product.name}">
                    </picture>
                    <picture class="secondary">
                        <source type="image/webp" srcset="${product.secondary_image}-768px.webp">
                        <img src="${product.secondary_image}-768px.webp" alt="${product.name}">
                    </picture>
                </a>
                ${product.stock > 0 ? `
                    <div class="button-wrapper">
                        <button class="quick-add" data-product-url="${product.link_slug}">
                            <div>Kosárba</div>
                            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag" viewBox="0 0 16 16">
                                <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                            </svg>
                        </button>
                    </div>
                ` : ''}
            </div>
            <div class="card-body">
                <div class="name" title="${product.name}">${product.name}</div>
                <div class="price">
                    <span class="price-value">${product.unit_price}</span>
                    <span class="price-currency">Ft</span>
                </div>
                ${this.createReviewStars(product)}
            </div>
        `;

        return card;
    }

    // Értékelő csillagok HTML létrehozása
    createReviewStars(product) {
        const rating = product.avg_rating || 0;
        if (product.review_count > 0) {
            return `
                <div class="card-bottom">
                    <div class="review-stars stars" data-rating="${rating}"></div>
                    <div class="review-count">${product.review_count} értékelés</div>
                </div>
            `;
        }
        return `
            <div class="card-bottom">
                <div class="review-count">Még nincs értékelve</div>
            </div>
        `;
    }

    // Termékszám frissítése
    updateProductCount() {
        if (this.productCount) {
            this.productCount.textContent = `${this.filteredProducts.length} termék`;
        }
    }

    // Oldalváltás kezelése
    changePage(page) {
        const totalPages = Math.ceil(this.filteredProducts.length / this.productsPerPage);
        if (page >= 1 && page <= totalPages && this.currentPage != page && !this.pageTransitionInProgress) {
            this.currentPage = page;
            
            // Lapozó gombok ideiglenes letiltása animáció alatt
            const buttons = this.pagination.querySelectorAll('button, .page-number');
            buttons.forEach(btn => btn.style.pointerEvents = 'none');
            
            this.updateUI();
            
            lenis.scrollTo('top');

            // Lapozó gombok visszaengedélyezése az animáció után
            setTimeout(() => {
                buttons.forEach(btn => {
                    if (!btn.classList.contains('disabled')) {
                        btn.style.pointerEvents = 'auto';
                    }
                });
            }, 800); // Animációs időnél kicsit hosszabb
        }
    }

    /**
     * Backend Metódusok
     */

    // Kezdeti adatok betöltése
    async initialize() {
        await this.fetchProducts();
        if (!Array.isArray(this.allProducts) || this.allProducts.length === 0) return;
            
        this.filter = new FilterWindow(this.allProducts, this.handleFilterUpdate.bind(this));
        this.sortDropdown = new SortDropdown(this.handleSort.bind(this));

        this.filteredProducts = this.sortDropdown.sortProducts(
            this.filteredProducts,
            'name',
            'asc'
        );
        
        // Update total product count
        this.totalProductCount.textContent = `${this.allProducts.length} termék összesen`;
        
        this.updateUI();
    }

    // Termékek lekérése
    async fetchProducts() {
        const response = await APIFetch('/api/subcategory/products', 'POST', {
            url: window.location.pathname
        });

        if (!response.ok) {
            console.error('Hiba a termékek lekérésekor:', error);
            return;
        }

        if (response.ok) {
            const data = await response.json();
            this.allProducts = data.message;
            this.filteredProducts = [...this.allProducts]; // Lemásoljuk a tömböt, hogy ne változzon az eredeti

            if (this.filteredProducts.length === 0 || !Array.isArray(this.allProducts)) {
                this.noResults();
            }
        }
    }

    // Szűrés eredményének kezelése
    handleFilterUpdate(filteredProducts) {
        this.filteredProducts = filteredProducts;
        
        // Apply current sorting if there is an active sort
        if (this.sortDropdown.activeSortType) {
            const [property, direction] = this.sortDropdown.activeSortType.split('-');
            this.filteredProducts = this.sortDropdown.sortProducts(
                this.filteredProducts,
                property,
                direction
            );
        }
        
        this.currentPage = 1;
        this.updateUI();

        lenis.scrollTo('top');
    }

    handleSort(property, direction) {
        if (this.pageTransitionInProgress) return;
        
        this.filteredProducts = this.sortDropdown.sortProducts(
            this.filteredProducts,
            property,
            direction
        );
        
        this.currentPage = 1;
        this.updateUI();
    }

    // Nincs találat kezelése
    noResults() {
        // Töröljük a meglévő tartalmakat
        this.cardsContainer.innerHTML = '';
        
        // Nincs találat üzenet hozzáadása SVG grafikákkal
        const noResultsMessage = document.createElement('div');
        noResultsMessage.className = 'no-results-message';
        noResultsMessage.innerHTML = `
            <div class="no-results-graphics">
                <svg class="no-results-svg" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 500 300">
                    <!-- Felhők -->
                    <path class="cloud cloud-1" d="M100,120 C90,110 70,110 65,120 C50,95 10,105 10,130 C10,155 50,160 65,150 C75,165 100,160 110,150 C125,160 145,150 140,130 C135,115 115,110 100,120 Z" />
                    
                    <path class="cloud cloud-2" d="M430,100 C420,90 400,90 395,100 C380,75 340,85 340,110 C340,135 380,140 395,130 C405,145 430,140 440,130 C455,140 475,130 470,110 C465,95 445,90 430,100 Z" />
                    
                    <path class="cloud cloud-3" d="M250,210 C245,205 235,205 230,210 C225,195 205,200 205,215 C205,230 225,235 230,225 C235,235 250,235 255,225 C265,230 275,225 270,215 C265,205 255,205 250,210 Z" />
                    
                    <!-- Nagyító -->
                    <circle class="magnifier-glass" cx="250" cy="140" r="70" stroke-width="12" fill="none" />
                    <path class="magnifier-handle" d="M200,200 L160,240" stroke-width="15" stroke-linecap="round" />
                    
                    <!-- Centered and bigger question mark -->
                    <path class="question-mark" transform="translate(204, 98) scale(6)" fill-rule="evenodd" d="M4.475 5.458c-.284 0-.514-.237-.47-.517C4.28 3.24 5.576 2 7.825 2c2.25 0 3.767 1.36 3.767 3.215 0 1.344-.665 2.288-1.79 2.973-1.1.659-1.414 1.118-1.414 2.01v.03a.5.5 0 0 1-.5.5h-.77a.5.5 0 0 1-.5-.495l-.003-.2c-.043-1.221.477-2.001 1.645-2.712 1.03-.632 1.397-1.135 1.397-2.028 0-.979-.758-1.698-1.926-1.698-1.009 0-1.71.529-1.938 1.402-.066.254-.278.461-.54.461h-.777ZM7.496 14c.622 0 1.095-.474 1.095-1.09 0-.618-.473-1.092-1.095-1.092-.606 0-1.087.474-1.087 1.091S6.89 14 7.496 14"/>
                </svg>
            </div>
            <div class="no-results-text">Nincsenek találatok!</div>
        `;
        this.cardsContainer.appendChild(noResultsMessage);
        
        // UI elemek elrejtése
        if (this.filterButton) this.filterButton.style.display = 'none';
        if (this.sortButton) this.sortButton.style.display = 'none';
        if (this.pagination) this.pagination.style.display = 'none';
    }
}

// Csak az alkategória oldalon példányosítjuk
if (document.querySelector('script[src*="subcategory.js"]')) {
    const site = new SubcategorySite(true);
}
import FilterWindow from "../../assets/js/filterwindow.js";
import APIFetch from "../../assets/js/apifetch.js";

class SubcategorySite {
    // Osztály tulajdonságok
    productsPerPage = 9;
    currentPage = 1;
    allProducts = [];
    filteredProducts = [];
    pageTransitionInProgress = false;

    constructor() {
        this.initDOM();
        this.bindEvents();
        this.initialize();
    }

    /**
     * Frontend Metódusok
     */

    // DOM elemek inicializálása
    initDOM() {
        this.cardsContainer = document.querySelector('.cards');
        this.pagination = document.querySelector('.pagination');
        this.productCount = document.querySelector('.product-count');
        
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

        pageProducts.forEach(product => {
            const card = this.createProductCard(product);
            card.style.opacity = "0";
            card.style.transform = "translateY(20px)";
            this.cardsContainer.appendChild(card);
        });
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
        card.className = 'card';
        card.dataset.productId = product.id;

        card.innerHTML = `
            <div class="card-image">
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
                <div class="button-wrapper">
                    <button class="quick-add" data-product-url="${product.link_slug}">
                        <div>Kosárba</div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-bag" viewBox="0 0 16 16">
                            <path d="M8 1a2.5 2.5 0 0 1 2.5 2.5V4h-5v-.5A2.5 2.5 0 0 1 8 1m3.5 3v-.5a3.5 3.5 0 1 0-7 0V4H1v10a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V4zM2 5h12v9a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1z"/>
                        </svg>
                    </button>
                </div>
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
        this.filter = new FilterWindow(this.allProducts, this.handleFilterUpdate.bind(this));
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
        }
    }

    // Szűrés eredményének kezelése
    handleFilterUpdate(filteredProducts) {
        this.filteredProducts = filteredProducts;
        this.currentPage = 1;
        this.updateUI();

        lenis.scrollTo('top');
    }
}

// Oldal inicializálása
const site = new SubcategorySite();

// Értékelő csillagok generálása
function generateStars(element) {
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
}

// Csillagok figyelése és frissítése
const observer = new MutationObserver(() => {
    document.querySelectorAll(".review-stars").forEach((el) => {
        if (!el.hasChildNodes()) {
            generateStars(el);
        }
    });
});

observer.observe(document.querySelector('.cards'), { 
    childList: true,
    subtree: true 
});
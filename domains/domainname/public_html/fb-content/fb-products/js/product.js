import { setupNumberField } from './numberfield.js';
import APIFetch from "../../assets/js/apifetch.js";

class ProductPage {
    constructor() {
        // Állapot változók inicializálása
        this.isZoom = false;
        this.isNavAnimating = false;
        this.isChangingPage = false;
        this.frameId = null;
        this.scrollFrameId = null;
        this.reviewsPerPage = 3;
        this.currentPage = 1;
        this.totalPages = 1;

        this.initDOM();
        this.bindEvents();
        this.initialize();
    }

    initDOM() {
        // DOM elemek referenciáinak létrehozása
        this.imageViewer = {
            container: document.querySelector('.images'),
            images: document.querySelectorAll('.images .image'),
            navigator: {
                images: document.querySelectorAll('.navigator-image'),
                arrows: document.querySelectorAll('.arrow'),
                scrollbar: document.querySelector('.navigator > .navigator-arrows > .navigator-progress > .progressbar'),
                carousel: document.querySelector('.navigator > .navigator-images')
            }
        };

        this.recommendations = {
            container: document.querySelector('.products'),
            scrollbar: document.querySelector('.product-navigator > .navigator-progress > .progressbar'),
            arrows: document.querySelectorAll('.navigator-button')
        };

        this.tags = document.querySelectorAll('.tag');
        this.avgReviewElement = document.querySelector('.avg-review');
        this.reviewContainer = document.querySelector('.review-container');
        this.topButton = document.getElementById('top-button');
        this.shareButton = document.querySelector('.share');

        // Numberfield setup
        this.quantityField = document.querySelector('.product-quantity');
        this.addToCartButton = document.querySelector('.add-to-cart');

        // Lebegő kosár gomb
        this.bigAddToCartBtn = document.querySelector(".add-to-cart");
        this.floatingCart = document.getElementById("floatingCart");
    }

    bindEvents() {
        // Képnéző események
        this.imageViewer.navigator.images.forEach((img, index) => img.addEventListener('click', () => this.switchImage(index)));

        this.imageViewer.images.forEach(img => {
            const wrapper = img.parentElement;
            wrapper.addEventListener('mouseenter', () => this.handleImageZoomEnter(img));
            wrapper.addEventListener('mousemove', (e) => this.handleImageZoomMove(e));
            wrapper.addEventListener('mouseleave', () => this.handleImageZoomLeave(img));
        });

        // Navigációs nyilak
        this.imageViewer.navigator.arrows.forEach(arrow => {
            arrow.addEventListener('click', (e) => this.navigatorScroll(
                e.target.classList.contains('arrow-left') ? 'left' : 'right',
                this.imageViewer.navigator.carousel
            ));
        });

        // Ajánlott termékek navigáció
        this.recommendations.arrows.forEach(button => {
            button.addEventListener('click', (e) => this.navigatorScroll(
                e.target.classList.contains('navigator-left') ? 'left' : 'right',
                this.recommendations.container
            ));
        });

        // Görgetési események
        this.imageViewer.navigator.carousel.addEventListener('scroll', () => this.handleScroll(this.imageViewer.navigator.scrollbar, this.imageViewer.navigator.carousel));
        this.recommendations.container.addEventListener('scroll', () => this.handleScroll(this.recommendations.scrollbar, this.recommendations.container));

        document.querySelectorAll('.quick-add').forEach(button => {
            button.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                const url = button.dataset.productUrl;
                if (url) {
                    this.addToCart(url);
                }
            });
        });

        // Ablak események
        window.addEventListener('load', () => this.handleLoad());
        window.addEventListener('resize', () => this.handleResize());

        if (this.bigAddToCartBtn) {
            window.addEventListener('scroll', () => this.checkBigButtonVisibility());
        }

        // Felgördítő gomb
        this.topButton?.addEventListener('click', () => lenis.scrollTo('top'));

        // Címkék feliratozásának beállítása
        this.tags.forEach(tag => this.setupTooltip(tag));

        // Megosztás gomb kezelése
        this.shareButton?.addEventListener('click', () => this.handleShare());

        // Add pagination button handlers
        const paginationContainer = document.querySelector('.review-pagination');
        if (paginationContainer) {
            const prevButton = paginationContainer.querySelector('.prev-page-btn');
            const nextButton = paginationContainer.querySelector('.next-page-btn');

            prevButton?.addEventListener('click', async () => {
                if (this.isChangingPage) return;
                this.isChangingPage = true;
                if (this.currentPage > 1) {
                    await this.changePage(this.currentPage - 1);
                }
                this.isChangingPage = false;
            });

            nextButton?.addEventListener('click', async () => {
                if (this.isChangingPage) return;
                this.isChangingPage = true;
                if (this.currentPage < this.totalPages) {
                    await this.changePage(this.currentPage + 1);
                }
                this.isChangingPage = false;
            });
        }
    }

    async initialize() {
        // Kezdeti állapot beállítása
        await this.generateReviewSection();
        this.reviewStars = document.querySelectorAll('.review-stars');
        this.reviewStars.forEach(el => this.generateStars(el));
        this.updateDynamicBackground();

        if (this.bigAddToCartBtn) {
            this.checkBigButtonVisibility();
        }

        // Mennyiség beviteli mező inicializálása
        if (document.querySelector('.number-field')) {
            setupNumberField();
        }
    }

    // Segéd metódusok
    generateStars(element) {
        // Értékelő csillagok generálása
        const rating = element.dataset.rating ?? 0;
        const fullStars = Math.floor(rating);
        const halfStar = rating % 1 >= 0.5;
        const totalStars = 5;

        element.innerHTML = '';
        for (let i = 0; i < totalStars; i++) {
            const star = document.createElement('span');
            if (i < fullStars) {
                star.classList.add('filled');
            } else if (i === fullStars && halfStar) {
                star.classList.add('half');
            }
            element.appendChild(star);
        }
    }

    async getReviews(page = 1) {
        if (!page) return;
        const response = await APIFetch(`/api/product/reviews?url=${encodeURIComponent(window.location.pathname)}&page=${page}`);
        if (response.ok)
        {
            const reviewsData = (await response.json()).message;
            if (reviewsData.pagination.total > 0) {
                this.reviews = reviewsData.reviews;
                this.currentPage = parseInt(reviewsData.pagination.currentPage);
                this.totalPages = parseInt(reviewsData.pagination.totalPages);
            }
            else {
                this.reviews = [];
                this.currentPage = 1;
                this.totalPages = 1;
            }
        }
    }

    generateReviewCards() {
        this.reviewContainer.innerHTML = "";

        if (this.totalPages <= 1) {
            this.reviewContainer.classList.add('single-page');
        } else {
            this.reviewContainer.classList.remove('single-page');
        }
        
        this.reviews?.forEach(r => {
            this.reviewContainer.innerHTML += 
            `
                <div class="review">
                    <div class="review-head">
                        <div class="review-info">
                        <div class="user">
                            <div class="profile-pic">
                            <img src="${r.pfp_uri}" alt="" />
                            ${
                            r.verified_purchase && 
                            '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-check-circle-fill" viewBox="0 0 16 16" ><path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0m-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" /></svg>'
                            }
                            </div>
                            <div class="profile-info">
                            <div class="name">${r.last_name + " " + r.first_name}</div>
                            <div class="verified">${r.verified_purchase ? "Hitelesített vásárló" : "Felhasználó"}</div>
                            </div>
                        </div>
                        <div class="stars-title">
                            <div class="review-stars stars" data-rating="${r.rating}"></div>
                            <div class="review-title">${r.title}</div>
                        </div>
                        </div>
                        <div class="date">${r.created_at}</div>
                    </div>
                    <div class="review-body">
                        <div class="review-text">
                        ${r.description}
                        </div>
                    </div>
                </div>
            `;
        });
        
        // Lapozás állapot és értékelési csillagok frissítése
        this.updatePaginationState();
        document.querySelectorAll('.review .review-stars').forEach(el => this.generateStars(el));
    }

    // Lapozás állapotának frissítése
    updatePaginationState() {
        const paginationContainer = document.querySelector('.review-pagination');
        if (!paginationContainer) return;

        if (this.totalPages <= 1) {
            paginationContainer.style.display = 'none';
            return;
        }
        
        paginationContainer.style.display = 'flex';
        
        // Gombok állapotának frissítése
        const prevButton = paginationContainer.querySelector('.prev-page-btn');
        const nextButton = paginationContainer.querySelector('.next-page-btn');
        
        prevButton.disabled = this.currentPage <= 1;
        nextButton.disabled = this.currentPage >= this.totalPages;
        
        // Oldalszámok frissítése
        this.updatePageNumbers();
    }

    // Oldalszámok állapotának frissítése
    updatePageNumbers() {
        const pageNumbers = document.querySelector('.review-pagination .page-numbers');
        if (!pageNumbers) return;

        // Látható oldalszámok tartományának kiszámítása
        let startPage = Math.max(1, this.currentPage - 2);
        let endPage = Math.min(this.totalPages, startPage + 4);
        
        if (endPage - startPage < 4) {
            startPage = Math.max(1, endPage - 4);
        }

        // Meglévő oldalszám gombok állapotának frissítése vagy létrehozása
        const existingButtons = pageNumbers.children;
        const neededButtons = endPage - startPage + 1;

        // Felesleges gombok eltávolítása
        while (existingButtons.length > neededButtons) {
            pageNumbers.removeChild(pageNumbers.lastChild);
        }

        // Hiányzó gombok létrehozása
        while (existingButtons.length < neededButtons) {
            const button = document.createElement('div');
            button.className = 'page-number';
            button.addEventListener('click', async (e) => {
                if (this.isChangingPage) return;
                this.isChangingPage = true;
                const page = parseInt(e.target.textContent);
                if (page !== this.currentPage) {
                    await this.changePage(page);
                }
                this.isChangingPage = false;
            });
            pageNumbers.appendChild(button);
        }

        // Gombok számozásának és aktív állapotának frissítése
        Array.from(existingButtons).forEach((button, i) => {
            const pageNum = startPage + i;
            button.textContent = pageNum;
            button.classList.toggle('active', pageNum === this.currentPage);
        });
    }

    async changePage(page) {
        if (page < 1 || page > this.totalPages || page === this.currentPage) {
            return;
        }
        
        // Fade out
        gsap.to(this.reviewContainer, {
            opacity: 0,
            duration: 0.3,
            ease: "power2.out"
        });
        
        await new Promise(resolve => setTimeout(resolve, 350));
        
        // Get new reviews
        await this.getReviews(page);
        
        // Generate review cards and update pagination
        this.generateReviewCards();
        
        // Scroll to top of reviews
        this.reviewContainer.scrollTop = 0;
        
        // Fade in
        gsap.fromTo(this.reviewContainer, 
            { opacity: 0 },
            { 
                opacity: 1, 
                duration: 0.4, 
                ease: "power2.in" 
            }
        );
    }

    async generateReviewSection() {
        await this.getReviews();

        if (!this.avgReviewElement) return;

        const rating = parseFloat(this.avgReviewElement.getAttribute('data-rating'));
        const reviewCount = parseInt(this.avgReviewElement.getAttribute('data-reviews'));

        this.avgReviewElement.innerHTML = `
            <div class="rating-number">${rating.toFixed(1)}</div>
            <div class="stars-and-reviews">
                <div class="stars" data-rating="${rating}"></div>
                <div class="review-count">${reviewCount} értékelés</div>
            </div>
        `;
        this.generateStars(this.avgReviewElement.querySelector('.stars'));

        this.generateReviewCards();
    }

    checkBigButtonVisibility() {
        const rect = this.bigAddToCartBtn.getBoundingClientRect();
        const isVisible = document.documentElement.scrollTop <= 450;

        if (isVisible) {
            this.floatingCart.classList.remove("show");
        } else {
            this.floatingCart.classList.add("show");
        }
    }

    updateDynamicBackground() {
        // Dinamikus háttér frissítése az aktív kép alapján
        const activeImage = document.querySelector('.images img.active');
        if (activeImage) {
            const color = this.getDominantColor(activeImage);
            activeImage.parentElement.style.boxShadow = `0px 0px 150px ${color}88`;
        }
    }

    getDominantColor(image) {
        // Domináns szín kinyerése a képből
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 1;
        canvas.height = 1;
        ctx.drawImage(image, 0, 0, 1, 1);
        const [r, g, b] = ctx.getImageData(0, 0, 1, 1).data;
        return `#${((1 << 24) | (r << 16) | (g << 8) | b).toString(16).slice(1).toUpperCase()}`;
    }

    // Eseménykezelők
    switchImage(index) {
        if (this.isNavAnimating) return;
        this.isNavAnimating = true;

        const currentImage = document.querySelector('.image.active');
        const targetImage = this.imageViewer.images[index];

        if (currentImage === targetImage) {
            this.isNavAnimating = false;
            return;
        }

        targetImage.classList.add('active');
        currentImage.classList.remove('active');

        currentImage.style.zIndex = '10';
        targetImage.style.zIndex = '100';

        targetImage.animate(
            {
                scale: [1.2, 1],
                opacity: [0, 1],
            },
            {
                fill: 'forwards',
                duration: 300,
                easing: 'ease',
            }
        );

        this.updateDynamicBackground();

        setTimeout(() => {
            currentImage.style.zIndex = '-1';
            this.isNavAnimating = false;
        }, 300);
    }

    handleImageZoomEnter(image) {
        if (image.classList.contains('active')) {
            this.isZoom = true;
            image.style.transition = 'transform 0.5s ease-out';
            image.style.transform = 'scale(1.2)';
            image.classList.add('zoomed');
        }
    }

    handleImageZoomMove(e) {
        if (!this.isZoom) return;

        if (!this.frameId) {
            this.frameId = requestAnimationFrame(() => {
                if (this.isZoom) this.requestZoomUpdate(e);
                this.frameId = null;
            });
        }
    }

    handleImageZoomLeave(image) {
        if (image.classList.contains('active')) {
            this.isZoom = false;
            image.style.transition = 'transform 0.5s ease-out';
            image.style.transform = 'scale(1)';
            image.classList.remove('zoomed');
        }
    }

    requestZoomUpdate(e) {
        const wrapper = e.target.parentElement;
        const rect = wrapper.getBoundingClientRect();
        const imageRect = e.target.getBoundingClientRect();

        const relX = (e.clientX - rect.left) / rect.width - 0.5;
        const relY = (e.clientY - rect.top) / rect.height - 0.5;

        const maxMoveX = (imageRect.width - rect.width) / 2;
        const maxMoveY = (imageRect.height - rect.height) / 2;

        const moveX = 2 * relX * maxMoveX;
        const moveY = 2 * relY * maxMoveY;

        if (e.target.classList.contains('image')) {
            e.target.style.transition = 'transform 0.1s ease-out';
            e.target.style.transform = `translate(${-moveX}px, ${-moveY}px) scale(1.2)`;
        }
    }

    navigatorScroll(direction, scrollElement) {
        const scrollAmount = 200;
        const maxScrollLeft = scrollElement.scrollWidth - scrollElement.offsetWidth;
        const newScrollPosition = direction === 'left'
            ? Math.max(scrollElement.scrollLeft - scrollAmount, 0)
            : Math.min(scrollElement.scrollLeft + scrollAmount, maxScrollLeft);

        scrollElement.scrollTo({
            left: newScrollPosition,
            behavior: 'smooth'
        });
    }

    handleScroll(scrollbar, scrollElement) {
        if (!this.scrollFrameId) {
            this.scrollFrameId = setTimeout(() => {
                this.updateScrollbar(scrollbar, scrollElement);
                this.scrollFrameId = null;
            }, 100);
        }
    }

    updateScrollbar(scrollbar, scrollElement) {
        const progress = (scrollElement.scrollLeft + scrollElement.offsetWidth) / scrollElement.scrollWidth;
        scrollbar.style.width = `${progress * 100}%`;

        const shouldHideAfter = scrollElement.scrollWidth === scrollElement.offsetWidth || window.innerWidth <= 414;
        scrollElement.style.setProperty('--after-end', shouldHideAfter ? 'transparent' : '#1d1d1d');
    }

    handleResize() {
        this.imageViewer.images.forEach(image => this.updateImageLeft(image));
        this.updateScrollbar(this.recommendations.scrollbar, this.recommendations.container);
        this.updateScrollbar(this.imageViewer.navigator.scrollbar, this.imageViewer.navigator.carousel);

        if (this.bigAddToCartBtn) {
            this.checkBigButtonVisibility();
        }
    }

    handleLoad() {
        this.imageViewer.images.forEach(image => this.updateImageLeft(image));
        this.updateScrollbar(this.recommendations.scrollbar, this.recommendations.container);
        this.updateScrollbar(this.imageViewer.navigator.scrollbar, this.imageViewer.navigator.carousel);
        setTimeout(() => this.updateDynamicBackground(), 10);
    }

    updateImageLeft(image) {
        const imageRect = image.getBoundingClientRect();
        const wrapperRect = image.parentElement.getBoundingClientRect();
        image.style.left = `${imageRect.width * -0.5 - wrapperRect.width * -0.5}px`;
    }

    setupTooltip(tag) {
        const tooltipText = tag.querySelector('img').alt;
        if (!tooltipText) return;

        const tooltip = document.createElement('div');
        tooltip.classList.add('tooltip');
        tooltip.textContent = tooltipText;
        tooltip.style.setProperty('--visible', '0');
        tag.appendChild(tooltip);

        let isTooltipActive = false;
        let tooltipAnimationId = null;

        const updateTooltipPosition = () => {
            const rect = tag.getBoundingClientRect();
            const tooltipRect = tooltip.getBoundingClientRect();

            tooltip.classList.remove('right', 'left');
            let top = rect.top - tooltipRect.height - 8;
            let left = rect.left + rect.width / 2 - tooltipRect.width / 2;

            if (top < 0) top = rect.bottom + 8;
            if (left + tooltipRect.width > window.innerWidth) {
                left = window.innerWidth - tooltipRect.width - 30;
                tooltip.classList.add('right');
            }
            if (left < 0) {
                left = rect.left;
                tooltip.classList.add('left');
            }

            tooltip.style.top = `${top}px`;
            tooltip.style.left = `${left}px`;
            tooltipAnimationId = null;
        };

        tag.addEventListener('mouseenter', () => {
            isTooltipActive = true;
            tooltip.style.setProperty('--visible', '1');
            updateTooltipPosition();
        });

        tag.addEventListener('mouseleave', () => {
            isTooltipActive = false;
            setTimeout(() => {
                if (!isTooltipActive) {
                    tooltip.style.setProperty('--visible', '0');
                    tooltip.style.top = 'auto';
                    tooltip.style.left = 'auto';
                }
            }, 300);
            if (tooltipAnimationId) {
                cancelAnimationFrame(tooltipAnimationId);
                tooltipAnimationId = null;
            }
        });

        window.addEventListener('scroll', () => {
            if (getComputedStyle(tooltip).getPropertyValue('--visible') === '1') {
                if (!tooltipAnimationId) {
                    tooltipAnimationId = requestAnimationFrame(updateTooltipPosition);
                }
            }
        });
    }

    async handleShare() {
        const url = window.location.href;
        const title = document.querySelector('.product-name').textContent.trim();

        try {
            if (navigator.share) {
                // Native share API használata mobilon
                await navigator.share({
                    title: title,
                    url: url
                });
            } else {
                // Fallback: URL másolása vágólapra
                await navigator.clipboard.writeText(url);

                // Felhasználó értesítése
                this.shareButton.style.backgroundColor = '#2a332c';
                this.shareButton.style.borderColor = '#caffb1';
                this.shareButton.style.color = '#caffb1';
                this.shareButton.innerHTML = `
                    <div>Másolva</div>
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M12.736 3.97a.733.733 0 0 1 1.047 0c.286.289.29.756.01 1.05L7.88 12.01a.733.733 0 0 1-1.065.02L3.217 8.384a.757.757 0 0 1 0-1.06.733.733 0 0 1 1.047 0l3.052 3.093 5.4-6.425a.247.247 0 0 1 .02-.022Z"/>
                    </svg>
                `;

                // Visszaállítás eredeti állapotra
                setTimeout(() => {
                    this.shareButton.style.backgroundColor = '';
                    this.shareButton.style.borderColor = '';
                    this.shareButton.style.color = '';
                    this.shareButton.innerHTML = `
                        <div>Megosztás</div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-share" viewBox="0 0 16 16">
                            <path d="M13.5 1a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3M11 2.5a2.5 2.5 0 1 1 .603 1.628l-6.718 3.12a2.5 2.5 0 0 1 0 1.504l6.718 3.12a2.5 2.5 0 1 1-.488.876l-6.718-3.12a2.5 2.5 0 1 1 0-3.256l6.718-3.12A2.5 2.5 0 0 1 11 2.5m-8.5 4a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3m11 5.5a1.5 1.5 0 1 0 0 3 1.5 1.5 0 0 0 0-3"/>
                        </svg>
                    `;
                }, 2000);
            }
        } catch (err) {
            console.error('Megosztás sikertelen:', err);
        }
    }

    async addToCart(url) {
        try {
            if (window.cart) {
                await window.cart.add(null, url);
            } else {
                console.error('Nem elérhető a kosár');
            }
        } catch (err) {
            console.error('Hiba a kosárba rakáskor:', err);
        }
    }
}

// Modul-stílusú inicializálás az importálás támogatásához
document.addEventListener('DOMContentLoaded', () => {
    new ProductPage();
});

// Az osztály exportálása a potenciális újrafelhasználáshoz
export default ProductPage;

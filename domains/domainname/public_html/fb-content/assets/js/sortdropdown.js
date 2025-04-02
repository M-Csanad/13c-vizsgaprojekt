import HungarianComparator from "./hungarianComparator.js";

export default class SortDropdown {
    constructor(updateCallback, activeSortType = 'name-asc') {
        this.updateCallback = updateCallback;
        this.isOpen = false;
        this.hungarianComparator = new HungarianComparator();
        this.activeSortType = activeSortType;
        this.initDOM();
        this.bindEvents();
        this.updateActiveOption();
    }

    initDOM() {
        this.sortOptions = [
            { id: 'name-asc', label: 'A-Z sorrend növekvő', icon: 'sort-alpha-down' },
            { id: 'name-desc', label: 'A-Z sorrend csökkenő', icon: 'sort-alpha-up' },
            { id: 'price-asc', label: 'Ár szerint növekvő', icon: 'sort-numeric-down' },
            { id: 'price-desc', label: 'Ár szerint csökkenő', icon: 'sort-numeric-up' },
            { id: 'rating-asc', label: 'Értékelés szerint növekvő', icon: 'star' },
            { id: 'rating-desc', label: 'Értékelés szerint csökkenő', icon: 'star-fill' }
        ];

        document.querySelector('.sort-open').innerHTML = `
            <div class="sort-current">
                <span>Rendezés</span>
                <svg xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M7.247 11.14 2.451 5.658C1.885 5.013 2.345 4 3.204 4h9.592a1 1 0 0 1 .753 1.659l-4.796 5.48a1 1 0 0 1-1.506 0z"/>
                </svg>
            </div>
            <div class="sort-options">
                ${this.sortOptions.map(option => `
                    <div class="sort-option" data-sort="${option.id}">
                        <svg class="bi bi-${option.icon}" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 16">
                            ${this.getSVGPath(option.icon)}
                        </svg>
                        <span>${option.label}</span>
                    </div>
                `).join('')}
            </div>
        `;

        this.dropdown = document.querySelector('.sort-open');
    }

    getSVGPath(icon) {
        switch(icon) {
            case 'sort-alpha-down':
                return '<path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371h-1.781zm1.57-.785L11 2.687h-.047l-.652 2.157h1.351z"/><path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645V14zM4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293V2.5z"/>';
            case 'sort-alpha-up':
                return '<path fill-rule="evenodd" d="M10.082 5.629 9.664 7H8.598l1.789-5.332h1.234L13.402 7h-1.12l-.419-1.371h-1.781zm1.57-.785L11 2.687h-.047l-.652 2.157h1.351z"/><path d="M12.96 14H9.028v-.691l2.579-3.72v-.054H9.098v-.867h3.785v.691l-2.567 3.72v.054h2.645V14zm-8.46-.5a.5.5 0 0 1-1 0V3.707L2.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.498.498 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L4.5 3.707V13.5z"/>';
            case 'sort-numeric-down':
                return '<path d="M12.438 1.668V7H11.39V2.684h-.051l-1.211.859v-.969l1.262-.906h1.046z"/><path fill-rule="evenodd" d="M11.36 14.098c-1.137 0-1.708-.657-1.762-1.278h1.004c.058.223.343.45.773.45.824 0 1.164-.829 1.133-1.856h-.059c-.148.39-.57.742-1.261.742-.91 0-1.72-.613-1.72-1.758 0-1.148.848-1.835 1.973-1.835 1.09 0 2.063.636 2.063 2.687 0 1.867-.723 2.848-2.145 2.848zm.062-2.735c.504 0 .933-.336.933-.972 0-.633-.398-1.008-.94-1.008-.52 0-.927.375-.927 1 0 .64.418.98.934.98z"/><path d="M4.5 2.5a.5.5 0 0 0-1 0v9.793l-1.146-1.147a.5.5 0 0 0-.708.708l2 1.999.007.007a.497.497 0 0 0 .7-.006l2-2a.5.5 0 0 0-.707-.708L4.5 12.293V2.5z"/>';
            case 'sort-numeric-up':
                return '<path d="M12.438 1.668V7H11.39V2.684h-.051l-1.211.859v-.969l1.262-.906h1.046z"/><path fill-rule="evenodd" d="M11.36 14.098c-1.137 0-1.708-.657-1.762-1.278h1.004c.058.223.343.45.773.45.824 0 1.164-.829 1.133-1.856h-.059c-.148.39-.57.742-1.261.742-.91 0-1.72-.613-1.72-1.758 0-1.148.848-1.835 1.973-1.835 1.09 0 2.063.636 2.063 2.687 0 1.867-.723 2.848-2.145 2.848zm.062-2.735c.504 0 .933-.336.933-.972 0-.633-.398-1.008-.94-1.008-.52 0-.927.375-.927 1 0 .64.418.98.934.98z"/><path d="M4.5 13.5a.5.5 0 0 1-1 0V3.707L2.354 4.854a.5.5 0 1 1-.708-.708l2-1.999.007-.007a.498.498 0 0 1 .7.006l2 2a.5.5 0 1 1-.707.708L4.5 3.707V13.5z"/>';
            case 'star':
                return '<path d="M2.866 14.85c-.078.444.36.791.746.593l4.39-2.256 4.389 2.256c.386.198.824-.149.746-.592l-.83-4.73 3.522-3.356c.33-.314.16-.888-.282-.95l-4.898-.696L8.465.792a.513.513 0 0 0-.927 0L5.354 5.12l-4.898.696c-.441.062-.612.636-.283.95l3.523 3.356-.83 4.73zm4.905-2.767-3.686 1.894.694-3.957a.565.565 0 0 0-.163-.505L1.71 6.745l4.052-.576a.525.525 0 0 0 .393-.288L8 2.223l1.847 3.658a.525.525 0 0 0 .393.288l4.052.575-2.906 2.77a.565.565 0 0 0-.163.506l.694 3.957-3.686-1.894a.503.503 0 0 0-.461 0z"/>';
            case 'star-fill':
                return '<path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>';
            default:
                return '';
        }
    }

    bindEvents() {
        this.dropdown.querySelector('.sort-current').addEventListener('click', () => this.toggle());
        
        this.dropdown.querySelectorAll('.sort-option').forEach(option => {
            option.addEventListener('click', () => this.handleSort(option.dataset.sort));
        });

        document.addEventListener('click', (e) => {
            if (!this.dropdown.contains(e.target) && this.isOpen) {
                this.close();
            }
        });
    }

    toggle() {
        this.isOpen ? this.close() : this.open();
    }

    open() {
        if (this.isOpen) return;
        this.isOpen = true;

        const options = this.dropdown.querySelector('.sort-options');
        const arrow = this.dropdown.querySelector('svg');

        gsap.to(options, {
            height: 'auto',
            duration: 0.3,
            ease: 'power2.out',
            opacity: 1
        });

        gsap.to(arrow, {
            rotation: 180,
            ease: "none",
            duration: 0.1
        });
    }

    close() {
        if (!this.isOpen) return;
        this.isOpen = false;

        const options = this.dropdown.querySelector('.sort-options');
        const arrow = this.dropdown.querySelector('svg');

        gsap.to(options, {
            height: 0,
            duration: 0.3,
            ease: 'power2.in',
            opacity: 0
        });

        gsap.to(arrow, {
            rotation: 0,
            ease: "none",
            duration: 0.1
        });
    }

    handleSort(sortType) {
        this.close();
        const [property, direction] = sortType.split('-');
        this.activeSortType = sortType;
        this.updateActiveOption();
        this.updateCallback(property, direction);
    }

    updateActiveOption() {
        this.dropdown.querySelectorAll('.sort-option').forEach(option => {
            option.classList.toggle('active', option.dataset.sort === this.activeSortType);
        });
    }

    sortProducts(products, property, direction) {
        const sorted = [...products].sort((a, b) => {
            let comparison;
            
            switch(property) {
                case 'name':
                    comparison = this.hungarianComparator.compareStrings(a.name, b.name);
                    break;
                case 'price':
                    comparison = a.unit_price - b.unit_price;
                    break;
                case 'rating':
                    comparison = a.avg_rating - b.avg_rating;
                    break;
                default:
                    comparison = 0;
            }

            return direction === 'asc' ? comparison : -comparison;
        });

        return sorted;
    }
}

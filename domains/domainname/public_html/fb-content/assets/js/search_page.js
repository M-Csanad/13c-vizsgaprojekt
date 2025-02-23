import FilterWindow from "./filterwindow.js";
import APIFetch from "./apifetch.js";
import SortDropdown from "./sortdropdown.js";
import SubcategorySite from "../../fb-subcategories/js/subcategory.js";

class SearchPage extends SubcategorySite {
    constructor() {
        super(false); // Megakadályozza, hogy a SubcategorySite lekérést végezzen
        this.initialize();
    }

    initDOM() {
        super.initDOM();
        this.headerProductCount = document.querySelector('header > .product-count');
    }

    async initialize() {
        await this.fetchProducts();
        this.filter = new FilterWindow(this.allProducts, this.handleFilterUpdate.bind(this));
        this.sortDropdown = new SortDropdown(this.handleSort.bind(this), '');
        this.updateUI();
    }

    async fetchProducts() {
        const urlParams = new URLSearchParams(window.location.search);
        const query = urlParams.get('q');
        
        if (!query) {
            console.error('Nincs keresési érték');
            return;
        }

        const response = await APIFetch(`/api/search?q=${encodeURIComponent(query)}`, 'GET');

        if (!response.ok) {
            console.error('Hiba a keresés során:', response.statusText);
            return;
        }

        const data = await response.json();
        if (data.type === "SUCCESS") {
            this.allProducts = data.message;
            this.filteredProducts = [...this.allProducts];
            this.headerProductCount.textContent = `${this.filteredProducts.length} találat`;
        } else {
            console.error('Hiba a keresési találatok lekérésében:', data.message);
        }
    }
}

const page = new SearchPage();

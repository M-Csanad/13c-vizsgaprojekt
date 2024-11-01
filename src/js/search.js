// Segédfüggvény egy keresőmező inicializálásához
function initializeSearch(search) {
    const searchInput = search.querySelector("input");
    const searchItemsContainer = search.closest(".section-body").querySelector('.items');
    const searchType = search.dataset.searchType; // Pl. 'category' vagy 'user'
    const searchConfig = {
        category: {
            template: (item) => `
                <img src='${item.thumbnail_image_uri}'><div><b>${item.name}</b> - 
                ${(item.type === "category") ? "Főkategória" : `Alkategória <i>(${item.parent_category})</i>`}</div>`,
            clickHandler: (item) => itemClickHandler(item, ["id", "type", "name"])
        },
        user: {
            template: (user) => `
                <div><b>${user.name}</b> - ${user.email} (<i>${user.role}</i>)</div>`,
            clickHandler: (user) => itemClickHandler(user, ["id", "name"])
        }
    };

    // Aszinkron keresés a megfelelő PHP segítségével
    async function searchHandler() {
        let input = searchInput.value;
        let data = new FormData();
        data.append('search_term', input);
        if (input.length > 0) {
            const response = await fetch(`./misc/search_${searchType}.php`, {
                method: "POST",
                body: data
            });

            if (response.ok) {
                let data = await response.json();
                populateItemContainer(data);
            }
        } else {
            toggleDropdown(false);
        }
    }

    // A legördülömenü feltöltése találatokkal
    function populateItemContainer(items) {
        if (items === "Nincs találat!") {
            toggleDropdown(false);
            return;
        }

        searchItemsContainer.innerHTML = "";
        toggleDropdown(true);

        if (!Array.isArray(items)) items = [items];

        const config = searchConfig[searchType];
        if (!config) return;

        for (let item of items) {
            let itemDOM = document.createElement("div");
            itemDOM.className = "search-item";
            itemDOM.innerHTML = config.template(item);
            itemDOM.addEventListener("click", () => config.clickHandler(item));
            searchItemsContainer.appendChild(itemDOM);
        }
    }

    // A legördülőmenü elhelyezése a beviteli mező alá (mivel az űrlapon overflow: hidden van, ezért a legördülőmenü kívül kell hogy legyen)
    function positionDropdown() {
        const rect = search.getBoundingClientRect();
        searchItemsContainer.style.top = `${rect.bottom + window.scrollY}px`;
        searchItemsContainer.style.left = `${rect.left + window.scrollX}px`;
        searchItemsContainer.style.width = `${rect.width}px`;
    }

    // Segédfüggvény a keresési találatok megjelenítése / elrejtése
    function toggleDropdown(show) {
        if (show) {
            search.style.borderRadius = "5px 5px 0 0";
            searchItemsContainer.style.display = "block";
        } else {
            searchItemsContainer.style.display = "none";
            searchItemsContainer.innerHTML = "";
            search.style.borderRadius = "5px 5px 5px 5px";
        }
    }
    // A keresési találatra történő kattintás kezelése
    function itemClickHandler(item, fields) {

        fields.forEach(field => {
            const input = search.querySelector(`input[name=${searchType}_${field}]`);
            if (input) input.value = item[field] || 'null';
        });

        searchInput.value = item.name;
        toggleDropdown(false);
        validateSearchInput();
    }
    
    // A rejtett mezők értékének visszaállítása
    function resetInputs() {
        search.querySelectorAll("input[type=hidden]").forEach(input => input.value = 'null');
    }

    // A keresőmezők validálása a rejtett mezők alapján
    function validateSearchInput() {
        const invalid = Array.from(search.querySelectorAll("input[type=hidden]")).some(input => input.value === 'null');
        searchInput.setCustomValidity(invalid ? 'Kérjük válasszon egy meglévő elemet!' : '');
    }

    // Eseménykezelés
    // A keresőmezőre kattintáskor jelenjenek meg az aktuális találatok
    searchInput.addEventListener("focusin", async () => {
        if (searchItemsContainer.style.display == "none") {
            await searchHandler();
            if (searchItemsContainer.children.length > 0) {
                toggleDropdown(true);
                positionDropdown();
            }
        }
    });

    // A képernyő átméretezésekor a legördülő menű pozícióját frissítjük
    window.addEventListener("resize", positionDropdown);
    window.addEventListener("load", validateSearchInput);

    // Ha nem a keresőmezőre, vagy a legördülő menüre kattint a user, akkor a legördülő menü becsukódik
    document.addEventListener("click", (e) => {
        if (!searchInput.contains(e.target) && !searchItemsContainer.contains(e.target)) {
            toggleDropdown(false);
        }
    });

    // Minden bevitt karakter után meghívjuk a szükséges függvényeket
    searchInput.addEventListener("input", async () => {
        resetInputs();
        positionDropdown();
        await searchHandler();
        validateSearchInput();
    });
}

// Mindegyik keresőmezőt inicializáljuk
document.querySelectorAll(".search").forEach(search => initializeSearch(search));
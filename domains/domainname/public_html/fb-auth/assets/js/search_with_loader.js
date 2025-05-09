// Segédfüggvény egy keresőmező inicializálásához
function initializeSearch(search) {
    const searchInput = search.querySelector("input");
    const parentForm = search.closest('form');
    const inputGrid = search.closest('.input-grid');
    const searchItemsContainer = search.closest(".section-body").querySelector('.items');
    const searchType = search.dataset.searchType; // Pl. 'category' vagy 'user'
    const autofill = search.dataset.autofillFields ?? null;
    const searchConfig = {
        category: {
            autofillFields: [
                { name: "name"}, 
                { name: "subname"}, 
                { name: "description"}, 
                { name: "type"}, 
                { name: "parent_category", disabledByDefault: true}
            ],
            template: (item) => `
                <img src='${item.thumbnail_image_horizontal_uri}'><div><b>${item.name}</b> - 
                ${(item.type === "category") ? "Főkategória" : `Alkategória <i>(${item.parent_category})</i>`}</div>`,

            clickHandler: (item) => {
                if (!autofill) {
                    itemClickHandler(item, ["id", "type", "name"]);
                }
                else {
                    itemClickHandler(item, ["id", "type", "name"], { fields: [
                        { field: "name", value: item.name },
                        { field: "subname", value: item.subname },
                        { field: "description", value: item.description },
                        { field: "type", value: item.parent_category ? "sub" : "main" },
                        { field: "parent_category", value: item.parent_category ? item.parent_category : null }
                    ]});
                }
            }
        },
        user: {
            template: (user) => `
                <div><b>${user.name}</b> - ${user.email} (<i>${user.role}</i>)</div>`,
            clickHandler: (user) => itemClickHandler(user, ["id", "name"])
        },
        product: {
            autofillFields: [
                { name: "name" }, 
                { name: "description" }, 
                { name: "price" }, 
                { name: "stock" },
                { name: "tags", multiple: true }
            ],
            template: (product) => {
                if (!product.category || !product.subcategory) {
                    return `<img src='${product.thumbnail_image_horizontal_uri}'><div><b>${product.name}</b> - <i>Még nincs kategóriába sorolva.</i></div>`;
                }
                else {
                    return `<img src='${product.thumbnail_image_horizontal_uri}'><div><b>${product.name}</b> - <i>${product.category ? product.category : "#"} / ${product.subcategory ? product.subcategory : "#"}</i></div>`;
                }
            },
            clickHandler: (product) => {
                if (!autofill) {
                    itemClickHandler(product, ["id", "name"]);
                }
                else {
                    itemClickHandler(product, ["id", "name"], { fields: [
                        { field: "name", value: product.name },
                        { field: "description", value: product.description },
                        { field: "price", value: product.unit_price },
                        { field: "stock", value: product.stock },
                        { field: "tags", value: product.tag_ids.split(",") }
                    ]});
                }
            }
        }
    };

    let isSearchEnabled = true;
    let lastContentfulSearchTerm = "";
    let isAnimating = false;

    // Aszinkron keresés a megfelelő PHP segítségével
    async function searchHandler() {
        let input = searchInput.value;
        let data = new FormData();
        data.append('search_term', input);
        if (input.length > 0) {
            clearItems();
            toggleLoader(true);

            try {
                const response = await fetch(`../../../../.ext/api/search_${searchType}.php`, {
                    method: "POST",
                    body: data
                });
    
                if (response.ok) {
                    let data = await response.json();
    
                    if (typeof data == "object") {
                        lastContentfulSearchTerm = input;
                    }
                    else {
                        isSearchEnabled = false;
                    }

                    await populateItemContainer(data);
                    setTimeout(()=>{
                        toggleLoader(false);
                    }, 300);
                }
            }
            catch(e) {
                console.log("Nem sikerült a keresés. Hiba: " + e);
            }
        } else {
            toggleDropdown(false);
            toggleLoader(false);
        }
    }

    function toggleLoader(show) {
        const loader = searchItemsContainer.querySelector('.loader-wrapper');
        if (!loader || isAnimating) return;

        if (show) {
            toggleDropdown(true);

            isAnimating = true;

            searchItemsContainer.style.overflowY = "hidden";
            searchItemsContainer.style.maxHeight = "100px";
            loader.style.display = "flex";
            loader.style.opacity = 1;

            setTimeout(()=>{
                isAnimating = false;
            }, 300);
        }
        else {
            isAnimating = true;
            loader.style.opacity = 0;
            
            setTimeout(() => {
                searchItemsContainer.style.maxHeight = "301px";
                loader.style.display = "none";
                isAnimating = false;
                searchItemsContainer.style.overflowY = "auto";
            }, 300);
        }
    }

    function clearItems() {
        searchItemsContainer.querySelectorAll("div.search-item").forEach(e => e.remove());
    }

    // A legördülömenü feltöltése találatokkal
    async function populateItemContainer(items) {
        if (items === "Nincs találat!") {
            toggleDropdown(false);
            return;
        }

        clearItems();
        toggleDropdown(true);

        if (!Array.isArray(items)) items = [items];

        const config = searchConfig[searchType];
        if (!config) return;

        let loadPromises = [];
        for (let item of items) {
            let itemDOM = document.createElement("div");
            itemDOM.className = "search-item";
            itemDOM.innerHTML = config.template(item);

            const image = itemDOM.querySelector('img');
            if (image) {
                const loadPromise = new Promise((resolve) => {
                    image.onload = resolve;
                    image.onerror = resolve;
                });
                loadPromises.push(loadPromise);
            }

            itemDOM.addEventListener("click", () => config.clickHandler(item));
            searchItemsContainer.appendChild(itemDOM);
        }

        await Promise.all(loadPromises);
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
            clearItems();
            search.style.borderRadius = "5px 5px 5px 5px";
        }
    }
    // A keresési találatra történő kattintás kezelése
    function itemClickHandler(item, fields, outputData = {}) { // Módosításkor a kitöltendő mezők az outputData alapján töltődnek ki
        fields.forEach(field => {
            const input = search.querySelector(`input[name=${searchType}_${field}]`);
            if (input) input.value = item[field];
        });

        if (outputData.fields) {
            outputData.fields.forEach(({ field, value }) => {
                const input = parentForm.querySelector(`[name=${field}]`);
                if (input) {
                    if (value) {
                        if (Array.isArray(value)) {
                            for (let id of value) {
                                input.querySelector(`input[type="checkbox"][value="${id}"]`).parentElement.click();
                            }
                        }
                        else {
                            input.value = value;
                            if (input.disabled) input.disabled = false;
                            input.dispatchEvent(new Event('change'));
                        }
                    }
                }
            });
        }

        searchInput.value = item.name;
        toggleDropdown(false);
        validateSearchInput();
        if (item.role) {
            disableRoleOption(item.role);
            selectFirstValidOption(inputGrid.querySelector("select"));
            enableDisabledInputs();
        }
    }

    function disableRoleOption(role) {
        let currentRoleOption = inputGrid.querySelector(`option[value='${role}']`);

        Array.from(inputGrid.querySelectorAll("option")).forEach((e) => {e.disabled = false;});
        currentRoleOption.disabled = true;
    }

    function selectFirstValidOption(element) {
        if (element.value) {
            element.querySelector('option:checked').selected = false;
        }
        element.querySelector('option:enabled').selected = true;
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

    function enableDisabledInputs() {
        const disabledInputs = inputGrid.querySelectorAll("input:disabled, select:disabled");
        disabledInputs.forEach((input)=>{input.disabled = false});
    }

    function clearAutofillFields() {
        const fields = searchConfig[searchType].autofillFields;
        for (let field of fields) {
            let element = parentForm.querySelector(`[name=${field.name}]`);
            if (element) {
                if (field.multiple) {
                    element.querySelectorAll("input:checked").forEach(e => e.click());
                    return;
                }
                
                if (field.disabledByDefault) element.disabled = true;
                if (element.nodeName == "SELECT") {
                    selectFirstValidOption(element);
                    element.dispatchEvent(new Event("change"));
                }
                else if (element.value != "") {
                    element.value = "";
                }
            }
        }
    }

    function disableSelectInputs() {
        inputGrid.querySelectorAll('select').forEach(e => e.disabled = true);
    }

    // Eseménykezelés
    // A keresőmezőre kattintáskor jelenjenek meg az aktuális találatok
    searchInput.addEventListener("focusin", async () => {
        if (searchItemsContainer.style.display == "none") {
            if (isSearchEnabled) {
                await searchHandler();
            }
            if (searchItemsContainer.children.length > 1) { // A loader-t nem számoljuk bele
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

        // A kereső optimalizálása, hogy ne legyenek felesleges lekérések
        if (!isSearchEnabled) {
            let value = searchInput.value;
            if (lastContentfulSearchTerm.includes(value) || value == "" || lastContentfulSearchTerm == value) {
                isSearchEnabled = true;
            }
        }

        if (isSearchEnabled) {
            await searchHandler();
            validateSearchInput();
        }

        if (!searchInput.checkValidity()) {
            if (autofill) {
                clearAutofillFields();
                disableSelectInputs();
            }
        }
    });
}

// Mindegyik keresőmezőt inicializáljuk
document.querySelectorAll(".search").forEach(search => initializeSearch(search));
/**
 * Dashboard osztály - Az adminisztrációs felület JavaScript funkcionalitását kezeli
 * A kód jobb kezelhetőség és modularitás érdekében osztályba szervezve
 */
class Dashboard {
    constructor() {
        // Állapottárolók és beállítások
        this.maxFileSize = 10; // MB-ban
        this.isPopupVisible = false;
        this.isTransitioning = false;

        // Kivételek azoknál az űrlapoknál, ahol speciális megerősítésre van szükség
        this.formExceptions = {
            "#form-role": {
                field: "select[name=role]",
                value: "Administrator"
            }
        };

        // Képfrissítések tárolása űrlaponként (form title => updates map)
        this.imageUpdates = new Map();
        
        // Számlálók a képek azonosítóihoz űrlaponként (form title => counters)
        this.imageCounters = new Map();

        // Az osztály inicializálása
        this.init();
    }

    /**
     * Inicializálja a Dashboard objektumot
     */
    async init() {
        try {
            // DOM betöltésének megvárása
            await this.waitForLoad();
            
            // DOM elemek inicializálása és események kezelése
            this.initDOM();
            this.bindEvents();
            
            // Speciális elemek inicializálása
            this.setupForms();
            this.setupFileInputs();
            this.setupToggleButtons();
            this.setupCategories();
            
            // Setup image card observer
            this.setupImageCardObserver();
            
            // Kezdeti állapot beállítása
            this.hideDisplayMessages();
            
        } catch (err) {
            console.error("Dashboard inicializálási hiba:", err);
        }
    }

    /**
     * Függvény, amelynek szerepe az oldal betöltésének megvárása
     */
    waitForLoad() {
        return new Promise((resolve) => {
            if (document.readyState === "complete") {
                resolve();
            } else {
                window.addEventListener("load", resolve);
            }
        });
    }

    /**
     * DOM elemek referenciáinak létrehozása
     */
    initDOM() {
        // Űrlap elemek
        this.loaderForms = document.querySelectorAll("form[data-show-loader=true]");
        this.confirmForms = document.querySelectorAll("form[data-needs-confirm='true']");
        
        // Kategória/alkategória elemek
        this.parentCategoryInput = document.querySelector("select#parent_category");
        this.parentCategoryModifyInput = document.querySelector("select#parent_category_modify");
        this.categoryType = document.getElementById("type");
        
        // Navigációs elemek
        this.pageLinks = document.querySelectorAll(".page");
        this.pages = document.querySelectorAll(".section-group");
        this.sectionHeaders = document.querySelectorAll(".section-header");
        
        // Fájl feltöltési elemek
        this.fileInputs = document.querySelectorAll("input[data-type]");
        this.toggleButtons = document.querySelectorAll(".toggle");
    }

    /**
     * Eseménykezelők beállítása
     */
    bindEvents() {
        // Szekció fejlécek eseménykezelése
        this.sectionHeaders.forEach(header => {
            header.addEventListener("click", (e) => this.expandGroup(e));
            header.addEventListener("keydown", (e) => {
                if (e.code === "Space" || e.code === "Enter") this.expandGroup(e);
            });
        });
        
        // Oldal váltás kezelése
        this.pageLinks.forEach(page => {
            page.addEventListener("click", () => this.togglePage(page.dataset.pageid));
            page.addEventListener("keydown", (e) => {
                if (e.code === "Space" || e.code === "Enter") 
                    this.togglePage(page.dataset.pageid);
            });
        });
        
        // Kategória típus változásának kezelése
        if (this.categoryType) {
            this.categoryType.addEventListener("change", () => this.handleCategoryTypeChange());
        }
        
        // Globális Escape billentyű kezelés
        window.addEventListener("keydown", (e) => {
            if (e.code === "Escape" && this.isPopupVisible) {
                const popup = document.querySelector(".popup");
                if (popup) this.closePopup(popup);
                this.isPopupVisible = false;
            }
        });
    }

    /**
     * Következő képazonosító megszerzése az adott típushoz
     * @param {string} formTitle - Az űrlap címe
     * @param {string} type - A kép típusa (pl. 'new', 'edit', stb.)
     * @returns {number} - A következő ID
     */
    getNextImageId(formTitle, type) {
        // Biztosítjuk, hogy a form létezik a számlálók között
        if (!this.imageCounters.has(formTitle)) {
            this.imageCounters.set(formTitle, {
                new: 0,
                edit: 0,
                delete: 0
            });
        }
        
        // Megszerezzük a számlálókat az adott űrlaphoz
        const counters = this.imageCounters.get(formTitle);
        
        // Növeljük az adott típus számlálóját
        counters[type]++;
        return counters[type];
    }

    /**
     * Űrlapok beállítása (betöltési animáció, megerősítés, stb.)
     */
    setupForms() {
        // Betöltési animációt megjelenítő űrlapok
        this.loaderForms.forEach(form => {
            // Megfelelő enctype beállítása fájl feltöltéshez
            form.setAttribute('enctype', 'multipart/form-data');
            
            form.addEventListener("submit", () => {
                if (form.dataset.needsConfirm === "false") {
                    toggleLoader("Képek optimalizálása... Ez több percig is eltarthat.");
                }
            });
        });

        // Megerősítést igénylő űrlapok
        this.confirmForms.forEach(form => {
            const formSubmitter = form.querySelector("input[type=submit]");
            const formMessage = form.dataset.confirmMessage;
            const formTitle = form.dataset.title || 'default';
            
            form.addEventListener("submit", (e) => {
                if (this.isPopupVisible || this.isFormException(form) === false) {
                    return;
                }
                
                // Megakadályozzuk az automatikus elküldést
                e.preventDefault();
                
                // Megerősítő ablak megjelenítése
                const popup = this.createConfirmPopup(formMessage);
                
                // Megerősítés gomb kezelése
                popup.querySelector("input.confirm").addEventListener("click", () => {
                    this.closePopup(popup);

                    // Ha módosítás űrlap, csatoljuk a képfrissítéseket
                    if (form.dataset.role === "modify") {
                        let hiddenInput = form.querySelector('input[name="image_updates"]');
            
                        if (!hiddenInput) {
                            hiddenInput = document.createElement('input');
                            hiddenInput.type = 'hidden';
                            hiddenInput.name = 'image_updates';
                            form.appendChild(hiddenInput);
                        }

                        // Képfrissítések JSON szerializálása
                        const formUpdates = this.imageUpdates.get(formTitle);
                        if (formUpdates && formUpdates.size > 0) {
                            const updatesArray = Array.from(formUpdates.values());
                            hiddenInput.value = JSON.stringify(updatesArray);
                        }
                    }

                    // Űrlap elküldése késleltetve, hogy az animáció befejeződjön
                    setTimeout(() => {
                        if (this.hasImage(form)) {
                            toggleLoader("Képek optimalizálása... Ez több percig is eltarthat.");
                        }
                        formSubmitter.click();
                        this.isPopupVisible = false;
                    }, 300);
                });
                
                // Mégsem gomb kezelése
                popup.querySelector("input.cancel").addEventListener("click", () => {
                    this.closePopup(popup);
                    this.isPopupVisible = false;
                });
            });
        });
    }

    /**
     * Fájl feltöltés kezelése és validálása
     */
    setupFileInputs() {
        this.fileInputs.forEach(input => {
            const orientation = input.dataset.orientation;
            const inputCount = input.dataset.count;
            const acceptedType = input.dataset.type;
            input.addEventListener("input", async () => {
                await this.validateFile(input, orientation, inputCount, acceptedType);
            });
        });
    }

    /**
     * Kapcsoló gombok beállítása
     */
    setupToggleButtons() {
        this.toggleButtons.forEach(button => {
            const input = button.closest(".file-input");
            const fileInput = input.querySelector("input[type=file]");
            
            button.addEventListener("click", () => {
                if (this.isTransitioning) return;
                this.isTransitioning = true;
                
                button.classList.toggle("on");
                if (button.classList.contains("on")) {
                    fileInput.classList.add("visible");
                    fileInput.removeAttribute("disabled");
                    fileInput.setAttribute("required", true);
                    setTimeout(() => {
                        this.isTransitioning = false;
                    }, 300);
                } else {
                    fileInput.classList.remove("visible");
                    setTimeout(() => {
                        if (!button.classList.contains("on")) {
                            fileInput.setAttribute("disabled", true);
                            fileInput.removeAttribute("required");
                        }
                        this.isTransitioning = false;
                    }, 300);
                }
            });
        });
    }

    /**
     * Kategóriák és alkategóriák kezelésének beállítása
     */
    setupCategories() {
        // Kategória legördülő listák beállítása
        document.querySelectorAll("select[data-table=category]").forEach(async (select) => {
            const table = select.dataset.table;
            
            // Kategóriák betöltése
            await this.populateOptions(select, null, table);
            this.setHiddenInput(select);
            
            // Alkategóriák kezelése
            const subcategorySelect = select
                .closest(".inline-input")
                ?.nextElementSibling
                ?.querySelector("select[data-table=subcategory]");
                
            if (subcategorySelect) {
                await this.populateOptions(subcategorySelect, select.value, "subcategory");
                this.setHiddenInput(subcategorySelect);
            }
            
            // Kategória változáskor frissítjük az alkategóriákat
            select.addEventListener("change", async () => {
                this.setHiddenInput(select);
                if (subcategorySelect) {
                    subcategorySelect.dispatchEvent(new CustomEvent("change", {
                        detail: { shouldPopulate: true }
                    }));
                }
            });
        });
        
        // Alkategória legördülő listák beállítása
        document.querySelectorAll("select[data-table=subcategory]").forEach(select => {
            select.addEventListener("change", async (e) => {
                if (e.detail && e.detail.shouldPopulate) {
                    const categorySelect = select.closest('.input-grid').querySelector("select[name=category]");
                    await this.populateOptions(select, categorySelect.value, "subcategory");
                }
                this.setHiddenInput(select);
            });
        });
    }

    /**
     * Kategória típus változás kezelése
     */
    handleCategoryTypeChange() {
        const selected = this.categoryType.value;
        
        if (selected === "sub") {
            this.parentCategoryInput.removeAttribute("disabled");
        } else {
            this.parentCategoryInput.setAttribute("disabled", true);
        }
    }

    /**
     * Ellenőrzi, hogy az űrlap kivételnek számít-e
     * @param {HTMLFormElement} form - Ellenőrizendő űrlap
     * @returns {boolean|null} - true ha kivétel és feltételek teljesülnek, false ha kivétel de nem teljesülnek, null ha nem kivétel
     */
    isFormException(form) {
        const selector = Object.keys(this.formExceptions).find(selector => form.matches(selector)) || null;
        
        if (selector) {
            const field = form.querySelector(this.formExceptions[selector]["field"]);
            return field && field.value === this.formExceptions[selector]["value"];
        }
        
        return null;
    }

    /**
     * Ellenőrzi, hogy van-e kép az űrlapban
     * @param {HTMLFormElement} form - Az ellenőrizendő űrlap
     * @returns {boolean} - Van-e kép feltöltve
     */
    hasImage(form) {
        const fileInputs = form.querySelectorAll('input[type="file"]');
        
        for (const fileInput of fileInputs) {
            if (fileInput.files && fileInput.files.length > 0) {
                return true;
            }
        }
        
        return false;
    }

    /**
     * Fájl fájl tájolásának meghatározása (képekhez)
     * @param {File} file - A vizsgálandó fájl
     * @returns {Promise<string>} - "horizontal" vagy "vertical" tájolás
     */
    getImageOrientation(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onload = () => {
                const img = new Image();
                img.onload = () => {
                    resolve(img.width >= img.height ? "horizontal" : "vertical");
                };
                img.onerror = reject;
                img.src = reader.result;
            };
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    /**
     * Videó tájolásának meghatározása
     * @param {File} file - A vizsgálandó fájl
     * @returns {Promise<string>} - "horizontal" vagy "vertical" tájolás
     */
    getVideoOrientation(file) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            
            reader.onload = () => {
                const video = document.createElement("video");
                
                video.onloadedmetadata = () => {
                    resolve(video.videoWidth >= video.videoHeight ? "horizontal" : "vertical");
                    video.remove();
                };
                
                video.onerror = reject;
                video.src = reader.result;
            };
            
            reader.onerror = reject;
            reader.readAsDataURL(file);
        });
    }

    /**
     * Fájl méretének meghatározása MB-ban
     * @param {File} file - A vizsgálandó fájl
     * @returns {number} - Fájl mérete MB-ban
     */
    getFileSize(file) {
        return file.size >> 20; // 2^20 - nal osztunk (B -> MB)
    }

    /**
     * Fájl validálása (méret, típus, tájolás)
     * @param {HTMLInputElement} input - Fájl beviteli mező
     * @param {string} orientation - Elvárt tájolás ("horizontal", "vertical", "any")
     * @param {string} inputCount - Elvárt fájlok száma ("singular", "multiple")
     * @param {string} acceptedType - Elfogadott fájltípus ("image", "video")
     */
    async validateFile(input, orientation, inputCount, acceptedType) {
        if (inputCount === "singular") {
            const file = input.files[0] || null;
            
            if (!file) {
                input.setCustomValidity("Kérjük adjon meg egy fájlt!");
                input.value = "";
                input.reportValidity();
                return;
            }
            
            const type = file.type || null;

            if (!type || !type.includes(acceptedType)) {
                input.setCustomValidity(`Kérjük ${acceptedType === "image" ? "képet" : "videót"} adjon meg!`);
                input.value = "";
                input.reportValidity();
                return;
            }
            
            if (this.getFileSize(file) > this.maxFileSize) {
                input.setCustomValidity(`Kérjük maximum ${this.maxFileSize} MB méretű fájlt töltsön fel!`);
                input.value = "";
                input.reportValidity();
                return;
            }
            
            if (orientation !== "any") {
                const currentOrientation = type.includes("image")
                    ? await this.getImageOrientation(file)
                    : await this.getVideoOrientation(file);
                
                if (currentOrientation !== orientation) {
                    input.setCustomValidity(`Kérjük megfelelő tájolású ${acceptedType === "image" ? "képet" : "videót"} adjon meg!`);
                    input.value = "";
                    input.reportValidity();
                    return;
                }
            }
            
            input.setCustomValidity("");
        } else {
            let isValid = true;
            
            for (const file of input.files) {
                if (!file) {
                    input.setCustomValidity("Kérjük adjon meg egy fájlt!");
                    input.value = "";
                    input.reportValidity();
                    isValid = false;
                    return;
                }
                
                const type = file.type || null;
                
                if (!type || !type.includes(acceptedType)) {
                    input.setCustomValidity(`Kérjük ${acceptedType === "image" ? "képet" : "videót"} adjon meg!`);
                    input.value = "";
                    input.reportValidity();
                    isValid = false;
                    return;
                }
                
                if (this.getFileSize(file) > this.maxFileSize) {
                    input.setCustomValidity(`Kérjük maximum ${this.maxFileSize} MB méretű fájlt töltsön fel!`);
                    input.value = "";
                    input.reportValidity();
                    isValid = false;
                    return;
                }
            }
            
            if (isValid) input.setCustomValidity("");
        }
    }

    /**
     * Beállít egy MutationObserver-t a képkártyák figyeléséhez
     */
    setupImageCardObserver() {
        // A DOM-hoz adott képkártyák figyelése
        const observer = new MutationObserver((mutations) => {
            mutations.forEach(mutation => {
                if (mutation.addedNodes.length) {
                    mutation.addedNodes.forEach(node => {
                        if (node.nodeType === 1) { // Element csomópont
                            // Képkártyák keresése
                            const imageCards = node.querySelectorAll ? 
                                Array.from(node.querySelectorAll('.image-card')) : [];
                            
                            if (node.classList && node.classList.contains('image-card')) {
                                imageCards.push(node);
                            }
                            
                            const formTitle = node.closest('form')?.dataset.title;

                            // Eseménykezelők csatolása az új képkártyákhoz
                            imageCards.forEach(card => this.attachImageCardHandlers(card, formTitle));
                            
                            // Hozzáadás gombok keresése
                            const addButtons = node.querySelectorAll ? 
                                Array.from(node.querySelectorAll('.add-field-light')) : [];
                            
                            if (node.classList && node.classList.contains('add-field-light')) {
                                addButtons.push(node);
                            }
                            
                            // Eseménykezelők csatolása az új hozzáadás gombokhoz
                            addButtons.forEach(button => this.attachAddButtonHandlers(button, formTitle));
                        }
                    });
                }
            });
        });
        
        // Dokumentum megfigyelésének indítása
        observer.observe(document.body, { 
            childList: true, 
            subtree: true 
        });
        
        // Meglévő képkártyák kezelése
        document.querySelectorAll('.image-card').forEach(card => {
            const formTitle = card.closest('form')?.dataset.title;

            this.attachImageCardHandlers(card, formTitle);
        });
        
        // Meglévő hozzáadás gombok kezelése
        document.querySelectorAll('.add-field-light').forEach(button => {
            const formTitle = button.closest('form')?.dataset.title;

            this.attachAddButtonHandlers(button, formTitle);
        });

        // Meglévő képkonténerek kezelése
        document.querySelectorAll('.image-cards').forEach(container => {
            this.updateDeleteButtonsState(container);
        });
    }
    
    /**
     * Csatolja az eseménykezelőket egy képkártyához
     * @param {HTMLElement} card - A képkártya elem
     * @param {string} formTitle - Az űrlap címe (azonosításhoz)
     */
    attachImageCardHandlers(card, formTitle = 'default') {
        // Törlés gomb kezelése
        const deleteBtn = card.querySelector('.action-delete');
        const parent = card.parentElement;
        if (deleteBtn) {
            const imageType = deleteBtn.dataset.imageType || 'item';
            
            // Ellenőrizzük, hogy ez termék kép-e és az utolsó-e a sorában
            const isProductImage = !['horizontal', 'vertical'].includes(imageType) && 
                                   !imageType.includes('category');
            
            if (isProductImage) {
                // Megkeressük a képek konténerét és megszámoljuk a képeket
                const container = parent;
                const imageCards = container ? container.querySelectorAll('.image-card') : [];
                const isLastImage = imageCards.length === 1;
                
                // Ha ez az utolsó kép, letiltjuk a törlés gombot
                if (isLastImage) {
                    deleteBtn.classList.add('disabled');
                    deleteBtn.setAttribute('disabled', 'true');
                    deleteBtn.setAttribute('aria-disabled', 'true');
                    deleteBtn.setAttribute('title', 'Legalább egy képnek maradnia kell');
                    return; // Nem adunk hozzá kattintás eseménykezelőt
                }
            }
            
            // Ha nem az utolsó kép vagy nem termék kép, akkor hozzáadjuk a törlés funkciót
            deleteBtn.addEventListener('click', () => {
                const imageId = deleteBtn.dataset.imageId;
                
                // Még egyszer ellenőrizzük, hogy nem az utolsó kép-e egy termék képsornál
                if (isProductImage) {
                    const container = parent;
                    const imageCards = container ? container.querySelectorAll('.image-card') : [];
                    if (imageCards.length === 1) {
                        return; // Ha valahogy mégis aktívvá vált, ne csináljon semmit
                    }
                }
                
                // Biztosítjuk a formTitle map létezését
                if (!this.imageUpdates.has(formTitle)) {
                    this.imageUpdates.set(formTitle, new Map());
                }
                
                const formUpdates = this.imageUpdates.get(formTitle);
                
                // Új képek törlése - teljesen eltávolítjuk
                if (imageType.startsWith('new-')) {
                    // A card.dataset.updateKey tartalmazza a pontos kulcsot, amellyel az elem hozzáadásra került
                    const updateKey = card.dataset.updateKey;
                    
                    if (updateKey && formUpdates.has(updateKey)) {
                        // Ha ez egy új kép, egyszerűen töröljük a nyilvántartásból
                        formUpdates.delete(updateKey);
                        card.remove();
                        
                        // Törlés után ellenőrizzük és frissítjük a többi képet
                        this.updateDeleteButtonsState(parent);
                        return;
                    }
                    
                    // Ha nincs updateKey, vagy nem találjuk a listában, de új elem, akkor egyszerűen eltávolítjuk
                    card.remove();
                    
                    // Törlés után ellenőrizzük és frissítjük a többi képet
                    this.updateDeleteButtonsState(parent);
                    return;
                }
                
                // Ha idáig eljutunk, akkor ez egy meglévő elem törlése
                let updateKey;
                
                // Kategóriaképekhez fix kulcsok
                if (imageType === 'horizontal' || imageType === 'vertical') {
                    updateKey = `${imageType}-edit`;
                }
                // Termékképeknél számozott törlési kulcsok
                else {
                    const deleteId = this.getNextImageId(formTitle, 'delete');
                    updateKey = `${imageType}-delete-${deleteId}`;
                }
                
                // Törlési adatok rögzítése meglévő képekhez
                formUpdates.set(updateKey, {
                    action: 'delete',
                    imageType: imageType,
                    id: imageId
                });
                
                card.remove();
                
                // Törlés után ellenőrizzük és frissítjük a többi képet
                this.updateDeleteButtonsState(parent);
            });
        }

        // Fájl beviteli mező változásának kezelése
        const fileInput = card.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.addEventListener('change', async () => {
                if (fileInput.files.length > 0) {
                    // Adatok megszerzése a dataset attribútumokból
                    const orientation = fileInput.dataset.orientation || 'any';
                    const inputCount = fileInput.dataset.count || 'singular';
                    const acceptedType = fileInput.dataset.type || 'image';
                    const imageId = fileInput.dataset.imageId;
                    const imageType = fileInput.dataset.imageType || 'item';
                    
                    try {
                        // Fájl validálása
                        await this.validateFile(fileInput, orientation, inputCount, acceptedType);
                        
                        if (fileInput.validity.valid) {
                            // Előnézet frissítése
                            const img = card.querySelector('img');
                            if (img) {
                                const file = fileInput.files[0];
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    img.src = e.target.result;
                                };
                                reader.readAsDataURL(file);
                                
                                // Frissítés előkészítése
                                if (!this.imageUpdates.has(formTitle)) {
                                    this.imageUpdates.set(formTitle, new Map());
                                }
                                
                                const formUpdates = this.imageUpdates.get(formTitle);
                                
                                // Új képek szerkesztése - a meglévő "add" rekord frissítése
                                if (imageType.startsWith('new-') && card.dataset.updateKey) {
                                    const updateKey = card.dataset.updateKey;
                                    
                                    if (formUpdates.has(updateKey)) {
                                        const currentUpdate = formUpdates.get(updateKey);
                                        
                                        if (currentUpdate.action === 'add') {
                                            // Frissítjük a fájl kulcsot
                                            currentUpdate.fileKey = fileInput.name;
                                            
                                            return;
                                        }
                                    }
                                }
                                
                                let updateKey;
                                
                                // Képfrissítések egyszerűsített adatstruktúrája
                                if (imageType === 'horizontal' || imageType === 'vertical') {
                                    // Kategória képeknél
                                    updateKey = `${imageType}-edit`;
                                    
                                    formUpdates.set(updateKey, {
                                        action: 'edit',
                                        imageType: imageType,
                                        fileKey: fileInput.name
                                    });
                                } 
                                else {
                                    // Termék képeknél
                                    const editId = imageId || this.getNextImageId(formTitle, 'edit');
                                    updateKey = `${imageType}-edit-${editId}`;
                                    
                                    // Kép indexének meghatározása (0-indexált)
                                    const imageCards = card.parentElement.querySelectorAll('.image-card');
                                    const index = Array.from(imageCards).indexOf(card);
                                    
                                    formUpdates.set(updateKey, {
                                        action: 'edit',
                                        imageType: imageType === 'thumbnail' ? 'thumbnail' : 'product_image',
                                        fileKey: fileInput.name,
                                        index: index,
                                        id: imageId || editId
                                    });
                                }
                                
                                if (!card.dataset.updateKey) {
                                    card.dataset.updateKey = updateKey;
                                }
                            }
                        }
                    } catch (error) {
                        console.error("Fájl validálási hiba:", error);
                    }
                }
            });
        }
    }

    /**
     * Frissíti a törlés gombok állapotát egy képkonténerben
     * @param {HTMLElement} container - A képkártyákat tartalmazó konténer
     */
    updateDeleteButtonsState(container) {
        if (!container) return;
        
        const imageCards = container.querySelectorAll('.image-card');
        const isProductContainer = !container.classList.contains('vertical') && 
                                   !container.classList.contains('horizontal');
                                   
        // Ha ez egy termék képkonténer és csak egy kép van, letiltjuk a törlés gombot
        if (isProductContainer && imageCards.length === 1) {
            const deleteBtn = imageCards[0].querySelector('.action-delete');
            if (deleteBtn) {
                deleteBtn.classList.add('disabled');
                deleteBtn.setAttribute('disabled', 'true');
                deleteBtn.setAttribute('aria-disabled', 'true');
                deleteBtn.setAttribute('title', 'Legalább egy képnek maradnia kell');
            }
        } 
        // Ha több kép van, engedélyezzük a törlés gombokat
        else if (isProductContainer && imageCards.length > 1) {
            imageCards.forEach(card => {
                const deleteBtn = card.querySelector('.action-delete');
                if (deleteBtn) {
                    deleteBtn.classList.remove('disabled');
                    deleteBtn.removeAttribute('disabled');
                    deleteBtn.setAttribute('aria-disabled', 'false');
                    deleteBtn.setAttribute('title', 'Kép törlése');
                }
            });
        }
    }

    /**
     * Csatolja az eseménykezelőket egy hozzáadás gombhoz
     * @param {HTMLElement} button - A hozzáadás gomb elem
     * @param {string} formTitle - Az űrlap címe (azonosításhoz)
     */
    attachAddButtonHandlers(button, formTitle = 'default') {
        const fileInput = button.querySelector('input[type="file"]');
        if (fileInput) {
            fileInput.addEventListener('change', async () => {
                if (fileInput.files.length > 0) {
                    // Adatok megszerzése a dataset attribútumokból
                    const orientation = fileInput.dataset.orientation || 'any';
                    const inputCount = fileInput.dataset.count || 'singular';
                    const acceptedType = fileInput.dataset.type || 'image';
                    const itemId = fileInput.dataset.productId || fileInput.dataset.categoryId || fileInput.dataset.id;
                    const itemType = fileInput.dataset.itemType || (fileInput.dataset.productId ? 'product' : 'category');
                    
                    try {
                        // Fájl validálása
                        await this.validateFile(fileInput, orientation, inputCount, acceptedType);
                        
                        if (fileInput.validity.valid) {
                            const file = fileInput.files[0];

                            // Új azonosító generálása
                            const newId = this.getNextImageId(formTitle, 'new');
                            
                            // Frissítés előkészítése
                            if (!this.imageUpdates.has(formTitle)) {
                                this.imageUpdates.set(formTitle, new Map());
                            }
                            
                            const formUpdates = this.imageUpdates.get(formTitle);
                            
                            // Bementi mező neve a PHP $_FILES tömbhöz
                            const inputName = `${itemType}-new-${newId}`;
                            
                            // Egyedi kulcs a frissítési nyilvántartáshoz
                            const updateKey = `${itemType}-new-${newId}`;
                            
                            // Kép típusának meghatározása
                            const isCategory = itemType === 'category';
                            const imageTypeValue = isCategory ? 
                                (button.closest('.image-cards.vertical') ? 'vertical' : 'horizontal') : 
                                (button.closest('.image-cards.thumbnail') ? 'thumbnail' : 'product_image');
                            
                            // Hozzáadási adatok egyszerűsített struktúrában
                            if (isCategory) {
                                // Kategória képeknél
                                formUpdates.set(updateKey, {
                                    action: 'add',
                                    imageType: imageTypeValue,
                                    fileKey: inputName
                                });
                            } else {
                                // Termék képeknél
                                // Kép indexének meghatározása (hány kép van már a containerben)
                                const imagesContainer = button.closest('.image-cards');
                                const currentCount = imagesContainer ? imagesContainer.querySelectorAll('.image-card').length : 0;
                                
                                formUpdates.set(updateKey, {
                                    action: 'add',
                                    imageType: imageTypeValue,
                                    fileKey: inputName,
                                    index: currentCount, // 0-indexelt pozíció
                                });
                            }
                            
                            // Új képkártya létrehozása és beillesztése
                            const imagesContainer = button.closest('.image-cards');
                            if (imagesContainer) {
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    // Új kártya elem létrehozása
                                    const newCard = document.createElement('div');
                                    newCard.className = 'image-card';
                                    newCard.dataset.id = newId;
                                    newCard.dataset.updateKey = updateKey;
                                    
                                    // Kártya HTML létrehozása
                                    newCard.innerHTML = `
                                        <img src="${e.target.result}" alt="Új kép"/>
                                        <div class="card-actions">
                                            <input type="file" name="${inputName}" id="${itemType}-new-${newId}" class="visually-hidden" accept="image/png, image/jpeg" data-orientation="any" data-type="image" data-count="singular" data-image-type="new-${itemType}" data-image-id="${newId}" data-form-title="${formTitle}" data-id="${itemId}" tabindex="-1">
                                            <label for="${itemType}-new-${newId}" class="action-edit" role="button" aria-label="Kép szerkesztése">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill edit" viewBox="0 0 16 16">
                                                    <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                                                </svg>
                                            </label>
                                            <button type="button" class="action-delete" role="button" aria-label="Kép törlése" data-image-id="${newId}" data-image-type="new-${itemType}" data-form-title="${formTitle}">
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                                                    <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                                                </svg>
                                            </button>
                                        </div>
                                    `;
                                    
                                    // Kártya beillesztése a hozzáadás gomb elé
                                    imagesContainer.insertBefore(newCard, button);
                                    
                                    // File feltöltése az új input mezőbe
                                    const newFileInput = newCard.querySelector('input[type="file"]');
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(file);
                                    newFileInput.files = dataTransfer.files;
                                    
                                    // Eseménykezelők hozzárendelése
                                    this.attachImageCardHandlers(newCard, formTitle);
                                    
                                    // Eredeti mező kiürítése későbbi használatra
                                    fileInput.value = '';
                                    
                                    // Frissítjük a törlés gombok állapotát a konténerben
                                    this.updateDeleteButtonsState(imagesContainer);
                                };
                                reader.readAsDataURL(file);
                            }
                        }
                    } catch (error) {
                        console.error("Fájl validálási hiba:", error);
                    }
                }
            });
        }
    }

    /**
     * Törli a képfrissítéseket egy adott űrlaphoz
     * @param {string} formTitle - Az űrlap címe, amelyhez a frissítések tartoznak
     */
    clearImageUpdates(formTitle = 'default') {
        // Töröljük a frissítéseket és nullázzuk a számlálókat is
        if (this.imageUpdates.has(formTitle)) {
            this.imageUpdates.set(formTitle, new Map());
            
            // Számlálók nullázása
            this.imageCounters.set(formTitle, {
                new: 0,
                edit: 0,
                delete: 0
            });
        }
    }

    /**
     * Megerősítő popup létrehozása
     * @param {string} message - Megjelenítendő üzenet
     * @returns {HTMLElement} - A létrehozott popup elem
     */
    createConfirmPopup(message) {
        this.isPopupVisible = true;
        
        const popup = document.createElement("div");
        const popupBody = document.createElement("div");
        
        popup.className = "popup";
        popupBody.className = "popup-body";
        popupBody.innerHTML = `
            <div class='popup-icon'>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-diamond-fill" viewBox="0 0 16 16">
                    <path d="M9.05.435c-.58-.58-1.52-.58-2.1 0L.436 6.95c-.58.58-.58 1.519 0 2.098l6.516 6.516c.58.58 1.519.58 2.098 0l6.516-6.516c.58-.58.58-1.519 0-2.098zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                </svg>
            </div>
            <h2 class='popup-message'>Biztosan folytatni szeretné?</h2>
            <div class='popup-description'>${message}</div>
            <div class='button-group-wrapper'>
                <div class='button-group'>
                    <input type='button' value='Folytatás' class='confirm'>
                    <input type='button' value='Mégsem' class='cancel'>
                </div>
            </div>`;
        
        popup.appendChild(popupBody);
        document.body.appendChild(popup);
        
        // Animáljuk a popup megjelenését
        popup.animate(
            {
                backgroundColor: ["rgba(0,0,0,0)", "rgba(0,0,0,0.35)"],
                backdropFilter: ["blur(0px)", "blur(3px)"]
            },
            {
                duration: 300,
                fill: "forwards",
                easing: "ease"
            }
        );
        
        popupBody.animate(
            { transform: ["scale(0)", "scale(1)"] },
            {
                duration: 300,
                fill: "forwards",
                easing: "ease"
            }
        );
        
        return popup;
    }

    /**
     * Popup bezárása animációval
     * @param {HTMLElement} popup - A bezárandó popup elem
    **/
    closePopup(popup) {
        popup.animate(
            {
                backgroundColor: ["rgba(0,0,0,0.35)", "rgba(0,0,0,0)"],
                backdropFilter: ["blur(3px)", "blur(0px)"]
            },
            {
                duration: 300,
                fill: "forwards",
                easing: "ease"
            }
        );
        
        popup.querySelector(".popup-body").animate(
            { transform: ["scale(1)", "scale(0)"] },
            {
                duration: 300,
                fill: "forwards",
                easing: "ease"
            }
        );
        
        setTimeout(() => {
            document.body.removeChild(popup);
        }, 300);
    }

    /**
     * Legördülő lista feltöltése adatokkal
     * @param {HTMLSelectElement} select - A feltöltendő select elem
     * @param {string|null} category - Kategória neve (alkategóriák lekéréséhez)
     * @param {string} table - Tábla neve ("category" vagy "subcategory")
     */
    async populateOptions(select, category, table) {
        const data = new FormData();
        data.append("search_type", "get_categories");
        data.append("table", table);
        if (category) data.append("category_name", category);
        
        try {
            const response = await fetch(`/api/auth/dashboard-search`, {
                method: "POST",
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: data
            });
            
            if (response.ok) {
                const responseData = await response.json();
                
                if (responseData.type === "ERROR" || responseData.type === "EMPTY") {
                    select.innerHTML = "";
                    return;
                }
                
                select.innerHTML = "";
                
                responseData.message.forEach(category => {
                    select.innerHTML += `<option value='${category.name}' data-id='${category.id}'>${category.name}</option>`;
                });
            }
        } catch (error) {
            console.error("Kategóriák lekérdezési hiba:", error);
        }
    }

    /**
     * Rejtett mező értékének beállítása a kiválasztott elem alapján
     * @param {HTMLSelectElement} select - A select elem, amelynek értéke alapján beállítjuk a hidden inputot
     */
    setHiddenInput(select) {
        const selected = select.querySelector("option:checked");
        if (!selected) return;
        
        const value = select.children.length > 0
            ? selected.dataset.id
            : "null";
            
        const hiddenInput = select.closest("div").querySelector("input[type=hidden]");
        if (hiddenInput) {
            hiddenInput.value = value;
        }
    }

    /**
     * Szekció kibontása/összezárása
     * @param {Event} e - Kattintási esemény
     */
    expandGroup(e) {
        const sourceElement = e.target;
        const section = sourceElement.closest("section");
        section.classList.toggle("active");
    }

    /**
     * Oldal váltása
     * @param {number} id - Az oldal azonosítója
     */
    togglePage(id) {
        // Összes oldal és link inaktívvá tétele
        for (let i = 0; i < this.pageLinks.length; i++) {
            this.pageLinks[i].classList.remove("active");
            this.pages[i].classList.remove("active");
        }
        
        // A kiválasztott oldal és link aktiválása
        this.pageLinks[id].classList.add("active");
        this.pages[id].classList.add("active");
    }

    /**
     * Üzenetek automatikus eltüntetésének kezelése
     */
    hideDisplayMessages() {
        const error = document.querySelector(".error");
        const success = document.querySelector(".success");
        
        if (error) {
            error.addEventListener("click", () => {
                error.style.opacity = "0";
                setTimeout(() => {
                    error.remove();
                }, 1000);
            });
        }
        
        if (success) {
            success.addEventListener("click", () => {
                success.style.opacity = "0";
                setTimeout(() => {
                    success.remove();
                }, 1000);
            });
        }
    }
}

// Dashboard objektum automatikus létrehozása
document.addEventListener('DOMContentLoaded', () => {
    window.dashboard = new Dashboard();
});
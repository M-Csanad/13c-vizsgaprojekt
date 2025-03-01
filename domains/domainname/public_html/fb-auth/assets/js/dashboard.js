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
     * Űrlapok beállítása (betöltési animáció, megerősítés, stb.)
     */
    setupForms() {
        // Betöltési animációt megjelenítő űrlapok
        this.loaderForms.forEach(form => {
            form.addEventListener("submit", () => {
                if (form.dataset.needsConfirm === "false") {
                    this.toggleLoader("Képek optimalizálása... Ez több percig is eltarthat.");
                }
            });
        });

        // Megerősítést igénylő űrlapok
        this.confirmForms.forEach(form => {
            const formSubmitter = form.querySelector("input[type=submit]");
            const formMessage = form.dataset.confirmMessage;
            
            form.addEventListener("submit", (e) => {
                if (this.isPopupVisible || this.isFormException(form) === false) {
                    return;
                }
                
                // Megakadályozzuk az automatikus leadást, és létrehozzuk az előugró ablakot
                e.preventDefault();
                
                const popup = this.createConfirmPopup(formMessage);
                
                // Megerősítés gomb kezelése
                popup.querySelector("input.confirm").addEventListener("click", () => {
                    this.closePopup(popup);
                    setTimeout(() => {
                        if (this.hasImage(form)) {
                            this.toggleLoader("Képek optimalizálása... Ez több percig is eltarthat.");
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
     */
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
                    console.log(`Hiba a kategória kereséskor: ${responseData.message}`);
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
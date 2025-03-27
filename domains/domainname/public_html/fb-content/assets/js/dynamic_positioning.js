/**
 * Dinamikus Elhelyezés Osztály
 * Elemek dinamikus újrapozicionálása egy adott elem jelenlétének függvényében
 */
class DynamicPositioning {
    /**
     * Konstruktor - inicializálja az osztályt a célelemmel és a dinamikusan pozícionálandó elemekkel
     * @param {Object} config - Konfigurációs beállítások
     * @param {HTMLElement|string} config.targetElement - Az elem, amit a többi elemnek el kell kerülnie (vagy CSS szelektora)
     * @param {Array<HTMLElement|string|Object>} config.elementsToReposition - Az elemek, amelyeket át kell pozícionálni
     * @param {string} config.visibilityTriggerClass - Az osztály, amely jelzi a célelelem láthatóságát (opcionális)
     * @param {number} config.requiredSpace - Szükséges tér az elemek között vízszintesen (pixelben)
     * @param {string} config.positionClass - A hozzáadandó pozicionáló osztály (opcionális)
     */
    constructor(config) {
        // Alapértelmezett beállítások
        this.config = {
            targetElement: null,
            elementsToReposition: [],
            visibilityTriggerClass: '',
            requiredSpace: 100,
            positionClass: 'dynamic-bottom-position',
            targetBottomOffset: 20,
            ...config
        };
        
        // Célelem kinyerése
        this.targetElement = typeof this.config.targetElement === 'string' 
            ? document.querySelector(this.config.targetElement)
            : this.config.targetElement;
        
        // Ellenőrzés, hogy a célelem létezik-e
        if (!this.targetElement) {
            console.warn('Dinamikus Elhelyezés: A célelem nem található!');
            return;
        }
        
        // Elemek feldolgozása újrapozicionáláshoz
        this.elementsToReposition = [];
        this.processElementsToReposition();
        
        // Eredeti pozíciók tárolása
        this.originalPositions = {};
        this.storeOriginalPositions();
        
        // Inicializálás
        this.init();
    }
    
    /**
     * Feldolgozza az újrapozicionálandó elemeket
     */
    processElementsToReposition() {
        if (!Array.isArray(this.config.elementsToReposition)) {
            console.warn('Dinamikus Elhelyezés: Az elementsToReposition paraméternek tömb típusúnak kell lennie!');
            return;
        }
        
        this.config.elementsToReposition.forEach(item => {
            let element, offset = 0;
            
            // Elem lehet: HTMLElement, szelektorstring vagy {element, offset} objektum
            if (typeof item === 'string') {
                element = document.querySelector(item);
            } else if (item instanceof HTMLElement) {
                element = item;
            } else if (typeof item === 'object' && item !== null) {
                element = typeof item.element === 'string' 
                    ? document.querySelector(item.element) 
                    : item.element;
                offset = item.offset || 0;
            }
            
            if (element) {
                this.elementsToReposition.push({ element, offset });
                element.classList.add(this.config.positionClass);
            }
        });
    }
    
    /**
     * Tárolja az elemek eredeti pozícióit
     */
    storeOriginalPositions() {
        this.elementsToReposition.forEach(({ element }) => {
            const id = element.id || 'element-' + Math.random().toString(36).substr(2, 9);
            this.originalPositions[id] = window.getComputedStyle(element).bottom;
        });
    }
    
    /**
     * Inicializálja a figyelőket és eseménykezelőket
     */
    init() {
        // ResizeObserver a célelem méretváltozásának figyeléséhez
        if (typeof ResizeObserver !== 'undefined') {
            this.resizeObserver = new ResizeObserver(this.updateElementPositions.bind(this));
            this.resizeObserver.observe(this.targetElement);
        }
        
        // MutationObserver a célelem DOM-változásainak figyeléséhez
        this.bodyObserver = new MutationObserver(this.handleBodyMutation.bind(this));
        this.bodyObserver.observe(document.body, { 
            attributes: true,
            childList: true,
            subtree: true
        });
        
        // Ablak átméretezésének figyelése
        window.addEventListener('resize', this.updateElementPositions.bind(this));
        setTimeout(() => {
            this.updateElementPositions();
        }, 1);
    }
    
    /**
     * Ellenőrzi, hogy az elemek elférnek-e egymás mellett
     * @returns {boolean} - Igaz, ha van elég hely az elemek egymás melletti elhelyezéséhez
     */
    canFitSideBySide() {
        if (!this.targetElement) return true;
        
        const targetRect = this.targetElement.getBoundingClientRect();
        const windowWidth = window.innerWidth;
        const targetRightEdge = targetRect.left + targetRect.width;
        
        // Ellenőrzi, hogy van-e elég hely jobb oldalon
        return windowWidth - targetRightEdge >= this.config.requiredSpace;
    }
    
    /**
     * Kezeli a body elem mutációit
     * @param {MutationRecord[]} mutations - A megfigyelt mutációk listája
     */
    handleBodyMutation(mutations) {
        let shouldUpdate = false;
        
        mutations.forEach(mutation => {
            // Osztályváltozás figyelése, ha van visibilityTriggerClass megadva
            if (this.config.visibilityTriggerClass && 
                mutation.type === 'attributes' && 
                mutation.attributeName === 'class' && 
                mutation.target === document.body) {
                shouldUpdate = true;
            }
        });
        
        if (shouldUpdate) {
            this.updateElementPositions();
        }
    }
    
    /**
     * Frissíti az elemek pozícióját a célelem alapján
     */
    updateElementPositions() {
        // Ellenőrizzük, hogy a célelem látható-e
        const isTargetVisible = this.isTargetVisible();
        
        if (!isTargetVisible || this.canFitSideBySide()) {
            // Célelem nem látható, vagy van elég hely egymás mellett
            // Visszaállítás eredeti pozíciókba
            this.elementsToReposition.forEach(({ element }) => {
                element.style.bottom = '';
            });
            return;
        }
        
        // Célelem méretének lekérése
        const targetRect = this.targetElement.getBoundingClientRect();
        const targetHeight = targetRect.height;
        const targetBottom = this.config.targetBottomOffset;
        
        // Új pozíció kiszámítása
        const newBottom = targetHeight + targetBottom;
        
        // Új pozíciók alkalmazása (csak a bottom tulajdonság változik)
        this.elementsToReposition.forEach(({ element, offset }) => {
            element.style.bottom = `${newBottom + offset}px`;
        });
    }
    
    /**
     * Ellenőrzi, hogy a célelem látható-e
     * @returns {boolean} - Igaz, ha a célelem látható
     */
    isTargetVisible() {
        if (!this.targetElement) return false;
        
        // Ha van trigger osztály, ellenőrizzük hogy aktív-e
        if (this.config.visibilityTriggerClass && 
            !document.body.classList.contains(this.config.visibilityTriggerClass)) {
            return false;
        }
        
        // Ellenőrizzük, hogy a célelem a DOM-ban van-e és látható-e
        const style = window.getComputedStyle(this.targetElement);
        return style.display !== 'none' && style.visibility !== 'hidden' && style.opacity !== '0';
    }
    
    /**
     * Külső hívásra manuálisan frissíti az elemek pozícióját
     */
    refresh() {
        this.updateElementPositions();
    }
    
    /**
     * Eseménykezelők és figyelők leállítása
     */
    destroy() {
        window.removeEventListener('resize', this.updateElementPositions.bind(this));
        
        if (this.resizeObserver) {
            this.resizeObserver.disconnect();
        }
        
        if (this.bodyObserver) {
            this.bodyObserver.disconnect();
        }
        
        // Elemek visszaállítása eredeti pozíciókba
        this.elementsToReposition.forEach(({ element }) => {
            element.style.bottom = '';
            element.classList.remove(this.config.positionClass);
        });
    }
}

// Példák az osztály használatára a DOM betöltése után
document.addEventListener('DOMContentLoaded', function() {
    // Süti értesítés és a hozzá igazodó gombok
    const cookieDynamicPositioning = new DynamicPositioning({
        targetElement: '.cookie-notification',
        elementsToReposition: [
            '#top-button',
            { element: '.floating-cart', offset: -3 } // 3px-el lejjebb a vizuális egyensúly miatt
        ],
        visibilityTriggerClass: 'cookienotice-shown',
        requiredSpace: 100
    });
});

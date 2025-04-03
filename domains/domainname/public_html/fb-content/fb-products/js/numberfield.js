import RateLimiter from "../../assets/js/ratelimit.js";

/**
 * Ellenőrzi a bevitelt, hogy érvényes szám legyen a min/max korlátokon belül
 * @param {HTMLInputElement} input - A beviteli elem
 */
export const validateInput = (input) => {
    input.value = input.value.replace(/[^0-9]/g, '');

    const value = parseInt(input.value) || 0;
    const min = parseInt(input.getAttribute('min')) || 1;
    const max = parseInt(input.getAttribute('max')) || Infinity;

    if (value < min || value > max || value === 0) {
        input.style.color = '#ff6b6b';
    } else {
        input.style.color = '';
    }
};

/**
 * Kezeli a fókuszváltást a számmezőkön, biztosítva, hogy az érték a tartományon belül maradjon
 * és opcionálisan meghívja az API callbacket
 * @param {HTMLInputElement} input - A beviteli elem
 * @param {Function|null} onCartDelta - Opcionális callback a kosár API frissítéséhez
 */
export const handleBlur = async (input, onCartDelta = null) => {
    const min = parseInt(input.getAttribute('min')) || 1;
    const max = parseInt(input.getAttribute('max')) || Infinity;
    const oldValue = parseInt(input.getAttribute('data-last-value')) || min;
    let newValue = parseInt(input.value) || min;

    if (newValue < min) newValue = min;
    if (newValue > max) newValue = max;
    
    input.value = newValue;
    input.style.color = '';

    const delta = newValue - oldValue;
    if (delta !== 0 && onCartDelta) {
        try {
            const success = await onCartDelta(delta, newValue, input);
            if (success === false) {
                // Ha hibás a változtatás, akkor visszaállítjuk az eredeti értékre
                input.value = oldValue;
                return;
            }
        } catch (err) {
            console.error('Sikertelen kosár frissítés:', err);
            input.value = oldValue;
            return;
        }
    }
    
    input.setAttribute('data-last-value', newValue);
};

/**
 * Megváltoztatja a mennyiséget egy delta értékkel, figyelembe véve a min/max korlátokat
 * @param {HTMLInputElement} input - A beviteli elem
 * @param {number} delta - Mennyiség változtatása (+1 vagy -1)
 * @param {Function|null} onCartDelta - Opcionális callback a kosár API frissítéséhez
 * @returns {Promise<boolean>} - Sikeres állapot
 */
export const handleQuantityChange = async (input, delta, onCartDelta = null) => {
    const min = parseInt(input.getAttribute('min')) || 1;
    const max = parseInt(input.getAttribute('max')) || Infinity;
    const oldValue = parseInt(input.value) || min;
    
    let newValue = oldValue + delta;
    if (newValue < min) newValue = min;
    if (newValue > max) newValue = max;
    
    // Ha az érték nem változott, nincs szükség folytatásra
    if (newValue === oldValue) return true;
    
    // Felhasználói felület azonnali frissítése
    input.value = newValue;
    
    // API hívás, ha meg van adva
    if (onCartDelta) {
        try {
            const success = await onCartDelta(delta, newValue, input);
            if (success === false) {
                // Eredeti érték visszaállítása, ha az API hívás sikertelen
                input.value = oldValue;
                return false;
            }
        } catch (err) {
            console.error('Sikertelen mennyiség frissítés:', err);
            input.value = oldValue;
            return false;
        }
    }
    
    input.setAttribute('data-last-value', newValue);
    return true;
};

/**
 * Beállítja a számmező kezelőszerveit a mennyiségi beviteli mezőkhöz
 * @param {Element|Document} container - A konténer elem, amiben keresni kell
 * @param {Function|null} onCartDelta - Opcionális callback a kosár API frissítéséhez
 */
export const setupNumberField = (container = document, onCartDelta = null) => {
    const limiter = new RateLimiter(
        {
            "change": 5
        }
    )
    const inputs = container.querySelectorAll('.product-quantity');
    inputs.forEach(input => {
        // Ha már be van állítva, akkor kihagyjuk
        if (input.hasAttribute('data-numberfield-initialized')) {
            return;
        }
        
        // Alapértelmezett érték beállítása
        const initialValue = input.value || '1';
        input.value = initialValue;
        input.setAttribute('data-last-value', initialValue);
        
        const numberField = input.closest('.number-field');
        if (!numberField) return;
        
        const addButton = numberField.querySelector('.number-field-add');
        const subtractButton = numberField.querySelector('.number-field-subtract');
        const stock = parseInt(input.getAttribute('max')) || 0;
        
        // Kezelőszervek letiltása, ha nincs készleten
        if (stock <= 0) {
            input.disabled = true;
            if (addButton) {
                addButton.style.opacity = '0.5';
                addButton.style.cursor = 'not-allowed';
            }
            if (subtractButton) {
                subtractButton.style.opacity = '0.5';
                subtractButton.style.cursor = 'not-allowed';
            }
            return;
        }
        
        // Eseménykezelők hozzáadása
        input.addEventListener('input', (e) => {
            const isLimited = limiter.useArea("change");
            if (isLimited) return;

            validateInput(e.target);
        });
        input.addEventListener('blur', (e) => {
            const isLimited = limiter.useArea("change");
            if (isLimited) return;

            handleBlur(e.target, onCartDelta);
        });

        if (addButton) {
            addButton.addEventListener('click', async () => {
                const isLimited = limiter.useArea("change");
                if (isLimited) return;

                await handleQuantityChange(input, 1, onCartDelta);
            });
        }

        if (subtractButton) {
            subtractButton.addEventListener('click', async () => {
                const isLimited = limiter.useArea("change");
                if (isLimited) return;

                await handleQuantityChange(input, -1, onCartDelta);
            });
        }
        
        // Jelöljük meg az inputot, hogy már be van állítva
        input.setAttribute('data-numberfield-initialized', 'true');
    });
};

/**
 * Eltávolítja az eseménykezelőket és inicializációs jelölést
 * @param {HTMLInputElement} input - A beviteli elem
 */
export const resetNumberField = (input) => {
    if (input.hasAttribute('data-numberfield-initialized')) {
        input.removeAttribute('data-numberfield-initialized');
        
        // Az eseménykezelőket közvetlenül nem tudjuk eltávolítani,
        // de ha újra inicializáljuk az elemet a setupNumberField függvénnyel,
        // akkor az ellenőrzi a data-numberfield-initialized attribútumot
    }
};

// Automatikus inicializálás, ha önálló szkriptként van betöltve
if (document.querySelector('script[src*="numberfield.js"]')) {
    document.addEventListener('DOMContentLoaded', () => setupNumberField());
}
/**
 * RateLimiter osztály
 * Különböző területek hívási gyakoriságának korlátozására használható.
 * Segítségével megakadályozható a túl gyakori API hívások vagy események kezelése.
 */
class RateLimiter {
    /**
     * RateLimiter osztály konstruktora
     * @param {Object} config - Konfigurációs objektum, ahol a kulcsok a területnevek,
     *                          az értékek pedig a megengedett hívások másodpercenkénti száma
     */
    constructor(config) {
        this.areas = [];
        this.debounceTimes = [];
        this.lastAccesses = [];

        for (const area in config) {
            const rate = config[area];

            this.debounceTimes.push(1000 / rate);
            this.areas.push(area);
            this.lastAccesses.push(-1);
        }
    }

    /**
     * Megkeresi egy terület indexét a belső tömbökben
     * @param {string} area - A keresett terület neve
     * @returns {number} A terület indexe
     * @throws {Error} Ha a megadott terület nem létezik
     */
    getAreaIndex(area) {
        if (!this.areas.includes(area)) {
            throw new Error("A ratelimiterben ismeretlen területnév található: " + area);
        }
        return this.areas.indexOf(area);
    }

    /**
     * Visszaadja egy terület utolsó hozzáférésének időpontját
     * @param {number} areaIndex - A terület indexe
     * @returns {number} Az utolsó hozzáférés időpontja, vagy -1 ha még nem volt hozzáférés
     */
    lastAccess(areaIndex) {
        return this.lastAccesses[areaIndex];
    }

    /**
     * Beállítja egy terület utolsó hozzáférésének időpontját
     * @param {number} areaIndex - A terület indexe
     * @param {number} time - Az időpont (performance.now() értéke)
     */
    setLastAccess(areaIndex, time) {
        this.lastAccesses[areaIndex] = time;
    }

    /**
     * Visszaadja egy terület késleltetési idejét
     * @param {number} areaIndex - A terület indexe
     * @returns {number} A késleltetési idő milliszekundumban
     */
    debounceTime(areaIndex) {
        return this.debounceTimes[areaIndex];
    }

    /**
     * Megpróbál hozzáférni egy területhez, ellenőrizve a korlátozásokat
     * @param {string} area - A használni kívánt terület neve
     * @returns {boolean} true, ha a hívás korlátozva van (túl gyakori), false ha engedélyezett
     */
    useArea(area) {        
        const areaId = this.getAreaIndex(area);
        const time = performance.now();

        if (this.lastAccess(areaId) < 0) {
            this.setLastAccess(areaId, time);
            return false;
        }
        
        if (time - this.lastAccess(areaId) >= this.debounceTime(areaId)) {
            this.setLastAccess(areaId, time);
            return false;
        }

        return true;
    }
}

export default RateLimiter;
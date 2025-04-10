import MobileDetector from "./mobiledetector.js";

/**
 * Egy osztály, amely lehetővé teszi az egérkurzorral történő vízszintes görgetést egy adott elemben, nem mobil eszközön.
 */
class CursorScroller {
    startLeft = 0;
    startX = 0;
    mouseDown = false;
    scrollElement = null;
    isMobile = MobileDetector.detect();

    /**
     * Létrehoz egy CursorScroller példányt.
     * Inicializálja az egérrel történő interakciók eseményfigyelőit a megadott görgethető elemhez.
     * 
     * @param {HTMLElement} scrollElement - Az elem, amelyhez az egérkurzor görgetési funkciót csatolja.
     * @throws {Error} Hibát dob, ha nincs megadva görgethető elem.
     */
    constructor(scrollElement) {
        if (!scrollElement) {
            throw new Error("CursorScroller: Nincs megadva elem");
        }

        if (this.isMobile) return;

        this.scrollElement = scrollElement;

        this.scrollElement.addEventListener("mousedown", this.handleMouseDown.bind(this));
        this.scrollElement.addEventListener("mouseup", this.handleMouseUp.bind(this));
        this.scrollElement.addEventListener("mousemove", this.handleMouseMove.bind(this));
        this.scrollElement.addEventListener("mouseleave", this.handleMouseUp.bind(this));
    }

    /**
     * Kezeli az egér lenyomásának eseményét a görgethető elemen.
     * Beállítja a húzás kezdeti állapotát, beleértve a kezdő X pozíciót
     * és az elem kezdeti görgetési pozícióját.
     *
     * @param {MouseEvent} e - Az esemény objektum, amely tartalmazza az esemény részleteit.
     */
    handleMouseDown(e) {
        this.mouseDown = true;
        this.startX = e.clientX;
        this.startLeft = this.scrollElement.scrollLeft;
    }

    /**
     * Kezeli az egér felengedésének eseményét azáltal, hogy a `mouseDown` tulajdonságot `false` értékre állítja.
     * Ez általában arra szolgál, hogy leállítsa az egérmozgás vagy interakciók követését,
     * amelyeket egy egér lenyomási esemény indított el.
     */
    handleMouseUp() {
        this.mouseDown = false;
    }

    /**
     * Kezeli az egér mozgatásának eseményét, hogy kiszámítsa a vízszintes távolságot,
     * és elindítja a görgetési animációt, ha az egér le van nyomva.
     *
     * @param {MouseEvent} e - Az egér esemény objektum, amely tartalmazza az egérmozgás részleteit.
     */
    handleMouseMove(e) {
        if (!this.mouseDown) return;

        let dx = e.clientX - this.startX;
        this.animateScroll(dx);
    }

    /**
     * A görgethető elem vízszintes görgetésének animálása.
     *
     * @param {number} dx - A görgetési távolság, ahol a végső görgetési pozíciót
     *                      `startLeft - dx / 2` képlettel számítjuk ki.
     */
    animateScroll(dx) {
        this.scrollElement.scrollTo({
            left: this.startLeft - dx / 2,
            behavior: "smooth"
        });
    }
}

export default CursorScroller;
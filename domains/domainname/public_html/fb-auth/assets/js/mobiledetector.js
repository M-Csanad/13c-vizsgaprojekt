class MobileDetector {
    static detect() {
        const cssMobile = getComputedStyle(document.body).getPropertyValue("--is-mobile") === "1";
        const touchDevice = ("ontouchstart" in window) || (navigator.msMaxTouchPoints > 0);
        return cssMobile ? cssMobile || touchDevice : touchDevice;
    }
}
export default MobileDetector;
/**
 * Süti Értesítés Komponens
 * Értesítést jelenít meg a webhely süti használatáról
 * Csak akkor jelenik meg, ha a felhasználó még nem fogadta el korábban
 */
class CookieNotification {
    constructor() {
        this.cookieName = 'read_cookie_notification';
        this.animationDuration = 0.5;
        this.easing = 'power3.inOut';
        
        // Csak akkor inicializálja, ha a süti nincs beállítva
        if (!this.hasCookieConsent()) {
            this.init();
        }
    }
    
    init() {
        
        // Értesítési elem létrehozása
        this.createNotificationElement();
        
        // Hozzáadás a DOM-hoz
        document.body.appendChild(this.element);
        
        // Eseményfigyelők hozzáadása
        this.bindEvents();
        
        // Megjelenítés animációval rövid késleltetés után
        setTimeout(() => {
            this.show();
        }, 1000);
    }
    
    createNotificationElement() {
        this.element = document.createElement('div');
        this.element.className = 'cookie-notification';

        this.element.innerHTML = `
            <div class="cookie-notification-container">
                <div class="cookie-icon">
                    <svg xmlns="http://www.w3.org/2000/svg" width="25" height="25" fill="currentColor" class="bi bi-cookie" viewBox="0 0 16 16">
                        <path d="M6 7.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0m4.5.5a1.5 1.5 0 1 0 0-3 1.5 1.5 0 0 0 0 3m-.5 3.5a1.5 1.5 0 1 1-3 0 1.5 1.5 0 0 1 3 0"/>
                        <path d="M8 0a7.96 7.96 0 0 0-4.075 1.114q-.245.102-.437.28A8 8 0 1 0 8 0m3.25 14.201a1.5 1.5 0 0 0-2.13.71A7 7 0 0 1 8 15a6.97 6.97 0 0 1-3.845-1.15 1.5 1.5 0 1 0-2.005-2.005A6.97 6.97 0 0 1 1 8c0-1.953.8-3.719 2.09-4.989a1.5 1.5 0 1 0 2.469-1.574A7 7 0 0 1 8 1c1.42 0 2.742.423 3.845 1.15a1.5 1.5 0 1 0 2.005 2.005A6.97 6.97 0 0 1 15 8c0 .596-.074 1.174-.214 1.727a1.5 1.5 0 1 0-1.025 2.25 7 7 0 0 1-2.51 2.224Z"/>
                    </svg>
                </div>
                <div class="cookie-message">
                    Az oldalunk csak a működéshez szükséges sütiket használ, hogy biztosítsuk a legjobb élményt.
                </div>
                <button class="cookie-accept">OK</button>
            </div>
        `;
        
        gsap.set(this.element, {
            y: '100%',
            opacity: 0,
            visibility: 'hidden'
        });
    }
    
    bindEvents() {
        const acceptButton = this.element.querySelector('.cookie-accept');
        acceptButton.addEventListener('click', () => {
            this.setCookieConsent();
            this.hide();
        });
    }
    
    show() {
        // CSS osztály hozzáadása a body elemhez az elrendezés beállításához
        document.body.classList.add('cookienotice-shown');
        
        gsap.set(this.element, { visibility: 'visible' });
        gsap.to(this.element, {
            y: '0%',
            opacity: 1,
            duration: this.animationDuration,
            ease: this.easing
        });
    }
    
    hide() {
        // CSS osztály eltávolítása a body elemről az értesítés elrejtésekor
        gsap.to(this.element, {
            y: '100%',
            opacity: 0,
            duration: this.animationDuration,
            ease: this.easing,
            onComplete: () => {
                document.body.classList.remove('cookienotice-shown');
                this.element.remove();
            }
        });
    }
    
    // Ellenőrzés, hogy a süti beleegyezés megtörtént-e
    hasCookieConsent() {
        return document.cookie.split(';').some(cookie => {
            return cookie.trim().startsWith(`${this.cookieName}=`);
        });
    }
    
    // Süti beállítása
    setCookieConsent() {
        // Süti lejáratának beállítása 1 évre
        const expirationDate = new Date();
        expirationDate.setFullYear(expirationDate.getFullYear() + 1);
        
        document.cookie = `${this.cookieName}=true; expires=${expirationDate.toUTCString()}; path=/; SameSite=Lax`;
    }
}

// Automatikus inicializálás, amikor a DOM betöltődött
document.addEventListener('DOMContentLoaded', () => {
    new CookieNotification();
});

export default CookieNotification;

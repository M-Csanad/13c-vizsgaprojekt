/**
 * Toast-stílusú értesítési komponens animált folyamatjelzővel
 * GSAP-et használ animációkhoz és többféle értesítési típust támogat
 */
class Notification {
    // Statikus tömb az aktív értesítések nyilvántartására
    static activeNotifications = [];
    static notificationGap = 10; // Távolság a halmozott értesítések között pixelben
    
    /**
     * Új értesítés létrehozása
     * @param {string} message - A megjelenítendő üzenet
     * @param {string} type - Értesítés típusa: 'success', 'error', 'warning', 'info'
     * @param {number} duration - Időtartam másodpercben az automatikus bezárásig (alapértelmezett: 5)
     */
    constructor(message, type = 'info', duration = 5) {
        // Függőségek ellenőrzése
        if (typeof gsap === 'undefined') {
            console.error('GSAP szükséges a Notification komponenshez');
            return;
        }
        
        this.message = message;
        this.type = type;
        this.duration = duration;
        this.element = null;
        this.progressBar = null;
        this.timeoutId = null;
        this.isClosing = false;
        this.height = 0; // Halmozási számításokhoz használva
        
        this.create();
    }
    
    /**
     * Az értesítés DOM elem létrehozása
     */
    create() {
        // Konténer létrehozása
        this.element = document.createElement('div');
        this.element.className = `notification notification-${this.type}`;
        
        // Ikon lekérése típus alapján
        const icon = this.getIconForType();
        
        // Tartalom létrehozása
        this.element.innerHTML = `
            <div class="notification-content">
                <div class="notification-icon">${icon}</div>
                <div class="notification-message">${this.message}</div>
                <button class="notification-close">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                        <path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/>
                    </svg>
                </button>
            </div>
            <div class="notification-progress-container">
                <div class="notification-progress"></div>
            </div>
        `;
        
        // Folyamatjelző elem lekérése
        this.progressBar = this.element.querySelector('.notification-progress');
        
        // Eseményfigyelők hozzáadása
        const closeButton = this.element.querySelector('.notification-close');
        closeButton.addEventListener('click', () => this.close());
        
        // Hozzáadás a dokumentumhoz
        document.body.appendChild(this.element);
    }
    
    /**
     * Az értesítés megjelenítése animációkkal
     */
    show() {
        // Meglévő értesítések gyors bezárása
        if (Notification.activeNotifications.length > 0) {
            Notification.activeNotifications.forEach(notification => {
                if (notification !== this && !notification.isClosing) {
                    notification.quickClose();
                }
            });
        }
        
        // Értesítés hozzáadása az aktív listához
        Notification.activeNotifications.push(this);
        
        // Kezdeti állapot
        gsap.set(this.element, { 
            y: '150%',
            x: '50%',
            opacity: 0,
            bottom: '20px',
            right: '50%'
        });
        
        // Értesítés magasságának lekérése (halmozási célokra)
        setTimeout(() => {
            this.height = this.element.offsetHeight;
        }, 10);
        
        // Belépési animáció
        gsap.to(this.element, {
            y: '0%',
            opacity: 1,
            duration: 0.5,
            ease: 'power2.out',
            onComplete: () => this.startProgressBar()
        });
        
        // Automatikus bezárási időzítő beállítása
        this.timeoutId = setTimeout(() => {
            if (!this.isClosing) this.close();
        }, this.duration * 1000);
        
        return this;
    }
    
    /**
     * Folyamatjelző animálása
     */
    startProgressBar() {
        gsap.to(this.progressBar, {
            width: '0%',
            duration: this.duration,
            ease: 'none'
        });
    }
    
    /**
     * Az értesítés bezárása normál animációkkal
     */
    close() {
        if (this.isClosing) return;
        
        this.isClosing = true;
        clearTimeout(this.timeoutId);
        
        // Eltávolítás az aktív értesítések tömbből
        const index = Notification.activeNotifications.indexOf(this);
        if (index > -1) {
            Notification.activeNotifications.splice(index, 1);
        }
        
        // Kilépési animáció
        gsap.to(this.element, {
            y: '150%',
            opacity: 0,
            duration: 0.5,
            ease: 'power2.in',
            onComplete: () => {
                this.element.remove();
            }
        });
    }
    
    /**
     * Az értesítés gyors bezárása (új értesítés megjelenésekor)
     */
    quickClose() {
        if (this.isClosing) return;
        
        this.isClosing = true;
        clearTimeout(this.timeoutId);
        
        // Eltávolítás az aktív értesítések tömbből
        const index = Notification.activeNotifications.indexOf(this);
        if (index > -1) {
            Notification.activeNotifications.splice(index, 1);
        }
        
        // Gyors kilépési animáció
        gsap.to(this.element, {
            y: '150%',
            opacity: 0,
            duration: 0.3,
            ease: 'power1.in',
            onComplete: () => {
                this.element.remove();
            }
        });
    }
    
    /**
     * Megfelelő ikon SVG lekérése az értesítés típusa alapján
     */
    getIconForType() {
        switch (this.type) {
            case 'success':
                return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                </svg>`;
            case 'error':
                return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8.982 1.566a1.13 1.13 0 0 0-1.96 0L.165 13.233c-.457.778.091 1.767.98 1.767h13.713c.889 0 1.438-.99.98-1.767L8.982 1.566zM8 5c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 5.995A.905.905 0 0 1 8 5zm.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2z"/>
                </svg>`;
            case 'warning':
                return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                </svg>`;
            case 'info':
            default:
                return `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16">
                    <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16zm.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2z"/>
                </svg>`;
        }
    }
    
    /**
     * Statikus segédfüggvény egy értesítés létrehozásához és megjelenítéséhez egy lépésben
     */
    static show(message, type = 'info', duration = 5) {
        return new Notification(message, type, duration).show();
    }
}

export default Notification;

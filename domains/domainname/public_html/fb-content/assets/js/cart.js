const randomId = () => "el-" + (Math.random() + 1).toString(36).substring(7);
const APIFetch = async (url, method, body=null) => {
    try {
        const params = {
            method: method,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        }

        if (body) params.body = JSON.stringify(body);

        const response = await fetch(url, params);

        if (response.ok) {
            return await response.json();
        }
        else {
            return response.status;
        }
    }
    catch (e) {
        return e;
    }
};

class Cart {
    isOpen = false;
    ease = "power2.inOut";
    ease2 = "power3.inOut";
    url = window.location.pathname;

    constructor() {
        // Fő filter ablak
        this.domElement = document.querySelector(".cart");
        if (!this.domElement) throw new Error("Nincs cart osztályú elem.");

        // Id és selector
        this.domElement.id = randomId();
        this.selector = `#${this.domElement.id}`;

        this.background = document.querySelector('.modal-background');
        if (!this.background) throw new Error("Nincs háttér.");

        // Kinyitó gomb
        this.openButton = document.querySelector(".cart-open");
        if (!this.openButton) throw new Error("Nincs kinyitó gomb.");

        // Bezáró gomb
        this.closeButton = this.domElement.querySelector(".cart-close");
        if (!this.closeButton) throw new Error("Nincs becsukó gomb.");

        this.cartAddButtons = document.querySelectorAll(".add-to-cart");

        // GSAP ellenőrzés
        if (!gsap) throw new Error("A GSAP nem található");

        if (!lenis) throw new Error("A Lenis nem található");

        // Eseménykezelés
        this.openButton.addEventListener("click", this.open.bind(this))
        this.closeButton.addEventListener("click", this.close.bind(this));
        this.cartAddButtons.forEach(button => button.addEventListener("click", this.add.bind(this)));
    }

    // UI metódusok
    open() {
        lenis.stop();
        gsap.set(this.domElement, { visibility: "visible"} );
        gsap.set(this.background, { visibility: "visible"} );
        gsap.to(this.domElement, {
            x: '0%',
            duration: 0.8,
            ease: this.ease2
        });
        gsap.to(this.background, {
            opacity: 1,
            duration: 0.8,
            ease: this.ease2
        });
    }

    close() {
        gsap.to(this.background, {
            opacity: 0,
            duration: 0.6,
            ease: this.ease, 
        });
        gsap.to(this.domElement, {
            x: '100%',
            duration: 0.6,
            ease: this.ease,
            onComplete: () => {
                lenis.start();
                gsap.set(this.domElement, { visibility: "hidden"} );
                gsap.set(this.background, { visibility: "hidden"} );
            }
        });
    }

    // Backend metódusok
    async add() {
        const result = await APIFetch("/api/cart/add", "POST", {url: this.url});

        console.log(result);
    }

    async remove() {

    }

    async changeCount() {

    }

    uploadToDB() {

    }

    writeToCookie() {

    }

    loadFromCookie() {

    }
}

export default Cart;
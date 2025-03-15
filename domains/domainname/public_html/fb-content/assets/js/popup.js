const randomId = () => "el-" + (Math.random() + 1).toString(36).substring(7);
class Popup {
    visible = false;
    ease = "power3.inOut";

    constructor(title, message, onReply, buttonText = "Összevonás") {
        if (!gsap) throw new Error("A GSAP nem található");
        if (!lenis) throw new Error("A Lenis nem található");

        if (typeof onReply != 'function') throw new Error("Kérlek egy függvényt adj meg");
        this.onReply = onReply;

        this.domElement = document.createElement("div");
        this.domElement.id = randomId();
        this.domElement.className = "popup";
        this.domElement.innerHTML = `
        <div class="popup-body">
            <div class='popup-icon'>
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-info-circle-fill" viewBox="0 0 16 16">
                <path d="M8 16A8 8 0 1 0 8 0a8 8 0 0 0 0 16m.93-9.412-1 4.705c-.07.34.029.533.304.533.194 0 .487-.07.686-.246l-.088.416c-.287.346-.92.598-1.465.598-.703 0-1.002-.422-.808-1.319l.738-3.468c.064-.293.006-.399-.287-.47l-.451-.081.082-.381 2.29-.287zM8 5.5a1 1 0 1 1 0-2 1 1 0 0 1 0 2"/>
            </svg>
            </div>
            <h2 class='popup-message'>${title}</h2>
            <div class='popup-description'>${message}</div>
            <div class='button-group-wrapper'>
            <div class='button-group'>
                <button class='confirm'>
                    ${buttonText}
                </button>
                <button class='cancel'>
                    Mégse
                </button>
            </div>
            </div>
        </div>
        `;
        document.body.appendChild(this.domElement);

        this.popupBody = this.domElement.querySelector(".popup-body");
        this.okButton = this.popupBody.querySelector(".confirm");
        this.noButton = this.popupBody.querySelector(".cancel");

        // Eseménykezelés
        this.okButton.addEventListener("click", this.confirm.bind(this));
        this.noButton.addEventListener("click", this.decline.bind(this));

        this.keyListener = window.addEventListener("keydown", async (e) => {
            if (document.body.contains(this.domElement) && e.code == "Escape") {
                await this.onReply(false);
                this.close();
            }
        });
    }

    async confirm() {
        await this.onReply(true);
        this.close();
    }

    async decline() {
        await this.onReply(false);
        this.close();
    }

    open() {
        if (this.visible) return;
        this.visible = true;
        lenis.stop();

        gsap.set(this.domElement, { visibility: "visible" });
        gsap.to(this.domElement, {
            opacity: 1,
            duration: 0.5,
            ease: this.ease
        });
        gsap.to(this.popupBody, {
            scale: 1,
            duration: 0.5,
            ease: this.ease
        });
    }

    close() {
        if (!this.visible) return;
        this.visible = false;

        gsap.to(this.domElement, {
            opacity: 0,
            duration: 0.5,
            ease: this.ease
        });
        gsap.to(this.popupBody, {
            scale: 0,
            duration: 0.5,
            ease: this.ease,
            onComplete: () => {
                gsap.set(this.domElement, { visibility: "hidden" });
                lenis.start();
                this.remove();
            }
        });
    }

    remove() {
        this.domElement.remove();
        window.removeEventListener("keydown", this.keyListener);
    }
}

export default Popup;

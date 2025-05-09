import APIFetch from "/fb-content/assets/js/apifetch.js";

class LoginForm {
    submitted = false;
    currentImageIndex = 0;
    isErrorMessageVisible = false;

    constructor(dom) {
        if (!dom) throw new Error("Nincs megadva űrlap.");
        if (dom.nodeName != "FORM") throw new Error("Nem űrlap típusú a megadott elem.");
        if (!gsap) throw new Error("GSAP nem található");
        
        this.initDOM(dom);
        this.bindEvents();
        this.startImageCycle();
    }

    initDOM(dom) {
        this.formDom = dom;
        this.formMessage = this.formDom.querySelector(".form-message");
        this.backgroundImages = document.querySelectorAll('.bg');
        this.loader = this.formDom.querySelector(".loader");
        
        this.form = {
            "username": {
                "dom": this.formDom.querySelector("#username"),
                "errorMessage": "Kérjük ne hagyja üresen a felhasználónév mezőt",
                get value() { return this.dom?.value }
            },
            "passwd": {
                "dom": this.formDom.querySelector("#passwd"),
                "errorMessage": "Kérjük ne hagyja üresen a jelszó mezőt",
                get value() { return this.dom?.value }
            }
        }

        this.validationRules = {
            username: value => value && value.length > 0,
            passwd: value => value && value.length > 0
        }

        this.submitter = this.formDom.querySelector(".action-button");
    }

    bindEvents() {
        this.submitter.addEventListener("click", this.send.bind(this));
        
        document.addEventListener("keypress", (event) => {
            if (event.key === "Enter") {
                event.preventDefault();
                this.send(event);
            }
        });
        
        this.formDom.querySelectorAll(".input-group").forEach(e => this.handleInputGroupFocus(e));

        this.formDom.querySelectorAll("input[type='password']").forEach(e => {
            e.parentElement.querySelector(".show-password").addEventListener("click", () => {
                const input = this.form.passwd.dom;
                const type = input.getAttribute("type") === "password" ? "text" : "password";

                input.classList.toggle("shown");
                input.setAttribute("type", type);
            });
        });

        for (let field in this.form) {
            const element = this.form[field];
            if (element.noValidate) continue;

            element.dom.addEventListener("change", () => {
                const error = this.validateField(field);
                this.toggleFieldState(field, error);
            });
        }
    }

    handleInputGroupFocus(e) {
        const inputGroup = e;
        const label = inputGroup.querySelector('label');
        const input = inputGroup.querySelector('input');

        input.addEventListener("focus", () => {
            label.classList.add('focus');
        });

        input.addEventListener("focusout", () => {
            if (input.value === "") label.classList.remove('focus');
        });
    }

    validateField(name) {
        const field = this.form[name];
        if (!field) return false;

        const validator = this.validationRules[name];
        const value = field.value;
        const error = field.errorMessage;

        if (!validator) return null;
        if (!value) return error;

        return validator(value) ? null : error;
    }

    validateForm() {
        let valid = true;
        for (let field in this.form) {
            if (this.form[field].noValidate) continue;

            const error = this.validateField(field);
            this.toggleFieldState(field, error);
            if (error) valid = false;
        }
        return valid;
    }

    toggleFieldState(name, error) {
        const field = this.form[name];
        if (!field) return;

        const errorWrapper = field.dom.closest('.input-group').querySelector('.message-wrapper');
        const messageContainer = errorWrapper.querySelector(".error-message");

        field.dom.classList.toggle('valid', !error);
        field.dom.classList.toggle('invalid', !!error);
        
        if (error) {
            messageContainer.innerHTML = error;
            gsap.set(errorWrapper, {visibility: "visible"});
            gsap.to(errorWrapper, {
                height: "auto",
                opacity: 1,
                ease: "power2.inOut",
                duration: 0.3
            });
        } else {
            gsap.to(errorWrapper, {
                height: 0,
                opacity: 0,
                ease: "power2.inOut",
                duration: 0.3,
                onComplete: () => {
                    gsap.set(errorWrapper, {visibility: "hidden"});
                }
            });
        }
    }

    shakeElement(element) {
        const ease = "power1.inOut";
        
        gsap.fromTo(
            element,
            { x: 0 },
            {
                x: "+=5",
                duration: 0.1,
                repeat: 7,
                yoyo: true,
                ease: ease,
            }
        );
    }

    async animateElementIn(element) {
        gsap.set(element, { opacity: 0, scale: 0 });
        await gsap.to(element, {
            opacity: 1,
            scale: 1,
            duration: 0.3,
            ease: "power1.inOut"
        });
    }

    async removeElementContent(element) {
        return new Promise((resolve) => {
            gsap.to(element, {
                opacity: 0,
                scale: 0,
                duration: 0.3,
                ease: "power1.inOut",
                onComplete: () => {
                    element.innerHTML = "";
                    resolve();
                }
            });
        });
    }

    async executeRecaptcha(action = 'login') {
        return new Promise((resolve) => {
            grecaptcha.enterprise.ready(() => {
                grecaptcha.enterprise.execute('6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj', { action: action })
                    .then(token => {
                        document.getElementById('g-recaptcha-response').value = token;
                        resolve();
                    });
            });
        });
    }

    cycleImages() {
        if (document.body.clientWidth <= 900) return;
        this.backgroundImages[this.currentImageIndex].classList.remove('visible');
        this.currentImageIndex = (this.currentImageIndex + 1) % this.backgroundImages.length;
        this.backgroundImages[this.currentImageIndex].classList.add('visible');
    }

    startImageCycle() {
        setInterval(() => this.cycleImages(), 5000);
    }

    async send(event) {
        if (this.submitted) return;
        event.preventDefault();
    
        if (!this.validateForm()) return;
        
        try {
            if (this.formMessage.innerHTML) {
                await this.removeElementContent(this.formMessage);
            }

            this.loader.classList.remove('hidden');

            await this.executeRecaptcha();
            this.submitted = true;
            
            const data = new FormData(this.formDom);
            data.append("login", "1");

            const response = await APIFetch("/api/auth/login", "POST", data, false);
            const result = await response.json();

            this.loader.classList.add('hidden');

            if (response.ok) {
                const outParams = {
                    scaleY: 1, 
                    duration: 1,
                    stagger: {
                        each: 0.05,
                        from: "start",
                        grid: "auto",
                        axis: "x"
                    },
                    ease: "power4.inOut"
                };
    
                await animatePageTransition(outParams);
                window.location.href = "./";
            } else {
                this.submitted = false;
                this.formMessage.innerHTML = result.message;
                this.formMessage.classList.remove('message-success');
                this.formMessage.classList.add('message-error');
                
                await this.animateElementIn(this.formMessage);
                this.shakeElement(this.formMessage);
                
                const input = this.form.passwd.dom;
                if (input) input.value = "";
            }
        } catch (error) {
            console.error('Hiba a bejelentkezéskor: ', error);
            this.submitted = false;
            
            if (this.formMessage.innerHTML) {
                await this.removeElementContent(this.formMessage);
            }
            this.formMessage.innerHTML = "Váratlan hiba történt. Kérjük próbálja újra később.";
            this.formMessage.classList.remove('message-success');
            this.formMessage.classList.add('message-error');
            
            await this.animateElementIn(this.formMessage);
            this.shakeElement(this.formMessage);
        }
    }
}

window.addEventListener("DOMContentLoaded", () => {
    const loginForm = new LoginForm(document.getElementById("login"));
});
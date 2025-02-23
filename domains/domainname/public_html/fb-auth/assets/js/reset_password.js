import APIFetch from "/fb-content/assets/js/apifetch.js";

class ResetPasswordForm {
    currentImageIndex = 0;

    constructor(dom) {
        if (!dom) throw new Error("Nincs megadva űrlap.");
        if (dom.nodeName != "FORM") throw new Error("Nem űrlap típusú a megadott elem.");
        if (!gsap) throw new Error("GSAP nem található");
        
        this.formType = dom.dataset.type;
        this.initDOM(dom);
        this.bindEvents();
        this.startImageCycle();
    }

    initDOM(dom) {
        this.formDom = dom;
        this.formMessage = this.formDom.querySelector(".form-message");
        this.loader = this.formDom.querySelector(".loader");
        this.backgroundImages = document.querySelectorAll('.bg');
        
        if (this.formType === 'request') {
            this.form = {
                "email": {
                    "dom": this.formDom.querySelector("#email"),
                    "errorMessage": "Kérjük adjon meg egy érvényes email címet",
                    get value() { return this.dom?.value }
                }
            };
            this.validationRules = {
                email: value => value && value.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)
            };
        } else {
            this.passwordHelperRules = {
                charlen: /^.{8,64}$/,
                haslower: /[a-záéöüóőúí]/,
                hasupper: /[A-ZÁÉÖÜÓŐÚÍ]/,
                hasdigit: /\d/,
                hasspecial: /[!@#$%^&*()_\-+=\[\]{}|\\;:'",.<>/?~]/,
            }

            this.form = {
                "password": {
                    "dom": this.formDom.querySelector("#password"),
                    "errorMessage": "A jelszó nem felel meg a követelményeknek",
                    get value() { return this.dom?.value }
                },
                "password_confirm": {
                    "dom": this.formDom.querySelector("#password_confirm"),
                    "errorMessage": "A jelszavak nem egyeznek",
                    get value() { return this.dom?.value }
                }
            };
            this.validationRules = {
                password: value => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/.test(value),
                password_confirm: value => value === this.form.password.value
            };
        }

        this.submitter = this.formDom.querySelector(".action-button");
    }

    bindEvents() {
        this.submitter.addEventListener("click", this.send.bind(this));
        
        this.formDom.querySelectorAll(".input-group").forEach(e => this.handleInputGroupFocus(e));

        for (let field in this.form) {
            const element = this.form[field];
            if (element.noValidate) continue;

            element.dom.addEventListener("change", () => {
                const error = this.validateField(field);
                this.toggleFieldState(field, error);
            });
        }

        if (this.formType !== 'request') {
            this.form.password.dom.addEventListener("input", () => this.updatePasswordHelper(this.form.password));
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

    async executeRecaptcha(action = 'resetpassword') {
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

    async animateElementIn(element) {
        gsap.set(element, { opacity: 0, scale: 0 });
        await gsap.to(element, {
            opacity: 1,
            scale: 1,
            duration: 0.3,
            ease: "power1.inOut"
        });
    }

    async send(event) {
        event.preventDefault();
        if (!this.validateForm()) return;
        
        try {
            if (this.formMessage.innerHTML) {
                await this.removeElementContent(this.formMessage);
            }

            this.loader.classList.remove('hidden');
            await this.executeRecaptcha();
            
            const data = new FormData(this.formDom);
            const method = this.formType === 'request' ? 'POST' : 'PUT';

            const response = await APIFetch("/api/auth/resetpassword", method, data, this.formType !== 'request');
            const result = await response.json();

            this.loader.classList.add('hidden');

            if (response.ok) {
                this.formMessage.innerHTML = result.message;

                this.formMessage.classList.remove('message-error');
                this.formMessage.classList.add('message-success');

                await this.animateElementIn(this.formMessage);
                
                if (this.formType !== 'request') {
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 5000);
                }
            } else {
                this.formMessage.innerHTML = result.message;

                this.formMessage.classList.remove('message-success');
                this.formMessage.classList.add('message-error');

                await this.animateElementIn(this.formMessage);
                this.shakeElement(this.formMessage);
                
                if (this.formType !== 'request') {
                    this.formDom.querySelectorAll("input[type=password]").forEach(e => e.value = "");
                }
            }
        } catch (error) {
            console.error('Hiba történt:', error);
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

    updatePasswordHelper(element) {
        const value = element.dom.value;
        const passwordHelperDom = this.formDom.querySelector(".password-state");
        
        for (let key in this.passwordHelperRules) {
            const rule = this.passwordHelperRules[key];
            const matcherDom = passwordHelperDom.querySelector(`[data-for=${key}]`);

            if (rule.test(value)) {
                matcherDom.classList.add("valid");
                matcherDom.classList.remove("invalid");
            }
            else {
                matcherDom.classList.remove("valid");
                matcherDom.classList.add("invalid");
            }
        }
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
}

window.addEventListener("DOMContentLoaded", () => {
    const resetForm = new ResetPasswordForm(document.getElementById("resetPassword"));
});

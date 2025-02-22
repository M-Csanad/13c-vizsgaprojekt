import APIFetch from "/fb-content/assets/js/apifetch.js";

class ResetPasswordForm {
    constructor(dom) {
        if (!dom) throw new Error("Nincs megadva űrlap.");
        if (dom.nodeName != "FORM") throw new Error("Nem űrlap típusú a megadott elem.");
        if (!gsap) throw new Error("GSAP nem található");
        
        this.formType = dom.dataset.type;
        this.initDOM(dom);
        this.bindEvents();
    }

    initDOM(dom) {
        this.formDom = dom;
        this.formMessage = this.formDom.querySelector(".form-message");
        this.loader = this.formDom.querySelector(".loader");
        
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
            this.form = {
                "password": {
                    "dom": this.formDom.querySelector("#password"),
                    "errorMessage": "A jelszónak legalább 8 karaktert kell tartalmaznia",
                    get value() { return this.dom?.value }
                },
                "password_confirm": {
                    "dom": this.formDom.querySelector("#password_confirm"),
                    "errorMessage": "A jelszavak nem egyeznek",
                    get value() { return this.dom?.value }
                }
            };
            this.validationRules = {
                password: value => value && value.length >= 8,
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

    async send(event) {
        event.preventDefault();
        if (!this.validateForm()) return;
        
        try {
            this.loader.classList.remove('hidden');
            await this.executeRecaptcha();
            
            const data = new FormData(this.formDom);
            const method = this.formType === 'request' ? 'POST' : 'PUT';
            const body = this.formType === 'request' ? data : JSON.stringify(Object.fromEntries(data));

            const response = await APIFetch("/api/auth/resetpassword", method, body, false);
            const result = await response.json();

            if (response.ok) {
                this.formMessage.innerHTML = result.message;
                this.formMessage.classList.remove('message-error');
                this.formMessage.classList.add('message-success');
                
                if (this.formType !== 'request') {
                    setTimeout(() => {
                        window.location.href = '/login';
                    }, 5000);
                }
            } else {
                this.formMessage.innerHTML = result.error;
                this.formMessage.classList.remove('message-success');
                this.formMessage.classList.add('message-error');
            }
        } catch (error) {
            console.error('Hiba történt:', error);
            this.formMessage.innerHTML = "Váratlan hiba történt. Kérjük próbálja újra később.";
            this.formMessage.classList.remove('message-success');
            this.formMessage.classList.add('message-error');
        } finally {
            this.loader.classList.remove('hidden');
        }
    }
}

window.addEventListener("DOMContentLoaded", () => {
    const resetForm = new ResetPasswordForm(document.getElementById("resetPassword"));
});

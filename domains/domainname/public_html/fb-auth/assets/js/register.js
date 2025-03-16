import APIFetch from "/fb-content/assets/js/apifetch.js";

class RegisterForm {
    submitted = false;
    currentImageIndex = 0;

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
        this.submitter = this.formDom.querySelector(".action-button");
        
        this.form = {
            "email": {
                "dom": this.formDom.querySelector("#email"),
                "errorMessage": "Kérjük adjon meg egy érvényes email címet",
                get value() { return this.dom?.value }
            },
            "username": {
                "dom": this.formDom.querySelector("#username"),
                "errorMessage": "A felhasználónév 3-20 karakter hosszú lehet és csak betűket, számokat és kötőjelet tartalmazhat",
                get value() { return this.dom?.value }
            },
            "firstName": {
                "dom": this.formDom.querySelector("#firstname"),
                "errorMessage": "Kérjük adja meg a keresztnevét",
                get value() { return this.dom?.value }
            },
            "lastName": {
                "dom": this.formDom.querySelector("#lastname"),
                "errorMessage": "Kérjük adja meg a vezetéknevét",
                get value() { return this.dom?.value }
            },
            "password": {
                "dom": this.formDom.querySelector("#password"),
                "errorMessage": "A jelszó nem felel meg a követelményeknek",
                get value() { return this.dom?.value }
            },
            "passwordConfirm": {
                "dom": this.formDom.querySelector("#passwordConfirm"),
                "errorMessage": "A két jelszó nem egyezik meg",
                get value() { return this.dom?.value }
            },
            "terms": {
                "dom": this.formDom.querySelector("#agree"),
                "noCustomValidate": true,
                "showDefaultValidity": true,
                "errorMessage": "Kérjük fogadja el az ÁSZF-et",
                get value() { return this.dom?.checked }
            }
        }

        this.passwordHelperRules = {
            charlen: /^.{8,64}$/,
            haslower: /[a-záéöüóőúí]/,
            hasupper: /[A-ZÁÉÖÜÓŐÚÍ]/,
            hasdigit: /\d/,
            hasspecial: /[!@#$%^&*()_\-+=\[\]{}|\\;:'",.<>/?~]/,
        }

        this.validationRules = {
            email: value => /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/.test(value),
            username: value => /^[\w-]{3,20}$/.test(value),
            firstName: value => value && value.length > 0,
            lastName: value => value && value.length > 0,
            password: value => /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/.test(value),
            passwordConfirm: value => value === this.form.password.value
        }
    }

    bindEvents() {
        this.submitter.addEventListener("click", this.send.bind(this));
        
        this.formDom.querySelectorAll(".input-group").forEach(e => this.handleInputGroupFocus(e));

        this.formDom.querySelectorAll("input[type='password']").forEach(e => {
            e.parentElement.querySelector(".show-password").addEventListener("click", (e) => {
                const input = e.target.closest(".input-group").querySelector("input");
                const type = input.getAttribute("type") === "password" ? "text" : "password";

                input.classList.toggle("shown");
                input.setAttribute("type", type);
            });
        });

        for (let field in this.form) {
            const element = this.form[field];

            if (element.noCustomValidate) continue;

            element.dom.addEventListener("change", () => {
                const error = this.validateField(field);
                this.toggleFieldState(field, error);
            });

            if (field === "password") {
                element.dom.addEventListener("input", () => this.updatePasswordHelper(element));
            }
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
        for (let fieldKey in this.form) {
            const field = this.form[fieldKey];
            if (field.noCustomValidate) {
                valid = field.value;
                if (!valid) field.dom.reportValidity();
                continue;
            }

            const error = this.validateField(fieldKey);
            this.toggleFieldState(fieldKey, error);
            if (error) valid = false;
        }
        return valid;
    }

    toggleFieldState(name, error) {
        const field = this.form[name];
        if (!field) return;

        if (field.showDefaultValidity) {
            field.dom.setCustomValidity(error ? "invalid" : "");
            field.dom.reportValidity();
            return;
        }

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

    async executeRecaptcha() {
        return new Promise((resolve) => {
            grecaptcha.enterprise.ready(() => {
                grecaptcha.enterprise.execute('6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj', { action: 'register' })
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

    updatePasswordHelper(element) {
        const value = element.value;
        console.log(value)
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
            data.append("register", "1");

            const response = await APIFetch("/api/auth/register", "POST", data, false);
            const result = await response.json();

            this.loader.classList.add('hidden');

            if (result.type === "SUCCESS") {
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
                window.location.href = "./login";
            } else {
                this.submitted = false;
                this.formMessage.innerHTML = result.message;
                this.formMessage.innerHTML = result.message;
                this.formMessage.classList.remove('message-success');
                this.formMessage.classList.add('message-error');
                
                await this.animateElementIn(this.formMessage);
                this.shakeElement(this.formMessage);
                
                this.formDom.querySelectorAll("input[type=password]").forEach(e => e.value = "");
                this.updatePasswordHelper(this.form.password.dom);
            }
        } catch (error) {
            console.error('Hiba regisztráláskor: ', error);
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
    const registerForm = new RegisterForm(document.getElementById("register"));
});
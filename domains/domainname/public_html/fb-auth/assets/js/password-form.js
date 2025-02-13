import APIFetch from "/fb-content/assets/js/apifetch.js";

class PasswordForm {
    actionTimeout = 10000;
    sent = false;
    isErrorMessageVisible = false;
    lastSubmissionTime = 0;

    constructor(dom) {
        if (!dom) throw new Error("Nincs megadva űrlap.");
        if (dom.nodeName != "FORM") throw new Error("Nem űrlap típusú a megadott elem.");
        if (!gsap) throw new Error("GSAP nem található");

        this.initDOM(dom);
        this.bindEvents();
    }

    initDOM(dom) {
        this.formDom = dom;
        this.form = {
            "oldPass": {
                "dom": this.formDom.querySelector("[name=old-pass]"),
                "errorMessage": "Kérem megfelelő formátumú jelszót adjon meg",
                get value() { return this.dom?.value },
                set value(val) { this.dom.value = val }
            },
            "newPass": {
                "dom": this.formDom.querySelector("[name=new-pass]"),
                "passwordHelperDom": null, // bindEvents-ben beállítjuk
                "hasPasswordFormatHelper": true,
                "errorMessage": "Kérem megfelelő formátumú jelszót adjon meg",
                "errorMessagePasswordsMatch": "Az új jelszó nem egyezhet a régi jelszóval.",
                get value() { return this.dom?.value },
                set value(val) { this.dom.value = val }
            },
            "newPassConfirm": {
                "dom": this.formDom.querySelector("[name=new-pass-confirm]"),
                "hasAdditionalRule": true,
                "errorMessage": "Kérem megfelelő formátumú jelszót adjon meg",
                "errorMessagePasswordsMatch": "A két jelszó nem egyezik meg.",
                get value() { return this.dom?.value },
                set value(val) { this.dom.value = val }
            }
        }

        this.validationRules = {
            oldPass:        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/,
            newPass:        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/,
            newPassConfirm: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/,
            passwordsMatch: (pass, pass2) => pass === pass2
        }

        this.passwordHelperRules = {
            charlen: /^.{8,64}$/,
            haslower: /[a-záéöüóőúí]/,
            hasupper: /[A-ZÁÉÖÜÓŐÚÍ]/,
            hasdigit: /\d/,
            hasspecial: /[!@#$%^&*()_\-+=\[\]{}|\\;:'",.<>/?~]/,
        }

        this.submitter = this.formDom.querySelector('.action-button');
        this.messageContainer = this.formDom.querySelector(".message-container");
    }

    bindEvents() {
        this.submitter.addEventListener("click", this.send.bind(this))

        // Beviteli mező fókusz eseményei
        document.querySelectorAll(".input-group").forEach((e) => this.handleInputGroupFocus(e));

        // Beviteli mezők validálási eseményei
        for (let field in this.form) {
            let element = this.form[field];
            if (element.noValidate) continue;

            if (element.hasPasswordFormatHelper) {
                this.form[field].passwordHelperDom = element.dom.closest('.password-input')?.querySelector(".password-state");
                element.dom.addEventListener("input", () => this.updatePasswordHelper(element));
            }

            element.dom.addEventListener("change", () => {
                const error = this.validateField(field);
                this.toggleFieldState(field, error);
            });
        }
    }

    handleInputGroupFocus(e) {
        const inputGroup = e;
        const label = inputGroup.querySelector('label');
        const input = inputGroup.querySelector('input, select');

        input.addEventListener("focus", () => {
            if (input.nodeName !== "SELECT") label.classList.add('focus');
        });

        input.addEventListener("focusout", () => {
            if (input.value === "") label.classList.remove('focus');
        });
    }

    updatePasswordHelper(element) {
        const value = element.value;
        
        for (let key in this.passwordHelperRules) {
            const rule = this.passwordHelperRules[key];
            const matcherDom = element.passwordHelperDom.querySelector(`[data-for=${key}]`);

            if (rule.test(value)) {
                matcherDom.classList.add("valid");
            }
            else {
                matcherDom.classList.remove("valid");
            }
        }
    }

    async updateMessage(message, type) {
        if (this.isErrorMessageVisible) await this.hideMessage();

        const messageDom = document.createElement("div");
        messageDom.className = "message " + type.toLowerCase()
        messageDom.innerText = message;

        this.messageContainer.innerHTML = "";
        this.messageContainer.appendChild(messageDom);

        gsap.set(this.messageContainer, {visibility: "visible"});
        gsap.to(this.messageContainer, {
            height: "auto",
            duration: 0.5,
            ease: "power3.inOut"
        });
        gsap.to(messageDom, {
            opacity: 1,
            duration: 0.5,
            ease: "power3.inOut"
        });

        this.isErrorMessageVisible = true;
    }

    hideMessage() {
        return new Promise((resolve) => {
            gsap.set(this.messageContainer, {visibility: "visible"});
            gsap.to(this.messageContainer, {
                height: 0,
                duration: 0.5,
                ease: "power3.inOut"
            });
            gsap.to(this.messageContainer.querySelector('.message'), {
                opacity: 0,
                duration: 0.5,
                ease: "power3.inOut",
                onComplete: resolve
            });
        })
    }

    // Beviteli mező validálása
    validateField(name) {
        // A mező lekérdezése a paraméterek alapján
        const field = this.form[name] || undefined;
        if (!field) return false;

        // Validátor függvény, Aktuális érték
        const validator = this.validationRules[name] || undefined;
        const value = field.value;
        const errorMessage = field.errorMessage;
        let error = null;

        // Változók ellenőrzése
        if (!validator) return null;
        if (!value) return errorMessage;

        // Érték validálása
        if (typeof validator === 'function') {
            error = validator(value) ? null : errorMessage;
        } else if (validator instanceof RegExp) {
            error = validator.test(value) ? null : errorMessage;
        }

        // Régi jelszó és új jelszó egyezésének megvizsgálása
        if (name === 'newPass' && !error) {
            const oldPassValue = this.form.oldPass.value;
            if (this.validationRules.passwordsMatch(oldPassValue, value)) {
                error = field.errorMessagePasswordsMatch;
            }
        }
    
        // Új jelszó és a megerősítés egyezésének megvizsgálása
        if (name === 'newPassConfirm' && !error) {
            const newPassValue = this.form.newPass.value;
            if (!this.validationRules.passwordsMatch(newPassValue, value)) {
                error = field.errorMessagePasswordsMatch;
            }
        }
    
        return error;
    }

    // Form validáció 
    validateForm() {
        let valid = true;
        for (let field in this.form) {
            let element = this.form[field];
            if (element.noValidate) continue;

            const error = this.validateField(field);
            this.toggleFieldState(field, error);

            if (error) valid = false;
        }
        return valid;
    }

    toggleFieldState(name, error) {
        // A mező lekérdezése a paraméterek alapján
        const field = this.form[name] || undefined;
        if (!field) return false;

        const errorWrapper = field.dom.closest('.input-group').querySelector('.message-wrapper');
        const messageContainer = errorWrapper.querySelector(".error-message");
        const validity = error ? "invalid" : "valid";
        const oppositeValidity = error ? "valid" : "invalid";
        const didValidityChange = field.dom.classList.contains(oppositeValidity) || field.dom.classList.length == 0;
        const didErrorMessageChange = messageContainer.innerHTML === error;

        if (!didValidityChange && didErrorMessageChange) return;

        field.dom.classList.add(validity);
        field.dom.classList.remove(oppositeValidity);
        
        if (error) {
            messageContainer.innerHTML = error;

            gsap.set(errorWrapper, {visibility: "visible"});
            gsap.to(errorWrapper, {
                height: 21,
                opacity: 1,
                ease: "power2.inOut",
                duration: 0.3
            });
        }
        else {
            gsap.to(errorWrapper, {
                height: 0,
                opacity: 0,
                ease: "power2.inOut",
                duration: 0.3,
                onComplete: () => {
                    gsap.set(errorWrapper, {visibility: "hidden"});
                }
            })
        }
    }


    getFormData(json = false, additional = null) {
        const data = new FormData(this.formDom);

        if (additional) {
            for (const [key, value] of Object.entries(additional)) {
                data.append(key, value);
            }              
        }
        
        if (json) {
            const dataJSON = {};
            data.forEach((value, key) => dataJSON[key] = value);
            return dataJSON;
        }
        return data;
    }

    async send() {
        if (!this.validateForm()) return;

        const data = this.getFormData(true);
        const response = await APIFetch("/api/settings/newpassword", "PUT", data);
        const responseJson = await response.json();
        this.updateMessage(responseJson.message, responseJson.type);
    }
}

export default PasswordForm;
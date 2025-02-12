import APIFetch from "/fb-content/assets/js/apifetch.js";

class PasswordForm {
    actionTimeout = 10000;
    sent = false;
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
                "dom": this.formDom.querySelector("[name=new-pass"),
                "errorMessage": "Kérem megfelelő formátumú jelszót adjon meg",
                get value() { return this.dom?.value },
                set value(val) { this.dom.value = val }
            },
            "newPassConfirm": {
                "dom": this.formDom.querySelector("[name=new-pass-confirm]"),
                "errorMessage": "Kérem megfelelő formátumú jelszót adjon meg",
                get value() { return this.dom?.value },
                set value(val) { this.dom.value = val }
            },
        }

        this.validationRules = {
            oldPass:        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/,
            newPass:        /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/,
            newPassConfirm: /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_\-+=\[\]{}|\\;:\'",.<>\/?~]).{8,64}$/
        }

        this.submitter = this.formDom.querySelector('.action-button');
    }

    bindEvents() {
        this.submitter.addEventListener("click", this.send.bind(this))

        // Beviteli mező fókusz eseményei
        document.querySelectorAll(".input-group").forEach((e) => this.handleInputGroupFocus(e));

        // Beviteli mezők validálási eseményei
        for (let field in this.form) {
            let element = this.form[field];
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
        const input = inputGroup.querySelector('input, select');

        input.addEventListener("focus", () => {
            if (input.nodeName !== "SELECT") label.classList.add('focus');
        });

        input.addEventListener("focusout", () => {
            if (input.value === "") label.classList.remove('focus');
        });
    }

    // Beviteli mező validálása
    validateField(name) {
        // A mező lekérdezése a paraméterek alapján
        const field = this.form[name] || undefined;
        if (!field) return false;

        // Validátor függvény, Aktuális érték és hibaüzenet megszerzése
        const validator = this.validationRules[name] || undefined;
        const value = field.value;
        const error = field.errorMessage;

        // Változók ellenőrzése
        if (!validator) return null;
        if (!value) return error;

        // Érték validálása
        return (typeof validator == 'function') ? validator(value)?null:error : validator.test(value)?null:error;
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

        if (!didValidityChange) return;

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
        // if (!this.validateForm()) return;

        const data = this.getFormData(true);
        const response = await APIFetch("/api/settings/newpassword", "PUT", data);
        console.log(response);
    }
}

export default PasswordForm;
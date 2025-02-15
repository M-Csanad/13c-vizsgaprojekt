import APIFetch from "/fb-content/assets/js/apifetch.js";

class PersonalDetailsForm {
    // Frontend tulajdonság
    ease = "power2.inOut";
    currentAvatarId;

    constructor(dom) {
        if (!gsap) throw new Error("GSAP nem található");
        
        this.initDOM(dom);
        this.bindEvents();
        this.handleAutofillFocus();
    }

    // DOM elemek lekérdezése és eltárolása későbbi használathoz
    initDOM(dom) {
        this.formDom = dom;
        this.formWrapper = dom.parentElement;

        this.form = {
            // "autofillName": {
            //     "dom": this.formDom.querySelector("[name=autofill-name]"),
            //     "errorMessage": "Kérem ne hagyja üresen a címet",
            //     get value() { return this.dom?.value },
            //     set value(val) { this.dom.value = val }
            // },
        }

        this.validationRules = {
        };

        this.avatarInputs = this.formDom.querySelectorAll(".avatar");
        this.avatarImage = document.querySelector(".profile-picture > img");
    }

    // Eseménykezelők hozzárendelése elemekhez
    bindEvents() {

        // Beviteli mező fókusz eseményei
        this.formDom.querySelectorAll(".input-group").forEach((e) => this.handleInputGroupFocus(e));

        // Beviteli mezők validálási eseményei
        for (let field in this.form) {
            let element = this.form[field];
            if (element.noValidate) continue;

            element.dom.addEventListener("change", () => {
                const error = this.validateField(field);
                this.toggleFieldState(field, error);
            });
        }

        // Avatár beviteli mezők eseményei
        this.avatarInputs.forEach(e => e.addEventListener("click", () => this.save({
            type: "avatar", 
            data: e.id
        })));
    }

    updateUI(type, newData) {
        switch (type) {
            case "avatar":
                this.changeAvatar(newData)
                break;
        
            default:
                break;
        }
    }

    changeAvatar(avatar) {
        this.avatarImage.src = avatar.uri;

        const avatarInput = [...this.avatarInputs].find(e => e.id == "avatar-"+avatar.id);
        this.avatarInputs.forEach(e => e.classList.remove("checked"));
        avatarInput.classList.add("checked");
    }

    handleAutofillFocus() {
        setTimeout(() => {
            document.querySelectorAll(".input-group").forEach(el => {
                const label = el.querySelector('label');
                const input = el.querySelector('input, select');
    
                if (!input) return;

                if (input.value) label.classList.add('focus');
            });
        }, 10);
    }

    dropFocus() {
        document.querySelectorAll(".input-group").forEach(el => {
            const label = el.querySelector('label');
            const input = el.querySelector('input, select');

            if (!input) return;

            label.classList.remove('focus');
        });
    }

    resetValidity() {
        for (let field in this.form) {
            let element = this.form[field];
            if (element.noValidate) continue;

            this.toggleFieldState(field, null);
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
        const validator = this.validationRules[name];
        const value = field.value;
        const error = field.errorMessage;

        // Változók ellenőrzése
        if (!validator) return null;
        if (!value) return error;

        // Érték validálása
        return (typeof validator == 'function') ? validator(value)?null:error : validator.test(value)?null:error;
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

    reset(uiReset = false) {
        if (uiReset) this.close();

        this.formDom.reset();
        this.dropFocus();
        this.resetValidity();
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

    getFormData(json = false, additional = null) {
        const data = new FormData(this.formDom);
        data.append("type", this.autofillType);

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

    // Backend metódusok
    async save(input) {
        // Validálás és az adatok megfelelő formátumra alakítása a backend számára
        switch (input.type) {
            case "avatar":
                const avatarId = Number(input.data.split('-')[1]);
                if (!avatarId) {
                    return;
                }

                input.data = { avatar_id: avatarId };
                break;
        
            default:
                break;
        }

        // Kérés elküldése
        const result = await APIFetch("/api/settings/updatedetails", "PUT", input);

        if (result.ok) {
            const response = await result.json();
            
            const updated = response.message[0];
            this.updateUI(input.type, updated)
        } else {
            console.log(result);
        }
    }

    cancel() {
        this.reset(true);
    }

    async remove(card, element) {
        const result = await APIFetch("/api/autofill/remove", "DELETE", {id: card.id, type: this.autofillType});

        if (result.ok) {
            this.removeCard(card, element);
        }
        else {
            console.log(result);
        }
    }

    async fetchContent() {
        const result = await APIFetch("/api/autofill/get", "GET", {type: this.autofillType});
        if (result.ok) {
            const adat = await result.json();
            if (adat.type == "EMPTY") {
                return;
            }
            else {
                this.cards = adat;
                this.updateCardUI();
            }
        } else {
            console.log(result);
        }
    }
}

export default PersonalDetailsForm;
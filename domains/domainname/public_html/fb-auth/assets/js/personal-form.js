import APIFetch from "/fb-content/assets/js/apifetch.js";

class PersonalDetailsForm {

	// Frontend tulajdonság
	ease = "power2.inOut";
	currentAvatarId;
    isCancelling = false;

	constructor(dom) {
		if (!gsap) throw new Error("GSAP nem található");
		
		this.currentActiveInput = null;
		this.initDOM(dom);
		
		this.bindEvents();
		this.bindEditSaveEvents();
		
		this.handleAutofillFocus();
	}

	// DOM elemek lekérdezése és eltárolása későbbi használathoz
	initDOM(dom) {
		this.formDom = dom;
		this.formWrapper = dom.parentElement;

		this.form = {
			"last_name": {
				"dom": this.formDom.querySelector("[name=last-name]"),
				"errorMessage": "Kérem adja meg a vezetéknevét",
				hasChanged: false,
				get value() { return this.dom?.value },
				set value(val) { this.dom.value = val }
			},
			"first_name": {
				"dom": this.formDom.querySelector("[name=first-name]"),
				"errorMessage": "Kérem adja meg a keresztnevét",
				hasChanged: false,
				get value() { return this.dom?.value },
				set value(val) { this.dom.value = val }
			},
			"email": {
				"dom": this.formDom.querySelector("[name=email]"),
				"errorMessage": "Kérem helyesen adja meg az email címét",
				hasChanged: false,
				get value() { return this.dom?.value },
				set value(val) { this.dom.value = val }
			},
			"phone": {
				"dom": this.formDom.querySelector("[name=phone]"),
				"errorMessage": "Kérem helyesen adja meg a telefonszámát",
				hasChanged: false,
				get value() { return this.dom?.value },
				set value(val) { this.dom.value = val }
			},
		};

		this.validationRules = {
			"last_name": /^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/,
			"first_name": /^[A-Za-záéíóöőúüűÁÉÍÓÖŐÚÜŰ]+$/,
			"email": /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/,
			"phone": /^(\+36|06)(\d{9})$/
		};

		this.avatarInputs = this.formDom.querySelectorAll(".avatar");
		this.avatarImage = document.querySelector(".profile-picture > img");
	}

	// Eseménykezelők hozzárendelése
	bindEvents() {

		// Beviteli mezők fókusz eseményei
		this.formDom.querySelectorAll(".input-group").forEach((e) => this.handleInputGroupFocus(e));

		// Avatár beviteli mezők eseményei
		this.avatarInputs.forEach(e => e.addEventListener("click", () => this.save({
			type: "avatar", 
			data: e.id
		})));
	}

	// Szerkesztés és mentés gomb eseménykezelése
	bindEditSaveEvents() {
		const disableOtherInputs = (currentInput) => {
			this.formDom.querySelectorAll(".input-body input").forEach(input => {
				if (input !== currentInput) {
					input.disabled = true;
				}
			});
			};

		// Add pointerdown listener to set the cancelling flag early
		this.formDom.addEventListener("pointerdown", (e) => {
			if (this.currentActiveInput && 
				(!e.target.closest(".input-body") || !e.target.closest(".input-body").contains(this.currentActiveInput))
			) {
				this.isCancelling = true;
			}
		});

		this.formDom.addEventListener("click", async (e) => {

			// Szerkesztés gomb kattintás kezelése
			if (e.target.closest(".edit")) {
				const button = e.target.closest(".edit");
				const closestInput = button.closest(".input-body").querySelector("input");
				closestInput.dataset.initialValue = closestInput.value;
				
				const fieldName = this.getFieldName(closestInput);
				if (fieldName) {
					this.form[fieldName].hasChanged = false;
				}
				
				disableOtherInputs(closestInput);
				
				closestInput.disabled = false;
				closestInput.focus();
				
				const len = closestInput.value.length;
				closestInput.setSelectionRange(len, len);
				
				this.currentActiveInput = closestInput;
				return;
			}

			// Mentés gomb kattintás kezelése
			if (e.target.closest(".save")) {
				const button = e.target.closest(".save");
				const closestInput = button.closest(".input-body").querySelector("input");
				
				const fieldName = this.getFieldName(closestInput);
				if (fieldName) {
					if (closestInput.value === closestInput.dataset.initialValue) {
						const errorMsg = "Nem történt változtatás";
						this.toggleFieldState(fieldName, errorMsg);
						return;
					}
				}

                // Ha hibás a mező, akkor nem küld kérést
                const error = this.validateField(fieldName);
                if (error) {
                    this.toggleFieldState(fieldName, error);
                    return;
                }

                const success = await this.save({
                    type: fieldName,
                    data: closestInput.value
                });

                if (success) {
                    closestInput.disabled = true;
                    this.currentActiveInput = null;
                    this.toggleFieldState(fieldName, null);
                }
                else {
                    this.toggleFieldState(fieldName, "Ismeretlen hiba történt");
                }

				return;
			}

			// Ha a kattintás nem az aktív input környezetében történik, visszaállítja azt
			if (this.currentActiveInput) {
				if (!e.target.closest(".input-body") || !e.target.closest(".input-body").contains(this.currentActiveInput)) {
					this.cancel(this.currentActiveInput);
					this.currentActiveInput = null;
				}
			}
		});
	}

    getFieldName(input) {
        return Object.keys(this.form).find(name => this.form[name].dom === input);
    }

	// UI frissítése
	updateUI(type, newData) {
		switch (type) {
			case "avatar":
				this.changeAvatar(newData);
				break;
			case "first_name":
			case "last_name":
				this.updateSidebarName(newData);
				break;
			default:
				break;
		}
	}

	updateSidebarName(newData) {
		const sidebarNameElement = document.querySelector(".profile-general .name");
		if (sidebarNameElement) {
			
			const firstName = newData.first_name || this.form.first_name.value;
			const lastName = newData.last_name || this.form.last_name.value;
			sidebarNameElement.innerHTML = `${lastName} ${firstName}`.trim();
		}
	}

	changeAvatar(avatar) {
		this.avatarImage.src = avatar.uri;
		
		const avatarInput = [...this.avatarInputs].find(e => e.id == "avatar-" + avatar.id);
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
			if (input.disabled) return;
			if (input.nodeName !== "SELECT") label.classList.add('focus');
		});

		input.addEventListener("focusout", () => {
			if (input.value === "" && !input.dataset.initialValue || !input.dataset.initialValue && this.isCancelling) label.classList.remove('focus');
		});
	}

	// A mező validálása
	validateField(name) {
		// A mező lekérdezése a paraméter alapján
		const field = this.form[name] || undefined;
		if (!field) return false;

		const validator = this.validationRules[name];
		const value = field.value;
		const error = field.errorMessage;

		if (!validator) return null;
		if (!value) return error;

		return (typeof validator == 'function') ? validator(value) ? null : error : validator.test(value) ? null : error;
	}

	toggleFieldState(name, error) {
        const field = this.form[name] || undefined;
		if (!field) return false;
        
		const errorWrapper = field.dom.closest('.input-group').querySelector('.message-wrapper');
		const messageContainer = errorWrapper.querySelector(".error-message");
		const validity = error ? "invalid" : "valid";
		const oppositeValidity = error ? "valid" : "invalid";
		const didValidityChange = field.dom.classList.contains(oppositeValidity) || (error && field.dom.classList.length == 0);
		const didErrorMessageChange = (error && messageContainer.innerHTML !== error) || (!error && messageContainer.innerHTML !== "");
        
        if (!didValidityChange && !didErrorMessageChange) return;
        
		field.dom.classList.add(validity);
		field.dom.classList.remove(oppositeValidity);
		
		if (error){
			messageContainer.innerHTML = error;

            if (!didValidityChange) return;

            gsap.killTweensOf(errorWrapper);
			gsap.set(errorWrapper, { visibility: "visible" });
			gsap.to(errorWrapper, {
				height: "auto",
				opacity: 1,
				ease: "power2.inOut",
				duration: 0.3
			});
		} else {
            if (!didValidityChange) return;

            gsap.killTweensOf(errorWrapper);
			gsap.to(errorWrapper, {
				height: 0,
				opacity: 0,
				ease: "power2.inOut",
				duration: 0.3,
				onComplete: () => {
					gsap.set(errorWrapper, { visibility: "hidden" });
				}
			});
		}
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
		// Az adatok backend számára megfelelő formázása
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
			this.updateUI(input.type, updated);
		} else {
			console.log(result);
		}

        return result.ok;
	}

	// Mező értékének visszaállítása
	cancel(activeInput) {
		if (activeInput) {
            this.isCancelling = true; // set flag before canceling
			activeInput.value = activeInput.dataset.initialValue || "";
			activeInput.disabled = true;

			// Érvényesség visszaállítása az adott mezőre
			const key = this.getFieldName(activeInput);
			this.toggleFieldState(key, null);

            this.isCancelling = false;
		}
	}

	async remove(card, element) {
		const result = await APIFetch("/api/autofill/remove", "DELETE", { id: card.id, type: this.autofillType });

		if (result.ok) {
			this.removeCard(card, element);
		} else {
			console.log(result);
		}
	}

	async fetchContent() {
		const result = await APIFetch("/api/autofill/get", "GET", { type: this.autofillType });
		if (result.ok) {
			const adat = await result.json();
			if (adat.type == "EMPTY") {
				return;
			} else {
				this.cards = adat;
				this.updateCardUI();
			}
		} else {
			console.log(result);
		}
	}
}

export default PersonalDetailsForm;
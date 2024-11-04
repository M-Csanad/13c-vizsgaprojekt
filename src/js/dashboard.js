const parentCategoryInput = document.querySelector("select#parent_category");
const parentCategoryHiddenInput = document.querySelector("input[name='parent_category_id']");
const categoryType = document.getElementById('type');
const pageLinks = document.querySelectorAll(".page");
const pages = document.querySelectorAll(".section-group");

let maxFileSize = 10;
let isPopupVisible = false;

function expandGroup(e) {
    let sourceElement = e.target;
    let section = sourceElement.closest("section");
    section.classList.toggle("active");
}

function togglePage(id) {
    for (let i=0; i < pageLinks.length; i++) {
        pageLinks[i].classList.remove("active");
        pages[i].classList.remove("active");
    }
    pageLinks[id].classList.add("active");
    pages[id].classList.add("active");
}

function hideDisplayMessages() {
    let error = document.querySelector(".error");
    let success = document.querySelector(".success");

    if (error) {
        setTimeout(() => {
            error.style.opacity = "0";
            setTimeout(() => {
                document.body.removeChild(error);
            }, 1000);
        }, 5000);
    }
    else if (success) {
        setTimeout(() => {
            success.style.opacity = "0";
            setTimeout(() => {
                document.body.removeChild(success);
            }, 1000);
        }, 5000);
    }
}

function createConfirmPopup(message) {
    isPopupVisible = true;

    let popup = document.createElement("div");
    let popupBody = document.createElement("div");

    popup.className = 'popup';
    popupBody.className = 'popup-body';
    popupBody.innerHTML = `<div class='popup-icon'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-diamond-fill" viewBox="0 0 16 16">
                                    <path d="M9.05.435c-.58-.58-1.52-.58-2.1 0L.436 6.95c-.58.58-.58 1.519 0 2.098l6.516 6.516c.58.58 1.519.58 2.098 0l6.516-6.516c.58-.58.58-1.519 0-2.098zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                                </svg>
                           </div>
                           <h2 class='popup-message'>Biztosan folytatni szeretné?</h2>
                           <div class='popup-description'>${message}</div>
                           <div class='button-group-wrapper'>
                                <div class='button-group'>
                                    <input type='button' value='Folytatás' class='confirm'>
                                    <input type='button' value='Mégsem' class='cancel'>
                                </div>
                           </div>`;

    popup.appendChild(popupBody);
    document.body.appendChild(popup);

    popup.animate( { backgroundColor: ["rgba(0,0,0,0)", "rgba(0,0,0,0.35)"], backdropFilter: ["blur(0px)", "blur(3px)"] }, {
        duration: 300,
        fill: "forwards",
        easing: "ease"
    });

    popupBody.animate( { transform: ["scale(0)", "scale(1)"] }, {
        duration: 300,
        fill: "forwards",
        easing: "ease"
    });

    window.addEventListener("keydown", (e) => {
        if (document.body.contains(popup) && e.code == "Escape") {
            closePopup(popup);
            isPopupVisible = false;
        }
    });

    return popup;
}

function closePopup(popup) {
    popup.animate( { backgroundColor: ["rgba(0,0,0,0.35)", "rgba(0,0,0,0)"], backdropFilter: ["blur(3px)", "blur(0px)"] }, {
        duration: 300,
        fill: "forwards",
        easing: "ease"
    });

    popup.querySelector('.popup-body').animate( { transform: ["scale(1)", "scale(0)"] }, {
        duration: 300,
        fill: "forwards",
        easing: "ease"
    });

    setTimeout(() => {
        document.body.removeChild(popup);
    }, 300);
}

function setCategoryHiddenInput() {
    let selected = parentCategoryInput.querySelector('option:checked') || null;
    
    if (selected) {
        parentCategoryInput.removeAttribute("disabled");
        parentCategoryHiddenInput.value = selected.dataset.id;
    }
    else {
        parentCategoryInput.setAttribute("disabled", true);
    }
}

// Azok az űrlapok, amelyeknél egy beviteli mező értékétől függ, hogy megjelenjen-e a felugró ablak
const formExceptions = {
    "#form-role": { // Az űrlap szelektor értéke
        "field": "select[name=role]", // Melyik mező értékétől függ
        "value": "Administrator" // Milyen értéknél kell a felugró ablak
    }
}

// Ellenőrizzük, hogy az aktuális űrlap kivétel-e (Tehát nem minden leadáskor kell figyelmeztető üzenet, csak ha megfelel a feltételeknek)
function isFormException(form) {
    let selector = Object.keys(formExceptions).find(selector => form.matches(selector)) || null;
    if (selector) {
        let field = form.querySelector(formExceptions[selector]["field"]);
        if (field && field.value == formExceptions[selector]["value"]) {
            return true; // Az űrlap kivétel, és az értéke megyegyezik a kivételben szereplővel
        }
        else {
            return false; // Az űrlap kivétel, de a kérdéses mező értéke nem egyezik meg a kivételben szereplővel
        }
    }
    else {
        return null; // Az űrlap nem kivétel
    }
}

function getImageOrientation(file) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => {
            const img = new Image();
            img.onload = () => {
                resolve( (img.width >= img.height) ? "horizontal" : "vertical");
            };
            img.onerror = reject;
            img.src = reader.result;
        };
        reader.onerror = reject;
        reader.readAsDataURL(file);
    });
}

function getFileSize(file) {
    return file.size >> 20; // 2^20 - nal osztunk (B -> MB)
}

window.addEventListener("load", () => {
    let sectionHeaders = document.querySelectorAll(".section-header");

    for (let header of sectionHeaders) {
        header.addEventListener("click", expandGroup);
        header.addEventListener("keydown", (e) => { if (e.code=="Space" || e.code=="Enter") expandGroup(e) });
    }

    for (let page of pageLinks) {
        page.addEventListener("click", ()=>{ togglePage(page.dataset.pageid); });
        page.addEventListener("keydown", (e) => { if (e.code=="Space" || e.code=="Enter") togglePage(page.dataset.pageid) });
    }

    document.querySelectorAll("input[data-type=image]").forEach((input) => {
        input.addEventListener("input", async () => {
            if (input.dataset.count == "singular") {
                let file = input.files[0] || null;

                if (!file) { // Ha nem töltött fel fájlt
                    input.setCustomValidity('Kérjük adjon meg egy képet!');
                    input.value = "";
                    input.reportValidity();
                    return;
                }

                let type = file.type || null;
                let acceptedTypes = input.getAttribute("accept");

                if (!acceptedTypes.includes(type)) { // Ha nem képet töltött fel
                    input.setCustomValidity('Kérjük képet adjon meg!');
                    input.value = "";
                    input.reportValidity();
                    return;
                }

                if (getFileSize(file) > maxFileSize) {
                    input.setCustomValidity('Kérjük maximum 10 MB méretű képet töltsön fel!');
                    input.value = "";
                    input.reportValidity();
                    return;
                }

                let orientation = await getImageOrientation(file);
                if (orientation != input.dataset.orientation) {
                    input.setCustomValidity('Kérjük megfelelő tájolású képet adjon meg!');
                    input.value = "";
                    input.reportValidity();
                    return;
                }

                input.setCustomValidity('');
            }
        });
    });

    // A megerősítést igénylő űrlapokhoz hozzácsatoljuk az eseményt, ami létrehozza felugró ablakokat beadáskor
    let confirmForms = document.querySelectorAll("form[data-needs-confirm='true']");

    confirmForms.forEach((form)=>{
        const formSubmitter = form.querySelector("input[type=submit]");
        form.addEventListener("submit", (e)=>{

            if (isPopupVisible || isFormException(form) === false) {
                return;
            }

            // Megakadályozzuk az automatikus leadást, és létrehozzuk az előugró ablakot
            e.preventDefault();
            let popup = createConfirmPopup(form.dataset.confirmMessage);

            // A gombokra nyomáskor vagy szimuláljuk a leadást, vagy csak bezárjuk az előugró ablakot.
            popup.querySelector("input.confirm").addEventListener("click", ()=>{
                closePopup(popup);
                setTimeout(() => {
                    formSubmitter.click();
                    isPopupVisible = false;
                }, 300);
            });

            popup.querySelector("input.cancel").addEventListener("click", ()=>{
                closePopup(popup);
                isPopupVisible = false;
            });
        });
    });

    hideDisplayMessages();
});

document.getElementById('type').addEventListener('change', ()=> {
    let selected = categoryType.value;
    
    if (selected == "sub") {
        parentCategoryInput.removeAttribute("disabled");
        parentCategoryHiddenInput.removeAttribute("disabled");
        setCategoryHiddenInput();
    }
    else {
        parentCategoryInput.setAttribute("disabled", true);
        parentCategoryHiddenInput.setAttribute("disabled", true);
    }
});

parentCategoryInput.addEventListener("change", setCategoryHiddenInput);
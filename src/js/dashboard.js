const parentCategoryInput = document.querySelector("select#parent_category");
const parentCategoryHiddenInput = document.querySelector("input[name='parent_category_id']");
const categoryType = document.getElementById('type');
const pageLinks = document.querySelectorAll(".page");
const pages = document.querySelectorAll(".section-group");
const formRole = document.getElementById("form-role");
const formRoleSubmitter = formRole.querySelector("input[type=submit]");
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

function createConfirmPopup() {
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
                           <div class='popup-description'>Adminisztrátori jogokkal csak megbízható személyeket lásson el!</div>
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

formRole.addEventListener("submit", (e) => {
    if (formRole.querySelector("select[name=role]").value == "Administrator" && !isPopupVisible) {
        e.preventDefault();
        let popup = createConfirmPopup();
        popup.querySelector("input.confirm").addEventListener("click", ()=>{
            closePopup(popup);
            setTimeout(() => {
                formRoleSubmitter.click();
                isPopupVisible = false;
            }, 300);
        });
        popup.querySelector("input.cancel").addEventListener("click", ()=>{
            closePopup(popup);
            isPopupVisible = false;
        });
    }
});

window.addEventListener("load", () => {
    let sectionHeaders = document.querySelectorAll(".section-header");

    for (let header of sectionHeaders) {
        header.addEventListener("click", expandGroup);
        header.addEventListener("keydown", (e) => { if (e.code=="Space" || e.code=="Enter") expandGroup(e) });
    }

    parentCategoryHiddenInput.value = parentCategoryInput.querySelector('option:checked').dataset.id;

    for (let page of pageLinks) {
        page.addEventListener("click", ()=>{ togglePage(page.dataset.pageid); });
        page.addEventListener("keydown", (e) => { if (e.code=="Space" || e.code=="Enter") togglePage(page.dataset.pageid) });
    }
    hideDisplayMessages();
});

document.getElementById('type').addEventListener('change', ()=> {
    let selected = categoryType.value;
    
    if (selected == "sub") {
        parentCategoryInput.removeAttribute("disabled");
        parentCategoryHiddenInput.removeAttribute("disabled");
    }
    else {
        parentCategoryInput.setAttribute("disabled", true);
        parentCategoryHiddenInput.setAttribute("disabled", true);
    }
});

parentCategoryInput.addEventListener("change", () => {
    parentCategoryHiddenInput.value = parentCategoryInput.querySelector('option:checked').dataset.id;
});
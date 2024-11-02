const parentCategoryInput = document.querySelector("select#parent_category");
const parentCategoryHiddenInput = document.querySelector("input[name='parent_category_id']");
const categoryType = document.getElementById('type');
const pageLinks = document.querySelectorAll(".page");
const pages = document.querySelectorAll(".section-group");
const formRole = document.getElementById("form-role");

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
    let popup = document.createElement("div");
    popup.className = 'popup';
    popup.innerHTML = "asdhiashdashd";
    document.body.appendChild(popup);
    popup.animate(
    {
        backgroundColor: ["rgba(0,0,0,0)", "rgba(0,0,0,0.35)"]
    },
    {
        duration: 300,
        fill: "forwards",
        easing: "ease"
    }
    )
    return popup;
}

formRole.addEventListener("submit", (e) => {
    if (formRole.querySelector("select[name=role]").value == "Administrator") {
        e.preventDefault();
        let popup = createConfirmPopup();
        // popup.querySelector("input.confirm").addEventListener("click", ()=>{formRole.submit();});
        // popup.querySelector("input.cancel").addEventListener("click", ()=>{formRole.submit();});
    }
    else {
        formRole.submit();
    }
});

window.addEventListener("load", ()=> {
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
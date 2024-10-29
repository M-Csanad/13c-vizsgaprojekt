const parentCategoryInput = document.querySelector("select#parent_category");
const parentCategoryHiddenInput = document.querySelector("input[name='parent_category_id']");
const categoryType = document.getElementById('type');
const pageLinks = document.querySelectorAll(".page");
const pages = document.querySelectorAll(".section-group");

// function updateHeight() {
//     let activeGroups = document.querySelectorAll(".section-group.active > .group-body");
//     for (let group of activeGroups) {
//         group.style.maxHeight = group.scrollHeight + 'px';
//     }
// }

function expandGroup(e) {
    let sourceElement = e.target;
    let sectionBody = sourceElement.closest(".section-header").nextElementSibling;
    sectionBody.classList.toggle("active");
}

window.addEventListener("load", ()=> {
    let sectionHeaders = document.querySelectorAll(".section-header");

    for (let header of sectionHeaders) {
        header.addEventListener("click", expandGroup);
    }

    parentCategoryHiddenInput.value = parentCategoryInput.querySelector('option:checked').dataset.id;
});

// window.addEventListener("resize", updateHeight);

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

window.addEventListener("load", () => {
    parentCategoryHiddenInput.value = parentCategoryInput.querySelector('option:checked').dataset.id;

    for (let page of pageLinks) {
        page.addEventListener("click", ()=>{ togglePage(page.dataset.pageid); });
    }
});

function togglePage(id) {
    for (let i=0; i < pageLinks.length; i++) {
        pageLinks[i].classList.remove("active");
        pages[i].classList.remove("active");
    }
    pageLinks[id].classList.add("active");
    pages[id].classList.add("active");
}
const parentCategoryInput = document.querySelector("select#parent_category");
const parentCategoryHiddenInput = document.querySelector("input[name='parent_category_id']");
const categoryType = document.getElementById('type');

function updateHeight() {
    let activeGroups = document.querySelectorAll(".section-group.active > .group-body");
    for (let group of activeGroups) {
        group.style.maxHeight = group.scrollHeight + 200 + 'px';
    }
}

function expandGroup(e) {
    let sourceElement = e.target;
    let sectionGroup = sourceElement.closest('.section-group');
    let groupBody = sectionGroup.querySelector('.group-body');

    sectionGroup.classList.toggle("active");

    if (sectionGroup.classList.contains('active')) {
        groupBody.style.maxHeight = groupBody.scrollHeight + 'px';
    }
    else {
        groupBody.style.maxHeight = '0px';
    }
}
window.addEventListener("load", ()=> {
    let groupHeaders = document.querySelectorAll(".section-group > .group-header");

    for (let header of groupHeaders) {
        header.addEventListener("click", expandGroup);
    }

    parentCategoryHiddenInput.value = parentCategoryInput.querySelector('option:checked').dataset.id;
});

window.addEventListener("resize", updateHeight);

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
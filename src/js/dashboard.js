window.addEventListener("load", ()=> {
    let groupHeaders = document.querySelectorAll(".section-group > .group-header");

    for (let header of groupHeaders) {
        header.addEventListener("click", expandGroup);
    }
});

function expandGroup(e) {
    let sourceElement = e.target;
    let sectionGroup = sourceElement.closest('.section-group');
    let groupBody = sectionGroup.querySelector('.group-body');

    sectionGroup.classList.toggle("active");

    if (sectionGroup.classList.contains('active')) {
        groupBody.style.height = groupBody.scrollHeight + 'px';
    }
    else {
        groupBody.style.height = '0px';
    }
}
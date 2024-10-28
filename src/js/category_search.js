const search = document.querySelector(".search");
const searchInput = search.querySelector("input");
const searchItemsContainer = search.querySelector(".items");

searchInput.addEventListener("focusout", () => {
    search.style.borderRadius = "5px 5px 5px 5px";
    updateHeight();
});
searchInput.addEventListener("focusin", () => {
    if (searchItemsContainer.children.length > 0) {
        toggleDropdown(true);
        search.style.borderRadius = "5px 5px 0 0";
        updateHeight();
    }
});
document.addEventListener("click", (e) => {
    if (!searchInput.contains(e.target) && !searchItemsContainer.contains(e.target)) {
        toggleDropdown(false);
    }
});
searchItemsContainer.addEventListener("click", (e)=>{e.stopPropagation()});
searchInput.addEventListener("input", async () => {
    let input = searchInput.value;
    let data = new FormData();
    data.append('search_term', input);
    if (input.length > 0) {
        const response = await fetch("./misc/search_categories.php",
            {
                method: "POST",
                body: data
            }
        );
        
        if (response.ok) {
            let data = await response.json();
            populateItemContainer(data);
        }
    }
    else {
        searchItemsContainer.innerHTML = "";
        searchItemsContainer.style.display = "none";
        search.style.borderRadius = "5px 5px 5px 5px";
        updateHeight();
    }
});

function populateItemContainer(categories) {
    if (categories == "Nincs találat!") {
        searchItemsContainer.style.display = "none";
        search.style.borderRadius = "5px 5px 5px 5px";
        updateHeight();
        return;
    }

    if (!Array.isArray(categories)) categories = [categories];

    searchItemsContainer.innerHTML = "";
    searchItemsContainer.style.display = "block";
    search.style.borderRadius = "5px 5px 0 0";
    for (let category of categories) {
        let categoryDOM = document.createElement("div");
        categoryDOM.className = "search-item";
        categoryDOM.addEventListener("click", ()=>{ setInputs(category.id, category.category_type, category.name);});
        categoryDOM.innerHTML = `<img src='${category.thumbnail_image_uri}'><b>${category.name}</b> - ${(category.category_type == "category") ? "Főkategória" : `Alkategória <i>(${category.parent_category})</i>`}`;
        searchItemsContainer.appendChild(categoryDOM);
    }

    updateHeight();
}

function setInputs(id, type, name) { 
    search.querySelector("input[name=selected_category]").value = id; 
    search.querySelector("input[name=selected_category_type]").value = type;
    searchInput.value = name;
    toggleDropdown(false);
    updateHeight();
}

function updateHeight() {
    let activeGroups = document.querySelectorAll(".section-group.active > .group-body");
    for (let group of activeGroups) {
        group.style.setProperty('height', 'auto', 'important'); 
        group.style.setProperty("height", group.scrollHeight + 'px', "important");
        group.style.setProperty("max-height", group.scrollHeight + 'px', "important");
    }
}

function toggleDropdown(show) {
    searchItemsContainer.style.display = show ? "block" : "none";
}
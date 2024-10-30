const search = document.querySelector(".search");
const searchInput = search.querySelector("input");
const searchItemsContainer = document.querySelector(".items");

function positionDropdown() {
    const rect = search.getBoundingClientRect();
    searchItemsContainer.style.top = `${rect.bottom + window.scrollY}px`;
    searchItemsContainer.style.left = `${rect.left + window.scrollX}px`;
    searchItemsContainer.style.width = `${rect.width}px`;
}

function populateItemContainer(categories) {
    if (categories == "Nincs találat!") {
        searchItemsContainer.style.display = "none";
        searchItemsContainer.innerHTML = "";
        search.style.borderRadius = "5px 5px 5px 5px";
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
        categoryDOM.innerHTML = `<img src='${category.thumbnail_image_uri}'><div><b>${category.name}</b> - ${(category.category_type == "category") ? "Főkategória" : `Alkategória <i>(${category.parent_category})</i></div>`}`;
        searchItemsContainer.appendChild(categoryDOM);
    }
}
function resetInputs() {
    search.querySelector("input[name=category_id]").value = 'null'; 
    search.querySelector("input[name=category_type]").value = 'null';
}

function validateSearchInput() {
    if (search.querySelector("input[name=category_id]").value == 'null' || search.querySelector("input[name=category_type]").value == 'null') {
        searchInput.setCustomValidity('Kérjük válasszon egy meglévő kategóriát!');
    }
    else {
        searchInput.setCustomValidity('');
    }
}

function setInputs(id, type, name) { 
    search.querySelector("input[name=category_id]").value = id; 
    search.querySelector("input[name=category_type]").value = type;
    searchInput.value = name;
    search.style.borderRadius = "5px 5px 5px 5px";
    searchItemsContainer.innerHTML = "";
    validateSearchInput();
    toggleDropdown(false);
}

function toggleDropdown(show) {
    searchItemsContainer.style.display = show ? "block" : "none";
}

searchInput.addEventListener("focusin", async () => {
    await searchCategory();
    if (searchItemsContainer.children.length > 0) {
        toggleDropdown(true);
        positionDropdown();
        search.style.borderRadius = "5px 5px 0 0";
    }
});
window.addEventListener("resize", positionDropdown);
document.addEventListener("click", (e) => {
    if (!searchInput.contains(e.target) && !searchItemsContainer.contains(e.target)) {
        search.style.borderRadius = "5px 5px 5px 5px";
        toggleDropdown(false);
    }
});
searchInput.addEventListener("input", async () => {
    resetInputs();
    positionDropdown();
    await searchCategory();
    validateSearchInput();
});

async function searchCategory(){
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
    }
}
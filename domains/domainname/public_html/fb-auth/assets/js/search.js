// Segédfüggvény egy keresőmező inicializálásához
function initializeSearch(search) {
  const searchInput = search.querySelector("input");
  const parentForm = search.closest("form");
  const inputGrid = search.closest(".input-grid");
  const searchItemsContainer = search
    .closest(".section-body")
    .querySelector(".items");
  const searchType = search.dataset.searchType; // Pl. 'category' vagy 'user'
  const autofill = search.dataset.autofillFields ?? null;
  const searchConfig = {
    category: {
      autofillFields: [
        { name: "name" },
        { name: "subname" },
        { name: "description" },
        { name: "type" },
        { name: "parent_category", disabledByDefault: true },
      ],
      template: (item) => `
                <img src='${item.thumbnail_image_horizontal_uri}'><div><b>${item.name}</b> - 
                ${
                  item.type === "category"
                    ? "Főkategória"
                    : `Alkategória <i>(${item.parent_category})</i>`
                }</div>`,

      clickHandler: async (item) => {
        if (!autofill) {
          itemClickHandler(item, ["id", "type", "name"]);
        } else {
          itemClickHandler(item, ["id", "type", "name"], {
            fields: [
              { field: "name", value: item.name },
              { field: "subname", value: item.subname },
              { field: "description", value: item.description },
              { field: "type", value: item.parent_category ? "sub" : "main" },
              {
                field: "parent_category",
                value: item.parent_category ? item.parent_category : null,
              },
              {
                field: "original_parent_category",
                value: item.parent_category ? item.parent_category : null,
              },
            ],
          });
        }
      },
    },
    user: {
      template: (user) => `
                <div><b>${user.name}</b> - ${user.email} (<i>${user.role}</i>)</div>`,
      clickHandler: async (user) => itemClickHandler(user, ["id", "name"]),
    },
    product: {
      autofillFields: [
        { name: "name" },
        { name: "description" },
        { name: "price" },
        { name: "stock" },
        { name: "tags", multiple: true },
      ],
      template: (product) => {
        if (!product.category || !product.subcategory) {
          return `<img src='${product.thumbnail_image_horizontal_uri}'><div><b>${
            product.name
          }</b> - <i>Még nincs kategóriába sorolva.</i></div>`;
        } else {
          return `<img src='${product.thumbnail_image_horizontal_uri}'><div><b>${product.name}</b> - <i>${
            product.category ? product.category : "#"
          } / ${product.subcategory ? product.subcategory : "#"}</i></div>`;
        }
      },
      clickHandler: async (product) => {
        if (!autofill) {
          itemClickHandler(product, ["id", "name"]);
        } else {
          itemClickHandler(product, ["id", "name"], {
            fields: [
              { field: "name", value: product.name },
              { field: "description", value: product.description },
              { field: "price", value: product.unit_price },
              { field: "stock", value: product.stock },
              {
                field: "tags",
                value: product.tag_ids ? product.tag_ids.split(",") : null,
              },
              { field: "benefits-container", value: product.benefit_ids ? product.benefit_ids.split(",") : null},
              { field: "side-effects-container", value: product.side_effect_ids ? product.side_effect_ids.split(",") : null}
            ],
          });
        }
      },
    },
    product_page: {
      autofillFields: [
        { name: "category" },
        { name: "subcategory" },
        { name: "price" },
        { name: "content" },
      ],
      template: (page) => {
        console.log(page.uri);
        return `<img src='${page.uri}'><div><b>${page.name}</b> - <i>${
          page.category_name ? page.category_name : "#"
        } / ${page.subcategory_name ? page.subcategory_name : "#"}</i> (${
          page.content_preview
        }...)</div>`;
      },
      clickHandler: async (page) => {
        if (!autofill) {
          itemClickHandler(page, ["id", "name"]);
        } else {
          itemClickHandler(page, ["id", "name"], {
            fields: [
              { field: "product_page_name", value: page.name },
              { field: "category", value: page.category_name },
              { field: "subcategory", value: page.subcategory_name },
              { field: "content", value: page.page_content },
            ],
          });
        }
      },
    },
  };

  let isSearchEnabled = true;
  let lastContentfulSearchTerm = "";

  // Aszinkron keresés a megfelelő PHP segítségével
  async function searchHandler() {
    let input = searchInput.value;
    let data = new FormData();
    data.append("search_type", searchType);
    data.append("search_term", input);
    if (input.length > 0) {
      const response = await fetch(
        `../../dashboard_search.php`,
        {
          method: "POST",
          body: data,
        }
      );

      if (response.ok) {
        let data = await response.json();

        if (data.type == "ERROR") {
          console.log("Hiba a kereséskor: " + data.message);
        }

        if (data.type == "SUCCESS") {
          lastContentfulSearchTerm = input;
        } else {
          isSearchEnabled = false;
        }

        if (data.type == "EMPTY") {
          toggleDropdown(false);
          return;
        } else {
          populateItemContainer(data.message);
        }
      }
    } else {
      toggleDropdown(false);
    }
  }

  function toggleLoader(show) {
    const loader = searchItemsContainer.querySelector(".loader");
    if (show) {
      loader.style.display = "block";
    } else {
      loader.style.display = "none";
    }
  }

  // A legördülömenü feltöltése találatokkal
  function populateItemContainer(items) {
    const config = searchConfig[searchType];
    if (!config) return;

    searchItemsContainer.innerHTML = "";
    toggleDropdown(true);

    if (!Array.isArray(items)) items = [items];

    for (let item of items) {
      let itemDOM = document.createElement("div");
      itemDOM.className = "search-item";
      itemDOM.innerHTML = config.template(item);
      itemDOM.addEventListener("click", () => config.clickHandler(item));
      searchItemsContainer.appendChild(itemDOM);
    }
  }

  // A legördülőmenü elhelyezése a beviteli mező alá (mivel az űrlapon overflow: hidden van, ezért a legördülőmenü kívül kell hogy legyen)
  function positionDropdown() {
    const rect = search.getBoundingClientRect();
    searchItemsContainer.style.top = `${rect.bottom + window.scrollY}px`;
    searchItemsContainer.style.left = `${rect.left + window.scrollX}px`;
    searchItemsContainer.style.width = `${rect.width}px`;
  }

  // Segédfüggvény a keresési találatok megjelenítése / elrejtése
  function toggleDropdown(show) {
    if (show) {
      search.style.borderRadius = "5px 5px 0 0";
      searchItemsContainer.style.display = "block";
    } else {
      searchItemsContainer.style.display = "none";
      searchItemsContainer.innerHTML = "";
      search.style.borderRadius = "5px 5px 5px 5px";
    }
  }
  // A keresési találatra történő kattintás kezelése
  async function itemClickHandler(item, fields, outputData = {}) {
    // Módosításkor a kitöltendő mezők az outputData alapján töltődnek ki
    fields.forEach((field) => {
      const input = search.querySelector(`input[name=${searchType}_${field}]`);
      if (input) input.value = item[field];
    });

    if (outputData.fields) {
      outputData.fields.forEach(({ field, value }) => {
        const input = parentForm.querySelector(`[name=${field}]`);
        console.log(input)
        if (input) {
          if (value) {
            if (Array.isArray(value)) {
              for (let id of value) {
                let clickElement = input.querySelector(`input[value="${id}"]`);
                if (!clickElement) clickElement = input.querySelector(`.option[data-value="${id}"] > .check`);

                console.log(clickElement)
                clickElement.click();
              }
            } else {
              input.value = value;
              if (input.disabled) input.disabled = false;
              input.dispatchEvent(new Event("change"));
            }
          }
        }
      });
    }

    searchInput.value = item.name;
    toggleDropdown(false);
    validateSearchInput();
    if (item.role) {
      disableRoleOption(item.role);
      selectFirstValidOption(inputGrid.querySelector("select"));
      enableDisabledInputs();
    }
  }

  function disableRoleOption(role) {
    let currentRoleOption = inputGrid.querySelector(`option[value='${role}']`);

    Array.from(inputGrid.querySelectorAll("option")).forEach((e) => {
      e.disabled = false;
    });
    currentRoleOption.disabled = true;
  }

  function selectFirstValidOption(element) {
    if (element.value) {
      element.querySelector("option:checked").selected = false;
    }
    element.querySelector("option:enabled").selected = true;
  }

  // A rejtett mezők értékének visszaállítása
  function resetInputs() {
    search
      .querySelectorAll("input[type=hidden]")
      .forEach((input) => (input.value = "null"));
  }

  // A keresőmezők validálása a rejtett mezők alapján
  function validateSearchInput() {
    const invalid = Array.from(
      search.querySelectorAll("input[type=hidden]")
    ).some((input) => input.value === "null");
    searchInput.setCustomValidity(
      invalid ? "Kérjük válasszon egy meglévő elemet!" : ""
    );
  }

  function enableDisabledInputs() {
    const disabledInputs = inputGrid.querySelectorAll(
      "input:disabled, select:disabled"
    );
    disabledInputs.forEach((input) => {
      input.disabled = false;
    });
  }

  function clearAutofillFields() {
    const fields = searchConfig[searchType].autofillFields;
    for (let field of fields) {
      let element = parentForm.querySelector(`[name=${field.name}]`);
      if (element) {
        if (field.multiple) {
          element.querySelectorAll("input:checked").forEach((e) => e.click());
          return;
        }

        if (field.disabledByDefault) element.disabled = true;
        if (element.nodeName == "SELECT") {
          selectFirstValidOption(element);
          element.dispatchEvent(new Event("change"));
        } else if (element.value != "") {
          element.value = "";
        }
      }
    }
  }

  function disableSelectInputs() {
    inputGrid.querySelectorAll("select").forEach((e) => (e.disabled = true));
  }

  // Eseménykezelés
  // A keresőmezőre kattintáskor jelenjenek meg az aktuális találatok
  searchInput.addEventListener("focusin", async () => {
    if (searchItemsContainer.style.display == "none") {
      if (isSearchEnabled) {
        await searchHandler();
      }
      if (searchItemsContainer.children.length > 0) {
        toggleDropdown(true);
        positionDropdown();
      }
    }
  });

  // A képernyő átméretezésekor a legördülő menű pozícióját frissítjük
  window.addEventListener("resize", positionDropdown);
  window.addEventListener("load", validateSearchInput);

  // Ha nem a keresőmezőre, vagy a legördülő menüre kattint a user, akkor a legördülő menü becsukódik
  document.addEventListener("click", (e) => {
    if (
      !searchInput.contains(e.target) &&
      !searchItemsContainer.contains(e.target)
    ) {
      toggleDropdown(false);
    }
  });

  // Minden bevitt karakter után meghívjuk a szükséges függvényeket
  searchInput.addEventListener("input", async () => {
    resetInputs();
    positionDropdown();

    // A kereső optimalizálása, hogy ne legyenek felesleges lekérések
    if (!isSearchEnabled) {
      let value = searchInput.value;
      if (
        lastContentfulSearchTerm.includes(value) ||
        value == "" ||
        lastContentfulSearchTerm == value
      ) {
        isSearchEnabled = true;
      }
    }

    if (isSearchEnabled) {
      await searchHandler();
      validateSearchInput();
    }

    if (!searchInput.checkValidity()) {
      if (autofill) {
        clearAutofillFields();
        disableSelectInputs();
      }
    }
  });
}

// Mindegyik keresőmezőt inicializáljuk
document
  .querySelectorAll(".search")
  .forEach((search) => initializeSearch(search));

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
        { name: "name", disabledByDefault: true },
        { name: "subname", disabledByDefault: true },
        { name: "description", disabledByDefault: true },
        { name: "type", disabledByDefault: true },
        { name: "parent_category", disabledByDefault: true },
      ],
      template: (item) => `
                <picture>
                  <source srcset="${item.thumbnail_image_horizontal_uri}-768px.avif" type="image/avif">
                  <source srcset="${item.thumbnail_image_horizontal_uri}-768px.webp" type="image/webp">
                  <img src="${item.thumbnail_image_horizontal_uri}-768px.jpg" alt="${item.name}">
                </picture>
                <div><b>${item.name}</b> - 
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
      autofillFields: [
        { name: "name", disabledByDefault: true },
      ],
      template: (user) => `
                <div><b>${user.name}</b> - ${user.email} (<i>${user.role}</i>)</div>`,
      clickHandler: async (user) => itemClickHandler(user, ["id", "name"]),
    },
    product: {
      autofillFields: [
        { name: "name", disabledByDefault: true },
        { name: "description", disabledByDefault: true },
        { name: "price", disabledByDefault: true },
        { name: "stock", disabledByDefault: true },
        { name: "tags", multiple: true },
        { name: "benefits-container", multiple: true },
        { name: "side-effects-container", multiple: true },
      ],
      template: (product) => {
        const pictureElement = `
                <picture>
                  <source srcset="${product.thumbnail_image_horizontal_uri}-768px.avif" type="image/avif">
                  <source srcset="${product.thumbnail_image_horizontal_uri}-768px.webp" type="image/webp">
                  <img src="${product.thumbnail_image_horizontal_uri}-768px.jpg" alt="${product.name}">
                </picture>`;
        if (!product.category || !product.subcategory) {
          return `${pictureElement}<div><b>${product.name}</b> - <i>Még nincs kategóriába sorolva.</i></div>`;
        } else {
          return `${pictureElement}<div><b>${product.name}</b> - <i>${product.category ? product.category : "#"} / ${product.subcategory ? product.subcategory : "#"}</i></div>`;
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
        { name: "category", disabledByDefault: true },
        { name: "subcategory", disabledByDefault: true },
        { name: "content", disabledByDefault: true },
      ],
      template: (page) => {
        return `<picture>
                  <source srcset="${page.uri}-768px.avif" type="image/avif">
                  <source srcset="${page.uri}-768px.webp" type="image/webp">
                  <img src="${page.uri}-768px.jpg" alt="${page.name}">
                </picture>
                <div><b>${page.name}</b> - <i>${page.category_name ? page.category_name : "#" } / ${page.subcategory_name ? page.subcategory_name : "#"}</i> (${page.content_preview}...)</div>`;
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
    order: {
      autofillFields: [
        { name: "status", disabledByDefault: true }
      ],
      template: (order) => {
        return `<div><b>#${order.id}</b> - ${order.user_name} (${order.user_email}) - <i>${order.status}</i> - ${order.order_total} Ft</div>`;
      },
      clickHandler: async (order) => {
        if (!autofill) {
          itemClickHandler(order, ["id"]);
        } else {
          itemClickHandler(order, ["id"], {
            fields: [
              { field: "order_status", value: order.status },
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
        `/api/auth/dashboard-search`,
        {
          method: "POST",
          headers: {
            'X-Requested-With': 'XMLHttpRequest'
          },
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
    // Háttér inputok beállítása
    fields.forEach((field) => {
      const input = search.querySelector(`input[name=${searchType}_${field}]`);
      if (input) input.value = item[field];
    });
    
    // Módosításkor a kitöltendő mezők az outputData alapján töltődnek ki
    if (outputData.fields) {
      outputData.fields.forEach(({ field, value }) => {
        const input = parentForm.querySelector(`[name=${field}]`);
        if (input) {
          if (value) {

            // Ha a value egy tömb, akkor checkboxokhoz, vagy selecthez tartoznak,
            // Ezért itt végigmegyünk rajtuk és rákattintunk a megfelelő elemre
            if (Array.isArray(value)) {
              for (let id of value) {
                let clickElement = input.querySelector(`input[value="${id}"]`);
                if (!clickElement) clickElement = input.querySelector(`.option[data-value="${id}"] > .check`);

                clickElement.click();
              }
            } else {
              // Ebben az esetben az input csak egy sima mező vagy select,
              // Emiatt ellátjuk értékkel és egy change eseményt is indítunk (category select)
              input.value = value;

              if (input.disabled) input.disabled = false;
              input.dispatchEvent(new Event("change"));
            }
          }
        }
      });
    }

    searchInput.value = item.name ? item.name : item.id;
    toggleDropdown(false);
    validateSearchInput();
    if (item.role) {
      disableRoleOption(item.role);
      selectFirstValidOption(inputGrid.querySelector("select"));
      enableDisabledInputs();
    }
  }

  // Aktív jogosultság opciójának letiltása
  function disableRoleOption(role) {
    let currentRoleOption = inputGrid.querySelector(`option[value='${role}']`);

    Array.from(inputGrid.querySelectorAll("option")).forEach((e) => {
      e.disabled = false;
    });
    currentRoleOption.disabled = true;
  }

  // Egy select elem első érvényes opciójának kiválasztása
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

  // Letiltott elemek visszakapcsolása
  function enableDisabledInputs() {
    const disabledInputs = inputGrid.querySelectorAll(
      "input:disabled, select:disabled"
    );
    disabledInputs.forEach((input) => {
      input.disabled = false;
    });
  }

  // Automatikusan kitöltött elemek kiürítése
  function clearAutofillFields() {
    // Mezők megszerzése
    const fields = searchConfig[searchType].autofillFields;
    
    for (let field of fields) {
      let element = parentForm.querySelector(`[name=${field.name}]`);
      if (element) {

        // Ha több mező van, akkor rányomunk minden bejelölt elemre
        if (field.multiple) {
          element.querySelectorAll("input:checked, .check.on").forEach((e) => e.click());
          continue;
        }

        // Ha alapból le van tiltva az elem, akkor letiltjuk
        if (field.disabledByDefault) element.disabled = true;

        // Ha legördülőmenü, akkor az első érvényes elemet kiválasztjuk
        // Egyébként pedig kiürítjük
        if (element.nodeName == "SELECT") {
          selectFirstValidOption(element);
          element.dispatchEvent(new Event("change"));
        } else if (element.value != "") {
          element.value = "";
        }
      }
    }
  }

  // Legördülőmenük letiltása
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

    // Ha be van kapcsolva a keresés, akkor lefuttatjuk
    if (isSearchEnabled) {
      await searchHandler();
      validateSearchInput();
    }


    // Ha üres, vagy invalid a kereső, akkor kiürítjük az űrlapelemeket és kikapcsoljuk őket.
    if (!searchInput.value || !searchInput.checkValidity()) {
      clearAutofillFields();
      disableSelectInputs();
    }
  });
}

// Mindegyik keresőmezőt inicializáljuk
document
  .querySelectorAll(".search")
  .forEach((search) => initializeSearch(search));

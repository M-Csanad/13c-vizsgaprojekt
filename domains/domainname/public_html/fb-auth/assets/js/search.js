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
      hasImageCards: true,
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
          await generateImageCards(item);
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
                <div><b>#${user.id} ${user.name}</b> - ${user.email} (<i>${user.role}</i>)</div>`,
      clickHandler: async (user) => itemClickHandler(user, ["id", "name"]),
    },
    product: {
      hasImageCards: true,
      autofillFields: [
        { name: "name", disabledByDefault: true },
        { name: "description", disabledByDefault: true },
        { name: "price", disabledByDefault: true },
        { name: "stock", disabledByDefault: true },
        { name: "net_weight", disabledByDefault: true },
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
          await generateImageCards(product);
          itemClickHandler(product, ["id", "name"], {
            fields: [
              { field: "name", value: product.name },
              { field: "description", value: product.description },
              { field: "price", value: product.unit_price },
              { field: "stock", value: product.stock },
              { field: "net_weight", value: product.net_weight },
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

  async function getImages(item) {
    let categoryType = null;
    if (searchType == "category") {
      if (item.parent_category) {
        categoryType = "subcategory";
      } else {
        categoryType = "category";
      }
    }

    const response = await fetch(
      `/api/images?search_type=${searchType}${categoryType ? "&type="+categoryType : ""}&id=${item.id}`,
      {
        method: "GET",
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        }
      }
    );

    if (response.ok) {
      let data = await response.json();
      if (data.type == "SUCCESS") {
        return data.message;
      }
    }
  }

  async function generateImageCards(item) {
    const images = await getImages(item);
    if (images) {
      if (searchType == "category") {
        generateCard(item, images[0]);
      }
      else if (searchType == "product") {
        generateCard(item, images);
      }
    }
  }

  function generateCard(item, images) {
    if (searchType == "category") {
      searchInput.closest("form").querySelector(".image-cards.horizontal").innerHTML =
      `
        <div class="image-card" data-update-key="horizontal-edit">
          <img src="${images.horizontal_uri}-768px.jpg" alt="${images.name}"/>
          <div class="card-actions">
              <input type="file" id="horizontal-edit" class="visually-hidden" name="horizontal-edit" accept="image/png, image/jpeg" data-orientation="horizontal" data-type="image" data-count="singular" data-image-type="horizontal" data-image-id="${images.id || ''}" tabindex="-1">
              <label for="horizontal-edit" class="action-edit" role="button" aria-label="Edit image">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill edit" viewBox="0 0 16 16">
                      <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                  </svg>
              </label>
          </div>
        </div>
      `;

      searchInput.closest("form").querySelector(".image-cards.vertical").innerHTML =
      `
        <div class="image-card" data-update-key="vertical-edit">
          <img src="${images.vertical_uri}-768px.jpg" alt="${images.name}"/>
          <div class="card-actions">
              <input type="file" id="vertical-edit" class="visually-hidden" name="vertical-edit" accept="image/png, image/jpeg" data-orientation="vertical" data-type="image" data-count="singular" data-image-type="vertical" data-image-id="${images.id || ''}" tabindex="-1">
              <label for="vertical-edit" class="action-edit" role="button" aria-label="Edit image">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill edit" viewBox="0 0 16 16">
                      <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                  </svg>
              </label>
          </div>
        </div>
      `;
    }
    else if (searchType == "product") {
      const thumbnailContainer = searchInput.closest("form").querySelector(".image-cards.thumbnail");
      const productImagesContainer = searchInput.closest("form").querySelector(".image-cards.product-images");
      const thumbnail_image = images.filter(x => x.is_thumbnail)[0];
      const product_images = images.filter(x => !x.is_thumbnail);

      thumbnailContainer.innerHTML =
      `
        <div class="image-card" data-id="${thumbnail_image.id}" data-update-key="thumbnail-edit-${thumbnail_image.id}">
          <img src="${thumbnail_image.uri}-768px.jpg" alt="${item.name}"/>
          <div class="card-actions">
              <input type="file" name="thumbnail-edit-${thumbnail_image.id}" id="thumbnail-edit-${thumbnail_image.id}" class="visually-hidden" accept="image/png, image/jpeg" data-orientation="any" data-type="image" data-count="singular" data-image-type="thumbnail" data-image-id="${thumbnail_image.id}" data-id="${item.id}" tabindex="-1">
              <label for="thumbnail-edit-${thumbnail_image.id}" class="action-edit" role="button" aria-label="Edit image">
                  <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill edit" viewBox="0 0 16 16">
                      <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                  </svg>
              </label>
          </div>
        </div>
      `;

      productImagesContainer.innerHTML = "";
      for (let i = 0; i < product_images.length; i++) {
        const image = product_images[i];
        const imageNumber = i + 1; // Human readable number for each image
        
        productImagesContainer.innerHTML +=
        `
          <div class="image-card" data-id="${image.id}" data-update-key="product-edit-${image.id}">
            <img src="${image.uri}-768px.jpg" alt="${item.name}"/>
            <div class="card-actions">
                <input type="file" name="product-edit-${image.id}" id="product-edit-${image.id}" class="visually-hidden" accept="image/png, image/jpeg" data-orientation="any" data-type="image" data-count="singular" data-image-type="product_image" data-image-id="${image.id}" data-id="${item.id}" tabindex="-1">
                <label for="product-edit-${image.id}" class="action-edit" role="button" aria-label="Edit image">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-pencil-fill edit" viewBox="0 0 16 16">
                      <path d="M12.854.146a.5.5 0 0 0-.707 0L10.5 1.793 14.207 5.5l1.647-1.646a.5.5 0 0 0 0-.708zm.646 6.061L9.793 2.5 3.293 9H3.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.207zm-7.468 7.468A.5.5 0 0 1 6 13.5V13h-.5a.5.5 0 0 1-.5-.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.175-.032l-.179.178a.5.5 0 0 0-.11.168l-2 5a.5.5 0 0 0 .65.65l5-2a.5.5 0 0 0 .168-.11z"/>
                    </svg>
                </label>
                <button type="button" class="action-delete" role="button" aria-label="Delete image" data-image-id="${image.id}" data-image-type="product_image">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-trash3-fill" viewBox="0 0 16 16">
                        <path d="M11 1.5v1h3.5a.5.5 0 0 1 0 1h-.538l-.853 10.66A2 2 0 0 1 11.115 16h-6.23a2 2 0 0 1-1.994-1.84L2.038 3.5H1.5a.5.5 0 0 1 0-1H5v-1A1.5 1.5 0 0 1 6.5 0h3A1.5 1.5 0 0 1 11 1.5m-5 0v1h4v-1a.5.5 0 0 0-.5-.5h-3a.5.5 0 0 0-.5.5M4.5 5.029l.5 8.5a.5.5 0 1 0 .998-.06l-.5-8.5a.5.5 0 1 0-.998.06m6.53-.528a.5.5 0 0 0-.528.47l-.5 8.5a.5.5 0 0 0 .998.058l.5-8.5a.5.5 0 0 0-.47-.528M8 4.5a.5.5 0 0 0-.5.5v8.5a.5.5 0 0 0 1 0V5a.5.5 0 0 0-.5-.5"/>
                    </svg>
                </button>
            </div>
          </div>
        `;
      }
      
      productImagesContainer.innerHTML += `
        <div class="add-field-light">
          <input type="file" id="product-add-image" class="visually-hidden" accept="image/png, image/jpeg" data-orientation="any" data-type="image" data-count="singular" data-id="${item.id}" data-item-type="product" tabindex="-1">
          <label for="product-add-image" role="button" aria-label="Add new image">+</label>
        </div>
      `;
    }
  }

  function clearImageCards() {
    searchInput.closest("form").querySelectorAll(".image-cards").forEach((e) => {
      e.innerHTML = `<div class="none-selected">Nincs kiválasztva ${searchType == "category" ? "kategória" : "termék"}!</div>`;
    });
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
    // Form title megszerzése a formTitle attribútumból
    const parentForm = search.closest("form");
    const formTitle = parentForm?.dataset.title || 'default';
    
    // Képfrissítések törlése az új elem kiválasztása előtt, ha létezik a dashboard objektum
    if (window.dashboard && typeof window.dashboard.clearImageUpdates === 'function') {
        window.dashboard.clearImageUpdates(formTitle);
    }
    
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
          if (value || value == 0) {

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
    
    // Ha a mező üres, akkor töröljük a képfrissítéseket is
    if (!searchInput.value) {
        const parentForm = search.closest("form");
        const formTitle = parentForm?.dataset.title || 'default';
        
        // Képfrissítések törlése ha a keresőmező üres 
        if (window.dashboard && typeof window.dashboard.clearImageUpdates === 'function') {
            window.dashboard.clearImageUpdates(formTitle);
        }
    }
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

  // Kategória opciók feltöltése
  async function populateOptions(select, category, table) {
    try {
      const response = await fetch(`/api/auth/dashboard-search`, {
        method: "POST",
        headers: {
          'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({ category, table })
      });

      if (response.ok) {
        const responseData = await response.json();

        if (responseData.type === "ERROR" || responseData.type === "EMPTY") {
          select.innerHTML = "";
          return;
        }

        select.innerHTML = "";

        responseData.message.forEach(category => {
          select.innerHTML += `<option value='${category.name}' data-id='${category.id}'>${category.name}</option>`;
        });
      }
    } catch (error) {
      console.error("Kategóriák lekérdezési hiba:", error);
    }
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
      if (searchConfig[searchType].hasImageCards) {
        clearImageCards();
      }
    }
  });
}

// Mindegyik keresőmezőt inicializáljuk
document
  .querySelectorAll(".search")
  .forEach((search) => initializeSearch(search));

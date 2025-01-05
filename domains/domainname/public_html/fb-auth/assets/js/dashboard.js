const parentCategoryInput = document.querySelector("select#parent_category");
const parentCategoryModifyInput = document.querySelector(
  "select#parent_category_modify"
);
const categoryType = document.getElementById("type");
// const categoryTypeModify = document.getElementById('type_modify');
const pageLinks = document.querySelectorAll(".page");
const pages = document.querySelectorAll(".section-group");

let maxFileSize = 10;
let isPopupVisible = false;

function expandGroup(e) {
  let sourceElement = e.target;
  let section = sourceElement.closest("section");
  section.classList.toggle("active");
}

function togglePage(id) {
  for (let i = 0; i < pageLinks.length; i++) {
    pageLinks[i].classList.remove("active");
    pages[i].classList.remove("active");
  }
  pageLinks[id].classList.add("active");
  pages[id].classList.add("active");
}

function timerCountdown(timer, progress) {
  timer.style.width = progress + "%";
}

function hideDisplayMessages() {
  const error = document.querySelector(".error");
  const success = document.querySelector(".success");

  if (error) {
    error.addEventListener("click", () => {
      error.style.opacity = "0";
      setTimeout(() => {
        error.remove();
      }, 1000);
    });
  }

  if (success) {
    success.addEventListener("click", () => {
      success.style.opacity = "0";
      setTimeout(() => {
        success.remove();
      }, 1000);
    });
  }
}

function createConfirmPopup(message) {
  isPopupVisible = true;

  let popup = document.createElement("div");
  let popupBody = document.createElement("div");

  popup.className = "popup";
  popupBody.className = "popup-body";
  popupBody.innerHTML = `<div class='popup-icon'>
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-exclamation-diamond-fill" viewBox="0 0 16 16">
                                    <path d="M9.05.435c-.58-.58-1.52-.58-2.1 0L.436 6.95c-.58.58-.58 1.519 0 2.098l6.516 6.516c.58.58 1.519.58 2.098 0l6.516-6.516c.58-.58.58-1.519 0-2.098zM8 4c.535 0 .954.462.9.995l-.35 3.507a.552.552 0 0 1-1.1 0L7.1 4.995A.905.905 0 0 1 8 4m.002 6a1 1 0 1 1 0 2 1 1 0 0 1 0-2"/>
                                </svg>
                           </div>
                           <h2 class='popup-message'>Biztosan folytatni szeretné?</h2>
                           <div class='popup-description'>${message}</div>
                           <div class='button-group-wrapper'>
                                <div class='button-group'>
                                    <input type='button' value='Folytatás' class='confirm'>
                                    <input type='button' value='Mégsem' class='cancel'>
                                </div>
                           </div>`;

  popup.appendChild(popupBody);
  document.body.appendChild(popup);

  popup.animate(
    {
      backgroundColor: ["rgba(0,0,0,0)", "rgba(0,0,0,0.35)"],
      backdropFilter: ["blur(0px)", "blur(3px)"],
    },
    {
      duration: 300,
      fill: "forwards",
      easing: "ease",
    }
  );

  popupBody.animate(
    { transform: ["scale(0)", "scale(1)"] },
    {
      duration: 300,
      fill: "forwards",
      easing: "ease",
    }
  );

  window.addEventListener("keydown", (e) => {
    if (document.body.contains(popup) && e.code == "Escape") {
      closePopup(popup);
      isPopupVisible = false;
    }
  });

  return popup;
}

function closePopup(popup) {
  popup.animate(
    {
      backgroundColor: ["rgba(0,0,0,0.35)", "rgba(0,0,0,0)"],
      backdropFilter: ["blur(3px)", "blur(0px)"],
    },
    {
      duration: 300,
      fill: "forwards",
      easing: "ease",
    }
  );

  popup.querySelector(".popup-body").animate(
    { transform: ["scale(1)", "scale(0)"] },
    {
      duration: 300,
      fill: "forwards",
      easing: "ease",
    }
  );

  setTimeout(() => {
    document.body.removeChild(popup);
  }, 300);
}

function hasImage(form) {
  let fileInputs = form.querySelectorAll('input[type="file"]');

  for (let fileInput of fileInputs) {
    if (fileInput.files && fileInput.files.length > 0) {
      return true;
    }
  }

  return false;
}

function getImageOrientation(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();
    reader.onload = () => {
      const img = new Image();
      img.onload = () => {
        resolve(img.width >= img.height ? "horizontal" : "vertical");
      };
      img.onerror = reject;
      img.src = reader.result;
    };
    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

function getVideoOrientation(file) {
  return new Promise((resolve, reject) => {
    const reader = new FileReader();

    reader.onload = () => {
      let video = document.createElement("video");

      video.onloadedmetadata = () => {
        resolve(
          video.videoWidth >= video.videoHeight ? "horizontal" : "vertical"
        );
        video = null;
      };

      video.onerror = reject;
      video.src = reader.result;
    };

    reader.onerror = reject;
    reader.readAsDataURL(file);
  });
}

function getFileSize(file) {
  return file.size >> 20; // 2^20 - nal osztunk (B -> MB)
}

const toggleButton = document.querySelectorAll(".toggle").forEach((button) => {
  let input = button.closest(".file-input");
  let fileInput = input.querySelector("input[type=file]");
  let isTransitioning = false;

  button.addEventListener("click", () => {
    if (isTransitioning) return;
    isTransitioning = true;

    button.classList.toggle("on");
    if (button.classList.contains("on")) {
      fileInput.classList.add("visible");
      fileInput.removeAttribute("disabled");
      fileInput.setAttribute("required", true);
      setTimeout(() => {
        isTransitioning = false;
      }, 300);
    } else {
      fileInput.classList.remove("visible");
      setTimeout(() => {
        if (!button.classList.contains("on")) {
          fileInput.setAttribute("disabled", true);
          fileInput.removeAttribute("required");
        }
        isTransitioning = false;
      }, 300);
    }
  });
});

// Azok az űrlapok, amelyeknél egy beviteli mező értékétől függ, hogy megjelenjen-e a felugró ablak
const formExceptions = {
  "#form-role": {
    // Az űrlap szelektor értéke
    field: "select[name=role]", // Melyik mező értékétől függ
    value: "Administrator", // Milyen értéknél kell a felugró ablak
  },
};

// Ellenőrizzük, hogy az aktuális űrlap kivétel-e (Tehát nem minden leadáskor kell figyelmeztető üzenet, csak ha megfelel a feltételeknek)
function isFormException(form) {
  let selector =
    Object.keys(formExceptions).find((selector) => form.matches(selector)) ||
    null;
  if (selector) {
    let field = form.querySelector(formExceptions[selector]["field"]);
    if (field && field.value == formExceptions[selector]["value"]) {
      return true; // Az űrlap kivétel, és az értéke megyegyezik a kivételben szereplővel
    } else {
      return false; // Az űrlap kivétel, de a kérdéses mező értéke nem egyezik meg a kivételben szereplővel
    }
  } else {
    return null; // Az űrlap nem kivétel
  }
}

window.addEventListener("load", () => {
  let loaderForms = document.querySelectorAll("form[data-show-loader=true]");
  loaderForms.forEach((el) => {
    el.addEventListener("submit", () => {
      if (el.dataset.needsConfirm == "false") {
        toggleLoader("Képek optimalizálása... Ez több percig is eltarthat.");
      }
    });
  });

  let sectionHeaders = document.querySelectorAll(".section-header");

  for (let header of sectionHeaders) {
    header.addEventListener("click", expandGroup);
    header.addEventListener("keydown", (e) => {
      if (e.code == "Space" || e.code == "Enter") expandGroup(e);
    });
  }

  for (let page of pageLinks) {
    page.addEventListener("click", () => {
      togglePage(page.dataset.pageid);
    });
    page.addEventListener("keydown", (e) => {
      if (e.code == "Space" || e.code == "Enter")
        togglePage(page.dataset.pageid);
    });
  }

  document.querySelectorAll("input[data-type]").forEach((input) => {
    let orientation = input.dataset.orientation;
    let inputCount = input.dataset.count;
    let acceptedType = input.dataset.type;

    input.addEventListener("input", async () => {
      if (inputCount == "singular") {
        let file = input.files[0] || null;

        if (!file) {
          // Ha nem töltött fel fájlt
          input.setCustomValidity("Kérjük adjon meg egy képet!");
          input.value = "";
          input.reportValidity();
          return;
        }

        let type = file.type || null;

        if (!type.includes(acceptedType)) {
          // Ha nem képet töltött fel
          input.setCustomValidity("Kérjük képet adjon meg!");
          input.value = "";
          input.reportValidity();
          return;
        }

        if (getFileSize(file) > maxFileSize) {
          input.setCustomValidity(
            "Kérjük maximum 10 MB méretű képet töltsön fel!"
          );
          input.value = "";
          input.reportValidity();
          return;
        }

        let currentOrientation = type.includes("image")
        ? await getImageOrientation(file)
        : await getVideoOrientation(file);

        if (currentOrientation != orientation && orientation != "any") {
          input.setCustomValidity(
            `Kérjük megfelelő tájolású ${
              acceptedType == "image" ? "képet" : "videót"
              } adjon meg!`
            );
            input.value = "";
            input.reportValidity();
            return;
          }
          
          input.setCustomValidity("");
        } else {
          let isValid = true;
          for (let file of input.files) {
            if (!file) {
              // Ha nem töltött fel fájlt
              input.setCustomValidity("Kérjük adjon meg egy képet!");
              input.value = "";
            input.reportValidity();
            
            isValid = false;
            return;
          }
          
          let type = file.type || null;
          
          if (!type.includes(acceptedType)) {
            // Ha nem képet töltött fel
            input.setCustomValidity("Kérjük képet adjon meg!");
            input.value = "";
            input.reportValidity();
            
            isValid = false;
            return;
          }
          
          if (getFileSize(file) > maxFileSize) {
            input.setCustomValidity(
              "Kérjük maximum 10 MB méretű képet töltsön fel!"
            );
            input.value = "";
            input.reportValidity();
            
            isValid = false;
            return;
          }
        }

        if (isValid) input.setCustomValidity("");
      }
    });
  });

  // A megerősítést igénylő űrlapokhoz hozzácsatoljuk az eseményt, ami létrehozza felugró ablakokat beadáskor
  let confirmForms = document.querySelectorAll(
    "form[data-needs-confirm='true']"
  );

  confirmForms.forEach((form) => {
    const formSubmitter = form.querySelector("input[type=submit]");
    let formMessage = form.dataset.confirmMessage;
    form.addEventListener("submit", (e) => {
      if (isPopupVisible || isFormException(form) === false) {
        return;
      }

      // Megakadályozzuk az automatikus leadást, és létrehozzuk az előugró ablakot
      e.preventDefault();
      let popup = createConfirmPopup(formMessage);

      // A gombokra nyomáskor vagy szimuláljuk a leadást, vagy csak bezárjuk az előugró ablakot.
      popup.querySelector("input.confirm").addEventListener("click", () => {
        closePopup(popup);
        setTimeout(() => {
          if (hasImage(form))
            toggleLoader(
              "Képek optimalizálása... Ez több percig is eltarthat."
            );
          formSubmitter.click();
          isPopupVisible = false;
        }, 300);
      });

      popup.querySelector("input.cancel").addEventListener("click", () => {
        closePopup(popup);
        isPopupVisible = false;
      });
    });
  });

  hideDisplayMessages();
});

categoryType.addEventListener("change", () => {
  let selected = categoryType.value;

  if (selected == "sub") {
    parentCategoryInput.removeAttribute("disabled");
  } else {
    parentCategoryInput.setAttribute("disabled", true);
  }
});

async function populateOptions(select, category, table) {
  let data = new FormData();
  data.append("search_type", "get_categories");
  data.append("table", table);
  if (category) data.append("category_name", category);

  const response = await fetch(`../../dashboard_search.php`, {
    method: "POST",
    body: data,
  });

  if (response.ok) {
    let data = await response.json();

    if (data.type == "ERROR") {
      select.innerHTML = "";
      console.log("Hiba a kategória kereséskor: " + data.message);
      return;
    } else if (data.type == "EMPTY") {
      select.innerHTML = "";
      console.log("Hiba a kategória kereséskor: " + data.message);
      return;
    }

    select.innerHTML = "";

    data = data.message;
    data.forEach(
      (category) =>
        (select.innerHTML += `<option value='${category.name}' data-id='${category.id}'>${category.name}</option>`)
    );
  }
}

function setHiddenInput(select) {
  let value =
    select.children.length > 0
      ? select.querySelector("option:checked").dataset.id
      : "null";
  select.closest("div").querySelector("input[type=hidden]").value = value;
}

document
  .querySelectorAll("select[data-table=category]")
  .forEach(async (select) => {
    let table = select.dataset.table;

    await populateOptions(select, null, table);
    setHiddenInput(select);

    let subcategorySelect = select
      .closest(".inline-input")
      .nextElementSibling.querySelector("select[data-table=subcategory]");
    if (subcategorySelect) {
      await populateOptions(subcategorySelect, select.value, "subcategory");
      setHiddenInput(subcategorySelect);
    }

    select.addEventListener("change", async () => {
      setHiddenInput(select);
      if (subcategorySelect) {
        await populateOptions(subcategorySelect, select.value, "subcategory");
        setHiddenInput(subcategorySelect);
      }
    });
  });

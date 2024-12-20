document.querySelectorAll(".multiselect").forEach((multiSelect) => {
  const selectedItemsContainer = multiSelect.querySelector(
    ".multiselect-selectedItems"
  );
  const checkElements = multiSelect.querySelectorAll(".check");
  const optionElements = multiSelect.querySelectorAll(".option");
  const filterInput = multiSelect.querySelector(".multiselect-filter");
  let selectedItems = [];

  checkElements.forEach((e) =>
    e.addEventListener("click", () => toggleCheck(e))
  );

  // Jelölőnégyzet bepipálása, kipipálása kattintásra
  function toggleCheck(e) {
  let option = e.closest(".option");
  if (!option) return;

  let selectedValue = option.dataset.labelValue ?? null;
  let selectedId = option.dataset.value ?? null;
  e.classList.toggle("on");

  if (e.classList.contains("on")) {
    // Ha az "Összes kiválasztása"- van kiválasztva, akkor mindet kiválasztja
    if (selectedValue == "Select All") {
      checkElements.forEach((e) => { if (!e.classList.contains("on") && e.closest(".option").classList.contains("visible")) toggleCheck(e); });
    } else {
      if (selectedId) selectedItems.push(selectedId);
    }
  } else {
    // Ha az "Összes kiválasztása"- van kiválasztva, akkor minden kiválasztást töröl
    if (selectedValue == "Select All") {
      checkElements.forEach((e) => {
        if (e.classList.contains("on")) toggleCheck(e);
      });
    }
    selectedItems = selectedItems.filter((e) => e !== selectedId);
  }

  // Az "Összes kiválasztása" automatikus bepipálása, ha minden elem be van pipálva
  let allOptionsSelected = Array.from(optionElements).every(
    (e) => e.dataset.labelValue === "Select All" || e.querySelector(".check").classList.contains("on")
  );

  if (allOptionsSelected) {
    let selectAllOption = Array.from(optionElements).find(
      (e) => e.dataset.labelValue === "Select All"
    );
    if (selectAllOption && !selectAllOption.querySelector(".check").classList.contains("on")) {
      toggleCheck(selectAllOption.querySelector(".check"));
    }
  }

  multiSelect.querySelector(".selected-item-count").innerHTML =
    selectedItems.length == 0
      ? "Elemek kiválasztása"
      : `<b>${selectedItems.length}</b> kiválasztott elem`;

  // Rejtett mező értékének frissíése
  selectedItemsContainer.value =
    selectedItems.length > 0 ? selectedItems.join(",") : "null";
}

  // Szűrés alkalmazása
  filterInput.addEventListener("input", () => {
    let filterValue = filterInput.value;
    let filterRegex = new RegExp(`^.*${filterValue}.*$`);
    optionElements.forEach((e) => {
      if (e.dataset.labelValue !== "Select All") e.classList.remove("visible");
    });

    let matches = Array.from(optionElements).filter(
      (e) =>
        e.dataset.labelValue.toLowerCase().match(filterRegex) &&
        e.dataset.labelValue !== "Select All"
    );

    if (matches.length == 0) {
      multiSelect.querySelector(".no-result").classList.add("visible");
    } else {
      multiSelect.querySelector(".no-result").classList.remove("visible");
      matches.forEach((e) => e.classList.add("visible"));
    }
  });

  // Kinyitás - bezárás
  multiSelect.querySelector(".body").addEventListener("click", () => {
    multiSelect.classList.toggle("active");
  });

  window.addEventListener("click", (e) => {if (!multiSelect.contains(e.target)) multiSelect.classList.remove("active")});
});

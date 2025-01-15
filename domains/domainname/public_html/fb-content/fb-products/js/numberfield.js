document.addEventListener("click", (event) => {
    if (event.target.classList.contains("number-field-subtract")) {
        handleQuantityChange(event.target, -1);
    }

    if (event.target.classList.contains("number-field-add")) {
        handleQuantityChange(event.target, 1);
    }
});

const handleQuantityChange = (button, change) => {
    const numberField = button.closest(".number-field");

    const valueInput = numberField.querySelector("input");

    let quantity = parseInt(valueInput.value);

    const minQuantity = parseInt(valueInput.getAttribute("min")) || 1;
    const maxQuantity = parseInt(valueInput.getAttribute("max")) || Infinity;

    quantity += change;

    if (quantity < minQuantity) {
        quantity = minQuantity;
    } else if (quantity > maxQuantity) {
        quantity = maxQuantity;
    }

    valueInput.value = quantity;
};
const subtractButton = document.querySelector(".number-field-subtract");
const addButton = document.querySelector(".number-field-add");
const valueInput = document.querySelector(".number-field > input");

const minQuantity = 1;
const maxQuantity = valueInput.getAttribute("max") ?? 1; // Alap érték, a készlettől függ

let quantity = minQuantity;

const updateQuantity = () => {
    valueInput.value = quantity;
}

subtractButton.addEventListener("click", () => {
    if (quantity - 1 >= minQuantity) {
        quantity--;
    }
    updateQuantity();
});

addButton.addEventListener("click", () => {
    if (quantity + 1 <= maxQuantity) {
        quantity++;
    }
    updateQuantity();
});
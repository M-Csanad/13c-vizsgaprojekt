export const validateInput = (input) => {
    input.value = input.value.replace(/[^0-9]/g, '');

    const value = parseInt(input.value) || 0;
    const min = parseInt(input.getAttribute('min')) || 1;
    const max = parseInt(input.getAttribute('max')) || Infinity;

    if (value < min || value > max || value === 0) {
        input.style.color = '#ff6b6b';
    } else {
        input.style.color = '';
    }
};

export const handleBlur = async (input, onCartDelta = null) => {
    const min = parseInt(input.getAttribute('min')) || 1;
    const max = parseInt(input.getAttribute('max')) || Infinity;
    const oldValue = parseInt(input.getAttribute('data-last-value')) || min;
    let newValue = parseInt(input.value) || min;

    if (newValue < min) newValue = min;
    if (newValue > max) newValue = max;
    input.value = newValue;
    input.style.color = '';

    const delta = newValue - oldValue;
    if (delta !== 0 && onCartDelta) {
        try {
            await onCartDelta(delta);
        } catch (err) {
            console.error('Sikertelen kosár frissítés:', err);
            input.value = oldValue;
        }
    }
    input.setAttribute('data-last-value', newValue);
};

export const handleQuantityChange = (input, delta) => {
    const min = parseInt(input.getAttribute('min')) || 1;
    const max = parseInt(input.getAttribute('max')) || Infinity;
    let value = parseInt(input.value) || min;
    
    value += delta;
    if (value < min) value = min;
    if (value > max) value = max;
    
    input.value = value;
    input.setAttribute('data-last-value', value);
};

export const setupNumberField = (container = document, onCartDelta = null) => {
    const inputs = container.querySelectorAll('.product-quantity');
    inputs.forEach(input => {
        // Lementjük a kezdeti értéket
        input.setAttribute('data-last-value', input.value);
        input.addEventListener('blur', () => handleBlur(input, (delta) => onCartDelta(delta)));
        const numberField = input.closest('.number-field');
        const addButton = numberField.querySelector('.number-field-add');
        const subtractButton = numberField.querySelector('.number-field-subtract');
        const stock = parseInt(input.getAttribute('max')) || 0;
        const index = Array.from(container.querySelectorAll('.cart-item'))
            .indexOf(numberField.closest('.cart-item'));

        input.setAttribute('data-last-value', input.value);

        if (stock <= 0) {
            input.disabled = true;
            addButton.style.opacity = '0.5';
            subtractButton.style.opacity = '0.5';
            addButton.style.cursor = 'not-allowed';
            subtractButton.style.cursor = 'not-allowed';
            return;
        }

        input.disabled = false;
        input.addEventListener('input', (e) => validateInput(e.target));
        input.addEventListener('blur', (e) => handleBlur(e.target, 
            (operation, newValue) => onCartDelta(operation, newValue, index)));

        addButton.addEventListener('click', () => handleQuantityChange(input, 1));
        subtractButton.addEventListener('click', () => handleQuantityChange(input, -1));
    });
};

document.addEventListener('DOMContentLoaded', () => setupNumberField());
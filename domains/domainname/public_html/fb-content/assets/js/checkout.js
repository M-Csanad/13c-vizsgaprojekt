if (!gsap) {
    throw new Error("Nincs GSAP");
}

window.addEventListener("load", () => {
    document.querySelectorAll(".input-group").forEach(el => {
        const label = el.querySelector('label');
        const input = el.querySelector('input, select');

        if (!input) return;

        if (input.value != "") label.classList.add('focus');
    });
})

document.addEventListener("click", (e) => {
    if (e.target.closest('.input-group')) {
        const inputGroup = e.target.closest('.input-group');
        const label = inputGroup.querySelector('label');
        const input = inputGroup.querySelector('input, select');

        if (input.nodeName != "SELECT") label.classList.add('focus');

        inputGroup.addEventListener("focusout", () => {
            if (input.value == "") label.classList.remove('focus');
        });
    }
})
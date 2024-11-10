const checkboxes = document.querySelectorAll('.tag-checkbox');

checkboxes.forEach(input => {
    let checkbox = input.querySelector('input[type=checkbox]');

    checkbox.addEventListener("click", ()=>{
        input.classList.toggle('active');
    }); 
});
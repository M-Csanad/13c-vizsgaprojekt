const checkboxes = document.querySelectorAll('.tag-checkbox');
const tagWrapper = document.querySelectorAll('.tag-body');

checkboxes.forEach(input => {
    let checkbox = input.querySelector('input[type=checkbox]');

    checkbox.addEventListener("click", ()=>{
        input.classList.toggle('active');
    }); 
});

tagWrapper.forEach(wrapper => {
    let mouseDown = false;
    let startX = 0;
    let startLeft = 0;
    let body = wrapper.firstElementChild;

    wrapper.addEventListener('mousedown', (e)=>{
        mouseDown=true;
        startX = e.clientX;
        startLeft = body.scrollLeft;
    });
    wrapper.addEventListener('mouseup', ()=>{mouseDown=false;});
    wrapper.addEventListener('mouseleave', ()=>{mouseDown=false;});
    wrapper.addEventListener('mousemove', (e)=>{
        if (!mouseDown) return;

        let dx =  e.clientX - startX;
        window.requestAnimationFrame(()=>{
            body.scrollTo({
                left: startLeft - dx / 2,
                behavior: "smooth"
            });
        }); 
    });
});
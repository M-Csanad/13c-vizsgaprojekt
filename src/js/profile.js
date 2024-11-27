let pageLinks;

function togglePage(id) {
    console.log(id);
    for (let i=0; i < pageLinks.length; i++) {
        pageLinks[i].classList.remove("active");
        // pages[i].classList.remove("active");
    }
    pageLinks[id].classList.add("active");
    // pages[id].classList.add("active");
}


window.addEventListener("load", () => {
    pageLinks = document.querySelectorAll(".page");
    for (let page of pageLinks) {
        console.log(page.dataset.pageid,page);
        page.addEventListener("click", ()=>{ togglePage(page.dataset.pageid); });
        page.addEventListener("keydown", (e) => { if (e.code=="Space" || e.code=="Enter") togglePage(page.dataset.pageid) });
    }

    let main = document.querySelector(".main");
    let animating = false;
    let borderElements = document.querySelectorAll(".dynamic-border")

    if (main) {
        main.addEventListener("mousemove", (e) => {
            if (!animating) {
                animating = true;
                requestAnimationFrame(() => {
                    borderElements.forEach(element => {
                        if (element.style.getPropertyValue("--radius") == "0px") {
                            animateRadius(element, 0, 400, 500);
                        }
                        const rect = element.getBoundingClientRect();
                        const x = e.clientX - rect.left;
                        const y = e.clientY - rect.top;

                        element.style.setProperty("--mouse-x", `${x}px`);
                        element.style.setProperty("--mouse-y", `${y}px`);
                    })
                    animating = false;
                });
            }
        });
    }
});

function animateRadius(element, start, end, duration) {
    const startTime = performance.now();

    function step(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const currentRadius = start + (end - start) * progress;

        element.style.setProperty("--radius", `${currentRadius}px`);

        if (progress < 1) {
            requestAnimationFrame(step);
        }
    }

    requestAnimationFrame(step);
}
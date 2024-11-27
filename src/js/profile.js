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
        page.addEventListener("click", ()=>{ togglePage(page.dataset.pageid); });
        page.addEventListener("keydown", (e) => { if (e.code=="Space" || e.code=="Enter") togglePage(page.dataset.pageid) });
    }

    let main = document.querySelector(".main");
    let animating = false;
    let borderElements = document.querySelectorAll(".dynamic-border");
    let logout = document.querySelector(".logout");

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
                    });
                    animating = false;
                });
            }
        });
    }

    logout.addEventListener("mouseover", () => {
        animateColor(borderElements, "#4d4d4d", "#b92424", 500);
    });

    logout.addEventListener("mouseleave", () => {
        animateColor(borderElements, "#b92424", "#4d4d4d", 500);
    });
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

function animateColor(element, startColor, endColor, duration) {
    const startTime = performance.now();

    function parseColor(color) {
        const hex = color.replace("#", "");
        return {
            r: parseInt(hex.substring(0, 2), 16),
            g: parseInt(hex.substring(2, 4), 16),
            b: parseInt(hex.substring(4, 6), 16),
        };
    }

    function interpolateColor(start, end, t) {
        return {
            r: Math.round(start.r + (end.r - start.r) * t),
            g: Math.round(start.g + (end.g - start.g) * t),
            b: Math.round(start.b + (end.b - start.b) * t),
        };
    }

    const startRGB = parseColor(startColor);
    const endRGB = parseColor(endColor);

    function step(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const currentColor = interpolateColor(startRGB, endRGB, progress);

        if (typeof element == "object") {
            console.log("asd")
            element.forEach(e => e.style.setProperty("--color", `rgb(${currentColor.r}, ${currentColor.g}, ${currentColor.b})`));
        }
        else {
            element.style.setProperty("--color", `rgb(${currentColor.r}, ${currentColor.g}, ${currentColor.b})`);
        }

        if (progress < 1) {
            requestAnimationFrame(step);
        }
    }

    requestAnimationFrame(step);
}

let pageLinks;
let pages;

function togglePage(id) {
    for (let i=0; i < pageLinks.length; i++) {
        pageLinks[i].classList.remove("active");
        pages[i].classList.remove("active");
    }
    pageLinks[id].classList.add("active");
    pages[id].classList.add("active");
}

pageLinks = document.querySelectorAll(".page");
pages = document.querySelectorAll(".content-page");
for (let page of pageLinks) {
    page.addEventListener("click", () => {
        togglePage(page.dataset.pageid);
    });
    page.addEventListener("keydown", (e) => {
        if (e.code === "Space" || e.code === "Enter") togglePage(page.dataset.pageid);
    });
}


window.addEventListener("load", () => {
    
    let main = document.querySelector(".main");
    let borderElements = document.querySelectorAll(".dynamic-border");
    let logout = document.querySelector(".logout");
    let dashboard = document.querySelector(".dashboard");
    let saveButtons = document.querySelectorAll(".save");
    let editButtons = document.querySelectorAll(".edit");
    let dyk = document.querySelector(".didyouknow");

    saveButtons.forEach(e => e.addEventListener("click", () => {
        e.closest(".field-body").querySelector("input").disabled = true;
    }));

    editButtons.forEach(e => e.addEventListener("click", () => {
        e.closest(".field-body").querySelector("input").disabled = false;
    }));

    let animating = false;
    let insideCustomStyleElement = false;

    const radiusAnimationTokens = new WeakMap();
    const colorAnimationTokens = new WeakMap();

    function startRadiusAnimation(element, animationFunction) {
        const token = Symbol();
        radiusAnimationTokens.set(element, token);

        animationFunction(() => radiusAnimationTokens.get(element) === token);
    }

    function startColorAnimation(element, animationFunction) {
        const token = Symbol();
        colorAnimationTokens.set(element, token);

        animationFunction(() => colorAnimationTokens.get(element) === token);
    }

    function animateRadius(element, start, end, duration, isValid) {
        const startTime = performance.now();
    
        function step(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(Math.max(elapsed / duration, 0), 1);
            const currentRadius = start + (end - start) * progress;
    
            element.style.setProperty("--radius", `${currentRadius}px`);
    
            if (progress < 1 && isValid()) {
                requestAnimationFrame(step);
            }
        }
    
        requestAnimationFrame(step);
    }
    
    function animateColor(element, startColor, endColor, duration, isValid) {
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
    
            element.style.setProperty("--color", `rgb(${currentColor.r}, ${currentColor.g}, ${currentColor.b})`);
    
            if (progress < 1 && isValid()) {
                requestAnimationFrame(step);
            }
        }
    
        requestAnimationFrame(step);
    }

    function rgbToHex(rgb) {
        const result = rgb.match(/\d+/g).map((num) => {
            const hex = parseInt(num).toString(16);
            return hex.length === 1 ? "0" + hex : hex;
        });
        return `#${result.join("")}`;
    }

    // A függvény segítségével az elemre hover eseményt csatolunk, 
    // ami a dinamikus keretek színét animálja meg a kívánt színre.
    function setHoverStyle(element, color = null) {
        
        if (!element || !color) return;
        
        // Ha a szín nem hex-színként van megadva, akkor kilépünk.
        if (!/^#[A-Fa-f0-9]{6}$/.test(color)) return;
        
        element.addEventListener("mouseover", () => {
            insideCustomStyleElement = true;
            borderElements.forEach(e => {
                const start = getComputedStyle(e).getPropertyValue("--color").trim();
                const startHex = rgbToHex(start);
    
                startColorAnimation(e, (isValid) =>
                    animateColor(e, startHex, color, 500, isValid)
                );
            });
        });
        
        element.addEventListener("mouseleave", () => {
            borderElements.forEach(e => {
                const start = getComputedStyle(e).getPropertyValue("--color").trim();
                const startHex = rgbToHex(start);
                
                startColorAnimation(e, (isValid) =>
                    animateColor(e, startHex, "#4d4d4d", 500, isValid)
                );
                insideCustomStyleElement = false;
            });
        });
    }
    

    main.addEventListener("mousemove", (e) => {
        if (borderElements[0].style.getPropertyValue("--radius") == "0px") {
            borderElements.forEach(element => {
                startRadiusAnimation(element, (isValid) =>
                    animateRadius(element, 0, 400, 500, isValid)
                );

                if (!insideCustomStyleElement) {
                    const start = getComputedStyle(element).getPropertyValue("--color").trim();
                    const startHex = rgbToHex(start);
        
                    startColorAnimation(element, (isValid) =>
                        animateColor(element, startHex, "#4d4d4d", 500, isValid)
                    );
                }
            });
        }

        if (!animating) {
            animating = true;
            requestAnimationFrame(() => {
                borderElements.forEach(element => {
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

    main.addEventListener("mouseleave", () => {
        borderElements.forEach(element => {
            const start = Number(element.style.getPropertyValue("--radius").replace("px", "") || 0);

            startRadiusAnimation(element, (isValid) => 
                animateRadius(element, start, 0, 500, isValid)
            );
        });
    });

    main.addEventListener("mouseenter", () => {
        borderElements.forEach(element => {
            const start = Number(element.style.getPropertyValue("--radius").replace("px", "") || 0);
            startRadiusAnimation(element, (isValid) =>
                animateRadius(element, start, 400, 500, isValid)
            );

            if (!insideCustomStyleElement) {
                const start = getComputedStyle(element).getPropertyValue("--color").trim();
                const startHex = rgbToHex(start);
    
                startColorAnimation(element, (isValid) =>
                    animateColor(element, startHex, "#4d4d4d", 500, isValid)
                );
            }
        });
    }); 

    window.addEventListener("mouseenter", () => {
        console.log("window enter");
    })

    setHoverStyle(logout, "#b92424");
    setHoverStyle(dashboard, "#2797ca");
    setHoverStyle(dyk, "#5dbc55");

    document.getElementById("back-button").addEventListener("click", ()=>{
        const referrer = document.referrer;
        const origin = window.location.origin;

        if (referrer && referrer.startsWith(origin)) {
            history.back();
        } else {
            window.location.href = './';
        }
    })
});
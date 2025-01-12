const loadingImages = document.querySelectorAll("img");
const imagePromises = Array.from(loadingImages).map((img) => {
    if (img.complete || img.getAttribute("loading") == "lazy") return Promise.resolve();
    return new Promise((resolve) => {
        img.onload = resolve;
        img.onerror = resolve;
    });
});

let isAnimating = false;

window.addEventListener("load", () => {
    let isBackForwardNav = false;
    if (window.performance && window.performance.getEntriesByType) {
        const navigationEntries = window.performance.getEntriesByType('navigation');
    
        if (navigationEntries.length > 0) {
            const navigationType = navigationEntries[0].type;
    
            if (navigationType === 'back_forward') {
                isBackForwardNav = true;
            }
        }
    }
    const ease = "power3.inOut";
    const inParams = {
        scaleY: 0, 
        duration: 1,
        stagger: {
            each: 0.05,
            from: "start",
            grid: "auto",
            axis: "x"
        },
        ease: ease
    }
    const outParams = {
        scaleY: 1, 
        duration: 1,
        stagger: {
            each: 0.05,
            from: "start",
            grid: "auto",
            axis: "x"
        },
        ease: ease
    }

    document.querySelectorAll("a").forEach((link) => {
        if (!link.href || link.href.includes("#")) return;
        link.addEventListener("click", (e) => {
            if (e.ctrlKey || e.metaKey) {
                return; // CTRL + kattintás esetén marad az alap működés
            }

            e.preventDefault();
            
            if (isAnimating) return;

            isAnimating = true;
            const href = link.href;

            if (href.includes("dashboard")) {
                window.location.href = href;
                return;
            }

            if (href && !href.startsWith("#") && href !== window.location.pathname) {
                animatePageTransition(outParams).then(() => {
                    window.location.href = href;
                });
            }
        });
    });

    Promise.all(imagePromises).then(() => {
        setTimeout(() => {
            revealPageTransition(inParams).then(() => { 
                isAnimating = false;
                gsap.set(".block", { visibility: "hidden" });
            });
        }, 100)
    });
});

function revealPageTransition(inParams) {
    return new Promise((resolve) => {
        isAnimating = true;
        gsap.set(".block", { scaleY: 1 });

        gsap.to(".layer-0 .block", { 
            ...inParams,
        });
        gsap.to(".quote > .char", {
            y: "-100%",
            stagger: {
                each: 0.005,
                from: "start",
                grid: "auto",
                axis: "x"
            },
            duration: 0.5,
            ease: inParams.ease,
            delay: 0.1
        })
        gsap.to(".hero > .char", {
            y: "-100%",
            stagger: {
                each: 0.01,
                from: "start",
                grid: "auto",
                axis: "x"
            },
            duration: 1,
            ease: inParams.ease,
            delay: 0.1
        });
        gsap.to(".layer-1 .block", { 
            ...inParams,
            delay: 0.1,
        });
        gsap.to(".layer-2 .block", { 
            ...inParams,
            delay: 0.2,
        });

        gsap.to(".layer-3 .block", { 
            ...inParams,
            delay: 0.25,
            onComplete: resolve
        });
    });
}

function animatePageTransition(outParams) {
    return new Promise((resolve) => {
        gsap.set(".transition-background", { opacity: 0, visibility: "visible"});
        gsap.set(".hero > .char, .quote > .char", { opacity: 0, y: "0%" });

        gsap.to(".transition-background", { 
            opacity: 1,
            duration: 0.3,
            ease: outParams.ease
        })
        gsap.to(".hero > .char, .quote > .char", {
            opacity: 1,
            duration: 0.3,
            ease: outParams.ease,
            onComplete: resolve
        });
    });
}
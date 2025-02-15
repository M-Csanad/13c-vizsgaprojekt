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
    const ease = "power2.inOut";
    const inParams = {
        scaleY: 0, 
        duration: 0.3,
        ease: ease
    }
    const outParams = {
        scaleY: 1, 
        duration: 0.3,
        ease: ease
    }

    document.addEventListener("click", (e) => {
        if (e.target.nodeName === "A" || e.target.closest('a')) {
            const link = e.target.closest('a');

            if (!link.href || link.href.includes("#")) return;

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
        }
    });

    // document.querySelectorAll("a").forEach((link) => {
    //     if (!link.href || link.href.includes("#")) return;
    //     link.addEventListener("click", (e) => {
    //         if (e.ctrlKey || e.metaKey) {
    //             return; // CTRL + kattintás esetén marad az alap működés
    //         }

    //         e.preventDefault();
            
    //         if (isAnimating) return;

    //         isAnimating = true;
    //         const href = link.href;

    //         if (href.includes("dashboard")) {
    //             window.location.href = href;
    //             return;
    //         }

    //         if (href && !href.startsWith("#") && href !== window.location.pathname) {
    //             animatePageTransition(outParams).then(() => {
    //                 window.location.href = href;
    //             });
    //         }
    //     });
    // });

    Promise.all(imagePromises).then(() => {
        setTimeout(() => {
            revealPageTransition(inParams).then(() => { 
                isAnimating = false;
                gsap.set(".transition-background", { visibility: "hidden" });
            });
        }, 200)
    });
});

function revealPageTransition(inParams) {
    return new Promise((resolve) => {
        isAnimating = true;
        gsap.to(".transition-background", { 
            opacity: 0,
            duration: 0.3,
            ease: inParams.ease,
            onComplete: resolve
        })
        gsap.to(".quote > .char", {
            opacity: 0,
            // stagger: {
            //     each: 0.005,
            //     from: "start",
            //     grid: "auto",
            //     axis: "x"
            // },
            duration: 0.3,
            ease: inParams.ease,
        })
        gsap.to(".hero > .char", {
            opacity: 0,
            // stagger: {
            //     each: 0.01,
            //     from: "start",
            //     grid: "auto",
            //     axis: "x"
            // },
            duration: 0.3,
            ease: inParams.ease,
            
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
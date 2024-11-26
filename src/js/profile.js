window.addEventListener("load", () => {
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
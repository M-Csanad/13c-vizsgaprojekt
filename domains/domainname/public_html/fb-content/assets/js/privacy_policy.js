document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href').substring(1);
        const targetElement = document.getElementById(targetId);
        
        if (targetElement) {
            if (!lenis) return;
            lenis.scrollTo(targetElement, {
                offset: 0,
                duration: 1.2,
            });
        }
    });
});
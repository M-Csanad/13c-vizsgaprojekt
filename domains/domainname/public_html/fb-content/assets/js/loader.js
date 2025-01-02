(function () {
  // Ensure overlay is available immediately from HTML
  const loadingOverlay = document.getElementById("loadingOverlay");

  document.body.classList.add("loading");

  // Wait until all images are loaded
  const images = document.querySelectorAll("img");
  const imagePromises = Array.from(images).map((img) => {
    if (img.complete) return Promise.resolve();
    return new Promise((resolve) => {
      img.onload = resolve;
      img.onerror = resolve; // Consider the image "loaded" even if it fails
    });
  });

  Promise.all(imagePromises).then(() => {
    setTimeout(() => {
      // Smoothly fade out the overlay
      loadingOverlay.style.transition = "opacity 0.8s ease";
      loadingOverlay.style.opacity = "0";

      // Remove loading state from body
      document.body.classList.remove("loading");

      // Remove the overlay after the fade-out
      loadingOverlay.addEventListener("transitionend", () => {
        loadingOverlay.remove();
      });
    }, 500);
  });
})();

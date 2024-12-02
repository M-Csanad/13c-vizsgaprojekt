function toggleLoader(message) {
  document.body.style.overflow = "hidden";
  if (document.querySelector(".loader-SuperOverlay")) return;

  const loaderOverlay = document.createElement("div");
  loaderOverlay.className = "loader-SuperOverlay";

  const loaderContent = document.createElement("div");
  loaderContent.className = "loader-content";

  const loaderLogo = document.createElement("div");
  loaderLogo.className = "loader-logo";

  const loaderLogoInner = document.createElement("div");
  loaderLogoInner.className = "loader-logo-inner";

  loaderLogo.appendChild(loaderLogoInner);
  
  const loaderMessage = document.createElement("div");
  loaderMessage.className = "loader-message";
  loaderMessage.textContent = message;
  
  loaderContent.appendChild(loaderLogo);
  loaderContent.appendChild(loaderMessage);
  loaderContent.innerHTML += `<iframe src="./web/dino/index.html" title="Chrome Dinó játék" id="dino-game" height="300" width="70%"></iframe>`;
  
  
  loaderOverlay.appendChild(loaderContent);
  document.body.appendChild(loaderOverlay);
  loaderOverlay.focus();

  setTimeout(() => loaderOverlay.classList.add("active"), 500);

  const disableSpaceKey = (event) => {
    const activeElement = document.activeElement;

    const isInsideLoader = loaderOverlay.contains(activeElement);
    if ([" ", "Enter", "Tab"].includes(event.key) && !isInsideLoader) {
      event.preventDefault();
    }
  };

  document.addEventListener("keydown", disableSpaceKey);
}
function toggleLoader(message) {
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
  
    loaderOverlay.appendChild(loaderContent);
    document.body.appendChild(loaderOverlay);
  
    setTimeout(() => loaderOverlay.classList.add("active"), 50);
    document.body.style.overflow = "hidden";
  }
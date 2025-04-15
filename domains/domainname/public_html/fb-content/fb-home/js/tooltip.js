document.querySelectorAll(".tag").forEach((elem) => {
  setupTooltip(elem);
});

function setupTooltip(tag) {
  const tooltipText = tag.querySelector("img").alt;
  if (!tooltipText) return;

  const tooltip = document.createElement("div");
  tooltip.classList.add("tooltip");
  tooltip.textContent = tooltipText;
  tooltip.style.setProperty("--visible", "0");
  tooltip.style.display = "none";

  document.body.appendChild(tooltip);

  let isTooltipActive = false;
  let tooltipAnimationId = null;

  const updateTooltipPosition = () => {
    // Csak akkor frissíti a pozíciót, ha a tooltip aktív
    if (!isTooltipActive) return;

    const rect = tag.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();

    tooltip.classList.remove("right", "left");
    let top = rect.top - tooltipRect.height - 8;
    let left = rect.left + rect.width / 2 - tooltipRect.width / 2;

    // Ellenőrzi, hogy a tooltip nem lóg-e le felül a képernyőről
    if (top < 0) {
      top = rect.bottom + 8;
    }

    if (left + tooltipRect.width > window.innerWidth) {
      left = window.innerWidth - tooltipRect.width - 30;
      tooltip.classList.add("right");
    }
    if (left < 0) {
      left = rect.left;
      tooltip.classList.add("left");
    }
    tooltip.style.position = "fixed";
    tooltip.style.top = `${top}px`;
    tooltip.style.left = `${left}px`;
    tooltipAnimationId = null;
  };

  tag.addEventListener("mouseenter", () => {
    isTooltipActive = true;
    tooltip.style.display = "block";
    tooltip.getBoundingClientRect();

    updateTooltipPosition();
    setTimeout(() => {
      if (isTooltipActive) {
        tooltip.style.setProperty("--visible", "1");
      }
    }, 10);
  });

  tag.addEventListener("mouseleave", () => {
    isTooltipActive = false;
    tooltip.style.setProperty("--visible", "0");

    setTimeout(() => {
      if (!isTooltipActive) {
        tooltip.style.display = "none";
      }
    }, 300);

    if (tooltipAnimationId) {
      cancelAnimationFrame(tooltipAnimationId);
      tooltipAnimationId = null;
    }
  });

  document.addEventListener("mousemove", (e) => {
    if (!isTooltipActive) return;


    const rect = tag.getBoundingClientRect();
    const buffer = 10;

    if (
      e.clientX < rect.left - buffer ||
      e.clientX > rect.right + buffer ||
      e.clientY < rect.top - buffer ||
      e.clientY > rect.bottom + buffer
    ) {

      isTooltipActive = false;
      tooltip.style.setProperty("--visible", "0");

      setTimeout(() => {
        tooltip.style.display = "none";
      }, 300);

      if (tooltipAnimationId) {
        cancelAnimationFrame(tooltipAnimationId);
        tooltipAnimationId = null;
      }
    }
  });

  window.addEventListener("scroll", () => {
    if (!isTooltipActive) return;
    updateTooltipPosition();

    if (tooltipAnimationId) {
      cancelAnimationFrame(tooltipAnimationId);
    }

    tooltipAnimationId = requestAnimationFrame(() => {
      updateTooltipPosition();
      tooltipAnimationId = null;
    });
  });

  window.addEventListener("resize", () => {
    if (isTooltipActive) {
      updateTooltipPosition();
    }
  });


  window.addEventListener("beforeunload", () => {
    if (tooltip && tooltip.parentNode) {
      tooltip.parentNode.removeChild(tooltip);
    }
  });
}

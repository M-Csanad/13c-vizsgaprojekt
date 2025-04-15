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
  const imgElem = tag.querySelector("img");
  tag.insertBefore(tooltip, imgElem);

  let isTooltipActive = false;
  let tooltipAnimationId = null;

  const updateTooltipPosition = () => {
    const rect = tag.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();

    tooltip.classList.remove("right", "left");
    let top = rect.top - tooltipRect.height - 8;
    let left = rect.left + rect.width / 2 - tooltipRect.width / 2;

    if (top < 0) top = rect.bottom + 8;
    if (left + tooltipRect.width > window.innerWidth) {
      left = window.innerWidth - tooltipRect.width - 30;
      tooltip.classList.add("right");
    }
    if (left < 0) {
      left = rect.left;
      tooltip.classList.add("left");
    }

    tooltip.style.top = `${top}px`;
    tooltip.style.left = `${left}px`;
    tooltipAnimationId = null;
  };

  tag.addEventListener("mouseenter", () => {
    isTooltipActive = true;
    tooltip.style.setProperty("--visible", "1");
    updateTooltipPosition();
  });

  tag.addEventListener("mouseleave", () => {
    isTooltipActive = false;
    setTimeout(() => {
      if (!isTooltipActive) {
        tooltip.style.setProperty("--visible", "0");
        tooltip.style.top = "auto";
        tooltip.style.left = "auto";
      }
    }, 300);
    if (tooltipAnimationId) {
      cancelAnimationFrame(tooltipAnimationId);
      tooltipAnimationId = null;
    }
  });

  window.addEventListener("scroll", () => {
    if (getComputedStyle(tooltip).getPropertyValue("--visible") === "1") {
      if (!tooltipAnimationId) {
        tooltipAnimationId = requestAnimationFrame(updateTooltipPosition);
      }
    }
  });
}

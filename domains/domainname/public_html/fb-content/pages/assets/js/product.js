// Egyszerű 5 csillagos értékelés generáló segédfüggvény
function generateStars(element) {
  let rating = element.dataset.rating ?? 0;

  const fullStars = Math.floor(rating);
  const halfStar = rating % 1 >= 0.5;
  const totalStars = 5;

  for (let i = 0; i < totalStars; i++) {
    const star = document.createElement("span");
    if (i < fullStars) {
      star.classList.add("filled");
    } else if (i === fullStars && halfStar) {
      star.classList.add("half");
    }
    element.appendChild(star);
  }
}

// Értékelés generálása
const avgReviewElement = document.querySelector(".avg-review");

const rating = parseFloat(avgReviewElement.getAttribute("data-rating"));
const reviewCount = parseInt(avgReviewElement.getAttribute("data-reviews"));

const ratingNumber = document.createElement("div");
const starsContainer = document.createElement("div");

ratingNumber.classList.add("rating-number");
ratingNumber.textContent = rating.toFixed(1);
starsContainer.classList.add("stars");

const fullStars = Math.floor(rating);
const halfStar = rating % 1 >= 0.5;
const totalStars = 5;

for (let i = 0; i < totalStars; i++) {
  const star = document.createElement("span");
  if (i < fullStars) {
    star.classList.add("filled");
  } else if (i === fullStars && halfStar) {
    star.classList.add("half");
  }
  starsContainer.appendChild(star);
}

const reviewCountElement = document.createElement("div");
const starsAndReviews = document.createElement("div");

reviewCountElement.classList.add("review-count");
starsAndReviews.classList.add("stars-and-reviews");
reviewCountElement.textContent = `${reviewCount} értékelés`;

starsAndReviews.appendChild(starsContainer);
starsAndReviews.appendChild(reviewCountElement);
avgReviewElement.appendChild(ratingNumber);
avgReviewElement.appendChild(starsAndReviews);

// A termékképek mögött megjelenő dinamikus háttér frissítése
function updateDynamicBackground() {
  const activeImage = document.querySelector(".images img.active");

  if (activeImage) {
    const color = getDominantColor(activeImage);
    activeImage.parentElement.style.boxShadow = `0px 0px 150px ${color}`;
  }
}

// Ahhoz, hogy a termékképnek be tudjunk állítani egy megfelelő színű háttérszínt,
// egy Canvas trükk segítségével kinyerjük a 'domináns' színt.
function getDominantColor(image) {
  const canvas = document.createElement("canvas");
  const ctx = canvas.getContext("2d");

  // Canvas méretének lecsökkentése 1x1 pixelre
  canvas.width = 1;
  canvas.height = 1;

  // A képünket beillesztjük a Canvasba -> Domináns szín fog csak megjelenni.
  ctx.drawImage(image, 0, 0, canvas.width, canvas.height);

  // A szín kinyerése
  const imageData = ctx.getImageData(0, 0, 1, 1).data;
  let [r, g, b] = imageData;

  return `#${((1 << 24) | (r << 16) | (g << 8) | b) // RGB -> Hex
    .toString(16)
    .slice(1)
    .toUpperCase()}`;
}

// Címkék felett megjelenő tooltip generálása
const tags = document.querySelectorAll(".tag");

tags.forEach((tag) => {
  const tooltipText = tag.querySelector("img").alt;

  if (!tooltipText) return;

  const tooltip = document.createElement("div");
  tooltip.classList.add("tooltip");
  tooltip.textContent = tooltipText;
  tooltip.style.setProperty("--visible", 0);
  tag.appendChild(tooltip);

  let isTooltipActive = false;
  let animationId = null;

  const updateTooltipPosition = () => {
    const rect = tag.getBoundingClientRect();
    const tooltipRect = tooltip.getBoundingClientRect();

    tooltip.classList.remove("right", "left");
    tooltip.style.top = "auto";
    tooltip.style.left = "auto";

    let top = rect.top - tooltipRect.height - 8;
    let left = rect.left + rect.width / 2 - tooltipRect.width / 2;

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

    tooltip.style.top = `${top}px`;
    tooltip.style.left = `${left}px`;

    animationId = null;
  };

  const showTooltip = () => {
    isTooltipActive = true;
    tooltip.style.setProperty("--visible", 1);
    updateTooltipPosition();
  };

  const hideTooltip = () => {
    isTooltipActive = false;
    setTimeout(() => {
      if (!isTooltipActive) {
        tooltip.style.setProperty("--visible", 0);
        tooltip.style.top = "auto";
        tooltip.style.left = "auto";
      }
    }, 300);
    if (animationId) {
      cancelAnimationFrame(animationId);
      animationId = null;
    }
  };

  tag.addEventListener("mouseenter", showTooltip);
  tag.addEventListener("mouseleave", hideTooltip);

  const requestTooltipUpdate = () => {
    if (!animationId) {
      animationId = requestAnimationFrame(updateTooltipPosition);
    }
  };

  window.addEventListener("scroll", () => {
    if (getComputedStyle(tooltip).getPropertyValue("--visible") == true) {
      requestTooltipUpdate();
    }
  });
});

const navigatorImages = document.querySelectorAll(".navigator-image");
let isAnimating = false;
const allImages = document.querySelectorAll(".images .image");
navigatorImages.forEach((navigatorImage, index) => {
  allImages[index].style.zIndex = navigatorImages.length - 1 - index;
  navigatorImage.addEventListener("click", () => {
    if (isAnimating) return;
    isAnimating = true;

    const currentImage = document.querySelector(".image.active");
    const targetImage = allImages[index];

    if (currentImage == targetImage) {
      isAnimating = false;
      return;
    }

    targetImage.classList.add("active");
    currentImage.classList.remove("active");

    currentImage.style.zIndex = 10;
    targetImage.style.zIndex = 100;

    targetImage.animate(
      {
        scale: [1.2, 1],
        opacity: [0, 1],
      },
      {
        fill: "forwards",
        duration: 300,
        easing: "ease",
      }
    );

    updateDynamicBackground();
    setTimeout(() => {
      currentImage.style.zIndex = -1;
      isAnimating = false;
    }, 300);
  });
});

function requestZoomUpdate(e) {
  const wrapper = e.target.parentElement;

  const rect = wrapper.getBoundingClientRect();
  const imageRect = e.target.getBoundingClientRect();

  // Kurzor relatív pozíciója a .wrapper-hez képest.
  const relX = (e.clientX - rect.left) / rect.width - 0.5; // [-0.5 ... 0.5]
  const relY = (e.clientY - rect.top) / rect.height - 0.5; // [-0.5 ... 0.5]

  // Maximális mozgás mindkét tengelyen (px)
  const maxMoveX = (imageRect.width - rect.width) / 2;
  const maxMoveY = (imageRect.height - rect.height) / 2;

  // Elmozdulás kiszámítása, normalizálás
  const moveX = 2 * relX * maxMoveX;
  const moveY = 2 * relY * maxMoveY;

  if (e.target.classList.contains("image")) {
    e.target.style.transition = "transform 0.1s ease-out";
    e.target.style.transform = `translate(${-moveX}px, ${-moveY}px) scale(1.2)`;
  }
}

const updateImageLeft = (image) => {
  const imageRect = image.getBoundingClientRect();
  const wrapperRect = image.parentElement.getBoundingClientRect();
  image.style.left = imageRect.width * -0.5 - wrapperRect.width * -0.5 + "px";
};

let isZoom = false;
let frameId = null;

allImages.forEach((image) => {
  image.parentElement.addEventListener("mouseenter", () => {
    if (image.classList.contains("active")) {
      console.log("first");
      isZoom = true;
      image.style.transition = "transform 0.5s ease-out";
      image.style.transform = "scale(1.2)";
      image.classList.add("zoomed");
    }
  });

  image.parentElement.addEventListener("mousemove", (e) => {
    if (isZoom) {
      if (!frameId) {
        frameId = requestAnimationFrame(() => {
          if (isZoom) requestZoomUpdate(e);
          frameId = null;
        });
      }
    }
  });

  image.addEventListener("mouseleave", () => {
    if (image.classList.contains("active")) {
      isZoom = false;
      image.style.transition = "transform 0.5s ease-out";
      image.style.transform = "scale(1)";
      image.classList.remove("zoomed");
    }
  });
});

function navigatorScroll(direction, scrollElement) {
  const scrollAmount = 200; // Desired scroll amount
  const maxScrollLeft = scrollElement.scrollWidth - scrollElement.offsetWidth;

  // Determine the new scroll position
  let newScrollPosition =
    direction === "left"
      ? Math.max(scrollElement.scrollLeft - scrollAmount, 0)
      : Math.min(scrollElement.scrollLeft + scrollAmount, maxScrollLeft);

  // Use scrollTo or scrollBy to respect snap points
  scrollElement.scrollTo({
    left: newScrollPosition,
    behavior: "smooth",
  });
}
function updateScrollbar(scrollbar, scrollElement) {
  const progress =
    (scrollElement.scrollLeft + scrollElement.offsetWidth) /
    scrollElement.scrollWidth;

  scrollbar.style.width = progress * 100 + "%";

  if (
    scrollElement.scrollWidth == scrollElement.offsetWidth ||
    window.innerWidth <= 414
  ) {
    scrollElement.style.setProperty("--after-end", "transparent");
  } else {
    if (scrollElement.style.getPropertyValue("--after-end") != "#1d1d1d") {
      scrollElement.style.setProperty("--after-end", "#1d1d1d");
    }
  }
}

const productImageScrollbar = document.querySelector(
  ".navigator > .navigator-arrows > .navigator-progress > .progressbar"
);
const productImageCarousel = document.querySelector(
  ".navigator > .navigator-images"
);
const productScrollbar = document.querySelector(
  ".product-navigator > .navigator-progress > .progressbar"
);
const productCarousel = document.querySelector(".products");

document.querySelectorAll(".arrow").forEach((arrow) => {
  arrow.addEventListener("click", (e) => {
    navigatorScroll(
      e.target.classList.contains("arrow-left") ? "left" : "right",
      productImageCarousel
    );
  });
});

document.querySelectorAll(".navigator-button").forEach((element) =>
  element.addEventListener("click", (e) => {
    navigatorScroll(
      e.target.classList.contains("navigator-left") ? "left" : "right",
      productCarousel
    );
  })
);

let scrollFrameId = null;
productImageCarousel.addEventListener("scroll", () => {
  if (!scrollFrameId) {
    scrollFrameId = setTimeout(() => {
      updateScrollbar(productImageScrollbar, productImageCarousel);
      scrollFrameId = null;
    }, 100);
  }
});

scrollFrameId = null;
productCarousel.addEventListener("scroll", () => {
  if (!scrollFrameId) {
    scrollFrameId = setTimeout(() => {
      updateScrollbar(productScrollbar, productCarousel);
      scrollFrameId = null;
    }, 100);
  }
});

window.addEventListener("resize", () => {
  allImages.forEach((image) => updateImageLeft(image));
  updateScrollbar(productScrollbar, productCarousel);
  updateScrollbar(productImageScrollbar, productImageCarousel);
});

window.addEventListener("load", () => {
  allImages.forEach((image) => updateImageLeft(image));
  updateScrollbar(productScrollbar, productCarousel);
  updateScrollbar(productImageScrollbar, productImageCarousel);

  setTimeout(() => {
    updateDynamicBackground();
  }, 10);
});

// Csillagok generálása
document.querySelectorAll(".review-stars").forEach((el) => generateStars(el));

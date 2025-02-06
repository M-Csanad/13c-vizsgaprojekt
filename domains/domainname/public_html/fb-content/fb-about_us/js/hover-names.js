document.addEventListener("DOMContentLoaded", function () {
  const profileM = document.getElementById("Profile_M");
  const profileB = document.getElementById("Profile_B");
  const square04 = document.querySelector(".white-square.square_04");
  const nameDisplay = document.getElementById("nameDisplay");

  let typewriterIntervalId;

  function typeWriter(element, text, speed) {
    element.textContent = "";
    let i = 0;
    const intervalId = setInterval(() => {
      element.textContent += text.charAt(i);
      i++;
      if (i >= text.length) {
        clearInterval(intervalId);
      }
    }, speed);
    return intervalId;
  }

  profileM.addEventListener("mouseenter", () => {
    if (typewriterIntervalId) clearInterval(typewriterIntervalId);
    square04.classList.add("show-text"); // Ha van animációhoz stílus
    typewriterIntervalId = typeWriter(nameDisplay, "CSANÁD", 80);
  });

  profileM.addEventListener("mouseleave", () => {
    if (typewriterIntervalId) {
      clearInterval(typewriterIntervalId);
      typewriterIntervalId = null;
    }
    nameDisplay.textContent = "";
    square04.classList.remove("show-text");
  });

  profileB.addEventListener("mouseenter", () => {
    if (typewriterIntervalId) clearInterval(typewriterIntervalId);
    square04.classList.add("show-text");
    typewriterIntervalId = typeWriter(nameDisplay, "MÁTÉ", 80);
  });

  profileB.addEventListener("mouseleave", () => {
    if (typewriterIntervalId) {
      clearInterval(typewriterIntervalId);
      typewriterIntervalId = null;
    }
    nameDisplay.textContent = "";
    square04.classList.remove("show-text");
  });
});

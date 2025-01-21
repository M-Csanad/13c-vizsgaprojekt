const halo = document.querySelector(".halo");

function stableState() {
  halo.classList.remove("dimmed", "vibrating");

  // stabil marad az időpontig
  const stableTime = Math.random() * (12000 - 5000) + 5000; //  Math.random() * (max - min) + min;
  setTimeout(startVibration, stableTime);
}

function startVibration() {
  function randomEffect() {
    const randomTime = Math.random() * (80 - 10) + 10; // 10ms - 50ms között
    const randomState = Math.random();

    if (randomState < 0.3) {
      // Halványodik (pislákol)
      halo.classList.add("dimmed");
      halo.classList.remove("vibrating");
    } else if (randomState > 0.7) {
      // Vibrál
      halo.classList.add("vibrating");
      halo.classList.remove("dimmed");
    } else {
      // Vissza alapállapot
      halo.classList.remove("dimmed", "vibrating");
    }

    const totalVibrationTime = Date.now() - vibrationStart;
    if (totalVibrationTime < vibrationDuration) {
      setTimeout(randomEffect, randomTime);
    } else {
      stableState();
    }
  }

  const vibrationDuration = Math.random() * (1500 - 500) + 500;
  const vibrationStart = Date.now();
  randomEffect();
}

stableState();

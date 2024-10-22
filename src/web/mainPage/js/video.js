const video = document.getElementById("welcomeVideo");

video.addEventListener("loadedmetadata", function () {
  const videoDuration = video.duration;

  // CSS animáció dinamikusan beállítása a videó hosszához
  video.style.animationDuration = `${videoDuration}s`;
});

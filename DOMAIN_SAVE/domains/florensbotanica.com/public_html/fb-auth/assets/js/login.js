const form = document.getElementById("login");
let submitted = false;

form.addEventListener('submit', function (event) {
  if (submitted) return;
  event.preventDefault();

  grecaptcha.enterprise.ready(function () {
      grecaptcha.enterprise.execute('6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj', { action: 'login' }).then(function (token) {
          document.getElementById('g-recaptcha-response').value = token;
          submitted = true;
          form.querySelector("input[type=submit]").click();
      });
  });
});


function validateUserNameInput() {
    const username = document.querySelector('input[name=username]');
    const usernameRegex = /^[\w-]{3,20}$/;
    if (usernameRegex.test(username.value)) {
      username.setCustomValidity('');
    }
    else {
      username.setCustomValidity('Kérjük tartsa magát a kívánt formátumhoz.');
    }
}

window.addEventListener("DOMContentLoaded", ()=>{
  document.querySelectorAll(".empty").forEach((input)=>{
    if (input.value) input.classList.remove("empty")
    else input.classList.add("empty")
    input.addEventListener("input", ()=>{
      if (input.value) input.classList.remove("empty")
      else input.classList.add("empty")
    });
  });
});

const images = document.querySelectorAll('.bg');
let currentIndex = 0;

function cycleImages() {
  if (document.body.clientWidth <= 900) return;
  images[currentIndex].classList.remove('visible');
  currentIndex = (currentIndex + 1) % images.length;
  images[currentIndex].classList.add('visible');
}

setInterval(cycleImages, 5000);
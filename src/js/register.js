const form = document.getElementById("register");
let submitted = false;

form.addEventListener('submit', function (event) {
  if (submitted) return;
  event.preventDefault();

  grecaptcha.enterprise.ready(function () {
      grecaptcha.enterprise.execute('6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj', { action: 'register' }).then(function (token) {
          document.getElementById('g-recaptcha-response').value = token;
          submitted = true;
          form.querySelector("input[type=submit]").click();
      });
  });
});

function validatePasswordInputs() {
    const password = document.querySelector('input[name=password]');
    const confirm = document.querySelector('input[name=passwordConfirm]');
    if (confirm.value === password.value) {
      confirm.setCustomValidity('');
    } else if (confirm.value.length){
      confirm.setCustomValidity('A két jelszó nem egyezik meg.');
    }
}

function validateEmailInput() {
    const email = document.querySelector('input[type=email]');
    const emailRegex = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}$/;
    if (emailRegex.test(email.value)) {
      email.setCustomValidity('');
    }
    else {
      email.setCustomValidity('Kérjük tartsa magát a kívánt formátumhoz.');
    }
}

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

window.addEventListener("load", ()=>{
  document.getElementById("email").addEventListener("input", validateEmailInput);
  document.getElementById("username").addEventListener("input", validateUserNameInput);
  document.getElementById("password").addEventListener("input", validatePasswordInputs);
  document.getElementById("passwordConfirm").addEventListener("input", validatePasswordInputs);

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
function shakeElement(element) {
  const ease = "power1.inOut";

  const shouldReverse = element.style.transform = "scale(0,0)";

  gsap.set(element, {opacity: 0})
  gsap.to(element, {
    opacity: 1,
    duration: 0.3,
    ease: ease,
    scale: shouldReverse ? 1 : 0
  })
  gsap.fromTo(
    element,
    { x: 0 },
    {
      x: `+=5`,
      duration: 0.1,
      repeat: 7,
      yoyo: true,
      ease: ease,
    }
  );
}

function removeElementContent(element) {
  return new Promise((resolve) => {
    const ease = "power1.inOut";
  
    gsap.to(element, {
      opacity: 0,
      scale: 0,
      duration: 0.3,
      ease: ease,
      onComplete: () => {
        element.innerHTML = "";
        resolve();
      }
    })
  })
}

const form = document.getElementById("register");
const formMessage = form.querySelector(".form-message");
let submitted = false;

form.addEventListener('submit', async function (event) {
  if (submitted) return;
  event.preventDefault();

  grecaptcha.enterprise.ready(async function () {
      grecaptcha.enterprise.execute('6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj', { action: 'register' }).then(async function (token) {
          document.getElementById('g-recaptcha-response').value = token;
          submitted = true;

          const data = new FormData(form);
          data.append("register", "1");
          const response = await fetch("./fb-auth/_register.php", {
            method: "POST",
            body: data
          })

          const result = await response.json();
          if (response.ok) {
            if (formMessage.innerHTML) {
              await removeElementContent(formMessage);
            }

            const outParams = {
              scaleY: 1, 
              duration: 1,
              stagger: {
                  each: 0.05,
                  from: "start",
                  grid: "auto",
                  axis: "x"
              },
              ease: "power4.inOut"
            }

            animatePageTransition(outParams).then(() => {
              window.location.href = "./login";
            });

          }
          else {
            submitted = false;
            if (formMessage.innerHTML) await removeElementContent(formMessage);
            formMessage.innerHTML = result.message;
            shakeElement(formMessage);
            
            form.querySelectorAll("input[type=password").forEach(e => e.value="");
          }
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
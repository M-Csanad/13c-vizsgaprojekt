function shakeElement(element) {
  const ease = "power1.inOut";

  gsap.set(element, {opacity: 0})
  gsap.to(element, {
    opacity: 1,
    duration: 0.3,
    ease: ease,
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

const form = document.getElementById("login");
const formMessage = form.querySelector(".form-message");
let submitted = false;

form.querySelector(".action-button").addEventListener('click', async function (event) {
  if (submitted) return;
  event.preventDefault();

  
  grecaptcha.enterprise.ready(async function () {
    grecaptcha.enterprise.execute('6Lc93ocqAAAAANIt9nxnKrNav4dcVN8_gv57Fpzj', { action: 'login' }).then(async function (token) {
      document.getElementById('g-recaptcha-response').value = token;
        submitted = true;
        
        const data = new FormData(form);
        data.append("login", "1");
        const response = await fetch("./fb-auth/login.php", {
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
            window.location.href = "./";
          });

        }
        else {
          submitted = false;
          if (formMessage) formMessage.innerHTML = result.message;
          shakeElement(formMessage);
          
          const passwdInput = form.querySelector("input[type=password");
          if (passwdInput) passwdInput.value = "";
        }
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
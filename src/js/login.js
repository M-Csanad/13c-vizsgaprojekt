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
  document.querySelectorAll(".empty").forEach((input)=>{
    if (input.value) input.classList.remove("empty")
    else input.classList.add("empty")
    input.addEventListener("input", ()=>{
      if (input.value) input.classList.remove("empty")
      else input.classList.add("empty")
    });
  });
});

// Ha az egérrel megyünk vissza a bejelentkezésre, akkor nem kerül rá a username beviteli mezőre az autofill (nem tudom miért)
document.addEventListener("DOMContentLoaded", function() {
  // Valamiért ez ide kell
  setTimeout(()=>{
    const inputs = document.querySelectorAll("input[type='text'], input[type='password']");
  
    inputs.forEach(input => {
        if (input.value) {
            input.classList.add("autofilled");
        }
        let listener = input.addEventListener("input", () => {
            input.classList.remove("autofilled");
            input.removeEventListener("input", listener);
        });
    });
  }, 0);
});
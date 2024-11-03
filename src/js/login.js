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
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
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
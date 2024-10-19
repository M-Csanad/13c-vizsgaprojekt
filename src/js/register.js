function validatePasswordInputs() {
    const password = document.querySelector('input[name=password]');
    const confirm = document.querySelector('input[name=passwordConfirm]');
    if (confirm.value === password.value) {
      confirm.setCustomValidity('');
    } else if (confirm.value.length){
      confirm.setCustomValidity('A két jelszó nem egyezik meg.');
    }
  }
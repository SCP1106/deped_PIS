document.addEventListener("DOMContentLoaded", () => {
  // Form elements
  const form = document.getElementById("signupForm");
  const password = document.getElementById("password");
  const passwordConfirmation = document.getElementById("password_confirmation");
  const terms = document.getElementById("terms");
  const submitBtn = document.getElementById("submitBtn");

  // Password strength indicators
  const lengthCheck = document.getElementById("lengthCheck");
  const uppercaseCheck = document.getElementById("uppercaseCheck");
  const lowercaseCheck = document.getElementById("lowercaseCheck");
  const numberCheck = document.getElementById("numberCheck");
  const specialCheck = document.getElementById("specialCheck");

  // Toggle password visibility buttons
  const togglePassword = document.getElementById("togglePassword");
  const toggleConfirmPassword = document.getElementById(
    "toggleConfirmPassword"
  );

  // Password visibility toggle
  if (togglePassword) {
    togglePassword.addEventListener("click", function () {
      togglePasswordVisibility(password, this.querySelector("i"));
    });
  }

  if (toggleConfirmPassword) {
    toggleConfirmPassword.addEventListener("click", function () {
      togglePasswordVisibility(passwordConfirmation, this.querySelector("i"));
    });
  }

  function togglePasswordVisibility(inputField, icon) {
    if (inputField.type === "password") {
      inputField.type = "text";
      icon.classList.remove("bi-eye");
      icon.classList.add("bi-eye-slash");
    } else {
      inputField.type = "password";
      icon.classList.remove("bi-eye-slash");
      icon.classList.add("bi-eye");
    }
  }

  // Password strength checker
  if (password) {
    password.addEventListener("input", checkPasswordStrength);
  }

  function checkPasswordStrength() {
    const value = password.value;

    // Check length
    if (value.length >= 8) {
      updateCheckStatus(lengthCheck, true);
    } else {
      updateCheckStatus(lengthCheck, false);
    }

    // Check uppercase
    if (/[A-Z]/.test(value)) {
      updateCheckStatus(uppercaseCheck, true);
    } else {
      updateCheckStatus(uppercaseCheck, false);
    }

    // Check lowercase
    if (/[a-z]/.test(value)) {
      updateCheckStatus(lowercaseCheck, true);
    } else {
      updateCheckStatus(lowercaseCheck, false);
    }

    // Check number
    if (/[0-9]/.test(value)) {
      updateCheckStatus(numberCheck, true);
    } else {
      updateCheckStatus(numberCheck, false);
    }

    // Check special character
    if (/[@$!%*?&]/.test(value)) {
      updateCheckStatus(specialCheck, true);
    } else {
      updateCheckStatus(specialCheck, false);
    }
  }

  function updateCheckStatus(element, isValid) {
    if (!element) return;

    const icon = element.querySelector("i");

    if (isValid) {
      element.classList.remove("text-muted");
      element.classList.add("text-success");
      icon.classList.remove("bi-x-circle");
      icon.classList.add("bi-check-circle-fill");
    } else {
      element.classList.remove("text-success");
      element.classList.add("text-muted");
      icon.classList.remove("bi-check-circle-fill");
      icon.classList.add("bi-x-circle");
    }
  }

  // Password confirmation match check
  if (passwordConfirmation) {
    passwordConfirmation.addEventListener("input", function () {
      if (password.value === this.value) {
        this.setCustomValidity("");
      } else {
        this.setCustomValidity("Passwords do not match");
      }
    });
  }
});

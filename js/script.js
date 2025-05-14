import L from "leaflet";
import Swal from "sweetalert2";

// THIS IS FOR THE LOGIN

document.getElementById("btn-login").addEventListener("click", () => {
  Swal.fire({
    title: "Login as Admin",
    html: `
  <input type="text" id="username" class="swal2-input custom-input" placeholder="Username">
  <input type="password" id="password" class="swal2-input custom-input" placeholder="Password">
  <div style="font-size: small;">
    <a href="#" id="forgot-password">Forgot password?</a>
  </div>
`,
    width: 520,
    padding: "2em",
    color: "#0d8017",
    background: "#f2f2f2",
    confirmButtonText: "Login",
    focusConfirm: false,
    customClass: {
      popup: "custom-swal",
      confirmButton: "custom-button",
    },
    preConfirm: () => {
      const username = document.getElementById("username").value.trim();
      const password = document.getElementById("password").value.trim();

      if (!username || !password) {
        Swal.showValidationMessage("Username and password cannot be empty.");
        return false;
      }

      return fetch("..phpp/login/login_otp.php", {
        method: "POST",
        body: new URLSearchParams({ username, password }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            return Swal.fire({
              title: "OTP Sent",
              text: "An OTP has been sent to your email.",
              icon: "success",
              confirmButtonText: "OK",
            }).then(() => showOtpInput());
          } else {
            throw new Error("Invalid credentials");
          }
        })
        .catch(() => {
          Swal.fire({
            title: "Error",
            text: "Invalid login credentials. Please try again.",
            icon: "error",
            confirmButtonText: "Retry",
          });
        });
    },
  });
});

function showOtpInput() {
  return Swal.fire({
    title: "Enter OTP",
    html: `
  <div id="otp-container" style="display: flex; justify-content: center; gap: 10px;">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
  </div>
`,
    confirmButtonText: "Verify",
    focusConfirm: false,
    didOpen: setupOtpInputs,
    preConfirm: () => verifyOtp().then((success) => success || false),
  });
}

function setupOtpInputs() {
  const inputs = document.querySelectorAll(".otp-input");

  inputs.forEach((input, index) => {
    Object.assign(input.style, {
      width: "40px",
      height: "50px",
      fontSize: "20px",
      textAlign: "center",
      border: "2px solid #0d8017",
      borderRadius: "5px",
    });

    input.addEventListener("input", (e) => {
      input.value = input.value.replace(/\D/g, ""); // Allow only numbers
      if (input.value.length === 1 && index < inputs.length - 1) {
        inputs[index + 1].focus();
      }
    });

    input.addEventListener("keydown", (e) => {
      if (e.key === "Backspace" && !input.value && index > 0) {
        inputs[index - 1].focus();
      }
    });
  });

  inputs[0].focus(); // Auto-focus first OTP input
}

function verifyOtp() {
  const otpInputs = document.querySelectorAll(".otp-input");
  const otp = Array.from(otpInputs)
    .map((input) => input.value)
    .join("");

  if (otp.length < 6) {
    Swal.showValidationMessage("Please enter a 6-digit OTP.");
    return false;
  }

  return fetch("..phpp/login/verify_otp.php", {
    method: "POST",
    body: JSON.stringify({ otp }),
    headers: { "Content-Type": "application/json" },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        return Swal.fire({
          title: "Success",
          text: data.message,
          icon: "success",
          confirmButtonText: "OK",
        }).then(() => {
          window.location.href = "dashboard.html";
        });
      } else {
        Swal.showValidationMessage(data.message || "OTP verification failed.");
        return false;
      }
    })
    .catch(() => {
      Swal.showValidationMessage("There was an error verifying the OTP.");
      return false;
    });
}

document.getElementById("searchInput").addEventListener("input", function () {
  const searchValue = this.value.toLowerCase();
});

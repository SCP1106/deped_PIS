<?php
// Start session
session_start();

// Check if email is verified
if (!isset($_SESSION['verified_email'])) {
  // Redirect to register page if email is not verified
  header("Location: register.php");
  exit;
}

// CSRF protection
if (!isset($_SESSION['csrf_token'])) {
  $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Complete Your Account | DepEd</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="/auth/assets/css/styles.css">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
  <style>
    .main-content {
      display: flex;
      justify-content: center;
      position: relative;
      background-color: #f9f9f9;
      min-height: 100vh;
      padding: 4rem 0;
      overflow: hidden;
      z-index: 1;
      background: url("data:image/svg+xml,%3Csvg width='100%' height='200' viewBox='100 50 1100 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,100 C200,250 400,-50 600,100 C800,250 1000,-50 1200,100' fill='none' stroke='%232e8b57' stroke-width='3' stroke-linecap='round'/%3E%3C/svg%3E") repeat-x;
      background-size: cover;
      background-position: center;
    }
  </style>
</head>

<body>
  <div class="main-content">
    <div class="container py-5">
      <div class="row justify-content-center">
        <div class="col-12 col-md-8 col-lg-6 col-xl-5">
          <div class="text-center mb-4">
            <h1 class="fw-bold" style="color: #2e8b57;">Complete Your Account</h1>
            <p class="text-muted">Set up your password and security options</p>
          </div>

          <!-- Display validation errors if any -->
          <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
            <div class="alert alert-danger">
              <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $key => $error): ?>
                  <li><?php echo htmlspecialchars($error); ?></li>
                <?php endforeach; ?>
              </ul>
            </div>
            <?php unset($_SESSION['errors']); ?>
          <?php endif; ?>

          <!-- Display success message if any -->
          <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success">
              <?php echo htmlspecialchars($_SESSION['success']); ?>
              <?php unset($_SESSION['success']); ?>
            </div>
          <?php endif; ?>

          <div class="card shadow-lg border-0 rounded-4">
            <div class="card-body p-4 p-md-5">
              <form id="signupForm" action="/auth/process/process-signup.php" method="post" novalidate>
                <div class="row g-3">
                  <!-- First Name -->
                  <div class="col-md-6">
                    <label for="first_name" class="form-label">
                      First Name <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control <?php echo isset($_SESSION['errors']['first_name']) ? 'is-invalid' : ''; ?>"
                      id="first_name"
                      name="first_name"
                      placeholder="John"
                      value="<?php echo isset($_SESSION['old']['first_name']) ? htmlspecialchars($_SESSION['old']['first_name']) : ''; ?>"
                      pattern="^[A-Za-z ]+$"
                      title="First name must contain only letters and spaces."
                      required>
                    <?php if (isset($_SESSION['errors']['first_name'])): ?>
                      <div class="invalid-feedback"><?php echo htmlspecialchars($_SESSION['errors']['first_name']); ?></div>
                    <?php endif; ?>
                  </div>

                  <!-- Last Name -->
                  <div class="col-md-6">
                    <label for="last_name" class="form-label">
                      Last Name <span class="text-danger">*</span>
                    </label>
                    <input
                      type="text"
                      class="form-control <?php echo isset($_SESSION['errors']['last_name']) ? 'is-invalid' : ''; ?>"
                      id="last_name"
                      name="last_name"
                      placeholder="Doe"
                      value="<?php echo isset($_SESSION['old']['last_name']) ? htmlspecialchars($_SESSION['old']['last_name']) : ''; ?>"
                      pattern="^[A-Za-z ]+$"
                      title="Last name must contain only letters and spaces."
                      required>
                    <?php if (isset($_SESSION['errors']['last_name'])): ?>
                      <div class="invalid-feedback"><?php echo htmlspecialchars($_SESSION['errors']['last_name']); ?></div>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- Middle Name -->
                <div class="mb-3 mt-3">
                  <label for="middle_name" class="form-label">Middle Name (Optional)</label>
                  <input
                    type="text"
                    class="form-control"
                    id="middle_name"
                    name="middle_name"
                    placeholder="Middle name"
                    value="<?php echo isset($_SESSION['old']['middle_name']) ? htmlspecialchars($_SESSION['old']['middle_name']) : ''; ?>"
                    pattern="^[A-Za-z ]*$"
                    title="Middle name must contain only letters.">
                </div>

                <div class="row g-3">
                  <!-- Birthday -->
                  <div class="col-md-6">
                    <label for="birthday" class="form-label">
                      Birthday <span class="text-danger">*</span>
                    </label>
                    <input
                      type="date"
                      class="form-control <?php echo isset($_SESSION['errors']['birthday']) ? 'is-invalid' : ''; ?>"
                      id="birthday"
                      name="birthday"
                      value="<?php echo isset($_SESSION['old']['birthday']) ? htmlspecialchars($_SESSION['old']['birthday']) : ''; ?>"
                      required>
                    <?php if (isset($_SESSION['errors']['birthday'])): ?>
                      <div class="invalid-feedback"><?php echo htmlspecialchars($_SESSION['errors']['birthday']); ?></div>
                    <?php endif; ?>
                  </div>

                  <!-- Gender -->
                  <div class="col-md-6">
                    <label for="gender" class="form-label">
                      Gender <span class="text-danger">*</span>
                    </label>
                    <select
                      class="form-select <?php echo isset($_SESSION['errors']['gender']) ? 'is-invalid' : ''; ?>"
                      id="gender"
                      name="gender"
                      required>
                      <option value="" disabled <?php echo !isset($_SESSION['old']['gender']) ? 'selected' : ''; ?>>Select your gender</option>
                      <option value="male" <?php echo (isset($_SESSION['old']['gender']) && $_SESSION['old']['gender'] === 'male') ? 'selected' : ''; ?>>Male</option>
                      <option value="female" <?php echo (isset($_SESSION['old']['gender']) && $_SESSION['old']['gender'] === 'female') ? 'selected' : ''; ?>>Female</option>
                      <option value="other" <?php echo (isset($_SESSION['old']['gender']) && $_SESSION['old']['gender'] === 'other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                    <?php if (isset($_SESSION['errors']['gender'])): ?>
                      <div class="invalid-feedback"><?php echo htmlspecialchars($_SESSION['errors']['gender']); ?></div>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- Password -->
                <div class="mb-3 mt-3">
                  <label for="password" class="form-label">
                    Password <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <input
                      type="password"
                      class="form-control <?php echo isset($_SESSION['errors']['password']) ? 'is-invalid' : ''; ?>"
                      id="password"
                      name="password"
                      placeholder="••••••••"
                      required>
                    <button
                      class="btn btn-outline-secondary"
                      type="button"
                      id="togglePassword">
                      <i class="bi bi-eye"></i>
                    </button>
                    <?php if (isset($_SESSION['errors']['password'])): ?>
                      <div class="invalid-feedback"><?php echo htmlspecialchars($_SESSION['errors']['password']); ?></div>
                    <?php endif; ?>
                  </div>

                  <!-- Password strength indicators -->
                  <ul class="password-strength mt-2">
                    <li id="lengthCheck" class="text-muted">
                      <i class="bi bi-x-circle"></i> At least 8 characters
                    </li>
                    <li id="uppercaseCheck" class="text-muted">
                      <i class="bi bi-x-circle"></i> At least one uppercase letter
                    </li>
                    <li id="lowercaseCheck" class="text-muted">
                      <i class="bi bi-x-circle"></i> At least one lowercase letter
                    </li>
                    <li id="numberCheck" class="text-muted">
                      <i class="bi bi-x-circle"></i> At least one number
                    </li>
                    <li id="specialCheck" class="text-muted">
                      <i class="bi bi-x-circle"></i> At least one special character (@, $, !, %, *, ?, &)
                    </li>
                  </ul>
                </div>

                <!-- Confirm Password -->
                <div class="mb-3">
                  <label for="password_confirmation" class="form-label">
                    Confirm Password <span class="text-danger">*</span>
                  </label>
                  <div class="input-group">
                    <input
                      type="password"
                      class="form-control <?php echo isset($_SESSION['errors']['password_confirmation']) ? 'is-invalid' : ''; ?>"
                      id="password_confirmation"
                      name="password_confirmation"
                      placeholder="••••••••"
                      required>
                    <button
                      class="btn btn-outline-secondary"
                      type="button"
                      id="toggleConfirmPassword">
                      <i class="bi bi-eye"></i>
                    </button>
                    <?php if (isset($_SESSION['errors']['password_confirmation'])): ?>
                      <div class="invalid-feedback"><?php echo htmlspecialchars($_SESSION['errors']['password_confirmation']); ?></div>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- Google Authenticator Option -->
                <div class="mb-3">
                  <div class="form-check">
                    <input
                      class="form-check-input"
                      type="checkbox"
                      id="use_2fa"
                      name="use_2fa"
                      <?php echo (isset($_SESSION['old']['use_2fa']) && $_SESSION['old']['use_2fa']) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="use_2fa">
                      Enable Google Authenticator for enhanced security
                    </label>
                  </div>
                </div>

                <!-- Terms and Conditions -->
                <div class="mb-3">
                  <div class="form-check">
                    <input
                      class="form-check-input <?php echo isset($_SESSION['errors']['terms']) ? 'is-invalid' : ''; ?>"
                      type="checkbox"
                      id="terms"
                      name="terms"
                      <?php echo (isset($_SESSION['old']['terms']) && $_SESSION['old']['terms']) ? 'checked' : ''; ?>
                      required>
                    <label class="form-check-label" for="terms">
                      I agree to the
                      <a href="terms.php" class="link-primary">Terms of Service</a>
                      and
                      <a href="privacy.php" class="link-primary">Privacy Policy</a>.
                    </label>
                    <?php if (isset($_SESSION['errors']['terms'])): ?>
                      <div class="invalid-feedback"><?php echo htmlspecialchars($_SESSION['errors']['terms']); ?></div>
                    <?php endif; ?>
                  </div>
                </div>

                <!-- CSRF Protection -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <!-- Store verified email -->
                <input type="hidden" name="email" value="<?php echo htmlspecialchars($_SESSION['verified_email']); ?>">

                <button type="submit" class="btn btn-primary w-100 py-2 mt-3" id="submitBtn">
                  Complete Registration
                </button>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script src="/auth/assets/js/script.js"></script>
</body>

</html>
<?php
// Clear old form data after displaying the form
if (isset($_SESSION['old'])) {
  unset($_SESSION['old']);
}
?>
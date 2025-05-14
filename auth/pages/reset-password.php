<?php
// Start session
session_start();

// CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$token = $_GET["token"] ?? '';

if (empty($token)) {
    die("Token is missing from the URL.");
}

$token_hash = hash("sha256", $token);

$mysqli = require __DIR__ . "/database.php";

$sql = "SELECT * FROM users
        WHERE reset_token_hash = ?";

$stmt = $mysqli->prepare($sql);

if (!$stmt) {
    die("SQL error: " . $mysqli->error);
}

$stmt->bind_param("s", $token_hash);

$stmt->execute();

$result = $stmt->get_result();

$user = $result->fetch_assoc();

if ($user === null) {
    die("Token not found or has already been used.");
}

if (strtotime($user["reset_token_expires_at"]) <= time()) {
    die("Token has expired. Please request a new password reset link.");
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | DepEd</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
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
        <div class="container py-4 py-md-5">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                    <div class="text-center mb-4">
                        <h1 class="fw-bold" style="color: #2e8b57;">Reset Your Password</h1>
                        <p class="text-muted">Enter your new password below</p>
                    </div>

                    <?php if (isset($_SESSION['errors']) && !empty($_SESSION['errors'])): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($_SESSION['errors'] as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                        <?php unset($_SESSION['errors']); ?>
                    <?php endif; ?>

                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-3 p-sm-4 p-md-5">
                            <form method="post" action="process-reset-password.php" novalidate>
                                <input type="hidden" name="  novalidate>
                                <input type=" hidden" name="token" value="<?= htmlspecialchars($token) ?>">
                                <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                                <div class="mb-3">
                                    <label for="password" class="form-label">New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password" name="password" placeholder="••••••••" required>
                                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>

                                    <!-- Password strength indicators -->
                                    <ul class="password-strength mt-2 small">
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

                                <div class="mb-4">
                                    <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                    <div class="input-group">
                                        <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="••••••••" required>
                                        <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                            <i class="bi bi-eye"></i>
                                        </button>
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2">Reset Password</button>
                            </form>

                            <div class="mt-4 text-center">
                                <p class="text-muted">
                                    Remember your password?
                                    <a href="login.php" class="link-primary fw-medium">Sign in</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle
        document.getElementById('togglePassword').addEventListener('click', function() {
            togglePasswordVisibility('password', this.querySelector('i'));
        });

        document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
            togglePasswordVisibility('password_confirmation', this.querySelector('i'));
        });

        function togglePasswordVisibility(inputId, icon) {
            const input = document.getElementById(inputId);
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('bi-eye');
                icon.classList.add('bi-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('bi-eye-slash');
                icon.classList.add('bi-eye');
            }
        }

        // Password strength checker
        const password = document.getElementById('password');
        const lengthCheck = document.getElementById('lengthCheck');
        const uppercaseCheck = document.getElementById('uppercaseCheck');
        const lowercaseCheck = document.getElementById('lowercaseCheck');
        const numberCheck = document.getElementById('numberCheck');
        const specialCheck = document.getElementById('specialCheck');

        password.addEventListener('input', function() {
            const value = this.value;

            // Check length
            updateCheckStatus(lengthCheck, value.length >= 8);

            // Check uppercase
            updateCheckStatus(uppercaseCheck, /[A-Z]/.test(value));

            // Check lowercase
            updateCheckStatus(lowercaseCheck, /[a-z]/.test(value));

            // Check number
            updateCheckStatus(numberCheck, /[0-9]/.test(value));

            // Check special character
            updateCheckStatus(specialCheck, /[@$!%*?&]/.test(value));
        });

        function updateCheckStatus(element, isValid) {
            const icon = element.querySelector('i');

            if (isValid) {
                element.classList.remove('text-muted');
                element.classList.add('text-success');
                icon.classList.remove('bi-x-circle');
                icon.classList.add('bi-check-circle-fill');
            } else {
                element.classList.remove('text-success');
                element.classList.add('text-muted');
                icon.classList.remove('bi-check-circle-fill');
                icon.classList.add('bi-x-circle');
            }
        }
    </script>
</body>

</html>
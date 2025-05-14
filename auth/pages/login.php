<?php
// Composer autoloader for RobThree and other dependencies
require_once __DIR__ . '/../assets/vendor/autoload.php';
// Start session
session_start();

// Include database connection and rate limiter
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../core/rate-limiter.php';
require_once __DIR__ . '/../core/session.php';

// CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$is_invalid = false;
$is_rate_limited = false;
$time_until_unlocked = 0;
$needs_2fa = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    $email = filter_var($_POST["email"] ?? '', FILTER_VALIDATE_EMAIL);
    $password = $_POST["password"] ?? '';
    $verification_code = $_POST["verification_code"] ?? '';

    // Rate limiting
    $rateLimiter = new RateLimiter($mysqli, $_SERVER['REMOTE_ADDR'], $email, 5, 300); // 5 attempts per 5 minutes

    if ($rateLimiter->isRateLimited()) {
        $is_rate_limited = true;
        $time_until_unlocked = $rateLimiter->getTimeUntilUnlocked();
    } else {
        if ($email) {
            $stmt = $mysqli->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            // Set access level if user is admin
            if ($user && isset($user['role']) && $user['role'] === 'admin') {
                $_SESSION['access_level'] = 'admin';
            } else {
                $_SESSION['access_level'] = 'user';
            }

            if ($user && $user["account_activation_hash"] === null) {
                if (password_verify($password, $user["password"])) {
                    // Regenerate session ID before creating a new session to avoid duplicate entry
                    session_regenerate_id(true);
                    // Check if 2FA is enabled
                    if ($user["google2fa_enabled"] == 1) {
                        if (empty($verification_code)) {
                            // User needs to enter 2FA code
                            $needs_2fa = true;
                            $_SESSION['pending_login_email'] = $email;
                        } else {
                            // Verify 2FA code
                            $valid = false;

                            // Always use RobThree library for 2FA verification
                            $tfa = new \RobThree\Auth\TwoFactorAuth();
                            $valid = $tfa->verifyCode($user["google2fa_secret"], $verification_code);

                            if ($valid) {
                                // 2FA successful, create session
                                $sessionManager = new SessionManager($mysqli);
                                $sessionManager->createSession($user["id"]);
                                $_SESSION["user_id"] = $user["id"];
                                $_SESSION["authenticated"] = true;

                                header("Location: ../../dashboard.php");
                                exit;
                            } else {
                                $is_invalid = true;
                                $needs_2fa = true;
                                $_SESSION['pending_login_email'] = $email;
                            }
                        }
                    } else {
                        // No 2FA, create session directly
                        $sessionManager = new SessionManager($mysqli);
                        $sessionManager->createSession($user["id"]);
                        $_SESSION["user_id"] = $user["id"];
                        $_SESSION["authenticated"] = true;

                        header("Location: ../../dashboard.php");
                        exit;
                    }
                } else {
                    $is_invalid = true;
                    // Record failed login attempt
                    $rateLimiter->recordAttempt();
                }
            } else {
                $is_invalid = true;
                // Record failed login attempt
                $rateLimiter->recordAttempt();
            }
        } else {
            $is_invalid = true;
        }
    }
}

// If ?reset2fa=1 is in the URL, clear pending 2FA state
if (isset($_GET['reset2fa'])) {
    unset($_SESSION['pending_login_email']);
    $needs_2fa = false;
}
// On GET requests, clear any pending 2FA state unless reset2fa is set
if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !isset($_GET['reset2fa'])) {
    unset($_SESSION['pending_login_email']);
    $needs_2fa = false;
}
// Check if there's a pending 2FA login
if (isset($_SESSION['pending_login_email'])) {
    $needs_2fa = true;
    $email = $_SESSION['pending_login_email'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | DepEd</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/auth/assets/css/styles.css">
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
                <div class="col-12 col-md-8 col-lg-5">
                    <div class="text-center mb-4">
                        <h1 class="fw-bold" style="color: #2e8b57;">Welcome back</h1>
                        <p class="text-muted">Sign in to your account</p>
                    </div>

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($is_invalid && !$needs_2fa): ?>
                        <div class="alert alert-danger">
                            Invalid email or password
                        </div>
                    <?php endif; ?>

                    <?php if ($is_rate_limited): ?>
                        <div class="alert alert-warning">
                            Too many login attempts. Please try again in <?= ceil($time_until_unlocked / 60) ?> minutes.
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-4 p-md-5">
                            <?php if ($needs_2fa): ?>
                                <!-- 2FA Verification Form -->
                                <form method="post" novalidate>
                                    <h5 class="mb-3">Two-Factor Authentication</h5>

                                    <?php if ($is_invalid): ?>
                                        <div class="alert alert-danger">
                                            Invalid verification code
                                        </div>
                                    <?php endif; ?>

                                    <div class="mb-3">
                                        <label for="verification_code" class="form-label">Verification Code</label>
                                        <input
                                            type="text"
                                            class="form-control form-control-lg text-center"
                                            id="verification_code"
                                            name="verification_code"
                                            placeholder="000000"
                                            maxlength="6"
                                            autocomplete="one-time-code"
                                            required
                                            <?= $is_rate_limited ? 'disabled' : '' ?>>
                                        <div class="form-text">
                                            Enter the 6-digit code from Google Authenticator
                                        </div>
                                    </div>

                                    <!-- Hidden fields to maintain state -->
                                    <input type="hidden" name="email" value="<?= htmlspecialchars($email) ?>">
                                    <input type="hidden" name="password" value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>">
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <a href="login.php?reset2fa=1" class="text-decoration-none">
                                            <i class="bi bi-arrow-left"></i> Back to login
                                        </a>
                                        <button
                                            type="submit"
                                            class="btn btn-primary py-2 px-4"
                                            <?= $is_rate_limited ? 'disabled' : '' ?>>
                                            Verify
                                        </button>
                                    </div>
                                </form>
                            <?php else: ?>
                                <!-- Regular Login Form -->
                                <form method="post" novalidate>
                                    <div class="mb-3">
                                        <label for="email" class="form-label">Email</label>
                                        <input
                                            type="email"
                                            class="form-control"
                                            id="email"
                                            name="email"
                                            placeholder="name@example.com"
                                            value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                                            required
                                            <?= $is_rate_limited ? 'disabled' : '' ?>>
                                    </div>

                                    <div class="mb-3">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <label for="password" class="form-label">Password</label>
                                            <a href="forgot-password.php" class="text-decoration-none small">Forgot password?</a>
                                        </div>
                                        <div class="input-group">
                                            <input
                                                type="password"
                                                class="form-control"
                                                id="password"
                                                name="password"
                                                placeholder="••••••••"
                                                required
                                                <?= $is_rate_limited ? 'disabled' : '' ?>>
                                            <button
                                                class="btn btn-outline-secondary"
                                                type="button"
                                                id="togglePassword"
                                                <?= $is_rate_limited ? 'disabled' : '' ?>>
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </div>

                                    <div class="mb-3 form-check">
                                        <input
                                            type="checkbox"
                                            class="form-check-input"
                                            id="remember"
                                            name="remember"
                                            <?= $is_rate_limited ? 'disabled' : '' ?>>
                                        <label class="form-check-label" for="remember">Remember me</label>
                                    </div>

                                    <!-- CSRF Protection -->
                                    <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                                    <button
                                        type="submit"
                                        class="btn btn-primary w-100 py-2 mt-3"
                                        <?= $is_rate_limited ? 'disabled' : '' ?>>
                                        Sign in
                                    </button>
                                </form>


                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle
        document.addEventListener('DOMContentLoaded', function() {
            const togglePassword = document.getElementById('togglePassword');
            if (togglePassword) {
                togglePassword.addEventListener('click', function() {
                    const passwordInput = document.getElementById('password');
                    const icon = this.querySelector('i');

                    if (passwordInput.type === 'password') {
                        passwordInput.type = 'text';
                        icon.classList.remove('bi-eye');
                        icon.classList.add('bi-eye-slash');
                    } else {
                        passwordInput.type = 'password';
                        icon.classList.remove('bi-eye-slash');
                        icon.classList.add('bi-eye');
                    }
                });
            }

            // Format verification code input
            const verificationCode = document.getElementById('verification_code');
            if (verificationCode) {
                verificationCode.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
                });
            }
        });
    </script>
</body>

</html>
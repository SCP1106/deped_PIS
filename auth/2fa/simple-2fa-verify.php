<?php
// Start session
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once 'database.php';

// Get user from database
$user_id = $_SESSION['user_id'];
$stmt = $mysqli->prepare("SELECT id, email, google2fa_secret, google2fa_enabled FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $_SESSION['errors'] = ["User not found."];
    header("Location: login.php");
    exit;
}

// Check if 2FA is enabled
if ($user['google2fa_enabled'] != 1) {
    $_SESSION['errors'] = ["Two-factor authentication is not enabled for this account."];
    header("Location: dashboard.php");
    exit;
}

// Check if RobThree library is available
$autoload_found = false;
foreach ([__DIR__ . '/vendor/autoload.php', __DIR__ . '/../vendor/autoload.php', dirname(__DIR__) . '/vendor/autoload.php'] as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoload_found = true;
        break;
    }
}

$useRobThree = $autoload_found && class_exists('RobThree\Auth\TwoFactorAuth');

// If RobThree is not available, include the simple-2fa.php file
if (!$useRobThree) {
    require_once 'simple-2fa.php';
}

// Process verification
$verification_error = null;
$verification_success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verification_code = $_POST['verification_code'] ?? '';

    if (empty($verification_code)) {
        $verification_error = "Verification code is required.";
    } else {
        // Verify the code
        $valid = false;

        if ($useRobThree) {
            $tfa = new RobThree\Auth\TwoFactorAuth('DepEd App');
            $valid = $tfa->verifyCode($user['google2fa_secret'], $verification_code);
        } else {
            // Use simple TOTP verification
            $valid = verifyCode($user['google2fa_secret'], $verification_code);
        }

        if ($valid) {
            // Mark as verified in session
            $_SESSION['2fa_verified'] = true;
            $verification_success = true;

            // Redirect to dashboard
            header("Location: dashboard.php");
            exit;
        } else {
            $verification_error = "Invalid verification code. Please try again.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify 2FA | DepEd</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .main-content {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f9f9fa;
        }

        .verification-card {
            max-width: 450px;
            width: 100%;
        }

        .verification-input {
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card shadow-lg border-0 rounded-4 verification-card">
                        <div class="card-body p-4 p-md-5">
                            <div class="text-center mb-4">
                                <i class="bi bi-shield-lock text-primary" style="font-size: 3rem;"></i>
                                <h2 class="mt-3">Two-Factor Authentication</h2>
                                <p class="text-muted">Enter the 6-digit code from your authenticator app</p>
                            </div>

                            <?php if ($verification_error): ?>
                                <div class="alert alert-danger">
                                    <?php echo htmlspecialchars($verification_error); ?>
                                </div>
                            <?php endif; ?>

                            <?php if ($verification_success): ?>
                                <div class="alert alert-success">
                                    Verification successful! Redirecting...
                                </div>
                            <?php endif; ?>

                            <form method="post" class="needs-validation" novalidate>
                                <div class="mb-4">
                                    <input type="text"
                                        class="form-control form-control-lg verification-input"
                                        name="verification_code"
                                        id="verification_code"
                                        placeholder="000000"
                                        maxlength="6"
                                        inputmode="numeric"
                                        pattern="[0-9]{6}"
                                        required
                                        autofocus>
                                    <div class="invalid-feedback">
                                        Please enter the 6-digit verification code.
                                    </div>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    Verify
                                </button>
                            </form>

                            <div class="mt-4 text-center">
                                <p class="text-muted">
                                    Open your authenticator app and enter the code for<br>
                                    <strong><?php echo htmlspecialchars($user['email']); ?></strong>
                                </p>
                                <a href="../../logout.php" class="text-decoration-none">
                                    <i class="bi bi-arrow-left"></i> Back to login
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-format verification code input
        document.addEventListener('DOMContentLoaded', function() {
            const codeInput = document.getElementById('verification_code');
            if (codeInput) {
                codeInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
                });

                // Auto-submit when 6 digits are entered
                codeInput.addEventListener('keyup', function() {
                    if (this.value.length === 6) {
                        this.form.submit();
                    }
                });
            }
        });
    </script>
</body>

</html>
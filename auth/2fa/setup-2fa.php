<?php
// Start session
session_start();

// Check if user is setting up 2FA
if (!isset($_SESSION['setup_2fa']) || !isset($_SESSION['user_email'])) {
    header("Location: ../auth/pages/login.php");
    exit;
}

// Include database connection
require_once __DIR__ . '/../core/database.php';

// Always require Composer autoloader from the project root
require_once dirname(__DIR__, 2) . '/vendor/autoload.php';

// Check if RobThree library is available
if (!class_exists('RobThree\Auth\TwoFactorAuth')) {
    throw new Exception('RobThree TwoFactorAuth library is not installed or not autoloaded.');
}

// Get user from database
$email = $_SESSION['user_email'];
$stmt = $mysqli->prepare("SELECT id, google2fa_secret FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

if (!$user) {
    $_SESSION['errors'] = ["Error setting up 2FA. User not found."];
    header("Location: signup.php");
    exit;
}

// If user doesn't have a 2FA secret yet, generate one
if (!$user['google2fa_secret']) {
    // Generate a new secret using RobThree
    $tfa = new RobThree\Auth\TwoFactorAuth('DepEd App');
    $secret = $tfa->createSecret();

    // Update the user's record with the new secret
    $stmt = $mysqli->prepare("UPDATE users SET google2fa_secret = ? WHERE id = ?");
    $stmt->bind_param("si", $secret, $user['id']);
    $stmt->execute();
    $stmt->close();

    // Update the user variable with the new secret
    $user['google2fa_secret'] = $secret;
}

// Generate QR code
$tfa = new RobThree\Auth\TwoFactorAuth('DepEd App');
$qrCodeUrl = $tfa->getQRCodeImageAsDataUri(
    $email,
    $user['google2fa_secret']
);


// Process verification
$verification_error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $verification_code = $_POST['verification_code'] ?? '';

    if (empty($verification_code)) {
        $verification_error = "Verification code is required.";
    } else {
        // Verify the code
        $tfa = new RobThree\Auth\TwoFactorAuth('DepEd App');
        $valid = $tfa->verifyCode($user['google2fa_secret'], $verification_code);

        // DEBUG OUTPUT: Show verification process details
        $debug_2fa = [
            'submitted_code' => $verification_code,
            'expected_secret' => $user['google2fa_secret'],
            'verification_result' => $valid ? 'SUCCESS' : 'FAILURE',
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        if ($valid) {
            // Mark 2FA as verified
            $stmt = $mysqli->prepare("UPDATE users SET google2fa_enabled = 1 WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            $stmt->close();

            // Redirect to login
            $_SESSION['success'] = "Two-factor authentication has been set up successfully. You can now log in.";
            unset($_SESSION['setup_2fa']);
            unset($_SESSION['user_email']);
            unset($_SESSION['pending_login_email']);
            header("Location: ../auth/pages/login.php");
            exit;
        } else {
            $verification_error = "Invalid verification code. Please try again.";
        }
    }
}

/**
 * Generate a random secret key
 * 
 * @param int $length Length of the secret key
 * @return string The generated secret key
 */
function generateSecretKey($length = 16)
{
    $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < $length; $i++) {
        $secret .= $validChars[rand(0, strlen($validChars) - 1)];
    }
    return $secret;
}

/**
 * Simple TOTP verification function
 * 
 * @param string $secret Secret key
 * @param string $code Code to verify
 * @return bool True if code is valid, false otherwise
 */
function verifyTOTP($secret, $code)
{
    // For simplicity, we'll accept any code for now
    // In a real implementation, you would calculate the TOTP value
    return true;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Set Up Two-Factor Authentication | DepEd</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
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

        .qr-code-container {
            text-align: center;
            margin-bottom: 20px;
        }

        .qr-code-img {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 5px;
            background: white;
        }

        .debug-info {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 4px;
            font-size: 0.8rem;
        }
    </style>
</head>

<body>
    <div class="main-content">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6 col-xl-5">
                    <div class="text-center mb-4">
                        <h1 class="fw-bold" style="color: #2e8b57;">Set Up Two-Factor Authentication</h1>
                        <p class="text-muted">Enhance your account security</p>
                    </div>

                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="mb-4">
                                <h5>Step 1: Install Google Authenticator</h5>
                                <p>Download and install Google Authenticator on your mobile device:</p>
                                <div class="d-flex justify-content-center gap-3 mb-3">
                                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="btn btn-outline-dark">
                                        <i class="bi bi-android"></i> Android
                                    </a>
                                    <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank" class="btn btn-outline-dark">
                                        <i class="bi bi-apple"></i> iOS
                                    </a>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5>Step 2: Scan QR Code</h5>
                                <p>Open Google Authenticator and scan this QR code:</p>

                                <div class="qr-code-container">
                                    <img src="<?php echo $qrCodeUrl; ?>" alt="QR Code" class="qr-code-img">
                                </div>

                                <!-- Alternative setup methods -->
                                <div class="alert alert-info">
                                    <strong>Can't scan the QR code?</strong> Try these alternatives:

                                    <div class="mt-2">
                                        <strong>1. Manual entry:</strong> Enter this key manually in your authenticator app:
                                        <code class="user-select-all"><?php echo $user['google2fa_secret']; ?></code>
                                    </div>

                                    <div class="mt-2">
                                        <strong>2. Direct link:</strong>
                                        <a href="otpauth://totp/<?php echo urlencode('DepEd App'); ?>:<?php echo urlencode($email); ?>?secret=<?php echo $user['google2fa_secret']; ?>&issuer=<?php echo urlencode('DepEd App'); ?>" class="btn btn-sm btn-outline-primary">
                                            Open in Authenticator App
                                        </a>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-4">
                                <h5>Step 3: Verify Setup</h5>
                                <p>Enter the 6-digit verification code from Google Authenticator:</p>

                                <?php if ($verification_error): ?>
                                    <div class="alert alert-danger">
                                        <?php echo htmlspecialchars($verification_error); ?>
                                    </div>
                                <?php endif; ?>

                                <form method="post" class="needs-validation" novalidate>
                                    <div class="mb-3">
                                        <input type="text" class="form-control form-control-lg text-center"
                                            name="verification_code" id="verification_code"
                                            placeholder="000000" maxlength="6" inputmode="numeric"
                                            pattern="[0-9]{6}" required>
                                        <div class="invalid-feedback">
                                            Please enter the 6-digit verification code.
                                        </div>
                                    </div>

                                    <button type="submit" class="btn btn-primary w-100 py-2">
                                        Verify and Complete Setup
                                    </button>
                                </form>
                            </div>

                            <!-- Debug information (can be removed in production) -->
                            <div class="debug-info">
                                <p><strong>Debug Info:</strong></p>
                                <ul class="mb-0">
                                    <li>Using RobThree library: Yes</li>
                                    <li>Secret key: <?php echo $user['google2fa_secret']; ?></li>
                                    <li>Email: <?php echo $email; ?></li>
                                    <?php if (isset($debug_2fa)): ?>
                                        <li><strong>2FA Debug (POST only):</strong></li>
                                        <li>Submitted Code: <?php echo htmlspecialchars($debug_2fa['submitted_code']); ?></li>
                                        <li>Expected Secret: <?php echo htmlspecialchars($debug_2fa['expected_secret']); ?></li>
                                        <li>Verification Result: <?php echo htmlspecialchars($debug_2fa['verification_result']); ?></li>
                                        <li>Timestamp: <?php echo htmlspecialchars($debug_2fa['timestamp']); ?></li>
                                    <?php endif; ?>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('.needs-validation');

            form.addEventListener('submit', function(event) {
                if (!form.checkValidity()) {
                    event.preventDefault();
                    event.stopPropagation();
                }

                form.classList.add('was-validated');
            });

            // Auto-format verification code input
            const codeInput = document.getElementById('verification_code');
            if (codeInput) {
                codeInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
                });
            }
        });
    </script>
</body>

</html>
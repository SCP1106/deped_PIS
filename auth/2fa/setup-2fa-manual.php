<?php
// Start session
session_start();

// Check if user is setting up 2FA
if (!isset($_SESSION['setup_2fa']) || !isset($_SESSION['user_email'])) {
    header("Location: login.php");
    exit;
}

// Include database connection
require_once 'database.php';

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
    // Generate a new secret (16 characters is standard for TOTP)
    if ($useRobThree) {
        $tfa = new RobThree\Auth\TwoFactorAuth('DepEd App');
        $secret = $tfa->createSecret();
    } else {
        $secret = generateSecretKey();
    }

    // Update the user's record with the new secret
    $stmt = $mysqli->prepare("UPDATE users SET google2fa_secret = ? WHERE id = ?");
    $stmt->bind_param("si", $secret, $user['id']);
    $stmt->execute();
    $stmt->close();

    // Update the user variable with the new secret
    $user['google2fa_secret'] = $secret;
}

// Process verification
$verification_error = null;
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
            // Mark 2FA as verified
            $stmt = $mysqli->prepare("UPDATE users SET google2fa_enabled = 1 WHERE id = ?");
            $stmt->bind_param("i", $user['id']);
            $stmt->execute();
            $stmt->close();

            // Redirect to login
            $_SESSION['success'] = "Two-factor authentication has been set up successfully. You can now log in.";
            unset($_SESSION['setup_2fa']);
            unset($_SESSION['user_email']);
            header("Location: login.php");
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

        .secret-key {
            font-family: monospace;
            font-size: 1.5rem;
            letter-spacing: 2px;
            text-align: center;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px dashed #ced4da;
            border-radius: 4px;
            margin: 15px 0;
            user-select: all;
        }

        .step-container {
            margin-bottom: 25px;
            padding: 15px;
            border-radius: 8px;
            background-color: #fff;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }

        .step-number {
            display: inline-block;
            width: 30px;
            height: 30px;
            line-height: 30px;
            text-align: center;
            background-color: #2e8b57;
            color: white;
            border-radius: 50%;
            margin-right: 10px;
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
                        <p class="text-muted">Manual Setup Method</p>
                    </div>

                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-4 p-md-5">
                            <div class="step-container">
                                <h5><span class="step-number">1</span> Install Google Authenticator</h5>
                                <p>Download and install Google Authenticator on your mobile device if you haven't already:</p>
                                <div class="d-flex justify-content-center gap-3 mb-3">
                                    <a href="https://play.google.com/store/apps/details?id=com.google.android.apps.authenticator2" target="_blank" class="btn btn-outline-dark">
                                        <i class="bi bi-android"></i> Android
                                    </a>
                                    <a href="https://apps.apple.com/us/app/google-authenticator/id388497605" target="_blank" class="btn btn-outline-dark">
                                        <i class="bi bi-apple"></i> iOS
                                    </a>
                                </div>
                            </div>

                            <div class="step-container">
                                <h5><span class="step-number">2</span> Add Account Manually</h5>
                                <p>Open Google Authenticator and follow these steps:</p>
                                <ol>
                                    <li>Tap the <strong>+</strong> button</li>
                                    <li>Select <strong>"Enter a setup key"</strong></li>
                                    <li>For <strong>Account name</strong>, enter: <strong><?php echo htmlspecialchars($email); ?></strong></li>
                                    <li>For <strong>Your key</strong>, enter the secret key below:</li>
                                </ol>

                                <div class="secret-key" onclick="this.select();">
                                    <?php echo chunk_split($user['google2fa_secret'], 4, ' '); ?>
                                </div>

                                <div class="text-center">
                                    <button class="btn btn-sm btn-outline-primary" onclick="copySecretKey()">
                                        <i class="bi bi-clipboard"></i> Copy Secret Key
                                    </button>
                                </div>

                                <div class="mt-3">
                                    <p>Make sure <strong>"Time based"</strong> is selected, then tap <strong>Add</strong>.</p>
                                </div>
                            </div>

                            <div class="step-container">
                                <h5><span class="step-number">3</span> Verify Setup</h5>
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

                            <div class="mt-3 text-center">
                                <a href="setup-2fa.php" class="text-decoration-none">
                                    <i class="bi bi-arrow-left"></i> Return to QR code method
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

        // Copy secret key to clipboard
        function copySecretKey() {
            const secretKey = "<?php echo $user['google2fa_secret']; ?>";
            navigator.clipboard.writeText(secretKey).then(function() {
                alert("Secret key copied to clipboard!");
            }).catch(function(err) {
                console.error('Could not copy text: ', err);
            });
        }
    </script>
</body>

</html>
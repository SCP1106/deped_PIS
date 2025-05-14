<?php
// Start session
session_start();

// Include database connection
require_once 'database.php';
require_once 'simple-2fa.php'; // Include the simple-2fa.php file for TOTP verification

// CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = false;

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    // Get user ID from session
    $user_id = $_SESSION['user_id'] ?? null;

    if (!$user_id) {
        $errors[] = "User session not found. Please log in again.";
    } else {
        // Get OTP from form
        $otp_code = trim($_POST['otp_code'] ?? '');

        if (empty($otp_code)) {
            $errors[] = "Please enter the verification code.";
        } else {
            // Get user's secret key from database
            $stmt = $mysqli->prepare("SELECT google2fa_secret FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            $stmt->close();

            if (!$user || empty($user['google2fa_secret'])) {
                $errors[] = "User not found or 2FA not set up.";
            } else {
                // Verify OTP using the verifyCode function from simple-2fa.php
                if (verifyCode($user['google2fa_secret'], $otp_code)) {
                    // OTP is valid
                    $success = true;

                    // Mark user as verified in the database if needed
                    $stmt = $mysqli->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();

                    $_SESSION['success'] = "Your account has been verified successfully!";

                    // Redirect to dashboard or home page
                    header("Location: otp-success.php");
                    exit;
                } else {
                    $errors[] = "Invalid or expired verification code. Please try again.";
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP | DepEd</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
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
    </style>
</head>

<body>
    <div class="main-content">
        <div class="container py-4 py-md-5">
            <div class="row justify-content-center">
                <div class="col-12 col-sm-10 col-md-8 col-lg-5">
                    <div class="text-center mb-4">
                        <h1 class="fw-bold" style="color: #2e8b57;">Verify Your Account</h1>
                        <p class="text-muted">Enter the verification code from your authenticator app</p>
                    </div>

                    <?php if (!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <?php if ($success): ?>
                        <div class="alert alert-success">
                            Your account has been verified successfully!
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-3 p-sm-4 p-md-5">
                            <form method="post" novalidate>
                                <div class="mb-4">
                                    <label for="otp_code" class="form-label">Verification Code</label>
                                    <input
                                        type="text"
                                        class="form-control form-control-lg text-center"
                                        id="otp_code"
                                        name="otp_code"
                                        placeholder="Enter 6-digit code"
                                        maxlength="6"
                                        autocomplete="one-time-code"
                                        inputmode="numeric"
                                        pattern="[0-9]*"
                                        required>
                                </div>

                                <!-- CSRF Protection -->
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                                <button type="submit" class="btn btn-primary w-100 py-2">Verify Account</button>
                            </form>

                            <div class="mt-4 text-center">
                                <p class="text-muted">
                                    Didn't receive the code?
                                    <a href="resend-otp.php" class="link-primary fw-medium">Resend Code</a>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Format verification code input
        document.addEventListener('DOMContentLoaded', function() {
            const otpInput = document.getElementById('otp_code');
            if (otpInput) {
                otpInput.addEventListener('input', function() {
                    this.value = this.value.replace(/[^0-9]/g, '').substring(0, 6);
                });
            }
        });
    </script>
</body>

</html>
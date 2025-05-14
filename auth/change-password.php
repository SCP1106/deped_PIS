<?php
// Start session
session_start();

// Include database connection and session manager
require_once 'database.php';
require_once 'session.php';

// Create session manager
$sessionManager = new SessionManager($mysqli);

// Validate session
$user = $sessionManager->validateSession();

// Redirect to login if not logged in
if (!$user) {
    header("Location: login.php");
    exit;
}

// CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = false;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    // Validate current password
    $current_password = $_POST["current_password"] ?? "";
    if (!password_verify($current_password, $user["password"])) {
        $errors[] = "Current password is incorrect";
    }

    // Validate new password
    $password = $_POST["password"] ?? "";
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters";
    }

    if (!preg_match("/[a-z]/i", $password)) {
        $errors[] = "Password must contain at least one letter";
    }

    if (!preg_match("/[0-9]/", $password)) {
        $errors[] = "Password must contain at least one number";
    }

    if (!preg_match("/[@$!%*?&]/", $password)) {
        $errors[] = "Password must contain at least one special character";
    }

    $password_confirmation = $_POST["password_confirmation"] ?? "";
    if ($password !== $password_confirmation) {
        $errors[] = "Passwords must match";
    }

    // Update password if no errors
    if (empty($errors)) {
        // Hash new password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);

        // Update user password
        $stmt = $mysqli->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->bind_param("si", $password_hash, $user["id"]);
        $stmt->execute();
        $stmt->close();

        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password | Your Website</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Your Website</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Welcome, <?= htmlspecialchars($user["first_name"]) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="../../logout.php">Log out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-6">
                <h1 class="mb-4">Change Password</h1>

                <?php if ($success): ?>
                    <div class="alert alert-success mb-4">
                        Your password has been changed successfully!
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-3 p-sm-4 p-md-5">
                        <form method="post" novalidate>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleCurrentPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="password" class="form-label">New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
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
                                        <i class="bi bi-x-circle"></i> At least one special character (@$!%*?&)
                                    </li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <label for="password_confirmation" class="form-label">Confirm New Password</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" required>
                                    <button class="btn btn-outline-secondary" type="button" id="toggleConfirmPassword">
                                        <i class="bi bi-eye"></i>
                                    </button>
                                </div>
                            </div>

                            <!-- CSRF Protection -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="d-flex justify-content-between">
                                <a href="profile.php" class="btn btn-outline-secondary">Back to Profile</a>
                                <button type="submit" class="btn btn-primary">Change Password</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Password visibility toggle
        document.getElementById('toggleCurrentPassword').addEventListener('click', function() {
            togglePasswordVisibility('current_password', this.querySelector('i'));
        });

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
        if (password) {
            password.addEventListener('input', function() {
                const value = this.value;

                // Check length
                updateCheckStatus(
                    document.getElementById('lengthCheck'),
                    value.length >= 8
                );

                // Check uppercase
                updateCheckStatus(
                    document.getElementById('uppercaseCheck'),
                    /[A-Z]/.test(value)
                );

                // Check lowercase
                updateCheckStatus(
                    document.getElementById('lowercaseCheck'),
                    /[a-z]/.test(value)
                );

                // Check number
                updateCheckStatus(
                    document.getElementById('numberCheck'),
                    /[0-9]/.test(value)
                );

                // Check special character
                updateCheckStatus(
                    document.getElementById('specialCheck'),
                    /[@$!%*?&]/.test(value)
                );
            });
        }

        function updateCheckStatus(element, isValid) {
            if (!element) return;

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
<?php
// Start session
session_start();

// CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Rate limiting
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../core/rate-limiter.php';

$is_rate_limited = false;
$time_until_unlocked = 0;

// Check for rate limiting
$rateLimiter = new RateLimiter($mysqli, $_SERVER['REMOTE_ADDR'], null, 5, 300); // 5 attempts per 5 minutes
if ($rateLimiter->isRateLimited()) {
    $is_rate_limited = true;
    $time_until_unlocked = $rateLimiter->getTimeUntilUnlocked();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DepEd: Registration</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
        rel="stylesheet" />
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <link rel="stylesheet" href="../assets/css/styles.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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

        #sidebar-container .sidebar {
            height: 100vh;
            overflow-y: auto;
            /* Allows vertical scrolling */
        }

        .registration-card {
            background-color: #ffffff;
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            padding: 2rem;
            max-width: 500px;
            width: 100%;
            transition: all 0.3s ease;
        }

        .registration-card:hover {
            transform: translateY(-10px);
        }

        .registration-card h2 {
            color: #2e8b57;
            margin-bottom: 1.5rem;
            text-align: center;
            font-size: 2rem;
        }

        .form-control,
        .form-select {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #e0e0e0;
            transition: all 0.3s ease;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: #2e8b57;
            box-shadow: 0 0 0 0.2rem rgba(46, 139, 87, 0.25);
        }

        .btn-primary {
            background-color: #2e8b57;
            border-color: #2e8b57;
            border-radius: 8px;
            padding: 0.75rem 1.5rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #246c46;
            border-color: #246c46;
        }

        .form-label {
            font-weight: 500;
            color: #333;
        }

        .invalid-feedback {
            font-size: 0.875rem;
        }

        .skeleton-nav-item,
        .skeleton-logo,
        .skeleton-text {
            background-color: #e0e0e0;
            border-radius: 4px;
        }

        /* Optional: Customize skeleton loading animation */
        .skeleton-text {
            height: 1.5rem;
            width: 80%;
        }

        .skeleton-logo {
            height: 40px;
            width: 40px;
            border-radius: 50%;
        }

        .skeleton-nav-item {
            height: 2rem;
            width: 80%;
            margin-bottom: 0.5rem;
        }
    </style>
        <link rel="stylesheet" href="../css/side-topbar.css" />
</head>

<body>

<div id="sidebar-container">

    </div>
    <div id="topbar-container">

    </div>

    <div class="main-content p-4">
        <div class="registration-card">
            <h2>Create an Account</h2>

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

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                    <?php unset($_SESSION['success']); ?>
                </div>
                <?php if (isset($_SESSION['email_error'])): ?>
                    <div class="alert alert-warning">
                        <p><strong>Email Error:</strong> <?php echo htmlspecialchars($_SESSION['email_error']); ?></p>
                        <p>Please check your email configuration or use the activation link below.</p>
                        <?php unset($_SESSION['email_error']); ?>
                    </div>
                <?php endif; ?>
                <!-- <?php if (isset($_SESSION['debug_activation_link'])): ?>
                    <div class="alert alert-info">
                        <p><strong>Activation Link:</strong></p>
                        <a href="<?php echo htmlspecialchars($_SESSION['debug_activation_link']); ?>" class="link-primary">
                            <?php echo htmlspecialchars($_SESSION['debug_activation_link']); ?>
                        </a>
                        <?php unset($_SESSION['debug_activation_link']); ?>
                    </div> -->
            <?php endif; ?>
        <?php endif; ?>

        <?php if ($is_rate_limited): ?>
            <div class="alert alert-warning">
                Too many registration attempts. Please try again in <?= ceil($time_until_unlocked / 60) ?> minutes.
            </div>
        <?php else: ?>
            <form id="registrationForm" action="../process/process-register.php" method="post" class="needs-validation" novalidate>
                <div class="form-floating mb-4">
                    <input
                        type="email"
                        class="form-control"
                        id="email"
                        name="email"
                        placeholder="Email address"
                        required
                        value="<?php echo isset($_SESSION['old']['email']) ? htmlspecialchars($_SESSION['old']['email']) : ''; ?>" />
                    <label for="email">Email address</label>
                    <div class="invalid-feedback">
                        Please provide a valid email address.
                    </div>
                </div>

                <div class="form-floating mb-4">
                    <select class="form-select" id="role" name="role" required>
                        <option value="" disabled selected>Select role</option>
                        <option value="Admin" <?php echo (isset($_SESSION['old']['role']) && $_SESSION['old']['role'] == 'Admin') ? 'selected' : ''; ?>>Admin</option>
                        <option value="OSDS" <?php echo (isset($_SESSION['old']['role']) && $_SESSION['old']['role'] == 'OSDS') ? 'selected' : ''; ?>>OSDS</option>
                        <option value="CID" <?php echo (isset($_SESSION['old']['role']) && $_SESSION['old']['role'] == 'CID') ? 'selected' : ''; ?>>CID</option>
                        <option value="User" <?php echo (isset($_SESSION['old']['role']) && $_SESSION['old']['role'] == 'User') ? 'selected' : ''; ?>>User</option>
                    </select>
                    <label for="role">Role</label>
                    <div class="invalid-feedback">
                        Please select a role.
                    </div>
                </div>

                <!-- CSRF Protection -->
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                <button type="submit" class="btn btn-primary w-100" <?= $is_rate_limited ? 'disabled' : '' ?>>Register</button>


            </form>


        <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            // Form validation
            const form = document.getElementById('registrationForm');
            if (form) {
                form.addEventListener('submit', function(event) {
                    if (!form.checkValidity()) {
                        event.preventDefault();
                        event.stopPropagation();
                    }
                    form.classList.add('was-validated');
                });
            }
        });
    </script>
</body>


<script src="../../js/side-topbar.js"></script>
</html>
<?php
// Clear old form data after displaying the form
if (isset($_SESSION['old'])) {
    unset($_SESSION['old']);
}
?>
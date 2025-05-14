<?php
// Start session
session_start();

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
    <title>Forgot Password | DepEd</title>
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
                        <h1 class="fw-bold" style="color: #2e8b57;">Forgot Password</h1>
                        <p class="text-muted">Enter your email to receive a password reset link</p>
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

                    <?php if (isset($_SESSION['success'])): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($_SESSION['success']); ?>
                            <?php unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['debug_reset_link'])): ?>
                        <div class="alert alert-info">
                            <p><strong>Development Mode:</strong> Use this reset link:</p>
                            <a href="<?php echo htmlspecialchars($_SESSION['debug_reset_link']); ?>" class="link-primary">
                                <?php echo htmlspecialchars($_SESSION['debug_reset_link']); ?>
                            </a>
                            <?php unset($_SESSION['debug_reset_link']); ?>
                        </div>
                    <?php endif; ?>

                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-3 p-sm-4 p-md-5">
                            <form method="post" action="send-password-reset.php" novalidate>
                                <div class="mb-4">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" class="form-control" id="email" name="email" placeholder="name@example.com" required>
                                </div>

                                <!-- CSRF Protection -->
                                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                                <button type="submit" class="btn btn-primary w-100 py-2">Send Reset Link</button>
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
</body>

</html>
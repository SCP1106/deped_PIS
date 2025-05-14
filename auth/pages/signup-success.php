<?php
// Start session
session_start();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Successful | Your Website</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <div class="min-vh-100 bg-gradient d-flex align-items-center">
        <div class="container py-5">
            <div class="row justify-content-center">
                <div class="col-12 col-md-8 col-lg-6">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-4 p-md-5 text-center">
                            <h1 class="fw-bold mb-4">Signup Successful</h1>

                            <?php if (isset($_SESSION['success'])): ?>
                                <div class="alert alert-success mb-4">
                                    <?php echo htmlspecialchars($_SESSION['success']); ?>
                                    <?php unset($_SESSION['success']); ?>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-success mb-4">
                                    Your account has been created successfully! Please check your email to activate your account.
                                </div>
                            <?php endif; ?>

                            <p>Once you've activated your account, you can log in.</p>
                            <a href="login.php" class="btn btn-primary mt-3">Go to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
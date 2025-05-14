<?php
// Start session
session_start();

// Check if success message exists
if (!isset($_SESSION['success'])) {
    header("Location: index.php");
    exit;
}

$success_message = $_SESSION['success'];
unset($_SESSION['success']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Successful | DepEd</title>
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
                <div class="col-12 col-sm-10 col-md-8 col-lg-6">
                    <div class="card shadow-lg border-0 rounded-4">
                        <div class="card-body p-3 p-sm-4 p-md-5 text-center">
                            <div class="mb-4">
                                <svg xmlns="http://www.w3.org/2000/svg" width="64" height="64" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                                    <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z" />
                                </svg>
                            </div>
                            <h1 class="fw-bold mb-4" style="color: #2e8b57;">Verification Successful</h1>

                            <div class="alert alert-success mb-4">
                                <?php echo htmlspecialchars($success_message); ?>
                            </div>

                            <p>Your account has been successfully verified. You can now log in to access your account.</p>
                            <a href="login.php" class="btn btn-primary mt-3">Go to Login</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>

</html>
<?php
// Start session
session_start();

// Debug CSRF tokens
if (isset($_POST['csrf_token']) && isset($_SESSION['csrf_token'])) {
    error_log("POST token: " . $_POST['csrf_token']);
    error_log("SESSION token: " . $_SESSION['csrf_token']);
} else {
    error_log("POST token exists: " . (isset($_POST['csrf_token']) ? 'Yes' : 'No'));
    error_log("SESSION token exists: " . (isset($_SESSION['csrf_token']) ? 'Yes' : 'No'));
}

// Modify the CSRF validation to be more lenient during debugging
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token'])) {
    error_log("CSRF token missing in POST or SESSION");
    // Create a new token if missing
    if (!isset($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        error_log("Created new CSRF token: " . $_SESSION['csrf_token']);
    }
} else if ($_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    error_log("CSRF token mismatch");
    // For debugging purposes, we'll continue anyway
    // In production, you would want to die("CSRF token validation failed");
}

// Include database connection and rate limiter
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../core/rate-limiter.php';

// CSRF protection
if (!isset($_POST['csrf_token']) || !isset($_SESSION['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
    die("CSRF token validation failed");
}

// Check if email is verified
if (!isset($_SESSION['verified_email'])) {
    header("Location: register.php");
    exit;
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Rate limiting
    $rateLimiter = new RateLimiter($mysqli, $_SERVER['REMOTE_ADDR'], null, 10, 300); // 10 attempts per 5 minutes

    if ($rateLimiter->isRateLimited()) {
        $timeLeft = $rateLimiter->getTimeUntilUnlocked();
        $_SESSION['errors'] = ["Too many signup attempts. Please try again in " . ceil($timeLeft / 60) . " minutes."];
        header("Location: ../pages/signup.php");
        exit;
    }

    // Sanitize and validate input
    $email = $_SESSION['verified_email']; // Email is already verified
    $first_name = trim($_POST['first_name'] ?? '');
    $middle_name = trim($_POST['middle_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $birthday = $_POST['birthday'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $password = $_POST['password'] ?? '';
    $password_confirmation = $_POST['password_confirmation'] ?? '';
    $use_2fa = isset($_POST['use_2fa']) ? 1 : 0;

    // Error array to store validation errors
    $errors = [];

    // Validate required fields
    if (empty($first_name)) $errors['first_name'] = "First name is required.";
    if (empty($last_name)) $errors['last_name'] = "Last name is required.";
    if (empty($birthday)) {
        $errors['birthday'] = "Birthday is required.";
    } else {
        // Check if birthday is not in the future
        if (strtotime($birthday) > strtotime(date('Y-m-d'))) {
            $errors['birthday'] = "Birthday cannot be a future date.";
        }
    }
    if (empty($gender)) $errors['gender'] = "Gender is required.";
    if (empty($password)) $errors['password'] = "Password is required.";

    // Validate name fields to allow only letters and spaces
    if (!empty($first_name) && !preg_match("/^[A-Za-z ]+$/", $first_name)) {
        $errors['first_name'] = "First name must contain only letters.";
    }
    if (!empty($middle_name) && !preg_match("/^[A-Za-z ]*$/", $middle_name)) {
        $errors['middle_name'] = "Middle name must contain only letters.";
    }
    if (!empty($last_name) && !preg_match("/^[A-Za-z ]+$/", $last_name)) {
        $errors['last_name'] = "Last name must contain only letters.";
    }
    if ($password !== $password_confirmation) {
        $errors['password_confirmation'] = "Passwords do not match.";
    }

    // Check password strength
    if (!preg_match('/^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/', $password)) {
        $errors['password'] = "Password must be at least 8 characters long, include an uppercase letter, a lowercase letter, a number, and a special character.";
    }

    // Validate terms of service agreement
    if (empty($_POST['terms'])) {
        $errors['terms'] = "You must agree to the Terms of Service and Privacy Policy.";
    }

    // If there are errors, redirect back with errors
    if (!empty($errors)) {
        $_SESSION['errors'] = $errors;
        $_SESSION['old'] = $_POST;
        header("Location: ../pages/signup.php");
        exit;
    }

    // Record the attempt
    $rateLimiter->recordAttempt();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    // Generate 2FA secret if enabled
    $google2fa_secret = null;
    if ($use_2fa) {
        // Check if RobThree library is available
        if (class_exists('RobThree\Auth\TwoFactorAuth')) {
            $tfa = new \RobThree\Auth\TwoFactorAuth();
            $google2fa_secret = $tfa->createSecret();
        } else {
            // Fallback to a simple secret generation
            require_once __DIR__ . '/../2fa/simple-2fa.php';
            $google2fa_secret = generateSecretKey();
        }
    }

    // Update user in the database
    try {
        $stmt = $mysqli->prepare("UPDATE users SET 
           first_name = ?, 
           middle_name = ?, 
           last_name = ?, 
           birthday = ?, 
           gender = ?, 
           password = ?, 
           google2fa_secret = ?,
           account_activation_hash = NULL
           WHERE email = ?");

        $stmt->bind_param(
            "ssssssss",
            $first_name,
            $middle_name,
            $last_name,
            $birthday,
            $gender,
            $hashed_password,
            $google2fa_secret,
            $email
        );

        $stmt->execute();

        if ($stmt->affected_rows === 0) {
            throw new Exception("Error updating user account");
        }
        $stmt->close();

        // Assign default role
        $stmt_get_id = $mysqli->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt_get_id) {
            throw new Exception("Error preparing user ID query: " . $mysqli->error);
        }

        $stmt_get_id->bind_param("s", $email);
        $stmt_get_id->execute();
        $result_id = $stmt_get_id->get_result();
        $user_data = $result_id->fetch_assoc();
        $stmt_get_id->close();

        if ($user_data) {
            $user_id = $user_data['id'];
            $default_role = 'user';

            // Check if user_roles table exists
            $checkTable = $mysqli->query("SHOW TABLES LIKE 'user_roles'");
            $tableExists = ($checkTable && $checkTable->num_rows > 0);
            $checkTable->close();

            if ($tableExists) {
                // Check for existing role
                $stmt_check_role = $mysqli->prepare("SELECT COUNT(*) as count FROM user_roles WHERE user_id = ? AND role = ?");
                $stmt_check_role->bind_param("is", $user_id, $default_role);
                $stmt_check_role->execute();
                $result_check = $stmt_check_role->get_result();
                $role_count = $result_check->fetch_assoc()['count'];
                $stmt_check_role->close();

                if ($role_count === 0) {
                    // Insert default role
                    $stmt_role = $mysqli->prepare("INSERT INTO user_roles (user_id, role) VALUES (?, ?)");
                    if (!$stmt_role) {
                        throw new Exception("Error preparing role insert: " . $mysqli->error);
                    }

                    $stmt_role->bind_param("is", $user_id, $default_role);
                    if (!$stmt_role->execute()) {
                        error_log("Error assigning default role: " . $stmt_role->error);
                    }
                    $stmt_role->close();
                }
            } else {
                // Create user_roles table if it doesn't exist
                $create_table_sql = "CREATE TABLE IF NOT EXISTS user_roles (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    user_id INT NOT NULL,
                    role VARCHAR(50) NOT NULL,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (user_id) REFERENCES users(id),
                    UNIQUE KEY unique_user_role (user_id, role)
                )";
                if (!$mysqli->query($create_table_sql)) {
                    error_log("Error creating user_roles table: " . $mysqli->error);
                }
            }
        }

        // If 2FA is enabled, redirect to setup page
        if ($use_2fa) {
            $_SESSION['setup_2fa'] = true;
            $_SESSION['user_email'] = $email;
            header("Location: /auth/2fa/setup-2fa.php");
            exit;
        }

        // Redirect to login page
        $_SESSION['success'] = "Account setup complete! You can now log in.";
        unset($_SESSION['verified_email']); // Clear the verified email
        header("Location: /auth/pages/login.php");
        exit;
    } catch (Exception $e) {
        // Log the error but don't expose details to users
        error_log("Database error: " . $e->getMessage());

        // Handle database errors
        $_SESSION['errors'] = ["An error occurred while setting up your account. Please try again."];
        header("Location: ../pages/signup.php");
        exit;
    }
} else {
    // Redirect if accessed directly
    header("Location: ../pages/signup.php");
    exit;
}

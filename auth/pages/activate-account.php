<?php
// Start session
session_start();

// Enable error reporting for debugging (remove in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// --- Check Database Connection ---
$mysqli = require_once __DIR__ . '/../core/database.php';
if (!$mysqli || $mysqli->connect_errno) {
    error_log("Database connection failed in activate-account: " . ($mysqli ? $mysqli->connect_error : 'mysqli object not returned from database.php'));
    die("Database connection error. Please contact support.");
}

// --- Check SessionManager Class ---
require_once __DIR__ . '/../core/session.php';
if (!class_exists('SessionManager')) {
    error_log("SessionManager class not found after including session.php in activate-account.");
    die("Session component error. Please contact support.");
}

// Log access with improved logging
$token = $_GET["token"] ?? 'no token';
error_log("activate-account.php accessed with token: " . $token);

// Check if the token is provided in the URL
if (!isset($_GET["token"])) {
    die("Token is missing from the URL.");
}

$token = $_GET["token"];
$token_hash = hash("sha256", $token);

// Start transaction
$mysqli->begin_transaction();

try {
    // --- 1. Find the user by activation hash ---
    $sql_find = "SELECT id, email FROM users WHERE account_activation_hash = ?";
    $stmt_find = $mysqli->prepare($sql_find);
    if (!$stmt_find) throw new Exception("SQL prepare error (find user): " . $mysqli->error);

    $stmt_find->bind_param("s", $token_hash);
    $stmt_find->execute();
    $result = $stmt_find->get_result();
    $user = $result->fetch_assoc();
    $stmt_find->close();

    if ($user === null) {
        throw new Exception("Invalid or expired token.");
    }

    $user_id = $user['id'];
    $user_email = $user['email'];

    // --- 2. Activate the user account ---
    $sql_activate = "UPDATE users 
                    SET account_activation_hash = NULL,
                        is_verified = 1 
                    WHERE id = ?";
    $stmt_activate = $mysqli->prepare($sql_activate);
    if (!$stmt_activate) throw new Exception("SQL prepare error (activate user): " . $mysqli->error);

    $stmt_activate->bind_param("i", $user_id);
    if (!$stmt_activate->execute()) throw new Exception("Error activating account: " . $stmt_activate->error);

    $activation_success = false;
    if ($stmt_activate->affected_rows > 0) {
        $_SESSION['verified_email'] = $user_email;
        $activation_success = true;
    } else {
        error_log("Account activation did not affect any rows for user ID: " . $user_id);
    }
    $stmt_activate->close();

    // --- 3. Assign the 'admin' role ---
    $admin_role = 'admin';
    $checkTable = $mysqli->query("SHOW TABLES LIKE 'user_roles'");
    $tableExists = ($checkTable && $checkTable->num_rows > 0);
    $checkTable->close();

    if ($tableExists) {
        // Check if user already has admin role
        $stmt_check_role = $mysqli->prepare("SELECT COUNT(*) as count FROM user_roles WHERE user_id = ? AND role = ?");
        $stmt_check_role->bind_param("is", $user_id, $admin_role);
        $stmt_check_role->execute();
        $result_check = $stmt_check_role->get_result();
        $role_count = $result_check->fetch_assoc()['count'];
        $stmt_check_role->close();

        if ($role_count === 0) {
            $sql_role = "INSERT INTO user_roles (user_id, role) VALUES (?, ?)";
            $stmt_role = $mysqli->prepare($sql_role);
            if (!$stmt_role) throw new Exception("SQL prepare error (assign role): " . $mysqli->error);

            $stmt_role->bind_param("is", $user_id, $admin_role);
            if (!$stmt_role->execute()) throw new Exception("Error assigning admin role: " . $stmt_role->error);
            $stmt_role->close();
        }
    } else {
        error_log("`user_roles` table not found. Cannot assign admin role to user ID: " . $user_id);
    }

    // --- 4. Initialize session ---
    $sessionManager = new SessionManager($mysqli);
    // Always regenerate session ID to avoid duplicates
    session_regenerate_id(true);
    try {
        $sessionManager->createSession($user_id);
    } catch (Exception $e) {
        if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
            // Try once more with a new session ID
            session_regenerate_id(true);
            $sessionManager->createSession($user_id);
        } else {
            throw $e; // Rethrow if it's a different error
        }
    }
    $_SESSION["user_id"] = $user_id;
    $_SESSION["authenticated"] = true;

    // --- After activation statement: handle redirect ---
    if ($activation_success) {
        // Commit Transaction
        $mysqli->commit();
        header('Location: signup.php');
        exit;
    }

    // Commit Transaction
    $mysqli->commit();

    // --- 5. Redirect to Signup (not Dashboard) ---
    header("Location: signup.php?user_id=" . urlencode($user_id));
    exit;
} catch (Exception $e) {
    // Rollback Transaction on Error
    $mysqli->rollback();

    // Log the error
    error_log("Activation Error: " . $e->getMessage());

    // Show error message
    die("An error occurred during account activation. Please contact support or try again later.");
} finally {
    // Close the database connection
    if ($mysqli) {
        $mysqli->close();
    }
}

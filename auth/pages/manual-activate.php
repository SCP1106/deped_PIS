<?php
// This script manually activates an account by email

// Include database connection
require_once __DIR__ . '/../core/database.php';

echo "<h1>Manual Account Activation</h1>";

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the email from the form
    $email = filter_var($_POST['email'], FILTER_VALIDATE_EMAIL);

    if (!$email) {
        echo "<div style='color: red;'>Please enter a valid email address.</div>";
    } else {
        // Check if the email exists in the database
        $stmt = $mysqli->prepare("SELECT id, email, account_activation_hash FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if (!$user) {
            echo "<div style='color: red;'>Email not found in the database. Please register first.</div>";
        } else if (!$user['account_activation_hash']) {
            echo "<div style='color: orange;'>This account is already activated.</div>";
        } else {
            // Update the user's activation hash to NULL (activated)
            $update_stmt = $mysqli->prepare("UPDATE users SET account_activation_hash = NULL WHERE id = ?");
            $update_stmt->bind_param("i", $user['id']);
            $update_stmt->execute();

            if ($update_stmt->affected_rows > 0) {
                echo "<div style='color: green;'>Account activated successfully!</div>";
                echo "<p>The user can now complete their registration at: <a href='signup.php'>signup.php</a></p>";

                // Start session and set verified email
                session_start();
                $_SESSION['verified_email'] = $user['email'];

                echo "<p>Session has been set with verified_email = " . htmlspecialchars($user['email']) . "</p>";
                echo "<p><a href='signup.php' class='button'>Continue to Signup</a></p>";
            } else {
                echo "<div style='color: red;'>Failed to activate account.</div>";
            }
        }
    }
} else {
    echo "<div style='color: red;'>Invalid request method. Please use the form.</div>";
    echo "<p><a href='test-activation.php'>Go back to the test page</a></p>";
}

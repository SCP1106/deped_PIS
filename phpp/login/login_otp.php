<?php
// Start the session if not started already
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include '../Connect/credsDB.php';
include 'otpHandler.php'; // Include the shared OTP functions

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input values
    $username = isset($_POST['username']) ? mysqli_real_escape_string($conn, $_POST['username']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    // Prepare statement for username
    $stmt = $conn->prepare("SELECT userNo, email FROM username_tb WHERE email = ?");
    $stmt->bind_param("s", $username); // Bind the username from POST
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Username exists, proceed with password check
        $row = $result->fetch_assoc();
        $username_no = $row['userNo'];
        $user_email = $row['email']; // Get user email for OTP

        // Prepare statement for password verification using `username_no`
        $passw_stmt = $conn->prepare("SELECT p.password FROM password_tb p WHERE p.passNo = ?");
        
        if ($passw_stmt) {
             // Bind parameters
            $passw_stmt->bind_param("i", $username_no);
            $passw_stmt->execute();
            $passw_result = $passw_stmt->get_result();

            if ($passw_result->num_rows > 0) {
                $pass_row = $passw_result->fetch_assoc();
                $hashed_password = $pass_row['password'];

                // Verify password
                if ($password == $hashed_password) {
                    // Password matches, generate OTP
                    $otp_code = generateOTP(); // Generate OTP
                    storeOTP($username_no, $otp_code);
                    // Store OTP in session
                    $_SESSION['otp'] = $otp_code;
                    $_SESSION['user_id'] = $username_no;
                    // Send OTP via email
                    if (sendOTPEmail($user_email, $otp_code)) {
                        echo json_encode(['success' => true, 'message' => 'OTP generated and sent!']);

                    } else {
                        //Failed Sent OTP
                    }
                } else {
                    // Incorrect password
                }
            } else {
                // Password not found in the database
            }

            // Close password statement
            $passw_stmt->close();
        } else {
            // Log error if statement fails to prepare
            error_log("Failed to prepare password statement: " . $conn->error);
        }
    } else {
        // Username does not exist

    }

    // Close username statement
    $stmt->close();
}
// Close database connection
$conn->close();


?>

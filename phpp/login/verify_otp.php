<?php
// Start a session to access session variables
session_start();

// Include database connection
include '../Connect/credsDB.php';

// Set the response header to indicate JSON output
header('Content-Type: application/json');

// Check if the request method is POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get the JSON input from the request
    $inputData = json_decode(file_get_contents('php://input'), true);

    // Check if the OTP is provided in the request
    if (isset($inputData['otp'])) {
        $inputOtp = $inputData['otp'];
        $userId = $_SESSION['user_id']; // Retrieve user ID from session

        // Call the function to verify the OTP
        if (verifyOTP($userId, $inputOtp)) {
            echo json_encode(['success' => true, 'message' => 'OTP is valid!']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid OTP!']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'OTP not provided.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}

// Function to verify OTP
function verifyOTP($user_id, $inputOtp) {
    global $conn;

    $otp_code = 0;
    // Prepare the SQL statement to retrieve the OTP
    $stmt = $conn->prepare("SELECT otp_code FROM otps_log WHERE user_id = ? ORDER BY created_at DESC LIMIT 1");
    $stmt->bind_param("i", $user_id);
    // Execute the statement
    if ($stmt->execute()) {
        $stmt->bind_result($otp_code);
        $stmt->fetch();

        // Compare the retrieved OTP with the input OTP
        return ($inputOtp === $otp_code);
    } else {
        // Log error if the statement fails
        error_log("Error retrieving OTP: " . $stmt->error);
        return false;
    }
}
?>

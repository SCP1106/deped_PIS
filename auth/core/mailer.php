<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . "/../vendor/autoload.php";

// Only declare the class if it doesn't already exist
if (!class_exists('OTPHandler')) {
    /**
     * OTP Handler Class
     */
    class OTPHandler
    {
        private $mysqli;
        private $table_name = 'otps_log';
        private $default_expiry = 15; // Default expiry time in minutes

        /**
         * Constructor
         * 
         * @param mysqli $mysqli Database connection
         */
        public function __construct($mysqli)
        {
            $this->mysqli = $mysqli;
            $this->createTableIfNotExists();
        }

        /**
         * Generate a 6-digit OTP code
         * 
         * @return string The generated OTP code
         */
        public function generateOTP()
        {
            return str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT);
        }

        /**
         * Store OTP in the database
         * 
         * @param int $user_id User ID
         * @param string $otp_code The OTP code
         * @param int $expiry_minutes Minutes until OTP expires (default: 15)
         * @return bool True if successful, false otherwise
         */
        public function storeOTP($user_id, $otp_code, $expiry_minutes = null)
        {
            // Use default expiry if not specified
            $expiry_minutes = $expiry_minutes ?? $this->default_expiry;

            // Calculate expiry time
            $expires_at = date('Y-m-d H:i:s', time() + ($expiry_minutes * 60));

            // Invalidate any existing OTPs for this user
            $this->invalidateExistingOTPs($user_id);

            // Store the new OTP
            $stmt = $this->mysqli->prepare("INSERT INTO {$this->table_name} 
                                        (user_id, otp_code, expires_at, created_at) 
                                        VALUES (?, ?, ?, NOW())");
            $stmt->bind_param("iss", $user_id, $otp_code, $expires_at);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        }

        /**
         * Verify an OTP code for a user
         * 
         * @param int $user_id User ID
         * @param string $otp_code The OTP code to verify
         * @return bool True if valid, false otherwise
         */
        public function verifyOTP($user_id, $otp_code)
        {
            // Get the most recent valid OTP for this user
            $stmt = $this->mysqli->prepare("SELECT id, otp_code, expires_at 
                                        FROM {$this->table_name} 
                                        WHERE user_id = ? 
                                        AND used = 0 
                                        AND expires_at > NOW() 
                                        ORDER BY created_at DESC 
                                        LIMIT 1");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $otp_data = $result->fetch_assoc();
            $stmt->close();

            if (!$otp_data) {
                return false; // No valid OTP found for this user
            }

            // Verify the OTP code
            if ($otp_data['otp_code'] === $otp_code) {
                // Mark OTP as used
                $this->markOTPAsUsed($otp_data['id']);
                return true;
            }

            return false;
        }

        /**
         * Mark an OTP as used
         * 
         * @param int $otp_id OTP ID
         * @return bool True if successful, false otherwise
         */
        private function markOTPAsUsed($otp_id)
        {
            $stmt = $this->mysqli->prepare("UPDATE {$this->table_name} SET used = 1 WHERE id = ?");
            $stmt->bind_param("i", $otp_id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        }

        /**
         * Invalidate existing OTPs for a user
         * 
         * @param int $user_id User ID
         * @return bool True if successful, false otherwise
         */
        private function invalidateExistingOTPs($user_id)
        {
            $stmt = $this->mysqli->prepare("UPDATE {$this->table_name} SET used = 1 WHERE user_id = ? AND used = 0");
            $stmt->bind_param("i", $user_id);
            $result = $stmt->execute();
            $stmt->close();

            return $result;
        }

        /**
         * Send OTP via email
         * 
         * @param string $email Recipient email
         * @param string $otp_code The OTP code
         * @param PHPMailer $mail Configured PHPMailer instance
         * @return bool True if sent successfully, false otherwise
         */
        public function sendOTPEmail($email, $otp_code, $mail)
        {
            try {
                $mail->clearAddresses(); // Clear any previous addresses
                $mail->addAddress($email);
                $mail->Subject = 'Your Verification Code';

                // Create HTML body with the OTP code
                $mail->Body = <<<HTML
                <html>
                <head>
                    <style>
                        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                        .otp-box { background-color: #f5f5f5; padding: 15px; border-radius: 5px; text-align: center; margin: 20px 0; }
                        .otp-code { font-size: 24px; font-weight: bold; letter-spacing: 5px; color: #0d6efd; }
                        .footer { font-size: 12px; color: #777; margin-top: 30px; }
                    </style>
                </head>
                <body>
                    <div class="container">
                        <h2>Your Verification Code</h2>
                        <p>Please use the following code to verify your account:</p>
                        <div class="otp-box">
                            <div class="otp-code">{$otp_code}</div>
                        </div>
                        <p>This code will expire in {$this->default_expiry} minutes.</p>
                        <p>If you didn't request this code, please ignore this email.</p>
                        <div class="footer">
                            <p>This is an automated message, please do not reply to this email.</p>
                        </div>
                    </div>
                </body>
                </html>
                HTML;

                // Send the email
                return $mail->send();
            } catch (Exception $e) {
                error_log("OTP Email Error: " . $e->getMessage());
                return false;
            }
        }

        /**
         * Get the time remaining until an OTP expires
         * 
         * @param int $user_id User ID
         * @return int|false Seconds remaining until expiry, or false if no valid OTP
         */
        public function getTimeUntilExpiry($user_id)
        {
            $stmt = $this->mysqli->prepare("SELECT expires_at 
                                        FROM {$this->table_name} 
                                        WHERE user_id = ? 
                                        AND used = 0 
                                        AND expires_at > NOW() 
                                        ORDER BY created_at DESC 
                                        LIMIT 1");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $otp_data = $result->fetch_assoc();
            $stmt->close();

            if (!$otp_data) {
                return false;
            }

            $expires_at = strtotime($otp_data['expires_at']);
            $current_time = time();

            return max(0, $expires_at - $current_time);
        }

        /**
         * Check if a user has a valid OTP
         * 
         * @param int $user_id User ID
         * @return bool True if user has a valid OTP, false otherwise
         */
        public function hasValidOTP($user_id)
        {
            $stmt = $this->mysqli->prepare("SELECT COUNT(*) as count 
                                        FROM {$this->table_name} 
                                        WHERE user_id = ? 
                                        AND used = 0 
                                        AND expires_at > NOW()");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $data = $result->fetch_assoc();
            $stmt->close();

            return $data['count'] > 0;
        }

        /**
         * Create the OTPs table if it doesn't exist
         */
        private function createTableIfNotExists()
        {
            $this->mysqli->query("CREATE TABLE IF NOT EXISTS {$this->table_name} (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                otp_code VARCHAR(6) NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                expires_at DATETIME NOT NULL,
                used TINYINT(1) DEFAULT 0,
                INDEX (user_id)
            )");
        }

        /**
         * Clean up expired OTPs
         * 
         * @return int Number of deleted records
         */
        public function cleanupExpiredOTPs()
        {
            $stmt = $this->mysqli->prepare("DELETE FROM {$this->table_name} WHERE expires_at < NOW()");
            $stmt->execute();
            $affected_rows = $stmt->affected_rows;
            $stmt->close();

            return $affected_rows;
        }
    }
}

// Update the createMailer function with better default settings
function createMailer()
{
    // Create PHPMailer instance
    $mail = new PHPMailer(true);

    // $mail->SMTPDebug = SMTP::DEBUG_SERVER; // Uncomment for debugging

    $mail->isSMTP();
    $mail->SMTPAuth = true;

    $mail->Host = "mail.rimsdone.com";
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->Username = "no-reply@oras.rimsdone.com"; // REPLACE with your Gmail address
    $mail->Password = "u0eVL8P]~M+o"; // REPLACE with your Gmail App Password

    $mail->isHtml(true);

    return $mail;
}

// If this file is included directly, return a mailer instance
if (basename($_SERVER['SCRIPT_FILENAME']) == basename(__FILE__)) {
    return createMailer();
}

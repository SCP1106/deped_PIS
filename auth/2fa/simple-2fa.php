<?php

/**
 * Simple 2FA implementation without external dependencies
 * This is a fallback when the 2FA libraries are not available
 */

/**
 * Generate a random secret key
 * 
 * @param int $length Length of the secret key
 * @return string The generated secret key
 */
function generateSecretKey($length = 16)
{
    $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < $length; $i++) {
        $secret .= $validChars[random_int(0, strlen($validChars) - 1)];
    }
    return $secret;
}

/**
 * Generate a QR code URL for Google Authenticator
 * 
 * @param string $email User's email
 * @param string $secret Secret key
 * @return string URL for the QR code
 */
function generateQRCode($email, $secret)
{
    $appName = 'DepEd App';
    $otpauth = 'otpauth://totp/' . urlencode($appName) . ':' . urlencode($email) . '?secret=' . $secret . '&issuer=' . urlencode($appName);
    return 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpauth);
}

/**
 * Verify a TOTP code
 * 
 * @param string $secret Secret key
 * @param string $code Code to verify
 * @param int $window Time window for verification
 * @return bool True if code is valid, false otherwise
 */
function verifyCode($secret, $code, $window = 1)
{
    $timeSlice = floor(time() / 30);

    // Check codes in the time window
    for ($i = -$window; $i <= $window; $i++) {
        $calculatedCode = calculateCode($secret, $timeSlice + $i);
        if ($calculatedCode === $code) {
            return true;
        }
    }

    return false;
}

/**
 * Calculate a TOTP code for a given time slice
 * 
 * @param string $secret Secret key
 * @param int $timeSlice Time slice
 * @return string The calculated code
 */
function calculateCode($secret, $timeSlice)
{
    // Decode the secret from base32
    $secretKey = base32Decode($secret);

    // Pack the time slice as a binary string
    $time = chr(0) . chr(0) . chr(0) . chr(0) . pack('N*', $timeSlice);

    // Calculate HMAC-SHA1
    $hash = hash_hmac('sha1', $time, $secretKey, true);

    // Get the offset
    $offset = ord(substr($hash, -1)) & 0x0F;

    // Extract 4 bytes from the hash starting at the offset
    $hashPart = substr($hash, $offset, 4);

    // Convert the 4 bytes to an integer
    $value = unpack('N', $hashPart)[1] & 0x7FFFFFFF;

    // Generate a 6-digit code
    return str_pad($value % 1000000, 6, '0', STR_PAD_LEFT);
}

/**
 * Decode a base32 string
 * 
 * @param string $base32 Base32 encoded string
 * @return string Decoded string
 */
function base32Decode($base32)
{
    $base32Chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $base32CharsFlipped = array_flip(str_split($base32Chars));

    $base32 = strtoupper($base32);
    $buffer = 0;
    $bitsLeft = 0;
    $result = '';

    for ($i = 0; $i < strlen($base32); $i++) {
        $char = $base32[$i];
        if (!isset($base32CharsFlipped[$char])) {
            continue; // Skip invalid characters
        }

        $buffer = ($buffer << 5) | $base32CharsFlipped[$char];
        $bitsLeft += 5;

        if ($bitsLeft >= 8) {
            $bitsLeft -= 8;
            $result .= chr(($buffer >> $bitsLeft) & 0xFF);
        }
    }

    return $result;
}

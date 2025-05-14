<?php
// This file tests if the 2FA library is working correctly

echo "<h1>Testing 2FA Library</h1>";

// Check if vendor directory exists
if (!file_exists(__DIR__ . '/vendor/autoload.php')) {
    echo "<p style='color: red;'>Vendor directory not found. Please run <a href='install-2fa.php'>install-2fa.php</a> first.</p>";
    exit;
}

// Try to include autoload.php
try {
    require_once __DIR__ . '/vendor/autoload.php';
    echo "<p style='color: green;'>✓ Autoload.php included successfully</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>Error including autoload.php: " . $e->getMessage() . "</p>";
    exit;
}

// Check if RobThree TwoFactorAuth class exists
if (class_exists('RobThree\Auth\TwoFactorAuth')) {
    echo "<p style='color: green;'>✓ RobThree\Auth\TwoFactorAuth class exists</p>";

    // Try to instantiate TwoFactorAuth
    try {
        $tfa = new RobThree\Auth\TwoFactorAuth('DepEd');
        echo "<p style='color: green;'>✓ TwoFactorAuth instantiated successfully</p>";

        // Try to create a secret
        $secret = $tfa->createSecret();
        echo "<p style='color: green;'>✓ Secret created: " . $secret . "</p>";

        // Try to generate a QR code
        $qrCode = $tfa->getQRCodeImageAsDataUri('test@example.com', $secret);
        echo "<p style='color: green;'>✓ QR code generated successfully</p>";
        echo "<img src='$qrCode' width='200'><br>";

        // Show verification form
        echo "<form method='post'>";
        echo "<p>Enter the code from your Google Authenticator app:</p>";
        echo "<input type='text' name='code' pattern='[0-9]{6}' maxlength='6' required>";
        echo "<input type='hidden' name='secret' value='$secret'>";
        echo "<button type='submit'>Verify</button>";
        echo "</form>";

        // Verify code if submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code']) && isset($_POST['secret'])) {
            $code = $_POST['code'];
            $secret = $_POST['secret'];

            $valid = $tfa->verifyCode($secret, $code);

            if ($valid) {
                echo "<p style='color: green;'>✓ Code verified successfully!</p>";
            } else {
                echo "<p style='color: red;'>✗ Invalid code. Please try again.</p>";
            }
        }
    } catch (Exception $e) {
        echo "<p style='color: red;'>Error with TwoFactorAuth: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color: red;'>✗ RobThree\Auth\TwoFactorAuth class does not exist</p>";
    echo "<p>Please install the library by running: <code>composer require robthree/twofactorauth</code></p>";

    // Include simple implementation
    require_once 'simple-2fa.php';

    // Generate a test secret
    $secret = generateSecretKey();
    echo "<p style='color: orange;'>Using simple implementation instead.</p>";
    echo "<p>Test secret: " . $secret . "</p>";

    // Generate QR code URL
    $qrCodeUrl = generateQRCode('test@example.com', $secret);
    echo "<p>QR code URL generated:</p>";
    echo "<img src='$qrCodeUrl' width='200'><br>";

    // Show verification form
    echo "<form method='post'>";
    echo "<p>Enter the code from your Google Authenticator app:</p>";
    echo "<input type='text' name='code' pattern='[0-9]{6}' maxlength='6' required>";
    echo "<input type='hidden' name='secret' value='$secret'>";
    echo "<input type='hidden' name='use_simple' value='1'>";
    echo "<button type='submit'>Verify</button>";
    echo "</form>";

    // Verify code if submitted
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['code']) && isset($_POST['secret']) && isset($_POST['use_simple'])) {
        $code = $_POST['code'];
        $secret = $_POST['secret'];

        $valid = verifyCode($secret, $code);

        if ($valid) {
            echo "<p style='color: green;'>✓ Code verified successfully!</p>";
        } else {
            echo "<p style='color: red;'>✗ Invalid code. Please try again.</p>";
        }
    }
}

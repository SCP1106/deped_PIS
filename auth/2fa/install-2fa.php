<?php
// This script helps install the 2FA library

// Check if Composer is installed
$composerInstalled = shell_exec('composer --version');
if (!$composerInstalled) {
    echo "<h1>Composer Not Found</h1>";
    echo "<p>Please install Composer first. Visit <a href='https://getcomposer.org/download/'>https://getcomposer.org/download/</a> for instructions.</p>";
    exit;
}

// Check if vendor directory exists
if (!file_exists(__DIR__ . '/vendor')) {
    echo "<h1>Installing Dependencies</h1>";
    echo "<pre>";
    // Create composer.json if it doesn't exist
    if (!file_exists(__DIR__ . '/composer.json')) {
        file_put_contents(__DIR__ . '/composer.json', json_encode([
            "require" => [
                "phpmailer/phpmailer" => "^6.8",
                "robthree/twofactorauth" => "^1.8"
            ]
        ], JSON_PRETTY_PRINT));
        echo "Created composer.json\n";
    }

    // Run composer install
    echo shell_exec('composer install');
    echo "</pre>";

    if (file_exists(__DIR__ . '/vendor')) {
        echo "<h2>Installation Successful!</h2>";
        echo "<p>The required libraries have been installed successfully.</p>";
    } else {
        echo "<h2>Installation Failed</h2>";
        echo "<p>There was an error installing the required libraries. Please try running 'composer install' manually.</p>";
    }
} else {
    echo "<h1>Dependencies Already Installed</h1>";
    echo "<p>The vendor directory already exists. If you're having issues, try running 'composer update'.</p>";

    // Check if the 2FA library is installed
    if (file_exists(__DIR__ . '/vendor/robthree/twofactorauth')) {
        echo "<p style='color: green;'>✓ RobThree/TwoFactorAuth is installed.</p>";
    } else {
        echo "<p style='color: red;'>✗ RobThree/TwoFactorAuth is not installed.</p>";
        echo "<p>Try running: <code>composer require robthree/twofactorauth</code></p>";
    }

    // Check if PHPMailer is installed
    if (file_exists(__DIR__ . '/vendor/phpmailer/phpmailer')) {
        echo "<p style='color: green;'>✓ PHPMailer is installed.</p>";
    } else {
        echo "<p style='color: red;'>✗ PHPMailer is not installed.</p>";
        echo "<p>Try running: <code>composer require phpmailer/phpmailer</code></p>";
    }
}

// Add a link to test the 2FA setup
echo "<p><a href='test-2fa.php'>Test 2FA Setup</a></p>";

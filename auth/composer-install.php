<?php

/**
 * This script helps install Composer and the required dependencies
 */

echo "<h1>Composer and Dependencies Installation Helper</h1>";

// Check if PHP is available
if (!function_exists('exec')) {
    echo "<div style='color: red;'>The exec() function is disabled. You'll need to install Composer manually.</div>";
    echo "<p>Please follow these steps:</p>";
    echo "<ol>";
    echo "<li>Download Composer from <a href='https://getcomposer.org/download/' target='_blank'>https://getcomposer.org/download/</a></li>";
    echo "<li>Install Composer following the instructions on the website</li>";
    echo "<li>Run <code>composer require robthree/twofactorauth</code> in your project directory</li>";
    echo "</ol>";
    exit;
}

// Check if Composer is installed
$composerVersion = exec('composer --version 2>&1', $output, $returnCode);
$composerInstalled = $returnCode === 0;

if (!$composerInstalled) {
    echo "<div style='color: orange;'>Composer is not installed or not in the PATH.</div>";
    echo "<p>Would you like to install Composer?</p>";

    echo "<form method='post'>";
    echo "<input type='hidden' name='action' value='install_composer'>";
    echo "<button type='submit' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Install Composer</button>";
    echo "</form>";

    if (isset($_POST['action']) && $_POST['action'] === 'install_composer') {
        echo "<h2>Installing Composer...</h2>";
        echo "<pre>";

        // For Windows
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            echo "Downloading Composer installer...\n";
            $installerUrl = 'https://getcomposer.org/Composer-Setup.exe';
            $installerPath = __DIR__ . '/composer-setup.exe';

            if (file_put_contents($installerPath, file_get_contents($installerUrl))) {
                echo "Downloaded Composer installer to $installerPath\n";
                echo "Please run the installer manually to complete the installation.\n";
            } else {
                echo "Failed to download Composer installer.\n";
            }
        } else {
            // For Linux/Mac
            echo "Downloading Composer installer...\n";
            passthru('php -r "copy(\'https://getcomposer.org/installer\', \'composer-setup.php\');"');
            echo "Verifying installer...\n";
            passthru('php -r "if (hash_file(\'sha384\', \'composer-setup.php\') === \'e21205b207c3ff031906575712edab6f13eb0b361f2085f1f1237b7126d785e826a450292b6cfd1d64d92e6563bbde02\') { echo \'Installer verified\'; } else { echo \'Installer corrupt\'; unlink(\'composer-setup.php\'); } echo PHP_EOL;"');
            echo "Installing Composer...\n";
            passthru('php composer-setup.php');
            echo "Cleaning up...\n";
            passthru('php -r "unlink(\'composer-setup.php\');"');
            echo "Moving composer.phar to /usr/local/bin/composer...\n";
            passthru('sudo mv composer.phar /usr/local/bin/composer');
        }

        echo "</pre>";
        echo "<p>Please refresh this page after installing Composer.</p>";
        exit;
    }
} else {
    echo "<div style='color: green;'>Composer is installed: $composerVersion</div>";

    // Check if vendor directory exists
    if (!file_exists(__DIR__ . '/vendor')) {
        echo "<div style='color: orange;'>Vendor directory not found. Dependencies need to be installed.</div>";

        echo "<form method='post'>";
        echo "<input type='hidden' name='action' value='install_deps'>";
        echo "<button type='submit' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Install Dependencies</button>";
        echo "</form>";

        if (isset($_POST['action']) && $_POST['action'] === 'install_deps') {
            echo "<h2>Installing Dependencies...</h2>";
            echo "<pre>";

            // Create composer.json if it doesn't exist
            if (!file_exists(__DIR__ . '/composer.json')) {
                $composerJson = [
                    "require" => [
                        "robthree/twofactorauth" => "^1.8",
                        "phpmailer/phpmailer" => "^6.8"
                    ]
                ];
                file_put_contents(__DIR__ . '/composer.json', json_encode($composerJson, JSON_PRETTY_PRINT));
                echo "Created composer.json\n";
            }

            // Install dependencies
            echo "Running composer install...\n";
            passthru('composer install');

            echo "</pre>";

            if (file_exists(__DIR__ . '/vendor')) {
                echo "<div style='color: green;'>Dependencies installed successfully!</div>";
                echo "<p>You can now use the 2FA functionality.</p>";
            } else {
                echo "<div style='color: red;'>Failed to install dependencies.</div>";
                echo "<p>Please try running <code>composer install</code> manually in your project directory.</p>";
            }
        }
    } else {
        echo "<div style='color: green;'>Vendor directory exists. Dependencies are installed.</div>";

        // Check if the 2FA library is installed
        if (file_exists(__DIR__ . '/vendor/robthree/twofactorauth')) {
            echo "<div style='color: green;'>RobThree/TwoFactorAuth is installed.</div>";
        } else {
            echo "<div style='color: orange;'>RobThree/TwoFactorAuth is not installed.</div>";

            echo "<form method='post'>";
            echo "<input type='hidden' name='action' value='install_2fa'>";
            echo "<button type='submit' style='padding: 10px; background-color: #4CAF50; color: white; border: none; cursor: pointer;'>Install 2FA Library</button>";
            echo "</form>";

            if (isset($_POST['action']) && $_POST['action'] === 'install_2fa') {
                echo "<h2>Installing 2FA Library...</h2>";
                echo "<pre>";

                echo "Running composer require robthree/twofactorauth...\n";
                passthru('composer require robthree/twofactorauth');

                echo "</pre>";

                if (file_exists(__DIR__ . '/vendor/robthree/twofactorauth')) {
                    echo "<div style='color: green;'>2FA library installed successfully!</div>";
                } else {
                    echo "<div style='color: red;'>Failed to install 2FA library.</div>";
                    echo "<p>Please try running <code>composer require robthree/twofactorauth</code> manually in your project directory.</p>";
                }
            }
        }
    }
}

// Check autoload paths
echo "<h2>Checking Autoload Paths</h2>";
$autoload_paths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php',
    'C:/laragon/www/vendor/autoload.php'
];

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Path</th><th>Status</th></tr>";

foreach ($autoload_paths as $path) {
    echo "<tr>";
    echo "<td>" . htmlspecialchars($path) . "</td>";

    if (file_exists($path)) {
        echo "<td style='background-color: #d4edda; color: #155724;'>Found</td>";
    } else {
        echo "<td style='background-color: #f8d7da; color: #721c24;'>Not Found</td>";
    }

    echo "</tr>";
}

echo "</table>";

// Provide a link to test 2FA
echo "<h2>Test 2FA Setup</h2>";
echo "<p>Once you've installed the dependencies, you can test the 2FA setup:</p>";
echo "<a href='setup-2fa.php' style='padding: 10px; background-color: #007bff; color: white; text-decoration: none; display: inline-block;'>Test 2FA Setup</a>";

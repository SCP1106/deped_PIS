<?php

/**
 * This script checks if important PHP files exist and are accessible
 */

// List of important files to check
$files_to_check = [
    'activate-account.php',
    'database.php',
    'login.php',
    'register.php',
    'signup.php',
    'process-register.php',
    'process-signup.php',
    'mailer.php',
    'otp-handler.php'
];

echo "<h1>File Accessibility Check</h1>";
echo "<p>This script checks if important PHP files exist and are accessible.</p>";

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>File</th><th>Status</th><th>Full Path</th><th>URL Path</th></tr>";

foreach ($files_to_check as $file) {
    $file_path = __DIR__ . '/' . $file;
    $exists = file_exists($file_path);
    $readable = is_readable($file_path);

    // Get URL path
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $current_dir = dirname($_SERVER['PHP_SELF']);
    $url_path = $protocol . $host . $current_dir . '/' . $file;

    echo "<tr>";
    echo "<td>" . htmlspecialchars($file) . "</td>";

    if ($exists && $readable) {
        echo "<td style='background-color: #d4edda; color: #155724;'>Accessible</td>";
    } elseif ($exists && !$readable) {
        echo "<td style='background-color: #fff3cd; color: #856404;'>Exists but not readable</td>";
    } else {
        echo "<td style='background-color: #f8d7da; color: #721c24;'>Not found</td>";
    }

    echo "<td>" . htmlspecialchars($file_path) . "</td>";
    echo "<td><a href='" . htmlspecialchars($url_path) . "' target='_blank'>" . htmlspecialchars($url_path) . "</a></td>";
    echo "</tr>";
}

echo "</table>";

// Check server configuration
echo "<h2>Server Configuration</h2>";
echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Setting</th><th>Value</th></tr>";

echo "<tr><td>PHP Version</td><td>" . phpversion() . "</td></tr>";
echo "<tr><td>Document Root</td><td>" . $_SERVER['DOCUMENT_ROOT'] . "</td></tr>";
echo "<tr><td>Script Filename</td><td>" . $_SERVER['SCRIPT_FILENAME'] . "</td></tr>";
echo "<tr><td>PHP_SELF</td><td>" . $_SERVER['PHP_SELF'] . "</td></tr>";
echo "<tr><td>REQUEST_URI</td><td>" . $_SERVER['REQUEST_URI'] . "</td></tr>";
echo "<tr><td>HTTP_HOST</td><td>" . $_SERVER['HTTP_HOST'] . "</td></tr>";
echo "<tr><td>SCRIPT_NAME</td><td>" . $_SERVER['SCRIPT_NAME'] . "</td></tr>";

echo "</table>";

// Test URL generation
echo "<h2>URL Generation Test</h2>";
echo "<p>Testing how URLs are generated for the activation link:</p>";

// Generate a test token
$test_token = bin2hex(random_bytes(8));

// Different ways to generate URLs
$url1 = "http://" . $_SERVER['HTTP_HOST'] . "/activate-account.php?token=" . $test_token;
$url2 = "http://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['PHP_SELF']) . "/activate-account.php?token=" . $test_token;

$current_dir = basename(dirname($_SERVER['PHP_SELF']));
$base_url = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
$base_url .= $_SERVER['HTTP_HOST'];
if ($current_dir !== '' && $current_dir !== '/') {
    $base_url .= '/' . $current_dir;
}
$url3 = $base_url . "/activate-account.php?token=" . $test_token;

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Method</th><th>Generated URL</th></tr>";
echo "<tr><td>Method 1</td><td><a href='" . htmlspecialchars($url1) . "' target='_blank'>" . htmlspecialchars($url1) . "</a></td></tr>";
echo "<tr><td>Method 2</td><td><a href='" . htmlspecialchars($url2) . "' target='_blank'>" . htmlspecialchars($url2) . "</a></td></tr>";
echo "<tr><td>Method 3 (Recommended)</td><td><a href='" . htmlspecialchars($url3) . "' target='_blank'>" . htmlspecialchars($url3) . "</a></td></tr>";
echo "</table>";

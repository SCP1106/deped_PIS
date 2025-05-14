<?php
// This script helps debug URL generation issues

echo "<h1>URL Debugging Tool</h1>";

// Display server variables
echo "<h2>Server Variables</h2>";
echo "<pre>";
echo "HTTP_HOST: " . (isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'Not set') . "\n";
echo "REQUEST_SCHEME: " . (isset($_SERVER['REQUEST_SCHEME']) ? $_SERVER['REQUEST_SCHEME'] : 'Not set') . "\n";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'Not set') . "\n";
echo "PHP_SELF: " . (isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : 'Not set') . "\n";
echo "SCRIPT_NAME: " . (isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : 'Not set') . "\n";
echo "SCRIPT_FILENAME: " . (isset($_SERVER['SCRIPT_FILENAME']) ? $_SERVER['SCRIPT_FILENAME'] : 'Not set') . "\n";
echo "DOCUMENT_ROOT: " . (isset($_SERVER['DOCUMENT_ROOT']) ? $_SERVER['DOCUMENT_ROOT'] : 'Not set') . "\n";
echo "REQUEST_URI: " . (isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : 'Not set') . "\n";
echo "</pre>";

// Generate test URLs
echo "<h2>URL Generation Test</h2>";

// Method 1: Using HTTP_HOST and PHP_SELF
$method1 = "";
if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['PHP_SELF'])) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['PHP_SELF']);
    $method1 = $protocol . $host . rtrim($path, '/');
}

// Method 2: Using HTTP_HOST and SCRIPT_NAME
$method2 = "";
if (isset($_SERVER['HTTP_HOST']) && isset($_SERVER['SCRIPT_NAME'])) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://";
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname($_SERVER['SCRIPT_NAME']);
    $method2 = $protocol . $host . rtrim($path, '/');
}

// Method 3: Using absolute path
$method3 = "http://yourdomain.com"; // Replace with your actual domain

echo "<table border='1' cellpadding='5' style='border-collapse: collapse;'>";
echo "<tr><th>Method</th><th>Generated Base URL</th><th>Full Activation URL</th><th>Test Link</th></tr>";

echo "<tr>";
echo "<td>Method 1: Using HTTP_HOST and PHP_SELF</td>";
echo "<td>" . htmlspecialchars($method1) . "</td>";
echo "<td>" . htmlspecialchars($method1 . "/activate-account.php?token=test123") . "</td>";
echo "<td><a href='" . htmlspecialchars($method1 . "/activate-account.php?token=test123") . "' target='_blank'>Test Link</a></td>";
echo "</tr>";

echo "<tr>";
echo "<td>Method 2: Using HTTP_HOST and SCRIPT_NAME</td>";
echo "<td>" . htmlspecialchars($method2) . "</td>";
echo "<td>" . htmlspecialchars($method2 . "/activate-account.php?token=test123") . "</td>";
echo "<td><a href='" . htmlspecialchars($method2 . "/activate-account.php?token=test123") . "' target='_blank'>Test Link</a></td>";
echo "</tr>";

echo "<tr>";
echo "<td>Method 3: Hardcoded Domain (update this)</td>";
echo "<td>" . htmlspecialchars($method3) . "</td>";
echo "<td>" . htmlspecialchars($method3 . "/activate-account.php?token=test123") . "</td>";
echo "<td><a href='" . htmlspecialchars($method3 . "/activate-account.php?token=test123") . "' target='_blank'>Test Link</a></td>";
echo "</tr>";

echo "</table>";

// File existence check
echo "<h2>File Existence Check</h2>";
$activate_file = __DIR__ . "/activate-account.php";
$exists = file_exists($activate_file);
$readable = is_readable($activate_file);

echo "<p>Checking for activate-account.php:</p>";
echo "<ul>";
echo "<li>Full path: " . htmlspecialchars($activate_file) . "</li>";
echo "<li>File exists: " . ($exists ? "Yes" : "No") . "</li>";
echo "<li>File is readable: " . ($readable ? "Yes" : "No") . "</li>";
echo "</ul>";

// Directory listing
echo "<h2>Directory Listing</h2>";
echo "<p>Files in the current directory:</p>";
echo "<ul>";
$files = scandir(__DIR__);
foreach ($files as $file) {
    if ($file != "." && $file != "..") {
        echo "<li>" . htmlspecialchars($file) . " - " . (is_file(__DIR__ . "/" . $file) ? "File" : "Directory") . "</li>";
    }
}
echo "</ul>";

// Suggest solutions
echo "<h2>Possible Solutions</h2>";
echo "<ol>";
echo "<li><strong>Update the URL generation in process-register.php</strong> with the code from this debugging page.</li>";
echo "<li><strong>Verify that activate-account.php exists</strong> in the same directory as this debug script.</li>";
echo "<li><strong>Check file permissions</strong> - make sure activate-account.php is readable (chmod 644).</li>";
echo "<li><strong>Update your email template</strong> to use the correct URL.</li>";
echo "<li><strong>If using a subdirectory</strong>, make sure the path includes it.</li>";
echo "<li><strong>Check .htaccess rules</strong> that might be blocking access to PHP files.</li>";
echo "</ol>";

// Manual URL builder
echo "<h2>Manual URL Builder</h2>";
echo "<p>Use this form to manually build and test an activation URL:</p>";
echo "<form action='' method='get'>";
echo "<label>Domain (with protocol):</label><br>";
echo "<input type='text' name='domain' value='" . (isset($_SERVER['HTTP_HOST']) ? (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https://" : "http://") . $_SERVER['HTTP_HOST'] : '') . "' style='width: 300px;'><br><br>";
echo "<label>Path to activate-account.php (starting with /):</label><br>";
echo "<input type='text' name='path' value='" . (isset($_SERVER['PHP_SELF']) ? dirname($_SERVER['PHP_SELF']) : '') . "/activate-account.php' style='width: 300px;'><br><br>";
echo "<label>Test token:</label><br>";
echo "<input type='text' name='token' value='test123' style='width: 300px;'><br><br>";
echo "<input type='submit' value='Generate Test URL'>";
echo "</form>";

if (isset($_GET['domain']) && isset($_GET['path']) && isset($_GET['token'])) {
    $test_url = $_GET['domain'] . $_GET['path'] . "?token=" . $_GET['token'];
    echo "<p>Generated URL: <a href='" . htmlspecialchars($test_url) . "' target='_blank'>" . htmlspecialchars($test_url) . "</a></p>";
}

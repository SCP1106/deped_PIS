<?php
// This script tests different QR code generation methods

// Start session
session_start();

// Include database connection if needed
if (file_exists('database.php')) {
    require_once 'database.php';
}

// Generate a test secret key
function generateSecretKey($length = 16)
{
    $validChars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
    $secret = '';
    for ($i = 0; $i < $length; $i++) {
        $secret .= $validChars[rand(0, strlen($validChars) - 1)];
    }
    return $secret;
}

// Generate a test secret if not already in session
if (!isset($_SESSION['test_secret'])) {
    $_SESSION['test_secret'] = generateSecretKey();
}
$secret = $_SESSION['test_secret'];
$email = 'test@example.com';
$appName = 'DepEd App';

// Try to find the autoload file in different possible locations
$autoload_paths = [
    __DIR__ . '/vendor/autoload.php',
    __DIR__ . '/../vendor/autoload.php',
    dirname(__DIR__) . '/vendor/autoload.php',
    'C:/laragon/www/DepedSystem/vendor/autoload.php'
];

$autoload_found = false;
foreach ($autoload_paths as $path) {
    if (file_exists($path)) {
        require_once $path;
        $autoload_found = true;
        break;
    }
}

// Check which 2FA library is available
$useRobThree = $autoload_found && class_exists('RobThree\Auth\TwoFactorAuth');
$usePragmaRX = $autoload_found && class_exists('PragmaRX\Google2FA\Google2FA');

// Generate QR codes using different methods
$qrCodes = [];

// Method 1: Google Charts API (small)
$otpauth = 'otpauth://totp/' . urlencode($appName) . ':' . urlencode($email) . '?secret=' . $secret . '&issuer=' . urlencode($appName);
$qrCodes['google_charts_small'] = 'https://chart.googleapis.com/chart?chs=200x200&chld=M|0&cht=qr&chl=' . urlencode($otpauth);

// Method 2: Google Charts API (large)
$qrCodes['google_charts_large'] = 'https://chart.googleapis.com/chart?chs=300x300&chld=M|0&cht=qr&chl=' . urlencode($otpauth);

// Method 3: Google Charts API (extra large with error correction)
$qrCodes['google_charts_xl'] = 'https://chart.googleapis.com/chart?chs=400x400&chld=H|0&cht=qr&chl=' . urlencode($otpauth);

// Method 4: RobThree library if available
if ($useRobThree) {
    $tfa = new RobThree\Auth\TwoFactorAuth($appName);
    $qrCodes['robthree'] = $tfa->getQRCodeImageAsDataUri($email, $secret);
}

// Method 5: PragmaRX library if available
if ($usePragmaRX) {
    $google2fa = new PragmaRX\Google2FA\Google2FA();
    $pragmarx_url = $google2fa->getQRCodeUrl($appName, $email, $secret);
    $qrCodes['pragmarx'] = 'https://chart.googleapis.com/chart?chs=300x300&chld=M|0&cht=qr&chl=' . urlencode($pragmarx_url);
}

// Method 6: QR Code API
$qrCodes['qrcode_api'] = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($otpauth);

// Method 7: QR Code Monkey API
$qrCodes['qrcode_monkey'] = 'https://api.qrcode-monkey.com/qr/custom?data=' . urlencode($otpauth) . '&size=300&download=false';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>QR Code Test | DepEd</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        .qr-container {
            margin-bottom: 30px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 8px;
            background-color: #f8f9fa;
        }

        .qr-image {
            max-width: 100%;
            height: auto;
            border: 1px solid #ddd;
            background-color: white;
            padding: 10px;
            border-radius: 4px;
        }
    </style>
</head>

<body>
    <div class="container py-5">
        <h1 class="mb-4">QR Code Test</h1>

        <div class="alert alert-info">
            <p><strong>Test Information:</strong></p>
            <ul class="mb-0">
                <li>Secret Key: <code><?php echo $secret; ?></code></li>
                <li>Email: <code><?php echo $email; ?></code></li>
                <li>App Name: <code><?php echo $appName; ?></code></li>
                <li>OTP Auth URL: <code><?php echo $otpauth; ?></code></li>
                <li>RobThree Library Available: <strong><?php echo $useRobThree ? 'Yes' : 'No'; ?></strong></li>
                <li>PragmaRX Library Available: <strong><?php echo $usePragmaRX ? 'Yes' : 'No'; ?></strong></li>
            </ul>
        </div>

        <div class="alert alert-warning">
            <p><strong>Instructions:</strong></p>
            <ol class="mb-0">
                <li>Open Google Authenticator on your mobile device</li>
                <li>Try scanning each QR code below</li>
                <li>Note which ones work and which ones don't</li>
                <li>If none work, try the manual entry method</li>
            </ol>
        </div>

        <div class="row">
            <?php foreach ($qrCodes as $method => $url): ?>
                <div class="col-md-6">
                    <div class="qr-container">
                        <h3><?php echo ucwords(str_replace('_', ' ', $method)); ?></h3>
                        <div class="text-center">
                            <img src="<?php echo $url; ?>" alt="QR Code - <?php echo $method; ?>" class="qr-image">
                        </div>
                        <div class="mt-3">
                            <a href="<?php echo $url; ?>" target="_blank" class="btn btn-sm btn-outline-primary">Open in New Tab</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div class="card mt-4">
            <div class="card-header">
                <h2 class="mb-0">Manual Entry Method</h2>
            </div>
            <div class="card-body">
                <p>If you can't scan any of the QR codes above, try manual entry:</p>
                <ol>
                    <li>Open Google Authenticator</li>
                    <li>Tap the + button</li>
                    <li>Select "Enter a setup key"</li>
                    <li>Enter "<?php echo $appName; ?>" for the account name</li>
                    <li>Enter "<?php echo $secret; ?>" for the key</li>
                    <li>Make sure "Time based" is selected</li>
                    <li>Tap Add</li>
                </ol>

                <div class="alert alert-success">
                    <p><strong>Direct Link Method:</strong></p>
                    <p>On mobile devices, you can also try clicking this link to open directly in Google Authenticator:</p>
                    <a href="<?php echo $otpauth; ?>" class="btn btn-success">Open in Authenticator App</a>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="setup-2fa.php" class="btn btn-primary">Return to Setup 2FA</a>
        </div>
    </div>
</body>

</html>
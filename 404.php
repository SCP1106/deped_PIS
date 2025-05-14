<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
         :root {
            --primary-color: #2e7d32;
            --secondary-color: #4caf50;
  --accent-color: #388e3c;
  --sidebar-width: 280px;
}
        body {
            background-color: #ffffff;
            font-family: 'Arial', sans-serif;
        }
        .error-container {
            text-align: center;
            padding: 40px 15px;
            margin: 0 auto;
        }
        .error-text-container {
            position: relative;
            display: inline-block;
        }
        .error-code {
            font-size: 120px;
            font-weight: bold;
            color: #343a40;
            margin: 0;
            line-height: 1;
            position: absolute;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
        }
        .error-text {
            font-size: 28px;
            color: #6c757d;
            margin: 0;
            position: absolute;
            top: 120px;
            left: 50%;
            transform: translateX(-50%);
        }
        .error-gif {
            max-width: 100%;
            height: auto;
            margin: 0;
        }
        .home-button {
            transition: all 0.3s ease;
            color: white;
            background-color: var(--primary-color);
        }
        .home-button:hover {
            background-color: var(--accent-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .countdown {
            margin-top: 20px;
            font-size: 16px;
            color: #b3b3b3a5;
        }
        /* The text below the gif now */
        .below-gif-text {
            position: absolute;
            bottom: -40px; /* Adjust this value to fit the text below the gif */
            left: 50%;
            transform: translateX(-50%);
            text-align: center;
            color: #343a40;
            width: 100%;
        }
        .below-gif-text h2 {
            margin: 0;
            font-size: 24px;
        }
        .below-gif-text p {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="error-container">
            <div class="error-text-container">
                <h1 class="error-code">404</h1>
                <p class="error-text">Oops! Page not found</p>
                <img src="https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif" alt="404 Animation" class="error-gif">
                <!-- This is now inside the gif container -->
                <div class="below-gif-text">
                    <h2>Look like you're lost.</h2>
                    <p class="mb-4">The page you are looking for is not available!</p>
                    <a href="loading.php" class="btn btn-lg home-button">
                        <i class="bi bi-house-door"></i> Back to Homepage
                    </a>
                    <p class="countdown">Redirecting to homepage in <span id="countdown">10</span> seconds</p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Countdown timer for auto-redirect
        let seconds = 10;
        const countdownElement = document.getElementById('countdown');
        
        const countdownTimer = setInterval(() => {
            seconds--;
            countdownElement.textContent = seconds;
            
            if (seconds <= 0) {
                clearInterval(countdownTimer);
                window.location.href = 'loading.php';
            }
        }, 1000);

        // Cancel redirect if user clicks the button
        document.querySelector('.home-button').addEventListener('click', () => {
            clearInterval(countdownTimer);
        });
    </script>
</body>
</html>

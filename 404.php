<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>404 - Page Not Found</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
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
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .error-container {
            text-align: center;
            padding: 20px 5px;
            margin: 0 auto;
        }
        .error-code {
            font-size: clamp(80px, 15vw, 120px);
            font-weight: bold;
            color: #343a40;
            margin: 0;
            line-height: 1;
        }
        .error-text {
            font-size: clamp(20px, 5vw, 28px);
            color: #6c757d;
            margin: 0;
        }
        .error-gif {
            max-width: 100%;
            height: auto;
            margin: 0 auto;
            display: block;
        }
        .error-content h2 {
            font-size: clamp(18px, 4vw, 24px);
            margin-bottom: 10px;
            color: #343a40;
        }
        .error-content p {
            margin-bottom: 15px;
            color: #343a40;
        }
        .home-button {
            transition: all 0.3s ease;
            color: white;
            background-color: var(--primary-color);
            margin-bottom: 15px;
        }
        .home-button:hover {
            background-color: var(--accent-color);
            color: white;
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .countdown {
            margin-top: 10px;
            font-size: 16px;
            color: #b3b3b3a5;
        }
        
        /* Mobile-only adjustments */
        @media (max-width: 576px) {
            .error-container {
                padding: 15px 10px;
                width: 100%;
            }
            .error-image-container {
                width: 100%;
            }
            .error-gif {
                width: 100%;
                max-width: 280px;
            }
            .home-button {
                display: inline-block;
                width: auto;
                min-width: 200px;
            }
            .error-code {
                /* Ensure minimum size on very small screens */
                font-size: 80px;
            }
            .error-text {
                /* Ensure minimum size on very small screens */
                font-size: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8 col-lg-6">
                <div class="error-container">
                    <h1 class="error-code">404</h1>
                    <p class="error-text">Oops! Page not found</p>
                    
                    <div class="error-image-container">
                        <img src="https://cdn.dribbble.com/users/285475/screenshots/2083086/dribbble_1.gif" 
                             alt="404 Animation" 
                             class="error-gif img-fluid">
                    </div>
                    
                    <div class="error-content">
                        <h2>Look like you're lost.</h2>
                        <p class="mb-4">The page you are looking for is not available!</p>
                        <a href="loading.php" class="btn btn-lg home-button">
                            <i class="bi bi-house-door"></i> Back to Homepage
                        </a>
                        <div class="countdown">
                            Redirecting to homepage in <span id="countdown">10</span> seconds
                        </div>
                    </div>
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
        }, 1000000); // Changed from 100000 to 1000 for proper 1-second intervals

        // Cancel redirect if user clicks the button
        document.querySelector('.home-button').addEventListener('click', () => {
            clearInterval(countdownTimer);
        });
    </script>
</body>
</html>
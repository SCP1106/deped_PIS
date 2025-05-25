<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DepEd Nueva Ecija</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    /* Blob animations */
    @keyframes blob {
      0% {
        transform: translate(0px, 0px) scale(1);
      }
      33% {
        transform: translate(30px, -50px) scale(1.1);
      }
      66% {
        transform: translate(-20px, 20px) scale(0.9);
      }
      100% {
        transform: translate(0px, 0px) scale(1);
      }
    }

    /* Blob styling */
    .blob {
      position: fixed;
      width: 300px;
      height: 300px;
      border-radius: 50%;
      filter: blur(40px);
      opacity: 0.7;
      mix-blend-mode: multiply;
      z-index: -1;
    }

    #blob1 {
      background-color: rgba(0, 123, 255, 0.3);
      top: 20%;
      left: 15%;
      animation: blob 7s infinite;
    }

    #blob2 {
      background-color: rgba(40, 167, 69, 0.3);
      top: 30%;
      right: 15%;
      animation: blob 7s infinite 2s;
    }

    #blob3 {
      background-color: rgba(255, 193, 7, 0.3);
      bottom: 15%;
      left: 35%;
      animation: blob 7s infinite 4s;
    }

    /* Loading screen */
    .loader-container {
      background-color: white;
      z-index: 1000;
      transition: opacity 0.5s ease;
    }

    .logo-wrapper {
      position: relative;
      width: 150px;
      height: 150px;
      margin: 0 auto;
    }

    .rotating-border {
      position: absolute;
      width: 140px;
      height: 140px;
      border-radius: 50%;
      border: 4px solid;
      border-color: #28a745 #28a745 transparent transparent;
      animation: spin 3s linear infinite;
      top: 5px;
      left: 5px;
    }

    .rotating-border-inner {
      position: absolute;
      width: 120px;
      height: 120px;
      border-radius: 50%;
      border: 4px solid;
      border-color: transparent transparent #007bff #007bff;
      animation: spin 3s linear infinite reverse;
      top: 15px;
      left: 15px;
    }

    .logo-static {
      position: relative;
      width: 100px;
      height: 100px;
      object-fit: contain;
      z-index: 2;
      margin: 25px auto;
      display: block;
    }

    /* Main content */
    .content {
      opacity: 0;
      transition: opacity 0.5s ease;
    }

    .main-logo {
      max-width: 150px;
      height: auto;
    }

    /* Offline message */
    .offline-message {
      background-color: #dc3545;
      color: white;
      transform: translateY(-100%);
      transition: transform 0.3s ease;
      z-index: 1001;
    }

    /* Spin animation */
    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
      .logo-wrapper {
        width: 120px;
        height: 120px;
      }

      .rotating-border {
        width: 110px;
        height: 110px;
      }

      .rotating-border-inner {
        width: 90px;
        height: 90px;
      }

      .logo-static {
        width: 80px;
        height: 80px;
        margin: 20px auto;
      }

      .titles p {
        font-size: 14px;
      }

      .blob {
        width: 200px;
        height: 200px;
      }
    }
  </style>
</head>
<body>
  <!-- Background blobs -->
  <div class="blob" id="blob1"></div>
  <div class="blob" id="blob2"></div>
  <div class="blob" id="blob3"></div>

  <!-- Loading Screen -->
  <div class="loader-container position-fixed top-0 start-0 w-100 h-100 d-flex flex-column justify-content-center align-items-center">
    <div class="text-center px-4">
      <div class="logo-wrapper mb-4">
        <div class="rotating-border"></div>
        <div class="rotating-border-inner"></div>
        <img
          src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/sdone-logo-R1twHOYRot4GEzkYSHCILNcCeKKz61.png"
          alt="Logo"
          class="logo-static" />
      </div>
      <div class="titles">
        <p class="mb-1 fw-bold">Republic of the Philippines</p>
        <p class="mb-1 fw-bold">Department of Education</p>
        <p class="mb-0 fw-bold">SCHOOLS DIVISION OFFICE OF NUEVA ECIJA</p>
      </div>
      <h4 class="text-success mt-4">Loading...</h4>
    </div>
  </div>

  <!-- Offline Message -->
  <div class="offline-message position-fixed top-0 start-0 w-100 p-3 text-center">
    <p class="m-0">You are currently offline. Please check your internet connection.</p>
  </div>

  <!-- Main Content -->
  <div class="content container-fluid">
    <div class="row min-vh-100 justify-content-center align-items-center">
      <div class="col-12 col-md-8 col-lg-6 text-center">
        <img
          src="https://hebbkx1anhila5yf.public.blob.vercel-storage.com/sdone-logo-R1twHOYRot4GEzkYSHCILNcCeKKz61.png"
          alt="Logo"
          class="main-logo mb-4" />
        <!-- Add your main content here -->
      </div>
    </div>
  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      // Check online status
      function handleOnlineStatus() {
        const isOffline = !navigator.onLine;
        const offlineMessage = document.querySelector('.offline-message');
        
        if (offlineMessage) {
          if (isOffline) {
            offlineMessage.style.transform = 'translateY(0)';
          } else {
            offlineMessage.style.transform = 'translateY(-100%)';
          }
        }
      }

      window.addEventListener('online', handleOnlineStatus);
      window.addEventListener('offline', handleOnlineStatus);
      handleOnlineStatus();

      // Simulate loading completion
      setTimeout(function() {
        const loaderContainer = document.querySelector('.loader-container');
        const content = document.querySelector('.content');

        if (loaderContainer && content) {
          loaderContainer.style.opacity = '0';
          setTimeout(function() {
            loaderContainer.style.display = 'none';
            content.style.opacity = '1';
          }, 500);
        }
      }, 3000);
    });
  </script>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>DepEd: Registration</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
  <link rel="stylesheet" href="css/dashboard.css" />
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <style>
    .main-content {
      display: flex;
      justify-content: center;
      align-items: center;
      position: relative;
      background-color: #f9f9f9;
      min-height: 90vh;
      padding: 4rem 0;
      overflow: hidden;
      z-index: 1;
      background: url("data:image/svg+xml,%3Csvg width='100%' height='200' viewBox='100 50 1100 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,100 C200,250 400,-50 600,100 C800,250 1000,-50 1200,100' fill='none' stroke='%232e8b57' stroke-width='3' stroke-linecap='round'/%3E%3C/svg%3E") repeat-x;
      background-size: cover;
      background-position: center;
    }

    #sidebar-container .sidebar {
      height: 100vh;
      overflow-y: auto;
    }

    .registration-card {
      background-color: #ffffff;
      border-radius: 15px;
      box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
      padding: 2rem;
      max-width: 500px;
      max-height: 280px;
      width: 100%;
      position: relative;
    }

    .registration-card h2 {
      color: #2e8b57;
      margin-bottom: 1.5rem;
      text-align: center;
      font-size: 2rem;
    }

    .btn-primary {
      background-color: #2e8b57;
      border-color: #2e8b57;
      border-radius: 8px;
      padding: 0.75rem 1.5rem;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .btn-primary:hover {
      background-color: #246c46;
      border-color: #246c46;
    }

    .btn-register {
      font-size: 1.1rem;
      padding: 1rem 2rem;
    }

    .skeleton-nav-item, .skeleton-logo, .skeleton-text {
      background-color: #e0e0e0;
      border-radius: 4px;
    }

    .skeleton-text {
      height: 1.5rem;
      width: 80%;
    }

    .skeleton-logo {
      height: 40px;
      width: 40px;
      border-radius: 50%;
    }

    .skeleton-nav-item {
      height: 2rem;
      width: 80%;
      margin-bottom: 0.5rem;
    }

    .registration-content {
      text-align: center;
    }

    .registration-description {
      color: #666;
      margin-bottom: 2rem;
      font-size: 1rem;
    }
  </style>
</head>
<body>
  <div id="sidebar-container">
    <div class="sidebar">
      <div class="profile-section">
        <div class="d-flex align-items-center gap-3">
          <div class="skeleton-circle"></div>
          <div>
            <div class="skeleton-text"></div>
            <div class="skeleton-text"></div>
          </div>
        </div>
      </div>
      <nav class="mt-4">
        <div class="skeleton-nav-item"></div>
        <div class="skeleton-nav-item"></div>
        <div class="skeleton-nav-item"></div>
        <div class="skeleton-nav-item"></div>
      </nav>
    </div>
  </div>

  <div id="topbar-container"></div>

  <div class="main-content p-4">
    <div class="registration-card">
      <div class="registration-content">
        <h2>Create an Account</h2>
        <p class="registration-description">
          Join the DepEd system to access educational resources and manage your account.
        </p>
        <div id="message-container"></div>
        <button type="button" class="btn btn-primary btn-register w-100" onclick="window.location.href='auth/pages/register.php'">
          <i class="bi bi-person-plus me-2"></i>
          Start Registration
        </button>
      </div>
    </div>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="js/side-topbar.js"></script>
</body>
</html>
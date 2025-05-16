<?php
// Prevent browser caching of authenticated pages


// THIS IS FOR THE ANTI-BYPASS 
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DepEd: Dashboard</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
      rel="stylesheet"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/side-topbar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
  </head>
  <body>
    <div id="sidebar-container">

    </div>
    <div id="topbar-container">

    </div>

    <!-- Main Content -->
    <div class="main-content p-4" id="dashboardContent">
      <!-- Metrics Row -->
      <div class="row g-4 mb-4">
        <!-- Total Teachers Card -->
        <div class="col-12 col-md-6 col-lg-6">
          <div class="metric-card h-100">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-muted mb-1 fw-medium">TOTAL TEACHERS</p>
                <h3 class="mb-2 fw-bold" id="totalEmployees">0</h3>
              </div>
              <div
                class="metric-icon"
                style="background: linear-gradient(45deg, #2e7d32, #4caf50)"
              >
                <i class="bi bi-person-workspace"></i>
              </div>
            </div>
          </div>
        </div>

        <!-- Total Enrollment Card -->
        <div class="col-12 col-md-6 col-lg-6">
          <div class="metric-card h-100">
            <div class="d-flex justify-content-between align-items-start">
              <div>
                <p class="text-muted mb-1 fw-medium">TOTAL ENROLLMENT</p>
                <h3 class="mb-2 fw-bold" id="totalEnrollees">0</h3>
              </div>
              <div
                class="metric-icon"
                style="background: linear-gradient(45deg, #1b5e20, #2e7d32)"
              >
                <i class="bi bi-people-fill"></i>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Charts Row -->
      <div class="row g-4">
        <!-- Enxrollment Trends Chart -->
        <div class="col-12 col-lg-8">
          <div class="chart-container">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h5 class="mb-0 fw-bold">STUDENT ENROLLMENT TRENDS</h5>
              <select class="form-select form-select-sm" style="width: auto">
                <option>YEAR</option>
                <option>GRADE</option>
                <option>SCHOOL TYPE</option>
              </select>
            </div>
            <canvas id="studEnroll"></canvas>
          </div>
        </div>

        <!-- Total Schools Chart -->
        <div class="col-12 col-lg-4">
          <div class="chart-container h-100">
            <h5 class="mb-4 fw-bold">TOTAL SCHOOLS</h5>
            <canvas id="totSchools"></canvas>
          </div>
        </div>
      </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="js/db-fetchers.js"></script>
    <script src="js/dashboard.js"></script>
    <script src="js/side-topbar.js"></script>
  </body>
</html>

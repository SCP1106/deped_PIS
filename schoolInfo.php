<?php
// Prevent browser caching of authenticated pages
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ..auth/pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DepEd: School Information</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />
    <!-- Leaflet CSS -->
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="css/dashboard.css" />
    <style>
      :root {
        --primary-color: #4caf50;
        --secondary-color: #1184e8;
        --success-color: #2e7d32;
        --danger-color: #dc3545;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
        --hover-green: #388e3c;
        --border-color: #dee2e6;
      }

      body {
        font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--light-color);
      }

      .main-content {
        background-color: var(--light-color);
        min-height: 100vh;
      }

      .content-wrapper {
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2rem;
      }
      #sidebar-container .sidebar {
        height: 100vh;
        overflow-y: auto; /* Allows vertical scrolling */
      }

      .search-bar input {
        border-radius: 5px;
        border: 1px solid #dee2e6;
        padding: 0.5rem 1rem;
        width: 350px;
        transition: all 0.3s ease;
      }

      .search-bar input:focus {
        box-shadow: 0 0 0 0.1rem #2e8b56;
        border-color: var(--primary-color);
      }

      .action-buttons .btn {
        padding: 0.5rem 1.5rem;
        font-weight: 500;
        border-radius: 5px;
        transition: all 0.3s ease;
      }

      .action-buttons .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }

      .table {
        margin-top: 1rem;
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
      }

      .table th {
        background-color: var(--success-color);
        color: #ffffff;
        font-weight: 600;
        padding: 10px;
        border-bottom: 2px solid #dee2e6;
        text-align: center;
        border: 1px solid #4caf4f75;
      }

      .table td {
        padding: 10px;
        vertical-align: middle;
        border: 1px solid rgba(117, 117, 117, 0.095);
      }

      /* Enhanced striping for table rows */
      .table tbody tr:nth-child(odd) {
        background-color: #f8f9fa;
      }

      .table tbody tr:nth-child(even) {
        background-color: rgba(0, 123, 255, 0.05);
      }

      .table tbody tr:hover {
        background-color: rgba(4, 255, 0, 0.1);
        cursor: pointer;
      }

      .table tbody tr.table-row-odd {
        background-color: #f8f9fa;
      }

      .table tbody tr.table-row-even {
        background-color: rgba(0, 123, 255, 0.05);
      }

      /* Modern modal styling */
      .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15);
        overflow: hidden;
      }

      .modal-header {
        background-color: var(--success-color);
        color: #ffffff;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        padding: 1.25rem 1.5rem;
        border-bottom: none;
      }

      .modal-title {
        font-weight: 600;
        font-size: 1.25rem;
      }

      .modal-body {
        padding: 1.75rem;
      }

      .modal-footer {
        border-top: none;
        padding: 1rem 1.5rem 1.5rem;
      }

      /* Form styling */
      .form-floating {
        margin-bottom: 1.25rem;
      }

      .form-floating > .form-control {
        padding: 1rem 0.75rem;
        height: calc(3.5rem + 2px);
        line-height: 1.25;
      }

      .form-floating > label {
        padding: 1rem 0.75rem;
      }

      .form-control {
        border-radius: 8px;
        border: 1px solid var(--border-color);
        padding: 0.75rem 1rem;
        font-size: 1rem;
        transition: all 0.2s ease;
      }

      .form-control:focus {
        box-shadow: 0 0 0 0.25rem rgba(76, 175, 80, 0.25);
        border-color: var(--primary-color);
      }

      /* Card-based form layout */
      .form-card {
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        padding: 1.5rem;
        margin-bottom: 1.5rem;
      }

      .form-card-header {
        margin-bottom: 1.25rem;
        border-bottom: 1px solid var(--border-color);
        padding-bottom: 0.75rem;
      }

      .form-card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--success-color);
        margin-bottom: 0;
      }

      /* Button styling */
      .btn-success {
        background-color: var(--success-color);
        border-color: var(--success-color);
        font-weight: 500;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
      }

      .btn-success:hover {
        background-color: var(--hover-green);
        border-color: var(--hover-green);
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }

      .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
        font-weight: 500;
        padding: 0.5rem 1.5rem;
        border-radius: 8px;
        transition: all 0.3s ease;
      }

      .btn-secondary:hover {
        background-color: #5a6268;
        border-color: #545b62;
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }

      /* Map container styling */
      #school-map-container {
        height: 300px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        margin-bottom: 1rem;
      }

      /* Location picker section */
      .location-picker-section {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid var(--border-color);
      }

      .location-picker-section h5 {
        color: var(--success-color);
        margin-bottom: 10px;
        font-size: 1rem;
      }

      .location-picker-section p {
        font-size: 0.85rem;
        color: #6c757d;
        margin-bottom: 10px;
      }

      .coordinates-display {
        display: flex;
        justify-content: space-between;
        margin-top: 10px;
        gap: 10px;
      }

      .coordinate-box {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 8px 12px;
        width: 48%;
      }

      .coordinate-box label {
        display: block;
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 2px;
      }

      .coordinate-box input {
        width: 100%;
        border: none;
        background: transparent;
        color: var(--dark-color);
        font-weight: 500;
      }

      .coordinate-box input:focus {
        outline: none;
      }

      .skeleton-circle {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background-color: #e9ecef;
      }

      .skeleton-text {
        height: 1rem;
        background-color: #e9ecef;
        margin: 0.5rem 0;
        border-radius: 4px;
      }

      .skeleton-nav-item {
        height: 2.5rem;
        background-color: #e9ecef;
        margin: 0.5rem 0;
        border-radius: 4px;
      }

      .skeleton-logo {
        width: 30px;
        height: 30px;
        background-color: #e9ecef;
        border-radius: 4px;
      }

      /* New styles for filter panel */
      .filter-panel {
        display: none;
        position: absolute;
        top: 100%;
        left: 0;
        z-index: 1000;
        min-width: 200px;
        padding: 1rem;
        background-color: #ffffff;
        border-radius: 0.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }

      .filter-panel.show {
        display: block;
      }

      .filter-checkbox {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
      }

      .filter-checkbox input[type="checkbox"] {
        margin-right: 0.5rem;
      }

      /* Improved responsive table styles */
      @media (max-width: 768px) {
        .search-bar input {
          width: 100%;
          margin-bottom: 1rem;
        }

        .action-buttons {
          display: flex;
          gap: 0.5rem;
          flex-wrap: wrap;
          justify-content: space-between;
          width: 100%;
        }

        .action-buttons .btn {
          flex: 1;
          min-width: calc(33% - 0.5rem);
          padding: 0.5rem;
          font-size: 0.875rem;
        }

        /* Table responsive transformation */
        .table-responsive {
          overflow-x: visible;
        }

        /* Modern card-based layout for mobile */
        .school-card {
          background-color: #fff;
          border-radius: 10px;
          box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
          margin-bottom: 16px;
          overflow: hidden;
          cursor: pointer;
        }

        .school-card-header {
          padding: 12px 16px;
          background-color: var(--success-color);
          color: white;
          font-weight: 600;
          display: flex;
          justify-content: space-between;
          align-items: center;
        }

        .school-card-body {
          padding: 16px;
        }

        .school-card-row {
          display: flex;
          justify-content: space-between;
          padding: 8px 0;
          border-bottom: 1px solid rgba(0, 0, 0, 0.05);
        }

        .school-card-row:last-child {
          border-bottom: none;
        }

        .school-card-label {
          font-weight: 600;
          color: var(--dark-color);
        }

        .school-card-value {
          text-align: right;
          color: #555;
        }

        .school-card-footer {
          padding: 12px 16px;
          background-color: #f8f9fa;
          border-top: 1px solid rgba(0, 0, 0, 0.05);
        }

        .see-more-btn {
          width: 100%;
          text-align: center;
          padding: 8px;
          background-color: transparent;
          color: var(--primary-color);
          border: none;
          font-weight: 500;
          cursor: pointer;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: all 0.2s ease;
        }

        .see-more-btn:hover {
          background-color: rgba(76, 175, 80, 0.1);
        }

        .see-more-btn i {
          margin-left: 4px;
          transition: transform 0.2s ease;
        }

        .see-more-btn.active i {
          transform: rotate(180deg);
        }

        .hidden-info {
          display: none;
          padding-top: 8px;
        }

        .hidden-info.show {
          display: block;
          animation: fadeIn 0.3s ease;
        }

        @keyframes fadeIn {
          from {
            opacity: 0;
            transform: translateY(-10px);
          }
          to {
            opacity: 1;
            transform: translateY(0);
          }
        }

        /* Hide the regular table on mobile */
        .table {
          display: none;
        }

        /* Show cards container on mobile */
        .mobile-cards-container {
          display: block;
        }
      }

      @media (min-width: 769px) {
        /* Hide cards on desktop */
        .mobile-cards-container {
          display: none;
        }

        /* Show regular table on desktop */
        .table {
          display: table;
        }
      }

      /* Disabled update button style */
      .btn-update-disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
      }

      /* ENHANCED Enrollment popup styles */
      .enrollment-link {
        cursor: pointer;
        text-decoration: underline;
        color: var(--primary-color) !important;
        font-weight: 500;
        font-size: 1.1rem;
        display: inline-flex;
        align-items: center;
        transition: all 0.2s ease;
      }

      .enrollment-link:hover {
        color: var(--hover-green) !important;
        transform: scale(1.05);
      }


      .table td.enrollment-cell {
        position: relative;
        border-radius: 4px;
        text-align: center;
      }

      .enrollment-popup {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(0, 0, 0, 0.5);
        z-index: 2000;
        justify-content: center;
        align-items: center;
      }

      .enrollment-popup-content {
        background-color: white;
        border-radius: 12px;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
        width: 95%;
        max-width: 900px;
        max-height: 90vh;
        overflow-y: auto;
        position: relative;
      }

      .enrollment-popup-header {
        background-color: #2e7d32;
        color: white;
        padding: 18px 25px;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
      }

      .enrollment-popup-title {
        font-size: 1.5rem;
        font-weight: 600;
        margin: 0;
      }

      .enrollment-popup-close {
        background: none;
        border: none;
        color: white;
        font-size: 1.8rem;
        cursor: pointer;
        padding: 0;
        line-height: 1;
        transition: transform 0.2s ease;
      }

      .enrollment-popup-close:hover {
        transform: scale(1.2);
      }

      .enrollment-popup-body {
        padding: 25px;
      }

      .enrollment-table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
        overflow: hidden;
      }

      .enrollment-table th {
        background-color: #4caf50;
        color: white;
        font-weight: 600;
        text-align: left;
        padding: 12px 15px;
        font-size: 1.05rem;
        border: 1px solid #4caf4f75;
      }

      .enrollment-table td {
        padding: 12px 15px;
        border: 1px solid rgba(117, 117, 117, 0.2);
        font-size: 1.05rem;
      }

      .enrollment-table tr:nth-child(odd) {
        background-color: #f8f9fa;
      }

      .enrollment-table tr:nth-child(even) {
        background-color: rgba(0, 123, 255, 0.05);
      }

      .enrollment-table tr:hover {
        background-color: rgba(76, 175, 80, 0.1);
      }

      .enrollment-table tr.total-row {
        background-color: #e8f5e9;
        font-weight: bold;
        border-top: 2px solid #4caf50;
      }

      .enrollment-summary {
        margin-top: 25px;
        padding: 20px;
        background-color: #f1f8e9;
        border-radius: 8px;
        border: 1px solid #c5e1a5;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      }

      .enrollment-summary h5 {
        margin-top: 0;
        color: #2e7d32;
        font-weight: 600;
        font-size: 1.25rem;
        margin-bottom: 15px;
      }

      .enrollment-summary p {
        margin-bottom: 10px;
        font-size: 1.1rem;
      }

      .enrollment-summary strong {
        color: #2e7d32;
      }

      .gender-distribution {
        margin-top: 20px;
        padding: 15px;
        background-color: white;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
      }

      .gender-distribution h6 {
        color: #2e7d32;
        font-weight: 600;
        margin-bottom: 15px;
        font-size: 1.1rem;
      }

      .progress-container {
        height: 25px;
        background-color: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        margin-bottom: 10px;
      }

      .progress-bar-male {
        height: 100%;
        background-color: #1976d2;
        float: left;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 500;
      }

      .progress-bar-female {
        height: 100%;
        background-color: #e91e63;
        float: left;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 500;
      }

      .gender-legend {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 10px;
      }

      .legend-item {
        display: flex;
        align-items: center;
        gap: 5px;
      }

      .legend-color {
        width: 15px;
        height: 15px;
        border-radius: 3px;
      }

      .legend-male {
        background-color: #1976d2;
      }

      .legend-female {
        background-color: #e91e63;
      }

      .badge-percentage {
        background-color: #4caf50;
        color: white;
        padding: 4px 8px;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 500;
        margin-left: 5px;
      }

      @media (max-width: 768px) {
        .enrollment-popup-content {
          width: 95%;
          max-height: 85vh;
        }

        .enrollment-table {
          font-size: 0.9rem;
        }

        .enrollment-table th,
        .enrollment-table td {
          padding: 10px;
          font-size: 0.95rem;
        }

        .enrollment-summary p {
          font-size: 1rem;
        }
      }
    </style>

</head>
  <body>
    <div id="sidebar-container">
      <!-- Sidebar will be loaded here -->
    </div>
    <div id="topbar-container">
      <!-- Topbar will be loaded here -->
    </div>

    <!-- Main Content -->
    <div class="main-content p-4">
      <div class="content-wrapper">
        <div
          class="d-flex flex-wrap justify-content-between align-items-center mb-4"
        >
          <div class="d-flex flex-wrap align-items-center gap-2 mb-2 mb-sm-0">
            <div class="position-relative">
              <button class="btn btn-outline-secondary" id="filterButton">
                <i class="bi bi-funnel"></i> Filter
              </button>
              <div class="filter-panel" id="filterPanel">
                <h6 class="mb-3">Filter Options</h6>
                <div>
                  <input type="checkbox" value="0" id="sidCheck" />
                  <label for="sidCheck">School ID (SID)</label>
                </div>
                <div>
                  <input type="checkbox" value="1" id="sNameCheck" />
                  <label for="sNameCheck">School Name</label>
                </div>
                <div>
                  <input type="checkbox" value="2" id="typeCheck" />
                  <label for="typeCheck">Type</label>
                </div>
                <div>
                  <input type="checkbox" value="3" id="popCheck" />
                  <label for="popCheck">Population</label>
                </div>
                <div>
                  <input type="checkbox" value="4" id="districtCheck" />
                  <label for="districtCheck">District</label>
                </div>
                <div>
                  <input type="checkbox" value="5" id="principalCheck" />
                  <label for="principalCheck">Principal</label>
                </div>
                <div>
                  <input type="checkbox" value="6" id="CNumCheck" />
                  <label for="CNumCheck">Contact Number</label>
                </div>
                <div>
                  <input type="checkbox" value="7" id="emailCheck" />
                  <label for="emailCheck">Email</label>
                </div>
                <div>
                  <input type="checkbox" value="8" id="ageCheck" />
                  <label for="ageCheck">Age</label>
                </div>
              </div>
            </div>
            <div class="search-bar">
              <input
                type="text"
                placeholder="Search schools..."
                class="form-control"
                id="searchInput"
              />
            </div>
          </div>

          <div class="action-buttons">
            <button
              class="btn btn-success"
              data-bs-toggle="modal"
              data-bs-target="#schoolModal"
              onclick="prepareModal('add')"
            >
              <i class="bi bi-plus-lg"></i>
              Add
            </button>
            <button
              class="btn btn-primary btn-update-disabled"
              id="updateButton"
              data-bs-toggle="modal"
              data-bs-target="#schoolModal"
              onclick="prepareModal('update')"
              disabled
            >
              <i class="bi bi-pencil-square"></i>
              Update
            </button>
            <button class="btn btn-warning" onclick="exportToExcel()">
              <i class="bi bi-file-earmark-excel"></i>
              Export
            </button>
          </div>
        </div>

        <!-- Desktop Table View -->
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>SID</th>
                <th>School Name</th>
                <th>Type</th>
                <th>Enrollment</th>
                <th>District</th>
                <th>Principal</th>
                <th>Contact No.</th>
                <th>Email</th>
                <th>Age</th>
              </tr>
            </thead>
            <tbody id="schoolTableBody">
              <tr>
                <td colspan="9" class="text-center">
                  <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </td>
              </tr>
              <!-- Table rows will be populated by JavaScript -->
            </tbody>
          </table>
        </div>

        <!-- Mobile Cards View -->
        <div class="mobile-cards-container" id="mobileCardsContainer">
          <!-- Mobile cards will be inserted here by JavaScript -->
          <div class="text-center py-4">
            <div class="spinner-border" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>

        <!-- Pagination (works for both views) -->
        <div id="pagination" class="d-flex justify-content-center mt-3">
          <button id="prevPage" class="btn btn-outline-secondary mx-1">
            Previous
          </button>
          <span id="pageInfo" class="mx-2 d-flex align-items-center"></span>
          <button id="nextPage" class="btn btn-outline-secondary mx-1">
            Next
          </button>
        </div>
      </div>
    </div>

    <!-- School Modal (for both Add and Update) -->
    <div
      class="modal fade"
      id="schoolModal"
      tabindex="-1"
      aria-labelledby="schoolModalLabel"
      aria-hidden="true"
    >
      <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="schoolModalLabel">Add/Update School</h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form id="schoolForm">
              <div class="row">
                <!-- Left Column - School Information -->
                <div class="col-md-6">
                  <div class="form-card">
                    <div class="form-card-header">
                      <h6 class="form-card-title">
                        <i class="bi bi-building"></i> School Information
                      </h6>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="text"
                        class="form-control"
                        id="schoolId"
                        placeholder="School ID"
                        required
                      />
                      <label for="schoolId">School ID (SID)</label>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="text"
                        class="form-control"
                        id="schoolName"
                        placeholder="School Name"
                        required
                      />
                      <label for="schoolName">School Name</label>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="text"
                        class="form-control"
                        id="schoolType"
                        placeholder="Type"
                        required
                      />
                      <label for="schoolType">Type</label>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="number"
                        class="form-control"
                        id="schoolPopulation"
                        placeholder="Enrollment"
                        required
                      />
                      <label for="schoolPopulation">Enrollment</label>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="text"
                        class="form-control"
                        id="schoolDistrict"
                        placeholder="District"
                        required
                      />
                      <label for="schoolDistrict">District</label>
                    </div>
                  </div>

                  <div class="form-card">
                    <div class="form-card-header">
                      <h6 class="form-card-title">
                        <i class="bi bi-person"></i> Principal Information
                      </h6>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="text"
                        class="form-control"
                        id="schoolPrincipal"
                        placeholder="Principal"
                        required
                      />
                      <label for="schoolPrincipal">Principal</label>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="tel"
                        class="form-control"
                        id="contactNumber"
                        placeholder="Contact Number"
                      />
                      <label for="contactNumber">Contact Number</label>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="email"
                        class="form-control"
                        id="emailAddress"
                        placeholder="Email Address"
                      />
                      <label for="emailAddress">Email Address</label>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="number"
                        class="form-control"
                        id="principalAge"
                        placeholder="Age"
                        min="18"
                        max="100"
                      />
                      <label for="principalAge">Age</label>
                    </div>
                  </div>
                </div>

                <!-- Right Column - Map and Location -->
                <div class="col-md-6">
                  <div class="form-card">
                    <div class="form-card-header">
                      <h6 class="form-card-title">
                        <i class="bi bi-geo-alt"></i> School Location
                      </h6>
                    </div>

                    <div class="location-picker-section mb-3">
                      <h5><i class="bi bi-pin-map"></i> Location Picker</h5>
                      <p>
                        Click on the map to set the exact location of your
                        school. You can drag the marker to adjust the position.
                      </p>
                    </div>

                    <div id="school-map-container"></div>

                    <div class="coordinates-display">
                      <div class="coordinate-box">
                        <label for="latitude">Latitude</label>
                        <input
                          type="number"
                          id="latitude"
                          step="0.0000001"
                          placeholder="0.000000"
                          value="14.5995"
                        />
                      </div>
                      <div class="coordinate-box">
                        <label for="longitude">Longitude</label>
                        <input
                          type="number"
                          id="longitude"
                          step="0.0000001"
                          placeholder="0.000000"
                          value="120.9842"
                        />
                      </div>
                    </div>

                    <div class="d-flex gap-2 mt-3">
                      <button
                        type="button"
                        class="btn btn-sm btn-secondary"
                        onclick="resetMapLocation()"
                      >
                        <i class="bi bi-geo"></i> Reset Location
                      </button>
                      <button
                        type="button"
                        class="btn btn-sm btn-info text-white"
                        onclick="useCurrentLocation()"
                      >
                        <i class="bi bi-cursor"></i> Use Current Location
                      </button>
                    </div>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              <i class="bi bi-x-circle"></i> Cancel
            </button>

            <button type="button" class="btn btn-success" id="saveSchoolBtn">
              <i class="bi bi-check-circle"></i> Save Changes
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Enrollment Popup -->
    <div class="enrollment-popup" id="enrollmentPopup">
      <div class="enrollment-popup-content">
        <div class="enrollment-popup-header">
          <h5 class="enrollment-popup-title">Enrollment Details</h5>
          <button
            class="enrollment-popup-close"
            onclick="closeEnrollmentPopup()"
          >
            &times;
          </button>
        </div>
        <div class="enrollment-popup-body" id="enrollmentPopupBody">
          <!-- Content will be dynamically inserted here -->
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
      // --- Global Variables ---
      let schoolMap = null;
      let schoolMarker = null;
      let defaultLat = 14.5995; // Default to Manila
      let defaultLng = 120.9842;
      let schoolsData = [];
      let selectedSchoolIndex = -1;
      let selectedSchool = null;

      document.addEventListener("DOMContentLoaded", () => {
        loadComponent("sidebar.php", "sidebar-container", initializeSidebar);
        loadComponent("topbar.php", "topbar-container", initializeTopbar);
        fetchData();
        initializeFilters();
        document.getElementById("latitude").addEventListener("change", updateMarkerFromInputs);
        document.getElementById("longitude").addEventListener("change", updateMarkerFromInputs);
        document.getElementById("schoolModal").addEventListener("shown.bs.modal", initializeMap);
        
        // Set up save button event listener
        document.getElementById("saveSchoolBtn").addEventListener("click", function() {
          if (document.getElementById("schoolModalLabel").textContent.includes("Add")) {
            addSchool();
          } else {
            updateSchool();
          }
        });
      });

      // --- Component Loader ---
      function loadComponent(componentName, containerId, callback) {
        const container = document.getElementById(containerId);
        if (!container) return;
        
        fetch(componentName)
          .then((response) => { 
            if (!response.ok) throw new Error(`Failed to load ${componentName}`); 
            return response.text(); 
          })
          .then((html) => { 
            container.innerHTML = html; 
            if (callback) callback(); 
          })
          .catch((error) => { 
            console.error(error); 
            // Create fallback components if loading fails
            if (componentName === "sidebar.php") {
              createFallbackSidebar(container);
            } else if (componentName === "topbar.php") {
              createFallbackTopbar(container);
            }
          });
      }
      
      // Create fallback sidebar if loading fails
      function createFallbackSidebar(container) {
        container.innerHTML = `
          <div class="sidebar bg-dark text-white" style="width: 250px; position: fixed; height: 100vh; left: 0; top: 0; z-index: 1000; padding-top: 60px;">
            <div class="px-3 py-2">
              <h5 class="text-center mb-4">DepEd Dashboard</h5>
              <nav class="nav flex-column">
                <a class="nav-link text-white" href="dashboard.php"><i class="bi bi-speedometer2 me-2"></i> Dashboard</a>
                <a class="nav-link text-white active" href="school-info.php"><i class="bi bi-building me-2"></i> School Information</a>
                <a class="nav-link text-white" href="teachers.php"><i class="bi bi-person-badge me-2"></i> Teachers</a>
                <a class="nav-link text-white" href="students.php"><i class="bi bi-people me-2"></i> Students</a>
                <a class="nav-link text-white" href="reports.php"><i class="bi bi-file-earmark-text me-2"></i> Reports</a>
                <a class="nav-link text-white" href="settings.php"><i class="bi bi-gear me-2"></i> Settings</a>
              </nav>
            </div>
            <div class="mt-auto px-3 py-2 border-top">
              <a class="nav-link text-white" href="#" id="logoutLink"><i class="bi bi-box-arrow-right me-2"></i> Logout</a>
            </div>
          </div>
        `;
        initializeSidebar();
      }
      
      // Create fallback topbar if loading fails
      function createFallbackTopbar(container) {
        container.innerHTML = `
          <nav class="navbar navbar-expand-lg navbar-dark bg-success fixed-top" style="z-index: 1001; padding-left: 260px;">
            <div class="container-fluid">
              <button id="sidebarToggle" class="btn btn-sm btn-success me-2">
                <i class="bi bi-list"></i>
              </button>
              <a class="navbar-brand" href="#">DepEd School Management</a>
              <div class="ms-auto d-flex align-items-center">
                <div class="dropdown">
                  <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                    <i class="bi bi-person-circle me-1"></i> Admin User
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="profile.php"><i class="bi bi-person me-2"></i> Profile</a></li>
                    <li><a class="dropdown-item" href="#" id="logoutLink"><i class="bi bi-box-arrow-right me-2"></i> Logout</a></li>
                  </ul>
                </div>
              </div>
            </div>
          </nav>
        `;
        initializeTopbar();
      }

      // --- Sidebar/Topbar ---
      function initializeSidebar() {
        setTimeout(() => {
          const navLinks = document.querySelectorAll(".sidebar nav a");
          if (!navLinks.length) return;
          const currentPage = window.location.pathname.split("/").pop();
          navLinks.forEach((link) => {
            link.classList.toggle("active", link.getAttribute("href") === currentPage);
            link.addEventListener("click", function () {
              document.querySelectorAll(".sidebar nav a").forEach((el) => el.classList.remove("active"));
              this.classList.add("active");
            });
          });
        }, 200);
      }
      
      function initializeTopbar() {
        setTimeout(() => {
          const sidebarToggle = document.getElementById("sidebarToggle");
          const sidebar = document.querySelector(".sidebar");
          if (!sidebarToggle || !sidebar) return;
          sidebarToggle.addEventListener("click", () => sidebar.classList.toggle("show"));
          document.addEventListener("click", (e) => {
            if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) sidebar.classList.remove("show");
          });
        }, 200);
      }
      
      document.addEventListener("click", (event) => {
        if (event.target.id === "logoutLink") {
          event.preventDefault();
          if (typeof Swal !== "undefined") {
            Swal.fire({ 
              title: "Are you sure?", 
              text: "You are about to logout!", 
              icon: "warning", 
              showCancelButton: true, 
              confirmButtonColor: "#3085d6", 
              cancelButtonColor: "#d33", 
              confirmButtonText: "Yes, logout!" 
            }).then((result) => { 
              if (result.isConfirmed) window.location.href = "index.php"; 
            });
          } else {
            console.error("SweetAlert (Swal) is not defined. Make sure it's included in your HTML.");
            if (confirm("Are you sure you want to logout?")) {
              window.location.href = "index.php";
            }
          }
        }
      });

      function fetchData() {
        const loadingScreen = document.getElementById("loadingScreen");
        if (loadingScreen) loadingScreen.style.display = "flex";

        fetch("phpp/dashboard/fetchSchoolInfo.php", { method: "GET" })
          .then((response) => {
            if (!response.ok) {
              throw new Error("Network response was not ok");
            }
            return response.json();
          })
          .then((schools) => {
            if (!Array.isArray(schools)) {
              throw new Error("Invalid school data received.");
            }
            schoolsData = schools;
            populateTable(schools);
            populateMobileCards(schools);
            initializePagination();
            initializeEnrollmentLinks();
          })
          .catch((error) => {
            console.error("Error fetching data:", error);
            

            // Show error notification
            if (typeof Swal !== "undefined") {
              Swal.fire({
                icon: "warning",
                title: "Connection Issue",
                text: "Using sample data. Database connection failed.",
                confirmButtonColor: "#4caf50"
              });
            } else {
              alert("Database connection failed. Using sample data instead.");
            }
          })
          .finally(() => {
            if (loadingScreen) loadingScreen.style.display = "none";
          });
      }

      

      // Function to populate the desktop table
      function populateTable(data) {
        const tbody = document.getElementById("schoolTableBody");
        if (!tbody) return;

        tbody.innerHTML = "";

        if (data.length === 0) {
          tbody.innerHTML =
            '<tr><td colspan="9" class="text-center">No data available</td></tr>';
          return;
        }

        // Column headers for data-label attributes
        const headers = [
          "SID",
          "School Name",
          "Type",
          "Enrollment",
          "District",
          "Principal",
          "Contact No.",
          "Email",
          "Age",
        ];

        data.forEach((school, index) => {
          const row = document.createElement("tr");
          row.setAttribute("data-row-id", index);
          row.classList.add(
            index % 2 === 0 ? "table-row-even" : "table-row-odd"
          );

          row.innerHTML = `
          <td data-label="${headers[0]}">${school.SchoolID || "N/A"}</td>
          <td data-label="${headers[1]}">${school.Schoolname || "N/A"}</td>
          <td data-label="${headers[2]}">${school.Institution || "N/A"}</td>
          <td data-label="${headers[3]}" class="enrollment-cell"><a class="enrollment-link">${
            school.total_enrollees || "N/A"
          }</a></td>
          <td data-label="${headers[4]}">${school.district || "N/A"}</td>
          <td data-label="${headers[5]}">${school.principal || "N/A"}</td>
          <td data-label="${headers[6]}">${school.contact_no || "N/A"}</td>
          <td data-label="${headers[7]}">${school.email || "N/A"}</td>
          <td data-label="${headers[8]}">${school.principal_age || "N/A"}</td>
        `;

          // Add click event to select row and enable update button
          row.addEventListener("click", function (e) {
            // Skip if clicking on enrollment cell
            if (e.target.classList.contains("enrollment-link") || e.target.closest(".enrollment-link")) {
              return;
            }

            // Remove selection from all rows
            document.querySelectorAll("#schoolTableBody tr").forEach((r) => {
              r.classList.remove("table-primary");
            });

            // Add selection to clicked row
            this.classList.add("table-primary");

            // Store selected school
            selectedSchoolIndex = index;
            selectedSchool = schoolsData[index];

            // Enable update button
            const updateButton = document.getElementById("updateButton");
            updateButton.classList.remove("btn-update-disabled");
            updateButton.disabled = false;
          });

          tbody.appendChild(row);
        });
      }

      // Function to populate mobile cards
      function populateMobileCards(data) {
        const container = document.getElementById("mobileCardsContainer");
        if (!container) return;

        container.innerHTML = "";

        if (data.length === 0) {
          container.innerHTML =
            '<div class="text-center py-4">No data available</div>';
          return;
        }

        data.forEach((school, index) => {
          const card = document.createElement("div");
          card.className = "school-card";
          card.setAttribute("data-row-id", index);

          // Create card header with school ID
          const cardHeader = document.createElement("div");
          cardHeader.className = "school-card-header";
          cardHeader.innerHTML = `
            <span>School ID: ${school.SchoolID || "N/A"}</span>
            <span class="badge bg-light text-dark">${
              school.Institution || "N/A"
            }</span>
          `;

          // Create card body with main information
          const cardBody = document.createElement("div");
          cardBody.className = "school-card-body";

          // Add the main visible information
          cardBody.innerHTML = `
            <div class="school-card-row">
              <span class="school-card-label">School Name:</span>
              <span class="school-card-value">${
                school.Schoolname || "N/A"
              }</span>
            </div>
            <div class="school-card-row">
              <span class="school-card-label">Principal:</span>
              <span class="school-card-value">${
                school.principal || "N/A"
              }</span>
            </div>
            <div class="school-card-row">
              <span class="school-card-label">Enrollment:</span>
              <span class="school-card-value enrollment-cell"><a class="enrollment-link"> ${
                school.total_enrollees || "N/A"
              }</a></span>
            </div>
            <div class="school-card-row">
              <span class="school-card-label">District:</span>
              <span class="school-card-value">${school.district || "N/A"}</span>
            </div>
          `;

          // Create the hidden information section
          const hiddenInfo = document.createElement("div");
          hiddenInfo.className = "hidden-info";
          hiddenInfo.id = `hidden-info-${index}`;
          hiddenInfo.innerHTML = `
            <div class="school-card-row">
              <span class="school-card-label">Contact No.:</span>
              <span class="school-card-value">${
                school.contact_no || "N/A"
              }</span>
            </div>
            <div class="school-card-row">
              <span class="school-card-label">Email:</span>
              <span class="school-card-value">${school.email || "N/A"}</span>
            </div>
            <div class="school-card-row">
              <span class="school-card-label">Age:</span>
              <span class="school-card-value">${
                school.principal_age || "N/A"
              }</span>
            </div>
          `;

          // Create card footer with "See more" button
          const cardFooter = document.createElement("div");
          cardFooter.className = "school-card-footer";

          const seeMoreBtn = document.createElement("button");
          seeMoreBtn.className = "see-more-btn";
          seeMoreBtn.innerHTML = `See more... <span class="ms-1"><i class="bi bi-chevron-down"></i></span>`;
          seeMoreBtn.onclick = function (e) {
            e.stopPropagation(); // Prevent card click event
            toggleDetails(index);
          };

          // Add click event to the card to select it and enable update button
          card.addEventListener("click", function (e) {
            // Skip if clicking on enrollment cell or see more button
            if (
              e.target.classList.contains("enrollment-link") ||
              e.target.closest(".enrollment-link") ||
              e.target.closest(".see-more-btn")
            ) {
              return;
            }

            // Remove selection from all cards
            document.querySelectorAll(".school-card").forEach((c) => {
              c.classList.remove("border-primary");
              c.style.boxShadow = "";
            });

            // Add selection to clicked card
            this.classList.add("border-primary");
            this.style.boxShadow = "0 0 0 0.25rem rgba(13, 110, 253, 0.25)";

            // Store selected school
            selectedSchoolIndex = index;
            selectedSchool = schoolsData[index];

            // Enable update button
            const updateButton = document.getElementById("updateButton");
            updateButton.classList.remove("btn-update-disabled");
            updateButton.disabled = false;
          });

          // Assemble the card
          cardFooter.appendChild(seeMoreBtn);
          cardBody.appendChild(hiddenInfo);
          card.appendChild(cardHeader);
          card.appendChild(cardBody);
          card.appendChild(cardFooter);

          // Add to container
          container.appendChild(card);
        });
      }

      // Function to toggle the expanded details
      function toggleDetails(rowIndex) {
        const hiddenInfo = document.getElementById(`hidden-info-${rowIndex}`);
        const card = hiddenInfo.closest(".school-card");
        const seeMoreBtn = card.querySelector(".see-more-btn");

        if (hiddenInfo.classList.contains("show")) {
          hiddenInfo.classList.remove("show");
          seeMoreBtn.innerHTML = `See more... <span class="ms-1"><i class="bi bi-chevron-down"></i></span>`;
          seeMoreBtn.classList.remove("active");
        } else {
          hiddenInfo.classList.add("show");
          seeMoreBtn.innerHTML = `See less <span class="ms-1"><i class="bi bi-chevron-up"></i></span>`;
          seeMoreBtn.classList.add("active");
        }
      }

      // --- Map Functions ---
      function initializeMap() {
        const mapContainer = document.getElementById("school-map-container");
        if (!mapContainer) return;
        const lat = parseFloat(document.getElementById("latitude").value) || defaultLat;
        const lng = parseFloat(document.getElementById("longitude").value) || defaultLng;
        if (schoolMap) { schoolMap.remove(); schoolMap = null; }
        schoolMap = L.map("school-map-container").setView([lat, lng], 13);
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(schoolMap);
        schoolMarker = L.marker([lat, lng], { draggable: true }).addTo(schoolMap);
        schoolMarker.on("dragend", function (event) {
          const marker = event.target;
          const position = marker.getLatLng();
          document.getElementById("latitude").value = position.lat.toFixed(7);
          document.getElementById("longitude").value = position.lng.toFixed(7);
        });
        schoolMap.on("click", function (e) {
          schoolMarker.setLatLng(e.latlng);
          document.getElementById("latitude").value = e.latlng.lat.toFixed(7);
          document.getElementById("longitude").value = e.latlng.lng.toFixed(7);
        });
        setTimeout(() => { if (schoolMap) schoolMap.invalidateSize(); }, 100);
      }
      
      function updateMarkerFromInputs() {
        if (!schoolMap || !schoolMarker) return;
        const lat = parseFloat(document.getElementById("latitude").value);
        const lng = parseFloat(document.getElementById("longitude").value);
        if (!isNaN(lat) && !isNaN(lng)) {
          const newLatLng = L.latLng(lat, lng);
          schoolMarker.setLatLng(newLatLng);
          schoolMap.panTo(newLatLng);
        }
      }
      
      function useCurrentLocation() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(
            (position) => {
              const lat = position.coords.latitude;
              const lng = position.coords.longitude;
              document.getElementById("latitude").value = lat.toFixed(7);
              document.getElementById("longitude").value = lng.toFixed(7);
              if (schoolMarker && schoolMap) {
                const latLng = L.latLng(lat, lng);
                schoolMarker.setLatLng(latLng);
                schoolMap.panTo(latLng);
                schoolMap.setZoom(15);
              }
              Swal.fire({ toast: true, position: "top-end", icon: "success", title: "Current location set", showConfirmButton: false, timer: 1500 });
            },
            (error) => {
              let errorMessage = "Unable to retrieve your location";
              switch (error.code) {
                case error.PERMISSION_DENIED: errorMessage = "Location access was denied by the user."; break;
                case error.POSITION_UNAVAILABLE: errorMessage = "Location information is unavailable."; break;
                case error.TIMEOUT: errorMessage = "The request to get user location timed out."; break;
              }
              Swal.fire({ icon: "error", title: "Geolocation Error", text: errorMessage });
            }
          );
        } else {
          Swal.fire({ icon: "error", title: "Geolocation Not Supported", text: "Your browser does not support geolocation." });
        }
      }
      
      function resetMapLocation() {
        document.getElementById("latitude").value = defaultLat.toFixed(7);
        document.getElementById("longitude").value = defaultLng.toFixed(7);
        if (schoolMarker && schoolMap) {
          const latLng = L.latLng(defaultLat, defaultLng);
          schoolMarker.setLatLng(latLng);
          schoolMap.panTo(latLng);
          schoolMap.setZoom(13);
        }
      }

      // --- Modal Functions ---
      function prepareModal(action) {
        const modalTitle = document.getElementById("schoolModalLabel");
        const saveButton = document.getElementById("saveSchoolBtn");
        const schoolIdInput = document.getElementById("schoolId");
        if (!modalTitle || !saveButton || !schoolIdInput) return;
        
        modalTitle.textContent = action === "add" ? "Add New School" : "Update School Information";
        saveButton.textContent = action === "add" ? "Add School" : "Update School";
        schoolIdInput.readOnly = action !== "add";
        
        if (action === "add") {
          // Clear form for new school
          document.getElementById("schoolForm").reset();
          document.getElementById("latitude").value = defaultLat.toFixed(7);
          document.getElementById("longitude").value = defaultLng.toFixed(7);
        } else if (action === "update" && selectedSchool) {
          // Populate form with selected school data
          populateFormWithSchoolData();
        }
      }

      function populateFormWithSchoolData() {
        if (!selectedSchool) return;

        // Populate form fields
        document.getElementById("schoolId").value = selectedSchool.SchoolID || "";
        document.getElementById("schoolName").value = selectedSchool.Schoolname || "";
        document.getElementById("schoolType").value = selectedSchool.Institution || "";
        document.getElementById("schoolPopulation").value = selectedschool.total_enrollees || "";
        document.getElementById("schoolDistrict").value = selectedSchool.district || "";
        document.getElementById("schoolPrincipal").value = selectedSchool.principal || "";
        document.getElementById("contactNumber").value = selectedSchool.contact_no || "";
        document.getElementById("emailAddress").value = selectedSchool.email || "";
        document.getElementById("principalAge").value = selectedSchool.principal_age || "";

        // Set coordinates if available, otherwise use defaults
        document.getElementById("latitude").value = selectedSchool.latitude || defaultLat.toFixed(7);
        document.getElementById("longitude").value = selectedSchool.longitude || defaultLng.toFixed(7);
      }

      function addSchool() {
        // Validate form
        const form = document.getElementById("schoolForm");
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        // Show loading state
        const saveBtn = document.getElementById("saveSchoolBtn");
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        saveBtn.disabled = true;

        // Get form data including coordinates  Saving...';
        saveBtn.disabled = true;

        // Get form data including coordinates
        const formData = {
          schoolId: document.getElementById("schoolId").value,
          schoolName: document.getElementById("schoolName").value,
          schoolType: document.getElementById("schoolType").value,
          schoolPopulation: document.getElementById("schoolPopulation").value,
          schoolDistrict: document.getElementById("schoolDistrict").value,
          schoolPrincipal: document.getElementById("schoolPrincipal").value,
          contactNumber: document.getElementById("contactNumber").value,
          emailAddress: document.getElementById("emailAddress").value,
          principalAge: document.getElementById("principalAge").value,
          latitude: document.getElementById("latitude").value,
          longitude: document.getElementById("longitude").value,
        };

        // Prepare data for API call
        const apiData = new FormData();
        for (const key in formData) {
          apiData.append(key, formData[key]);
        }

        // Make API call to add school
        fetch("phpp/dashboard/addSchool.php", {
          method: "POST",
          body: apiData
        })
          .then(response => response.json())
          .then(data => {
            // Reset button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;

            if (data.success) {
              // Show success message
              Swal.fire({
                icon: "success",
                title: "School Added",
                text: "The school has been added successfully!",
                confirmButtonColor: "#2e7d32",
              });

              closeModal();
              // Refresh data after adding
              fetchData();
            } else {
              // Show error message
              Swal.fire({
                icon: "error",
                title: "Error",
                text: data.message || "Failed to add school. Please try again.",
                confirmButtonColor: "#dc3545",
              });
            }
          })
          .catch(error => {
            console.error("Error adding school:", error);
            
            // Reset button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;

            // For demo purposes, simulate success
            // In production, this should show an error message
            Swal.fire({
              icon: "success",
              title: "School Added (Demo)",
              text: "This is a demo. In production, this would add to the database.",
              confirmButtonColor: "#2e7d32",
            });

            // Add to local data for demo
            const newSchool = {
              SchoolID: formData.schoolId,
              Schoolname: formData.schoolName,
              Institution: formData.schoolType,
              population: formData.schoolPopulation,
              district: formData.schoolDistrict,
              principal: formData.schoolPrincipal,
              contact_no: formData.contactNumber,
              email: formData.emailAddress,
              principal_age: formData.principalAge,
              latitude: formData.latitude,
              longitude: formData.longitude
            };
            
            schoolsData.unshift(newSchool);
            closeModal();
            populateTable(schoolsData);
            populateMobileCards(schoolsData);
            initializePagination();
            initializeEnrollmentLinks();
          });
      }

      function updateSchool() {
        // Validate form
        const form = document.getElementById("schoolForm");
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        // Show loading state
        const saveBtn = document.getElementById("saveSchoolBtn");
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        saveBtn.disabled = true;

        // Get form data including coordinates
        const formData = {
          schoolId: document.getElementById("schoolId").value,
          schoolName: document.getElementById("schoolName").value,
          schoolType: document.getElementById("schoolType").value,
          schoolPopulation: document.getElementById("schoolPopulation").value,
          schoolDistrict: document.getElementById("schoolDistrict").value,
          schoolPrincipal: document.getElementById("schoolPrincipal").value,
          contactNumber: document.getElementById("contactNumber").value,
          emailAddress: document.getElementById("emailAddress").value,
          principalAge: document.getElementById("principalAge").value,
          latitude: document.getElementById("latitude").value,
          longitude: document.getElementById("longitude").value,
        };

        // Prepare data for API call
        const apiData = new FormData();
        for (const key in formData) {
          apiData.append(key, formData[key]);
        }

        // Make API call to update school
        fetch("phpp/dashboard/updateSchool.php", {
          method: "POST",
          body: apiData
        })
          .then(response => response.json())
          .then(data => {
            // Reset button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;

            if (data.success) {
              // Show success message
              Swal.fire({
                icon: "success",
                title: "School Updated",
                text: "The school information has been updated successfully!",
                confirmButtonColor: "#2e7d32",
              });

              closeModal();
              // Refresh data after updating
              fetchData();
            } else {
              // Show error message
              Swal.fire({
                icon: "error",
                title: "Error",
                text: data.message || "Failed to update school. Please try again.",
                confirmButtonColor: "#dc3545",
              });
            }
          })
          .catch(error => {
            console.error("Error updating school:", error);
            
            // Reset button state
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;

            // For demo purposes, simulate success
            // In production, this should show an error message
            Swal.fire({
              icon: "success",
              title: "School Updated (Demo)",
              text: "This is a demo. In production, this would update the database.",
              confirmButtonColor: "#2e7d32",
            });

            // Update local data for demo
            if (selectedSchoolIndex >= 0 && selectedSchoolIndex < schoolsData.length) {
              schoolsData[selectedSchoolIndex] = {
                ...schoolsData[selectedSchoolIndex],
                SchoolID: formData.schoolId,
                Schoolname: formData.schoolName,
                Institution: formData.schoolType,
                population: formData.schoolPopulation,
                district: formData.schoolDistrict,
                principal: formData.schoolPrincipal,
                contact_no: formData.contactNumber,
                email: formData.emailAddress,
                principal_age: formData.principalAge,
                latitude: formData.latitude,
                longitude: formData.longitude
              };
            }
            
            closeModal();
            populateTable(schoolsData);
            populateMobileCards(schoolsData);
            initializePagination();
            initializeEnrollmentLinks();
          });
      }

      function closeModal() {
        if (typeof bootstrap !== "undefined") {
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("schoolModal")
          );
          if (modal) modal.hide();
        } else {
          console.error(
            "Bootstrap is not defined. Make sure it's included in your HTML."
          );
          alert("Closing modal failed. Bootstrap is required.");
        }
      }

      // --- Search and Filter Functions ---
      function initializeFilters() {
        const filterButton = document.getElementById("filterButton");
        const filterPanel = document.getElementById("filterPanel");

        if (!filterButton || !filterPanel) return;

        filterButton.addEventListener("click", (event) => {
          event.stopPropagation();
          filterPanel.classList.toggle("show");
        });

        document.addEventListener("click", (event) => {
          if (
            !filterPanel.contains(event.target) &&
            event.target !== filterButton
          ) {
            filterPanel.classList.remove("show");
          }
        });

        // Add event listeners to filter checkboxes
        const filterCheckboxes = document.querySelectorAll(
          ".filter-panel input[type='checkbox']"
        );
        filterCheckboxes.forEach((checkbox) => {
          checkbox.addEventListener("change", () => {
            filterTable();
            resetPagination();
          });
        });
        
        // Add event listener to search input
        const searchInput = document.getElementById("searchInput");
        if (searchInput) {
          searchInput.addEventListener("input", () => {
            filterTable();
            resetPagination(); // Reset to first page after filtering
          });
        }
      }

      function filterTable() {
        const input =
          document.getElementById("searchInput")?.value.toLowerCase() || "";
        const checkboxes = document.querySelectorAll(
          ".filter-panel input[type='checkbox']:checked"
        );

        const selectedColumns = Array.from(checkboxes).map((checkbox) =>
          Number.parseInt(checkbox.value)
        );

        // Filter desktop table
        filterDesktopTable(input, selectedColumns);

        // Filter mobile cards
        filterMobileCards(input, selectedColumns);

        // Update pagination after filtering
        updatePagination();
      }

      function filterDesktopTable(input, selectedColumns) {
        const tbody = document.getElementById("schoolTableBody");
        if (!tbody) return;

        const rows = tbody.querySelectorAll("tr");

        rows.forEach((row) => {
          let showRow = false;
          const cells = row.querySelectorAll("td");

          if (selectedColumns.length === 0) {
            // If no filters are checked, search in all columns
            showRow = Array.from(cells).some((cell) =>
              cell.textContent.toLowerCase().includes(input)
            );
          } else {
            // Search only in checked columns
            selectedColumns.forEach((columnIndex) => {
              const cell = cells[columnIndex]; // Ensure the index is valid
              if (cell && cell.textContent.toLowerCase().includes(input)) {
                showRow = true;
              }
            });
          }

          // Set visibility
          row.setAttribute("data-visible", showRow ? "true" : "false");
          row.style.display = showRow ? "table-row" : "none";
        });
      }

      function filterMobileCards(input, selectedColumns) {
        const container = document.getElementById("mobileCardsContainer");
        if (!container) return;

        const cards = container.querySelectorAll(".school-card");

        cards.forEach((card) => {
          let showCard = false;

          // Map column indices to card elements
          const cardElements = {
            0: card.querySelector(".school-card-header span:first-child"), // SID
            1: card.querySelectorAll(".school-card-row")[0], // School Name
            2: card.querySelector(".school-card-header .badge"), // Type
            3: card.querySelectorAll(".school-card-row")[2], // Enrollment
            4: card.querySelectorAll(".school-card-row")[3], // District
            5: card.querySelectorAll(".school-card-row")[1], // Principal
            6: card.querySelector(".hidden-info .school-card-row:nth-child(1)"), // Contact No.
            7: card.querySelector(".hidden-info .school-card-row:nth-child(2)"), // Email
            8: card.querySelector(".hidden-info .school-card-row:nth-child(3)"), // Age
          };

          if (selectedColumns.length === 0) {
            // If no filters are checked, search in all text content
            showCard = card.textContent.toLowerCase().includes(input);
          } else {
            // Search only in checked columns
            selectedColumns.forEach((columnIndex) => {
              const element = cardElements[columnIndex];
              if (
                element &&
                element.textContent.toLowerCase().includes(input)
              ) {
                showCard = true;
              }
            });
          }

          // Set visibility
          card.style.display = showCard ? "block" : "none";
          card.setAttribute("data-visible", showCard ? "true" : "false");
        });
      }

      // --- Pagination Functions ---
      let currentPage = 1;
      const rowsPerPage = 15; // Change this value to set rows per page

      function initializePagination() {
        const prevBtn = document.getElementById("prevPage");
        const nextBtn = document.getElementById("nextPage");

        if (!prevBtn || !nextBtn) return;

        prevBtn.addEventListener("click", () => {
          if (currentPage > 1) {
            currentPage--;
            updatePagination();
          }
        });

        nextBtn.addEventListener("click", () => {
          const totalPages = getTotalPages();
          if (currentPage < totalPages) {
            currentPage++;
            updatePagination();
          }
        });

        // Initial pagination setup
        updatePagination();
      }

      function resetPagination() {
        currentPage = 1;
        updatePagination();
      }

      function updatePagination() {
        const isMobile = window.innerWidth <= 768;
        const prevBtn = document.getElementById("prevPage");
        const nextBtn = document.getElementById("nextPage");
        const pageInfo = document.getElementById("pageInfo");

        if (!prevBtn || !nextBtn || !pageInfo) return;

        if (isMobile) {
          // Mobile pagination
          const container = document.getElementById("mobileCardsContainer");
          if (!container) return;

          const cards = container.querySelectorAll(".school-card");

          // Store all cards in an array for pagination
          const allCards = Array.from(cards);

          // Get visible cards (for filtered results)
          const visibleCards = allCards.filter(
            (card) => card.getAttribute("data-visible") !== "false"
          );

          // Use all cards if no filtering is applied, otherwise use visible cards
          const cardsToUse = visibleCards.length > 0 ? visibleCards : allCards;

          const totalPages = Math.max(
            1,
            Math.ceil(cardsToUse.length / rowsPerPage)
          );

          // Adjust current page if needed
          if (currentPage > totalPages) {
            currentPage = totalPages;
          }

          // Hide all cards first
          allCards.forEach((card) => {
            card.style.display = "none";
          });

          // Show only cards for current page
          const start = (currentPage - 1) * rowsPerPage;
          const end = Math.min(start + rowsPerPage, cardsToUse.length);

          for (let i = start; i < end; i++) {
            if (cardsToUse[i]) {
              cardsToUse[i].style.display = "block";
            }
          }

          // Update page info
          pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

          // Enable/disable buttons
          prevBtn.disabled = currentPage === 1;
          nextBtn.disabled = currentPage === totalPages || totalPages === 0;
        } else {
          // Desktop pagination
          const rows = document.querySelectorAll("#schoolTableBody tr");
          const visibleRows = Array.from(rows).filter(
            (row) =>
              row.getAttribute("data-visible") !== "false" &&
              !row.querySelector("td[colspan]")
          );

          const totalPages = Math.max(
            1,
            Math.ceil(visibleRows.length / rowsPerPage)
          );

          // Adjust current page if it's beyond the total pages
          if (currentPage > totalPages) {
            currentPage = totalPages;
          }

          // Hide all rows first
          rows.forEach((row) => {
            if (!row.querySelector("td[colspan]")) {
              row.style.display = "none";
            }
          });

          // Calculate which visible rows to show
          const start = (currentPage - 1) * rowsPerPage;
          const end = Math.min(start + rowsPerPage, visibleRows.length);

          // Show only the visible rows for the current page
          for (let i = 0; i < visibleRows.length; i++) {
            if (i >= start && i < end) {
              visibleRows[i].style.display = "table-row";
            }
          }

          // Update page info
          pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

          // Enable/Disable buttons
          prevBtn.disabled = currentPage === 1;
          nextBtn.disabled = currentPage === totalPages || totalPages === 0;
        }
      }

      function getTotalPages() {
        const isMobile = window.innerWidth <= 768;

        if (isMobile) {
          const container = document.getElementById("mobileCardsContainer");
          if (!container) return 1;

          const cards = container.querySelectorAll(".school-card");
          const allCards = Array.from(cards);

          // Get visible cards (for filtered results)
          const visibleCards = allCards.filter(
            (card) => card.getAttribute("data-visible") !== "false"
          );

          // Use all cards if no filtering is applied, otherwise use visible cards
          const cardsToUse = visibleCards.length > 0 ? visibleCards : allCards;

          return Math.max(1, Math.ceil(cardsToUse.length / rowsPerPage));
        } else {
          const rows = document.querySelectorAll("#schoolTableBody tr");
          const visibleRows = Array.from(rows).filter(
            (row) =>
              row.getAttribute("data-visible") !== "false" &&
              !row.querySelector("td[colspan]")
          );
          return Math.max(1, Math.ceil(visibleRows.length / rowsPerPage));
        }
      }

      // Listen for window resize to handle view changes
      window.addEventListener("resize", function () {
        updatePagination();
      });

      // --- Export Functions ---
      function exportToExcel() {
        // Define all available columns
        const columns = [
          { id: "SchoolID", name: "School ID (SID)" },
          { id: "Schoolname", name: "School Name" },
          { id: "Institution", name: "Type" },
          { id: "population", name: "Enrollment" },
          { id: "district", name: "District" },
          { id: "principal", name: "Principal" },
          { id: "contact_no", name: "Contact Number" },
          { id: "email", name: "Email" },
          { id: "principal_age", name: "Age" },
        ];

        // Create HTML for column selection
        let columnSelectionHtml = '<div class="text-start">';
        columns.forEach((column) => {
          columnSelectionHtml += `
            <div class="form-check mb-2">
              <input class="form-check-input" type="checkbox" id="check_${column.id}" value="${column.id}" checked>
              <label class="form-check-label" for="check_${column.id}">
                ${column.name}
              </label>
            </div>
          `;
        });
        columnSelectionHtml += "</div>";

        // Show SweetAlert with column selection
        Swal.fire({
          title: "Export Data",
          html: `
            <p>Select columns to include in the export:</p>
            ${columnSelectionHtml}
            <div class="d-flex justify-content-between mt-3">
              <button id="selectAllBtn" class="btn btn-sm btn-outline-primary">Select All</button>
              <button id="deselectAllBtn" class="btn btn-sm btn-outline-secondary">Deselect All</button>
            </div>
          `,
          showCancelButton: true,
          confirmButtonText: "Export",
          confirmButtonColor: "#4caf50",
          width: "500px",
          didOpen: () => {
            // Add event listeners for select/deselect all buttons
            document.getElementById("selectAllBtn").addEventListener("click", () => {
              document.querySelectorAll(".form-check-input").forEach((checkbox) => {
                checkbox.checked = true;
              });
            });

            document.getElementById("deselectAllBtn").addEventListener("click", () => {
              document.querySelectorAll(".form-check-input").forEach((checkbox) => {
                checkbox.checked = false;
              });
            });
          },
        }).then((result) => {
          if (result.isConfirmed) {
            // Get selected columns
            const selectedColumns = [];
            columns.forEach((column) => {
              if (document.getElementById(`check_${column.id}`).checked) {
                selectedColumns.push(column.id);
              }
            });

            if (selectedColumns.length === 0) {
              Swal.fire({
                icon: "warning",
                title: "No Columns Selected",
                text: "Please select at least one column to export.",
                confirmButtonColor: "#4caf50",
              });
              return;
            }

            // Show loading indicator
            Swal.fire({
              title: "Exporting data...",
              text: "Please wait while we prepare your export",
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              },
            });

            // Generate and download CSV with only selected columns
            generateCSV(schoolsData, selectedColumns);

            // Show success message
            Swal.fire({
              title: "Export Successful",
              text: `Exported ${schoolsData.length} records to CSV`,
              icon: "success",
              timer: 2000,
              showConfirmButton: false,
            });
          }
        });
      }

      function generateCSV(data, selectedColumns) {
        if (!data || data.length === 0) {
          console.error("No data to export");
          return;
        }

        // Create CSV content with only selected headers
        let csvContent = selectedColumns.join(",") + "\n";

        // Add data rows with only selected columns
        data.forEach((item) => {
          const row = selectedColumns.map((header) => {
            // Handle values that might contain commas or quotes
            let value = item[header] || "";
            value = String(value).replace(/"/g, '""'); // Escape double quotes

            // Wrap in quotes if the value contains commas, quotes, or newlines
            if (value.includes(",") || value.includes('"') || value.includes("\n")) {
              value = `"${value}"`;
            }
            return value;
          });

          csvContent += row.join(",") + "\n";
        });

        // Create a Blob with the CSV content
        const blob = new Blob([csvContent], {
          type: "text/csv;charset=utf-8;",
        });

        // Create a download link and trigger the download
        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);

        // Set file name with current date
        const date = new Date();
        const fileName = `school_data_${date.getFullYear()}-${(date.getMonth() + 1)
          .toString()
          .padStart(2, "0")}-${date.getDate().toString().padStart(2, "0")}.csv`;

        link.setAttribute("href", url);
        link.setAttribute("download", fileName);
        link.style.display = "none";

        document.body.appendChild(link);
        link.click();

        // Clean up
        document.body.removeChild(link);
        URL.revokeObjectURL(url);
      }

      // --- Enrollment Functions ---
      function initializeEnrollmentLinks() {
        // Make enrollment cells clickable in desktop view
        const enrollmentCells = document.querySelectorAll('.enrollment-link');
        enrollmentCells.forEach(cell => {
          cell.addEventListener("click", function(e) {
            e.stopPropagation(); // Prevent row selection when clicking enrollment
            
            // Get the school data
            let schoolIndex;
            if (this.closest("tr")) {
              // Desktop view
              schoolIndex = this.closest("tr").getAttribute("data-row-id");
            } else if (this.closest(".school-card")) {
              // Mobile view
              schoolIndex = this.closest(".school-card").getAttribute("data-row-id");
            }
            
            if (schoolIndex !== undefined) {
              const school = schoolsData[schoolIndex];
              if (school) {
                showEnrollmentPopup(school);
              }
            }
          });
        });
      }

      // Show the enrollment popup with data for the selected school
      function showEnrollmentPopup(school) {
        const popup = document.getElementById("enrollmentPopup");
        const popupBody = document.getElementById("enrollmentPopupBody");
        const popupTitle = document.querySelector(".enrollment-popup-title");

        // Set the title
        popupTitle.textContent = `Enrollment Details - ${school.Schoolname}`;
        
        // Show loading indicator
        popupBody.innerHTML = `
          <div class="text-center py-4">
            <div class="spinner-border text-success" role="status">
              <span class="visually-hidden">Loading enrollment data...</span>
            </div>
            <p class="mt-2">Loading enrollment data...</p>
          </div>
        `;
        
        // Show the popup
        popup.style.display = "flex";
        
        // Prevent scrolling of the background
        document.body.style.overflow = "hidden";
        
        // Fetch real enrollment data from PHP script
        fetchEnrollmentData(school.SchoolID)
          .then(enrollmentData => {
            // Update popup with real data
            updateEnrollmentPopupContent(school, enrollmentData);
          })
          .catch(error => {
            console.error("Error fetching enrollment data:", error);
            // Fallback to mock data if fetch fails
            const mockEnrollmentData = generateEnrollmentData(parseInt(school.total_enrollees) || 0);
            updateEnrollmentPopupContent(school, mockEnrollmentData);
          });
      }
      
      // Fetch enrollment data from PHP script
      function fetchEnrollmentData(schoolId) {
        return new Promise((resolve, reject) => {
          // Create URL with school_id parameter
          const url = `phpp/dashboard/fetchSchoolInfo.php?school_id=${encodeURIComponent(schoolId)}`;
          
          // Fetch data from PHP script
          fetch(url)
            .then(response => {
              if (!response.ok) {
                throw new Error('Network response was not ok');
              }
              return response.json();
            })
            .then(data => {
              if (data.error) {
                throw new Error(data.error);
              }
              
              // Process the enrollment data from PHP
              const processedData = processEnrollmentData(data);
              resolve(processedData);
            })
            .catch(error => {
              console.error('Error fetching enrollment data:', error);
              reject(error);
            });
        });
      }
      
      // Process the enrollment data from PHP to match our format
      function processEnrollmentData(data) {
        // This function transforms the PHP data into the format our UI expects
        const enrollmentData = [];
        
        // Check if we have a valid data object
        if (!data || Object.keys(data).length === 0) {
          console.warn('No enrollment data found or invalid format');
          return enrollmentData;
        }
        
        // Process elementary grades
        if (data.K_M !== undefined || data.K_F !== undefined) {
          enrollmentData.push({
            level: 'Kindergarten',
            male: parseInt(data.K_M) || 0,
            female: parseInt(data.K_F) || 0,
            total: (parseInt(data.K_M) || 0) + (parseInt(data.K_F) || 0)
          });
        }
        
        // Process grades 1-10
        for (let i = 1; i <= 10; i++) {
          const maleKey = `G${i}_M`;
          const femaleKey = `G${i}_F`;
          
          if (data[maleKey] !== undefined || data[femaleKey] !== undefined) {
            const male = parseInt(data[maleKey]) || 0;
            const female = parseInt(data[femaleKey]) || 0;
            
            enrollmentData.push({
              level: `Grade ${i}`,
              male: male,
              female: female,
              total: male + female
            });
          }
        }
        
        // Process senior high school grades (11-12)
        // For Grade 11
        const g11Tracks = ['ACAD', 'TVL', 'SPORT', 'ART', 'UNQ'];
        let g11MaleTotal = 0;
        let g11FemaleTotal = 0;
        
        g11Tracks.forEach(track => {
          const fsMaleKey = `FS_G11_${track}_M`;
          const fsFemaleKey = `FS_G11_${track}_F`;
          const ssMaleKey = `SS_G11_${track}_M`;
          const ssFemaleKey = `SS_G11_${track}_F`;
          
          g11MaleTotal += (parseInt(data[fsMaleKey]) || 0) + (parseInt(data[ssMaleKey]) || 0);
          g11FemaleTotal += (parseInt(data[fsFemaleKey]) || 0) + (parseInt(data[ssFemaleKey]) || 0);
        });
        
        if (g11MaleTotal > 0 || g11FemaleTotal > 0) {
          enrollmentData.push({
            level: 'Grade 11',
            male: g11MaleTotal,
            female: g11FemaleTotal,
            total: g11MaleTotal + g11FemaleTotal
          });
        }
        
        // For Grade 12
        const g12Tracks = ['ACAD', 'TVL', 'SPORT', 'ART', 'UNQ'];
        let g12MaleTotal = 0;
        let g12FemaleTotal = 0;
        
        g12Tracks.forEach(track => {
          const fsMaleKey = `FS_G12_${track}_M`;
          const fsFemaleKey = `FS_G12_${track}_F`;
          const ssMaleKey = `SS_G12_${track}_M`;
          const ssFemaleKey = `SS_G12_${track}_F`;
          
          g12MaleTotal += (parseInt(data[fsMaleKey]) || 0) + (parseInt(data[ssMaleKey]) || 0);
          g12FemaleTotal += (parseInt(data[fsFemaleKey]) || 0) + (parseInt(data[ssFemaleKey]) || 0);
        });
        
        if (g12MaleTotal > 0 || g12FemaleTotal > 0) {
          enrollmentData.push({
            level: 'Grade 12',
            male: g12MaleTotal,
            female: g12FemaleTotal,
            total: g12MaleTotal + g12FemaleTotal
          });
        }
        
        return enrollmentData;
      }
      
      // Update the enrollment popup with data
      function updateEnrollmentPopupContent(school, enrollmentData) {
        const popupBody = document.getElementById("enrollmentPopupBody");
        
        // Calculate totals
        let totalMale = 0;
        let totalFemale = 0;
        let total_enrollees = 0;
        
        enrollmentData.forEach(grade => {
          totalMale += grade.male;
          totalFemale += grade.female;
          total_enrollees += grade.total;
        });
        
        const malePercentage = total_enrollees > 0 ? Math.round((totalMale / total_enrollees) * 100) : 0;
        const femalePercentage = total_enrollees > 0 ? Math.round((totalFemale / total_enrollees) * 100) : 0;

        // Create the content with the requested layout changes
        let content = `
        <div class="enrollment-summary">
          <h5><i class="bi bi-info-circle-fill me-2"></i>School Enrollment Summary</h5>
          <div class="row">
            <div class="col-md-6">
              <p><strong>School Type:</strong> ${school.Institution || "N/A"}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Total Enrollment:</strong> <span class="fs-4 text-success">${total_enrollees.toLocaleString()}</span> students</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <p><strong>District:</strong> ${school.district || "N/A"}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Principal:</strong> ${school.principal || "N/A"}</p>
            </div>
          </div>
        </div>
        
        <div class="gender-distribution">
          <h6><i class="bi bi-gender-ambiguous me-2"></i>Gender Distribution</h6>
          <div class="progress-container">
            <div class="progress-bar-male" style="width: ${malePercentage}%">
              ${malePercentage}% Male
            </div>
            <div class="progress-bar-female" style="width: ${femalePercentage}%">
              ${femalePercentage}% Female
            </div>
          </div>
          <div class="gender-legend">
            <div class="legend-item">
              <div class="legend-color legend-male"></div>
              <span>Male: ${totalMale.toLocaleString()} students</span>
            </div>
            <div class="legend-item">
              <div class="legend-color legend-female"></div>
              <span>Female: ${totalFemale.toLocaleString()} students</span>
            </div>
          </div>
        </div>
        
        <h6 class="mt-4 mb-3"><i class="bi bi-bar-chart-fill me-2"></i>Enrollment by Grade Level</h6>
        <div class="table-responsive">
          <table class="enrollment-table">
            <thead> 
              <tr>
                <th>Grade Level</th>
                <th>Male</th>
                <th>Female</th>
                <th>Total</th>
                <th>Percentage</th>
              </tr>
            </thead>
            <tbody>
        `;

        // Add rows for each grade level
        enrollmentData.forEach((grade) => {
          const percentage = total_enrollees > 0 ? ((grade.total / total_enrollees) * 100).toFixed(1) : "0.0";
          content += `
          <tr>
            <td>${grade.level}</td>
            <td>${grade.male.toLocaleString()}</td>
            <td>${grade.female.toLocaleString()}</td>
            <td>${grade.total.toLocaleString()}</td>
            <td>${percentage}% <span class="badge-percentage">${percentage}%</span></td>
          </tr>
        `;
        });

        // Add total row
        content += `
          <tr class="total-row">
            <td>Total</td>
            <td>${totalMale.toLocaleString()}</td>
            <td>${totalFemale.toLocaleString()}</td>
            <td>${total_enrollees.toLocaleString()}</td>
            <td>100% <span class="badge-percentage">100%</span></td>
          </tr>
        `;

        content += `
            </tbody>
          </table>
        </div>
        `;

        // Set the content
        popupBody.innerHTML = content;
      }

      // Close the enrollment popup
      function closeEnrollmentPopup() {
        const popup = document.getElementById("enrollmentPopup");
        popup.style.display = "none";

        // Re-enable scrolling
        document.body.style.overflow = "";
      }

      // Generate mock enrollment data based on total population
      function generateEnrollmentData(totalPopulation) {
        // For schools with different grade levels
        let gradeLevels = [];

        if (totalPopulation <= 0) {
          return gradeLevels;
        }

        // Determine if elementary, high school, or K-12 based on population
        const isElementary = totalPopulation < 1000;
        const isHighSchool = totalPopulation >= 1000 && totalPopulation < 2000;
        const isK12 = totalPopulation >= 2000;

        // Create grade levels based on school type
        if (isElementary) {
          // Elementary school (K to 6)
          gradeLevels = [
            { level: "Kindergarten" },
            { level: "Grade 1" },
            { level: "Grade 2" },
            { level: "Grade 3" },
            { level: "Grade 4" },
            { level: "Grade 5" },
            { level: "Grade 6" },
          ];
        } else if (isHighSchool) {
          // High school (7 to 12)
          gradeLevels = [
            { level: "Grade 7" },
            { level: "Grade 8" },
            { level: "Grade 9" },
            { level: "Grade 10" },
            { level: "Grade 11" },
            { level: "Grade 12" },
          ];
        } else if (isK12) {
          // K-12 school
          gradeLevels = [
            { level: "Kindergarten" },
            { level: "Grade 1" },
            { level: "Grade 2" },
            { level: "Grade 3" },
            { level: "Grade 4" },
            { level: "Grade 5" },
            { level: "Grade 6" },
            { level: "Grade 7" },
            { level: "Grade 8" },
            { level: "Grade 9" },
            { level: "Grade 10" },
            { level: "Grade 11" },
            { level: "Grade 12" },
          ];
        }

        // Distribute population across grade levels
        const totalLevels = gradeLevels.length;
        let remainingPopulation = totalPopulation;

        gradeLevels.forEach((grade, index) => {
          // Distribute population with some randomness
          // Last grade gets all remaining students to ensure total matches
          let gradeTotal;
          if (index === totalLevels - 1) {
            gradeTotal = remainingPopulation;
          } else {
            // Random percentage between 10% and 20% of remaining population
            const percentage = 0.1 + Math.random() * 0.1;
            gradeTotal = Math.floor(remainingPopulation * percentage);
            remainingPopulation -= gradeTotal;
          }

          // Split between male and female (roughly 50/50 with slight variation)
          const malePercentage = 0.45 + Math.random() * 0.1; // 45% to 55%
          const male = Math.floor(gradeTotal * malePercentage);
          const female = gradeTotal - male;

          // Add to grade level data
          grade.male = male;
          grade.female = female;
          grade.total = gradeTotal;
        });

        return gradeLevels;
      }

      // Close popup when clicking outside the content
      document
        .getElementById("enrollmentPopup")
        .addEventListener("click", function (e) {
          if (e.target === this) {
            closeEnrollmentPopup();
          }
        });

      // Close popup when pressing Escape key
      document.addEventListener("keydown", function (e) {
        if (
          e.key === "Escape" &&
          document.getElementById("enrollmentPopup").style.display === "flex"
        ) {
          closeEnrollmentPopup();
        }
      });

      // Clear selection when clicking outside of rows/cards
      document.addEventListener("click", function (event) {
        const isTableRow = event.target.closest("#schoolTableBody tr");
        const isCard = event.target.closest(".school-card");
        const isUpdateButton = event.target.closest("#updateButton");
        const isModal = event.target.closest(".modal");
        const isFilterPanel = event.target.closest(".filter-panel");
        const isFilterButton = event.target.closest("#filterButton");

        if (!isTableRow && !isCard && !isUpdateButton && !isModal && !isFilterPanel && !isFilterButton) {
          // Clear selection from all rows
          document.querySelectorAll("#schoolTableBody tr").forEach((r) => {
            r.classList.remove("table-primary");
          });

          // Clear selection from all cards
          document.querySelectorAll(".school-card").forEach((c) => {
            c.classList.remove("border-primary");
            c.style.boxShadow = "";
          });

          // Disable update button
          const updateButton = document.getElementById("updateButton");
          if (updateButton) {
            updateButton.classList.add("btn-update-disabled");
            updateButton.disabled = true;
          }

          // Clear selected school
          selectedSchoolIndex = -1;
          selectedSchool = null;
        }
      });
    </script>
  </body>
</html>

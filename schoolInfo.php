<?php
// Prevent browser caching of authenticated pages
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
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
        overflow-y: auto;
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
        display: inline;
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

      #school-map-container {
        height: 300px;
        border-radius: 8px;
        overflow: hidden;
        border: 1px solid var(--border-color);
        margin-bottom: 1rem;
      }

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

      .btn-update-disabled {
        opacity: 0.6;
        cursor: not-allowed;
        pointer-events: none;
      }

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

      .dropdown-loading {
        display: flex;
        align-items: center;
        justify-content: center;
        height: 38px;
        background-color: #f8f9fa;
        border-radius: 8px;
        margin-bottom: 1.25rem;
      }

      .dropdown-loading .spinner-border {
        width: 1.2rem;
        height: 1.2rem;
        margin-right: 0.5rem;
      }

      /* Location info styles */
      .location-info {
        background-color: #f8f9fa;
        border-radius: 8px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid var(--border-color);
      }

      .location-info h6 {
        color: var(--success-color);
        margin-bottom: 10px;
        font-weight: 600;
      }

      .location-info p {
        margin-bottom: 5px;
        font-size: 0.9rem;
      }

      .location-info strong {
        color: var(--dark-color);
      }

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
                  <label for="typeCheck">Institution Type</label>
                </div>
                <div>
                  <input type="checkbox" value="3" id="curricularCheck" />
                  <label for="curricularCheck">Curricular Offer</label>
                </div>
                <div>
                  <input type="checkbox" value="4" id="popCheck" />
                  <label for="popCheck">Enrollment</label>
                </div>
                <div>
                  <input type="checkbox" value="5" id="districtCheck" />
                  <label for="districtCheck">District</label>
                </div>
                <div>
                  <input type="checkbox" value="6" id="principalCheck" />
                  <label for="principalCheck">Principal</label>
                </div>
                <div>
                  <input type="checkbox" value="7" id="CNumCheck" />
                  <label for="CNumCheck">Contact Number</label>
                </div>
                <div>
                  <input type="checkbox" value="8" id="emailCheck" />
                  <label for="emailCheck">Email</label>
                </div>
                <div>
                  <input type="checkbox" value="9" id="ageCheck" />
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
            <button class="btn btn-warning dropdown-toggle" type="button" id="actionButton" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-gear"></i>
              Action
            </button>
            <ul class="dropdown-menu" aria-labelledby="actionButton">
              <li><button class="dropdown-item" onclick="importData()"><i class="bi bi-upload"></i> Import</button></li>
              <li><button class="dropdown-item" onclick="exportToExcel()"><i class="bi bi-file-earmark-excel"></i> Export</button></li>
            </ul>
          </div>
        </div>

        <!-- Schools Table -->
        <div class="table-responsive">
          <table class="table table-striped">
            <thead>
              <tr>
                <th>SID</th>
                <th>School Name</th>
                <th>Institution Type</th>
                <!-- <th>Curricular Offer</th> -->
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
                <td colspan="10" class="text-center">
                  <div class="spinner-border" role="status">
                    <span class="visually-hidden">Loading...</span>
                  </div>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination -->
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

    <!-- School Modal -->
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
                      <select class="form-select" id="institutionType" required>
                        <option value="" selected disabled>Select Institution Type</option>
                        <option value="Public">Public</option>
                        <option value="Private">Private</option>
                        <option value="SUC">SUC (State Universities and Colleges)</option>
                      </select>
                      <label for="institutionType">Institution Type</label>
                    </div>

                    <div class="form-floating mb-3">
                      <select class="form-select" id="curricularOffer" required>
                        <option value="" selected disabled>Select Curricular Offer</option>
                        <option value="Kinder, Grade 1-6">Kinder & Grade 1-6</option>
                        <option value="Grade 7-10">Grade 7-10</option>
                        <option value="Grade 11-12">Grade 11-12</option>
                        <option value="Grade 1-6, Grade 7-10">Grade 1-6, Grade 7-10</option>
                        <option value="Grade 7-10, Grade 11-12">Grade 7-10, Grade 11-12</option>
                        <option value="Kinder, Grade 1-6, Grade 7-10">Kinder & Grade 1-6, Grade 7-10</option>
                        <option value="Grade 1-6, Grade 7-10, Grade 11-12">Grade 1-6, Grade 7-10, Grade 11-12</option>
                        <option value="Kinder, Grade 1-6, Grade 7-10, Grade 11-12">Kinder & Grade 1-6, Grade 7-10, Grade 11-12</option>
                      </select>
                      <label for="curricularOffer">Curricular Offer</label>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="number"
                        class="form-control"
                        id="schoolPopulation"
                        placeholder="Enrollment"
                        required
                      />
                      <label for="schoolPopulation">Total Enrollment</label>
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
                      <label for="principalAge">Principal Age</label>
                    </div>
                  </div>
                </div>

                <!-- Right Column - Address and Map -->
                <div class="col-md-6">
                  <!-- Location Information Display -->
                  <div class="location-info" id="locationInfo" style="display: none;">
                    <h6><i class="bi bi-geo-alt-fill"></i> Current Location Information</h6>
                    <p><strong>Region:</strong> <span id="currentRegion">-</span></p>
                    <p><strong>Province:</strong> <span id="currentProvince">-</span></p>
                    <p><strong>City/Municipality:</strong> <span id="currentCity">-</span></p>
                    <p><strong>Barangay:</strong> <span id="currentBarangay">-</span></p>
                    <p><strong>Landmark:</strong> <span id="currentLandmark">-</span></p>
                    <p><strong>Coordinates:</strong> <span id="currentCoordinates">-</span></p>
                  </div>

                  <div class="form-card">
                    <div class="form-card-header">
                      <h6 class="form-card-title">
                        <i class="bi bi-geo-alt"></i> Address Information
                      </h6>
                    </div>

                    <div class="form-floating mb-3">
                      <select
                        class="form-select"
                        id="regionSelect"
                        required
                        onchange="loadProvinces()"
                      >
                        <option value="" selected disabled>Select Region</option>
                      </select>
                      <label for="regionSelect">Region</label>
                    </div>

                    <div class="form-floating mb-3">
                      <select
                        class="form-select"
                        id="provinceSelect"
                        required
                        onchange="loadCities()"
                        disabled
                      >
                        <option value="" selected disabled>Select Province</option>
                      </select>
                      <label for="provinceSelect">Province</label>
                    </div>

                    <div class="form-floating mb-3">
                      <select
                        class="form-select"
                        id="citySelect"
                        required
                        onchange="loadBarangays()"
                        disabled
                      >
                        <option value="" selected disabled>Select City/Municipality</option>
                      </select>
                      <label for="citySelect">City/Municipality</label>
                    </div>

                    <div class="form-floating mb-3">
                      <select
                        class="form-select"
                        id="barangaySelect"
                        required
                        disabled
                      >
                        <option value="" selected disabled>Select Barangay</option>
                      </select>
                      <label for="barangaySelect">Barangay</label>
                    </div>

                    <div class="form-floating mb-3">
                      <input
                        type="text"
                        class="form-control"
                        id="landMark"
                        placeholder="Landmark"
                        required
                      />
                      <label for="landMark">Landmark</label>
                    </div>
                  </div>

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
      // Global Variables
      let schoolMap = null;
      let schoolMarker = null;
      let defaultLat = 14.5995;
      let defaultLng = 120.9842;
      let originalLat = null; // Store original coordinates from database
      let originalLng = null;
      let schoolsData = [];
      let selectedSchoolIndex = -1;
      let selectedSchool = null;
      
      // Address data
      let regions = [];
      let provinces = {};
      let cities = {};
      let barangays = {};

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
        
        // Load regions when modal is shown
        document.getElementById("schoolModal").addEventListener("shown.bs.modal", loadRegions);
      });

      // Component Loader
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
            if (componentName === "sidebar.php") {
              createFallbackSidebar(container);
            } else if (componentName === "topbar.php") {
              createFallbackTopbar(container);
            }
          });
      }
      
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

      function fetchData() {
        fetch("phpp/school_info/fetchSchoolInfo.php", { method: "GET" })
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
            initializePagination();
            initializeEnrollmentLinks();
          })
          .catch((error) => {
            console.error("Error fetching data:", error);
            useSampleData();
          });
      }

      function useSampleData() {
        // Sample data for demonstration
        schoolsData = [
          {
            SchoolID: "123456",
            Schoolname: "Sample Elementary School",
            Institution: "Public",
            curricularOffer: "Kinder, Grade 1-6",
            total_enrollees: "450",
            district: "District I",
            principal: "John Doe",
            contact_no: "09123456789",
            email: "sample@deped.gov.ph",
            principal_age: "45"
          }
        ];
        populateTable(schoolsData);
        initializePagination();
        initializeEnrollmentLinks();
      }

      function populateTable(data) {
        const tbody = document.getElementById("schoolTableBody");
        if (!tbody) return;

        tbody.innerHTML = "";

        if (data.length === 0) {
          tbody.innerHTML =
            '<tr><td colspan="10" class="text-center">No data available</td></tr>';
          return;
        }

        const headers = [
          "SID",
          "School Name",
          "Institution Type",
          "Curricular Offer",
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
          <td data-label="${headers[4]}" class="enrollment-cell"><a class="enrollment-link">${
            school.total_enrollees || "N/A"
          }</a></td>
          <td data-label="${headers[5]}">${school.district || "N/A"}</td>
          <td data-label="${headers[6]}">${school.principal || "N/A"}</td>
          <td data-label="${headers[7]}">${school.contact_no || "N/A"}</td>
          <td data-label="${headers[8]}">${school.email || "N/A"}</td>
          <td data-label="${headers[9]}">${school.principal_age || "N/A"}</td>
        `;

          row.addEventListener("click", function (e) {
            if (e.target.classList.contains("enrollment-link") || e.target.closest(".enrollment-link")) {
              return;
            }

            document.querySelectorAll("#schoolTableBody tr").forEach((r) => {
              r.classList.remove("table-primary");
            });

            this.classList.add("table-primary");

            selectedSchoolIndex = index;
            selectedSchool = schoolsData[index];

            const updateButton = document.getElementById("updateButton");
            updateButton.classList.remove("btn-update-disabled");
            updateButton.disabled = false;
          });

          tbody.appendChild(row);
        });
      }

      function initializeMap() {
        const mapContainer = document.getElementById("school-map-container");
        if (!mapContainer) return;

        const lat = parseFloat(document.getElementById("latitude").value) || defaultLat;
        const lng = parseFloat(document.getElementById("longitude").value) || defaultLng;

        if (schoolMap) {
          schoolMap.remove();
          schoolMap = null;
        }

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

        setTimeout(() => {
          if (schoolMap) schoolMap.invalidateSize();
        }, 100);
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
        // Use original coordinates from database if in update mode, otherwise use default
        const resetLat = originalLat !== null ? originalLat : defaultLat;
        const resetLng = originalLng !== null ? originalLng : defaultLng;
        
        document.getElementById("latitude").value = resetLat.toFixed(7);
        document.getElementById("longitude").value = resetLng.toFixed(7);
        
        if (schoolMarker && schoolMap) {
          const latLng = L.latLng(resetLat, resetLng);
          schoolMarker.setLatLng(latLng);
          schoolMap.panTo(latLng);
          schoolMap.setZoom(13);
        }
        
        // Show appropriate message
        const message = originalLat !== null ? "Location reset to original database coordinates" : "Location reset to default coordinates";
        Swal.fire({ toast: true, position: "top-end", icon: "info", title: message, showConfirmButton: false, timer: 1500 });
      }

      // Address Functions
      function loadRegions() {
        const regionSelect = document.getElementById("regionSelect");
        
        while (regionSelect.options.length > 1) {
          regionSelect.remove(1);
        }
        
        regionSelect.disabled = true;
        
        fetch("phpp/school_info/getRegions.php")
          .then(response => {
            if (!response.ok) {
              throw new Error("Failed to fetch regions");
            }
            return response.json();
          })
          .then(data => {
            regions = data;
            
            regions.forEach(region => {
              const option = document.createElement("option");
              option.value = region.id;
              option.textContent = region.name;
              regionSelect.appendChild(option);
            });
            
            regionSelect.disabled = false;
            
            if (selectedSchool && selectedSchool.region_id) {
              regionSelect.value = selectedSchool.region_id;
              loadProvinces();
            }
          })
          .catch(error => {
            console.error("Error loading regions:", error);
            useMockRegions();
          });
      }
      
      function useMockRegions() {
        const regionSelect = document.getElementById("regionSelect");
        
        regions = [
          { id: "01", name: "Region I - Ilocos Region" },
          { id: "02", name: "Region II - Cagayan Valley" },
          { id: "03", name: "Region III - Central Luzon" },
          { id: "04", name: "Region IV-A - CALABARZON" },
          { id: "05", name: "Region V - Bicol Region" },
          { id: "06", name: "Region VI - Western Visayas" },
          { id: "07", name: "Region VII - Central Visayas" },
          { id: "08", name: "Region VIII - Eastern Visayas" },
          { id: "09", name: "Region IX - Zamboanga Peninsula" },
          { id: "10", name: "Region X - Northern Mindanao" },
          { id: "11", name: "Region XI - Davao Region" },
          { id: "12", name: "Region XII - SOCCSKSARGEN" },
          { id: "13", name: "Region XIII - Caraga" },
          { id: "NCR", name: "National Capital Region" },
          { id: "CAR", name: "Cordillera Administrative Region" },
          { id: "BARMM", name: "Bangsamoro Autonomous Region in Muslim Mindanao" }
        ];
        
        regions.forEach(region => {
          const option = document.createElement("option");
          option.value = region.id;
          option.textContent = region.name;
          regionSelect.appendChild(option);
        });
        
        regionSelect.disabled = false;
        
        if (selectedSchool && selectedSchool.region_id) {
          regionSelect.value = selectedSchool.region_id;
          loadProvinces();
        }
      }
      
      function loadProvinces() {
        const regionSelect = document.getElementById("regionSelect");
        const provinceSelect = document.getElementById("provinceSelect");
        const citySelect = document.getElementById("citySelect");
        const barangaySelect = document.getElementById("barangaySelect");
        
        provinceSelect.innerHTML = '<option value="" selected disabled>Select Province</option>';
        citySelect.innerHTML = '<option value="" selected disabled>Select City/Municipality</option>';
        barangaySelect.innerHTML = '<option value="" selected disabled>Select Barangay</option>';
        
        provinceSelect.disabled = true;
        citySelect.disabled = true;
        barangaySelect.disabled = true;
        
        const selectedRegionId = regionSelect.value;
        if (!selectedRegionId) return;
        
        provinceSelect.innerHTML = '<option value="" selected disabled>Loading provinces...</option>';
        
        fetch(`phpp/school_info/getProvinces.php?region_id=${selectedRegionId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error("Failed to fetch provinces");
            }
            return response.json();
          })
          .then(data => {
            provinces[selectedRegionId] = data;
            
            provinceSelect.innerHTML = '<option value="" selected disabled>Select Province</option>';
            
            provinces[selectedRegionId].forEach(province => {
              const option = document.createElement("option");
              option.value = province.id;
              option.textContent = province.name;
              provinceSelect.appendChild(option);
            });
            
            provinceSelect.disabled = false;
            
            if (selectedSchool && selectedSchool.province_id) {
              provinceSelect.value = selectedSchool.province_id;
              loadCities();
            }
          })
          .catch(error => {
            console.error("Error loading provinces:", error);
            useMockProvinces(selectedRegionId);
          });
      }
      
      function useMockProvinces(regionId) {
        const provinceSelect = document.getElementById("provinceSelect");
        
        let mockProvinces = [];
        
        if (regionId === "NCR") {
          mockProvinces = [
            { id: "NCR00", name: "National Capital Region" }
          ];
        } else if (regionId === "04") {
          mockProvinces = [
            { id: "0421", name: "Batangas" },
            { id: "0434", name: "Cavite" },
            { id: "0458", name: "Laguna" },
            { id: "0471", name: "Quezon" },
            { id: "0456", name: "Rizal" }
          ];
        } else {
          for (let i = 1; i <= 5; i++) {
            mockProvinces.push({
              id: `${regionId}${i.toString().padStart(2, '0')}`,
              name: `Province ${i}`
            });
          }
        }
        
        provinces[regionId] = mockProvinces;
        
        provinceSelect.innerHTML = '<option value="" selected disabled>Select Province</option>';
        
        mockProvinces.forEach(province => {
          const option = document.createElement("option");
          option.value = province.id;
          option.textContent = province.name;
          provinceSelect.appendChild(option);
        });
        
        provinceSelect.disabled = false;
        
        if (selectedSchool && selectedSchool.province_id) {
          provinceSelect.value = selectedSchool.province_id;
          loadCities();
        }
      }
      
      function loadCities() {
        const provinceSelect = document.getElementById("provinceSelect");
        const citySelect = document.getElementById("citySelect");
        const barangaySelect = document.getElementById("barangaySelect");
        
        citySelect.innerHTML = '<option value="" selected disabled>Select City/Municipality</option>';
        barangaySelect.innerHTML = '<option value="" selected disabled>Select Barangay</option>';
        
        citySelect.disabled = true;
        barangaySelect.disabled = true;
        
        const selectedProvinceId = provinceSelect.value;
        if (!selectedProvinceId) return;
        
        citySelect.innerHTML = '<option value="" selected disabled>Loading cities...</option>';
        
        fetch(`phpp/school_info/getCities.php?province_id=${selectedProvinceId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error("Failed to fetch cities");
            }
            return response.json();
          })
          .then(data => {
            cities[selectedProvinceId] = data;
            
            citySelect.innerHTML = '<option value="" selected disabled>Select City/Municipality</option>';
            
            cities[selectedProvinceId].forEach(city => {
              const option = document.createElement("option");
              option.value = city.id;
              option.textContent = city.name;
              citySelect.appendChild(option);
            });
            
            citySelect.disabled = false;
            
            if (selectedSchool && selectedSchool.city_id) {
              citySelect.value = selectedSchool.city_id;
              loadBarangays();
            }
          })
          .catch(error => {
            console.error("Error loading cities:", error);
            useMockCities(selectedProvinceId);
          });
      }
      
      function useMockCities(provinceId) {
        const citySelect = document.getElementById("citySelect");
        
        let mockCities = [];
        
        if (provinceId === "NCR00") {
          mockCities = [
            { id: "NCR01", name: "Manila" },
            { id: "NCR02", name: "Quezon City" },
            { id: "NCR03", name: "Caloocan" },
            { id: "NCR04", name: "Makati" },
            { id: "NCR05", name: "Pasig" },
            { id: "NCR06", name: "Taguig" },
            { id: "NCR07", name: "Parañaque" },
            { id: "NCR08", name: "Pasay" },
            { id: "NCR09", name: "Mandaluyong" },
            { id: "NCR10", name: "Marikina" }
          ];
        } else if (provinceId === "0421") {
          mockCities = [
            { id: "042101", name: "Batangas City" },
            { id: "042102", name: "Lipa" },
            { id: "042103", name: "Tanauan" },
            { id: "042104", name: "Santo Tomas" },
            { id: "042105", name: "Calaca" }
          ];
        } else {
          for (let i = 1; i <= 10; i++) {
            mockCities.push({
              id: `${provinceId}${i.toString().padStart(2, '0')}`,
              name: `City ${i}`
            });
          }
        }
        
        cities[provinceId] = mockCities;
        
        citySelect.innerHTML = '<option value="" selected disabled>Select City/Municipality</option>';
        
        mockCities.forEach(city => {
          const option = document.createElement("option");
          option.value = city.id;
          option.textContent = city.name;
          citySelect.appendChild(option);
        });
        
        citySelect.disabled = false;
        
        if (selectedSchool && selectedSchool.city_id) {
          citySelect.value = selectedSchool.city_id;
          loadBarangays();
        }
      }
      
      function loadBarangays() {
        const citySelect = document.getElementById("citySelect");
        const barangaySelect = document.getElementById("barangaySelect");
        
        barangaySelect.innerHTML = '<option value="" selected disabled>Select Barangay</option>';
        barangaySelect.disabled = true;
        
        const selectedCityId = citySelect.value;
        if (!selectedCityId) return;
        
        barangaySelect.innerHTML = '<option value="" selected disabled>Loading barangays...</option>';
        
        fetch(`phpp/school_info/getBarangays.php?city_id=${selectedCityId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error("Failed to fetch barangays");
            }
            return response.json();
          })
          .then(data => {
            barangays[selectedCityId] = data;
            
            barangaySelect.innerHTML = '<option value="" selected disabled>Select Barangay</option>';
            
            barangays[selectedCityId].forEach(barangay => {
              const option = document.createElement("option");
              option.value = barangay.id;
              option.textContent = barangay.name;
              barangaySelect.appendChild(option);
            });
            
            barangaySelect.disabled = false;
            
            if (selectedSchool && selectedSchool.barangay_id) {
              barangaySelect.value = selectedSchool.barangay_id;
            }
          })
          .catch(error => {
            console.error("Error loading barangays:", error);
            useMockBarangays(selectedCityId);
          });
      }
      
      function useMockBarangays(cityId) {
        const barangaySelect = document.getElementById("barangaySelect");
        
        const mockBarangays = [];
        for (let i = 1; i <= 10; i++) {
          mockBarangays.push({
            id: `${cityId}B${i.toString().padStart(2, '0')}`,
            name: `Barangay ${i}`
          });
        }
        
        barangays[cityId] = mockBarangays;
        
        barangaySelect.innerHTML = '<option value="" selected disabled>Select Barangay</option>';
        
        mockBarangays.forEach(barangay => {
          const option = document.createElement("option");
          option.value = barangay.id;
          option.textContent = barangay.name;
          barangaySelect.appendChild(option);
        });
        
        barangaySelect.disabled = false;
        
        if (selectedSchool && selectedSchool.barangay_id) {
          barangaySelect.value = selectedSchool.barangay_id;
        }
      }

      // Modal Functions
      function prepareModal(action) {
        const modalTitle = document.getElementById("schoolModalLabel");
        const saveButton = document.getElementById("saveSchoolBtn");
        const schoolIdInput = document.getElementById("schoolId");
        const locationInfo = document.getElementById("locationInfo");
        
        if (!modalTitle || !saveButton || !schoolIdInput) return;
        
        modalTitle.textContent = action === "add" ? "Add New School" : "Update School Information";
        saveButton.textContent = action === "add" ? "Add School" : "Update School";
        schoolIdInput.readOnly = action !== "add";
        
        if (action === "add") {
          document.getElementById("schoolForm").reset();
          document.getElementById("latitude").value = defaultLat.toFixed(7);
          document.getElementById("longitude").value = defaultLng.toFixed(7);
          
          // Reset original coordinates for add mode
          originalLat = null;
          originalLng = null;
          
          document.getElementById("provinceSelect").disabled = true;
          document.getElementById("citySelect").disabled = true;
          document.getElementById("barangaySelect").disabled = true;
          locationInfo.style.display = "none";
        } else if (action === "update" && selectedSchool) {
          // Show loading state
          showModalLoading();
          
          // Fetch complete school details including address and coordinates
          fetchCompleteSchoolData(selectedSchool.SchoolID)
            .then((schoolData) => {
              populateFormWithCompleteData(schoolData);
              displayLocationInfo(schoolData);
              hideModalLoading();
            })
            .catch((error) => {
              console.error("Error fetching school details:", error);
              // Fallback to existing data
              populateFormWithSchoolData();
              displayLocationInfo();
              hideModalLoading();
              
              Swal.fire({
                icon: "warning",
                title: "Partial Data Load",
                text: "Some data could not be loaded. Using available information.",
                confirmButtonColor: "#4caf50"
              });
            });
        }
      }

      // Function to fetch complete school data
      function fetchCompleteSchoolData(schoolId) {
        return new Promise((resolve, reject) => {
          fetch(`phpp/school_info/getSchoolDetails.php?school_id=${encodeURIComponent(schoolId)}`)
            .then(response => {
              if (!response.ok) {
                throw new Error("Network response was not ok");
              }
              return response.json();
            })
            .then(data => {
              if (data.error) {
                throw new Error(data.error);
              }
              resolve(data.school);
            })
            .catch(error => {
              reject(error);
            });
        });
      }

      // Function to populate form with complete data including address and coordinates
      function populateFormWithCompleteData(schoolData) {
        // Basic school information
        if (schoolData.basic_info) {
          document.getElementById("schoolId").value = schoolData.basic_info.SchoolID || "";
          document.getElementById("schoolName").value = schoolData.basic_info.SchoolName || "";
          document.getElementById("institutionType").value = schoolData.basic_info.Institution || "";
          document.getElementById("curricularOffer").value = schoolData.basic_info.CurricularOffer || "";
          document.getElementById("schoolPopulation").value = schoolData.basic_info.total_enrollees || "";
          document.getElementById("schoolDistrict").value = schoolData.basic_info.district || "";
          document.getElementById("schoolPrincipal").value = schoolData.basic_info.principal || "";
          document.getElementById("contactNumber").value = schoolData.basic_info.contact_no || "";
          document.getElementById("emailAddress").value = schoolData.basic_info.email || "";
          document.getElementById("principalAge").value = schoolData.basic_info.principal_age || "";
        }

        // Address information
        if (schoolData.address) {
          // Set landmark
          document.getElementById("landMark").value = schoolData.address.landmark || "";

          // Load and set region
          if (schoolData.address.region_id) {
            loadRegionsAndSetValue(schoolData.address.region_id, () => {
              // After region is set, load provinces
              if (schoolData.address.province_id) {
                loadProvincesAndSetValue(schoolData.address.province_id, () => {
                  // After province is set, load cities
                  if (schoolData.address.city_id) {
                    loadCitiesAndSetValue(schoolData.address.city_id, () => {
                      // After city is set, load barangays
                      if (schoolData.address.barangay_code) {
                        loadBarangaysAndSetValue(schoolData.address.barangay_code);
                      }
                    });
                  }
                });
              }
            });
          }
        }

        // Coordinates
        if (schoolData.coordinates) {
          const lat = schoolData.coordinates.latitude || defaultLat;
          const lng = schoolData.coordinates.longitude || defaultLng;

          // Store original coordinates for reset function
          originalLat = parseFloat(lat);
          originalLng = parseFloat(lng);

          document.getElementById("latitude").value = parseFloat(lat).toFixed(7);
          document.getElementById("longitude").value = parseFloat(lng).toFixed(7);

          // Update map if it's initialized
          if (schoolMap && schoolMarker) {
            const latLng = L.latLng(lat, lng);
            schoolMarker.setLatLng(latLng);
            schoolMap.panTo(latLng);
          }
        }
      }

      // Helper functions for loading and setting dropdown values
      function loadRegionsAndSetValue(regionId, callback) {
        const regionSelect = document.getElementById("regionSelect");

        // Clear existing options except the first one
        while (regionSelect.options.length > 1) {
          regionSelect.remove(1);
        }

        regionSelect.disabled = true;

        fetch("phpp/school_info/getRegions.php")
          .then(response => {
            if (!response.ok) {
              throw new Error("Failed to fetch regions");
            }
            return response.json();
          })
          .then(data => {
            regions = data;

            regions.forEach(region => {
              const option = document.createElement("option");
              option.value = region.id;
              option.textContent = region.name;
              regionSelect.appendChild(option);
            });

            regionSelect.disabled = false;
            regionSelect.value = regionId;

            if (callback) callback();
          })
          .catch(error => {
            console.error("Error loading regions:", error);
            regionSelect.disabled = false;
            if (callback) callback();
          });
      }

      function loadProvincesAndSetValue(provinceId, callback) {
        const regionSelect = document.getElementById("regionSelect");
        const provinceSelect = document.getElementById("provinceSelect");

        const selectedRegionId = regionSelect.value;
        if (!selectedRegionId) {
          if (callback) callback();
          return;
        }

        provinceSelect.innerHTML = '<option value="" selected disabled>Loading provinces...</option>';
        provinceSelect.disabled = true;

        fetch(`phpp/school_info/getProvinces.php?region_id=${selectedRegionId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error("Failed to fetch provinces");
            }
            return response.json();
          })
          .then(data => {
            provinces[selectedRegionId] = data;

            provinceSelect.innerHTML = '<option value="" selected disabled>Select Province</option>';

            provinces[selectedRegionId].forEach(province => {
              const option = document.createElement("option");
              option.value = province.id;
              option.textContent = province.name;
              provinceSelect.appendChild(option);
            });

            provinceSelect.disabled = false;
            provinceSelect.value = provinceId;

            if (callback) callback();
          })
          .catch(error => {
            console.error("Error loading provinces:", error);
            provinceSelect.disabled = false;
            if (callback) callback();
          });
      }

      function loadCitiesAndSetValue(cityId, callback) {
        const provinceSelect = document.getElementById("provinceSelect");
        const citySelect = document.getElementById("citySelect");

        const selectedProvinceId = provinceSelect.value;
        if (!selectedProvinceId) {
          if (callback) callback();
          return;
        }

        citySelect.innerHTML = '<option value="" selected disabled>Loading cities...</option>';
        citySelect.disabled = true;

        fetch(`phpp/school_info/getCities.php?province_id=${selectedProvinceId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error("Failed to fetch cities");
            }
            return response.json();
          })
          .then(data => {
            cities[selectedProvinceId] = data;

            citySelect.innerHTML = '<option value="" selected disabled>Select City/Municipality</option>';

            cities[selectedProvinceId].forEach(city => {
              const option = document.createElement("option");
              option.value = city.id;
              option.textContent = city.name;
              citySelect.appendChild(option);
            });

            citySelect.disabled = false;
            citySelect.value = cityId;

            if (callback) callback();
          })
          .catch(error => {
            console.error("Error loading cities:", error);
            citySelect.disabled = false;
            if (callback) callback();
          });
      }

      function loadBarangaysAndSetValue(barangayCode) {
        const citySelect = document.getElementById("citySelect");
        const barangaySelect = document.getElementById("barangaySelect");

        const selectedCityId = citySelect.value;
        if (!selectedCityId) return;

        barangaySelect.innerHTML = '<option value="" selected disabled>Loading barangays...</option>';
        barangaySelect.disabled = true;

        fetch(`phpp/school_info/getBarangays.php?city_id=${selectedCityId}`)
          .then(response => {
            if (!response.ok) {
              throw new Error("Failed to fetch barangays");
            }
            return response.json();
          })
          .then(data => {
            barangays[selectedCityId] = data;

            barangaySelect.innerHTML = '<option value="" selected disabled>Select Barangay</option>';

            barangays[selectedCityId].forEach(barangay => {
              const option = document.createElement("option");
              option.value = barangay.id;
              option.textContent = barangay.name;
              barangaySelect.appendChild(option);
            });

            barangaySelect.disabled = false;
            barangaySelect.value = barangayCode;
          })
          .catch(error => {
            console.error("Error loading barangays:", error);
            barangaySelect.disabled = false;
          });
      }

      // Loading state functions
      function showModalLoading() {
        const modalBody = document.querySelector("#schoolModal .modal-body");
        if (modalBody) {
          const loadingOverlay = document.createElement("div");
          loadingOverlay.id = "modalLoadingOverlay";
          loadingOverlay.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
          `;

          loadingOverlay.innerHTML = `
            <div class="text-center">
              <div class="spinner-border text-success" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2 mb-0">Loading school details...</p>
            </div>
          `;

          modalBody.appendChild(loadingOverlay);
        }
      }

      function hideModalLoading() {
        const loadingOverlay = document.getElementById("modalLoadingOverlay");
        if (loadingOverlay) {
          loadingOverlay.remove();
        }
      }

      function populateFormWithSchoolData() {
        if (!selectedSchool) return;

        document.getElementById("schoolId").value = selectedSchool.SchoolID || "";
        document.getElementById("schoolName").value = selectedSchool.Schoolname || "";
        document.getElementById("institutionType").value = selectedSchool.Institution || "";
        document.getElementById("curricularOffer").value = selectedSchool.curricularOffer || "";
        document.getElementById("schoolPopulation").value = selectedSchool.total_enrollees || "";
        document.getElementById("schoolDistrict").value = selectedSchool.district || "";
        document.getElementById("schoolPrincipal").value = selectedSchool.principal || "";
        document.getElementById("contactNumber").value = selectedSchool.contact_no || "";
        document.getElementById("emailAddress").value = selectedSchool.email || "";
        document.getElementById("principalAge").value = selectedSchool.principal_age || "";
        document.getElementById("landMark").value = selectedSchool.landmark || "";

        // Store and set original coordinates
        originalLat = parseFloat(selectedSchool.latitude) || defaultLat;
        originalLng = parseFloat(selectedSchool.longitude) || defaultLng;

        document.getElementById("latitude").value = originalLat.toFixed(7);
        document.getElementById("longitude").value = originalLng.toFixed(7);
        
        if (selectedSchool.region_id) {
          document.getElementById("regionSelect").value = selectedSchool.region_id;
          loadProvinces();
        }
      }

      function displayLocationInfo(schoolData = null) {
        const locationInfo = document.getElementById("locationInfo");
        const data = schoolData || selectedSchool;
        
        if (!data) return;
        
        document.getElementById("currentRegion").textContent = data.address?.region_name || data.region_name || "N/A";
        document.getElementById("currentProvince").textContent = data.address?.province_name || data.province_name || "N/A";
        document.getElementById("currentCity").textContent = data.address?.city_name || data.city_name || "N/A";
        document.getElementById("currentBarangay").textContent = data.address?.barangay_name || data.barangay_name || "N/A";
        document.getElementById("currentLandmark").textContent = data.address?.landmark || data.landmark || "N/A";
        
        const coordinates = (data.coordinates?.latitude && data.coordinates?.longitude) 
          ? `${data.coordinates.latitude}, ${data.coordinates.longitude}` 
          : (data.latitude && data.longitude)
          ? `${data.latitude}, ${data.longitude}`
          : "N/A";
        document.getElementById("currentCoordinates").textContent = coordinates;
        
        locationInfo.style.display = "block";
      }

      function addSchool() {
        const form = document.getElementById("schoolForm");
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        const saveBtn = document.getElementById("saveSchoolBtn");
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Saving...';
        saveBtn.disabled = true;

        const formData = new FormData();
        formData.append('schoolId', document.getElementById("schoolId").value);
        formData.append('schoolName', document.getElementById("schoolName").value);
        formData.append('institutionType', document.getElementById("institutionType").value);
        formData.append('curricularOffer', document.getElementById("curricularOffer").value);
        formData.append('schoolPopulation', document.getElementById("schoolPopulation").value);
        formData.append('schoolDistrict', document.getElementById("schoolDistrict").value);
        formData.append('schoolPrincipal', document.getElementById("schoolPrincipal").value);
        formData.append('contactNumber', document.getElementById("contactNumber").value);
        formData.append('emailAddress', document.getElementById("emailAddress").value);
        formData.append('principalAge', document.getElementById("principalAge").value);
        formData.append('latitude', document.getElementById("latitude").value);
        formData.append('longitude', document.getElementById("longitude").value);
        formData.append('regionId', document.getElementById("regionSelect").value);
        formData.append('provinceId', document.getElementById("provinceSelect").value);
        formData.append('cityId', document.getElementById("citySelect").value);
        formData.append('barangayId', document.getElementById("barangaySelect").value);
        formData.append('landMark', document.getElementById("landMark").value);

        fetch("phpp/school_info/addSchool.php", {
          method: "POST",
          body: formData
        })
          .then(response => response.json())
          .then(data => {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;

            if (data.success) {
              Swal.fire({
                icon: "success",
                title: "School Added",
                text: "The school has been added successfully!",
                confirmButtonColor: "#2e7d32",
              });

              closeModal();
              fetchData();
            } else {
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
            
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;

            Swal.fire({
              icon: "error",
              title: "Connection Error",
              text: "Failed to connect to the server. Please try again.",
              confirmButtonColor: "#dc3545",
            });
          });
      }

      function updateSchool() {
        const form = document.getElementById("schoolForm");
        if (!form.checkValidity()) {
          form.reportValidity();
          return;
        }

        const saveBtn = document.getElementById("saveSchoolBtn");
        const originalText = saveBtn.innerHTML;
        saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Updating...';
        saveBtn.disabled = true;

        const formData = new FormData();
        formData.append('schoolId', document.getElementById("schoolId").value);
        formData.append('schoolName', document.getElementById("schoolName").value);
        formData.append('institutionType', document.getElementById("institutionType").value);
        formData.append('curricularOffer', document.getElementById("curricularOffer").value);
        formData.append('schoolPopulation', document.getElementById("schoolPopulation").value);
        formData.append('schoolDistrict', document.getElementById("schoolDistrict").value);
        formData.append('schoolPrincipal', document.getElementById("schoolPrincipal").value);
        formData.append('contactNumber', document.getElementById("contactNumber").value);
        formData.append('emailAddress', document.getElementById("emailAddress").value);
        formData.append('principalAge', document.getElementById("principalAge").value);
        formData.append('latitude', document.getElementById("latitude").value);
        formData.append('longitude', document.getElementById("longitude").value);
        formData.append('regionId', document.getElementById("regionSelect").value);
        formData.append('provinceId', document.getElementById("provinceSelect").value);
        formData.append('cityId', document.getElementById("citySelect").value);
        formData.append('barangayId', document.getElementById("barangaySelect").value);
        formData.append('landMark', document.getElementById("landMark").value);

        fetch("phpp/school_info/updateSchool.php", {
          method: "POST",
          body: formData
        })
          .then(response => response.json())
          .then(data => {
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;

            if (data.success) {
              Swal.fire({
                icon: "success",
                title: "School Updated",
                text: "The school information has been updated successfully!",
                confirmButtonColor: "#2e7d32",
              });

              closeModal();
              fetchData();
            } else {
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
            
            saveBtn.innerHTML = originalText;
            saveBtn.disabled = false;

            Swal.fire({
              icon: "error",
              title: "Connection Error",
              text: "Failed to connect to the server. Please try again.",
              confirmButtonColor: "#dc3545",
            });
          });
      }

      function closeModal() {
        if (typeof bootstrap !== "undefined") {
          const modal = bootstrap.Modal.getInstance(
            document.getElementById("schoolModal")
          );
          if (modal) modal.hide();
        }
      }

      // Search and Filter Functions
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

        const filterCheckboxes = document.querySelectorAll(
          ".filter-panel input[type='checkbox']"
        );
        filterCheckboxes.forEach((checkbox) => {
          checkbox.addEventListener("change", () => {
            filterTable();
            resetPagination();
          });
        });
        
        const searchInput = document.getElementById("searchInput");
        if (searchInput) {
          searchInput.addEventListener("input", () => {
            filterTable();
            resetPagination();
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
          parseInt(checkbox.value)
        );

        const tbody = document.getElementById("schoolTableBody");
        if (!tbody) return;

        const rows = tbody.querySelectorAll("tr");

        rows.forEach((row) => {
          let showRow = false;
          const cells = row.querySelectorAll("td");

          if (selectedColumns.length === 0) {
            showRow = Array.from(cells).some((cell) =>
              cell.textContent.toLowerCase().includes(input)
            );
          } else {
            selectedColumns.forEach((columnIndex) => {
              const cell = cells[columnIndex];
              if (cell && cell.textContent.toLowerCase().includes(input)) {
                showRow = true;
              }
            });
          }

          row.setAttribute("data-visible", showRow ? "true" : "false");
          row.style.display = showRow ? "table-row" : "none";
        });

        updatePagination();
      }

      // Pagination Functions
      let currentPage = 1;
      const rowsPerPage = 15;

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

        updatePagination();
      }

      function resetPagination() {
        currentPage = 1;
        updatePagination();
      }

      function updatePagination() {
        const prevBtn = document.getElementById("prevPage");
        const nextBtn = document.getElementById("nextPage");
        const pageInfo = document.getElementById("pageInfo");

        if (!prevBtn || !nextBtn || !pageInfo) return;

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

        if (currentPage > totalPages) {
          currentPage = totalPages;
        }

        rows.forEach((row) => {
          if (!row.querySelector("td[colspan]")) {
            row.style.display = "none";
          }
        });

        const start = (currentPage - 1) * rowsPerPage;
        const end = Math.min(start + rowsPerPage, visibleRows.length);

        for (let i = 0; i < visibleRows.length; i++) {
          if (i >= start && i < end) {
            visibleRows[i].style.display = "table-row";
          }
        }

        pageInfo.textContent = `Page ${currentPage} of ${totalPages}`;

        prevBtn.disabled = currentPage === 1;
        nextBtn.disabled = currentPage === totalPages || totalPages === 0;
      }

      function getTotalPages() {
        const rows = document.querySelectorAll("#schoolTableBody tr");
        const visibleRows = Array.from(rows).filter(
          (row) =>
            row.getAttribute("data-visible") !== "false" &&
            !row.querySelector("td[colspan]")
        );
        return Math.max(1, Math.ceil(visibleRows.length / rowsPerPage));
      }

      // Export Functions
      function exportToExcel() {
        const columns = [
          { id: "SchoolID", name: "School ID (SID)" },
          { id: "Schoolname", name: "School Name" },
          { id: "Institution", name: "Institution Type" },
          { id: "curricularOffer", name: "Curricular Offer" },
          { id: "total_enrollees", name: "Enrollment" },
          { id: "district", name: "District" },
          { id: "principal", name: "Principal" },
          { id: "contact_no", name: "Contact Number" },
          { id: "email", name: "Email" },
          { id: "principal_age", name: "Age" },
          { id: "landmark", name: "Landmark" }
        ];

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

            Swal.fire({
              title: "Exporting data...",
              text: "Please wait while we prepare your export",
              allowOutsideClick: false,
              didOpen: () => {
                Swal.showLoading();
              },
            });

            generateCSV(schoolsData, selectedColumns);

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

        let csvContent = selectedColumns.join(",") + "\n";

        data.forEach((item) => {
          const row = selectedColumns.map((header) => {
            let value = item[header] || "";
            value = String(value).replace(/"/g, '""');

            if (value.includes(",") || value.includes('"') || value.includes("\n")) {
              value = `"${value}"`;
            }
            return value;
          });

          csvContent += row.join(",") + "\n";
        });

        const blob = new Blob([csvContent], {
          type: "text/csv;charset=utf-8;",
        });

        const link = document.createElement("a");
        const url = URL.createObjectURL(blob);

        const date = new Date();
        const fileName = `school_data_${date.getFullYear()}-${(date.getMonth() + 1)
          .toString()
          .padStart(2, "0")}-${date.getDate().toString().padStart(2, "0")}.csv`;

        link.setAttribute("href", url);
        link.setAttribute("download", fileName);
        link.style.display = "none";

        document.body.appendChild(link);
        link.click();

        document.body.removeChild(link);
        URL.revokeObjectURL(url);
      }

      function importData() {
  Swal.fire({
    title: 'Import Enrollment Data',
    html: `
    <div class="text-start">
      <div class="mb-3">
        <label for="schoolYear" class="form-label">School Year</label>
        <input type="number" class="form-control" id="schoolYear" placeholder="e.g. 2024" min="2000" max="2100" required>
        <div class="form-text">Enter a 4-digit year (e.g., 2024)</div>
      </div>
      <div class="mb-3">
        <label for="schoolType" class="form-label">School Type</label>
        <select class="form-select" id="schoolType" required>
          <option value="">Select School Type</option>
          <option value="Public">Public</option>
          <option value="Private">Private</option>
          <option value="SUC">SUC</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="importType" class="form-label">Import Type</label>
        <select class="form-select" id="importType" required onchange="toggleSchoolIdField()">
          <option value="">Select Import Type</option>
          <option value="whole">Update Whole Table</option>
          <option value="specific">Update Specific School</option>
        </select>
      </div>
      <div class="mb-3" id="schoolIdField" style="display: none;">
        <label for="specificSchoolId" class="form-label">School ID</label>
        <select class="form-select" id="specificSchoolId">
          <option value="">Select School</option>
        </select>
      </div>
      <div class="mb-3">
        <label for="csvFile" class="form-label">CSV File</label>
        <input type="file" class="form-control" id="csvFile" accept=".csv" required>
        <div class="form-text">Please select a CSV file containing enrollment data.</div>
      </div>
    </div>
  `,
    showCancelButton: true,
    confirmButtonText: 'Import Data',
    confirmButtonColor: '#4caf50',
    cancelButtonText: 'Cancel',
    width: '500px',
    didOpen: () => {
      // Populate school dropdown with existing schools
      const schoolSelect = document.getElementById('specificSchoolId');
      schoolsData.forEach(school => {
        const option = document.createElement('option');
        option.value = school.SchoolID;
        option.textContent = `${school.SchoolID} - ${school.Schoolname}`;
        schoolSelect.appendChild(option);
      });
      
      // Add toggle function to global scope temporarily
      window.toggleSchoolIdField = function() {
        const importType = document.getElementById('importType').value;
        const schoolIdField = document.getElementById('schoolIdField');
        schoolIdField.style.display = importType === 'specific' ? 'block' : 'none';
      };
    },
    preConfirm: () => {
      const schoolYear = document.getElementById('schoolYear').value;
      const schoolType = document.getElementById('schoolType').value;
      const importType = document.getElementById('importType').value;
      const csvFile = document.getElementById('csvFile').files[0];
      const schoolId = document.getElementById('specificSchoolId').value;
      
      if (!schoolYear || !schoolType || !importType || !csvFile) {
        Swal.showValidationMessage('Please fill in all required fields');
        return false;
      }
      
      if (importType === 'specific' && !schoolId) {
        Swal.showValidationMessage('Please select a school for specific import');
        return false;
      }
      
      return {
        schoolYear,
        schoolType,
        importType,
        csvFile,
        schoolId
      };
    }
  }).then((result) => {
    if (result.isConfirmed) {
      const { schoolYear, schoolType, importType, csvFile, schoolId } = result.value;
      
      // Show loading
      Swal.fire({
        title: 'Processing Import',
        text: 'Please wait while we process your enrollment data...',
        allowOutsideClick: false,
        didOpen: () => {
          Swal.showLoading();
        }
      });
      
      // Create FormData for file upload
      const formData = new FormData();
      formData.append('schoolYear', schoolYear);
      formData.append('schoolType', schoolType);
      formData.append('importType', importType);
      formData.append('csvFile', csvFile);
      
      if (importType === 'specific') {
        formData.append('schoolID', schoolId);
      }
      
      // Send to PHP handler
      fetch('phpp/dashboard/enrollment_import.php', {
        method: 'POST',
        body: formData
      })
      .then(response => response.json())
      .then(data => {
        if (data.success) {
          Swal.fire({
            title: 'Import Successful!',
            text: data.message,
            icon: 'success',
            confirmButtonColor: '#4caf50'
          }).then(() => {
            // Refresh the page or update the table
            fetchData();
          });
        } else {
          Swal.fire({
            title: 'Import Failed',
            text: data.message,
            icon: 'error',
            confirmButtonColor: '#dc3545'
          });
        }
      })
      .catch(error => {
        console.error('Import error:', error);
        Swal.fire({
          title: 'Connection Error',
          text: 'Failed to connect to the server. Please try again.',
          icon: 'error',
          confirmButtonColor: '#dc3545'
        });
      })
      .finally(() => {
        // Clean up the temporary function
        delete window.toggleSchoolIdField;
      });
    }
  });
}

      // Enrollment Functions
      function initializeEnrollmentLinks() {
        const enrollmentCells = document.querySelectorAll('.enrollment-link');
        enrollmentCells.forEach(cell => {
          cell.addEventListener("click", function(e) {
            e.stopPropagation();
            
            let schoolIndex;
            if (this.closest("tr")) {
              schoolIndex = this.closest("tr").getAttribute("data-row-id");
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

      function showEnrollmentPopup(school) {
        const popup = document.getElementById("enrollmentPopup");
        const popupBody = document.getElementById("enrollmentPopupBody");
        const popupTitle = document.querySelector(".enrollment-popup-title");

        popupTitle.textContent = `Enrollment Details - ${school.Schoolname}`;
        
        popupBody.innerHTML = `
          <div class="text-center py-4">
            <div class="spinner-border text-success" role="status">
              <span class="visually-hidden">Loading enrollment data...</span>
            </div>
            <p class="mt-2">Loading enrollment data...</p>
          </div>
        `;
        
        popup.style.display = "flex";
        document.body.style.overflow = "hidden";
        
        fetchEnrollmentData(school.SchoolID)
          .then(enrollmentData => {
            updateEnrollmentPopupContent(school, enrollmentData);
          })
          .catch(error => {
            console.error("Error fetching enrollment data:", error);
            const mockEnrollmentData = generateEnrollmentData(parseInt(school.total_enrollees) || 0);
            updateEnrollmentPopupContent(school, mockEnrollmentData);
          });
      }
      
      function fetchEnrollmentData(schoolId) {
        return new Promise((resolve, reject) => {
          fetch(`phpp/school_info/fetchEnrollmentData.php?school_id=${encodeURIComponent(schoolId)}`)
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
              
              const processedData = processEnrollmentData(data);
              resolve(processedData);
            })
            .catch(error => {
              reject(error);
            });
        });
      }
      
      function processEnrollmentData(data) {
        const enrollmentData = [];

        if (!data || Object.keys(data).length === 0) {
          console.warn('No enrollment data found or invalid format');
          return enrollmentData;
        }

        if (data.K_M !== undefined || data.K_F !== undefined) {
          const male = parseInt(data.K_M) || 0;
          const female = parseInt(data.K_F) || 0;
          if (male + female > 0) {
            enrollmentData.push({
              level: 'Kindergarten',
              male,
              female,
              total: male + female
            });
          }
        }

        for (let i = 1; i <= 10; i++) {
          const male = parseInt(data[`G${i}_M`]) || 0;
          const female = parseInt(data[`G${i}_F`]) || 0;
          if (male + female > 0) {
            enrollmentData.push({
              level: `Grade ${i}`,
              male,
              female,
              total: male + female
            });
          }
        }

        const elemMale = parseInt(data.ELEM_NG_M) || 0;
        const elemFemale = parseInt(data.ELEM_NG_F) || 0;
        if (elemMale + elemFemale > 0) {
          enrollmentData.push({
            level: 'Elementary Non-Graded',
            male: elemMale,
            female: elemFemale,
            total: elemMale + elemFemale
          });
        }

        const jhsMale = parseInt(data.JHS_NG_M) || 0;
        const jhsFemale = parseInt(data.JHS_NG_F) || 0;
        if (jhsMale + jhsFemale > 0) {
          enrollmentData.push({
            level: 'Junior High School Non-Graded',
            male: jhsMale,
            female: jhsFemale,
            total: jhsMale + jhsFemale
          });
        }

        const g11Tracks = ['ACAD', 'TVL', 'SPORT', 'ART', 'UNQ'];
        g11Tracks.forEach(track => {
          const male = (parseInt(data[`FS_G11_${track}_M`]) || 0) + (parseInt(data[`SS_G11_${track}_M`]) || 0);
          const female = (parseInt(data[`FS_G11_${track}_F`]) || 0) + (parseInt(data[`SS_G11_${track}_F`]) || 0);
          if (male + female > 0) {
            enrollmentData.push({
              level: `Grade 11 - ${track}`,
              male,
              female,
              total: male + female
            });
          }
        });

        const g12Tracks = ['ACAD', 'TVL', 'SPORT', 'ART', 'UNQ'];
        g12Tracks.forEach(track => {
          const male = (parseInt(data[`FS_G12_${track}_M`]) || 0) + (parseInt(data[`SS_G12_${track}_M`]) || 0);
          const female = (parseInt(data[`FS_G12_${track}_F`]) || 0) + (parseInt(data[`SS_G12_${track}_F`]) || 0);
          if (male + female > 0) {
            enrollmentData.push({
              level: `Grade 12 - ${track}`,
              male,
              female,
              total: male + female
            });
          }
        });

        return enrollmentData;
      }

      function updateEnrollmentPopupContent(school, enrollmentData) {
        const popupBody = document.getElementById("enrollmentPopupBody");
        
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

        let content = `
        <div class="enrollment-summary">
          <h5><i class="bi bi-info-circle-fill me-2"></i>School Enrollment Summary</h5>
          <div class="row">
            <div class="col-md-6">
              <p><strong>Institution Type:</strong> ${school.Institution || "N/A"}</p>
            </div>
            <div class="col-md-6">
              <p><strong>Total Enrollment:</strong> <span class="fs-4 text-success">${total_enrollees.toLocaleString()}</span> students</p>
            </div>
          </div>
          <div class="row">
            <div class="col-md-6">
              <p><strong>Curricular Offer:</strong> ${school.curricularOffer || "N/A"}</p>
            </div>
            <div class="col-md-6">
              <p><strong>District:</strong> ${school.district || "N/A"}</p>
            </div>
          </div>
          <div class="row">
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

        popupBody.innerHTML = content;
      }

      function closeEnrollmentPopup() {
        const popup = document.getElementById("enrollmentPopup");
        popup.style.display = "none";
        document.body.style.overflow = "";
      }

      function generateEnrollmentData(totalPopulation) {
        let gradeLevels = [];

        if (totalPopulation <= 0) {
          return gradeLevels;
        }

        const isElementary = totalPopulation < 1000;
        const isHighSchool = totalPopulation >= 1000 && totalPopulation < 2000;
        const isK12 = totalPopulation >= 2000;

        if (isElementary) {
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
          gradeLevels = [
            { level: "Grade 7" },
            { level: "Grade 8" },
            { level: "Grade 9" },
            { level: "Grade 10" },
            { level: "Grade 11" },
            { level: "Grade 12" },
          ];
        } else if (isK12) {
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

        const totalLevels = gradeLevels.length;
        let remainingPopulation = totalPopulation;

        gradeLevels.forEach((grade, index) => {
          let gradeTotal;
          if (index === totalLevels - 1) {
            gradeTotal = remainingPopulation;
          } else {
            const percentage = 0.1 + Math.random() * 0.1;
            gradeTotal = Math.floor(remainingPopulation * percentage);
            remainingPopulation -= gradeTotal;
          }

          const malePercentage = 0.45 + Math.random() * 0.1;
          const male = Math.floor(gradeTotal * malePercentage);
          const female = gradeTotal - male;

          grade.male = male;
          grade.female = female;
          grade.total = gradeTotal;
        });

        return gradeLevels;
      }

      document
        .getElementById("enrollmentPopup")
        .addEventListener("click", function (e) {
          if (e.target === this) {
            closeEnrollmentPopup();
          }
        });

      document.addEventListener("keydown", function (e) {
        if (
          e.key === "Escape" &&
          document.getElementById("enrollmentPopup").style.display === "flex"
        ) {
          closeEnrollmentPopup();
        }
      });

      document.addEventListener("click", function (event) {
        const isTableRow = event.target.closest("#schoolTableBody tr");
        const isUpdateButton = event.target.closest("#updateButton");
        const isModal = event.target.closest(".modal");
        const isFilterPanel = event.target.closest(".filter-panel");
        const isFilterButton = event.target.closest("#filterButton");

        if (!isTableRow && !isUpdateButton && !isModal && !isFilterPanel && !isFilterButton) {
          document.querySelectorAll("#schoolTableBody tr").forEach((r) => {
            r.classList.remove("table-primary");
          });

          const updateButton = document.getElementById("updateButton");
          if (updateButton) {
            updateButton.classList.add("btn-update-disabled");
            updateButton.disabled = true;
          }

          selectedSchoolIndex = -1;
          selectedSchool = null;
        }
      });

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
            if (confirm("Are you sure you want to logout?")) {
              window.location.href = "index.php";
            }
          }
        }
      });
    </script>
  </body>
</html>

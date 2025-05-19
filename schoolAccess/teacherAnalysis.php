<?php
// Prevent browser caching of authenticated pages
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Cache-Control: post-check=0, pre-check=0', false);
header('Pragma: no-cache');
header('Expires: 0');
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ..../auth/pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DepEd: Teacher Analysis</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3//bootstrap-icons.min.css"
    />
    <link rel="stylesheet" href="css/side-topbar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      :root {
        --primary-green: #2e7d32;
        --primary-color: #2ecc71;
        --primary-dark: #2e8b57;
        --secondary-color: #3498db;
        --background-color: #f8f9fa;
        --text-color: #333333;
        --border-color: #e0e0e0;
        --shadow-color: rgba(0, 0, 0, 0.1);
      }

      body {
        -family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
        background-color: var(--background-color);
        color: var(--text-color);
        margin: 0; /* Remove default margin */
        padding: 0; /* Remove default padding */
      }

      /* Remove any potential spacing from containers */
      #sidebar-container,
      #topbar-container {
        margin: 0;
        padding: 0;
      }

      .main-content {
        background-color: var(--background-color);
        min-height: 100vh;
        padding: 2rem;
      }

      .district-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
      }

      .district-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 6px 12px var(--shadow-color);
      }
      h2 {
        -weight: 600;
      }
      .metric-label {
        color: #666;
        -size: 0.9rem;
        margin-bottom: 0.5rem;
      }

      .metric-value {
        -size: 1.8rem;
        -weight: 600;
        color: var(--primary-dark);
      }

      .grid-icon {
        color: var(--primary-green);
        float: right;
        -size: 1.5rem;
      }

      .clickable {
        cursor: pointer;
      }

      .clickable:hover {
        text-decoration: underline;
      }

      .view-button {
        width: 100%;
        background-color: transparent;
        color: var(--primary-dark);
        border: 2px solid var(--primary-dark);
        padding: 0.75rem;
        border-radius: 5px;
        margin-top: 1rem;
        transition: all 0.3s ease;
        -weight: 500;
      }

      .view-button:hover {
        background-color: var(--primary-dark);
        color: #f8f9fa;
        transform: translateY(-2px);
      }

      /* Modal styles */
      .modal-content {
        border-radius: 10px;
        border: none;
        box-shadow: 0 4px 6px var(--shadow-color);
      }

      .modal-header {
        background-color: var(--primary-dark);
        color: white;
        border-top-left-radius: 10px;
        border-top-right-radius: 10px;
        padding: 1rem 1.5rem;
      }

      .modal-title {
        -weight: 600;
      }

      .modal-body {
        padding: 1.5rem;
      }
      @media (min-width: 768px) {
        .modal-body {
          max-height: 500px;
          overflow-y: auto;
        }
      }

      .table-responsive {
        max-height: 400px;
        overflow-y: auto;
        text-align: center;
      }

      .modal-footer {
        border-top: 1px solid var(--border-color);
        padding: 1rem 1.5rem;
        text-align: center;
      }

      .form-control {
        border-radius: 5px;
        border: 1px solid var(--border-color);
        max-width: 350px;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
      }

      .form-control:focus {
        box-shadow: 0 0 0 0.2rem rgba(74, 144, 226, 0.25);
        border-color: var(--primary-dark);
      }

      .btn {
        border-radius: 5px;
        padding: 0.5rem 1rem;
        -weight: 500;
        transition: all 0.3s ease;
      }

      .btn-primary {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
      }

      .btn-primary:hover {
        background-color: darken(var(--primary-dark), 10%);
        border-color: darken(var(--primary-dark), 10%);
      }

      .table {
        background-color: white;
        border-radius: 10px;
        overflow: hidden;
        box-shadow: 0 4px 6px var(--shadow-color);
      }

      .table th {
        background-color: var(--primary-dark);
        color: white;
        -weight: 600;
        text-transform: uppercase;
        -size: 0.9rem;
      }

      .table td,
      .table th {
        padding: 1rem;
        vertical-align: middle;
      }

      /* Table striped rows */
      .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(0, 0, 0, 0.05);
      }

      @media (max-width: 767.98px) {
        .district-card {
          padding: 1rem;
        }

        .metric-value {
          -size: 1.5rem;
        }

        .table-responsive {
          overflow-x: auto;
          -webkit-overflow-scrolling: touch;
          border: none;
        }

        .table {
          background-color: transparent;
          box-shadow: none;
          width: 100%;
        }

        .table thead {
          display: none;
        }

        .table tr {
          display: block;
          background-color: white;
          border: 1px solid var(--border-color);
          border-radius: 5px;
          margin-bottom: 1rem;
          box-shadow: 0 2px 4px var(--shadow-color);
        }

        .table td {
          display: flex;
          justify-content: space-between;
          align-items: center;
          border: none;
          padding: 0.75rem 1rem;
          text-align: right;
        }

        .table td::before {
          content: attr(data-label);
          -weight: 600;
          text-transform: uppercase;
          -size: 0.8rem;
          color: var(--primary-color);
          text-align: left;
          flex: 1;
        }

        .table td .btn {
          margin-left: auto;
        }
      }

      .modal-dialog.modal-lg {
        max-width: 80%;
      }

      @media (max-width: 992px) {
        .modal-dialog.modal-lg {
          max-width: 95%;
        }
      }

      .modal-content {
        display: flex;
        flex-direction: column;
        height: 100%;
        max-height: 90vh; /* Ensures the modal doesn't get too tall */
      }

      .modal-body {
        flex: 1; /* Allows body to take up available space */
        overflow-y: auto; /* Enables scrolling if content is long */
      }

      .modal-footer {
        display: flex;
        justify-content: center; /* Centers the pagination */
        align-items: center; /* Aligns items vertically */
        position: sticky;
        bottom: 0;
        width: 100%;
        background: #f8f9fa; /* Light gray background */
        padding: 10px;
        border-top: 1px solid #ddd;
        text-align: center; /* Ensures text alignment */
      }

      .form-label {
        -weight: 600;
        color: var(--text-color);
        margin-bottom: 0.5rem;
      }

      .form-control {
        border: 2px solid var(--border-color);
        border-radius: 8px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
      }

      .form-control:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
      }

      .btn-primary {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
      }

      .btn-primary:hover {
        background-color: var(--primary-color);
        border-color: var(--primary-dark);
      }
      #updateTeacherForm input[type="radio"] {
        transform: scale(1.5); /* Adjust size */
        margin-right: 10px; /* Add space between radio button and label */
      }
      #employeePagination {
        display: flex;
        justify-content: center; /* Ensures pagination stays centered */
        align-items: center;
        padding: 0; /* Remove extra padding */
        margin: 0 auto; /* Auto margins for centering */
        list-style: none;
      }

      #employeePagination .page-item {
        margin: 5px;
      }

      #employeePagination .page-item.active .page-link {
        background-color: #007bff; /* Change to your preferred color */
        color: white; /* Text color */
        border-color: #007bff; /* Border color */
      }

      #employeePagination .page-link {
        color: #007bff; /* Default link color */
        border: 1px solid #ddd;
      }

      #employeePagination .page-link:hover {
        background-color: #0056b3;
        color: white;
      }

      /* District filter dropdown styles */
      .district-filter-container {
        margin-bottom: 20px;
        display: flex;
        justify-content: flex-end;
      }

      .district-filter {
        width: auto;
        min-width: 180px;
        border-color: var(--primary-dark);
        color: var(--primary-dark);
        -weight: 500;
      }

      .district-filter:focus {
        border-color: var(--primary-dark);
        box-shadow: 0 0 0 0.25rem rgba(46, 139, 87, 0.25);
      }

      /* New styles for filter and export components */
      .filter-options,
      .export-options {
        margin-bottom: 1rem;
        border: 1px solid #ddd;
        border-radius: 0.25rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      }

      .card-title {
        -weight: 600;
        margin-bottom: 1rem;
        color: var(--primary-dark);
      }

      .filter-field {
        border: 1px solid #ced4da;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
        -size: 0.9rem;
      }

      .filter-field:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(46, 204, 113, 0.25);
      }

      .form-check {
        margin-bottom: 0.5rem;
      }

      .form-check-input {
        cursor: pointer;
      }

      .form-check-input:checked {
        background-color: var(--primary-dark);
        border-color: var(--primary-dark);
      }

      .form-check-label {
        cursor: pointer;
        user-select: none;
        -size: 0.9rem;
      }

      .btn-filter,
      .btn-export {
        white-space: nowrap;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
      }

      .btn-filter i,
      .btn-export i {
        -size: 1rem;
      }

      @media (max-width: 767.98px) {
        .col-md-3,
        .col-md-6 {
          margin-bottom: 0.75rem;
        }

        .btn-filter,
        .btn-export {
          padding: 0.25rem 0.5rem;
          -size: 0.875rem;
        }
      }
    </style>
    <script src="../js/side-topbar.js"></script>
  </head>
  <body>
    <div id="sidebar-container">
      <!-- Sidebar will be loaded here -->
    </div>
    <div id="topbar-container">
      <!-- Topbar will be loaded here -->
    </div>

    <div class="main-content p-4">
      <!-- District Filter Dropdown -->
      <div class="district-filter-container">
        <select id="districtFilter" class="form-select district-filter">
          <option value="all">All Districts</option>
          <option value="ALL">Congressional District</option>

          <option value="CD1">CD1</option>
          <option value="CD2">CD2</option>
          <option value="CD3">CD3</option>
          <option value="CD4">CD4</option>
        </select>
      </div>

      <div class="row" id="district-cards-container">
        <!-- District cards will be dynamically generated here -->
      </div>
    </div>

    <!-- Near Retirement Modal -->
    <div class="modal fade" id="nearRetirementModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Near Retirement Employees - <span id="districtNum"></span>
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <input
                type="text"
                class="form-control"
                id="searchInput"
                placeholder="Search employees..."
                style="max-width: 300px"
              />
              <button
                type="button"
                class="btn btn-primary ms-2 btn-filter"
                onclick="toggleFilterOptions('nearRetirementModal')"
              >
                <i class="bi bi-funnel"></i> Filter
              </button>
              <button
                type="button"
                class="btn btn-warning ms-auto btn-export"
                onclick="toggleExportOptions('nearRetirementModal')"
              >
                <i class="bi bi-filetype-csv"></i> Export
              </button>
            </div>
            
            <!-- Filter Options -->
            <div id="filterOptions-nearRetirementModal" class="filter-options card" style="display: none;">
              <div class="card-body">
                <h6 class="card-title">Filter Options</h6>
                <div class="row" id="filterFields-nearRetirementModal">
                  <div class="col-md-6 mb-2">
                    <label class="form-label">SID</label>
                    <input type="text" class="form-control filter-field" data-column="0" placeholder="Filter SID...">
                  </div>
                  <div class="col-md-6 mb-2">
                    <label class="form-label">School Name</label>
                    <input type="text" class="form-control filter-field" data-column="1" placeholder="Filter School Name...">
                  </div>
                  
                  <div class="col-md-6 mb-2">
                    <label class="form-label">Total Retirees</label>
                    <div class="d-flex gap-2 align-items-center">
                      <input type="number" class="form-control filter-field-range" data-column="2" data-range="min" placeholder="Min" min="0">
                      <span>to</span>
                      <input type="number" class="form-control filter-field-range" data-column="2" data-range="max" placeholder="Max" min="0">
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                  <button type="button" class="btn btn-secondary me-2" onclick="clearFilters('nearRetirementModal')">Clear</button>
                  <button type="button" class="btn btn-primary" onclick="applyFilters('nearRetirementModal', 'nearRetirementTableBody')">Apply</button>
                </div>
              </div>
            </div>
            
            <!-- Export Options -->
            <div id="exportOptions-nearRetirementModal" class="export-options card" style="display: none;">
              <div class="card-body">
                <h6 class="card-title">Select columns to export</h6>
                <div class="row" id="exportFields-nearRetirementModal">
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="0" id="export-nearRetirementModal-col-0" checked>
                      <label class="form-check-label" for="export-nearRetirementModal-col-0">
                        SID
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="1" id="export-nearRetirementModal-col-1" checked>
                      <label class="form-check-label" for="export-nearRetirementModal-col-1">
                        School Name
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="2" id="export-nearRetirementModal-col-2" checked>
                      <label class="form-check-label" for="export-nearRetirementModal-col-2">
                        Total Retirees
                      </label>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                  <button type="button" class="btn btn-secondary me-2" onclick="toggleExportOptions('nearRetirementModal')">Cancel</button>
                  <button type="button" class="btn btn-success" onclick="exportTableData('nearRetirementModal', 'nearRetirementTableBody')">Download CSV</button>
                </div>
              </div>
            </div>
            
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>SID</th>
                    <th>School Name</th>
                    <th>Total Retirees</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="nearRetirementTableBody">
                  <!-- Near retirement data will be dynamically inserted here -->
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>
    
    <!-- Employee Details Modal -->
    <div class="modal fade" id="employeeDetailsModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Employee Details - <span id="schoolNameTitle"></span>
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <input
                type="text"
                class="form-control"
                id="employeeDetailsSearchInput"
                placeholder="Search employees..."
                style="max-width: 300px"
              />
              <button
                type="button"
                class="btn btn-primary ms-2 btn-filter"
                onclick="toggleFilterOptions('employeeDetailsModal')"
              >
                <i class="bi bi-funnel"></i> Filter
              </button>
              <button
                type="button"
                class="btn btn-warning ms-auto btn-export"
                onclick="toggleExportOptions('employeeDetailsModal')"
              >
                <i class="bi bi-filetype-csv"></i> Export
              </button>
            </div>
            
            <!-- Filter Options -->
            <div id="filterOptions-employeeDetailsModal" class="filter-options card" style="display: none;">
              <div class="card-body">
                <h6 class="card-title">Filter Options</h6>
                <div class="row" id="filterFields-employeeDetailsModal">
                  <div class="col-md-6 mb-2">
                    <label class="form-label">Employee No</label>
                    <input type="text" class="form-control filter-field" data-column="0" placeholder="Filter Employee No...">
                  </div>
                  <div class="col-md-6 mb-2">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control filter-field" data-column="1" placeholder="Filter Name...">
                  </div>
                  
                  <div class="col-md-6 mb-2">
                    <label class="form-label">Age</label>
                    <div class="d-flex gap-2 align-items-center">
                      <input type="number" class="form-control filter-field-range" data-column="2" data-range="min" placeholder="Min" min="0" max="100">
                      <span>to</span>
                      <input type="number" class="form-control filter-field-range" data-column="2" data-range="max" placeholder="Max" min="0" max="100">
                    </div>
                  </div>
                  <div class="col-md-6 mb-2">
                    <label class="form-label">Position</label>
                    <select id="positionDropdown-employeeDetailsModal" class="form-control filter-field" data-column="3">
                      <option value="">All Positions</option>
                      <!-- Options will be loaded via JavaScript -->
                    </select>
                  </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                  <button type="button" class="btn btn-secondary me-2" onclick="clearFilters('employeeDetailsModal')">Clear</button>
                  <button type="button" class="btn btn-primary" onclick="applyFilters('employeeDetailsModal', 'employeeDetailsTableBody')">Apply</button>
                </div>
              </div>
            </div>
            
            <!-- Export Options -->
            <div id="exportOptions-employeeDetailsModal" class="export-options card" style="display: none;">
              <div class="card-body">
                <h6 class="card-title">Select columns to export</h6>
                <div class="row" id="exportFields-employeeDetailsModal">
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="0" id="export-employeeDetailsModal-col-0" checked>
                      <label class="form-check-label" for="export-employeeDetailsModal-col-0">
                        Employee No
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="1" id="export-employeeDetailsModal-col-1" checked>
                      <label class="form-check-label" for="export-employeeDetailsModal-col-1">
                        Name
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="2" id="export-employeeDetailsModal-col-2" checked>
                      <label class="form-check-label" for="export-employeeDetailsModal-col-2">
                        Age
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="3" id="export-employeeDetailsModal-col-3" checked>
                      <label class="form-check-label" for="export-employeeDetailsModal-col-3">
                        Position
                      </label>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                  <button type="button" class="btn btn-secondary me-2" onclick="toggleExportOptions('employeeDetailsModal')">Cancel</button>
                  <button type="button" class="btn btn-success" onclick="exportTableData('employeeDetailsModal', 'employeeDetailsTableBody')">Download CSV</button>
                </div>
              </div>
            </div>
            
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>Employee No</th>
                    <th>Name</th>
                    <th>Age</th>
                    <th>Position</th>
                  </tr>
                </thead>
                <tbody id="employeeDetailsTableBody">
                  <!-- Employee details will be dynamically inserted here -->
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Total Employees Modal -->
    <div class="modal fade" id="employeeModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Total Employees - <span id="employeeDistrictName"></span>
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <input
                type="text"
                class="form-control"
                id="employeeSearchInput"
                placeholder="Search schools..."
                style="max-width: 300px"
              />
              <button
                type="button"
                class="btn btn-primary ms-2 btn-filter"
                onclick="toggleFilterOptions('employeeModal')"
              >
                <i class="bi bi-funnel"></i> Filter
              </button>
              <button
                type="button"
                class="btn btn-warning ms-auto btn-export"
                onclick="toggleExportOptions('employeeModal')"
              >
                <i class="bi bi-filetype-csv"></i> Export
              </button>
            </div>
            
            <!-- Filter Options -->
            <div id="filterOptions-employeeModal" class="filter-options card" style="display: none;">
              <div class="card-body">
                <h6 class="card-title">Filter Options</h6>
                <div class="row" id="filterFields-employeeModal">
                  <div class="col-md-6 mb-2">
                    <label class="form-label">SID</label>
                    <input type="text" class="form-control filter-field" data-column="0" placeholder="Filter SID...">
                  </div>
                  <div class="col-md-6 mb-2">
                    <label class="form-label">School Name</label>
                    <input type="text" class="form-control filter-field" data-column="1" placeholder="Filter School Name...">
                  </div>
                  
                  <div class="col-md-6 mb-2">
                    <label class="form-label">Total Employees</label>
                    <div class="d-flex gap-2 align-items-center">
                      <input type="number" class="form-control filter-field-range" data-column="2" data-range="min" placeholder="Min" min="0">
                      <span>to</span>
                      <input type="number" class="form-control filter-field-range" data-column="2" data-range="max" placeholder="Max" min="0">
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                  <button type="button" class="btn btn-secondary me-2" onclick="clearFilters('employeeModal')">Clear</button>
                  <button type="button" class="btn btn-primary" onclick="applyFilters('employeeModal', 'employeeTableBody')">Apply</button>
                </div>
              </div>
            </div>
            
            <!-- Export Options -->
            <div id="exportOptions-employeeModal" class="export-options card" style="display: none;">
              <div class="card-body">
                <h6 class="card-title">Select columns to export</h6>
                <div class="row" id="exportFields-employeeModal">
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="0" id="export-employeeModal-col-0" checked>
                      <label class="form-check-label" for="export-employeeModal-col-0">
                        SID
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="1" id="export-employeeModal-col-1" checked>
                      <label class="form-check-label" for="export-employeeModal-col-1">
                        School Name
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="2" id="export-employeeModal-col-2" checked>
                      <label class="form-check-label" for="export-employeeModal-col-2">
                        Total Employees
                      </label>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                  <button type="button" class="btn btn-secondary me-2" onclick="toggleExportOptions('employeeModal')">Cancel</button>
                  <button type="button" class="btn btn-success" onclick="exportTableData('employeeModal', 'employeeTableBody')">Download CSV</button>
                </div>
              </div>
            </div>
            
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>SID</th>
                    <th>School Name</th>
                    <th>Total Employees</th>
                    <th>Action</th>
                  </tr>
                </thead>
                <tbody id="employeeTableBody">
                  <!-- School data will be dynamically inserted here -->
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <ul
              id="employeePagination"
              class="pagination justify-content-center flex-wrap"
            ></ul>
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Average Service Modal -->
    <div class="modal fade" id="averageServiceModal" tabindex="-1">
      <div class="modal-dialog modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">
              Average Years of Service - <span id="serviceDistrictName"></span>
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <div class="d-flex justify-content-between align-items-center mb-3">
              <input
                type="text"
                class="form-control"
                id="serviceSearchInput"
                placeholder="Search schools..."
                style="max-width: 300px"
              />
              <button
                type="button"
                class="btn btn-primary ms-2 btn-filter"
                onclick="toggleFilterOptions('averageServiceModal')"
              >
                <i class="bi bi-funnel"></i> Filter
              </button>
              <button
                type="button"
                class="btn btn-warning ms-auto btn-export"
                onclick="toggleExportOptions('averageServiceModal')"
              >
                <i class="bi bi-filetype-csv"></i> Export
              </button>
            </div>
            
            <!-- Filter Options -->
            <div id="filterOptions-averageServiceModal" class="filter-options card" style="display: none;">
              <div class="card-body">
                <h6 class="card-title">Filter Options</h6>
                <div class="row" id="filterFields-averageServiceModal">
                  <div class="col-md-6 mb-2">
                    <label class="form-label">School Name</label>
                    <input type="text" class="form-control filter-field" data-column="0" placeholder="Filter School Name...">
                  </div>
                  
                  <div class="col-md-6 mb-2">
                    <label class="form-label">Total Employees</label>
                    <div class="d-flex gap-2 align-items-center">
                      <input type="number" class="form-control filter-field-range" data-column="1" data-range="min" placeholder="Min" min="0">
                      <span>to</span>
                      <input type="number" class="form-control filter-field-range" data-column="1" data-range="max" placeholder="Max" min="0">
                    </div>
                  </div>
                  <div class="col-md-6 mb-2">
                    <label class="form-label">Average Years of Service</label>
                    <input type="text" class="form-control filter-field" data-column="2" placeholder="Filter Average Years...">
                  </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                  <button type="button" class="btn btn-secondary me-2" onclick="clearFilters('averageServiceModal')">Clear</button>
                  <button type="button" class="btn btn-primary" onclick="applyFilters('averageServiceModal', 'serviceTableBody')">Apply</button>
                </div>
              </div>
            </div>
            
            <!-- Export Options -->
            <div id="exportOptions-averageServiceModal" class="export-options card" style="display: none;">
              <div class="card-body">
                <h6 class="card-title">Select columns to export</h6>
                <div class="row" id="exportFields-averageServiceModal">
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="0" id="export-averageServiceModal-col-0" checked>
                      <label class="form-check-label" for="export-averageServiceModal-col-0">
                        School Name
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="1" id="export-averageServiceModal-col-1" checked>
                      <label class="form-check-label" for="export-averageServiceModal-col-1">
                        Total Employees
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3 mb-2">
                    <div class="form-check">
                      <input class="form-check-input export-column" type="checkbox" value="2" id="export-averageServiceModal-col-2" checked>
                      <label class="form-check-label" for="export-averageServiceModal-col-2">
                        Average Years of Service
                      </label>
                    </div>
                  </div>
                </div>
                <div class="d-flex justify-content-end mt-3">
                  <button type="button" class="btn btn-secondary me-2" onclick="toggleExportOptions('averageServiceModal')">Cancel</button>
                  <button type="button" class="btn btn-success" onclick="exportTableData('averageServiceModal', 'serviceTableBody')">Download CSV</button>
                </div>
              </div>
            </div>
            
            <div class="table-responsive">
              <table class="table table-striped">
                <thead>
                  <tr>
                    <th>School Name</th>
                    <th>Total Employees</th>
                    <th>Average Years of Service</th>
                  </tr>
                </thead>
                <tbody id="serviceTableBody">
                  <!-- Service data will be dynamically inserted here -->
                </tbody>
              </table>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        loadComponent("sidebar.php", "sidebar-container", initializeSidebar);
        loadComponent("topbar.php", "topbar-container", initializeTopbar);

        // Initialize the district filter
        setupDistrictFilter();

        // Show all district cards by default
        showAllMunicipalityCards();
        
        // Set up search functionality for employee details modal
        document.getElementById("employeeDetailsSearchInput")?.addEventListener("input", function() {
          filterTableBySearch(this.value, "employeeDetailsTableBody");
        });
        
        // Set up search functionality for service modal
        document.getElementById("serviceSearchInput")?.addEventListener("input", function() {
          filterTableBySearch(this.value, "serviceTableBody");
        });
      });

      // District data mapping
      const districtSchoolsMapping = {
        CD1: [
          "Aliaga",
          "Cuyapo East",
          "Cuyapo West",
          "Guimba East",
          "Guimba West",
          "Licab",
          "Nampicuan",
          "Quezon",
          "Sto. Domingo",
          "Talavera North",
          "Talavera South",
          "Zaragoza",
        ],
        CD2: [
          "Carranglan",
          "Llanera",
          "Lupao",
          "Pantabangan",
          "Rizal",
          "Talugtug",
        ],
        CD3: [
          "Bongabon",
          "Gabaldon",
          "Gen M. Natividad",
          "Laur",
          "Palayan City",
          "Santa Rosa North",
          "Santa Rosa South",
        ],
        CD4: [
          "Cabiao",
          "General Tinio",
          "Jaen North",
          "Jaen South",
          "Peñaranda",
          "San Antonio",
          "San Isidro",
          "San Leonardo",
        ],
      };

      // Original district mapping (for "All District" view)
      const originalDistricts = [
        { id: 1, name: "District I" },
        { id: 2, name: "District II" },
        { id: 3, name: "District III" },
        { id: 4, name: "District IV" },
      ];

      // Get all municipalities for "All Districts" view
      const allMunicipalities = [];
      for (const district in districtSchoolsMapping) {
        districtSchoolsMapping[district].forEach((municipality) => {
          if (!allMunicipalities.includes(municipality)) {
            allMunicipalities.push(municipality);
          }
        });
      }
      // Sort municipalities alphabetically
      allMunicipalities.sort();

      // Global variables
      let schoolData = [];
      let filteredSchoolData = [];
      const itemsPerPage = 10;
      let currentPage = 1;
      let currentDistrict = null;
      let currentMunicipality = null;
      let employeeData = {};
      let retirementData = {};
      let serviceData = {};

      function closeAllModals() {
        document.querySelectorAll(".modal").forEach((modal) => {
          const bootstrapModal = bootstrap.Modal.getInstance(modal);
          if (bootstrapModal) {
            bootstrapModal.hide();
          }
        });
      }

      function loadComponent(componentName, containerId, callback) {
        const container = document.getElementById(containerId);
        if (!container) return;

        const xhr = new XMLHttpRequest();
        xhr.open("GET", componentName, true);
        xhr.onload = function () {
          if (xhr.status === 200) {
            container.innerHTML = xhr.responseText;
            if (callback && typeof callback === "function") {
              callback();
            }
          } else {
            console.error(`Failed to load ${componentName}`);
            container.innerHTML = `<p>Error loading ${componentName}. Please refresh the page.</p>`;
          }
        };
        xhr.onerror = function () {
          console.error(`Network error while loading ${componentName}`);
          container.innerHTML = `<p>Network error. Please try again later.</p>`;
        };
        xhr.send();
      }

      function initializeSidebar() {
        setTimeout(() => {
          const navLinks = document.querySelectorAll(".sidebar nav a");
          if (!navLinks.length) return;

          const currentPage = window.location.pathname.split("/").pop();
          navLinks.forEach((link) => {
            if (link.getAttribute("href") === currentPage) {
              link.classList.add("active");
            }
          });

          navLinks.forEach((link) =>
            link.addEventListener("click", function () {
              document
                .querySelectorAll(".sidebar nav a")
                .forEach((el) => el.classList.remove("active"));
              this.classList.add("active");
            })
          );
        }, 200);
      }

      function initializeTopbar() {
        setTimeout(() => {
          const sidebarToggle = document.getElementById("sidebarToggle");
          const sidebar = document.querySelector(".sidebar");

          if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener("click", () => {
              sidebar.classList.toggle("show");
            });

            document.addEventListener("click", (e) => {
              if (
                !sidebar.contains(e.target) &&
                !sidebarToggle.contains(e.target)
              ) {
                sidebar.classList.remove("show");
              }
            });
          }
        }, 200);
      }

      // Set up district filter dropdown
      function setupDistrictFilter() {
        const districtFilter = document.getElementById("districtFilter");
        if (!districtFilter) return;

        districtFilter.addEventListener("change", function () {
          const selectedValue = this.value;

          if (selectedValue === "all") {
            // Show all municipalities
            showAllMunicipalityCards();
          } else if (selectedValue === "ALL") {
            // Show all district cards
            showAllDistrictCards();
          } else {
            // Show municipality cards for the selected CD district
            showMunicipalityCards(selectedValue);
          }
        });
      }

      // Show all municipality cards (for "All Districts" option)
      function showAllMunicipalityCards() {
        const cardsContainer = document.getElementById(
          "district-cards-container"
        );
        if (!cardsContainer) return;

        // Clear existing cards
        cardsContainer.innerHTML = "";

        // Generate a card for each municipality
        allMunicipalities.forEach((municipality) => {
          // Skip CD1-CD4 as requested
          if (["CD1", "CD2", "CD3", "CD4"].includes(municipality)) {
            return;
          }

          // Find which CD district this municipality belongs to
          let cdDistrict = "";
          for (const [district, municipalities] of Object.entries(
            districtSchoolsMapping
          )) {
            if (municipalities.includes(municipality)) {
              cdDistrict = district;
              break;
            }
          }

          const municipalityId = municipality.replace(/\s+/g, "-");
          const cardHtml = `
  <div class="col-md-6">
    <div class="district-card" data-municipality="${municipality}" data-cd-district="${cdDistrict}">
      <i class="bi bi-grid-3x3-gap grid-icon"></i>
      <h2 style="color: var(--primary-green);">${municipality}</h2>
      <div class="row mt-4">
        <div class="col-4">
          <div class="metric-label">
            <i class="bi bi-person"></i> Total Employees
          </div>
          <div class="metric-value clickable" id="totEmp-${municipalityId}" onclick="showMunicipalityEmployees('${municipality}', '${cdDistrict}')">
            <div class="spinner-border text-success" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 3px;">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="metric-label">
            <i class="bi bi-clock"></i> Near Retirement
          </div>
          <div class="metric-value clickable" id="RetireEmp-${municipalityId}" onclick="showMunicipalityRetirement('${municipality}', '${cdDistrict}')">
            <div class="spinner-border text-success" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 3px;">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="metric-label">
            <i class="bi bi-award"></i> Avg. Years Service
          </div>
          <div class="metric-value" id="avgServe-${municipalityId}">
            <div class="spinner-border text-success" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 3px;">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
`;

          cardsContainer.innerHTML += cardHtml;
        });

        // Update metrics for all municipalities
        updateAllMunicipalityMetrics();
      }

      // Update metrics for all municipalities
      function updateAllMunicipalityMetrics() {
        // For each district, update its municipalities
        for (const [cdDistrict, municipalities] of Object.entries(
          districtSchoolsMapping
        )) {
          // Determine which district number this CD corresponds to
          let districtNumber;
          switch (cdDistrict) {
            case "CD1":
              districtNumber = 1;
              break;
            case "CD2":
              districtNumber = 2;
              break;
            case "CD3":
              districtNumber = 3;
              break;
            case "CD4":
              districtNumber = 4;
              break;
            default:
              continue;
          }

          // Fetch municipality-specific data
          fetch("phpp/dashboard/getMunicipalityData.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
              districtNum: districtNumber,
              municipalities: municipalities,
            }),
          })
            .then((response) => response.json())
            .then((data) => {
              if (data.success) {
                // Process municipality data
                const municipalityData = data.data || {};

                // Update each municipality's metrics
                municipalities.forEach((municipality) => {
                  const municipalityId = municipality.replace(/\s+/g, "-");
                  const metrics = municipalityData[municipality] || {
                    employees: 0,
                    retirement: 0,
                    service: 0,
                  };

                  // Update employee count
                  const empElement = document.getElementById(
                    `totEmp-${municipalityId}`
                  );
                  if (empElement) {
                    empElement.textContent = metrics.employees || 0;
                  }

                  // Update retirement count
                  const retireElement = document.getElementById(
                    `RetireEmp-${municipalityId}`
                  );
                  if (retireElement) {
                    retireElement.textContent = metrics.retirement || 0;
                  }

                  // Update average service years
                  const serviceElement = document.getElementById(
                    `avgServe-${municipalityId}`
                  );
                  if (serviceElement) {
                    serviceElement.textContent = metrics.service || 0;
                  }
                });
              } else {
                console.error(
                  "Error fetching municipality data:",
                  data.message
                );

                // Fallback: Distribute district metrics proportionally
                distributeMunicipalityMetrics(municipalities, districtNumber);
              }
            })
            .catch((error) => {
              console.error("Error fetching municipality data:", error);

              // Fallback: Distribute district metrics proportionally
              distributeMunicipalityMetrics(municipalities, districtNumber);
            });
        }
      }

      // Show all district cards (original view)
      function showAllDistrictCards() {
        const cardsContainer = document.getElementById(
          "district-cards-container"
        );
        {
          const cardsContainer = document.getElementById(
            "district-cards-container"
          );
          if (!cardsContainer) return;

          // Clear existing cards
          cardsContainer.innerHTML = "";

          // Generate the original 4 district cards
          originalDistricts.forEach((district) => {
            const cardHtml = `
  <div class="col-md-6">
    <div class="district-card" data-district="${district.id}">
      <i class="bi bi-grid-3x3-gap grid-icon"></i>
      <h2 style="color: var(--primary-green);">${district.name}</h2>
      <div class="row mt-4">
        <div class="col-4">
          <div class="metric-label">
            <i class="bi bi-person"></i> Total Employees
          </div>
          <div class="metric-value clickable" id="totEmp-${district.id}" onclick="showEmployees(${district.id})">
            <div class="spinner-border text-success" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 3px;">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="metric-label">
            <i class="bi bi-clock"></i> Near Retirement
          </div>
          <div class="metric-value clickable" id="RetireEmp-${district.id}" onclick="showNearRetirement(${district.id})">
            <div class="spinner-border text-success" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 3px;">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="metric-label">
            <i class="bi bi-award"></i> Avg. Years Service
          </div>
          <div class="metric-value" id="avgServe-${district.id}">
            <div class="spinner-border text-success" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 3px;">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
`;

            cardsContainer.innerHTML += cardHtml;
          });

          // Fetch and update metrics for all districts
          fetchAllDistrictsData();
        }
      }
      // Show municipality cards for a specific CD district
      function showMunicipalityCards(cdDistrict) {
        const cardsContainer = document.getElementById(
          "district-cards-container"
        );
        if (!cardsContainer) return;

        // Get municipalities for this CD district
        const municipalities = districtSchoolsMapping[cdDistrict] || [];
        if (municipalities.length === 0) return;

        // Clear existing cards
        cardsContainer.innerHTML = "";

        // Generate a card for each municipality
        municipalities.forEach((municipality) => {
          const municipalityId = municipality.replace(/\s+/g, "-");
          const cardHtml = `
  <div class="col-md-6">
    <div class="district-card" data-municipality="${municipality}" data-cd-district="${cdDistrict}">
      <i class="bi bi-grid-3x3-gap grid-icon"></i>
      <h2 style="color: var(--primary-green);">${municipality}</h2>
      <div class="row mt-4">
        <div class="col-4">
          <div class="metric-label">
            <i class="bi bi-person"></i> Total Employees
          </div>
          <div class="metric-value clickable" id="totEmp-${municipalityId}" onclick="showMunicipalityEmployees('${municipality}', '${cdDistrict}')">
            <div class="spinner-border text-success" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 3px;">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="metric-label">
            <i class="bi bi-clock"></i> Near Retirement
          </div>
          <div class="metric-value clickable" id="RetireEmp-${municipalityId}" onclick="showMunicipalityRetirement('${municipality}', '${cdDistrict}')">
            <div class="spinner-border text-success" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 3px;">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
        <div class="col-4">
          <div class="metric-label">
            <i class="bi bi-award"></i> Avg. Years Service
          </div>
          <div class="metric-value" id="avgServe-${municipalityId}">
            <div class="spinner-border text-success" role="status" style="width: 1.5rem; height: 1.5rem; border-width: 3px;">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
`;

          cardsContainer.innerHTML += cardHtml;
        });

        // Update metrics for each municipality
        updateMunicipalityMetrics(cdDistrict);
      }

      // Fetch data for all districts
      function fetchAllDistrictsData() {
        // Fetch total employees data
        fetch("phpp/dashboard/Total_Employees.php")
          .then((response) => response.json())
          .then((responseData) => {
            if (responseData.success) {
              const districtData = responseData.data;
              employeeData = districtData;

              // Update employee counts
              Object.keys(districtData).forEach((district) => {
                const element = document.getElementById(`totEmp-${district}`);
                if (element) {
                  element.textContent = districtData[district];
                }
              });
            } else {
              console.error(
                "Error fetching employee data:",
                responseData.message
              );
            }
          })
          .catch((error) => {
            console.error("Error fetching total employees:", error);
          });

        // Fetch retirement data
        fetch("phpp/dashboard/Total_Retirement.php")
          .then((response) => response.json())
          .then((responseData) => {
            if (responseData.success) {
              const districtData = responseData.data;
              retirementData = districtData;

              // Update retirement counts
              Object.keys(districtData).forEach((district) => {
                const element = document.getElementById(
                  `RetireEmp-${district}`
                );
                if (element) {
                  element.textContent = districtData[district];
                }
              });
            } else {
              console.error("Error fetching data:", responseData.message);
            }
          })
          .catch((error) => {
            console.error("Error fetching total retirement:", error);
          });

        // Fetch average service data
        fetch("phpp/dashboard/AvgService.php")
          .then((response) => response.json())
          .then((responseData) => {
            if (responseData.success) {
              const districtData = responseData.data;
              serviceData = districtData;

              // Update average service years
              Object.keys(districtData).forEach((district) => {
                const element = document.getElementById(`avgServe-${district}`);
                if (element) {
                  element.textContent = districtData[district];
                }
              });
            } else {
              console.error("Error fetching data:", responseData.message);
            }
          })
          .catch((error) => {
            console.error("Error fetching average service:", error);
          });
      }

      // Update metrics for all municipalities in a CD district
      function updateMunicipalityMetrics(cdDistrict) {
        // Get municipalities for this CD district
        const municipalities = districtSchoolsMapping[cdDistrict] || [];
        if (municipalities.length === 0) return;

        // Determine which district number this CD corresponds to
        let districtNumber;
        switch (cdDistrict) {
          case "CD1":
            districtNumber = 1;
            break;
          case "CD2":
            districtNumber = 2;
            break;
          case "CD3":
            districtNumber = 3;
            break;
          case "CD4":
            districtNumber = 4;
            break;
          default:
            return;
        }

        // Fetch municipality-specific data
        fetch("phpp/dashboard/getMunicipalityData.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            districtNum: districtNumber,
            municipalities: municipalities,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Process municipality data
              const municipalityData = data.data || {};

              // Update each municipality's metrics
              municipalities.forEach((municipality) => {
                const municipalityId = municipality.replace(/\s+/g, "-");
                const metrics = municipalityData[municipality] || {
                  employees: 0,
                  retirement: 0,
                  service: 0,
                };

                // Update employee count
                const empElement = document.getElementById(
                  `totEmp-${municipalityId}`
                );
                if (empElement) {
                  empElement.textContent = metrics.employees || 0;
                }

                // Update retirement count
                const retireElement = document.getElementById(
                  `RetireEmp-${municipalityId}`
                );
                if (retireElement) {
                  retireElement.textContent = metrics.retirement || 0;
                }

                // Update average service years
                const serviceElement = document.getElementById(
                  `avgServe-${municipalityId}`
                );
                if (serviceElement) {
                  serviceElement.textContent = metrics.service || 0;
                }
              });
            } else {
              console.error("Error fetching municipality data:", data.message);

              // Fallback: Distribute district metrics proportionally
              distributeMunicipalityMetrics(municipalities, districtNumber);
            }
          })
          .catch((error) => {
            console.error("Error fetching municipality data:", error);

            // Fallback: Distribute district metrics proportionally
            distributeMunicipalityMetrics(municipalities, districtNumber);
          });
      }

      // Fallback function to distribute district metrics proportionally
      function distributeMunicipalityMetrics(municipalities, districtNumber) {
        if (!municipalities || municipalities.length === 0) return;

        // Get district totals
        const totalEmployees = employeeData[districtNumber] || 0;
        const totalRetirement = retirementData[districtNumber] || 0;
        const avgService = serviceData[districtNumber] || 0;

        // Calculate per-municipality values (simple distribution)
        const municipalityCount = municipalities.length;
        const employeesPerMunicipality = Math.ceil(
          totalEmployees / municipalityCount
        );
        const retirementPerMunicipality = Math.ceil(
          totalRetirement / municipalityCount
        );

        // Update each municipality's metrics
        municipalities.forEach((municipality) => {
          const municipalityId = municipality.replace(/\s+/g, "-");

          // Update employee count
          const empElement = document.getElementById(
            `totEmp-${municipalityId}`
          );
          if (empElement) {
            empElement.textContent = employeesPerMunicipality;
          }

          // Update retirement count
          const retireElement = document.getElementById(
            `RetireEmp-${municipalityId}`
          );
          if (retireElement) {
            retireElement.textContent = retirementPerMunicipality;
          }

          // Update average service years (same as district)
          const serviceElement = document.getElementById(
            `avgServe-${municipalityId}`
          );
          if (serviceElement) {
            serviceElement.textContent = avgService;
          }
        });
      }

      // Show employees for a specific municipality
      function showMunicipalityEmployees(municipality, cdDistrict) {
        closeAllModals();

        // Store current municipality
        currentMunicipality = municipality;

        // Determine which district number this CD corresponds to
        let districtNumber;
        switch (cdDistrict) {
          case "CD1":
            districtNumber = 1;
            break;
          case "CD2":
            districtNumber = 2;
            break;
          case "CD3":
            districtNumber = 3;
            break;
          case "CD4":
            districtNumber = 4;
            break;
          default:
            return;
        }

        // Store current district
        currentDistrict = districtNumber;

        // Update modal title
        document.getElementById("employeeDistrictName").textContent =
          municipality;

        // Show the modal
        const modal = new bootstrap.Modal(
          document.getElementById("employeeModal")
        );
        modal.show();

        // Fetch employee data for this municipality
        fetch("phpp/dashboard/getMunicipalityEmployees.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            districtNum: districtNumber,
            municipality: municipality,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            schoolData = data;
            filteredSchoolData = [...schoolData]; // Initialize with full data
            displaySchoolPage(1);
            setupPagination(filteredSchoolData.length);
          })
          .catch((error) => {
            console.error("Error fetching municipality employee data:", error);
            const tableBody = document.getElementById("employeeTableBody");
            tableBody.innerHTML =
              "<tr><td colspan='4'>Error loading data. Please try again.</td></tr>";
          });

        // Set up search functionality
        addSearchFunctionality("employeeSearchInput", "employeeTableBody");
      }

      // Show near retirement employees for a specific municipality
      function showMunicipalityRetirement(municipality, cdDistrict) {
        closeAllModals();

        // Store current municipality
        currentMunicipality = municipality;

        // Determine which district number this CD corresponds to
        let districtNumber;
        switch (cdDistrict) {
          case "CD1":
            districtNumber = 1;
            break;
          case "CD2":
            districtNumber = 2;
            break;
          case "CD3":
            districtNumber = 3;
            break;
          case "CD4":
            districtNumber = 4;
            break;
          default:
            return;
        }

        // Update modal title
        document.getElementById("districtNum").textContent = municipality;

        // Show the modal
        const modal = new bootstrap.Modal(
          document.getElementById("nearRetirementModal")
        );
        modal.show();

        // Fetch retirement data for this municipality
        fetch("phpp/dashboard/getMunicipalityRetirement.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({
            districtNum: districtNumber,
            municipality: municipality,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            const tableBody = document.getElementById(
              "nearRetirementTableBody"
            );
            tableBody.innerHTML = ""; // Clear previous data

            if (data.length === 0) {
              tableBody.innerHTML =
                "<tr><td colspan='4'>No near retirement employees found.</td></tr>";
              return;
            }

            data.forEach((employee) => {
              const row = `
            <tr>
              <td data-label="SID">${employee.SchoolID}</td>
              <td data-label="School Name">${employee.schoolName}</td>
              <td data-label="Total Retirees">${
                employee.total_retirement
              }</td>
              <td data-label="Action">
                <button class="btn btn-danger btn-sm" onclick="showRetirementDetails('${
                  employee.SchoolID
                }', '${encodeURIComponent(employee.schoolName)}')">
                  View
                </button>
              </td>
            </tr>
          `;
              tableBody.insertAdjacentHTML("beforeend", row);
            });
          })
          .catch((error) => {
            console.error(
              "Error fetching municipality retirement data:",
              error
            );
            const tableBody = document.getElementById(
              "nearRetirementTableBody"
            );
            tableBody.innerHTML =
              "<tr><td colspan='4'>Error loading data. Please try again.</td></tr>";
          });

        // Set up search functionality
        document
          .getElementById("searchInput")
          .addEventListener("input", function () {
            const filter = this.value.toLowerCase();
            document
              .querySelectorAll("#nearRetirementTableBody tr")
              .forEach((row) => {
                row.style.display = row.textContent
                  .toLowerCase()
                  .includes(filter)
                  ? ""
                  : "none";
              });
          });
      }

      function redirectToProfile() {
        window.location.href = "profile.html";
      }

      function showRetirementDetails(schoolID, schoolName) {
        closeAllModals(); // Close all open modals
        document.getElementById("schoolNameTitle").textContent = schoolName;
        const modal = new bootstrap.Modal(
          document.getElementById("employeeDetailsModal")
        );
        modal.show();
        const tableBody = document.getElementById("employeeDetailsTableBody");
        tableBody.innerHTML = "<tr><td colspan='4'>Loading...</td></tr>";

        // Fetch employee data from the database
        fetch("phpp/dashboard/RetirementView.php?schoolID=" + schoolID)
          .then((response) => response.json())
          .then((data) => {
            tableBody.innerHTML = ""; // Clear loading text
            if (data.length === 0) {
              tableBody.innerHTML =
                "<tr><td colspan='4'>No employees found.</td></tr>";
            } else {
              data.forEach((employee) => {
                const row = `
                <tr>
                  <td data-label="Employee No">${employee.employee_no}</td>
                  <td data-label="Name">${employee.name}</td>
                  <td data-label="Age">${employee.age}</td>
                  <td data-label="Position">${employee.position}</td>
                </tr>
              `;
                tableBody.innerHTML += row;
              });
            }
          })
          .catch((error) => {
            console.error("Error fetching employee data:", error);
            tableBody.innerHTML =
              "<tr><td colspan='4'>Error loading data.</td></tr>";
          });
      }

      function showEmployees(district) {
        closeAllModals();

        // Clear current municipality
        currentMunicipality = null;

        // Store current district
        currentDistrict = district;

        document.getElementById(
          "employeeDistrictName"
        ).textContent = `District ${district}`;
        const modal = new bootstrap.Modal(
          document.getElementById("employeeModal")
        );
        modal.show();

        fetch("phpp/dashboard/getEmployees.php", {
          method: "POST",
          headers: { "Content-Type": "application/json" },
          body: JSON.stringify({ districtNum: district }),
        })
          .then((response) => response.json())
          .then((data) => {
            schoolData = data;
            filteredSchoolData = [...schoolData]; // Initialize with full data
            displaySchoolPage(1);
            setupPagination(filteredSchoolData.length);
          })
          .catch((error) =>
            console.error("Error fetching employee data:", error)
          );

        addSearchFunctionality("employeeSearchInput", "employeeTableBody");
      }

      function displaySchoolPage(page) {
        const tableBody = document.getElementById("employeeTableBody");
        tableBody.innerHTML = "";

        const start = (page - 1) * itemsPerPage;
        const end = start + itemsPerPage;
        const pageData = filteredSchoolData.slice(start, end);

        if (pageData.length === 0) {
          tableBody.innerHTML =
            "<tr><td colspan='4'>No matching records found.</td></tr>";
          return;
        }

        pageData.forEach((school) => {
          tableBody.innerHTML += `
          <tr>
            <td data-label="SID">${school.SchoolID}</td>
            <td data-label="School Name">${school.schoolName}</td>
            <td data-label="Total Employees">${school.total_employees}</td>
            <td data-label="Action"><button class="btn btn-danger btn-sm" onclick="showSchoolEmployees('${school.schoolName}', ${school.SchoolID})">View</button></td>
          </tr>
        `;
        });
      }

      function setupPagination(totalItems) {
        const totalPages = Math.ceil(totalItems / itemsPerPage);
        const paginationElement = document.getElementById("employeePagination");
        paginationElement.innerHTML = "";

        if (totalPages <= 1) return;

        let pages = [];
        const range = 2; // Number of pages to show before/after current page

        for (let i = 1; i <= totalPages; i++) {
          if (
            i === 1 ||
            i === totalPages ||
            (i >= currentPage - range && i <= currentPage + range)
          ) {
            pages.push(i);
          } else if (pages[pages.length - 1] !== "...") {
            pages.push("...");
          }
        }

        pages.forEach((page) => {
          if (page === "...") {
            paginationElement.innerHTML += `<li class="page-item disabled"><span class="page-link">...</span></li>`;
          } else {
            paginationElement.innerHTML += `
            <li class="page-item ${page === currentPage ? "active" : ""}">
              <a class="page-link" href="#" onclick="changePage(${page})">${page}</a>
            </li>
          `;
          }
        });
      }

      function changePage(page) {
        if (currentPage !== page) {
          currentPage = page;
          displaySchoolPage(page);
          setupPagination(filteredSchoolData.length);
        }
      }

      function addSearchFunctionality(inputId, tableBodyId) {
        document.getElementById(inputId).addEventListener("input", function () {
          const filter = this.value.toLowerCase();
          filteredSchoolData = schoolData.filter((school) =>
            Object.values(school).some((value) =>
              value.toString().toLowerCase().includes(filter)
            )
          );

          currentPage = 1; // Reset to first page when searching
          displaySchoolPage(currentPage);
          setupPagination(filteredSchoolData.length);
        });
      }

      function displayFilteredSchoolData(filteredData) {
        const tableBody = document.getElementById("employeeTableBody");
        tableBody.innerHTML = filteredData
          .map(
            (school) => `
          <tr>
            <td data-label="SID">${school.SchoolID}</td>
            <td data-label="School Name">${school.schoolName}</td>
            <td data-label="Total Employees">${school.total_employees}</td>
            <td data-label="Action"><button class="btn btn-danger btn-sm" onclick="showSchoolEmployees('${school.schoolName}', ${school.SchoolID})">View</button></td>
          </tr>
        `
          )
          .join("");
      }

      function showSchoolEmployees(schoolName, schoolID) {
        closeAllModals(); // Close all open modals
        document.getElementById("schoolNameTitle").textContent = schoolName;
        const modal = new bootstrap.Modal(
          document.getElementById("employeeDetailsModal")
        );
        modal.show();
        const tableBody = document.getElementById("employeeDetailsTableBody");
        tableBody.innerHTML = "<tr><td colspan='4'>Loading...</td></tr>";

        // Fetch employee data from the database
        fetch("phpp/dashboard/EmpView.php?schoolID=" + schoolID)
          .then((response) => response.json())
          .then((data) => {
            tableBody.innerHTML = ""; // Clear loading text
            if (data.length === 0) {
              tableBody.innerHTML =
                "<tr><td colspan='4'>No employees found.</td></tr>";
            } else {
              data.forEach((employee) => {
                const row = `
                <tr>
                  <td data-label="Employee No">${employee.employee_no}</td>
                  <td data-label="Name">${employee.name}</td>
                  <td data-label="Age">${employee.age}</td>
                  <td data-label="Position">${employee.position}</td>
                </tr>
              `;
                tableBody.innerHTML += row;
              });
            }
          })
          .catch((error) => {
            console.error("Error fetching employee data:", error);
            tableBody.innerHTML =
              "<tr><td colspan='4'>Error loading data.</td></tr>";
          });
      }

      function showNearRetirement(districtNum) {
        closeAllModals();

        // Clear current municipality
        currentMunicipality = null;

        document.getElementById(
          "districtNum"
        ).textContent = `District ${districtNum}`;
        const modal = new bootstrap.Modal(
          document.getElementById("nearRetirementModal")
        );
        modal.show();

        const tableBody = document.getElementById("nearRetirementTableBody");
        tableBody.innerHTML = ""; // Clear previous data

        // Prepare JSON data
        const requestData = { districtNum: districtNum };

        // Fetch data from PHP using POST (JSON format)
        fetch("phpp/dashboard/getRetirement.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify(requestData),
        })
          .then((response) => response.json())
          .then((data) => {
            data.forEach((employee) => {
              const row = `
              <tr>
                <td data-label="SID">${employee.SchoolID}</td>
                <td data-label="School Name">${employee.schoolName}</td>
                <td data-label="Total Retirees">${
                  employee.near_retirement_count
                }</td>
                <td data-label="Action">
                  <button class="btn btn-danger btn-sm" onclick="showRetirementDetails('${
                    employee.SchoolID
                  }', '${encodeURIComponent(employee.schoolName)}')">
                    View
                  </button>
                </td>
              </tr>
            `;
              tableBody.insertAdjacentHTML("beforeend", row);
            });
          })
          .catch((error) => console.error("Error fetching data:", error));

        // Search filter
        document
          .getElementById("searchInput")
          .addEventListener("input", function () {
            const filter = this.value.toLowerCase();
            document
              .querySelectorAll("#nearRetirementTableBody tr")
              .forEach((row) => {
                row.style.display = row.textContent
                  .toLowerCase()
                  .includes(filter)
                  ? ""
                  : "none";
              });
          });
      }

      // Toggle filter options visibility
      function toggleFilterOptions(modalId) {
        const filterOptions = document.getElementById(`filterOptions-${modalId}`);
        if (filterOptions) {
          filterOptions.style.display = filterOptions.style.display === "none" ? "block" : "none";

          // Hide export options when showing filter options
          const exportOptions = document.getElementById(`exportOptions-${modalId}`);
          if (exportOptions && filterOptions.style.display === "block") {
            exportOptions.style.display = "none";
          }
        }
      }

      // Toggle export options visibility
      function toggleExportOptions(modalId) {
        const exportOptions = document.getElementById(`exportOptions-${modalId}`);
        if (exportOptions) {
          exportOptions.style.display = exportOptions.style.display === "none" ? "block" : "none";

          // Hide filter options when showing export options
          const filterOptions = document.getElementById(`filterOptions-${modalId}`);
          if (filterOptions && exportOptions.style.display === "block") {
            filterOptions.style.display = "none";
          }
        }
      }

      // Clear all filter fields
      function clearFilters(modalId) {
        const filterFields = document.querySelectorAll(`#filterFields-${modalId} .filter-field`);
        const rangeFilterFields = document.querySelectorAll(`#filterFields-${modalId} .filter-field-range`);
        
        filterFields.forEach((field) => {
          field.value = "";
        });
        
        rangeFilterFields.forEach((field) => {
          field.value = "";
        });
      }

      // Apply filters to the table
      function applyFilters(modalId, tableBodyId) {
        const filterFields = document.querySelectorAll(`#filterFields-${modalId} .filter-field`);
        const rangeFilterFields = document.querySelectorAll(`#filterFields-${modalId} .filter-field-range`);
        const filters = [];
        const rangeFilters = {};

        // Process regular text filters
        filterFields.forEach((field) => {
          if (field.value.trim() !== "") {
            filters.push({
              column: Number.parseInt(field.dataset.column),
              value: field.value.toLowerCase(),
            });
          }
        });

        // Process range filters
        rangeFilterFields.forEach((field) => {
          if (field.value.trim() !== "") {
            const column = Number.parseInt(field.dataset.column);
            const range = field.dataset.range;
            
            if (!rangeFilters[column]) {
              rangeFilters[column] = {};
            }
            
            rangeFilters[column][range] = Number.parseFloat(field.value);
          }
        });

        // Apply both types of filters
        filterTableByFiltersAndRanges(filters, rangeFilters, tableBodyId);
        toggleFilterOptions(modalId); // Hide filter options after applying
      }

      // Filter table by multiple column filters
      function filterTableByFiltersAndRanges(filters, rangeFilters, tableBodyId) {
        const tableBody = document.getElementById(tableBodyId);
        if (!tableBody) return;

        const rows = tableBody.querySelectorAll("tr");

        rows.forEach((row) => {
          const cells = row.querySelectorAll("td");
          let showRow = true;

          // Apply text filters
          filters.forEach((filter) => {
            if (filter.column < cells.length) {
              const cellText = cells[filter.column].textContent.toLowerCase();
              if (!cellText.includes(filter.value)) {
                showRow = false;
              }
            }
          });

          // Apply range filters
          if (showRow) {
            for (const column in rangeFilters) {
              if (column < cells.length) {
                const cellValue = parseFloat(cells[column].textContent.replace(/[^\d.-]/g, ''));
                
                // Skip if not a valid number
                if (isNaN(cellValue)) continue;
                
                const range = rangeFilters[column];
                
                // Check min value if specified
                if (range.min !== undefined && cellValue < range.min) {
                  showRow = false;
                  break;
                }
                
                // Check max value if specified
                if (range.max !== undefined && cellValue > range.max) {
                  showRow = false;
                  break;
                }
              }
            }
          }

          row.style.display = showRow ? "" : "none";
        });
      }

      // Export table data to CSV
      function exportTableData(modalId, tableBodyId) {
        const tableBody = document.getElementById(tableBodyId);
        const table = tableBody ? tableBody.closest("table") : null;

        if (!table) {
          alert("Table not found!");
          return;
        }

        // Get selected columns
        const selectedColumns = [];
        const checkboxes = document.querySelectorAll(`#exportFields-${modalId} .export-column:checked`);

        checkboxes.forEach((checkbox) => {
          selectedColumns.push(Number.parseInt(checkbox.value));
        });

        if (selectedColumns.length === 0) {
          alert("Please select at least one column to export!");
          return;
        }

        // Get table headers
        const headers = [];
        const headerCells = table.querySelectorAll("thead th");

        selectedColumns.forEach((colIndex) => {
          if (colIndex < headerCells.length) {
            headers.push(headerCells[colIndex].textContent.trim());
          }
        });

        // Get visible rows data
        const rows = [];
        const dataCells = tableBody.querySelectorAll('tr:not([style*="display: none"]):not([style*="display:none"])');

        dataCells.forEach((row) => {
          const cells = row.querySelectorAll("td");
          const rowData = [];

          selectedColumns.forEach((colIndex) => {
            if (colIndex < cells.length) {
              // Escape quotes in cell content
              let cellContent = cells[colIndex].textContent.trim();
              cellContent = cellContent.replace(/"/g, '""');
              rowData.push(`"${cellContent}"`);
            } else {
              rowData.push('""');
            }
          });

          if (rowData.length > 0) {
            rows.push(rowData.join(","));
          }
        });

        // Create CSV content
        const csvContent = [headers.join(","), ...rows].join("\n");

        // Get modal title for filename
        const modalTitle = document.querySelector(`#${modalId} .modal-title`).textContent.trim();
        const filename = `${modalTitle.replace(/[^a-z0-9]/gi, "_").toLowerCase()}_export.csv`;

        // Create download link
        const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
        const url = URL.createObjectURL(blob);
        const link = document.createElement("a");
        link.setAttribute("href", url);
        link.setAttribute("download", filename);
        link.style.visibility = "hidden";
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        // Hide export options
        toggleExportOptions(modalId);
      }

      function exportToExcel(type = "retirement") {
        let data = [];
        let filename = "";
        let headers = [];

        if (type === "employees") {
          // Export employee data
          data = filteredSchoolData;
          headers = ["School ID", "School Name", "Total Employees"];

          if (currentMunicipality) {
            filename = `Employees_${currentMunicipality.replace(
              /\s+/g,
              "_"
            )}.csv`;
          } else {
            filename = `Employees_District_${currentDistrict}.csv`;
          }

          // Map data to CSV format
          const csvContent = [
            headers.join(","),
            ...data.map((item) =>
              [
                item.SchoolID,
                `"${item.schoolName.replace(/"/g, '""')}"`,
                item.total_employees,
              ].join(",")
            ),
          ].join("\n");

          downloadCSV(csvContent, filename);
        } else if (type === "service") {
          // Export service data
          const tableBody = document.getElementById("serviceTableBody");
          const rows = tableBody.querySelectorAll("tr");

          headers = [
            "School Name",
            "Total Employees",
            "Average Years of Service",
          ];

          if (currentMunicipality) {
            filename = `Service_${currentMunicipality.replace(
              /\s+/g,
              "_"
            )}.csv`;
          } else {
            filename = `Service_District_${currentDistrict}.csv`;
          }

          // Extract data from table
          rows.forEach((row) => {
            const cells = row.querySelectorAll("td");
            if (cells.length >= 3) {
              data.push({
                schoolName: cells[0].textContent,
                employees: cells[1].textContent,
                service: cells[2].textContent,
              });
            }
          });

          // Map data to CSV format
          const csvContent = [
            headers.join(","),
            ...data.map((item) =>
              [
                `"${item.schoolName.replace(/"/g, '""')}"`,
                item.employees,
                item.service,
              ].join(",")
            ),
          ].join("\n");

          downloadCSV(csvContent, filename);
        } else {
          // Export retirement data (default)
          const tableBody = document.getElementById("nearRetirementTableBody");
          const rows = tableBody.querySelectorAll("tr");

          headers = ["School ID", "School Name", "Total Retirees"];

          if (currentMunicipality) {
            filename = `Retirement_${currentMunicipality.replace(
              /\s+/g,
              "_"
            )}.csv`;
          } else {
            filename = `Retirement_District_${currentDistrict}.csv`;
          }

          // Extract data from table
          rows.forEach((row) => {
            const cells = row.querySelectorAll("td");
            if (cells.length >= 3) {
              data.push({
                schoolId: cells[0].textContent,
                schoolName: cells[1].textContent,
                retirees: cells[2].textContent,
              });
            }
          });

          // Map data to CSV format
          const csvContent = [
            headers.join(","),
            ...data.map((item) =>
              [
                item.schoolId,
                `"${item.schoolName.replace(/"/g, '""')}"`,
                item.retirees,
              ].join(",")
            ),
          ].join("\n");

          downloadCSV(csvContent, filename);
        }
      }

      function downloadCSV(csvContent, filename) {
        const encodedUri = encodeURI(
          "data:text/csv;charset=utf-8," + csvContent
        );
        const link = document.createElement("a");
        link.setAttribute("href", encodedUri);
        link.setAttribute("download", filename);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
      }

      document.addEventListener('DOMContentLoaded', function () {
        fetch('phpp/dashboard/dropdownChoices.php')
          .then(response => response.json())
          .then(data => {
            const dropdown = document.getElementById('positionDropdown-employeeDetailsModal');
            data.forEach(position => {
              const option = document.createElement('option');
              option.value = position;
              option.textContent = position;
              dropdown.appendChild(option);
            });
          })
          .catch(error => console.error('Error loading positions:', error));
      });
    </script>
  </body>
</html>

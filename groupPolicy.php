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
    <title>DepEd: Group Policy</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />
    <link rel="stylesheet" href="css/dashboard.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      .main-content {
        display: flex;
        justify-content: center;
        position: relative;
        background-color: #f9f9f9;
        min-height: 100vh;
        overflow: hidden;
        z-index: 1;
        background: url("data:image/svg+xml,%3Csvg width='100%' height='200' viewBox='100 50 1100 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,100 C200,250 400,-50 600,100 C800,250 1000,-50 1200,100' fill='none' stroke='%232e8b57' stroke-width='3' stroke-linecap='round'/%3E%3C/svg%3E")
          repeat-x;
        background-size: cover;
        background-position: center;
      }

      .skeleton-nav-item,
      .skeleton-logo,
      .skeleton-text {
        background-color: #e0e0e0;
        border-radius: 4px;
      }

      /* Optional: Customize skeleton loading animation */
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
      #sidebar-container .sidebar {
  height: 100vh;
  overflow-y: auto; /* Allows vertical scrolling */
}

      /* Group Policy Table Styles */
      .policy-card {
        border-radius: 12px;
        box-shadow: 0 6px 18px rgba(0, 0, 0, 0.1);
        background-color: #fff;
        overflow: hidden;
        margin-bottom: 2rem;
      }

      .policy-card .card-header {
        background-color: #2e8b57;
        color: white;
        font-weight: 600;
        border-bottom: 0;
        padding: 1rem 1.5rem;
      }

      .policy-card .card-header h5 {
        font-size: 1.1rem;
        letter-spacing: 0.02rem;
      }

      .role-selector-container {
        min-width: 180px;
      }

      .role-selector-container .form-select {
        border: 2px solid rgba(255, 255, 255, 0.3);
        background-color: rgba(255, 255, 255, 0.2);
        color: white;
        font-weight: 500;
        padding: 0.5rem 1rem;
        cursor: pointer;
      }

      .role-selector-container .form-select:focus {
        box-shadow: 0 0 0 0.25rem rgba(255, 255, 255, 0.2);
        border-color: rgba(255, 255, 255, 0.5);
      }

      .role-selector-container .form-select option {
        background-color: #fff;
        color: #333;
      }

      .policy-table {
        margin-bottom: 0;
      }

      .policy-table th {
        background-color: #f8f9fa;
        font-weight: 600;
        border-bottom: 2px solid #dee2e6;
        padding: 1rem 0.75rem;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.03rem;
      }

      .policy-table td {
        padding: 0.85rem 0.75rem;
        vertical-align: middle;
      }

      .policy-table .form-check-input {
        width: 1.2rem;
        height: 1.2rem;
        margin-top: 0;
        cursor: pointer;
      }

      .policy-table .form-check-input:checked {
        background-color: #2e8b57;
        border-color: #2e8b57;
      }

      .policy-table .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(46, 139, 87, 0.25);
      }

      .policy-table .module-name {
        font-weight: 600;
        font-size: 1rem;
        color: #333;
        cursor: pointer;
        transition: color 0.2s;
        padding-left: 1rem;
      }

      .policy-table .module-name:hover {
        color: #2e8b57;
      }

      .policy-table .submodule-row {
        background-color: #f8f9fa;
      }

      .policy-table .submodule-name {
        padding-left: 2.5rem;
        color: #555;
        font-size: 0.95rem;
      }

      .card-footer {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #eee;
      }

      .btn {
        font-weight: 500;
        border-radius: 6px;
        transition: all 0.2s;
      }

      .save-btn {
        background-color: #2e8b57;
        border-color: #2e8b57;
        padding-left: 2rem;
        padding-right: 2rem;
      }

      .save-btn:hover {
        background-color: #236b43;
        border-color: #236b43;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(35, 107, 67, 0.2);
      }

      .reset-btn {
        color: #2e8b57;
        border-color: #2e8b57;
        background-color: transparent;
        margin-right: 1rem;
      }

      .reset-btn:hover {
        background-color: #2e8b57;
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(35, 107, 67, 0.2);
      }

      .page-header {
        margin-bottom: 2rem;
        padding: 0 1rem;
      }

      .page-header h2 {
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
      }

      .page-header p {
        font-size: 1.05rem;
        max-width: 600px;
      }

      /* Responsive adjustments */
      @media (max-width: 991.98px) {
        .policy-card .card-header {
          flex-direction: column;
          align-items: flex-start;
          gap: 1rem;
        }
        
        .role-selector-container {
          width: 100%;
        }
        
        .role-selector-container .form-select {
          width: 100%;
        }
      }
      
      @media (max-width: 767.98px) {
        .policy-table {
          font-size: 0.85rem;
        }
        
        .policy-table th, 
        .policy-table td {
          padding: 0.75rem 0.5rem;
        }
        
        .policy-table .submodule-name {
          padding-left: 1.5rem;
        }
        
        .policy-table .module-name {
          padding-left: 0.5rem;
        }
        
        .card-footer {
          flex-direction: column;
          gap: 0.75rem;
        }
        
        .reset-btn {
          margin-right: 0;
          order: 2;
        }
        
        .save-btn {
          order: 1;
        }
      }
    </style>

</head>
  <body>
    <div id="sidebar-container">
      <!-- Basic sidebar structure -->
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
    <div id="topbar-container">
      <!-- Basic topbar structure -->
      <div
        class="top-bar py-3 px-4 text-dark d-flex justify-content-between align-items-center"
      >
        <button class="btn d-md-none action-button" id="sidebarToggle">
          <i class="bi bi-list"></i>
        </button>
        <div class="d-none d-md-block">
          <div class="skeleton-text"></div>
          <div class="skeleton-text"></div>
        </div>
        <div class="d-flex gap-3" style="height: 30px">
          <div class="skeleton-logo"></div>
          <div class="skeleton-logo"></div>
          <div class="skeleton-logo"></div>
        </div>
      </div>
    </div>

    <!-- Main Content - Group Policy Table -->
    <div class="main-content">
      <div class="container py-4">
        <div class="row">
          <div class="col-12 page-header">
            <h2 class="fw-bold text-dark">Group Policy Management</h2>
            <p class="text-muted">Configure access permissions for school heads and district supervisors</p>
          </div>
        </div>

        <div class="row">
          <div class="col-12">
            <div class="policy-card">
              <div class="card-header d-flex justify-content-between align-items-center">
                <div>
                  <h5 class="mb-0">Access Control Settings</h5>
                </div>
                <div class="role-selector-container">
                  <select class="form-select" id="roleSelector">
                    <option value="school_head">School Head</option>
                    <option value="district_supervisor">District Supervisor</option>
                  </select>
                </div>
              </div>
              <div class="card-body p-0">
                <div class="table-responsive">
                  <table class="table table-hover policy-table mb-0">
                    <thead>
                      <tr>
                        <th style="width: 40%">Module / Feature</th>
                        <th class="text-center">View</th>
                        <th class="text-center">Create</th>
                        <th class="text-center">Edit</th>
                        <th class="text-center">Delete</th>
                        <th class="text-center">Approve</th>
                      </tr>
                    </thead>
                    <tbody>
                      <!-- Student Management -->
                      <tr>
                        <td class="module-name">Student Management</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="student-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="student-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="student-edit" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="student-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="student-approve" checked>
                          </div>
                        </td>
                      </tr>
                      <tr class="submodule-row">
                        <td class="submodule-name">Student Records</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="student-records-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="student-records-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="student-records-edit" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="student-records-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="student-records-approve" checked>
                          </div>
                        </td>
                      </tr>
                      <tr class="submodule-row">
                        <td class="submodule-name">Enrollment</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="enrollment-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="enrollment-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="enrollment-edit" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="enrollment-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="enrollment-approve" checked>
                          </div>
                        </td>
                      </tr>

                      <!-- Faculty Management -->
                      <tr>
                        <td class="module-name">Faculty Management</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="faculty-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="faculty-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="faculty-edit" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="faculty-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="faculty-approve" checked>
                          </div>
                        </td>
                      </tr>
                      <tr class="submodule-row">
                        <td class="submodule-name">Teacher Records</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="teacher-records-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="teacher-records-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="teacher-records-edit" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="teacher-records-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="teacher-records-approve" checked>
                          </div>
                        </td>
                      </tr>
                      <tr class="submodule-row">
                        <td class="submodule-name">Workload Assignment</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="workload-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="workload-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="workload-edit" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="workload-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="workload-approve" checked>
                          </div>
                        </td>
                      </tr>

                      <!-- Academic Management -->
                      <tr>
                        <td class="module-name">Academic Management</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="academic-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="academic-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="academic-edit" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="academic-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="academic-approve" checked>
                          </div>
                        </td>
                      </tr>
                      <tr class="submodule-row">
                        <td class="submodule-name">Curriculum</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="curriculum-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="curriculum-create">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="curriculum-edit">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="curriculum-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="curriculum-approve">
                          </div>
                        </td>
                      </tr>
                      <tr class="submodule-row">
                        <td class="submodule-name">Grading System</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="grading-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="grading-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="grading-edit" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="grading-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="grading-approve" checked>
                          </div>
                        </td>
                      </tr>

                      <!-- Reports & Analytics -->
                      <tr>
                        <td class="module-name">Reports & Analytics</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="reports-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="reports-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="reports-edit">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="reports-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="reports-approve">
                          </div>
                        </td>
                      </tr>
                      <tr class="submodule-row">
                        <td class="submodule-name">Performance Reports</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="performance-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="performance-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="performance-edit">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="performance-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="performance-approve">
                          </div>
                        </td>
                      </tr>
                      <tr class="submodule-row">
                        <td class="submodule-name">Attendance Reports</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="attendance-view" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="attendance-create" checked>
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="attendance-edit">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="attendance-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="attendance-approve">
                          </div>
                        </td>
                      </tr>

                      <!-- System Administration -->
                      <tr>
                        <td class="module-name">System Administration</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="system-view">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="system-create">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="system-edit">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="system-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="system-approve">
                          </div>
                        </td>
                      </tr>
                      <tr class="submodule-row">
                        <td class="submodule-name">User Management</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="user-view">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="user-create">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="user-edit">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="user-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="user-approve">
                          </div>
                        </td>
                      </tr>
                      <tr class="submodule-row">
                        <td class="submodule-name">Backup & Restore</td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="backup-view">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="backup-create">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="backup-edit">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="backup-delete">
                          </div>
                        </td>
                        <td class="text-center">
                          <div class="form-check d-flex justify-content-center">
                            <input class="form-check-input" type="checkbox" id="backup-approve">
                          </div>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
              <div class="card-footer bg-white d-flex justify-content-end gap-2">
                <button class="btn reset-btn" id="resetBtn">Reset to Default</button>
                <button class="btn save-btn text-white" id="saveBtn">Save Changes</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        // Load Sidebar and Topbar via AJAX
        loadComponent("sidebar.php", "sidebar-container", initializeSidebar);
        loadComponent("topbar.php", "topbar-container", initializeTopbar);
        
        // Initialize Group Policy functionality
        initializeGroupPolicy();
      });

      // Function to load components (sidebar, topbar) dynamically
      function loadComponent(componentName, containerId, callback) {
        const container = document.getElementById(containerId);

        const xhr = new XMLHttpRequest();
        xhr.open("GET", componentName, true);
        xhr.onload = function () {
          if (xhr.status === 200) {
            container.innerHTML = xhr.responseText;

            // Call the callback (initializeSidebar or initializeTopbar) after content is loaded
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

      // Sidebar initialization (wait for the sidebar to be injected into the DOM)
      function initializeSidebar() {
        setTimeout(() => {
          const navLinks = document.querySelectorAll(".sidebar nav a");
          if (navLinks.length === 0) return; // Prevent errors if links aren't loaded yet

          const currentPage = window.location.pathname.split("/").pop();
          navLinks.forEach((link) => {
            if (link.getAttribute("href") === currentPage) {
              link.classList.add("active");
            }
          });

          navLinks.forEach((link) => {
            link.addEventListener("click", function () {
              document
                .querySelectorAll(".sidebar nav a")
                .forEach((el) => el.classList.remove("active"));
              this.classList.add("active");
            });
          });
        }, 200); // Delay execution to allow DOM update
        // Logout functionality
        document.addEventListener("click", (event) => {
          if (event.target.id === "logoutLink") {
            event.preventDefault();
            Swal.fire({
              title: "Are you sure?",
              text: "You are about to logout!",
              icon: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Yes, logout!",
            }).then((result) => {
              if (result.isConfirmed) {
                window.location.href = "index.php"; // Redirect
              }
            });
          }
        });
      }

      // Topbar initialization (toggle sidebar visibility)
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

      // Initialize Group Policy functionality
      function initializeGroupPolicy() {
        const roleSelector = document.getElementById('roleSelector');
        const saveBtn = document.getElementById('saveBtn');
        const resetBtn = document.getElementById('resetBtn');
        
        // Default permissions for different roles
        const defaultPermissions = {
          school_head: {
            // Student Management permissions
            'student-view': true,
            'student-create': true,
            'student-edit': true,
            'student-delete': false,
            'student-approve': true,
            'student-records-view': true,
            'student-records-create': true,
            'student-records-edit': true,
            'student-records-delete': false,
            'student-records-approve': true,
            'enrollment-view': true,
            'enrollment-create': true,
            'enrollment-edit': true,
            'enrollment-delete': false,
            'enrollment-approve': true,
            
            // Faculty Management permissions
            'faculty-view': true,
            'faculty-create': true,
            'faculty-edit': true,
            'faculty-delete': false,
            'faculty-approve': true,
            'teacher-records-view': true,
            'teacher-records-create': true,
            'teacher-records-edit': true,
            'teacher-records-delete': false,
            'teacher-records-approve': true,
            'workload-view': true,
            'workload-create': true,
            'workload-edit': true,
            'workload-delete': false,
            'workload-approve': true,
            
            // Academic Management permissions
            'academic-view': true,
            'academic-create': true,
            'academic-edit': true,
            'academic-delete': false,
            'academic-approve': true,
            'curriculum-view': true,
            'curriculum-create': false,
            'curriculum-edit': false,
            'curriculum-delete': false,
            'curriculum-approve': false,
            'grading-view': true,
            'grading-create': true,
            'grading-edit': true,
            'grading-delete': false,
            'grading-approve': true,
            
            // Reports & Analytics permissions
            'reports-view': true,
            'reports-create': true,
            'reports-edit': false,
            'reports-delete': false,
            'reports-approve': false,
            'performance-view': true,
            'performance-create': true,
            'performance-edit': false,
            'performance-delete': false,
            'performance-approve': false,
            'attendance-view': true,
            'attendance-create': true,
            'attendance-edit': false,
            'attendance-delete': false,
            'attendance-approve': false,
            
            // System Administration permissions
            'system-view': false,
            'system-create': false,
            'system-edit': false,
            'system-delete': false,
            'system-approve': false,
            'user-view': false,
            'user-create': false,
            'user-edit': false,
            'user-delete': false,
            'user-approve': false,
            'backup-view': false,
            'backup-create': false,
            'backup-edit': false,
            'backup-delete': false,
            'backup-approve': false
          },
          district_supervisor: {
            // Student Management permissions
            'student-view': true,
            'student-create': false,
            'student-edit': false,
            'student-delete': false,
            'student-approve': true,
            'student-records-view': true,
            'student-records-create': false,
            'student-records-edit': false,
            'student-records-delete': false,
            'student-records-approve': true,
            'enrollment-view': true,
            'enrollment-create': false,
            'enrollment-edit': false,
            'enrollment-delete': false,
            'enrollment-approve': true,
            
            // Faculty Management permissions
            'faculty-view': true,
            'faculty-create': true,
            'faculty-edit': true,
            'faculty-delete': true,
            'faculty-approve': true,
            'teacher-records-view': true,
            'teacher-records-create': true,
            'teacher-records-edit': true,
            'teacher-records-delete': true,
            'teacher-records-approve': true,
            'workload-view': true,
            'workload-create': true,
            'workload-edit': true,
            'workload-delete': true,
            'workload-approve': true,
            
            // Academic Management permissions
            'academic-view': true,
            'academic-create': true,
            'academic-edit': true,
            'academic-delete': true,
            'academic-approve': true,
            'curriculum-view': true,
            'curriculum-create': true,
            'curriculum-edit': true,
            'curriculum-delete': false,
            'curriculum-approve': true,
            'grading-view': true,
            'grading-create': true,
            'grading-edit': true,
            'grading-delete': false,
            'grading-approve': true,
            
            // Reports & Analytics permissions
            'reports-view': true,
            'reports-create': true,
            'reports-edit': true,
            'reports-delete': false,
            'reports-approve': true,
            'performance-view': true,
            'performance-create': true,
            'performance-edit': true,
            'performance-delete': false,
            'performance-approve': true,
            'attendance-view': true,
            'attendance-create': true,
            'attendance-edit': true,
            'attendance-delete': false,
            'attendance-approve': true,
            
            // System Administration permissions
            'system-view': true,
            'system-create': false,
            'system-edit': false,
            'system-delete': false,
            'system-approve': false,
            'user-view': true,
            'user-create': false,
            'user-edit': false,
            'user-delete': false,
            'user-approve': false,
            'backup-view': true,
            'backup-create': false,
            'backup-edit': false,
            'backup-delete': false,
            'backup-approve': false
          }
        };
        
        // Apply permissions based on selected role
        function applyPermissions(role) {
          const permissions = defaultPermissions[role];
          
          // Loop through all checkboxes and set their state based on permissions
          for (const [id, value] of Object.entries(permissions)) {
            const checkbox = document.getElementById(id);
            if (checkbox) {
              checkbox.checked = value;
            }
          }
        }
        
        // Apply default permissions for School Head on page load
        applyPermissions('school_head');
        
        // Handle role selection change
        roleSelector.addEventListener('change', function() {
          const selectedRole = this.value;
          applyPermissions(selectedRole);
        });
        
        // Handle reset button click
        resetBtn.addEventListener('click', function() {
          const selectedRole = roleSelector.value;
          applyPermissions(selectedRole);
          
          Swal.fire({
            title: 'Reset Complete',
            text: 'Permissions have been reset to default values',
            icon: 'success',
            confirmButtonColor: '#2e8b57'
          });
        });
        
        // Handle save button click
        saveBtn.addEventListener('click', function() {
          // Collect current permissions state
          const currentPermissions = {};
          const checkboxes = document.querySelectorAll('.policy-table input[type="checkbox"]');
          
          checkboxes.forEach(checkbox => {
            currentPermissions[checkbox.id] = checkbox.checked;
          });
          
          // In a real application, you would send this data to the server
          // For demo purposes, we'll just show a success message
          console.log('Saving permissions:', currentPermissions);
          
          Swal.fire({
            title: 'Changes Saved',
            text: 'Group policy permissions have been updated successfully',
            icon: 'success',
            confirmButtonColor: '#2e8b57'
          });
        });
        
        // Add select all functionality for each module
        const moduleRows = document.querySelectorAll('tr:not(.submodule-row) td.module-name');
        moduleRows.forEach(moduleRow => {
          moduleRow.addEventListener('click', function() {
            const moduleName = this.textContent.trim().toLowerCase().replace(/\s+/g, '-');
            const parentRow = this.parentElement;
            const siblingRows = [];
            
            // Find all submodule rows that follow this module row
            let nextRow = parentRow.nextElementSibling;
            while (nextRow && nextRow.classList.contains('submodule-row')) {
              siblingRows.push(nextRow);
              nextRow = nextRow.nextElementSibling;
            }
            
            // Get the state of checkboxes in the parent row
            const parentCheckboxes = parentRow.querySelectorAll('input[type="checkbox"]');
            const parentStates = Array.from(parentCheckboxes).map(cb => cb.checked);
            
            // Calculate if we should check or uncheck (if any parent checkbox is checked, we'll uncheck all)
            const shouldCheck = !parentStates.some(state => state);
            
            // Update all checkboxes in the parent and sibling rows
            parentCheckboxes.forEach(cb => cb.checked = shouldCheck);
            siblingRows.forEach(row => {
              const checkboxes = row.querySelectorAll('input[type="checkbox"]');
              checkboxes.forEach(cb => cb.checked = shouldCheck);
            });
          });
        });
      }

      // Redirecting to Profile Section
      function redirectToProfile() {
        window.location.href = "profile.html";
      }
    </script>
  </body>
</html>
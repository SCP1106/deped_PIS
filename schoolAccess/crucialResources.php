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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/side-topbar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <style>
      :root {
        --primary-color: #4caf50;
        --secondary-color: #1184e8;
        --success-color: #2e7d32;
        --danger-color: #dc3545;
        --light-color: #f8f9fa;
        --dark-color: #343a40;
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

      .table tbody tr:nth-child(odd) {
        background-color: #f8f9fa;
      }

      .table tbody tr:nth-child(even) {
        background-color: rgba(0, 123, 255, 0.05);
      }

      .table tbody tr:hover {
        background-color: rgba(4, 255, 0, 0.1);
      }

      .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      }

      .modal-header {
        background-color: var(--success-color);
        color: #ffffff;
        border-top-left-radius: 12px;
        border-top-right-radius: 12px;
      }

      .modal-title {
        font-weight: 600;
      }

      .modal-body {
        padding: 1.5rem;
      }

      .modal-footer {
        border-top: none;
        padding: 0.75rem 1.5rem 1.25rem;
        justify-content: space-between;
      }

      .form-control {
        border-radius: 5px;
        padding: 0.5rem 1rem;
      }

      .form-control:focus {
        box-shadow: 0 0 0 0.2rem #388e3c;
        border-color: var(--primary-color);
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

      .filter-option {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
      }

      .filter-option input[type="checkbox"] {
        margin-right: 0.5rem;
      }

      /* New styles for detail view modal */
      .detail-view-buttons {
        display: flex;
        gap: 10px;
        margin-bottom: 20px;
      }

      .detail-view-buttons .btn {
        flex: 1;
        font-weight: 500;
      }

      .detail-view-buttons .btn.active {
        background-color: var(--success-color);
        color: white;
        border-color: var(--success-color);
      }

      .detail-header {
        margin-bottom: 20px;
      }

      .detail-form-section {
        margin-bottom: 25px;
      }

      .detail-form-section h5 {
        margin-bottom: 12px;
        padding-bottom: 6px;
        border-bottom: 1px solid #dee2e6;
        color: var(--success-color);
        font-size: 1rem;
      }

      .detail-form-row {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 12px;
      }

      .detail-form-group {
        flex: 1;
        min-width: 180px;
      }

      .form-label {
        font-weight: 500;
        margin-bottom: 4px;
        color: #495057;
        font-size: 0.9rem;
      }

      .footer-btn-group {
        display: flex;
        gap: 10px;
      }

      /* Make the detail view modal smaller on desktop */
      @media (min-width: 992px) {
        .modal-xl-custom {
          max-width: 70%;
        }
      }

      @media (min-width: 1200px) {
        .modal-xl-custom {
          max-width: 60%;
        }
      }

      /* Modern card-like styling for table rows on mobile */
      @media (max-width: 768px) {
        .search-bar input {
          width: 100%;
          margin-bottom: 1rem;
        }
        .action-buttons {
          display: flex;
          gap: 0.5rem;
          flex-wrap: wrap;
        }
        .action-buttons .btn {
          flex: 1;
          min-width: calc(50% - 0.5rem);
        }

        .table,
        .table tbody,
        .table tr,
        .table td {
          display: block;
        }
        .table thead {
          display: none;
        }
        .table tbody tr {
          margin-bottom: 1rem;
          border: 1px solid #dee2e6;
          border-radius: 12px;
          padding: 0.5rem;
          background-color: #ffffff;
          box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .table td {
          padding: 0.5rem;
          text-align: right;
          position: relative;
          padding-left: 50%;
        }
        .table td::before {
          content: attr(data-label);
          position: absolute;
          left: 0.5rem;
          width: 45%;
          text-align: left;
          font-weight: 600;
          color: var(--primary-color);
        }

        .detail-view-buttons {
          flex-direction: column;
        }
        
        .detail-form-row {
          flex-direction: column;
          gap: 10px;
        }
        
        .detail-form-group {
          width: 100%;
        }

        .modal-footer {
          flex-direction: column;
          gap: 10px;
        }

        .footer-btn-group {
          width: 100%;
          flex-direction: column;
        }
        
        /* Make modal take full width on mobile */
        .modal-dialog {
          margin: 0.5rem;
        }
      }
      
      /* Modern styling for buttons */
      .btn {
        border-radius: 6px;
        font-weight: 500;
        transition: all 0.2s ease;
      }
      
      .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
      }
      
      .btn-warning {
        background-color: #ffc107;
        border-color: #ffc107;
      }
      
      .btn-success {
        background-color: var(--success-color);
        border-color: var(--success-color);
      }
      
      /* Make all buttons in footer the same size */
      .modal-footer .btn {
        padding: 0.5rem 1rem;
        font-size: 0.95rem;
        min-width: 100px;
      }
      #updateDetailBtn {
        margin-right: 10%;
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
                <div class="filter-option">
                  <input type="checkbox" id="publicSchools" />
                  <label for="publicSchools">School ID (SID)</label>
                </div>
                <div class="filter-option">
                  <input type="checkbox" id="privateSchools" />
                  <label for="privateSchools">School Name</label>
                </div>
                <div class="filter-option">
                  <input type="checkbox" id="privateSchools" />
                  <label for="privateSchools">District</label>
                </div>
                <div class="filter-option">
                  <input type="checkbox" id="privateSchools" />
                  <label for="privateSchools">Enrollment</label>
                </div>
                <div class="filter-option">
                  <input type="checkbox" id="privateSchools" />
                  <label for="privateSchools">Classroom</label>
                </div>
                <div class="filter-option">
                  <input type="checkbox" id="privateSchools" />
                  <label for="privateSchools">Seats</label>
                </div>
                <div class="filter-option">
                  <input type="checkbox" id="privateSchools" />
                  <label for="privateSchools">Textbooks</label>
                </div>
                <!-- Add more filter options as needed -->
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
            
            <button class="btn btn-warning" onclick="exportToExcel()">
              <i class="bi bi-file-earmark-excel"></i>
              Export
            </button>
          </div>
        </div>

        <div class="table-responsive">
          <table class="table">
            <thead>
              <tr>
                <th>SID</th>
                <th>School Name</th>
                <th>District</th>
                <th>Enrollment</th>
                <th>Classroom</th>
                <th>Seats</th>
                <th>Textbooks</th>
                <th>Action</th>
              </tr>
            </thead>
            <tbody id="schoolTableBody">
              <tr>
                <td data-label="SID">1001</td>
                <td data-label="School Name">Green Valley High</td>
                <td data-label="District">North</td>
                <td data-label="Enrollment">520</td>
                <td data-label="Classroom">20</td>
                <td data-label="Seats">500</td>
                <td data-label="Textbooks">480</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1001" data-name="Green Valley High">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1002</td>
                <td data-label="School Name">Sunnydale Elementary</td>
                <td data-label="District">East</td>
                <td data-label="Enrollment">310</td>
                <td data-label="Classroom">12</td>
                <td data-label="Seats">300</td>
                <td data-label="Textbooks">295</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1002" data-name="Sunnydale Elementary">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1003</td>
                <td data-label="School Name">Riverbend Academy</td>
                <td data-label="District">West</td>
                <td data-label="Enrollment">450</td>
                <td data-label="Classroom">18</td>
                <td data-label="Seats">440</td>
                <td data-label="Textbooks">460</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1003" data-name="Riverbend Academy">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1004</td>
                <td data-label="School Name">Mountainview High</td>
                <td data-label="District">North</td>
                <td data-label="Enrollment">620</td>
                <td data-label="Classroom">25</td>
                <td data-label="Seats">600</td>
                <td data-label="Textbooks">610</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1004" data-name="Mountainview High">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1005</td>
                <td data-label="School Name">Lakewood School</td>
                <td data-label="District">Central</td>
                <td data-label="Enrollment">275</td>
                <td data-label="Classroom">10</td>
                <td data-label="Seats">260</td>
                <td data-label="Textbooks">270</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1005" data-name="Lakewood School">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1006</td>
                <td data-label="School Name">Pinehill Elementary</td>
                <td data-label="District">East</td>
                <td data-label="Enrollment">330</td>
                <td data-label="Classroom">13</td>
                <td data-label="Seats">320</td>
                <td data-label="Textbooks">310</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1006" data-name="Pinehill Elementary">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1007</td>
                <td data-label="School Name">Maple Leaf School</td>
                <td data-label="District">South</td>
                <td data-label="Enrollment">410</td>
                <td data-label="Classroom">17</td>
                <td data-label="Seats">400</td>
                <td data-label="Textbooks">405</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1007" data-name="Maple Leaf School">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1008</td>
                <td data-label="School Name">Hilltop High</td>
                <td data-label="District">West</td>
                <td data-label="Enrollment">530</td>
                <td data-label="Classroom">22</td>
                <td data-label="Seats">520</td>
                <td data-label="Textbooks">515</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1008" data-name="Hilltop High">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1009</td>
                <td data-label="School Name">Brookfield Academy</td>
                <td data-label="District">North</td>
                <td data-label="Enrollment">480</td>
                <td data-label="Classroom">19</td>
                <td data-label="Seats">470</td>
                <td data-label="Textbooks">465</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1009" data-name="Brookfield Academy">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1010</td>
                <td data-label="School Name">Redwood School</td>
                <td data-label="District">Central</td>
                <td data-label="Enrollment">295</td>
                <td data-label="Classroom">11</td>
                <td data-label="Seats">280</td>
                <td data-label="Textbooks">275</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1010" data-name="Redwood School">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1011</td>
                <td data-label="School Name">Seaside Elementary</td>
                <td data-label="District">South</td>
                <td data-label="Enrollment">360</td>
                <td data-label="Classroom">15</td>
                <td data-label="Seats">350</td>
                <td data-label="Textbooks">345</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1011" data-name="Seaside Elementary">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1012</td>
                <td data-label="School Name">Clearwater High</td>
                <td data-label="District">East</td>
                <td data-label="Enrollment">510</td>
                <td data-label="Classroom">21</td>
                <td data-label="Seats">500</td>
                <td data-label="Textbooks">495</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1012" data-name="Clearwater High">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1013</td>
                <td data-label="School Name">Sunrise Academy</td>
                <td data-label="District">North</td>
                <td data-label="Enrollment">470</td>
                <td data-label="Classroom">18</td>
                <td data-label="Seats">460</td>
                <td data-label="Textbooks">450</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1013" data-name="Sunrise Academy">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1014</td>
                <td data-label="School Name">Blue Ridge School</td>
                <td data-label="District">West</td>
                <td data-label="Enrollment">385</td>
                <td data-label="Classroom">14</td>
                <td data-label="Seats">370</td>
                <td data-label="Textbooks">360</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1014" data-name="Blue Ridge School">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <tr>
                <td data-label="SID">1015</td>
                <td data-label="School Name">Golden Hills High</td>
                <td data-label="District">South</td>
                <td data-label="Enrollment">525</td>
                <td data-label="Classroom">23</td>
                <td data-label="Seats">510</td>
                <td data-label="Textbooks">505</td>
                <td data-label="Action">
                  <button class="btn btn-sm btn-primary view-btn" data-sid="1015" data-name="Golden Hills High">
                    <i class="bi bi-eye"></i> View
                  </button>
                </td>
              </tr>
              <!-- Table rows here (same as in your original code) -->
            </tbody>
          </table>
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
      <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="schoolModalLabel">
              Add/Update Resources
            </h5>
            <button
              type="button"
              class="btn-close"
              data-bs-dismiss="modal"
              aria-label="Close"
            ></button>
          </div>
          <div class="modal-body">
            <form id="schoolForm">
              <div class="form-floating mb-3">
                <input
                  type="text"
                  class="form-control"
                  id="schoolId"
                  placeholder="School ID"
                  required
                />
                <label for="schoolId">School ID</label>
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
                <label for="schoolType">District</label>
              </div>

              <div class="form-floating mb-3">
                <input
                  type="text"
                  class="form-control"
                  id="schoolLevel"
                  placeholder="Level"
                  required
                />
                <label for="schoolLevel">Enrollment</label>
              </div>

              <div class="form-floating mb-3">
                <input
                  type="number"
                  class="form-control"
                  id="schoolPopulation"
                  placeholder="Population"
                  required
                />
                <label for="schoolPopulation">Classroom</label>
              </div>

              <div class="form-floating mb-3">
                <input
                  type="text"
                  class="form-control"
                  id="schoolAddress"
                  placeholder="Address"
                  required
                />
                <label for="schoolAddress">Seats</label>
              </div>

              <div class="form-floating mb-3">
                <input
                  type="text"
                  class="form-control"
                  id="schoolPrincipal"
                  placeholder="Principal"
                  required
                />
                <label for="schoolPrincipal">Textbooks</label>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-bs-dismiss="modal"
            >
              Close
            </button>
            <button type="button" class="btn btn-success" id="saveSchoolBtn">
              Save
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Detail View Modal -->
    <div class="modal fade" id="detailViewModal" tabindex="-1" aria-labelledby="detailViewModalLabel" aria-hidden="true">
      <div class="modal-dialog modal-xl-custom modal-dialog-centered">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="detailViewModalLabel">School Details</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <div class="detail-view-buttons">
              <button type="button" class="btn btn-outline-success active" data-view="classroom">Classroom</button>
              <button type="button" class="btn btn-outline-success" data-view="seats">Seats</button>
              <button type="button" class="btn btn-outline-success" data-view="textbooks">Textbooks</button>
            </div>
            
            <div class="detail-header">
              <div class="row w-100">
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="detailSchoolId" class="form-label">School ID</label>
                    <input type="text" class="form-control" id="detailSchoolId" disabled>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="mb-3">
                    <label for="detailSchoolName" class="form-label">School Name</label>
                    <input type="text" class="form-control" id="detailSchoolName" disabled>
                  </div>
                </div>
              </div>
            </div>
            
            <form id="detailForm">
              <!-- Kinder Section -->
              <div class="detail-form-section">
                <h5>Kinder</h5>
                <div class="detail-form-row">
                  <div class="detail-form-group">
                    <label for="kinderNeeds" class="form-label">No. of Needs</label>
                    <input type="number" class="form-control resource-field" id="kinderNeeds" disabled>
                  </div>
                  <div class="detail-form-group">
                    <label for="kinderExcess" class="form-label">No. of Excess</label>
                    <input type="number" class="form-control resource-field" id="kinderExcess" disabled>
                  </div>
                  <div class="detail-form-group">
                    <label for="kinderTotal" class="form-label">Total</label>
                    <input type="number" class="form-control resource-field" id="kinderTotal" disabled>
                  </div>
                </div>
              </div>
              
              <!-- Grade 1-6 Section -->
              <div class="detail-form-section">
                <h5>Grade 1-6</h5>
                <div class="detail-form-row">
                  <div class="detail-form-group">
                    <label for="grade16Needs" class="form-label">No. of Needs</label>
                    <input type="number" class="form-control resource-field" id="grade16Needs" disabled>
                  </div>
                  <div class="detail-form-group">
                    <label for="grade16Excess" class="form-label">No. of Excess</label>
                    <input type="number" class="form-control resource-field" id="grade16Excess" disabled>
                  </div>
                  <div class="detail-form-group">
                    <label for="grade16Total" class="form-label">Total</label>
                    <input type="number" class="form-control resource-field" id="grade16Total" disabled>
                  </div>
                </div>
              </div>
              
              <!-- SPED Section -->
              <div class="detail-form-section">
                <h5>SPED</h5>
                <div class="detail-form-row">
                  <div class="detail-form-group">
                    <label for="spedNeeds" class="form-label">No. of Needs</label>
                    <input type="number" class="form-control resource-field" id="spedNeeds" disabled>
                  </div>
                  <div class="detail-form-group">
                    <label for="spedExcess" class="form-label">No. of Excess</label>
                    <input type="number" class="form-control resource-field" id="spedExcess" disabled>
                  </div>
                  <div class="detail-form-group">
                    <label for="spedTotal" class="form-label">Total</label>
                    <input type="number" class="form-control resource-field" id="spedTotal" disabled>
                  </div>
                </div>
              </div>
              
              <!-- JHS Section -->
              <div class="detail-form-section">
                <h5>JHS</h5>
                <div class="detail-form-row">
                  <div class="detail-form-group">
                    <label for="jhsNeeds" class="form-label">No. of Needs</label>
                    <input type="number" class="form-control resource-field" id="jhsNeeds" disabled>
                  </div>
                  <div class="detail-form-group">
                    <label for="jhsExcess" class="form-label">No. of Excess</label>
                    <input type="number" class="form-control resource-field" id="jhsExcess" disabled>
                  </div>
                  <div class="detail-form-group">
                    <label for="jhsTotal" class="form-label">Total</label>
                    <input type="number" class="form-control resource-field" id="jhsTotal" disabled>
                  </div>
                </div>
              </div>
              
              <!-- SHS Section -->
              <div class="detail-form-section">
                <h5>SHS</h5>
                <div class="detail-form-row">
                  <div class="detail-form-group">
                    <label for="shsNeeds" class="form-label">No. of Needs</label>
                    <input type="number" class="form-control resource-field" id="shsNeeds" disabled>
                  </div>
                  <div class="detail-form-group">
                    <label for="shsExcess" class="form-label">No. of Excess</label>
                    <input type="number" class="form-control resource-field" id="shsExcess" disabled>
                  </div>
                  <div class="detail-form-group">
                    <label for="shsTotal" class="form-label">Total</label>
                    <input type="number" class="form-control resource-field" id="shsTotal" disabled>
                  </div>
                </div>
              </div>
            </form>
          </div>
          <div class="modal-footer">
            <div class="d-flex justify-content-between w-100">
              <button type="button" class="btn btn-warning" id="updateDetailBtn">
                <i class="bi bi-pencil-square"></i> Update
              </button>
              <div class="footer-btn-group">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-success" id="saveDetailBtn" style="display: none;">Save Changes</button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
  
  document.addEventListener("DOMContentLoaded",()=>{loadComponent("sidebar.php","sidebar-container",initializeSidebar);loadComponent("topbar.php","topbar-container",initializeTopbar);const filterButton=document.getElementById("filterButton");const filterPanel=document.getElementById("filterPanel");filterButton.addEventListener("click",function(event){event.stopPropagation();filterPanel.classList.toggle("show")});document.addEventListener("click",function(event){if(!filterPanel.contains(event.target)&&event.target!==filterButton){filterPanel.classList.remove("show")}});initializeViewButtons();initializeDetailViewButtons();initializeUpdateButton();initializeSaveDetailButton()});function loadComponent(componentName,containerId,callback){const container=document.getElementById(containerId);const xhr=new XMLHttpRequest();xhr.open("GET",componentName,!0);xhr.onload=function(){if(xhr.status===200){container.innerHTML=xhr.responseText;if(callback&&typeof callback==="function"){callback()}}else{console.error(`Failed to load ${componentName}`);container.innerHTML=`<p>Error loading ${componentName}. Please refresh the page.</p>`}};xhr.onerror=function(){console.error(`Network error while loading ${componentName}`);container.innerHTML=`<p>Network error. Please try again later.</p>`};xhr.send()}
  function initializeSidebar(){setTimeout(()=>{const navLinks=document.querySelectorAll(".sidebar nav a");if(navLinks.length===0)return;const currentPage=window.location.pathname.split("/").pop();navLinks.forEach((link)=>{if(link.getAttribute("href")===currentPage){link.classList.add("active")}});navLinks.forEach((link)=>{link.addEventListener("click",function(){document.querySelectorAll(".sidebar nav a").forEach((el)=>el.classList.remove("active"));this.classList.add("active")})})},200)}
  function initializeTopbar(){setTimeout(()=>{const sidebarToggle=document.getElementById("sidebarToggle");const sidebar=document.querySelector(".sidebar");if(sidebarToggle&&sidebar){sidebarToggle.addEventListener("click",()=>{sidebar.classList.toggle("show")});document.addEventListener("click",(e)=>{if(!sidebar.contains(e.target)&&!sidebarToggle.contains(e.target)){sidebar.classList.remove("show")}})}},200)}
  function initializeViewButtons(){const viewButtons=document.querySelectorAll('.view-btn');viewButtons.forEach(button=>{button.addEventListener('click',function(){const sid=this.getAttribute('data-sid');const schoolName=this.getAttribute('data-name');document.getElementById('detailViewModalLabel').textContent=`${schoolName} Details`;document.getElementById('detailSchoolId').value=sid;document.getElementById('detailSchoolName').value=schoolName;loadDetailData(sid,'classroom');const detailViewModal=new bootstrap.Modal(document.getElementById('detailViewModal'));detailViewModal.show()})})}
  function initializeDetailViewButtons(){const detailButtons=document.querySelectorAll('.detail-view-buttons .btn');detailButtons.forEach(button=>{button.addEventListener('click',function(){detailButtons.forEach(btn=>btn.classList.remove('active'));this.classList.add('active');const viewType=this.getAttribute('data-view');updateFormLabels(viewType);const sid=document.getElementById('detailSchoolId').value;loadDetailData(sid,viewType)})})}
  function updateFormLabels(viewType){const resourceType=viewType.charAt(0).toUpperCase()+viewType.slice(1);document.querySelectorAll('.detail-form-group label').forEach(label=>{if(label.textContent.includes('No. of Needs')){label.textContent=`No. of ${resourceType} Needs`}else if(label.textContent.includes('No. of Excess')){label.textContent=`No. of ${resourceType} Excess`}else if(label.textContent.includes('Total')){label.textContent=`Total ${resourceType}`}})}
  function initializeUpdateButton(){const updateButton=document.getElementById('updateDetailBtn');updateButton.addEventListener('click',function(){const isEditing=this.textContent.includes('Update');if(isEditing){this.innerHTML='<i class="bi bi-x-circle"></i> Cancel';this.classList.remove('btn-warning');this.classList.add('btn-danger');document.querySelectorAll('.resource-field').forEach(field=>{field.disabled=!1});document.getElementById('saveDetailBtn').style.display='block'}else{this.innerHTML='<i class="bi bi-pencil-square"></i> Update';this.classList.remove('btn-danger');this.classList.add('btn-warning');document.querySelectorAll('.resource-field').forEach(field=>{field.disabled=!0});document.getElementById('saveDetailBtn').style.display='none';const sid=document.getElementById('detailSchoolId').value;const viewType=document.querySelector('.detail-view-buttons .btn.active').getAttribute('data-view');loadDetailData(sid,viewType)}})}
  function initializeSaveDetailButton(){const saveButton=document.getElementById('saveDetailBtn');saveButton.addEventListener('click',function(){const formData={sid:document.getElementById('detailSchoolId').value,schoolName:document.getElementById('detailSchoolName').value,kinder:{needs:document.getElementById('kinderNeeds').value,excess:document.getElementById('kinderExcess').value,total:document.getElementById('kinderTotal').value},grade16:{needs:document.getElementById('grade16Needs').value,excess:document.getElementById('grade16Excess').value,total:document.getElementById('grade16Total').value},sped:{needs:document.getElementById('spedNeeds').value,excess:document.getElementById('spedExcess').value,total:document.getElementById('spedTotal').value},jhs:{needs:document.getElementById('jhsNeeds').value,excess:document.getElementById('jhsExcess').value,total:document.getElementById('jhsTotal').value},shs:{needs:document.getElementById('shsNeeds').value,excess:document.getElementById('shsExcess').value,total:document.getElementById('shsTotal').value}};console.log('Saving data:',formData);const updateButton=document.getElementById('updateDetailBtn');updateButton.innerHTML='<i class="bi bi-pencil-square"></i> Update';updateButton.classList.remove('btn-danger');updateButton.classList.add('btn-warning');document.querySelectorAll('.resource-field').forEach(field=>{field.disabled=!0});document.getElementById('saveDetailBtn').style.display='none';Swal.fire({title:'Success!',text:`${formData.schoolName} resources have been updated successfully.`,icon:'success',confirmButtonColor:'#4caf50'})})}
  function loadDetailData(sid,viewType){const mockData={classroom:{kinder:{needs:1,excess:0,total:4},grade16:{needs:2,excess:1,total:8},sped:{needs:0,excess:1,total:2},jhs:{needs:1,excess:0,total:4},shs:{needs:1,excess:0,total:2}},seats:{kinder:{needs:10,excess:5,total:100},grade16:{needs:20,excess:10,total:200},sped:{needs:0,excess:5,total:50},jhs:{needs:15,excess:0,total:100},shs:{needs:5,excess:0,total:50}},textbooks:{kinder:{needs:20,excess:10,total:200},grade16:{needs:40,excess:20,total:400},sped:{needs:0,excess:10,total:100},jhs:{needs:30,excess:10,total:200},shs:{needs:10,excess:0,total:100}}};const data=mockData[viewType];document.getElementById('kinderNeeds').value=data.kinder.needs;document.getElementById('kinderExcess').value=data.kinder.excess;document.getElementById('kinderTotal').value=data.kinder.total;document.getElementById('grade16Needs').value=data.grade16.needs;document.getElementById('grade16Excess').value=data.grade16.excess;document.getElementById('grade16Total').value=data.grade16.total;document.getElementById('spedNeeds').value=data.sped.needs;document.getElementById('spedExcess').value=data.sped.excess;document.getElementById('spedTotal').value=data.sped.total;document.getElementById('jhsNeeds').value=data.jhs.needs;document.getElementById('jhsExcess').value=data.jhs.excess;document.getElementById('jhsTotal').value=data.jhs.total;document.getElementById('shsNeeds').value=data.shs.needs;document.getElementById('shsExcess').value=data.shs.excess;document.getElementById('shsTotal').value=data.shs.total}

  document.addEventListener("click",(event)=>{if(event.target.id==="logoutLink"){event.preventDefault();Swal.fire({title:"Are you sure?",text:"You are about to logout!",icon:"warning",showCancelButton:!0,confirmButtonColor:"#3085d6",cancelButtonColor:"#d33",confirmButtonText:"Yes, logout!",}).then((result)=>{if(result.isConfirmed){window.location.href="index.php"}})}});function prepareModal(action){const modalTitle=document.getElementById("schoolModalLabel");const saveButton=document.getElementById("saveSchoolBtn");const schoolIdInput=document.getElementById("schoolId");if(action==="add"){modalTitle.textContent="Add New School";saveButton.onclick=addSchool;schoolIdInput.value="";schoolIdInput.readOnly=!1;document.getElementById("schoolForm").reset()}else if(action==="update"){modalTitle.textContent="Update School";saveButton.onclick=updateSchool;schoolIdInput.readOnly=!0;schoolIdInput.value="";schoolIdInput.readOnly=!1}}
  function addSchool(){console.log("Adding new school");var modal=bootstrap.Modal.getInstance(document.getElementById("schoolModal"));modal.hide()}
  function updateSchool(){console.log("Updating school");var modal=bootstrap.Modal.getInstance(document.getElementById("schoolModal"));modal.hide()}
  function exportToExcel(){console.log("Exporting data to Excel");alert("Exporting to Excel... (This is a placeholder)")}
  const tableBody=document.getElementById("schoolTableBody");const searchInput=document.getElementById("searchInput");searchInput.addEventListener("input",function(){const filter=this.value.toLowerCase();const rows=tableBody.getElementsByTagName("tr");for(let row of rows){const text=row.textContent.toLowerCase();row.style.display=text.includes(filter)?"":"none"}});function redirectToProfile(){window.location.href="profile.html"}
    </script>
  </body>
</html>

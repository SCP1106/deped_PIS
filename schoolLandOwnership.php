<!DOCTYPE
html >
  <html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DepEd: School Land Ownership</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />
    <link rel="stylesheet" href="css/side-topbar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <!-- Leaflet CSS -->
    <link
      rel="stylesheet"
      href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"
    />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
      :root {
        --primary-green: #2e7d32;
        --light-green: #4caf50;
        --dark-green: #1b5e20;
        --hover-green: #388e3c;
        --sidebar-width: 280px;

        /* Update theme colors */
        --primary-color: var(--primary-green);
        --secondary-color: var(--light-green);
        --accent-color: var(--hover-green);
        --text-color: #333;
        --light-bg: #f8f9fa;
        --border-color: #dee2e6;
      }

      .district-card {
        background-color: white;
        border-radius: 10px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        height: 280px; /* Add fixed height */
        display: flex;
        flex-direction: column;
        justify-content: space-between;
      }

      .grid-icon {
        position: absolute;
        top: 20px;
        right: 20px;
        font-size: 2rem;
        color: var(--primary-color);
        opacity: 0.2;
      }

      .metric-label {
        font-size: 0.9rem;
        color: #6c757d;
        margin-bottom: 5px;
        text-align: center;
      }

      .metric-value {
        font-size: 1.8rem;
        font-weight: 600;
        color: rgb(52, 52, 52);
        text-align: center;
        min-height: 40px; /* Ensure consistent height for values */
        display: flex;
        align-items: center;
        justify-content: center;
      }

      .clickable {
        cursor: pointer;
        transition: color 0.3s;
      }

      .clickable:hover {
        color: var(--secondary-color);
        text-decoration: underline;
      }

      .table th {
        background-color: var(--light-bg);
      }

      .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
      }

      .pagination .page-link {
        color: var(--primary-color);
      }

      .btn-view {
        background-color: var(--primary-color);
        color: white;
      }

      .btn-view:hover {
        background-color: var(--hover-green);
        color: white;
      }

      .btn-district {
        background-color: var(--primary-color);
        color: white;
        margin-top: auto; /* Push button to bottom */
        width: 100%;
        transition: background-color 0.3s;
      }

      .btn-district:hover {
        background-color: var(--hover-green);
        color: white;
      }

      #schoolMap {
        border-radius: 5px;
        border: 1px solid var(--border-color);
      }

      .card {
        border-radius: 8px;
        overflow: hidden;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
      }

      .card-header {
        padding: 12px 16px;
        background-color: var(--primary-color) !important;
      }

      .table-bordered {
        border: 1px solid var(--border-color);
      }

      .table-bordered th,
      .table-bordered td {
        border: 1px solid var(--border-color);
        padding: 10px 15px;
      }

      .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
      }
      .modal-header {
        background-color: var(--primary-color);
        color: white;
      }

      /* Pagination styles */
      .pagination {
        margin-bottom: 0;
      }

      .pagination .page-item .page-link {
        padding: 0.5rem 0.75rem;
        border-radius: 0;
        border: 1px solid #dee2e6;
      }

      .pagination .page-item.active .page-link {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
        color: white;
      }

      .pagination .page-item.disabled .page-link {
        color: #6c757d;
        pointer-events: none;
        background-color: #fff;
        border-color: #dee2e6;
      }

      .pagination .page-link:hover {
        background-color: #e9ecef;
        border-color: #dee2e6;
        color: var(--primary-color);
      }

      .pagination-info {
        color: #6c757d;
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
      }
      #pdf-viewer {
        width: 100%;
        height: 600px;
        border: 1px solid #ccc;
        margin-top: 20px;
      }

      .file-upload-section {
        border: 1px dashed #ccc;
        padding: 15px;
        border-radius: 5px;
        background-color: #f8f9fa;
        margin-bottom: 15px;
      }

      .file-upload-section .form-label {
        font-weight: 500;
      }

      .file-info {
        margin-top: 8px;
        font-size: 0.85rem;
        color: #6c757d;
      }

      .file-name {
        font-weight: 500;
        color: var(--primary-color);
        word-break: break-all;
      }

      /* Map container for update modal */
      #update-map-container {
        height: 300px;
        margin-bottom: 15px;
        border-radius: 5px;
        border: 1px solid var(--border-color);
      }

      /* Styling for the location picker section */
      .location-picker-section {
        background-color: #f8f9fa;
        border-radius: 5px;
        padding: 15px;
        margin-bottom: 15px;
        border: 1px solid var(--border-color);
      }

      .location-picker-section h5 {
        color: var(--primary-color);
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
      }

      .coordinate-box {
        background-color: white;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 8px 12px;
        width: 48%;
        text-align: center;
      }

      .coordinate-box label {
        display: block;
        font-size: 0.75rem;
        color: #6c757d;
        margin-bottom: 2px;
      }

      .coordinate-box span {
        font-weight: 500;
        color: var(--text-color);
      }

      .btn-success {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
      }

      .btn-success:hover {
        background-color: var(--hover-green);
        border-color: var(--hover-green);
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
        border-color: var(--primary-color);
        color: var(--primary-color);
        font-weight: 500;
      }

      .district-filter:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.25rem rgba(46, 125, 50, 0.25);
      }

      /* Modal backdrop enhancement */
      .modal-backdrop {
        backdrop-filter: blur(3px);
        background-color: rgba(0, 0, 0, 0.5);
      }

      /* Fix for multiple backdrops */
      body.modal-open {
        overflow: hidden;
        padding-right: 0 !important;
      }
    </style>
  </head>
  <body>
    <div>
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
            <option value="ALL">All Districts</option>
            <option value="CD">Congressional District</option>
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

      <!-- Schools Modal -->
      <div class="modal fade" id="schoolsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                Schools - <span id="districtNum"></span>
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
                <div class="position-relative" style="max-width: 300px;">
                  <input type="text" class="form-control" id="searchInput" placeholder="Search schools...">
                </div>
                
                <div class="btn-group">
                  <button type="button" class="btn btn-primary" onclick="showUpdateSchoolModal()" id="updateButton" disabled>
                    <i class="bi bi-pencil-square"></i> Update
                  </button>
                  <button type="button" class="btn btn-warning" onclick="exportToExcel()">
                    <i class="bi bi-filetype-exe"></i> Export
                  </button>
                </div>
              </div>
              <div class="table-responsive">
                <table class="table">
                  <thead>
                    <tr>
                      <th>School ID</th>
                      <th>School Name</th>
                      <th>Year Established</th>
                      <th>Total Land Area</th>
                      <th>Transfer Doc</th>
                      <th>Title Number</th>
                      <th>Action</th>
                    </tr>
                  </thead>
                  <tbody id="schoolsTableBody">
                    <!-- Schools data will be dynamically inserted here -->
                  </tbody>
                </table>
              </div>
              <div
                class="d-flex justify-content-between align-items-center mt-3"
              >
                <div class="pagination-info" id="paginationInfo">
                  Showing <span id="startRecord">0</span> to
                  <span id="endRecord">0</span> of
                  <span id="totalRecords">0</span> entries
                </div>
                <div>
                  <label for="itemsPerPage" class="me-2">Items per page:</label>
                  <select
                    id="itemsPerPage"
                    class="form-select form-select-sm d-inline-block"
                    style="width: auto"
                    onchange="changeItemsPerPage()"
                  >
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <ul
                id="schoolsPagination"
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

      <!-- School Details Modal -->
      <div class="modal fade" id="schoolDetailsModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                School Land Details - <span id="schoolNameTitle"></span>
              </h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <div class="row">
                <div class="col-md-6">
                  <div class="card mb-3">
                    <div class="card-header text-white">
                      <h5 class="mb-0">Land Ownership Details</h5>
                    </div>
                    <div class="card-body">
                      <table class="table table-bordered">
                        <tbody id="landDetailsTable">
                          <tr>
                            <th width="40%">School ID</th>
                            <td id="detail-schoolId"></td>
                          </tr>
                          <tr>
                            <th>School Name</th>
                            <td id="detail-schoolName"></td>
                          </tr>
                          <tr>
                            <th>Year Established</th>
                            <td id="detail-yearEstablished"></td>
                          </tr>
                          <tr>
                            <th>Lot Information</th>
                            <td id="detail-lotInfo"></td>
                          </tr>
                          <tr>
                            <th>Total Land Area</th>
                            <td
                              id="detail-totalLandArea"
                              data-raw-value="0"
                            ></td>
                          </tr>
                          <tr>
                            <th>Classification</th>
                            <td id="detail-classification"></td>
                          </tr>
                          <tr>
                            <th>Instrument of Transfer</th>
                            <td id="detail-transferInstrument"></td>
                          </tr>
                          <tr>
                            <th>Title Number</th>
                            <td id="detail-titleNumber"></td>
                          </tr>
                          <tr>
                            <th>Proclamation Number</th>
                            <td id="detail-proclamationNumber"></td>
                          </tr>
                          <tr>
                            <th>Latitude</th>
                            <td id="detail-latitude"></td>
                          </tr>
                          <tr>
                            <th>Longitude</th>
                            <td id="detail-longitude"></td>
                          </tr>
                          <tr>
                            <th>Ownership Document</th>
                            <td id="detail-ownershipDoc"></td>
                          </tr>
                          <tr>
                            <th>School Site Plan</th>
                            <td id="detail-sitePlanDoc"></td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="card">
                    <div class="card-header text-white">
                      <h5 class="mb-0">School Location</h5>
                    </div>
                    <div class="card-body">
                      <div id="schoolMap" style="height: 400px"></div>
                      <div class="mt-3 d-flex justify-content-center">
                        <button
                          class="btn btn-view me-2"
                          onclick="viewOwnershipPdf()"
                        >
                          <i class="bi bi-file-earmark-pdf"></i> View Ownership
                          Document
                        </button>
                        <button
                          class="btn btn-view"
                          onclick="viewSchoolsitePdf()"
                        >
                          <i class="bi bi-file-earmark-pdf"></i> View School
                          Site Plan
                        </button>
                      </div>
                      <div id="pdf-container" style="display: none">
                        <!-- The embedded PDF will appear here -->
                        <iframe id="pdf-viewer" src="" frameborder="0"></iframe>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
              <div
                class="d-flex justify-content-between align-items-center mt-3"
              >
                <div class="pagination-info" id="paginationInfo">
                  Showing <span id="startRecord">0</span> to
                  <span id="endRecord">0</span> of
                  <span id="totalRecords">0</span> entries
                </div>
                <div>
                  <label for="itemsPerPage" class="me-2">Items per page:</label>
                  <select
                    id="itemsPerPage"
                    class="form-select form-select-sm d-inline-block"
                    style="width: auto"
                    onchange="changeItemsPerPage()"
                  >
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                  </select>
                </div>
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

      <!-- Update School Modal - Redesigned to match School Land Details UI -->
      <div class="modal fade" id="updateSchoolModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title">
                Update School Information -
                <span id="updateSchoolNameTitle"></span>
              </h5>
              <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
                aria-label="Close"
              ></button>
            </div>
            <div class="modal-body">
              <form id="updateSchoolForm">
                <input type="hidden" id="update-district" />

                <div class="row">
                  <!-- Left column - School details -->
                  <div class="col-md-6">
                    <div class="card mb-3">
                      <div class="card-header text-white">
                        <h5 class="mb-0">School Information</h5>
                      </div>
                      <div class="card-body">
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label for="update-schoolId" class="form-label"
                              >School ID</label
                            >
                            <input
                              type="text"
                              class="form-control"
                              id="update-schoolId"
                              disabled
                            />
                          </div>
                          <div class="col-md-6">
                            <label for="update-schoolName" class="form-label"
                              >School Name</label
                            >
                            <input
                              type="text"
                              class="form-control"
                              id="update-schoolName"
                              disabled
                            />
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label
                              for="update-yearEstablished"
                              class="form-label"
                              >Year Established</label
                            >
                            <input
                              type="number"
                              class="form-control"
                              id="update-yearEstablished"
                              required
                              min="1000"
                              max="9999"
                              oninput="this.value = this.value.slice(0, 4);"
                            />
                          </div>
                          <div class="col-md-6">
                            <label for="update-lotInfo" class="form-label"
                              >Lot Information</label
                            >
                            <input
                              type="text"
                              class="form-control"
                              id="update-lotInfo"
                              required
                            />
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label for="update-totalLandArea" class="form-label"
                              >Total Land Area (sqm)</label
                            >
                            <input
                              type="number"
                              class="form-control"
                              id="update-totalLandArea"
                              required
                            />
                          </div>
                          <div class="col-md-6">
                            <label
                              for="update-classification"
                              class="form-label"
                              >Classification</label
                            >
                            <input
                              type="text"
                              class="form-control"
                              id="update-classification"
                              required
                            />
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-md-6">
                            <label
                              for="update-transferInstrument"
                              class="form-label"
                              >Instrument of Transfer</label
                            >
                            <select
                              class="form-select"
                              id="update-transferInstrument"
                              required
                            >
                              <option value="">Select...</option>
                              <option value="Deed of Donation">
                                Deed of Donation
                              </option>
                              <option value="Deed of Sale">Deed of Sale</option>
                              <option value="Presidential Proclamation">
                                Presidential Proclamation
                              </option>
                            </select>
                          </div>
                          <div class="col-md-6">
                            <label for="update-titleNumber" class="form-label"
                              >Title Number</label
                            >
                            <input
                              type="text"
                              class="form-control"
                              id="update-titleNumber"
                            />
                          </div>
                        </div>
                        <div class="row mb-3">
                          <div class="col-md-12">
                            <label
                              for="update-proclamationNumber"
                              class="form-label"
                              >Proclamation Number</label
                            >
                            <input
                              type="text"
                              class="form-control"
                              id="update-proclamationNumber"
                            />
                          </div>
                        </div>

                        <!-- Document Upload Section -->
                        <div class="row">
                          <div class="col-md-6">
                            <div class="file-upload-section">
                              <label
                                for="update-ownershipDoc"
                                class="form-label"
                              >
                                <i class="bi bi-file-earmark-pdf"></i> Ownership
                                Document
                              </label>
                              <input
                                type="file"
                                class="form-control"
                                id="update-ownershipDoc"
                                accept=".pdf"
                              />
                              <div class="file-info mt-2">
                                <small class="text-muted"
                                  >Upload ownership document (PDF only, max
                                  5MB)</small
                                >
                                <div
                                  id="update-current-ownership-doc"
                                  class="mt-1"
                                >
                                  <span class="text-muted">Current file: </span>
                                  <span
                                    class="file-name"
                                    id="update-ownership-filename"
                                    >No file uploaded</span
                                  >
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="col-md-6">
                            <div class="file-upload-section">
                              <label
                                for="update-sitePlanDoc"
                                class="form-label"
                              >
                                <i class="bi bi-file-earmark-pdf"></i> School
                                Site Plan
                              </label>
                              <input
                                type="file"
                                class="form-control"
                                id="update-sitePlanDoc"
                                accept=".pdf"
                              />
                              <div class="file-info mt-2">
                                <small class="text-muted"
                                  >Upload school site plan (PDF only, max
                                  5MB)</small
                                >
                                <div
                                  id="update-current-siteplan-doc"
                                  class="mt-1"
                                >
                                  <span class="text-muted">Current file: </span>
                                  <span
                                    class="file-name"
                                    id="update-siteplan-filename"
                                    >No file uploaded</span
                                  >
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>

                  <!-- Right column - Map and location -->
                  <div class="col-md-6">
                    <div class="card">
                      <div class="card-header text-white">
                        <h5 class="mb-0">School Location</h5>
                      </div>
                      <div class="card-body">
                        <div class="location-picker-section mb-3">
                          <h5><i class="bi bi-geo-alt"></i> Location Picker</h5>
                          <p>
                            Click on the map to set the exact location of your
                            school. You can drag the marker to adjust the
                            position.
                          </p>
                        </div>

                        <div id="update-map-container"></div>

                        <div class="coordinates-display">
                          <div class="coordinate-box">
                            <label for="update-latitude">Latitude</label>
                            <input
                              type="number"
                              step="0.0001"
                              class="form-control"
                              id="update-latitude"
                              required
                            />
                          </div>
                          <div class="coordinate-box">
                            <label for="update-longitude">Longitude</label>
                            <input
                              type="number"
                              step="0.0001"
                              class="form-control"
                              id="update-longitude"
                              required
                            />
                          </div>
                        </div>

                        <div class="mt-3">
                          <button
                            type="button"
                            class="btn btn-sm btn-secondary"
                            onclick="resetMapLocation()"
                          >
                            <i class="bi bi-geo"></i> Reset Location
                          </button>
                          <button
                            type="button"
                            class="btn btn-sm btn-info"
                            onclick="useCurrentLocation()"
                          >
                            <i class="bi bi-cursor"></i> Use Current Location
                          </button>
                        </div>
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
                Cancel
              </button>
              <button
                type="button"
                class="btn btn-primary"
                onclick="updateSchool()"
              >
                Update School
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="js/side-topbar.js"></script>
    <script>
      // Global variables
      let map = null;
      let updateMap = null;
      let updateMarker = null;
      let selectedSchoolId = null;
      let selectedDistrict = null;
      const schoolData = {};
      let currentPage = 1;
      let itemsPerPage = 10;
      let currentDistrict = null;
      let filteredSchools = [];
      let currentMunicipality = null;
      
      // Global variables for search and filter functionality
      window.selectedColumns = {
        schoolId: true,
        schoolName: true,
        yearEstablished: true,
        totalLandArea: true,
        transferDoc: true,
        titleNumber: true
      };

      // District data mapping
      const districtSchoolsMapping = {
        CD1: [
          "ALIAGA",
          "CUYAPO EAST",
          "CUYAPO WEST",
          "GUIMBA EAST",
          "GUIMBA WEST",
          "LICAB",
          "NAMPICUAN",
          "QUEZON",
          "STO. DOMINGO",
          "TALAVERA NORTH",
          "TALAVERA SOUTH",
          "ZARAGOZA",
        ],
        CD2: [
          "CARRANGLAN",
          "LLANERA",
          "LUPAO",
          "PANTABANGAN",
          "RIZAL",
          "TALUTUG",
        ],
        CD3: [
          "BONGABON",
          "GABALDON",
          "GEN M. NATIVIDAD",
          "LAUR",
          "PALAYAN CITY",
          "SANTA ROSA NORTH",
          "SANTA ROSA SOUTH",
        ],
        CD4: [
          "CABIAO",
          "GENERAL TINIO",
          "JAEN NORTH",
          "JAEN SOUTH",
          "PEÑARANDA",
          "SAN ANTONIO",
          "SAN ISIDRO",
          "SAN LEONARDO",
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

      // Function to format land area based on screen size
      function formatLandArea(value, isMobile) {
        if (!value) return "0 sqm";

        // Convert to number if it's a string
        const numValue =
          typeof value === "number" ? value : parseFloat(value) || 0;

        if (isMobile) {
          // For mobile: Format as millions/thousands with 1 decimal place
          if (numValue >= 1000000) {
            return (numValue / 1000000).toFixed(1) + "m sqm";
          } else if (numValue >= 1000) {
            return (numValue / 1000).toFixed(1) + "k sqm";
          } else {
            return numValue.toLocaleString() + " sqm";
          }
        } else {
          // For desktop: Show full number with commas
          return numValue.toLocaleString() + " sqm";
        }
      }

      // Function to update all land area displays
      function updateLandAreaDisplays() {
        // Check if we're on mobile (screen width less than 768px)
        const isMobile = window.innerWidth < 768;

        // Update all elements with data-raw-value attribute
        const landAreaElements = document.querySelectorAll("[data-raw-value]");
        landAreaElements.forEach((element) => {
          if (element && element.dataset.rawValue) {
            element.textContent = formatLandArea(
              Number.parseFloat(element.dataset.rawValue),
              isMobile
            );
          }
        });
      }

      // Initialize when the DOM is loaded
      window.onload = function () {
        // Initialize empty schoolData object
        window.schoolData = {};

        // Set up search event listener
        setupSearchAndFilter();

        // Fetch data for all districts on page load
        fetchAllDistrictsData();

        // Add resize event listener for responsive land area
        window.addEventListener("resize", function () {
          // Use debounce to avoid excessive updates during resize
          clearTimeout(window.resizeTimer);
          window.resizeTimer = setTimeout(function () {
            updateLandAreaDisplays();
          }, 250);
        });

        // Set up district filter dropdown event listener
        setupDistrictFilter();

        // Initialize with "Congressional District" view
        showAllMunicipalityCards();

        // Set up modal event listeners for proper backdrop handling
        setupModalEventListeners();
      };

      // Function to set up search and filter functionality
      function setupSearchAndFilter() {
        const searchInput = document.getElementById("searchInput");
        if (!searchInput) return;

        // Create filter button and add it before the search input
        const searchContainer = searchInput.parentElement;
         
        // Create the filter button
        const filterButton = document.createElement("button");
        filterButton.className = "btn btn-primary me-2";
        filterButton.innerHTML = '<i class="bi bi-funnel-fill"></i> Filter';
        filterButton.style.backgroundColor = "var(--primary-green)";
        filterButton.style.borderColor = "var(--primary-green)";
        filterButton.id = "filterButton";
        
        // Create a wrapper div for the search input and filter button
        const inputGroup = document.createElement("div");
        inputGroup.className = "input-group";
        
        // Move the search input into the input group
        searchInput.parentNode.removeChild(searchInput);
        inputGroup.appendChild(filterButton);
        inputGroup.appendChild(searchInput);
        
        // Add the input group to the search container
        searchContainer.appendChild(inputGroup);
        
        // Create the filter dropdown
        const filterDropdown = document.createElement("div");
        filterDropdown.className = "position-absolute start-0 mt-1 p-2 bg-white border rounded shadow-sm";
        filterDropdown.style.zIndex = "1000";
        filterDropdown.style.width = "250px";
        filterDropdown.style.display = "none";
        filterDropdown.id = "filterDropdown";
        
        // Add content to the filter dropdown
        filterDropdown.innerHTML = `
          <div class="fw-bold mb-2">Search in columns:</div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="check-schoolId" checked>
            <label class="form-check-label" for="check-schoolId">School ID</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="check-schoolName" checked>
            <label class="form-check-label" for="check-schoolName">School Name</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="check-yearEstablished" checked>
            <label class="form-check-label" for="check-yearEstablished">Year Established</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="check-totalLandArea" checked>
            <label class="form-check-label" for="check-totalLandArea">Total Land Area</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="check-transferDoc" checked>
            <label class="form-check-label" for="check-transferDoc">Transfer Doc</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" id="check-titleNumber" checked>
            <label class="form-check-label" for="check-titleNumber">Title Number</label>
          </div>
          <div id="column-warning" class="text-danger small mt-2" style="display: none;">
            Please select at least one column to search
          </div>
        `;
        
        // Make the search container relative for proper dropdown positioning
        searchContainer.style.position = "relative";
        
        // Add the dropdown to the search container
        searchContainer.appendChild(filterDropdown);
        
        // Toggle dropdown when filter button is clicked
        filterButton.addEventListener("click", function(event) {
          event.stopPropagation();
          filterDropdown.style.display = filterDropdown.style.display === "none" ? "block" : "none";
        });
        
        // Close dropdown when clicking outside
        document.addEventListener("click", function(event) {
          if (!filterButton.contains(event.target) && !filterDropdown.contains(event.target)) {
            filterDropdown.style.display = "none";
          }
        });
        
        // Set up event listeners for checkboxes
        const checkboxes = filterDropdown.querySelectorAll("input[type='checkbox']");
        checkboxes.forEach(function(checkbox) {
          checkbox.addEventListener("change", function() {
            const columnName = this.id.replace("check-", "");
            window.selectedColumns[columnName] = this.checked;
            
            // Check if at least one column is selected
            const isAnyColumnSelected = Object.values(window.selectedColumns).some(value => value);
            document.getElementById("column-warning").style.display = isAnyColumnSelected ? "none" : "block";
            
            // Re-run search with new column settings
            if (searchInput.value.trim()) {
              performSearch(searchInput.value);
            }
          });
        });
        
        // Set up search input event listener
        searchInput.addEventListener("keyup", function() {
          performSearch(this.value);
        });
      }

      // Function to perform search based on selected columns
      function performSearch(searchTerm) {
  if (!currentDistrict) return;
  
  const searchTermLower = searchTerm.toLowerCase().trim();
  
  // Safely access school data
  const schools = window.schoolData && window.schoolData[currentDistrict] 
    ? window.schoolData[currentDistrict] 
    : [];
  
  if (!Array.isArray(schools)) {
    console.error("Invalid schools data for district", currentDistrict);
    return;
  }
  
  // If search term is empty, show all schools for current municipality or district
  if (!searchTermLower) {
    filteredSchools = currentMunicipality 
      ? schools.filter((school) => {
          const schoolCDName = (school.CDName || "").toLowerCase();
          return schoolCDName === currentMunicipality.toLowerCase();
        })
      : [...schools];
    
    // Reset to first page and display
    currentPage = 1;
    displaySchoolsWithPagination();
    return;
  }
  
  // Check if any column is selected
  const isAnyColumnSelected = Object.values(window.selectedColumns).some(value => value);
  if (!isAnyColumnSelected) {
    // If no columns selected, don't filter
    document.getElementById("column-warning").style.display = "block";
    return;
  }
  
  // Filter schools based on search term and current municipality if applicable
  filteredSchools = schools.filter(function(school) {
    if (!school) return false;
    
    // First check if we need to filter by municipality
    if (currentMunicipality) {
      const schoolCDName = (school.CDName || "").toLowerCase();
      if (schoolCDName !== currentMunicipality.toLowerCase()) {
        return false;
      }
    }
    
    // Search in selected columns only
    if (window.selectedColumns.schoolId && 
        (school.schoolId || school.school_id || "").toString().toLowerCase().includes(searchTermLower)) {
      return true;
    }
    
    if (window.selectedColumns.schoolName && 
        (school.SchoolName || school.school_name || "").toLowerCase().includes(searchTermLower)) {
      return true;
    }
    
    if (window.selectedColumns.yearEstablished && 
        (school.yearEstablished || school.year_estab || school.year_established || "").toString().toLowerCase().includes(searchTermLower)) {
      return true;
    }
    
    if (window.selectedColumns.totalLandArea && 
        (school.totalLandArea || school.land_area || "").toString().toLowerCase().includes(searchTermLower)) {
      return true;
    }
    
    if (window.selectedColumns.transferDoc && 
        (school.transfer_doc || school.transfer_instrument || "").toLowerCase().includes(searchTermLower)) {
      return true;
    }
    
    if (window.selectedColumns.titleNumber && 
        (school.titleNumber || school.title_no || "").toLowerCase().includes(searchTermLower)) {
      return true;
    }
    
    return false;
  });
  
  // Reset to first page when searching
  currentPage = 1;
  
  // Display filtered schools with pagination
  displaySchoolsWithPagination();
}

      // Set up event listeners for all modals to ensure proper backdrop cleanup
      function setupModalEventListeners() {
        const modalIds = [
          "schoolsModal",
          "schoolDetailsModal",
          "updateSchoolModal",
        ];

        modalIds.forEach((modalId) => {
          const modalElement = document.getElementById(modalId);
          if (modalElement) {
            // When modal is hidden, ensure backdrop is removed
            modalElement.addEventListener("hidden.bs.modal", () => {
              cleanupModalBackdrop();
            });
          }
        });
      }

      // Function to clean up modal backdrop and body classes
      function cleanupModalBackdrop() {
        // Check if there are any open modals
        const openModals = document.querySelectorAll(".modal.show");

        // Only remove backdrop and body classes if no modals are open
        if (openModals.length === 0) {
          // Remove any lingering modal backdrops
          document.querySelectorAll(".modal-backdrop").forEach((backdrop) => {
            backdrop.remove();
          });

          // Remove modal-open class from body
          document.body.classList.remove("modal-open");
          document.body.style.overflow = "";
          document.body.style.paddingRight = "";
        }
      }

      // Set up district filter dropdown
      function setupDistrictFilter() {
        const districtFilter = document.getElementById("districtFilter");
        if (!districtFilter) return;

        districtFilter.addEventListener("change", function () {
          const selectedValue = this.value;

          if (selectedValue === "CD") {
            // Show all district cards
            showAllDistrictCards();
          } else if (selectedValue === "ALL") {
            // Show all municipalities
            showAllMunicipalityCards();
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

          const cardHtml = `
            <div class="col-md-6">
              <div class="district-card" data-municipality="${municipality}" data-cd-district="${cdDistrict}">
                <i class="bi bi-building grid-icon"></i>
                <h2 style="color: var(--primary-color)">${municipality}</h2>
                <div class="row mt-4">
                  <div class="col-4">
                    <div class="metric-label">
                      <i class="bi bi-buildings"></i> Total Schools
                    </div>
                    <div class="metric-value" id="totSchools-${municipality.replace(
                      /\s+/g,
                      "-"
                    )}">0</div>
                  </div>
                  <div class="col-4">
                    <div class="metric-label">
                      <i class="bi bi-map"></i> Total Land Area
                    </div>
                    <div class="metric-value" id="totLandArea-${municipality.replace(
                      /\s+/g,
                      "-"
                    )}" data-raw-value="0">0 sqm</div>
                  </div>
                  <div class="col-4">
                    <div class="metric-label">
                      <i class="bi bi-file-earmark-text"></i> With Title
                    </div>
                    <div class="metric-value" id="withTitle-${municipality.replace(
                      /\s+/g,
                      "-"
                    )}">0</div>
                  </div>
                </div>
                <button class="btn btn-district" onclick="showMunicipalitySchools('${municipality}', '${cdDistrict}')">
                  <i class="bi bi-list-ul"></i> View Schools
                </button>
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
        // Make sure we have data for all districts
        for (let district = 1; district <= 4; district++) {
          if (!window.schoolData || !window.schoolData[district]) {
            // If no data, fetch it first
            fetch(`phpp/dashboard/fetchLandOwner.php?district=${district}`)
              .then((response) => response.json())
              .then((data) => {
                if (data && Array.isArray(data)) {
                  window.schoolData[district] = data;
                  // Update metrics for municipalities in this district
                  updateMunicipalityMetricsForDistrict(district);
                }
              })
              .catch((error) =>
                console.error(
                  `Error fetching data for district ${district}:`,
                  error
                )
              );
          } else {
            // We already have data, update metrics
            updateMunicipalityMetricsForDistrict(district);
          }
        }
      }

      // Update metrics for municipalities in a specific district
      function updateMunicipalityMetricsForDistrict(districtNumber) {
        // Determine which CD district this corresponds to
        let cdDistrict;
        switch (districtNumber) {
          case 1:
            cdDistrict = "CD1";
            break;
          case 2:
            cdDistrict = "CD2";
            break;
          case 3:
            cdDistrict = "CD3";
            break;
          case 4:
            cdDistrict = "CD4";
            break;
          default:
            return;
        }

        // Get municipalities for this CD district
        const municipalities = districtSchoolsMapping[cdDistrict] || [];
        if (municipalities.length === 0) return;

        // Update metrics for each municipality
        updateMunicipalityMetricsWithData(municipalities, districtNumber);
      }

      // Show all district cards (original view)
      function showAllDistrictCards() {
        const cardsContainer = document.getElementById(
          "district-cards-container"
        );
        if (!cardsContainer) return;

        // Clear existing cards
        cardsContainer.innerHTML = "";

        // Generate the original 4 district cards
        originalDistricts.forEach((district) => {
          console.log(district.id);
          const cardHtml = `
            <div class="col-md-6">
              <div class="district-card" data-district="${district.id}">
                <i class="bi bi-building grid-icon"></i>
                <h2 style="color: var(--primary-color)">${district.name}</h2>
                <div class="row mt-4">
                  <div class="col-4">
                    <div class="metric-label">
                      <i class="bi bi-buildings"></i> Total Schools
                    </div>
                    <div class="metric-value" id="totSchools-${district.id}">0</div>
                  </div>
                  <div class="col-4">
                    <div class="metric-label">
                      <i class="bi bi-map"></i> Total Land Area
                    </div>
                    <div class="metric-value" id="totLandArea-${district.id}" data-raw-value="0">0 sqm</div>
                  </div>
                  <div class="col-4">
                    <div class="metric-label">
                      <i class="bi bi-file-earmark-text"></i> With Title
                    </div>
                    <div class="metric-value" id="withTitle-${district.id}">0</div>
                  </div>
                </div>
                <button class="btn btn-district" onclick="showSchools(${district.id})">
                  <i class="bi bi-list-ul"></i> View Schools
                </button>
              </div>
            </div>
          `;

          cardsContainer.innerHTML += cardHtml;
        });

        // Update metrics for all districts
        updateDistrictMetricsUI();
        for (let district = 1; district <= 4; district++) {
          updateDistrictMetricsForDistrict(district);
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
        municipalities.forEach((municipality, index) => {
          const cardHtml = `
            <div class="col-md-6">
              <div class="district-card" data-municipality="${municipality}" data-cd-district="${cdDistrict}">
                <i class="bi bi-building grid-icon"></i>
                <h2 style="color: var(--primary-color)">${municipality}</h2>
                <div class="row mt-4">
                  <div class="col-4">
                    <div class="metric-label">
                      <i class="bi bi-buildings"></i> Total Schools
                    </div>
                    <div class="metric-value" id="totSchools-${municipality.replace(
                      /\s+/g,
                      "-"
                    )}">0</div>
                  </div>
                  <div class="col-4">
                    <div class="metric-label">
                      <i class="bi bi-map"></i> Total Land Area
                    </div>
                    <div class="metric-value" id="totLandArea-${municipality.replace(
                      /\s+/g,
                      "-"
                    )}" data-raw-value="0">0 sqm</div>
                  </div>
                  <div class="col-4">
                    <div class="metric-label">
                      <i class="bi bi-file-earmark-text"></i> With Title
                    </div>
                    <div class="metric-value" id="withTitle-${municipality.replace(
                      /\s+/g,
                      "-"
                    )}">0</div>
                  </div>
                </div>
                <button class="btn btn-district" onclick="showMunicipalitySchools('${municipality}', '${cdDistrict}')">
                  <i class="bi bi-list-ul"></i> View Schools
                </button>
              </div>
            </div>
          `;

          cardsContainer.innerHTML += cardHtml;
        });

        // Update metrics for each municipality
        updateMunicipalityMetrics(cdDistrict);
      }

      // Update metrics for all municipalities in a CD district
      function updateMunicipalityMetrics(cdDistrict) {
        // Get municipalities for this CD district
        const municipalities = districtSchoolsMapping[cdDistrict] || [];
        if (municipalities.length === 0) return;

        // Determine which district number this CD corresponds to (for data fetching)
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

        // Make sure we have data for this district
        if (
          !window.schoolData ||
          !window.schoolData[districtNumber] ||
          !Array.isArray(window.schoolData[districtNumber])
        ) {
          // If no data, fetch it first
          fetch(
            `phpp/dashboard/fetchLandOwner.php?district=${districtNumber}`
          )
            .then((response) => response.json())
            .then((data) => {
              if (data && Array.isArray(data)) {
                window.schoolData[districtNumber] = data;
                // Now update metrics
                updateMunicipalityMetricsWithData(
                  municipalities,
                  districtNumber
                );
              }
            })
            .catch((error) =>
              console.error(
                `Error fetching data for district ${districtNumber}:`,
                error
              )
            );
        } else {
          // We already have data, update metrics
          updateMunicipalityMetricsWithData(municipalities, districtNumber);
        }
      }

      // Helper function to update municipality metrics with available data
      function updateMunicipalityMetricsWithData(municipalities, districtNumber) {
        const schools = window.schoolData[districtNumber] || [];

        municipalities.forEach((municipality) => {
          // Filter schools where school.CDName matches the municipality
          const municipalitySchools = schools.filter((school) => {
            const cdName = school.CDName || "";
            return cdName === municipality;
          });

          // Calculate metrics
          const totalSchools = municipalitySchools.length;

          const totalLandArea = municipalitySchools.reduce((sum, school) => {
            const landArea = school.totalLandArea || school.land_area || 0;
            return (
              sum +
              (typeof landArea === "number"
                ? landArea
                : parseFloat(landArea) || 0)
            );
          }, 0);

          const withTitle = municipalitySchools.filter(
            (school) =>
              (school.titleNumber && school.titleNumber !== "N/A") ||
              (school.title_no && school.title_no !== "N/A")
          ).length;

          // Prepare municipality ID by replacing spaces with hyphens
          const municipalityId = municipality.replace(/\s+/g, "-");

          // Find the HTML elements
          const totSchoolsEl = document.getElementById(`totSchools-${municipalityId}`);
          const totLandAreaEl = document.getElementById(`totLandArea-${municipalityId}`);
          const withTitleEl = document.getElementById(`withTitle-${municipalityId}`);

          // Update the elements if they exist
          if (totSchoolsEl) totSchoolsEl.textContent = totalSchools.toString();

          if (totLandAreaEl) {
            const isMobile = window.innerWidth < 768;
            totLandAreaEl.textContent = formatLandArea(totalLandArea, isMobile);
            totLandAreaEl.dataset.rawValue = totalLandArea;
          }

          if (withTitleEl) withTitleEl.textContent = withTitle.toString();
        });
      }

      // Show schools for a specific municipality
      function showMunicipalitySchools(municipality, cdDistrict) {
        // Store the current municipality for filtering
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
            // For "All Districts" view, we need to find which district this municipality belongs to
            for (const [cd, municipalities] of Object.entries(districtSchoolsMapping)) {
              if (municipalities.includes(municipality)) {
                switch (cd) {
                  case "CD1": districtNumber = 1; break;
                  case "CD2": districtNumber = 2; break;
                  case "CD3": districtNumber = 3; break;
                  case "CD4": districtNumber = 4; break;
                }
                break;
              }
            }
            if (!districtNumber) return; // Municipality not found in any district
        }

        // Store current district
        currentDistrict = districtNumber;

        // Reset pagination to first page
        currentPage = 1;

        // Get DOM elements
        const tableBody = document.getElementById("schoolsTableBody");
        const districtNum = document.getElementById("districtNum");

        if (!tableBody || !districtNum) {
          console.error("Required DOM elements not found");
          return;
        }

        // Show loading indicator
        tableBody.innerHTML = `
          <tr>
            <td colspan="7" class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Loading schools data...</p>
            </td>
          </tr>
        `;

        // Set district/municipality name in the modal
        districtNum.textContent = municipality;

        // Show the modal while data is loading
        try {
          const schoolsModal = new bootstrap.Modal(
            document.getElementById("schoolsModal")
          );
          schoolsModal.show();
        } catch (error) {
          console.error("Error showing modal:", error);
        }

        // Fetch data for the district if not already available
        if (!window.schoolData || !window.schoolData[districtNumber]) {
          fetch(
            `phpp/dashboard/fetchLandOwner.php?district=${districtNumber}`
          )
            .then((response) => {
              if (!response.ok) {
                throw new Error(
                  `Network response was not ok: ${response.status} ${response.statusText}`
                );
              }
              return response.json();
            })
            .then((data) => {
              if (!data || !Array.isArray(data)) {
                throw new Error("Invalid data format received from server");
              }

              // Store the fetched data
              window.schoolData[districtNumber] = data;

              // Filter schools for this municipality
              filterAndDisplayMunicipalitySchools(municipality, districtNumber);
            })
            .catch((error) => {
              console.error("Error fetching school data:", error);

              // Show error message in the table
              if (tableBody) {
                tableBody.innerHTML = `
                  <tr>
                    <td colspan="7" class="text-center text-danger">
                      <i class="bi bi-exclamation-triangle"></i> Error loading data: ${
                        error.message || "Unknown error"
                      }
                    </td>
                  </tr>
                `;
              }

              // Show error message to user
              Swal.fire({
                icon: "error",
                title: "Data Loading Error",
                text: "Could not load school data. Please try again later.",
              });
            });
        } else {
          // We already have the data, just filter and display
          filterAndDisplayMunicipalitySchools(municipality, districtNumber);
        }
      }

      // Filter and display schools for a municipality
      function filterAndDisplayMunicipalitySchools(municipality, districtNumber) {
        const schools = window.schoolData[districtNumber] || [];

        // Filter schools for this municipality - FIXED to use CDName instead of Municipality
        filteredSchools = schools.filter((school) => {
          // Check both CDName and Municipality fields to be safe
          const schoolMunicipality = school.Municipality || school.municipality || "";
          const schoolCDName = school.CDName || school.cdname || "";
          
          return schoolCDName === municipality || schoolMunicipality === municipality;
        });

        // Display the schools in the table with pagination
        displaySchoolsWithPagination();
      }

      // Add a new function to fetch data for all districts
      function fetchAllDistrictsData() {
        // Fetch data for all 4 districts
        for (let district = 1; district <= 4; district++) {
          fetchDistrictData(district);
        }
      }

      // Add a function to fetch data for a specific district
      function fetchDistrictData(district) {
        fetch(`phpp/dashboard/fetchLandOwner.php?district=${district}`)
          .then((response) => {
            if (!response.ok) {
              throw new Error(
                `Network response was not ok: ${response.status} ${response.statusText}`
              );
            }
            return response.json();
          })
          .then((data) => {
            // Validate data
            if (!data || !Array.isArray(data)) {
              throw new Error("Invalid data format received from server");
            }

            // Store the fetched data for this district
            if (typeof window.schoolData !== "object") {
              window.schoolData = {};
            }
            window.schoolData[district] = data;

            // Update district metrics for this district
            updateDistrictMetricsForDistrict(district);
          })
          .catch((error) => {
            console.error(
              `Error fetching data for District ${district}:`,
              error
            );
          });
      }

      // Initialize UI with empty values
      function updateDistrictMetricsUI() {
        for (let district = 1; district <= 4; district++) {
          const totSchoolsEl = document.getElementById(
            `totSchools-${district}`
          );
          const totLandAreaEl = document.getElementById(
            `totLandArea-${district}`
          );
          const withTitleEl = document.getElementById(`withTitle-${district}`);

          if (totSchoolsEl) totSchoolsEl.textContent = "0";
          if (totLandAreaEl) totLandAreaEl.textContent = "0 sqm";
          if (withTitleEl) withTitleEl.textContent = "0";
        }
      }

      // Fetch data for a specific district when its button is clicked
      function showSchools(district) {
        if (!district || isNaN(district)) {
          console.error("Invalid district number");
          return;
        }

        // Store current district
        currentDistrict = district;

        // Clear current municipality
        currentMunicipality = null;

        // Reset pagination to first page
        currentPage = 1;

        // Get DOM elements
        const tableBody = document.getElementById("schoolsTableBody");
        const districtNum = document.getElementById("districtNum");

        if (!tableBody || !districtNum) {
          console.error("Required DOM elements not found");
          return;
        }

        // Show loading indicator
        tableBody.innerHTML = `
          <tr>
            <td colspan="7" class="text-center">
              <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Loading...</span>
              </div>
              <p class="mt-2">Loading district data...</p>
            </td>
          </tr>
        `;

        // Set district number in the modal
        districtNum.textContent = `District ${district}`;

        // Show the modal while data is loading
        try {
          const schoolsModal = new bootstrap.Modal(
            document.getElementById("schoolsModal")
          );
          schoolsModal.show();
        } catch (error) {
          console.error("Error showing modal:", error);
        }

        // Fetch data for the specific district
        fetch(`phpp/dashboard/fetchLandOwner.php?district=${district}`)
          .then((response) => {
            if (!response.ok) {
              throw new Error(
                `Network response was not ok: ${response.status} ${response.statusText}`
              );
            }
            return response.json();
          })
          .then((data) => {
            // Validate data
            if (!data || !Array.isArray(data)) {
              throw new Error("Invalid data format received from server");
            }

            // Store the fetched data for this district
            if (typeof window.schoolData !== "object") {
              window.schoolData = {};
            }
            window.schoolData[district] = data;

            // Set filtered schools to all schools initially
            filteredSchools = data;

            // Update district metrics for this district only
            updateDistrictMetricsForDistrict(district);

            // Display the schools in the table with pagination
            displaySchoolsWithPagination();
          })
          .catch((error) => {
            console.error("Error fetching school data:", error);

            // Show error message in the table
            if (tableBody) {
              tableBody.innerHTML = `
                <tr>
                  <td colspan="7" class="text-center text-danger">
                    <i class="bi bi-exclamation-triangle"></i> Error loading data: ${
                      error.message || "Unknown error"
                    }
                  </td>
                </tr>
              `;
            }

            // Show error message to user
            try {
              Swal.fire({
                icon: "error",
                title: "Data Loading Error",
                text: "Could not load school data. Please try again later.",
              });
            } catch (swalError) {
              console.error("Error showing alert:", swalError);
              alert("Error loading data. Please try again later.");
            }
          });
      }

      // Update metrics for a specific district
      function updateDistrictMetricsForDistrict(district) {
        if (!district || isNaN(district)) return;

        // Safely access school data
        const schools =
          window.schoolData && window.schoolData[district]
            ? window.schoolData[district]
            : [];

        if (!Array.isArray(schools)) {
          console.error("Invalid schools data for district", district);
          return;
        }

        // Calculate metrics
        const totalSchools = schools.length;

        const totalLandArea = schools.reduce((sum, school) => {
          // Safely parse land area value
          const landArea =
            school && school.totalLandArea
              ? typeof school.totalLandArea === "number"
                ? school.totalLandArea
                : Number.parseFloat(school.totalLandArea) || 0
              : school && school.land_area
              ? Number.parseFloat(school.land_area) || 0
              : 0;

          return sum + landArea;
        }, 0);

        const withTitle = schools.filter(
          (school) =>
            (school && school.titleNumber && school.titleNumber !== "N/A") ||
            (school && school.title_no && school.title_no !== "N/A")
        ).length;

        // Update DOM elements
        const totSchoolsEl = document.getElementById(`totSchools-${district}`);
        const totLandAreaEl = document.getElementById(
          `totLandArea-${district}`
        );
        const withTitleEl = document.getElementById(`withTitle-${district}`);

        if (totSchoolsEl) totSchoolsEl.textContent = totalSchools.toString();

        // Use our responsive formatter for land area
        if (totLandAreaEl) {
          const isMobile = window.innerWidth < 768;
          totLandAreaEl.textContent = formatLandArea(totalLandArea, isMobile);
          // Store the raw value as a data attribute for later updates
          totLandAreaEl.dataset.rawValue = totalLandArea;
        }

        if (withTitleEl) withTitleEl.textContent = withTitle.toString();
      }

      // Function to handle pagination
      function displaySchoolsWithPagination() {
        if (!currentDistrict) return;

        // Get the total number of schools
        const totalSchools = filteredSchools.length;

        // Calculate total pages
        const totalPages = Math.ceil(totalSchools / itemsPerPage);

        // Make sure current page is valid
        if (currentPage < 1) currentPage = 1;
        if (currentPage > totalPages) currentPage = totalPages;

        // Calculate start and end indices for the current page
        const startIndex = (currentPage - 1) * itemsPerPage;
        const endIndex = Math.min(startIndex + itemsPerPage, totalSchools);

        // Get the schools for the current page
        const schoolsForCurrentPage = filteredSchools.slice(
          startIndex,
          endIndex
        );

        // Display the schools in the table
        displaySchoolsInTable(schoolsForCurrentPage);

        // Update pagination controls
        updatePaginationControls(totalSchools, startIndex, endIndex);
      }

      // Function to update pagination controls
      function updatePaginationControls(totalSchools, startIndex, endIndex) {
        const paginationElement = document.getElementById("schoolsPagination");
        const paginationInfo = document.getElementById("paginationInfo");
        const startRecord = document.getElementById("startRecord");
        const endRecord = document.getElementById("endRecord");
        const totalRecords = document.getElementById("totalRecords");

        if (
          !paginationElement ||
          !paginationInfo ||
          !startRecord ||
          !endRecord ||
          !totalRecords
        ) {
          console.error("Pagination elements not found");
          return;
        }

        // Update pagination info
        startRecord.textContent = totalSchools > 0 ? startIndex + 1 : 0;
        endRecord.textContent = endIndex;
        totalRecords.textContent = totalSchools;

        // Calculate total pages
        const totalPages = Math.ceil(totalSchools / itemsPerPage);

        // Clear previous pagination
        paginationElement.innerHTML = "";

        // Don't show pagination if there's only one page or no data
        if (totalPages <= 1) {
          return;
        }

        // Create pagination HTML
        let paginationHTML = "";

        // Previous button
        paginationHTML += `
          <li class="page-item ${currentPage === 1 ? "disabled" : ""}">
            <a class="page-link" href="#" onclick="changePage(${
              currentPage - 1
            }); return false;">
              <i class="bi bi-chevron-left"></i>
            </a>
          </li>
        `;

        // Determine which page numbers to show
        let startPage = Math.max(1, currentPage - 2);
        let endPage = Math.min(totalPages, startPage + 4);

        // Adjust if we're near the end
        if (endPage - startPage < 4) {
          startPage = Math.max(1, endPage - 4);
        }

        // First page
        if (startPage > 1) {
          paginationHTML += `
            <li class="page-item">
              <a class="page-link" href="#" onclick="changePage(1); return false;">1</a>
            </li>
          `;

          if (startPage > 2) {
            paginationHTML += `
              <li class="page-item disabled">
                <a class="page-link" href="#">...</a>
              </li>
            `;
          }
        }

        // Page numbers
        for (let i = startPage; i <= endPage; i++) {
          paginationHTML += `
            <li class="page-item ${i === currentPage ? "active" : ""}">
              <a class="page-link" href="#" onclick="changePage(${i}); return false;">${i}</a>
            </li>
          `;
        }

        // Last page
        if (endPage < totalPages) {
          if (endPage < totalPages - 1) {
            paginationHTML += `
              <li class="page-item disabled">
                <a class="page-link" href="#">...</a>
              </li>
            `;
          }

          paginationHTML += `
            <li class="page-item">
              <a class="page-link" href="#" onclick="changePage(${totalPages}); return false;">${totalPages}</a>
            </li>
          `;
        }

        // Next button
        paginationHTML += `
          <li class="page-item ${currentPage === totalPages ? "disabled" : ""}">
            <a class="page-link" href="#" onclick="changePage(${
              currentPage + 1
            }); return false;">
              <i class="bi bi-chevron-right"></i>
            </a>
          </li>
        `;

        // Set the pagination HTML
        paginationElement.innerHTML = paginationHTML;
      }

      // Function to change page
      function changePage(page) {
        currentPage = page;
        displaySchoolsWithPagination();
      }

      // Function to change items per page
      function changeItemsPerPage() {
        const select = document.getElementById("itemsPerPage");
        if (select) {
          itemsPerPage = Number.parseInt(select.value);
          currentPage = 1; // Reset to first page
          displaySchoolsWithPagination();
        }
      }

      // Display schools in the table
      function displaySchoolsInTable(schools) {
        const tableBody = document.getElementById("schoolsTableBody");
        if (!tableBody) {
          console.error("Table body element not found");
          return;
        }

        // Clear the table
        tableBody.innerHTML = "";

        if (!Array.isArray(schools)) {
          console.error("Invalid schools data");
          tableBody.innerHTML = `<tr><td colspan="7" class="text-center text-danger">Invalid data format</td></tr>`;
          return;
        }

        if (schools.length === 0) {
          tableBody.innerHTML = `<tr><td colspan="7" class="text-center">No schools found in this district</td></tr>`;
          return;
        }

        // Check if we're on mobile
        const isMobile = window.innerWidth < 768;

        // Add each school to the table
        schools.forEach((school) => {
          if (!school) return;

          // Safely access school properties
          const schoolId = school.schoolId || school.school_id || "N/A";
          const schoolName = school.SchoolName || school.school_name || "N/A";
          const yearEstablished =
            school.year_estab || school.year_established || "N/A";
          const totalLandArea = school.totalLandArea || school.land_area || 0;
          const transferInstrument =
            school.transfer_doc || school.transfer_instrument || "N/A";
          const titleNumber = school.titleNumber || school.title_no || "N/A";

          const row = document.createElement("tr");
          row.innerHTML = `
            <td>${safeText(schoolId)}</td>
            <td>${safeText(schoolName)}</td>
            <td>${safeText(yearEstablished)}</td>
            <td>${formatLandArea(totalLandArea, isMobile)}</td>
            <td>${safeText(transferInstrument)}</td>
            <td>${safeText(titleNumber)}</td>
            <td>
              <div class="btn-group">
                <button class="btn btn-sm btn-view" onclick="showSchoolDetails('${safeAttribute(
                  String(schoolId)
                )}', ${currentDistrict})">
                  <i class="bi bi-eye"></i> View
                </button>
                <button class="btn btn-sm btn-outline-primary" onclick="selectSchoolForUpdate('${safeAttribute(
                  String(schoolId)
                )}', ${currentDistrict})">
                  <i class="bi bi-check"></i> Select
                </button>
              </div>
            </td>
          `;
          tableBody.appendChild(row);
        });
      }

      // Select a school for update
      function selectSchoolForUpdate(schoolId, district) {
        // Update the selected school
        selectedSchoolId = schoolId;
        selectedDistrict = district;

        // Enable the update button
        document.getElementById("updateButton").disabled = false;

        // Highlight the selected row
        const rows = document.querySelectorAll("#schoolsTableBody tr");
        rows.forEach((row) => {
          if (row.cells[0].textContent === schoolId) {
            row.classList.add("table-primary");
          } else {
            row.classList.remove("table-primary");
          }
        });

        // Show a toast notification
        Swal.fire({
          toast: true,
          position: "top-end",
          icon: "success",
          title: "School selected for update",
          showConfirmButton: false,
          timer: 1500,
        });
      }

      // Fix for the showUpdateSchoolModal function
      function showUpdateSchoolModal() {
        if (!selectedSchoolId || !selectedDistrict) {
          Swal.fire({
            icon: "warning",
            title: "No School Selected",
            text: "Please select a school to update first.",
          });
          return;
        }

        // Make sure we're using window.schoolData consistently
        if (!window.schoolData || !window.schoolData[selectedDistrict]) {
          console.error(
            "School data not available for district",
            selectedDistrict
          );
          Swal.fire({
            icon: "error",
            title: "Data Error",
            text: "Could not find school data for the selected district.",
          });
          return;
        }

        // Find the school in the data
        const school = window.schoolData[selectedDistrict].find(
          (s) => String(s.schoolId || s.school_id) === String(selectedSchoolId)
        );

        if (!school) {
          console.error("School not found:", selectedSchoolId);
          Swal.fire({
            icon: "error",
            title: "School Not Found",
            text: "Could not find the selected school in the database.",
          });
          return;
        }

        // Set school name in title
        document.getElementById("updateSchoolNameTitle").textContent =
          school.SchoolName || school.school_name || "School";

        // Fill the form with school data
        document.getElementById("update-district").value = selectedDistrict;
        document.getElementById("update-schoolId").value =
          school.schoolId || school.school_id || "";
        document.getElementById("update-schoolName").value =
          school.SchoolName || school.school_name || "";
        document.getElementById("update-yearEstablished").value =
          school.yearEstablished ||
          school.year_estab ||
          school.year_established ||
          "";
        document.getElementById("update-lotInfo").value =
          school.lotInfo || school.lot_info || "";
        document.getElementById("update-totalLandArea").value =
          school.totalLandArea || school.land_area || "";
        document.getElementById("update-classification").value =
          school.classification || "";
        document.getElementById("update-transferInstrument").value =
          school.transferInstrument ||
          school.transfer_doc ||
          school.transfer_instrument ||
          "";
        document.getElementById("update-titleNumber").value =
          school.titleNumber || school.title_no || "";
        document.getElementById("update-proclamationNumber").value =
          school.proclamationNumber ||
          school.proc_no ||
          school.proclamation_no ||
          "";
        document.getElementById("update-latitude").value =
          school.latitude || "";
        document.getElementById("update-longitude").value =
          school.longitude || "";

        // Update document file names
        const ownershipFilename = document.getElementById(
          "update-ownership-filename"
        );
        const sitePlanFilename = document.getElementById(
          "update-siteplan-filename"
        );

        if (school.ownershipDoc) {
          ownershipFilename.textContent = school.ownershipDoc;
        } else {
          ownershipFilename.textContent = "No file uploaded";
        }

        if (school.sitePlanDoc) {
          sitePlanFilename.textContent = school.sitePlanDoc;
        } else {
          sitePlanFilename.textContent = "No file uploaded";
        }

        // Show the modal
        const updateModal = new bootstrap.Modal(
          document.getElementById("updateSchoolModal")
        );
        updateModal.show();

        // Initialize map after modal is shown
        document.getElementById("updateSchoolModal").addEventListener(
          "shown.bs.modal",
          function () {
            initUpdateMap(school);
          },
          { once: true }
        );
      }

      // Improved function to close modals properly
      function closeModal(modalId) {
        try {
          const modalElement = document.getElementById(modalId);
          if (!modalElement) return;

          const modalInstance = bootstrap.Modal.getInstance(modalElement);
          if (modalInstance) {
            // Hide the modal using Bootstrap's API
            modalInstance.hide();

            // Let Bootstrap handle the backdrop removal naturally
            // The hidden.bs.modal event listener will clean up if needed
          }
        } catch (error) {
          console.error(`Error closing modal ${modalId}:`, error);

          // Manual cleanup as a fallback
          cleanupModalBackdrop();
        }
      }

      // Show school details and map
      function showSchoolDetails(schoolId, district) {
        if (!schoolId || !district || isNaN(district)) {
          console.error("Invalid school ID or district");
          return;
        }

        // Debug information
        console.log("Looking for school:", schoolId, "in district:", district);

        // Safely access school data
        if (
          !window.schoolData ||
          !window.schoolData[district] ||
          !Array.isArray(window.schoolData[district])
        ) {
          console.error("School data not available for district", district);
          console.log("Available data:", window.schoolData);
          return;
        }

        // Log available schools for debugging
        console.log(
          "Available schools in district:",
          window.schoolData[district].map((s) => s.schoolId || s.school_id)
        );

        // Find the school in the data - convert IDs to strings for comparison
        const school = window.schoolData[district].find(
          (s) =>
            s &&
            (String(s.schoolId) === String(schoolId) ||
              String(s.school_id) === String(schoolId))
        );

        if (!school) {
          console.error("School not found:", schoolId);
          // Try a more flexible search if exact match fails
          const possibleSchool = window.schoolData[district].find(
            (s) =>
              s &&
              ((s.schoolId && s.schoolId.toString().includes(schoolId)) ||
                (s.school_id && s.school_id.toString().includes(schoolId)))
          );

          if (possibleSchool) {
            console.log("Found possible match:", possibleSchool);
            // Use the possible match
            showSchoolDetailsWithData(possibleSchool, district);
            return;
          }

          // Show error message to user
          Swal.fire({
            icon: "error",
            title: "School Not Found",
            text: `Could not find school with ID: ${schoolId} in district ${district}`,
          });
          return;
        }

        // If school is found, show details
        showSchoolDetailsWithData(school, district);
      }

      // New helper function to display school details
      function showSchoolDetailsWithData(school, district) {
        // Store the selected district for PDF viewing
        selectedDistrict = district;

        // Get DOM elements
        const schoolNameTitle = document.getElementById("schoolNameTitle");
        const detailSchoolId = document.getElementById("detail-schoolId");
        const detailSchoolName = document.getElementById("detail-schoolName");
        const detailYearEstablished = document.getElementById(
          "detail-yearEstablished"
        );
        const detailLotInfo = document.getElementById("detail-lotInfo");
        const detailTotalLandArea = document.getElementById(
          "detail-totalLandArea"
        );
        const detailClassification = document.getElementById(
          "detail-classification"
        );
        const detailTransferInstrument = document.getElementById(
          "detail-transferInstrument"
        );
        const detailTitleNumber = document.getElementById("detail-titleNumber");
        const detailProclamationNumber = document.getElementById(
          "detail-proclamationNumber"
        );
        const detailLatitude = document.getElementById("detail-latitude");
        const detailLongitude = document.getElementById("detail-longitude");
        const detailOwnershipDoc = document.getElementById(
          "detail-ownershipDoc"
        );
        const detailSitePlanDoc = document.getElementById("detail-sitePlanDoc");

        // Safely access school properties
        const schoolId = school.school_id || "N/A";
        const schoolName = school.SchoolName || "N/A";
        const yearEstablished = school.year_estab || "N/A";
        const lotInfo = school.lot_info || "N/A";
        const totalLandArea = school.land_area || 0;
        const classification = school.classification || "N/A";
        const transferInstrument = school.transfer_doc || "N/A";
        const titleNumber = school.title_no || "N/A";
        const proclamationNumber = school.proc_no || "N/A";
        const latitude = school.latitude || 0;
        const longitude = school.longitude || 0;

        // Extract just the filenames from the paths
        const ownershipDocFull = school.ownership_doc || "No document uploaded";
        const sitePlanDocFull = school.siteplan_doc || "No document uploaded";

        const ownershipDoc =
          ownershipDocFull.split("/").pop() || ownershipDocFull;
        const sitePlanDoc = sitePlanDocFull.split("/").pop() || sitePlanDocFull;

        // Set school name in title
        if (schoolNameTitle) schoolNameTitle.textContent = schoolName;

        // Fill in the details table
        if (detailSchoolId) detailSchoolId.textContent = schoolId;
        if (detailSchoolName) detailSchoolName.textContent = schoolName;
        if (detailYearEstablished)
          detailYearEstablished.textContent = yearEstablished;
        if (detailLotInfo) detailLotInfo.textContent = lotInfo;

        // Use responsive formatting for land area
        if (detailTotalLandArea) {
          const rawLandArea =
            typeof totalLandArea === "number"
              ? totalLandArea
              : Number.parseFloat(totalLandArea) || 0;

          // Store raw value as data attribute
          detailTotalLandArea.dataset.rawValue = rawLandArea;

          // Format based on current screen size
          const isMobile = window.innerWidth < 768;
          detailTotalLandArea.textContent = formatLandArea(
            rawLandArea,
            isMobile
          );
        }

        if (detailClassification)
          detailClassification.textContent = classification;
        if (detailTransferInstrument)
          detailTransferInstrument.textContent = transferInstrument;
        if (detailTitleNumber) detailTitleNumber.textContent = titleNumber;
        if (detailProclamationNumber)
          detailProclamationNumber.textContent = proclamationNumber;
        if (detailLatitude) detailLatitude.textContent = latitude.toString();
        if (detailLongitude) detailLongitude.textContent = longitude.toString();
        if (detailOwnershipDoc) detailOwnershipDoc.textContent = ownershipDoc;
        if (detailSitePlanDoc) detailSitePlanDoc.textContent = sitePlanDoc;

        // Close the schools modal properly with backdrop cleanup
        closeModal("schoolsModal");

        // Show the details modal
        const detailsModal = new bootstrap.Modal(
          document.getElementById("schoolDetailsModal")
        );
        detailsModal.show();

        // Initialize map after modal is shown to ensure proper rendering
        document.getElementById("schoolDetailsModal").addEventListener(
          "shown.bs.modal",
          function () {
            initMap(school);
          },
          { once: true }
        );
      }

      // Initialize map
      function initMap(school) {
        if (!school) {
          console.error("No school data provided for map");
          return;
        }

        const mapContainer = document.getElementById("schoolMap");
        if (!mapContainer) {
          console.error("Map container not found");
          return;
        }

        // Safely access school properties
        const schoolName = school.SchoolName || school.school_name || "School";
        const totalLandArea = school.totalLandArea || school.land_area || 0;
        const latitude = Number.parseFloat(school.latitude) || 14.5995; // Default to Manila if no coordinates
        const longitude = Number.parseFloat(school.longitude) || 120.9842;

        // Clear previous map instance if exists
        if (map) {
          try {
            map.remove();
          } catch (error) {
            console.error("Error removing previous map:", error);
          }
        }

        // Clear the container before initializing
        mapContainer.innerHTML = "";

        try {
          // Create a new map centered on the school location
          map = L.map("schoolMap").setView([latitude, longitude], 15);

          // Add OpenStreetMap tile layer
          L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
            attribution:
              '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
          }).addTo(map);

          // Add marker for the school location
          const marker = L.marker([latitude, longitude]).addTo(map);

          // Format land area based on screen size
          const isMobile = window.innerWidth < 768;
          const formattedLandArea = formatLandArea(totalLandArea, isMobile);

          marker
            .bindPopup(
              `<b>${safeText(
                schoolName
              )}</b><br>Land Area: ${formattedLandArea}`
            )
            .openPopup();

          // Add a circle to represent the school area
          const areaRadius = Math.sqrt(
            Number.parseFloat(totalLandArea) / Math.PI
          );
          L.circle([latitude, longitude], {
            color: "#2e7d32",
            fillColor: "#4caf50",
            fillOpacity: 0.2,
            radius: areaRadius / 10, // Scale down for visualization
          }).addTo(map);

          // Force map to recalculate size
          setTimeout(() => {
            if (map) map.invalidateSize();
          }, 100);
        } catch (error) {
          console.error("Error initializing map:", error);
          mapContainer.innerHTML = `<div class="alert alert-danger">Error loading map: ${error.message}</div>`;
        }
      }

      // MODIFIED: View PDF function that uses window.open as a popup window instead of a new tab
      function viewPdf(type) {
        const schoolId = document.getElementById("detail-schoolId").textContent;
        const district = selectedDistrict;

        const school = window.schoolData[district].find(
          (s) => String(s.school_id || s.schoolId) === String(schoolId)
        );

        if (!school) {
          Swal.fire({
            icon: "warning",
            title: "School Not Found",
            text: "Unable to find the school information.",
          });
          return;
        }

        const docPath =
          type === "ownership" ? school.ownership_doc : school.siteplan_doc;

        if (!docPath) {
          Swal.fire({
            icon: "warning",
            title: "No Document Available",
            text: `No ${
              type === "ownership" ? "ownership document" : "school site plan"
            } has been uploaded for this school.`,
          });
          return;
        }

        // Extract just the filename from the path
        const filename = docPath.split("/").pop();

        // Update the correct detail element to show the filename
        const detailElementId =
          type === "ownership" ? "detail-ownershipDoc" : "detail-sitePlanDoc";
        document.getElementById(detailElementId).textContent =
          filename || docPath;

        // Construct the PDF URL
        let pdfUrl = docPath;

        // If docPath is just a filename or partial path
        if (!docPath.startsWith("http") && !docPath.startsWith("/")) {
          pdfUrl = `phpp/uploads/${docPath}`;
        }

        console.log("Opening PDF URL:", pdfUrl);

        // Set the size of the popup window
        const width = 800;
        const height = 600;

        // Calculate the position of the popup to center it
        const left = window.innerWidth / 2 - width / 2;
        const top = window.innerHeight / 2 - height / 2;

        // Open a new centered window with the PDF
        window.open(
          pdfUrl,
          "pdfWindow",
          `width=${width},height=${height},left=${left},top=${top},resizable=yes,scrollbars=yes,status=yes`
        );
      }

      // Usage functions remain the same
      function viewOwnershipPdf() {
        viewPdf("ownership");
      }

      function viewSchoolsitePdf() {
        viewPdf("siteplan");
      }

      // Export to Excel function
      function exportToExcel() {
        if (!currentDistrict) {
          console.error("No district selected");
          return;
        }

        // Use filtered schools for export
        const schools = filteredSchools;

        if (!Array.isArray(schools) || schools.length === 0) {
          alert("No data available to export");
          return;
        }

        try {
          // Create CSV content
          let csvContent = "data:text/csv;charset=utf-8,";
          csvContent +=
            "School ID,School Name,Year Established,Lot Information,Total Land Area,Classification,Instrument of Transfer,Title Number,Proclamation Number,Latitude,Longitude\n";

          schools.forEach((school) => {
            if (!school) return;

            // Safely access school properties
            const schoolId = safeCSV(school.schoolId || school.school_id || "");
            const schoolName = safeCSV(
              school.SchoolName || school.school_name || ""
            );
            const yearEstablished = safeCSV(
              school.year_estab || school.year_established || ""
            );
            const lotInfo = safeCSV(school.lotInfo || school.lot_info || "");
            const totalLandArea = school.totalLandArea || school.land_area || 0;
            const classification = safeCSV(school.classification || "");
            const transferInstrument = safeCSV(
              school.transfer_doc || school.transfer_instrument || ""
            );
            const titleNumber = safeCSV(
              school.titleNumber || school.title_no || ""
            );
            const proclamationNumber = safeCSV(
              school.proc_no || school.proclamation_no || ""
            );
            const latitude = school.latitude || 0;
            const longitude = school.longitude || 0;

            csvContent += `${schoolId},${schoolName},${yearEstablished},"${lotInfo}",${totalLandArea},${classification},"${transferInstrument}",${titleNumber},${proclamationNumber},${latitude},${longitude}\n`;
          });

          // Create download link
          const encodedUri = encodeURI(csvContent);
          const link = document.createElement("a");
          link.setAttribute("href", encodedUri);

          // Set filename based on current view (district or municipality)
          let filename = `School_Land_Ownership_`;
          if (currentMunicipality) {
            filename += `${currentMunicipality.replace(/\s+/g, "_")}.csv`;
          } else {
            filename += `District_${currentDistrict}.csv`;
          }

          link.setAttribute("download", filename);
          document.body.appendChild(link);

          // Trigger download
          link.click();
          document.body.removeChild(link);
        } catch (error) {
          console.error("Error exporting to Excel:", error);
          alert("Error exporting data. Please try again.");
        }
      }

      // Helper functions for safe text handling
      function safeText(text) {
        if (text === null || text === undefined) return "";
        return String(text).replace(/</g, "&lt;").replace(/>/g, "&gt;");
      }

      function safeAttribute(text) {
        if (text === null || text === undefined) return "";
        return String(text).replace(/"/g, "&quot;");
      }

      function safeCSV(text) {
        if (text === null || text === undefined) return "";
        return String(text).replace(/"/g, '""');
      }

      // Initialize the map in the update modal - FIXED FUNCTION
      function initUpdateMap(school) {
        // Clear previous map instance if exists
        if (updateMap) {
          updateMap.remove();
        }

        // Get the correct school name from various possible properties
        const schoolName = school.SchoolName || school.school_name || "School";

        // Get coordinates with fallback to default values
        const latitude = parseFloat(school.latitude) || 14.5995; // Default to Manila if no coordinates
        const longitude = parseFloat(school.longitude) || 120.9842;

        // Create a new map centered on the school location
        updateMap = L.map("update-map-container").setView(
          [latitude, longitude],
          15
        );

        // Add OpenStreetMap tile layer
        L.tileLayer("https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {
          attribution:
            '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        }).addTo(updateMap);

        // Add marker for the school location with green color
        updateMarker = L.marker([latitude, longitude], {
          draggable: true, // Make the marker draggable
        }).addTo(updateMap);

        // Add popup to the marker with the correct school name
        updateMarker.bindPopup(`<b>${schoolName}</b>`).openPopup();

        // Update coordinates when marker is dragged
        updateMarker.on("dragend", function (event) {
          const marker = event.target;
          const position = marker.getLatLng();

          // Update the form fields
          document.getElementById("update-latitude").value =
            position.lat.toFixed(6);
          document.getElementById("update-longitude").value =
            position.lng.toFixed(6);
        });
        // Allow clicking on the map to move the marker
        updateMap.on("click", function (e) {
          // Update marker position
          updateMarker.setLatLng(e.latlng);

          // Update the form fields
          document.getElementById("update-latitude").value =
            e.latlng.lat.toFixed(6);
          document.getElementById("update-longitude").value =
            e.latlng.lng.toFixed(6);
        });

        // Update marker when coordinates are changed manually
        document
          .getElementById("update-latitude")
          .addEventListener("change", updateMarkerFromInputs);
        document
          .getElementById("update-longitude")
          .addEventListener("change", updateMarkerFromInputs);

        // Force map to recalculate size
        setTimeout(() => {
          updateMap.invalidateSize();
        }, 100);
      }

      // Update marker position from input fields
      function updateMarkerFromInputs() {
        const lat = parseFloat(
          document.getElementById("update-latitude").value
        );
        const lng = parseFloat(
          document.getElementById("update-longitude").value
        );

        if (!isNaN(lat) && !isNaN(lng) && updateMarker && updateMap) {
          const newLatLng = L.latLng(lat, lng);
          updateMarker.setLatLng(newLatLng);
          updateMap.panTo(newLatLng);
        }
      }

      // Use current location
      function useCurrentLocation() {
        if (navigator.geolocation) {
          navigator.geolocation.getCurrentPosition(
            (position) => {
              const lat = position.coords.latitude;
              const lng = position.coords.longitude;

              // Update form fields
              document.getElementById("update-latitude").value = lat.toFixed(6);
              document.getElementById("update-longitude").value =
                lng.toFixed(6);

              // Update marker and map
              if (updateMarker && updateMap) {
                const latLng = L.latLng(lat, lng);
                updateMarker.setLatLng(latLng);
                updateMap.panTo(latLng);
                updateMap.setZoom(15);
              }

              // Show notification
              Swal.fire({
                toast: true,
                position: "top-end",
                icon: "success",
                title: "Current location set",
                showConfirmButton: false,
                timer: 1500,
              });
            },
            (error) => {
              // Handle errors
              let errorMessage = "Unable to retrieve your location";
              switch (error.code) {
                case error.PERMISSION_DENIED:
                  errorMessage = "Location access was denied by the user.";
                  break;
                case error.POSITION_UNAVAILABLE:
                  errorMessage = "Location information is unavailable.";
                  break;
                case error.TIMEOUT:
                  errorMessage = "The request to get user location timed out.";
                  break;
              }

              Swal.fire({
                icon: "error",
                title: "Geolocation Error",
                text: errorMessage,
              });
            }
          );
        } else {
          Swal.fire({
            icon: "error",
            title: "Geolocation Not Supported",
            text: "Your browser does not support geolocation.",
          });
        }
      }

      // Function to update school information - FIXED FOR MODAL BACKDROP ISSUE
      function updateSchool() {
        // Get the form element
        const form = document.getElementById("updateSchoolForm");

        // Create FormData object from the form
        const formData = new FormData();

        // Add all the form fields to the FormData
        formData.append(
          "update-schoolId",
          document.getElementById("update-schoolId").value
        );
        formData.append(
          "update-yearEstablished",
          document.getElementById("update-yearEstablished").value
        );
        formData.append(
          "update-lotInfo",
          document.getElementById("update-lotInfo").value
        );
        formData.append(
          "update-totalLandArea",
          document.getElementById("update-totalLandArea").value
        );
        formData.append(
          "update-classification",
          document.getElementById("update-classification").value
        );
        formData.append(
          "update-transferInstrument",
          document.getElementById("update-transferInstrument").value
        );
        formData.append(
          "update-titleNumber",
          document.getElementById("update-titleNumber").value
        );
        formData.append(
          "update-proclamationNumber",
          document.getElementById("update-proclamationNumber").value
        );
        formData.append(
          "update-latitude",
          document.getElementById("update-latitude").value
        );
        formData.append(
          "update-longitude",
          document.getElementById("update-longitude").value
        );

        // Add file inputs if files are selected
        const ownershipDocInput = document.getElementById(
          "update-ownershipDoc"
        );
        if (ownershipDocInput.files.length > 0) {
          formData.append("update-ownershipDoc", ownershipDocInput.files[0]);
        }

        const sitePlanDocInput = document.getElementById("update-sitePlanDoc");
        if (sitePlanDocInput.files.length > 0) {
          formData.append("update-sitePlanDoc", sitePlanDocInput.files[0]);
        }

        // Show loading indicator
        Swal.fire({
          title: "Updating...",
          text: "Please wait while we update the school information",
          allowOutsideClick: false,
          didOpen: () => {
            Swal.showLoading();
          },
        });

        // Send the form data to the PHP script
        fetch("phpp/dashboard/updateLandInfo.php", {
          method: "POST",
          body: formData,
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error(`HTTP error! Status: ${response.status}`);
            }
            return response.json();
          })
          .then((data) => {
            // First close the SweetAlert loading indicator
            Swal.close();

            // Then close the modal properly
            const modalElement = document.getElementById("updateSchoolModal");
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
              modalInstance.hide();
            }

            // Show success or error message after a short delay
            setTimeout(() => {
              if (data.success) {
                Swal.fire({
                  icon: "success",
                  title: "School Updated",
                  text:
                    data.message ||
                    "School information has been updated successfully",
                });

                // Refresh the schools table
                if (currentDistrict) {
                  fetchDistrictData(currentDistrict);

                  // Refresh the current view based on context
                  if (currentMunicipality) {
                    showMunicipalitySchools(
                      currentMunicipality,
                      getCDDistrictFromMunicipality(currentMunicipality)
                    );
                  } else {
                    showSchools(currentDistrict);
                  }
                }
              } else {
                Swal.fire({
                  icon: "error",
                  title: "Update Failed",
                  text: data.message || "Failed to update school information",
                });
              }
            }, 300);
          })
          .catch((error) => {
            console.error("Error updating school:", error);

            // Close the SweetAlert loading indicator
            Swal.close();

            // Close the modal properly
            const modalElement = document.getElementById("updateSchoolModal");
            const modalInstance = bootstrap.Modal.getInstance(modalElement);
            if (modalInstance) {
              modalInstance.hide();
            }

            // Show error message after a short delay
            setTimeout(() => {
              Swal.fire({
                icon: "error",
                title: "Error",
                text: "An error occurred while updating the school information. Please try again.",
              });
            }, 300);
          });
      }

      // Helper function to get CD district from municipality name
      function getCDDistrictFromMunicipality(municipality) {
        for (const [cdDistrict, municipalities] of Object.entries(
          districtSchoolsMapping
        )) {
          if (municipalities.includes(municipality)) {
            return cdDistrict;
          }
        }
        return null;
      }

      // Function to reset map location
      function resetMapLocation() {
        if (!updateMap || !updateMarker) return;

        // Get the original coordinates from the school data
        const schoolId = document.getElementById("update-schoolId").value;
        const district = document.getElementById("update-district").value;

        if (!schoolId || !district) return;

        const school = window.schoolData[district].find(
          (s) => String(s.schoolId || s.school_id) === String(schoolId)
        );

        if (!school) return;

        const latitude = parseFloat(school.latitude) || 14.5995;
        const longitude = parseFloat(school.longitude) || 120.9842;

        // Update form fields
        document.getElementById("update-latitude").value = latitude.toFixed(6);
        document.getElementById("update-longitude").value =
          longitude.toFixed(6);

        // Update marker and map
        const latLng = L.latLng(latitude, longitude);
        updateMarker.setLatLng(latLng);
        updateMap.panTo(latLng);
        updateMap.setZoom(15);

        // Show notification
        Swal.fire({
          toast: true,
          position: "top-end",
          icon: "info",
          title: "Location reset to original",
          showConfirmButton: false,
          timer: 1500,
        });
      }
    </script>
  </body>
</html>

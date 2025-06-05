<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DepEd: School Information</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="css/side-topbar.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <style>
        /* Your existing CSS styles remain the same */
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
            
            .modal-dialog {
                margin: 0.5rem;
            }
        }
        
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
        
        .modal-footer .btn {
            padding: 0.5rem 1rem;
            font-size: 0.95rem;
            min-width: 100px;
        }
        #updateDetailBtn {
            margin-right: 10%;
        }

        .loading {
            text-align: center;
            padding: 2rem;
        }

        .error {
            color: #dc3545;
            text-align: center;
            padding: 1rem;
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
    <div id="topbar-container">
        <div class="top-bar py-3 px-4 text-dark d-flex justify-content-between align-items-center">
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
            <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2 mb-sm-0">
                    <div class="position-relative">
                        <button class="btn btn-outline-secondary" id="filterButton">
                            <i class="bi bi-funnel"></i> Filter
                        </button>
                        <div class="filter-panel" id="filterPanel">
                            <h6 class="mb-3">Filter Options</h6>
                            <div class="filter-option">
                                <input type="checkbox" id="filterSID" />
                                <label for="filterSID">School ID (SID)</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterName" />
                                <label for="filterName">School Name</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterDistrict" />
                                <label for="filterDistrict">District</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterEnrollment" />
                                <label for="filterEnrollment">Enrollment</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterClassroom" />
                                <label for="filterClassroom">Classroom</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterSeats" />
                                <label for="filterSeats">Seats</label>
                            </div>
                            <div class="filter-option">
                                <input type="checkbox" id="filterTextbooks" />
                                <label for="filterTextbooks">Textbooks</label>
                            </div>
                        </div>
                    </div>
                    <div class="search-bar">
                        <input type="text" placeholder="Search schools..." class="form-control" id="searchInput" />
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
                            <td colspan="8" class="loading">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p>Loading school data...</p>
                            </td>
                        </tr>
                    </tbody>
                </table>
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
        let schoolsData = [];
        let currentSchoolID = '';
        let currentResourceType = 'classroom';

        document.addEventListener("DOMContentLoaded", () => {
            loadComponent("sidebar.php", "sidebar-container", initializeSidebar);
            loadComponent("topbar.php", "topbar-container", initializeTopbar);
            
            const filterButton = document.getElementById("filterButton");
            const filterPanel = document.getElementById("filterPanel");
            
            filterButton.addEventListener("click", function (event) {
                event.stopPropagation();
                filterPanel.classList.toggle("show");
            });
            
            document.addEventListener("click", function (event) {
                if (!filterPanel.contains(event.target) && event.target !== filterButton) {
                    filterPanel.classList.remove("show");
                }
            });
            
            initializeDetailViewButtons();
            initializeUpdateButton();
            initializeSaveDetailButton();
            initializeSearch();
            
            // Load school data
            loadSchoolData();
        });

        function loadComponent(componentName, containerId, callback) {
            const container = document.getElementById(containerId);
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

        function loadSchoolData() {
            fetch('phpp/crucialResources/crucial_get_school.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        schoolsData = data.data;
                        displaySchools(schoolsData);
                    } else {
                        showError('Failed to load school data: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Network error while loading school data');
                });
        }

        function displaySchools(schools) {
            const tableBody = document.getElementById('schoolTableBody');
            
            if (schools.length === 0) {
                tableBody.innerHTML = '<tr><td colspan="8" class="text-center">No schools found</td></tr>';
                return;
            }
            
            tableBody.innerHTML = schools.map(school => `
                <tr>
                    <td data-label="SID">${school.schoolID}</td>
                    <td data-label="School Name">${school.schoolName}</td>
                    <td data-label="District">${school.district || 'N/A'}</td>
                    <td data-label="Enrollment">${school.enrollment}</td>
                    <td data-label="Classroom">${school.classroom}</td>
                    <td data-label="Seats">${school.seats}</td>
                    <td data-label="Textbooks">${school.textbooks}</td>
                    <td data-label="Action">
                        <button class="btn btn-sm btn-primary view-btn" 
                                data-sid="${school.schoolID}" 
                                data-name="${school.schoolName}">
                            <i class="bi bi-eye"></i> View
                        </button>
                    </td>
                </tr>
            `).join('');
            
            // Re-initialize view buttons
            initializeViewButtons();
        }

        function initializeViewButtons() {
            const viewButtons = document.querySelectorAll(".view-btn");
            viewButtons.forEach((button) => {
                button.addEventListener("click", function () {
                    const sid = this.getAttribute("data-sid");
                    const schoolName = this.getAttribute("data-name");
                    
                    currentSchoolID = sid;
                    
                    document.getElementById("detailViewModalLabel").textContent = `${schoolName} Details`;
                    document.getElementById("detailSchoolId").value = sid;
                    document.getElementById("detailSchoolName").value = schoolName;
                    
                    // Reset to classroom view
                    currentResourceType = 'classroom';
                    document.querySelectorAll('.detail-view-buttons .btn').forEach(btn => btn.classList.remove('active'));
                    document.querySelector('[data-view="classroom"]').classList.add('active');
                    updateFormLabels('classroom');
                    
                    loadDetailData(sid, 'classroom');
                    
                    const detailViewModal = new bootstrap.Modal(document.getElementById("detailViewModal"));
                    detailViewModal.show();
                });
            });
        }

        function initializeDetailViewButtons() {
            const detailButtons = document.querySelectorAll(".detail-view-buttons .btn");
            detailButtons.forEach((button) => {
                button.addEventListener("click", function () {
                    detailButtons.forEach((btn) => btn.classList.remove("active"));
                    this.classList.add("active");
                    
                    const viewType = this.getAttribute("data-view");
                    currentResourceType = viewType;
                    updateFormLabels(viewType);
                    
                    loadDetailData(currentSchoolID, viewType);
                });
            });
        }

        function updateFormLabels(viewType) {
            const resourceType = viewType.charAt(0).toUpperCase() + viewType.slice(1);
            document.querySelectorAll(".detail-form-group label").forEach((label) => {
                if (label.textContent.includes("No. of Needs") || label.textContent.includes("Needs")) {
                    label.textContent = `No. of ${resourceType} Needs`;
                } else if (label.textContent.includes("No. of Excess") || label.textContent.includes("Excess")) {
                    label.textContent = `No. of ${resourceType} Excess`;
                } else if (label.textContent.includes("Total")) {
                    label.textContent = `Total ${resourceType}`;
                }
            });
        }

        function loadDetailData(schoolID, resourceType) {
            fetch(`phpp/crucialResources/crucial_get_school_details.php?schoolID=${schoolID}&resource_type=${resourceType}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const resourceData = data.data;
                        
                        // Populate form fields
                        document.getElementById("kinderNeeds").value = resourceData.kinder_needs || 0;
                        document.getElementById("kinderExcess").value = resourceData.kinder_excess || 0;
                        document.getElementById("kinderTotal").value = resourceData.kinder_total || 0;
                        
                        document.getElementById("grade16Needs").value = resourceData.grade16_needs || 0;
                        document.getElementById("grade16Excess").value = resourceData.grade16_excess || 0;
                        document.getElementById("grade16Total").value = resourceData.grade16_total || 0;
                        
                        document.getElementById("spedNeeds").value = resourceData.sped_needs || 0;
                        document.getElementById("spedExcess").value = resourceData.sped_excess || 0;
                        document.getElementById("spedTotal").value = resourceData.sped_total || 0;
                        
                        document.getElementById("jhsNeeds").value = resourceData.jhs_needs || 0;
                        document.getElementById("jhsExcess").value = resourceData.jhs_excess || 0;
                        document.getElementById("jhsTotal").value = resourceData.jhs_total || 0;
                        
                        document.getElementById("shsNeeds").value = resourceData.shs_needs || 0;
                        document.getElementById("shsExcess").value = resourceData.shs_excess || 0;
                        document.getElementById("shsTotal").value = resourceData.shs_total || 0;
                    } else {
                        console.error('Failed to load detail data:', data.error);
                        showError('Failed to load resource details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Network error while loading resource details');
                });
        }

        function initializeUpdateButton() {
            const updateButton = document.getElementById("updateDetailBtn");
            updateButton.addEventListener("click", function () {
                const isEditing = this.textContent.includes("Update");
                
                if (isEditing) {
                    this.innerHTML = '<i class="bi bi-x-circle"></i> Cancel';
                    this.classList.remove("btn-warning");
                    this.classList.add("btn-danger");
                    
                    document.querySelectorAll(".resource-field").forEach((field) => {
                        field.disabled = false;
                    });
                    
                    document.getElementById("saveDetailBtn").style.display = "block";
                } else {
                    this.innerHTML = '<i class="bi bi-pencil-square"></i> Update';
                    this.classList.remove("btn-danger");
                    this.classList.add("btn-warning");
                    
                    document.querySelectorAll(".resource-field").forEach((field) => {
                        field.disabled = true;
                    });
                    
                    document.getElementById("saveDetailBtn").style.display = "none";
                    
                    // Reload original data
                    loadDetailData(currentSchoolID, currentResourceType);
                }
            });
        }

        function initializeSaveDetailButton() {
            const saveButton = document.getElementById("saveDetailBtn");
            saveButton.addEventListener("click", function () {
                const formData = {
                    schoolID: currentSchoolID,
                    resource_type: currentResourceType,
                    kinder: {
                        needs: parseInt(document.getElementById("kinderNeeds").value) || 0,
                        excess: parseInt(document.getElementById("kinderExcess").value) || 0,
                        total: parseInt(document.getElementById("kinderTotal").value) || 0,
                    },
                    grade16: {
                        needs: parseInt(document.getElementById("grade16Needs").value) || 0,
                        excess: parseInt(document.getElementById("grade16Excess").value) || 0,
                        total: parseInt(document.getElementById("grade16Total").value) || 0,
                    },
                    sped: {
                        needs: parseInt(document.getElementById("spedNeeds").value) || 0,
                        excess: parseInt(document.getElementById("spedExcess").value) || 0,
                        total: parseInt(document.getElementById("spedTotal").value) || 0,
                    },
                    jhs: {
                        needs: parseInt(document.getElementById("jhsNeeds").value) || 0,
                        excess: parseInt(document.getElementById("jhsExcess").value) || 0,
                        total: parseInt(document.getElementById("jhsTotal").value) || 0,
                    },
                    shs: {
                        needs: parseInt(document.getElementById("shsNeeds").value) || 0,
                        excess: parseInt(document.getElementById("shsExcess").value) || 0,
                        total: parseInt(document.getElementById("shsTotal").value) || 0,
                    },
                };

                // Save to database
                fetch('phpp/crucialResources/crucial_update_school_resources.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(formData)
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Reset form state
                        const updateButton = document.getElementById("updateDetailBtn");
                        updateButton.innerHTML = '<i class="bi bi-pencil-square"></i> Update';
                        updateButton.classList.remove("btn-danger");
                        updateButton.classList.add("btn-warning");
                        
                        document.querySelectorAll(".resource-field").forEach((field) => {
                            field.disabled = true;
                        });
                        
                        document.getElementById("saveDetailBtn").style.display = "none";
                        
                        // Reload school data to update main table
                        loadSchoolData();
                        
                        Swal.fire({
                            title: "Success!",
                            text: `${currentResourceType} resources have been updated successfully.`,
                            icon: "success",
                            confirmButtonColor: "#4caf50",
                        });
                    } else {
                        showError('Failed to save changes: ' + data.error);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Network error while saving changes');
                });
            });
        }

        function initializeSearch() {
            const searchInput = document.getElementById("searchInput");
            searchInput.addEventListener("input", function () {
                const filter = this.value.toLowerCase();
                const filteredSchools = schoolsData.filter(school => 
                    school.schoolID.toLowerCase().includes(filter) ||
                    school.schoolName.toLowerCase().includes(filter) ||
                    (school.district && school.district.toLowerCase().includes(filter))
                );
                displaySchools(filteredSchools);
            });
        }

        function initializeSidebar() {
            setTimeout(() => {
                const navLinks = document.querySelectorAll(".sidebar nav a");
                if (navLinks.length === 0) return;
                
                const currentPage = window.location.pathname.split("/").pop();
                navLinks.forEach((link) => {
                    if (link.getAttribute("href") === currentPage) {
                        link.classList.add("active");
                    }
                });
                
                navLinks.forEach((link) => {
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
                if (sidebarToggle && sidebar) {
                    sidebarToggle.addEventListener("click", () => {
                        sidebar.classList.toggle("show");
                    });
                    document.addEventListener("click", (e) => {
                        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
                            sidebar.classList.remove("show");
                        }
                    });
                }
            }, 200);
        }

        function showError(message) {
            const tableBody = document.getElementById('schoolTableBody');
            tableBody.innerHTML = `<tr><td colspan="8" class="error">${message}</td></tr>`;
        }

        function exportToExcel() {
            // Create CSV content
            let csvContent = "School ID,School Name,District,Enrollment,Classroom,Seats,Textbooks\n";
            
            schoolsData.forEach(school => {
                csvContent += `"${school.schoolID}","${school.schoolName}","${school.district || 'N/A'}",${school.enrollment},${school.classroom},${school.seats},${school.textbooks}\n`;
            });
            
            // Create and download file
            const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
            const link = document.createElement("a");
            const url = URL.createObjectURL(blob);
            link.setAttribute("href", url);
            link.setAttribute("download", "school_data.csv");
            link.style.visibility = 'hidden';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }

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
                        window.location.href = "index.php";
                    }
                });
            }
        });
    </script>
</body>
</html>
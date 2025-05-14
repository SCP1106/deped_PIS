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
    <title>DepEd: Audit Trail</title>
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
        padding: 4rem 0;
        overflow: hidden;
        z-index: 1;
        background: url("data:image/svg+xml,%3Csvg width='100%' height='200' viewBox='60 50 1400 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,100 C200,250 400,-50 600,100 C800,250 1000,-50 1200,100' fill='none' stroke='%232e8b57' stroke-width='3' stroke-linecap='round'/%3E%3C/svg%3E")
          repeat-x;
        background-size: cover;
        background-position: center;
      }
      #sidebar-container .sidebar {
  height: 100vh;
  overflow-y: auto; /* Allows vertical scrolling */
}

      .audit-card {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        width: 100%;
        max-width: 1200px;
        transition: all 0.3s ease;
      }

      .audit-card:hover {
        transform: translateY(-5px);
      }

      .audit-card h2 {
        color: #2e8b57;
        margin-bottom: 1.5rem;
        font-size: 2rem;
      }

      .table-container {
        overflow-x: auto;
      }

      .table {
        min-width: 800px;
      }

      .table th {
        background-color: #2e8b57;
        color: white;
        font-weight: 500;
        border: none;
      }

      .table-striped tbody tr:nth-of-type(odd) {
        background-color: rgba(46, 139, 87, 0.05);
      }

      .table-hover tbody tr:hover {
        background-color: rgba(46, 139, 87, 0.1);
      }

      .pagination .page-item.active .page-link {
        background-color: #2e8b57;
        border-color: #2e8b57;
        color: white;
      }

      .pagination .page-link {
        color: #2e8b57;
      }

      .search-container {
        position: relative;
      }

      .search-container i {
        position: absolute;
        top: 12px;
        left: 15px;
        color: #6c757d;
      }

      .search-input {
        padding-left: 40px;
      }

      .btn-primary {
        background-color: #2e8b57;
        border-color: #2e8b57;
      }

      .btn-primary:hover {
        background-color: #246c46;
        border-color: #246c46;
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

      @media (max-width: 768px) {
        .audit-card {
          padding: 1.5rem;
        }
        
        .audit-card h2 {
          font-size: 1.5rem;
        }
        
        .filter-row {
          flex-direction: column;
        }
        
        .filter-row > div {
          margin-bottom: 1rem;
          width: 100%;
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

    <!-- Main Content -->
    <div class="main-content p-4">
      <div class="audit-card">
        <div class="d-flex justify-content-between align-items-center mb-4">
          <h2><i class="bi bi-clock-history me-2"></i>Audit Trail</h2>
          <div class="d-flex gap-2">
            <button class="btn btn-outline-secondary" id="exportBtn">
              <i class="bi bi-download me-2"></i>Export
            </button>
            <button class="btn btn-primary" id="refreshBtn">
              <i class="bi bi-arrow-clockwise me-2"></i>Refresh
            </button>
          </div>
        </div>
        
        <div class="row filter-row mb-4">
          <div class="col-md-4 mb-3 mb-md-0">
            <div class="search-container">
              <i class="bi bi-search"></i>
              <input 
                type="text" 
                class="form-control search-input" 
                id="searchInput" 
                placeholder="Search audit trail..."
              >
            </div>
          </div>
          <div class="col-md-3 mb-3 mb-md-0">
            <select class="form-select" id="userFilter">
              <option value="">All Users</option>
              <option value="admin@deped.gov.ph">admin@deped.gov.ph</option>
              <option value="teacher1@deped.gov.ph">teacher1@deped.gov.ph</option>
              <option value="principal@deped.gov.ph">principal@deped.gov.ph</option>
            </select>
          </div>
          <div class="col-md-3 mb-3 mb-md-0">
            <select class="form-select" id="pageFilter">
              <option value="">All Pages</option>
              <option value="Login">Login</option>
              <option value="Registration">Registration</option>
              <option value="User Management">User Management</option>
              <option value="Dashboard">Dashboard</option>
            </select>
          </div>
          <div class="col-md-2">
            <select class="form-select" id="dateFilter">
              <option value="">All Dates</option>
              <option value="today">Today</option>
              <option value="yesterday">Yesterday</option>
              <option value="week">This Week</option>
              <option value="month">This Month</option>
            </select>
          </div>
        </div>
        
        <div class="table-container">
          <table class="table table-striped table-hover">
            <thead>
              <tr>
                <th scope="col">Date & Time</th>
                <th scope="col">User</th>
                <th scope="col">Page</th>
                <th scope="col">Description</th>
              </tr>
            </thead>
            <tbody id="auditTableBody">
              <!-- Table content will be populated by JavaScript -->
            </tbody>
          </table>
        </div>
        
        <div class="d-flex justify-content-between align-items-center mt-4">
          <div>
            <span id="showingEntries">Showing 1-10 of 0 entries</span>
          </div>
          <nav aria-label="Audit trail pagination">
            <ul class="pagination" id="pagination">
              <!-- Pagination will be populated by JavaScript -->
            </ul>
          </nav>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        // Load Sidebar and Topbar via AJAX
        loadComponent("sidebar.php", "sidebar-container", initializeSidebar);
        loadComponent("topbar.php", "topbar-container", initializeTopbar);
        
        // Initialize the audit trail
        initializeAuditTrail();
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

      // Audit Trail functionality
      function initializeAuditTrail() {
        // Sample audit trail data
        const auditData = [
          {
            dateTime: "2025-03-07 11:30:45",
            user: "admin@deped.gov.ph",
            page: "User Management",
            description: "Created new user account for teacher1@deped.gov.ph"
          },
          {
            dateTime: "2025-03-07 10:15:22",
            user: "teacher1@deped.gov.ph",
            page: "Login",
            description: "Successful login"
          },
          {
            dateTime: "2025-03-07 09:45:10",
            user: "principal@deped.gov.ph",
            page: "Registration",
            description: "Approved registration for student ID #2025-0123"
          },
          {
            dateTime: "2025-03-06 16:30:05",
            user: "admin@deped.gov.ph",
            page: "Dashboard",
            description: "Generated monthly report"
          },
          {
            dateTime: "2025-03-06 15:22:18",
            user: "teacher1@deped.gov.ph",
            page: "User Management",
            description: "Updated profile information"
          },
          {
            dateTime: "2025-03-06 14:10:33",
            user: "principal@deped.gov.ph",
            page: "Dashboard",
            description: "Viewed enrollment statistics"
          },
          {
            dateTime: "2025-03-06 11:05:47",
            user: "admin@deped.gov.ph",
            page: "Login",
            description: "Successful login"
          },
          {
            dateTime: "2025-03-05 16:45:22",
            user: "teacher1@deped.gov.ph",
            page: "Registration",
            description: "Submitted new student registration"
          },
          {
            dateTime: "2025-03-05 14:30:11",
            user: "principal@deped.gov.ph",
            page: "User Management",
            description: "Reset password for teacher1@deped.gov.ph"
          },
          {
            dateTime: "2025-03-05 10:20:05",
            user: "admin@deped.gov.ph",
            page: "Dashboard",
            description: "Exported user activity report"
          },
          {
            dateTime: "2025-03-04 15:15:30",
            user: "teacher1@deped.gov.ph",
            page: "Login",
            description: "Failed login attempt"
          },
          {
            dateTime: "2025-03-04 14:50:22",
            user: "principal@deped.gov.ph",
            page: "Registration",
            description: "Rejected registration for student ID #2025-0124"
          },
          {
            dateTime: "2025-03-04 11:35:18",
            user: "admin@deped.gov.ph",
            page: "User Management",
            description: "Deactivated user account for inactive.user@deped.gov.ph"
          },
          {
            dateTime: "2025-03-04 09:25:40",
            user: "teacher1@deped.gov.ph",
            page: "Dashboard",
            description: "Viewed class attendance report"
          },
          {
            dateTime: "2025-03-03 16:10:05",
            user: "principal@deped.gov.ph",
            page: "Login",
            description: "Successful login"
          }
        ];
        
        // Variables for pagination
        let currentPage = 1;
        const rowsPerPage = 10;
        let filteredData = [...auditData];
        
        // Initialize the table
        renderTable();
        
        // Add event listeners for search and filters
        document.getElementById('searchInput').addEventListener('input', filterData);
        document.getElementById('userFilter').addEventListener('change', filterData);
        document.getElementById('pageFilter').addEventListener('change', filterData);
        document.getElementById('dateFilter').addEventListener('change', filterData);
        
        // Add event listener for refresh button
        document.getElementById('refreshBtn').addEventListener('click', function() {
          // In a real application, this would fetch fresh data from the server
          Swal.fire({
            title: 'Refreshed!',
            text: 'Audit trail data has been refreshed.',
            icon: 'success',
            confirmButtonColor: '#2e8b57'
          });
          
          // Reset filters and search
          document.getElementById('searchInput').value = '';
          document.getElementById('userFilter').value = '';
          document.getElementById('pageFilter').value = '';
          document.getElementById('dateFilter').value = '';
          
          // Reset to first page and original data
          currentPage = 1;
          filteredData = [...auditData];
          renderTable();
        });
        
        // Add event listener for export button
        document.getElementById('exportBtn').addEventListener('click', function() {
          // In a real application, this would generate a CSV or Excel file
          Swal.fire({
            title: 'Export Started',
            text: 'Your audit trail data is being exported.',
            icon: 'info',
            confirmButtonColor: '#2e8b57'
          });
        });
        
        // Function to filter data based on search and filters
        function filterData() {
          const searchTerm = document.getElementById('searchInput').value.toLowerCase();
          const userFilter = document.getElementById('userFilter').value;
          const pageFilter = document.getElementById('pageFilter').value;
          const dateFilter = document.getElementById('dateFilter').value;
          
          // Reset to first page when filters change
          currentPage = 1;
          
          // Apply filters
          filteredData = auditData.filter(item => {
            // Search term filter (checks all fields)
            const matchesSearch = 
              item.dateTime.toLowerCase().includes(searchTerm) ||
              item.user.toLowerCase().includes(searchTerm) ||
              item.page.toLowerCase().includes(searchTerm) ||
              item.description.toLowerCase().includes(searchTerm);
            
            // User filter
            const matchesUser = userFilter === '' || item.user === userFilter;
            
            // Page filter
            const matchesPage = pageFilter === '' || item.page === pageFilter;
            
            // Date filter
            let matchesDate = true;
            if (dateFilter !== '') {
              const itemDate = new Date(item.dateTime);
              const today = new Date();
              today.setHours(0, 0, 0, 0);
              
              const yesterday = new Date(today);
              yesterday.setDate(yesterday.getDate() - 1);
              
              const weekStart = new Date(today);
              weekStart.setDate(weekStart.getDate() - weekStart.getDay());
              
              const monthStart = new Date(today.getFullYear(), today.getMonth(), 1);
              
              switch(dateFilter) {
                case 'today':
                  matchesDate = itemDate >= today;
                  break;
                case 'yesterday':
                  matchesDate = itemDate >= yesterday && itemDate < today;
                  break;
                case 'week':
                  matchesDate = itemDate >= weekStart;
                  break;
                case 'month':
                  matchesDate = itemDate >= monthStart;
                  break;
              }
            }
            
            return matchesSearch && matchesUser && matchesPage && matchesDate;
          });
          
          renderTable();
        }
        
        // Function to render the table with current data and pagination
        function renderTable() {
          const tableBody = document.getElementById('auditTableBody');
          tableBody.innerHTML = '';
          
          // Calculate pagination
          const totalPages = Math.ceil(filteredData.length / rowsPerPage);
          const startIndex = (currentPage - 1) * rowsPerPage;
          const endIndex = Math.min(startIndex + rowsPerPage, filteredData.length);
          
          // Update showing entries text
          document.getElementById('showingEntries').textContent = 
            `Showing ${startIndex + 1}-${endIndex} of ${filteredData.length} entries`;
          
          // Render table rows
          if (filteredData.length === 0) {
            tableBody.innerHTML = `
              <tr>
                <td colspan="4" class="text-center py-4">
                  <i class="bi bi-info-circle me-2"></i>No audit trail records found
                </td>
              </tr>
            `;
          } else {
            for (let i = startIndex; i < endIndex; i++) {
              const item = filteredData[i];
              const row = document.createElement('tr');
              
              // Format date for better readability
              const dateObj = new Date(item.dateTime);
              const formattedDate = dateObj.toLocaleString('en-US', {
                year: 'numeric',
                month: 'short',
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit'
              });
              
              row.innerHTML = `
                <td>${formattedDate}</td>
                <td>${item.user}</td>
                <td>${item.page}</td>
                <td>${item.description}</td>
              `;
              
              tableBody.appendChild(row);
            }
          }
          
          // Render pagination
          renderPagination(totalPages);
        }
        
        // Function to render pagination controls
        function renderPagination(totalPages) {
          const pagination = document.getElementById('pagination');
          pagination.innerHTML = '';
          
          // Previous button
          const prevLi = document.createElement('li');
          prevLi.className = `page-item ${currentPage === 1 ? 'disabled' : ''}`;
          prevLi.innerHTML = `<a class="page-link" href="#" aria-label="Previous"><span aria-hidden="true">&laquo;</span></a>`;
          prevLi.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentPage > 1) {
              currentPage--;
              renderTable();
            }
          });
          pagination.appendChild(prevLi);
          
          // Page numbers
          const maxVisiblePages = 5;
          let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
          let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);
          
          // Adjust if we're near the end
          if (endPage - startPage + 1 < maxVisiblePages && startPage > 1) {
            startPage = Math.max(1, endPage - maxVisiblePages + 1);
          }
          
          for (let i = startPage; i <= endPage; i++) {
            const pageLi = document.createElement('li');
            pageLi.className = `page-item ${i === currentPage ? 'active' : ''}`;
            pageLi.innerHTML = `<a class="page-link" href="#">${i}</a>`;
            pageLi.addEventListener('click', function(e) {
              e.preventDefault();
              currentPage = i;
              renderTable();
            });
            pagination.appendChild(pageLi);
          }
          
          // Next button
          const nextLi = document.createElement('li');
          nextLi.className = `page-item ${currentPage === totalPages ? 'disabled' : ''}`;
          nextLi.innerHTML = `<a class="page-link" href="#" aria-label="Next"><span aria-hidden="true">&raquo;</span></a>`;
          nextLi.addEventListener('click', function(e) {
            e.preventDefault();
            if (currentPage < totalPages) {
              currentPage++;
              renderTable();
            }
          });
          pagination.appendChild(nextLi);
        }
      }
    </script>
  </body>
</html>
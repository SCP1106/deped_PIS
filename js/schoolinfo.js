document.addEventListener("DOMContentLoaded", () => {
  loadComponent("sidebar.php", "sidebar-container", initializeSidebar);
  loadComponent("topbar.php", "topbar-container", initializeTopbar);
  fetchData();
  initializeFilters();
});

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
      container.innerHTML = `<p>Error loading ${componentName}. Please refresh the page.</p>`;
    });
}

function initializeSidebar() {
  setTimeout(() => {
    const navLinks = document.querySelectorAll(".sidebar nav a");
    if (!navLinks.length) return;

    const currentPage = window.location.pathname.split("/").pop();
    navLinks.forEach((link) => {
      link.classList.toggle(
        "active",
        link.getAttribute("href") === currentPage
      );
      link.addEventListener("click", function () {
        document
          .querySelectorAll(".sidebar nav a")
          .forEach((el) => el.classList.remove("active"));
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

    sidebarToggle.addEventListener("click", () =>
      sidebar.classList.toggle("show")
    );

    document.addEventListener("click", (e) => {
      if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
        sidebar.classList.remove("show");
      }
    });
  }, 200);
}

document.addEventListener("click", (event) => {
  if (event.target.id === "logoutLink") {
    event.preventDefault();
    // SweetAlert (Swal) is assumed to be available globally, e.g., via a <script> tag
    if (typeof Swal !== "undefined") {
      Swal.fire({
        title: "Are you sure?",
        text: "You are about to logout!",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes, logout!",
      }).then((result) => {
        if (result.isConfirmed) window.location.href = "index.php";
      });
    } else {
      console.error(
        "SweetAlert (Swal) is not defined. Make sure it's included in your HTML."
      );
      alert("Logout confirmation failed. SweetAlert is required.");
    }
  }
});

function prepareModal(action) {
  const modalTitle = document.getElementById("schoolModalLabel");
  const saveButton = document.getElementById("saveSchoolBtn");
  const schoolIdInput = document.getElementById("schoolId");

  if (!modalTitle || !saveButton || !schoolIdInput) return;

  modalTitle.textContent =
    action === "add" ? "Add New School" : "Update School";
  saveButton.onclick = action === "add" ? addSchool : updateSchool;
  schoolIdInput.readOnly = action === "update";
  if (action === "add") {
    document.getElementById("schoolForm").reset();
    schoolIdInput.value = "";
  }
}

function addSchool() {
  console.log("Adding new school");
  closeModal();
}

function updateSchool() {
  console.log("Updating school");
  closeModal();
}

function closeModal() {
  // Bootstrap is assumed to be available globally, e.g., via a <script> tag
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

function exportToExcel() {
  console.log("Exporting data to Excel");
  alert("Exporting to Excel... (This is a placeholder)");
}

function fetchData() {
  const loadingScreen = document.getElementById("loadingScreen");
  if (loadingScreen) loadingScreen.style.display = "flex";

  fetch("phpp/dashboard/fetchSchoolInfo.php", { method: "POST" })
    .then((response) => response.json())
    .then((schools) => {
      if (!Array.isArray(schools))
        throw new Error("Invalid school data received.");
      populateTable(schools);
      // Initialize pagination after data is loaded
      initializePagination();
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
      alert("An error occurred while fetching school data.");
    })
    .finally(() => {
      if (loadingScreen) loadingScreen.style.display = "none";
    });
}

// Modified populateTable function to add data-label attributes for responsive view
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
    "Address",
    "Principal",
    "Contact No.",
    "Email",
    "Age",
  ];

  data.forEach((school, index) => {
    const row = document.createElement("tr");

    // Add striping classes
    row.classList.add(index % 2 === 0 ? "table-row-even" : "table-row-odd");

    // Create cells with data-label attributes
    const createCell = (content, label) => {
      const cell = document.createElement("td");
      cell.textContent = content || "N/A";
      cell.setAttribute("data-label", label);
      return cell;
    };

    row.appendChild(createCell(school.SchoolID, headers[0]));
    row.appendChild(createCell(school.Schoolname, headers[1]));
    row.appendChild(createCell(school.Institution, headers[2]));
    row.appendChild(createCell(school.population, headers[3]));
    row.appendChild(createCell(school.address, headers[4]));
    row.appendChild(createCell(school.principal, headers[5]));
    row.appendChild(createCell(school.contact_no, headers[6]));
    row.appendChild(createCell(school.email, headers[7]));
    row.appendChild(createCell(school.principal_age, headers[8]));

    tbody.appendChild(row);
  });
}

// Improved search function that updates pagination after filtering
document.addEventListener("DOMContentLoaded", () => {
  const searchInput = document.getElementById("searchInput");
  if (searchInput) {
    searchInput.addEventListener("input", () => {
      filterTable();
      resetPagination(); // Reset to first page after filtering
    });
  }
});

function filterTable() {
  const input =
    document.getElementById("searchInput")?.value.toLowerCase() || "";
  const checkboxes = document.querySelectorAll(
    ".filter-panel input[type='checkbox']:checked"
  );
  const tbody = document.getElementById("schoolTableBody");

  if (!tbody) return;

  const selectedColumns = Array.from(checkboxes).map((checkbox) =>
    Number.parseInt(checkbox.value)
  );
  const rows = tbody.getElementsByTagName("tr");

  for (const row of rows) {
    let showRow = false;
    const cells = row.getElementsByTagName("td");

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

    // Set a data attribute instead of directly changing display style
    // This allows pagination to work with filtered results
    row.setAttribute("data-visible", showRow ? "true" : "false");
  }

  // Update pagination after filtering
  updatePagination();
}

// Filter Panel
function initializeFilters() {
  const filterButton = document.getElementById("filterButton");
  const filterPanel = document.getElementById("filterPanel");

  if (!filterButton || !filterPanel) return;

  filterButton.addEventListener("click", (event) => {
    event.stopPropagation();
    filterPanel.classList.toggle("show");
  });

  document.addEventListener("click", (event) => {
    if (!filterPanel.contains(event.target) && event.target !== filterButton) {
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
}

// Redirecting to Profile Section
function redirectToProfile() {
  window.location.href = "profile.html";
}

// Improved pagination system
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
  const tableBody = document.getElementById("schoolTableBody");
  const prevBtn = document.getElementById("prevPage");
  const nextBtn = document.getElementById("nextPage");
  const pageInfo = document.getElementById("pageInfo");

  if (!tableBody || !prevBtn || !nextBtn || !pageInfo) return;

  const rows = tableBody.getElementsByTagName("tr");
  const visibleRows = Array.from(rows).filter(
    (row) => row.getAttribute("data-visible") !== "false"
  );

  const totalPages = Math.max(1, Math.ceil(visibleRows.length / rowsPerPage));

  // Adjust current page if it's beyond the total pages
  if (currentPage > totalPages) {
    currentPage = totalPages;
  }

  // Hide all rows first
  for (let i = 0; i < rows.length; i++) {
    rows[i].style.display = "none";
  }

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

function getTotalPages() {
  const tableBody = document.getElementById("schoolTableBody");
  if (!tableBody) return 1;
  {
    const tableBody = document.getElementById("schoolTableBody");
    if (!tableBody) return 1;

    const rows = tableBody.getElementsByTagName("tr");
    const visibleRows = Array.from(rows).filter(
      (row) => row.getAttribute("data-visible") !== "false"
    );

    return Math.max(1, Math.ceil(visibleRows.length / rowsPerPage));
  }
}

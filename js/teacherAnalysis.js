document.addEventListener("DOMContentLoaded", () => {
  loadComponent("sidebar.php", "sidebar-container", initializeSidebar);
  loadComponent("topbar.php", "topbar-container", initializeTopbar);
});

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
        if (!sidebar.contains(e.target) && !sidebarToggle.contains(e.target)) {
          sidebar.classList.remove("show");
        }
      });
    }
  }, 200);
}

function showRetirementDetails(schoolID, schoolName) {
  closeAllModals(); // Close all open modals
  document.getElementById("schoolNameTitle").textContent = schoolName;
  const modal = new bootstrap.Modal(
    document.getElementById("employeeDetailsModal")
  );
  modal.show();
  const tableBody = document.getElementById("employeeDetailsTableBody");
  tableBody.innerHTML = "<tr><td colspan='3'>Loading...</td></tr>";

  // Fetch employee data from the database
  fetch("..phpp/dashboard/RetirementView.php?schoolID=" + schoolID)
    .then((response) => response.json())
    .then((data) => {
      tableBody.innerHTML = ""; // Clear loading text
      if (data.length === 0) {
        tableBody.innerHTML =
          "<tr><td colspan='3'>No employees found.</td></tr>";
      } else {
        data.forEach((employee) => {
          const row = `
            <tr>
              <td>${employee.employee_no}</td>
              <td>${employee.name}</td>
              <td>${employee.age}</td>
              <td>${employee.position}</td>
            </tr>
          `;
          tableBody.innerHTML += row;
        });
      }
    })
    .catch((error) => {
      console.error("Error fetching employee data:", error);
      tableBody.innerHTML = "<tr><td colspan='3'>Error loading data.</td></tr>";
    });
}

let schoolData = [];
const itemsPerPage = 10;
let currentPage = 1;

function showEmployees(district) {
  closeAllModals();
  document.getElementById("employeeDistrictName").textContent = district;
  const modal = new bootstrap.Modal(document.getElementById("employeeModal"));
  modal.show();

  fetch("..phpp/dashboard/getEmployees.php", {
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
    .catch((error) => console.error("Error fetching employee data:", error));

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
        <td>${school.SchoolID}</td>
        <td>${school.schoolName}</td>
        <td>${school.total_employees}</td>
        <td><button class="btn btn-danger btn-sm" onclick="showSchoolEmployees('${school.schoolName}', ${school.SchoolID})">View</button></td>
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
        <td>${school.SchoolID}</td>
        <td>${school.schoolName}</td>
        <td>${school.total_employees}</td>
        <td><button class="btn btn-danger btn-sm" onclick="showSchoolEmployees('${school.schoolName}', ${school.SchoolID})">View</button></td>
      </tr>
    `
    )
    .join("");
}

document.addEventListener("DOMContentLoaded", function () {
  fetch("..phpp/dashboard/Total_Employees.php")
    .then((response) => response.json())
    .then((responseData) => {
      if (responseData.success) {
        const districtData = responseData.data;

        // Loop through available districts
        Object.keys(districtData).forEach((district) => {
          const element = document.getElementById(`totEmp-${district}`);
          if (element) {
            element.textContent = districtData[district];
          }
        });
      } else {
        console.error("Error fetching data:", responseData.message);
      }
    })
    .catch((error) => {
      console.error("Error fetching total employees:", error);
    });
});

document.addEventListener("DOMContentLoaded", function () {
  fetch("..phpp/dashboard/Total_Retirement.php")
    .then((response) => response.json())
    .then((responseData) => {
      if (responseData.success) {
        const districtData = responseData.data;

        // Loop through available districts
        Object.keys(districtData).forEach((district) => {
          const element = document.getElementById(`RetireEmp-${district}`);
          if (element) {
            element.textContent = districtData[district];
          }
        });
      } else {
        console.error("Error fetching data:", responseData.message);
      }
    })
    .catch((error) => {
      console.error("Error fetching total employees:", error);
    });
});

document.addEventListener("DOMContentLoaded", function () {
  fetch("..phpp/dashboard/AvgService.php")
    .then((response) => response.json())
    .then((responseData) => {
      if (responseData.success) {
        const districtData = responseData.data;

        // Loop through available districts
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
      console.error("Error fetching total employees:", error);
    });
});

function showSchoolEmployees(schoolName, schoolID) {
  closeAllModals(); // Close all open modals
  document.getElementById("schoolNameTitle").textContent = schoolName;
  const modal = new bootstrap.Modal(
    document.getElementById("employeeDetailsModal")
  );
  modal.show();
  const tableBody = document.getElementById("employeeDetailsTableBody");
  tableBody.innerHTML = "<tr><td colspan='3'>Loading...</td></tr>";

  // Fetch employee data from the database
  fetch("..phpp/dashboard/EmpView.php?schoolID=" + schoolID)
    .then((response) => response.json())
    .then((data) => {
      tableBody.innerHTML = ""; // Clear loading text
      if (data.length === 0) {
        tableBody.innerHTML =
          "<tr><td colspan='3'>No employees found.</td></tr>";
      } else {
        data.forEach((employee) => {
          const row = `
            <tr>
              <td>${employee.employee_no}</td>
              <td>${employee.name}</td>
              <td>${employee.age}</td>
              <td>${employee.position}</td>
            </tr>
          `;
          tableBody.innerHTML += row;
        });
      }
    })
    .catch((error) => {
      console.error("Error fetching employee data:", error);
      tableBody.innerHTML = "<tr><td colspan='3'>Error loading data.</td></tr>";
    });
}

function showNearRetirement(districtNum) {
  closeAllModals();
  document.getElementById("districtNum").textContent = districtNum;
  const modal = new bootstrap.Modal(
    document.getElementById("nearRetirementModal")
  );
  modal.show();

  const tableBody = document.getElementById("nearRetirementTableBody");
  tableBody.innerHTML = ""; // Clear previous data

  // Prepare JSON data
  const requestData = { districtNum: districtNum };

  // Fetch data from PHP using POST (JSON format)
  fetch("..phpp/dashboard/getRetirement.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/json",
    },
    body: JSON.stringify(requestData),
  })
    .then((response) => response.json())
    .then((data) => {
      data.forEach((employee) => {
        console.log(employee);
        const row = `
          <tr>
            <td>${employee.SchoolID}</td>
            <td>${employee.schoolName}</td>
            <td>${employee.near_retirement_count}</td>
            <td>
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
  document.getElementById("searchInput").addEventListener("input", function () {
    const filter = this.value.toLowerCase();
    document.querySelectorAll("#nearRetirementTableBody tr").forEach((row) => {
      row.style.display = row.textContent.toLowerCase().includes(filter)
        ? ""
        : "none";
    });
  });
}

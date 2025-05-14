document.addEventListener("DOMContentLoaded", () => {
  function loadComponent(componentName, containerId, callback) {
    const container = document.getElementById(containerId);
    const cachedHTML = localStorage.getItem(componentName);

    if (cachedHTML) {
      container.innerHTML = cachedHTML;
      if (callback) callback();
    }

    fetch(`${componentName}.php`)
      .then((response) => response.text())
      .then((data) => {
        if (data !== cachedHTML) {
          container.innerHTML = data;
          localStorage.setItem(componentName, data);
          if (callback) callback();
        }
      })
      .catch((error) => {
        console.error(`Error loading ${componentName}:`, error);
        if (!cachedHTML) {
          container.innerHTML = `<p>Error loading ${componentName}. Please refresh the page.</p>`;
        }
      });
  }

  // Load sidebar and topbar
  loadComponent("sidebar", "sidebar-container", initializeSidebar);
  loadComponent("topbar", "topbar-container", initializeTopbar);

  function initializeSidebar() {
    const currentPage = window.location.pathname.split("/").pop();
    const navLinks = document.querySelectorAll(".nav-link");

    navLinks.forEach((link) => {
      if (link.getAttribute("href") === currentPage) {
        link.classList.add("active");
      }
    });

    document.querySelectorAll(".sidebar nav a").forEach((link) => {
      link.addEventListener("click", function () {
        document
          .querySelectorAll(".sidebar nav a")
          .forEach((el) => el.classList.remove("active"));
        this.classList.add("active");
      });
    });

    document.getElementById("logos")?.addEventListener("click", (e) => {
      e.preventDefault();
      toggleVisibility([dashboardContent], true);
      setActiveLink(dashboardLink);
    });

    // Teachers vs Students Bar Chart
    const ctx1 = document
      .getElementById("teacherStudentBarChart")
      ?.getContext("2d");
    if (ctx1) {
      new Chart(ctx1, {
        type: "bar",
        data: {
          labels: ["Teachers", "Students"],
          datasets: [
            {
              label: "Count",
              data: [totalTeachers, totalStudents],
              backgroundColor: ["#81C784", "#4CAF50"],
              borderColor: ["#000", "#000"],
              borderRadius: 5,
            },
          ],
        },
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: true,
              title: { display: true, text: "Count" },
            },
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: (context) =>
                  `${context.label}: ${context.raw.toLocaleString()}`,
              },
            },
          },
        },
      });
    }

    // Performance Line Chart
    const ctx2 = document
      .getElementById("performanceLineChart")
      ?.getContext("2d");
    if (ctx2) {
      const schoolPerformance = {
        school1: {
          years: [2020, 2021, 2022, 2023, 2024, 2025],
          ratings: [80, 85, 88, 90, 85, 95],
        },
        school2: {
          years: [2020, 2021, 2022, 2023, 2024, 2025],
          ratings: [70, 75, 78, 82, 85, 90],
        },
        school3: {
          years: [2020, 2021, 2022, 2023, 2024, 2025],
          ratings: [85, 88, 90, 92, 94, 97],
        },
        school4: {
          years: [2020, 2021, 2022, 2023, 2024, 2025],
          ratings: [60, 65, 70, 75, 89, 80],
        },
        school5: {
          years: [2020, 2021, 2022, 2023, 2024, 2025],
          ratings: [75, 78, 80, 83, 87, 93],
        },
      };

      const performanceFilter = document.getElementById("performanceFilter");

      function getChartData(selectedSchool) {
        return {
          labels: schoolPerformance[selectedSchool].years,
          datasets: [
            {
              label: `Performance Rating (${selectedSchool.toUpperCase()})`,
              data: schoolPerformance[selectedSchool].ratings,
              borderColor: "green",
              backgroundColor: "rgba(6, 125, 12, 0.31)",
              borderWidth: 2,
              pointRadius: 5,
              pointBackgroundColor: "green",
              fill: true,
            },
          ],
        };
      }

      let selectedSchool = performanceFilter?.value || "school1";
      const lineChart = new Chart(ctx2, {
        type: "line",
        data: getChartData(selectedSchool),
        options: {
          responsive: true,
          scales: {
            y: {
              beginAtZero: false,
              title: { display: true, text: "Performance Rating (%)" },
            },
            x: {
              title: { display: true, text: "Year" },
            },
          },
          plugins: {
            tooltip: {
              callbacks: {
                label: (context) => `Rating: ${context.raw}%`,
              },
            },
          },
        },
      });

      performanceFilter?.addEventListener("change", function (e) {
        selectedSchool = e.target.value;
        const newChartData = getChartData(selectedSchool);
        lineChart.data.labels = newChartData.labels;
        lineChart.data.datasets = newChartData.datasets;
        lineChart.update();
      });
    }

    updateTable("all");

    // Logout link
    document
      .getElementById("logoutLink")
      ?.addEventListener("click", (event) => {
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
      });
  }

  function initializeTopbar() {
    const sidebarToggle = document.querySelector(".action-button");
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
  }
});

function redirectToProfile() {
  window.location.href = "profile.php";
}

// Enrollment Trends Chart
const enrollmentTrends = document
  .getElementById("studEnroll")
  ?.getContext("2d");

if (enrollmentTrends) {
  const enrollmentChart = new Chart(enrollmentTrends, {
    type: "bar",
    data: {
      labels: [],
      datasets: [
        {
          data: [],
          backgroundColor: "#4CAF50",
          borderRadius: 5,
          barThickness: 30,
        },
      ],
    },
    options: {
      responsive: true,
      plugins: { legend: { display: false } },
      scales: {
        y: {
          beginAtZero: true,
          grid: { color: "#f0f0f0" },
          ticks: { font: { size: 12 } },
        },
        x: { grid: { display: false }, ticks: { font: { size: 12 } } },
      },
      animation: { duration: 1500, easing: "easeInOutQuad" },
    },
  });

  fetch("phpp/dashboard/BarGraphData.php")
    .then((response) => response.json())
    .then((data) => {
      console.log(data);
      const years = data.map((item) => item.enrollment_year);
      const enrollees = data.map((item) => item.total_enrollees);

      enrollmentChart.data.labels = years;
      enrollmentChart.data.datasets[0].data = enrollees;
      enrollmentChart.update();
    })
    .catch((error) => console.error("Error fetching data:", error));
}

function updateTable(param) {
  // TODO: Implement table update logic here
  console.log("updateTable called with:", param);
}

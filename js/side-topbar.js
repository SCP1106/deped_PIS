function loadComponent(component, containerId, callback) {
  const container = document.getElementById(containerId);
  if (!container) return;

  // Optional: Show loading text while fetching
  container.innerHTML = "Loading...";

  fetch(`${component}.php`)
    .then((response) => {
      if (!response.ok) {
        throw new Error(`HTTP error! Status: ${response.status}`);
      }
      return response.text();
    })
    .then((html) => {
      container.innerHTML = html;
      if (callback) callback();
    })
    .catch((error) => {
      console.error(`Error loading ${component}:`, error);
      container.innerHTML = `<div class="error">Failed to load ${component}.</div>`;
    });
}

// Load sidebar and topbar
loadComponent("sidebar", "sidebar-container", initializeSidebar);
loadComponent("topbar", "topbar-container", initializeTopbar);

function initializeSidebar() {
  const currentPage = window.location.pathname.split("/").pop();
  const navLinks = document.querySelectorAll(".nav-link");

  // Set active link based on current page
  navLinks.forEach((link) => {
    if (link.getAttribute("href") === currentPage) {
      link.classList.add("active");

      if (link.classList.contains("submenu-link")) {
        const submenu = link.closest(".submenu");
        if (submenu) {
          submenu.classList.add("show");
          const parentLink = document.querySelector(
            `[href="${submenu.id.replace("Submenu", ".php")}"]`
          );
          if (parentLink) {
            parentLink.classList.add("active");
            const submenuToggle = parentLink.querySelector(".submenu-toggle");
            if (submenuToggle) submenuToggle.classList.add("rotate");
          }
        }
      }
    }
  });

  // Handle School Info submenu toggle
  const schoolInfoLink = document.getElementById("schoolInfoLink");
  const schoolInfoSubmenu = document.getElementById("schoolInfoSubmenu");

  if (schoolInfoLink && schoolInfoSubmenu) {
    schoolInfoLink.addEventListener("click", function (e) {
      e.preventDefault();
      schoolInfoSubmenu.classList.toggle("show");
      this.querySelector(".submenu-toggle")?.classList.toggle("rotate");

      if (schoolInfoSubmenu.classList.contains("show")) {
        this.classList.add("active");
      } else {
        const activeSubmenuItems =
          schoolInfoSubmenu.querySelectorAll(".nav-link.active");
        if (activeSubmenuItems.length === 0) {
          this.classList.remove("active");
        }
      }
    });
  }

  // Handle main nav link clicks
  document.querySelectorAll(".sidebar nav a").forEach((link) => {
    if (
      !link.classList.contains("submenu-link") &&
      link.id !== "schoolInfoLink"
    ) {
      link.addEventListener("click", function () {
        document.querySelectorAll(".sidebar nav a").forEach((el) => {
          el.classList.remove("active");
        });

        this.classList.add("active");

        if (schoolInfoSubmenu) {
          schoolInfoSubmenu.classList.remove("show");
          schoolInfoLink
            .querySelector(".submenu-toggle")
            ?.classList.remove("rotate");
        }
      });
    }
  });

  // Handle submenu link clicks
  document.querySelectorAll(".submenu-link").forEach((link) => {
    link.addEventListener("click", function () {
      document.querySelectorAll(".sidebar nav a").forEach((el) => {
        el.classList.remove("active");
      });

      this.classList.add("active");
      const parentLink = document.querySelector(
        `[href="${this.closest(".submenu").id.replace("Submenu", ".php")}"]`
      );
      if (parentLink) {
        parentLink.classList.add("active");
      }
    });
  });

  // Logout functionality
  const logoutLink = document.getElementById("logoutLink");
  if (logoutLink) {
    logoutLink.addEventListener("click", (event) => {
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
          window.location.href = "loading.php";
        }
      });
    });
  }
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

// Redirect helper functions
function redirectToProfile() {
  window.location.href = "profile.php";
}

function redirectDashboard() {
  window.location.href = "dashboard.php";
}

// Safely handle .has-submenu clicks, even after dynamic loading
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("has-submenu")) {
    e.target.classList.toggle("active");
  }
});

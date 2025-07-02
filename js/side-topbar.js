function loadComponent(component, containerId, callback) {
  const container = document.getElementById(containerId);
  if (!container) return;

  container.innerHTML = "Loading...";

  fetch(`${component}.php`)
    .then((response) => {
      if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
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

loadComponent("sidebar", "sidebar-container", initializeSidebar);
loadComponent("topbar", "topbar-container", initializeTopbar);

function initializeSidebar() {
  const currentPage = window.location.pathname.split("/").pop();
  const navLinks = document.querySelectorAll(".nav-link");

  // Highlight the active link
  navLinks.forEach((link) => {
    if (link.getAttribute("href") === currentPage) {
      link.classList.add("active");

      // Handle submenu
      if (link.classList.contains("submenu-link")) {
        const submenu = link.closest(".submenu");
        if (submenu) {
          submenu.classList.add("show");
          const parentLink = document.querySelector(
            `[href="${submenu.id.replace("Submenu", ".php")}"]`
          );
          if (parentLink) {
            parentLink.classList.add("active");
            const toggleIcon = parentLink.querySelector(".submenu-toggle");
            if (toggleIcon) toggleIcon.classList.add("rotate");
          }
        }
      }
    }
  });

  // Handle submenu toggle
  const schoolInfoLink = document.getElementById("schoolInfoLink");
  const schoolInfoSubmenu = document.getElementById("schoolInfoSubmenu");

  if (schoolInfoLink && schoolInfoSubmenu) {
    schoolInfoLink.addEventListener("click", function (e) {
      e.preventDefault();
      schoolInfoSubmenu.classList.toggle("show");
      this.querySelector(".submenu-toggle")?.classList.toggle("rotate");

      // Toggle active state
      if (schoolInfoSubmenu.classList.contains("show")) {
        this.classList.add("active");
      } else {
        const activeSubmenuItems = schoolInfoSubmenu.querySelectorAll(".nav-link.active");
        if (activeSubmenuItems.length === 0) {
          this.classList.remove("active");
        }
      }
    });
  }

  // Handle regular nav link clicks
  document.querySelectorAll(".sidebar nav a").forEach((link) => {
    if (!link.classList.contains("submenu-link") && link.id !== "schoolInfoLink") {
      link.addEventListener("click", function () {
        document.querySelectorAll(".sidebar nav a").forEach((el) => el.classList.remove("active"));
        this.classList.add("active");

        // Collapse submenu if open
        if (schoolInfoSubmenu) {
          schoolInfoSubmenu.classList.remove("show");
          schoolInfoLink.querySelector(".submenu-toggle")?.classList.remove("rotate");
        }

        // Mobile: close sidebar after clicking a link
        document.querySelector(".sidebar")?.classList.remove("show");
        document.body.classList.remove("sidebar-open");
      });
    }
  });

  // Handle submenu link clicks
  document.querySelectorAll(".submenu-link").forEach((link) => {
    link.addEventListener("click", function () {
      document.querySelectorAll(".sidebar nav a").forEach((el) => el.classList.remove("active"));
      this.classList.add("active");

      const parentLink = document.querySelector(
        `[href="${this.closest(".submenu").id.replace("Submenu", ".php")}"]`
      );
      if (parentLink) parentLink.classList.add("active");

      // Mobile: close sidebar
      document.querySelector(".sidebar")?.classList.remove("show");
      document.body.classList.remove("sidebar-open");
    });
  });

  // Logout
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
      document.body.classList.toggle("sidebar-open");
    });

    // Click outside to close (mobile only)
    document.addEventListener("click", (e) => {
      const isClickInsideSidebar = sidebar.contains(e.target);
      const isClickToggle = sidebarToggle.contains(e.target);
      const isMobile = window.innerWidth <= 768;

      if (!isClickInsideSidebar && !isClickToggle && isMobile) {
        sidebar.classList.remove("show");
        document.body.classList.remove("sidebar-open");
      }
    });
  }
}

// Submenu toggle handler (optional if using .has-submenu separately)
document.addEventListener("click", function (e) {
  if (e.target.classList.contains("has-submenu")) {
    e.target.classList.toggle("active");
  }
});

// Optional: Prevent body scroll when sidebar is open on mobile
if (window.innerWidth <= 768) {
  const observer = new MutationObserver(() => {
    const sidebarOpen = document.querySelector(".sidebar")?.classList.contains("show");
    document.body.style.overflowY = sidebarOpen ? "hidden" : "auto";
  });

  observer.observe(document.querySelector(".sidebar"), { attributes: true });
}

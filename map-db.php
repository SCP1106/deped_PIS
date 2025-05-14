<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DepEd: Dashboard with Map</title>
    <link
      href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css"
      rel="stylesheet"
    />
    <link
      rel="stylesheet"
      href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css"
    />
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="css/dashboard.css" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
      /* Ensure the entire page takes up the full viewport */
      html,
      body {
        height: 100%;
        margin: 0;
        padding: 0;
        overflow: hidden; /* Prevent scrolling */
        display: flex;
        flex-direction: column;
      }
      #sidebar-container .sidebar {
        height: 100vh;
        overflow-y: auto; /* Allows vertical scrolling */
      }

      /* Adjust the main content to expand fully */
      .main-content {
        display: flex;
        flex-grow: 1;
        overflow: hidden; /* Prevent inner scrolling */
        padding: 0; /* Remove extra padding */
        margin: 0;
      }

      /* Make the map take up the full height of .main-content */
      #map {
        flex-grow: 1;
        height: 100%;
        width: 100%;
        border-radius: 0; /* Optional: remove border-radius for full coverage */
      }
      /* Modern Popup Styling */
      .popup {
        display: none;
        position: fixed;
        top: 50%;
        right: 20px;
        transform: translateY(-50%) translateX(100%);
        background: linear-gradient(
          135deg,
          #2e7d32,
          #4caf50
        ); /* Green gradient */
        color: #fff;
        padding: 24px;
        box-shadow: 0 10px 30px rgba(0, 128, 0, 0.4);
        border-radius: 16px;
        z-index: 1500; /* Increased z-index to ensure it's above map elements */
        min-width: 350px;
        max-width: 400px;
        width: 30%;
        height: auto;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: transform 0.4s ease-out, opacity 0.3s ease-in-out;
        opacity: 0;
        overflow-y: auto; /* Allow scrolling for content */
        max-height: 80vh; /* Limit height */
      }

      /* Show popup animation */
      .popup.show {
        opacity: 1;
        transform: translateY(-50%) translateX(0);
      }

      /* Close button */
      .close-btn {
        position: absolute;
        top: 12px;
        right: 12px;
        cursor: pointer;
        font-size: 18px;
        color: #fff;
        background: rgba(0, 255, 0, 0.2);
        border: none;
        padding: 6px 12px;
        border-radius: 50%;
        transition: background 0.3s ease-in-out, transform 0.2s, box-shadow 0.3s;
        z-index: 1; /* Ensure it's above popup content */
      }

      .close-btn:hover {
        background: limegreen;
        transform: scale(1.1);
        box-shadow: 0 0 8px rgba(0, 255, 0, 0.8);
      }

      /* Popup content */
      .popup-content {
        font-family: "Poppins", sans-serif;
        position: relative; /* For proper positioning of close button */
      }

      .popup-content h3 {
        font-size: 20px;
        margin-bottom: 12px;
        font-weight: 600;
        border-bottom: 2px solid rgba(255, 255, 255, 0.3);
        padding-bottom: 8px;
        padding-right: 30px; /* Make room for close button */
      }

      .popup-content p {
        font-size: 14px;
        line-height: 1.6;
        color: #e0ffe0;
      }

      /* School info styling */
      .school-info {
        margin-top: 15px;
      }

      .school-info-item {
        display: flex;
        margin-bottom: 8px;
        flex-wrap: wrap; /* Allow wrapping on small screens */
      }

      .school-info-label {
        font-weight: 600;
        min-width: 120px;
        color: #e0ffe0;
      }

      .school-info-value {
        color: white;
        flex: 1;
        word-break: break-word; /* Prevent overflow of long text */
      }

      .loading-spinner {
        text-align: center;
        padding: 20px 0;
      }

      /* Hover tooltip styles */
      .leaflet-tooltip {
        background-color: rgba(46, 125, 50, 0.95);
        color: white;
        border: none;
        box-shadow: 0 4px 14px rgba(0, 0, 0, 0.3);
        padding: 10px 14px;
        border-radius: 6px;
        font-weight: bold;
        font-size: 14px;
        transition: all 0.2s ease;
        transform: translateY(-5px);
        pointer-events: none;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
      }

      .leaflet-tooltip-top:before {
        border-top-color: rgba(46, 125, 50, 0.95);
      }

      /* Responsive Design */
      @media (max-width: 768px) {
        .popup {
          left: 50%;
          top: 50%;
          transform: translate(-50%, -50%) scale(0.9);
          width: 85%;
          border-radius: 12px;
          right: auto; /* Reset right position */
        }

        .popup.show {
          transform: translate(-50%, -50%) scale(1);
        }

        .school-info-item {
          flex-direction: column;
        }

        .school-info-label {
          margin-bottom: 4px;
        }
      }

      /* Remove outline from Leaflet paths */
      .leaflet-interactive {
        outline: none !important;
      }

      /* Remove outline from Leaflet paths */
      .leaflet-interactive {
        outline: none !important;
      }

      /* Google Maps button styling */
      .popup-content .btn-light {
        background: rgba(255, 255, 255, 0.9);
        border: none;
        color: #2e7d32;
        font-weight: 600;
        transition: all 0.2s ease;
        margin-top: 10px;
      }

      .popup-content .btn-light:hover {
        background: white;
        box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
        transform: translateY(-2px);
      }

      .popup-content .bi-geo-alt-fill {
        color: #e53935;
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
      <div
        class="top-bar py-3 px-4 text-dark d-flex justify-content-between align-items-center"
      >
        <button class="btn d-md-none action-button" id="sidebarToggle">
          <i class="bi bi-list"></i>
        </button>
      </div>
    </div>

    <!-- Main Content -->
    <div class="main-content p-1" id="dashboardContent">
      <div id="map"></div>
      <div id="popupPanel" class="popup">
        <div class="popup-content">
          <button type="button" class="close-btn" aria-label="Close">
            &times;
          </button>
          <h3 id="popup-title">School Information</h3>
          <div id="popup-loading" class="loading-spinner">
            <div class="spinner-border text-light" role="status">
              <span class="visually-hidden">Loading...</span>
            </div>
          </div>
          <div id="popup-content" class="school-info">
            <!-- School information will be loaded here -->
          </div>
        </div>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
      document.addEventListener("DOMContentLoaded", () => {
        // Load Sidebar and Topbar via AJAX
        loadComponent("sidebar.php", "sidebar-container", initializeSidebar);
        loadComponent("topbar.php", "topbar-container", initializeTopbar);
      });

      // Function to load components (sidebar, topbar) dynamically
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

      // Sidebar initialization
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
              document
                .querySelectorAll(".sidebar nav a")
                .forEach((el) => el.classList.remove("active"));
              this.classList.add("active");
            });
          });
        }, 200);
      }

      // Topbar initialization
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

      const map = L.map("map", {
        zoomControl: false,
      }).setView([15.648753870285125, 121.01049859359448], 10);

      var defaulty = L.tileLayer(
        "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
        {
          maxZoom: 19,
          attribution:
            '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
        }
      ).addTo(map);

      var googleSat = L.tileLayer(
        "http://{s}.google.com/vt/lyrs=s&x={x}&y={y}&z={z}",
        {
          maxZoom: 16,
          subdomains: ["mt0", "mt1", "mt2", "mt3"],
        }
      );

      var baseMaps = {
        Default: defaulty,
        Satellite: googleSat,
      };

      var baseTileLayer = L.tileLayer(
        "https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png"
      ).addTo(map);

      var overlayMaps = {};
      L.control
        .layers(baseMaps, overlayMaps, { position: "bottomright" })
        .addTo(map);

      var layerControl = document.querySelector(".leaflet-control-layers");

      layerControl.addEventListener("touchstart", function () {
        layerControl.classList.toggle("active");
      });

      const geoJSONLayerGroup = L.layerGroup().addTo(map);
      const brgyLayerGroup = L.layerGroup().addTo(map);

      let placeClicked = "NUEVAECIJA";
      let selectedPolygonData = null;
      let previousZoomLevel = map.getZoom();
      let previousCenter = map.getCenter();
      // Store the current marker's coordinates when clicked
      let currentMarkerCoords = null;

      function getNextColor(cdNum) {
        cdNum = String(cdNum);
        const colors = {
          1: "red",
          2: "blue",
          3: "yellow",
          4: "green",
          10: "white",
        };
        const color = colors[cdNum] || "gray";
        const borderColor = "black";
        return { color, borderColor };
      }

      // Function to fetch school information from PHP
      function fetchSchoolInfo(SchoolID) {
        console.log("Fetching school info for ID: " + SchoolID);
        return fetch("phpp/map/fetchPanelData.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({ SchoolID }),
        }).then((response) => {
          if (!response.ok) {
            throw new Error("HTTP error! Status: " + response.status);
          }
          return response.json();
        });
      }

      // Function to display school information in the popup
      function displaySchoolInfo(schoolData, markerCoords) {
        const popupContent = document.getElementById("popup-content");
        const popupLoading = document.getElementById("popup-loading");

        // Hide loading spinner
        popupLoading.style.display = "none";

        // If no data or error
        if (!schoolData || schoolData.error) {
          popupContent.innerHTML = `
        <div class="alert alert-warning">
          No information available for this school.
        </div>
      `;
          return;
        }

        // Create HTML for essential school information (less info)
        let html = `
      <div class="school-info-item">
        <div class="school-info-label">School ID:</div>
        <div class="school-info-value">${
          schoolData.school_info?.schoolID || "N/A"
        }</div>
      </div>
      <div class="school-info-item">
        <div class="school-info-label">School Name:</div>
        <div class="school-info-value">${
          schoolData.school_info?.schoolName || "N/A"
        }</div>
      </div>
    `;

        // Create HTML for additional information (hidden initially)
        let additionalInfo = `
      <div class="school-info-item">
        <div class="school-info-label">School Type:</div>
        <div class="school-info-value">${
          schoolData.school_info?.Institution || "N/A"
        }</div>
      </div>
      <div class="school-info-item">
        <div class="school-info-label">District:</div>
        <div class="school-info-value">${
          schoolData.school_info?.district || "N/A"
        }</div>
      </div>
    `;

        // Contact information
        if (schoolData.ContactNumber || schoolData.Email) {
          additionalInfo += `
        <div class="school-info-item">
          <div class="school-info-label">Contact:</div>
          <div class="school-info-value">${
            schoolData.ContactNumber || "N/A"
          }</div>
        </div>
        <div class="school-info-item">
          <div class="school-info-label">Email:</div>
          <div class="school-info-value">${schoolData.Email || "N/A"}</div>
        </div>
      `;
        }

        // Principal information
        if (schoolData.school_info.principal) {
          additionalInfo += `
        <div class="school-info-item">
          <div class="school-info-label">Principal:</div>
          <div class="school-info-value">${
            schoolData.school_info.principal || "N/A"
          }</div>
        </div>
      `;
        }

        // Student count if available
        if (
          schoolData.enrollment_data &&
          schoolData.enrollment_data.total_enrollees
        ) {
          additionalInfo += `
        <div class="school-info-item">
          <div class="school-info-label">Enrollees:</div>
          <div class="school-info-value">${
            schoolData.enrollment_data.total_enrollees || "N/A"
          }</div>
        </div>
        <div class="school-info-item">
          <div class="school-info-label">Male:</div>
          <div class="school-info-value">${
            schoolData.enrollment_data.total_males || "N/A"
          }</div>
        </div>
        <div class="school-info-item">
          <div class="school-info-label">Female:</div>
          <div class="school-info-value">${
            schoolData.enrollment_data.total_females || "N/A"
          }</div>
        </div>
      `;
        }

        // Student count if available
        if (schoolData.employee_count) {
          additionalInfo += `
        <div class="school-info-item">
          <div class="school-info-label"># Of Teachers:</div>
          <div class="school-info-value">${
            schoolData.employee_count || "N/A"
          }</div>
        </div>
      `;
        }

        // Add the Google Maps button (always visible)
        let googleMapsButton = "";
        if (markerCoords && markerCoords.lat && markerCoords.lng) {
          googleMapsButton = `
        <div class="school-info-item mt-3">
          <a href="https://www.google.com/maps?q=${markerCoords.lat},${markerCoords.lng}" target="_blank" class="btn btn-light w-100 d-flex align-items-center justify-content-center gap-2">
            <i class="bi bi-geo-alt-fill"></i>
            Open in Google Maps
          </a>
        </div>
      `;
        }

        // Add the "See More" button and hidden additional info
        html += `
      <div id="additional-info" style="display: none;">
        ${additionalInfo}
      </div>
      <div class="d-flex justify-content-between mt-3 mb-3">
        <button id="toggle-info" class="btn btn-sm btn-outline-light">See More</button>
      </div>
      ${googleMapsButton}
    `;

        // Add any additional fields from the schoolData object
        popupContent.innerHTML = html;

        // Add event listener for the toggle button
        document
          .getElementById("toggle-info")
          .addEventListener("click", function () {
            const additionalInfo = document.getElementById("additional-info");
            const toggleBtn = document.getElementById("toggle-info");

            if (additionalInfo.style.display === "none") {
              additionalInfo.style.display = "block";
              toggleBtn.textContent = "See Less";
            } else {
              additionalInfo.style.display = "none";
              toggleBtn.textContent = "See More";
            }
          });
      }

      // Generic function to handle polygon clicks for both municipalities and barangays
      function handlePolygonClick(layer, feature) {
        selectedPolygonData = feature.properties;
        const popupPanel = document.getElementById("popupPanel");
        const popupLoading = document.getElementById("popup-loading");
        const popupContent = document.getElementById("popup-content");

        // Show popup with loading state
        document.getElementById("popup-title").innerText = "Loading...";
        popupLoading.style.display = "block";
        popupContent.innerHTML = "";

        // Make sure popup is visible before adding show class
        popupPanel.style.display = "block";

        // Force a reflow before adding the show class
        void popupPanel.offsetWidth;

        // Add show class to trigger animation
        setTimeout(() => popupPanel.classList.add("show"), 10);

        // If it's a school marker (has SchoolID property)
        if (feature.properties && feature.properties.schoolID) {
          const schoolId = feature.properties.schoolID;
          document.getElementById("popup-title").innerText =
            "School Information";

          // Store the marker coordinates for Google Maps link
          currentMarkerCoords = null;
          if (feature.properties.latitude && feature.properties.longitude) {
            currentMarkerCoords = {
              lat: feature.properties.latitude,
              lng: feature.properties.longitude,
            };
          }

          // Fetch school information from PHP
          fetchSchoolInfo(schoolId)
            .then((schoolData) => {
              console.log("School data received:", schoolData);
              displaySchoolInfo(schoolData, currentMarkerCoords);
            })
            .catch((error) => {
              console.error("Error fetching school information:", error);
              popupLoading.style.display = "none";
              popupContent.innerHTML = `
                <div class="alert alert-danger">
                  Error loading school information. Please try again.
                </div>
              `;
            });
        }
        // If it's a municipality
        else if (feature.properties && feature.properties.adm3_en) {
          const featureName = feature.properties.adm3_en || "Unknown";
          document.getElementById("popup-title").innerText = featureName;
          popupLoading.style.display = "none";
          popupContent.innerHTML = `
            <div class="school-info-item">
              <div class="school-info-label">Municipality:</div>
              <div class="school-info-value">${featureName}</div>
            </div>
            <div class="school-info-item">
              <div class="school-info-label">Province:</div>
              <div class="school-info-value">Nueva Ecija</div>
            </div>
          `;

          placeClicked = featureName.toUpperCase().replace(/\s+/g, "");
          brgyLayerGroup.clearLayers();
          map.fitBounds(layer.getBounds());
          previousZoomLevel = map.getZoom();
          previousCenter = map.getCenter();
          loadDataBrgy(placeClicked);
        }
        // If it's a barangay
        else if (
          feature.properties &&
          (feature.properties.brgy_name || feature.properties.adm4_en)
        ) {
          const featureName =
            feature.properties.brgy_name ||
            feature.properties.adm4_en ||
            "Unknown";
          document.getElementById("popup-title").innerText = featureName;
          popupLoading.style.display = "none";
          popupContent.innerHTML = `
            <div class="school-info-item">
              <div class="school-info-label">Barangay:</div>
              <div class="school-info-value">${featureName}</div>
            </div>
          `;

          // Add Google Maps link for barangay
          if (feature.geometry && feature.geometry.coordinates) {
            // Get center point of the polygon for barangays
            const bounds = layer.getBounds();
            const center = bounds.getCenter();
            popupContent.innerHTML += `
              <div class="school-info-item mt-3">
                <a href="https://www.google.com/maps?q=${center.lat},${center.lng}" target="_blank" class="btn btn-light w-100 d-flex align-items-center justify-content-center gap-2" rel="noreferrer">
                  <i class="bi bi-geo-alt-fill"></i>
                  Open in Google Maps
                </a>
              </div>
            `;
          }
        }
        // Default case
        else {
          document.getElementById("popup-title").innerText =
            "Location Information";
          popupLoading.style.display = "none";
          popupContent.innerHTML = `
            <div class="school-info-item">
              <div class="school-info-label">Details:</div>
              <div class="school-info-value">No specific information available for this location.</div>
            </div>
          `;
        }
      }

      function loadDataBrgy(data) {
        fetch("phpp/map/fetchMapData.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({ data }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("HTTP error! Status: " + response.status);
            }
            return response.json();
          })
          .then((geojsonData) => {
            L.geoJSON(geojsonData, {
              style: (feature) => {
                const { borderColor } = getNextColor(10);
                return {
                  fillColor: "white",
                  color: borderColor,
                  weight: 3,
                  fillOpacity: 0.2,
                };
              },
              onEachFeature: (feature, layer) => {
                // Add tooltip for hover functionality
                if (feature.properties) {
                  // Use brgy_name if available, otherwise use adm3_en
                  const tooltipText =
                    feature.properties.brgy_name ||
                    feature.properties.adm4_en ||
                    "Unknown";
                  layer.bindTooltip(tooltipText, {
                    permanent: false,
                    direction: "top",
                    className: "leaflet-tooltip",
                  });
                }

                // Store original style to restore it on mouseout
                const originalStyle = {
                  fillOpacity: 0.2,
                };

                // Add hover effects
                layer.on({
                  mouseover: (e) => {
                    const layer = e.target;
                    layer.setStyle({
                      fillOpacity: 0.5,
                    });

                    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                      layer.bringToFront();
                    }
                  },
                  mouseout: (e) => {
                    const layer = e.target;
                    layer.setStyle(originalStyle);
                  },
                  click: (e) => {
                    const layer = e.target;
                    // Prevent focus style
                    if (layer._path) {
                      layer._path.setAttribute("tabindex", "-1");
                    }
                    // Use the generic click handler for barangay polygons
                    handlePolygonClick(layer, feature);
                  },
                });
              },
            }).addTo(brgyLayerGroup);
          })
          .catch((error) => {
            console.error("Error fetching GeoJSON data:", error);
          });

        loadMarkerData(placeClicked);
      }

      function loadMarkerData(data) {
        fetch("phpp/map/fetchMarkerData.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({ data }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("HTTP error! Status: " + response.status);
            }
            return response.json();
          })
          .then((jsonData) => {
            jsonData.forEach((item) => {
              const latitude = Number.parseFloat(item.latitude);
              const longitude = Number.parseFloat(item.longitude);

              // Skip invalid coordinates
              if (isNaN(latitude) || isNaN(longitude)) {
                console.warn("Invalid coordinates for school:", item);
                return;
              }

              const customIcon = L.icon({
                iconUrl: "image/marker.png",
                iconSize: [32, 32],
                iconAnchor: [16, 32],
                popupAnchor: [0, -32],
              });

              const marker = L.marker([latitude, longitude], {
                icon: customIcon,
              });

              // Create a tooltip with the school name if available
              if (item.SchoolName) {
                marker.bindTooltip(item.SchoolName, {
                  permanent: false,
                  direction: "top",
                  className: "leaflet-tooltip",
                });
              }

              marker.on("click", () => {
                // Create a feature-like object for the marker
                const markerFeature = {
                  properties: {
                    schoolID: item.SchoolID, // Make sure property name matches what's expected
                    SchoolName: item.SchoolName,
                    latitude: latitude,
                    longitude: longitude,
                    // Add any other properties from item
                  },
                };
                handlePolygonClick(marker, markerFeature);
              });

              marker.addTo(brgyLayerGroup);
            });
          })
          .catch((error) => {
            console.error("Error fetching marker data:", error);
          });
      }

      function loadGeoJSONData(data) {
        fetch("phpp/map/fetchMapData.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/x-www-form-urlencoded",
          },
          body: new URLSearchParams({ data }),
        })
          .then((response) => {
            if (!response.ok) {
              throw new Error("HTTP error! Status: " + response.status);
            }
            return response.json();
          })
          .then((geojsonData) => {
            L.geoJSON(geojsonData, {
              style: (feature) => {
                const { color, borderColor } = getNextColor(
                  feature.properties.CD_NUM
                );
                return {
                  fillColor: color,
                  color: borderColor,
                  weight: 1,
                  fillOpacity: 0.175,
                };
              },
              onEachFeature: (feature, layer) => {
                // Add tooltip for hover functionality
                if (feature.properties && feature.properties.adm3_en) {
                  layer.bindTooltip(feature.properties.adm3_en, {
                    permanent: false,
                    direction: "top",
                    className: "leaflet-tooltip",
                  });
                }

                // Store original style to restore it on mouseout
                const originalStyle = {
                  fillOpacity: 0.175,
                };

                // Add hover and click effects
                layer.on({
                  mouseover: (e) => {
                    const layer = e.target;
                    layer.setStyle({
                      fillOpacity: 0.5,
                    });

                    if (!L.Browser.ie && !L.Browser.opera && !L.Browser.edge) {
                      layer.bringToFront();
                    }
                  },
                  mouseout: (e) => {
                    const layer = e.target;
                    layer.setStyle(originalStyle);
                  },
                  click: (e) => {
                    const layer = e.target;
                    // Prevent focus style
                    if (layer._path) {
                      layer._path.setAttribute("tabindex", "-1");
                    }
                    // Use the generic click handler for municipality polygons
                    handlePolygonClick(layer, feature);
                  },
                });
              },
            }).addTo(geoJSONLayerGroup);
          })
          .catch((error) => {
            console.error("Error fetching GeoJSON data:", error);
          });
      }

      loadGeoJSONData(placeClicked);

      let lastRightClickTime = 0;

      document.addEventListener("contextmenu", (event) => {
        const currentTime = Date.now();
        const timeDifference = currentTime - lastRightClickTime;

        if (timeDifference >= 1000) {
          event.preventDefault();

          geoJSONLayerGroup.clearLayers();
          brgyLayerGroup.clearLayers();

          loadGeoJSONData("NUEVAECIJA");

          const defaultCenter = [15.666687955868635, 121.0108536597733];
          const defaultZoomLevel = 10;

          map.setView(defaultCenter, defaultZoomLevel);

          lastRightClickTime = currentTime;

          const popupPanel = document.getElementById("popupPanel");
          popupPanel.classList.remove("show");
          setTimeout(() => (popupPanel.style.display = "none"), 300);
        }
      });

      // Close the Popup on Click
      document
        .querySelector(".close-btn")
        .addEventListener("click", function () {
          const popupPanel = document.getElementById("popupPanel");
          popupPanel.classList.remove("show");
          setTimeout(() => (popupPanel.style.display = "none"), 300);
        });
    </script>
  </body>
</html>

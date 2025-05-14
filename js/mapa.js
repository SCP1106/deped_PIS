const map = L.map("map", {
  zoomControl: false,
}).setView([15.648753870285125, 121.01049859359448], 10);

var defaulty = L.tileLayer("https://tile.openstreetmap.org/{z}/{x}/{y}.png", {
  maxZoom: 19,
  attribution:
    '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
}).addTo(map);

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
L.control.layers(baseMaps, overlayMaps, { position: "bottomright" }).addTo(map);
L.control.zoom({ position: "bottomleft" }).addTo(map);

var layerControl = document.querySelector(".leaflet-control-layers");

layerControl.addEventListener("touchstart", function () {
  layerControl.classList.toggle("active");
});

map.on("click", function (e) {
  if (!layerControl.contains(e.target)) {
    layerControl.classList.remove("active");
  }
});

const geoJSONLayerGroup = L.layerGroup().addTo(map);
const brgyLayerGroup = L.layerGroup().addTo(map);

let placeClicked = "NUEVAECIJA";
let selectedPolygonData = null;
let previousZoomLevel = map.getZoom();
let previousCenter = map.getCenter();

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

// function onPolygonClick(layer, item) {
//   console.log(item.SchoolID);

//   brgyLayerGroup.clearLayers();

//   map.fitBounds(layer.getBounds());
//   previousZoomLevel = map.getZoom();
//   previousCenter = map.getCenter();
// }

function onPolygonMapClick(layer, feature) {
  selectedPolygonData = feature.properties;
  placeClicked = (feature.properties.adm3_en || "Unknown")
    .toUpperCase()
    .replace(/\s+/g, "");

  brgyLayerGroup.clearLayers();

  map.fitBounds(layer.getBounds());
  previousZoomLevel = map.getZoom();
  previousCenter = map.getCenter();

  loadDataBrgy(placeClicked);
}

function loadDataBrgy(data) {
  fetch("..phpp/map/fetchMapData.php", {
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
          layer.bindPopup(
            "Feature ID: " + (feature.properties.adm3_en || "Unknown")
          );
          //layer.on("click", () => onPolygonClick(layer, feature));
        },
      }).addTo(brgyLayerGroup);
    })
    .catch((error) => {
      console.error("Error fetching GeoJSON data:", error);
    });

  loadMarkerData(placeClicked);
}

function loadMarkerData(data) {
  console.log(data);
  showSchoolPanel(data);

  fetch("..phpp/map/fetchMarkerData.php", {
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
        createMarker(item);
      });
    })
    .catch((error) => {
      console.error("Error fetching data:", error);
    });
}

function createMarker(item) {
  const latitude = item.latitude;
  const longitude = item.longitude;

  const customIcon = L.icon({
    iconUrl: "image/marker.png",
    iconSize: [32, 32],
    iconAnchor: [16, 32],
    popupAnchor: [0, -32],
  });

  const marker = L.marker([latitude, longitude], { icon: customIcon });

  marker
    .bindPopup(
      "School ID: " + item.SchoolID + "<br>School Name: " + item.SchoolName
    )
    .on("click", () => {
      sID = item.SchoolID;
      console.log(sID);
      updatePanelFromServer(sID); // Fetch panel data from another PHP file
      //onPolygonClick(marker, item);
    });

  marker.addTo(brgyLayerGroup);
}

function showSchoolPanel(data) {
  fetch("..phpp/map/fetchPolygonPanel.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: new URLSearchParams({ data }),
  })
    .then((response) => response.json()) // Parse JSON response
    .then((data) => {
      polygonPanel(data);
    })
    .catch((error) => console.error("Error fetching data:", error));
}

function polygonPanel(data) {
  const container = document.getElementById("card-container");
  container.innerHTML = ""; // Clear existing content

  if (!Array.isArray(data) || data.length === 0) {
    container.innerHTML =
      "<p class='text-gray-500 text-center mt-4'>No data available</p>";
    return;
  }

  data.forEach((item) => {
    const card = document.createElement("div");
    card.className =
      "bg-white shadow p-4 border rounded cursor-pointer hover:shadow-md transition";

    card.innerHTML = `
      <h3 class="text-lg font-semibold">${item.schoolName || "N/A"}</h3>
      <p class="text-sm">School ID: <span class="font-medium">${
        item.schoolID || "N/A"
      }</span></p>
      <p class="text-sm">Barangay: <span class="font-medium">${
        item.barangay_name || "N/A"
      }</span></p>
    `;

    card.addEventListener("click", () => {
      Swal.fire({
        title: item.schoolName || "N/A",
        text: `School ID: ${item.schoolID || "N/A"}\nBarangay: ${
          item.barangay_name || "N/A"
        }`,
        icon: "info",
        confirmButtonText: "OK",
      });
    });

    container.appendChild(card);
  });

  document.getElementById("card-container").style.display = "block";
  document.getElementById("welcome-banner").style.display = "none";
  document.getElementById("school-info").style.display = "none";
}

// Function to update the panel with fetched data
function updatePanelFromServer(schoolID) {
  fetch("..phpp/map/fetchPanelData.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "SchoolID=" + encodeURIComponent(schoolID),
  })
    .then((response) => response.json()) // Parse JSON response
    .then((data) => {
      updatePanel(data);
    })
    .catch((error) => console.error("Error fetching data:", error));
}

function updatePanel(data) {
  document.getElementById("school-id").textContent =
    data.school_info.schoolID || "N/A";
  document.getElementById("school-name").textContent =
    data.school_info.schoolName || "N/A";
  document.getElementById("total-enrollees").textContent =
    data.enrollment_data.total_enrollees || "N/A";
  document.getElementById("male-enrollees").textContent =
    data.enrollment_data.total_males || "N/A";
  document.getElementById("female-enrollees").textContent =
    data.enrollment_data.total_females || "N/A";
  document.getElementById("teachers").textContent = data.Teachers || "N/A";
  document.getElementById("barangay").textContent =
    data.school_info.barangay_name || "N/A";
  document.getElementById("school-info").style.display = "block";
  document.getElementById("welcome-banner").style.display = "none";
  document.getElementById("card-container").style.display = "none";
}

function loadGeoJSONData(data) {
  fetch("..phpp/map/fetchMapData.php", {
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
          layer.bindPopup(
            "Feature ID: " + (feature.properties.adm3_en || "Unknown")
          );
          layer.on("click", () => onPolygonMapClick(layer, feature));
        },
      }).addTo(geoJSONLayerGroup);
    })
    .catch((error) => {
      // console.error("Error fetching GeoJSON data:", error);
    });
}

loadGeoJSONData(placeClicked);

let lastRightClickTime = 0;

document.addEventListener("contextmenu", (event) => {
  const currentTime = Date.now();
  const timeDifference = currentTime - lastRightClickTime;
  document.getElementById("welcome-banner").style.display = "BLOCK";
  document.getElementById("school-info").style.display = "none";
  document.getElementById("card-container").style.display = "none";

  if (timeDifference >= 1000) {
    event.preventDefault();

    geoJSONLayerGroup.clearLayers();
    brgyLayerGroup.clearLayers();

    loadGeoJSONData("NUEVAECIJA");

    const defaultCenter = [15.666687955868635, 121.0108536597733];
    const defaultZoomLevel = 10;

    map.setView(defaultCenter, defaultZoomLevel);

    lastRightClickTime = currentTime;
  }
});

const panel = document.querySelector(".panel");
const button = document.querySelector(".login-btn");
let isDragging = false;
let startY = 0;
let startPanelY = 0;

function onDragStart(event) {
  if (window.innerWidth <= 768) {
    isDragging = true;
    startY = event.touches ? event.touches[0].clientY : event.clientY;
    startPanelY = panel.getBoundingClientRect().top;
    panel.classList.add("draggable");
  }
}

function onDragMove(event) {
  if (isDragging && window.innerWidth <= 768) {
    const deltaY =
      startY - (event.touches ? event.touches[0].clientY : event.clientY);

    const newHeight = startPanelY + deltaY;

    const maxHeight = window.innerHeight * 0.85;
    panel.style.height = `${Math.min(Math.max(newHeight, 150), maxHeight)}px`;

    const currentHeight = parseFloat(panel.style.height);

    if (currentHeight >= window.innerHeight * 0.65) {
      button.style.display = "block";
    } else if (currentHeight <= window.innerHeight * 0.3) {
      button.style.display = "none";
    }

    map.invalidateSize();
  }
}

function onDragEnd() {
  isDragging = false;
  panel.classList.remove("draggable");
}

panel.addEventListener("touchstart", onDragStart);
document.addEventListener("touchmove", onDragMove);
document.addEventListener("touchend", onDragEnd);

panel.addEventListener("mousedown", onDragStart);
document.addEventListener("mousemove", onDragMove);
document.addEventListener("mouseup", onDragEnd);

function resetPanelHeightForDesktop() {
  if (window.innerWidth > 768) {
    panel.style.height = "100vh";
    button.style.display = "block";
  }
  if (window.innerWidth <= 768) {
    panel.style.height = "20vh";
    button.style.display = "none";
  }
}

window.addEventListener("resize", resetPanelHeightForDesktop);
resetPanelHeightForDesktop();

document.getElementById("btn-login").addEventListener("click", () => {
  Swal.fire({
    title: "Login as Admin",
    html: `
  <input type="text" id="username" class="swal2-input custom-input" placeholder="Username">
  <input type="password" id="password" class="swal2-input custom-input" placeholder="Password">
  <div style="font-size: small;">
    <a href="#" id="forgot-password">Forgot password?</a>
  </div>
`,
    width: 520,
    padding: "2em",
    color: "#0d8017",
    background: "#f2f2f2",
    confirmButtonText: "Login",
    focusConfirm: false,
    customClass: {
      popup: "custom-swal",
      confirmButton: "custom-button",
    },
    preConfirm: () => {
      const username = document.getElementById("username").value.trim();
      const password = document.getElementById("password").value.trim();

      if (!username || !password) {
        Swal.showValidationMessage("Username and password cannot be empty.");
        return false;
      }

      return fetch("..phpp/login/login_otp.php", {
        method: "POST",
        body: new URLSearchParams({ username, password }),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.success) {
            return Swal.fire({
              title: "OTP Sent",
              text: "An OTP has been sent to your email.",
              icon: "success",
              confirmButtonText: "OK",
            }).then(() => showOtpInput());
          } else {
            throw new Error("Invalid credentials");
          }
        })
        .catch(() => {
          Swal.fire({
            title: "Error",
            text: "Invalid login credentials. Please try again.",
            icon: "error",
            confirmButtonText: "Retry",
          });
        });
    },
  });
});

function showOtpInput() {
  return Swal.fire({
    title: "Enter OTP",
    html: `
  <div id="otp-container" style="display: flex; justify-content: center; gap: 10px;">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
    <input type="text" class="otp-input" maxlength="1" inputmode="numeric">
  </div>
`,
    confirmButtonText: "Verify",
    focusConfirm: false,
    didOpen: setupOtpInputs,
    preConfirm: () => verifyOtp().then((success) => success || false),
  });
}

function setupOtpInputs() {
  const inputs = document.querySelectorAll(".otp-input");

  inputs.forEach((input, index) => {
    Object.assign(input.style, {
      width: "40px",
      height: "50px",
      fontSize: "20px",
      textAlign: "center",
      border: "2px solid #0d8017",
      borderRadius: "5px",
    });

    input.addEventListener("input", (e) => {
      input.value = input.value.replace(/\D/g, ""); // Allow only numbers
      if (input.value.length === 1 && index < inputs.length - 1) {
        inputs[index + 1].focus();
      }
    });

    input.addEventListener("keydown", (e) => {
      if (e.key === "Backspace" && !input.value && index > 0) {
        inputs[index - 1].focus();
      }
    });
  });

  inputs[0].focus(); // Auto-focus first OTP input
}

function verifyOtp() {
  const otpInputs = document.querySelectorAll(".otp-input");
  const otp = Array.from(otpInputs)
    .map((input) => input.value)
    .join("");

  if (otp.length < 6) {
    Swal.showValidationMessage("Please enter a 6-digit OTP.");
    return false;
  }

  return fetch("..phpp/login/verify_otp.php", {
    method: "POST",
    body: JSON.stringify({ otp }),
    headers: { "Content-Type": "application/json" },
  })
    .then((response) => response.json())
    .then((data) => {
      if (data.success) {
        return Swal.fire({
          title: "Success",
          text: data.message,
          icon: "success",
          confirmButtonText: "OK",
        }).then(() => {
          window.location.href = "dashboard.html";
        });
      } else {
        Swal.showValidationMessage(data.message || "OTP verification failed.");
        return false;
      }
    })
    .catch(() => {
      Swal.showValidationMessage("There was an error verifying the OTP.");
      return false;
    });
}

document.getElementById("searchInput").addEventListener("input", function () {
  const searchValue = this.value.toLowerCase();
});

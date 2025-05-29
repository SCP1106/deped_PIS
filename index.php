<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Planning Information System</title>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" />
  <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" />
  <style>
    body, .container-fluid {
      margin: 0;
      padding: 0;
      font-family: "Verdana", sans-serif;
    }
    
    #map {
      height: 95vh;
      width: 100%;
      position: absolute;
      top: 40px;
      left: 0;
      z-index: 1;
    }

    /* Navigation Bar */
    .navbar {
      background-color: #ccedde;
      padding: 0.1rem 0.5rem;
      z-index: 1100;
      min-height: 40px;
    }

    .navbar-brand {
      color: #000;
      font-weight: bold;
      font-size: 1rem;
      padding: 0;
    }

    .navbar-brand img {
      height: 25px;
      margin-right: 5px;
    }

    .navbar-nav .nav-link {
      color: rgba(0, 0, 0, 0.9);
      transition: color 0.3s;
      font-size: 0.8rem;
      padding: 0.25rem 0.5rem;
    }

    .navbar-nav .nav-link:hover {
      color: #4caf50;
    }

    .navbar-toggler {
      border-color: rgba(0, 0, 0, 0.3);
      padding: 0.15rem 0.3rem;
      font-size: 0.8rem;
    }

    .navbar-toggler-icon {
      width: 1em;
      height: 1em;
      background-image: url("https://www.svgrepo.com/show/506800/burger-menu.svg");
    }

    /* Mobile-specific navbar */
    @media (max-width: 768px) {
      .navbar {
        min-height: 60px;
        padding: 0.3rem 0.7rem;
      }
      
      .navbar-brand img {
        height: 30px;
      }
      
      .navbar-toggler {
        padding: 0.25rem 0.5rem;
        font-size: 1rem;
      }
      
      .navbar-toggler-icon {
        width: 1.2em;
        height: 1.2em;
      }
      
      #map {
        top: 60px;
      }
    }

    /* Panel - hidden by default */
    .panel {
      position: fixed;
      z-index: 1000;
      background: linear-gradient(
        140deg,
        rgb(204, 237, 222) 0%,
        rgb(204, 237, 222) 20%,
        rgb(255, 255, 255) 20%,
        rgb(255, 255, 255) 80%,
        rgb(204, 237, 222) 80%,
        rgb(204, 237, 222) 100%
      );
      border: 1px solid #ddd;
      padding: 10px;
      scrollbar-width: thin;
      scrollbar-color: #0d80177f #ffffff00;
      overflow-y: auto;
      transition: all 0.3s ease-in-out;
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
      display: none;
    }

    /* Mobile panel */
    @media (max-width: 768px) {
      .panel {
        bottom: 0;
        left: 0;
        width: 100%;
        height: 150px;
        border-top-left-radius: 10%;
        border-top-right-radius: 10%;
        border-top: 1px solid #ddd;
      }

      .top-line {
        display: block;
        width: 25%;
        height: 5px;
        border-radius: 20px;
        background-color: #ccc;
        cursor: pointer;
        position: absolute;
        top: 10px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 15;
      }
    }

    /* Desktop panel */
    @media (min-width: 769px) {
      .panel {
        top: 40px;
        left: 0;
        width: 25%;
        height: calc(100vh - 40px);
        border-right: 1px solid #ddd;
      }

      .top-line {
        display: none;
      }
    }

    /* Panel toggle button */
    .panel-toggle {
      position: fixed;
      top: 50px;
      left: 10px;
      z-index: 999;
      background-color: white;
      border: 1px solid #ddd;
      border-radius: 4px;
      padding: 5px 10px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
      cursor: pointer;
    }

    .card {
      border: 1px solid #ddd;
      border-radius: 8px;
      padding: 15px;
      margin: 10px;
      background: #f9f9f9;
      box-shadow: 2px 2px 10px rgba(0, 0, 0, 0.1);
      cursor: pointer;
      transition: transform 0.2s, box-shadow 0.2s;
      border-right: 4px solid #ddd;
    }

    /* School level color indicators */
    .card.elementary {
      border-right: 4px solid #1e88e5;
    }

    .card.secondary {
      border-right: 4px solid #7b0404;
    }

    /* School level badge */
    .school-level-badge {
      display: inline-block;
      padding: 2px 6px;
      border-radius: 4px;
      font-size: 11px;
      font-weight: bold;
      color: white;
      margin-left: 5px;
    }

    .school-level-badge.elementary {
      background-color: #1e88e5;
    }

    .school-level-badge.secondary {
      background-color: #7b0404;
    }

    .card:hover {
      transform: translateY(-3px);
      box-shadow: 2px 5px 15px rgba(0, 0, 0, 0.2);
    }

    .draggable {
      cursor: grab;
    }

    .search-bar-container {
      position: fixed;
      top: 50px;
      left: 50%;
      transform: translateX(-50%);
      z-index: 1000;
      width: 450px;
      display: flex;
      align-items: center;
    }

    /* Mobile search bar */
    @media (max-width: 768px) {
      .search-bar-container {
        top: 70px;
      }
    }

    .search-input {
      height: 40px;
      width: 100%;
      padding: 8px;
      border-bottom: 5px solid #0d8017;
      border-radius: 5px;
    }

    /* Filter button */
    .filter-map-btn {
      height: 40px;
      margin-right: 5px;
      background-color: #0d8017;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 0 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .filter-map-btn:hover {
      background-color: #0b6d14;
    }

    .filter-map-btn i {
      font-size: 16px;
    }

    /* Filter dropdown */
    .filter-dropdown {
      position: absolute;
      top: 45px;
      left: 0;
      background-color: white;
      border-radius: 5px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      z-index: 1001;
      display: none;
      width: 200px;
    }

    .filter-dropdown.show {
      display: block;
    }

    .filter-dropdown-item {
      padding: 10px 15px;
      cursor: pointer;
      transition: background-color 0.2s;
      display: flex;
      align-items: center;
    }

    .filter-dropdown-item.active {
      background-color: #e8f5e9;
      font-weight: bold;
    }

    .filter-dropdown-item:hover {
      background-color: #f5f5f5;
    }

    .filter-dropdown-item .color-dot {
      width: 10px;
      height: 10px;
      border-radius: 50%;
      margin-right: 8px;
    }

    .filter-dropdown-item .color-dot.blue {
      background-color: #1e88e5;
    }

    .filter-dropdown-item .color-dot.red {
      background-color: #7b0404;
    }

    .leaflet-control-layers:hover .leaflet-control-layers-list {
      visibility: visible;
    }

    .leaflet-control-layers .leaflet-control-layers-list {
      visibility: hidden;
      transition: visibility 0.3s ease-in-out;
    }

    @media (max-width: 768px) {
      .search-bar-container {
        width: calc(100% - 20px);
      }

      .login-btn {
        background-color: #0d8017;
        color: #fff;
        width: 90%;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 16px;
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 1000;
        text-align: center;
        transition: background-color 0.3s ease, transform 0.2s ease;
      }

      .login-btn:hover {
        background-color: #0b6d14;
      }
    }

    @media (min-width: 769px) {
      .login-btn {
        background-color: #0d8017;
        color: #fff;
        width: 300px;
        border: none;
        border-radius: 8px;
        padding: 10px 20px;
        font-size: 16px;
        position: fixed;
        bottom: 20px;
        left: 12%;
        transform: translateX(-50%);
        z-index: 1000;
        text-align: center;
        transition: background-color 0.3s ease, transform 0.2s ease;
      }

      .login-btn:hover {
        background-color: #0b6d14;
      }
    }

    /* Tooltip */
    .leaflet-tooltip {
      background-color: rgba(13, 128, 23, 0.8);
      color: white;
      border: none;
      padding: 6px 12px;
      border-radius: 4px;
      font-size: 14px;
      font-weight: bold;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }
    
    .leaflet-tooltip-top:before {
      border-top-color: rgba(13, 128, 23, 0.8);
    }

    /* Marker pulse animation */
    @keyframes marker-pulse {
      0% {
        transform: scale(1);
        opacity: 1;
      }
      50% {
        transform: scale(1.2);
        opacity: 0.8;
      }
      100% {
        transform: scale(1);
        opacity: 1;
      }
    }

    .marker-pulse {
      animation: marker-pulse 0.8s ease-in-out;
    }

    /* Barangay info panel */
    #barangay-info {
      display: none;
      background: none;
      padding: 15px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      max-width: 400px;
      margin: 20px auto;
      font-family: Arial, sans-serif;
    }

    #back-to-overview {
      background-color: #0d8017;
      color: white;
      border: none;
      border-radius: 5px;
      padding: 5px 10px;
      margin-top: 10px;
      cursor: pointer;
      font-size: 14px;
    }

    #back-to-overview:hover {
      background-color: #0b6d14;
    }

    /* Search highlight */
    .search-highlight {
      background-color: rgba(13, 128, 23, 0.2);
      padding: 0 2px;
      border-radius: 2px;
    }

    /* Search container */
    .search-container {
      position: relative;
      display: flex;
      align-items: center;
      flex: 1;
    }

    .search-clear {
      position: absolute;
      right: 10px;
      cursor: pointer;
      color: #666;
      display: none;
    }

    .search-clear.visible {
      display: block;
    }

    /* Search loading indicator */
    .search-spinner {
      position: absolute;
      right: 30px;
      display: none;
      width: 16px;
      height: 16px;
      border: 2px solid rgba(13, 128, 23, 0.3);
      border-radius: 50%;
      border-top-color: #0d8017;
      animation: spin 1s ease-in-out infinite;
    }

    @keyframes spin {
      to {
        transform: rotate(360deg);
      }
    }

    .search-spinner.visible {
      display: block;
    }

    /* Panel loading indicator */
    .panel-loading {
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100px;
    }

    .panel-spinner {
      width: 40px;
      height: 40px;
      border: 4px solid rgba(13, 128, 23, 0.3);
      border-radius: 50%;
      border-top-color: #0d8017;
      animation: spin 1s ease-in-out infinite;
    }

    /* Google Maps button */
    .google-maps-btn {
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 8px;
      background-color: #ededed;
      color: rgb(9, 9, 9);
      border: none;
      border-radius: 5px;
      padding: 8px 12px;
      margin-top: 15px;
      width: 100%;
      font-size: 14px;
      transition: all 0.2s ease;
      cursor: pointer;
    }

    .google-maps-btn:hover {
      background-color: #ffffff;
      color: black;
      transform: translateY(-2px);
      box-shadow: 0 3px 8px rgba(0, 0, 0, 0.2);
    }

    .google-maps-btn i {
      color: #ff4a4a;
      font-size: 16px;
    }

    .legend-item {
      display: flex;
      align-items: center;
      margin-bottom: 5px;
    }

    .legend-color {
      width: 16px;
      height: 16px;
      margin-right: 8px;
      border-radius: 50%;
    }

    /* Municipality header */
    .municipality-header {
      background-color: rgba(255, 255, 255, 0.9);
      border-radius: 5px;
      padding: 10px;
      margin-bottom: 15px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      text-align: center;
    }

    .municipality-name {
      font-size: 20px;
      font-weight: bold;
      color: #0d8017;
      margin-bottom: 5px;
    }

    .stats-item {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
      font-size: 13px;
    }

    .stats-label {
      font-size: larger;
      font-weight: normal;
      color: #555;
    }

    .stats-value {
      font-size: larger;
      font-weight: bold;
      color: #0d8017;
    }
    
    #elementary-schools {
      font-size: larger;
      font-weight: bold;
      color: #1e88e5;
    }
    
    #secondary-schools {
      font-size: larger;
      font-weight: bold;
      color: #7b0404;
    }
    
    #school-info {
      background: linear-gradient(135deg, #2e7d32, #4caf50);
      color: #fff;
      padding: 24px;
      box-shadow: 0 10px 30px rgba(0, 128, 0, 0.4);
      border-radius: 10px;
    }

    /* Filter buttons */
    .filter-buttons {
      display: flex;
      justify-content: space-between;
      margin-bottom: 15px;
      gap: 5px;
    }

    .filter-btn {
      flex: 1;
      padding: 6px 10px;
      border-left: 3px solid #0d8017;
      border-radius: 4px;
      background-color: #f5f5f5;
      font-size: 12px;
      font-weight: 500;
      cursor: pointer;
      transition: all 0.2s ease;
      text-align: center;
    }

    .filter-btn:hover {
      background-color: #e9e9e9;
    }

    .filter-btn.active {
      background-color: #0d8017;
      color: white;
      border-color: #0d8017;
    }

    .filter-btn.elementary {
      border-left: 3px solid #1e88e5;
    }
    
    .filter-btn.elementary.active {
      background-color: #1e88e5;
    }

    .filter-btn.secondary {
      border-left: 3px solid #7b0404;
    }
    
    .filter-btn.secondary.active {
      background-color: #7b0404;
    }
    
    /* Filter buttons disabled state */
    .filter-btn.disabled {
      opacity: 0.5;
      cursor: not-allowed;
      pointer-events: none;
    }

    /* Map legend */
    .map-legend {
      position: fixed;
      bottom: 20px;
      right: 20px;
      background-color: rgba(255, 255, 255, 0.86);
      padding: 10px;
      border-radius: 5px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      z-index: 1;
      font-size: 12px;
      max-width: 150px;
      transition: transform 0.3s ease-in-out;
    }

    .map-legend h6 {
      margin-bottom: 8px;
      font-size: 14px;
      font-weight: bold;
    }

    .legend-color.elementary {
      background-color: #1e88e5;
    }

    .legend-color.secondary {
      background-color: #7b0404;
    }
    
    /* Legend toggle button */
    .legend-toggle {
      position: fixed;
      bottom: 20px;
      right: 20px;
      width: 40px;
      height: 40px;
      background-color: #0d8017;
      color: white;
      border: none;
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: pointer;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
      z-index: 2;
      transition: background-color 0.3s ease;
    }

    .legend-toggle:hover {
      background-color: #0b6d14;
    }

    .legend-toggle i {
      font-size: 18px;
    }

    /* Mobile legend */
    @media (max-width: 768px) {
      .map-legend {
        transform: translateX(120%);
      }
      
      .map-legend.visible {
        transform: translateX(0);
      }
    }

    /* Desktop legend */
    @media (min-width: 769px) {
      .legend-toggle {
        display: none;
      }
      
      .map-legend {
        transform: translateX(0);
      }
    }
    
    /* Schools in barangay list */
    .schools-in-barangay {
      margin-top: 15px;
      max-height: 200px;
      overflow-y: auto;
    }
    
    .schools-in-barangay h4 {
      font-size: 16px;
      margin-bottom: 10px;
      color: #0d8017;
    }
    
    .school-item {
      padding: 8px;
      margin-bottom: 5px;
      background-color: #f5f5f5;
      border-radius: 4px;
      cursor: pointer;
      transition: all 0.2s ease;
    }
    
    .school-item:hover {
      background-color: #e0e0e0;
    }
    
    .school-item.elementary {
      border-left: 3px solid #1e88e5;
    }
    
    .school-item.secondary {
      border-left: 3px solid #7b0404;
    }

    /* Reset button */
    .reset-btn {
      height: 40px;
      margin-left: 5px;
      background-color: rgb(54, 136, 244);
      color: white;
      border: none;
      border-radius: 5px;
      padding: 0 10px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .reset-btn:hover {
      background-color: rgb(26, 125, 255);
    }

    .reset-btn i {
      font-size: 16px;
    }

    /* Filter note */
    .filter-note {
      background-color: #fff3cd;
      border-left: 4px solid #ffc107;
      padding: 8px 12px;
      margin: 10px 0;
      border-radius: 4px;
      font-size: 12px;
      color: #856404;
    }

    .filter-message {
      background-color: #fff3cd;
      border-left: 4px solid #ffc107;
      padding: 12px 15px;
      margin: 15px 0;
      border-radius: 4px;
      font-size: 14px;
      color: #856404;
      text-align: center;
    }
  </style>
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg fixed-top">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="image/deped-logo.png" alt="DepEd Logo" class="mr-1" />
        <img src="image/bagong-pilipinas-logo.png" alt="Bagong Pilipinas Logo" class="mr-1" />
        <img src="image/sdone-logo.png" alt="SDOne Logo" class="mr-1" />
      </a>
      <button
        class="navbar-toggler"
        type="button"
        data-toggle="collapse"
        data-target="#navbarNav"
        aria-controls="navbarNav"
        aria-expanded="false"
        aria-label="Toggle navigation"
      >
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item">
            <a class="nav-link" href="#"><i class="bi bi-envelope mr-1"></i>Contact Us</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" id="nav-login"><i class="bi bi-box-arrow-in-right mr-1"></i>Login</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container-fluid h-100 p-0">
    <!-- Search Bar with Filter Button -->
    <div class="search-bar-container">
      <!-- Filter Button -->
      <button id="filter-map-btn" class="filter-map-btn">
        <i class="bi bi-funnel-fill"></i>
      </button>

      <div class="search-container">
        <input
          type="text"
          id="searchInput"
          class="search-input"
          placeholder="Search by Barangay or School"
        />
        <span class="search-clear">&times;</span>
        <div class="search-spinner"></div>
      </div>

      <!-- Reset Button -->
      <button id="reset-context-btn" class="reset-btn">
        <i class="bi bi-arrow-counterclockwise"></i>
      </button>

      <!-- Filter Dropdown -->
      <div id="filter-dropdown" class="filter-dropdown">
        <div class="filter-dropdown-item" data-filter="hide">
          <i class="bi bi-eye-slash"></i> Hide All Schools
        </div>
        <div class="filter-dropdown-item active" data-filter="all">
          <i class="bi bi-eye"></i> Show All Schools
        </div>
        <div class="filter-dropdown-item" data-filter="elementary">
          <div class="color-dot blue"></div>
          Show Elementary Only
        </div>
        <div class="filter-dropdown-item" data-filter="secondary">
          <div class="color-dot red"></div>
          Show Secondary Only
        </div>
      </div>
    </div>

    <!-- Panel Toggle Button -->
    <button id="panel-toggle" class="panel-toggle" hidden>
      <i class="bi bi-list"></i>
    </button>

    <!-- Panel Section - Hidden by default -->
    <div class="panel" id="info-panel">
      <p class="top-line mh-10px"></p>

      <!-- Municipality Header Panel -->
      <div class="municipality-header" id="municipality-header">
        <div class="municipality-name" id="municipality-name">
          Nueva Ecija
        </div>
        <div class="stats-item">
          <span class="stats-label">Total Schools:</span>
          <span class="stats-value" id="total-schools">0</span>
        </div>
        <div class="stats-item">
          <span class="stats-label"><span style="color: #1e88e5">■</span> Elementary Schools:</span>
          <span class="stats-value" id="elementary-schools">0</span>
        </div>
        <div class="stats-item">
          <span class="stats-label"><span style="color: #7b0404">■</span> Secondary Schools:</span>
          <span class="stats-value" id="secondary-schools">0</span>
        </div>
      </div>

      <!-- Filter Buttons -->
      <div class="filter-buttons">
        <div class="filter-btn active" id="filter-all">All Schools</div>
        <div class="filter-btn elementary" id="filter-elementary">
          Elementary Only
        </div>
        <div class="filter-btn secondary" id="filter-secondary">
          Secondary Only
        </div>
      </div>

      <!-- Filter Note -->
      <div class="filter-note">
        <i class="bi bi-info-circle"></i> <strong>Note:</strong> Use the filter dropdown at the top to show or hide schools on the map. The panel will update to match your selection.
      </div>

      <!-- School Information Panel -->
      <div id="school-info">
        <h2>School Information</h2>
        <br />
        <p>
          <i class="bi bi-buildings"></i> <strong>District:</strong>
          <span id="district"></span>
        </p>
        <p>
          <i class="bi bi-mortarboard"></i> <strong>School ID:</strong>
          <span id="school-id"></span>
        </p>
        <p>
          <i class="bi bi-book"></i> <strong>School Name:</strong>
          <span id="school-name"></span>
        </p>
        <p>
          <i class="bi bi-mortarboard"></i> <strong>School Level:</strong>
          <span id="school-level"></span>
        </p>
        <p>
          <i class="bi bi-people"></i> <strong>No. of Enrollees:</strong>
          <span id="total-enrollees"></span>
        </p>
        <ul style="list-style: none; padding: 0">
          <li>
            <i class="bi bi-person"></i> <strong>Male:</strong>
            <span id="male-enrollees"></span>
          </li>
          <li>
            <i class="bi bi-person"></i> <strong>Female:</strong>
            <span id="female-enrollees"></span>
          </li>
        </ul>
        <p>
          <i class="bi bi-person-badge"></i>
          <strong>No. of Teachers:</strong>
          <span id="teachers"></span>
        </p>
        <p>
          <i class="bi bi-geo-alt"></i> <strong>Barangay:</strong>
          <span id="barangay"></span>
        </p>

        <!-- Google Maps Button -->
        <button
          id="open-google-maps"
          class="google-maps-btn"
          style="display: none"
        >
          <i class="bi bi-geo-alt-fill"></i>
          Open in Google Maps
        </button>

        <!-- Loading indicator for school info -->
        <div
          id="school-info-loading"
          class="panel-loading"
          style="display: none"
        >
          <div class="panel-spinner"></div>
        </div>
      </div>

      <!-- Barangay Information Panel -->
      <div id="barangay-info" style="display: none">
        <h2>Barangay Information</h2>
        <p>
          <i class="bi bi-geo-alt"></i> <strong>Barangay:</strong>
          <span id="barangay-name"></span>
        </p>
        <p>
          <i class="bi bi-building"></i> <strong>Schools:</strong>
          <span id="barangay-school-count">0</span>
        </p>
        <div class="stats-item">
          <span class="stats-label">Elementary Schools:</span>
          <span class="stats-value" id="barangay-elementary-schools">0</span>
        </div>
        <div class="stats-item">
          <span class="stats-label">Secondary Schools:</span>
          <span class="stats-value" id="barangay-secondary-schools">0</span>
        </div>
        
        <!-- Schools in Barangay List -->
        <div class="schools-in-barangay" id="schools-in-barangay">
          <h4>Schools in this Barangay:</h4>
          <div id="barangay-schools-list">
            <!-- School items will be added here dynamically -->
          </div>
        </div>
        
        <button id="back-to-overview" class="btn btn-sm">
          <i class="bi bi-arrow-left"></i> Back to Overview
        </button>

        <!-- Google Maps Button for Barangay -->
        <button
          id="barangay-google-maps"
          class="google-maps-btn"
          style="display: none"
        >
          <i class="bi bi-geo-alt-fill"></i>
          Open in Google Maps
        </button>
      </div>

      <!-- School Cards Container -->
      <div id="card-container" style="display: none">
        <!-- Cards will be appended here dynamically -->
      </div>
    </div>

    <!-- Map Section -->
    <div id="map"></div>

    <!-- Legend Toggle Button -->
    <button id="legend-toggle" class="legend-toggle">
      <i class="bi bi-info-circle"></i>
    </button>

    <!-- Map Legend -->
    <div class="map-legend">
      <!-- Congressional Districts Legend -->
      <h6 class="mb-2">Congressional Districts</h6>
      <div class="legend-item">
        <div class="legend-color" style="background-color: red;"></div>
        <span>CD1</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background-color: blue;"></div>
        <span>CD2</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background-color: yellow;"></div>
        <span>CD3</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background-color: green;"></div>
        <span>CD4</span>
      </div>
      <div class="legend-item">
        <div class="legend-color" style="background-color: gray;"></div>
        <span>City Division</span>
      </div>
      
      <hr class="my-2">
      
      <!-- School Levels Legend -->
      <h6 class="mb-2">School Levels</h6>
      <div class="legend-item">
        <div class="legend-color elementary"></div>
        <span>Elementary</span>
      </div>
      <div class="legend-item">
        <div class="legend-color secondary"></div>
        <span>Secondary</span>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

  <script>
    // Initialize the map
    const map = L.map("map", {
      zoomControl: false,
    }).setView([15.648753870285125, 121.01049859359448], 10);

    // Base tile layers
    const defaulty = L.tileLayer(
      "https://tile.openstreetmap.org/{z}/{x}/{y}.png",
      {
        maxZoom: 19,
        attribution: '© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a>',
      }
    ).addTo(map);

    const googleSat = L.tileLayer(
      "http://{s}.google.com/vt/lyrs=s&x={y}&z={z}",
      {
        maxZoom: 16,
        subdomains: ["mt0", "mt1", "mt2", "mt3"],
      }
    );

    const baseMaps = {
      Default: defaulty,
      Satellite: googleSat,
    };

    // Add layer controls and zoom control
    L.control.layers(baseMaps, {}, { position: "bottomleft" }).addTo(map);
    L.control.zoom({ position: "bottomleft" }).addTo(map);

    // Layer groups
    const geoJSONLayerGroup = L.layerGroup().addTo(map);
    const brgyLayerGroup = L.layerGroup().addTo(map);

    // Store data
    const markersMap = new Map();
    const barangayPolygons = new Map();
    const schoolData = new Map();

    // State variables
    let placeClicked = "NUEVAECIJA";
    let selectedPolygonData = null;
    let selectedBarangay = null;
    let previousZoomLevel = map.getZoom();
    let previousCenter = map.getCenter();
    let activeMarker = null;
    let currentSchoolId = null;
    let isPanelVisible = false;
    let currentMarkerCoords = null;
    let currentFilter = "all";
    let mapFilter = "all";
    let lastSearchText = ""; // Track the last search text

    // School level colors and icons
    const schoolLevelColors = {
      elementary: "#1e88e5", // Blue
      secondary: "#7b0404", // Red
    };

    const schoolLevelIcons = {
      elementary: createCustomIcon("elementary"),
      secondary: createCustomIcon("secondary"),
      elementaryLarge: createCustomIcon("elementary", true),
      secondaryLarge: createCustomIcon("secondary", true),
    };

    // School statistics
    const schoolStats = {
      total: 0,
      elementary: 0,
      secondary: 0,
    };

    // Create custom marker icons
    function createCustomIcon(schoolLevel, isLarge = false) {
      const size = isLarge ? 48 : 32;
      const anchor = isLarge ? 24 : 16;

      const canvas = document.createElement("canvas");
      canvas.width = size;
      canvas.height = size;
      const ctx = canvas.getContext("2d");

      // Draw circle with school level color
      ctx.beginPath();
      ctx.arc(size / 2, size / 2, size / 2 - 2, 0, 2 * Math.PI);
      ctx.fillStyle = schoolLevelColors[schoolLevel] || "#999";
      ctx.fill();
      ctx.strokeStyle = "#fff";
      ctx.lineWidth = 2;
      ctx.stroke();

      // Add white dot in center
      ctx.beginPath();
      ctx.arc(size / 2, size / 2, size / 6, 0, 2 * Math.PI);
      ctx.fillStyle = "#fff";
      ctx.fill();

      return L.icon({
        iconUrl: canvas.toDataURL(),
        iconSize: [size, size],
        iconAnchor: [anchor, anchor],
        popupAnchor: [0, -anchor],
      });
    }

    // Determine school level from name or data
    function getSchoolLevel(schoolName, schoolData) {
      if (schoolData && schoolData.CurricularOffer) {
        return schoolData.CurricularOffer.toLowerCase();
      }
      
      if (schoolName) {
        const name = schoolName.toLowerCase();
        if (name.includes("elementary")) {
          return "elementary";
        } else if (
          name.includes("high school") ||
          name.includes("secondary") ||
          name.includes("integrated")
        ) {
          return "secondary";
        }
      }

      return "elementary"; // Default
    }

    // Initialize filters
    function initializeFilters() {
  // Set default filters - both panel and map start synchronized
  currentFilter = "all";
  mapFilter = "all";
  
  // Update UI to reflect default state
  document.getElementById("filter-all").classList.add("active");
  document.getElementById("filter-elementary").classList.remove("active");
  document.getElementById("filter-secondary").classList.remove("active");
  updateMapFilterDropdown("all");
  
  console.log("Filters initialized - currentFilter:", currentFilter, "mapFilter:", mapFilter);
}

    // Panel toggle functionality
    const panelToggle = document.getElementById("panel-toggle");
    const infoPanel = document.getElementById("info-panel");

    function togglePanel() {
      isPanelVisible = !isPanelVisible;
      infoPanel.style.display = isPanelVisible ? "block" : "none";
      panelToggle.innerHTML = isPanelVisible ? '<i class="bi bi-x"></i>' : '<i class="bi bi-list"></i>';
    }

    panelToggle.addEventListener("click", togglePanel);

    // Hide panel on context menu
    document.addEventListener("contextmenu", function (e) {
      if (isPanelVisible) {
        togglePanel();
      }
    });

    // Get color for polygons based on CD number
    function getNextColor(cdNum) {
      cdNum = String(cdNum);
      const colors = {
        1: "red",
        2: "blue",
        3: "yellow",
        4: "green",
        10: "white",
      };
      return { 
        color: colors[cdNum] || "gray", 
        borderColor: "black" 
      };
    }

    // Handle polygon click
    function onPolygonMapClick(layer, feature) {
      selectedPolygonData = feature.properties;
      placeClicked = (
        feature.properties.adm3_en ||
        feature.properties.adm4_en ||
        "Nueva Ecija"
      )
        .toUpperCase()
        .replace(/\s+/g, "");

      // Get municipality name for display
      const municipalityName = feature.properties.adm3_en || "Nueva Ecija";
      document.getElementById("municipality-name").textContent = municipalityName;

      // Clear existing layers and reset state
      brgyLayerGroup.clearLayers();
      markersMap.clear();
      barangayPolygons.clear();
      selectedBarangay = null;
      currentSchoolId = null;
      currentMarkerCoords = null;

      // Reset school statistics
      schoolStats.total = 0;
      schoolStats.elementary = 0;
      schoolStats.secondary = 0;

      // Hide barangay info panel
      document.getElementById("barangay-info").style.display = "none";

      // Zoom to clicked polygon
      map.fitBounds(layer.getBounds());
      previousZoomLevel = map.getZoom();
      previousCenter = map.getCenter();

      // Load barangay data
      loadDataBrgy(placeClicked);

      // Show panel
      if (!isPanelVisible) {
        togglePanel();
      }
      
      // Show card container
      document.getElementById("card-container").style.display = "block";
      document.getElementById("school-info").style.display = "none";
    }

    // Fixed loadDataBrgy function
    function loadDataBrgy(data) {
      console.log("Loading barangay data for:", data);
      
      barangayPolygons.clear();
      brgyLayerGroup.clearLayers();
      
      // Ensure layer group is added to map
      if (!map.hasLayer(brgyLayerGroup)) {
        brgyLayerGroup.addTo(map);
      }
      
      fetch("phpp/map/fetchMapData.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({ data }),
      })
      .then(response => {
        if (!response.ok) {
          throw new Error("HTTP error! Status: " + response.status);
        }
        return response.json();
      })
      .then(geojsonData => {
        console.log("Received barangay GeoJSON data:", geojsonData);
        
        // Check if we have valid GeoJSON data
        if (!geojsonData || !geojsonData.features || geojsonData.features.length === 0) {
          console.error("No barangay features found in GeoJSON data");
          return;
        }
        
        // Create barangay layer
        const barangayLayer = L.geoJSON(geojsonData, {
          style: feature => {
            return {
              fillColor: "white",
              color: "black",
              weight: 2,
              fillOpacity: 0.3,
              dashArray: "5, 5" // Dashed border to distinguish from municipality borders
            };
          },
          onEachFeature: (feature, layer) => {
            // Get barangay name from various possible properties
            const barangayName = 
              feature.properties.adm4_en ||
              feature.properties.brgy_name ||
              feature.properties.name ||
              feature.properties.BRGY_NAME ||
              "Unknown";

            console.log("Creating barangay polygon for:", barangayName);

            // Store barangay polygon
            barangayPolygons.set(barangayName, layer);

            // Add popup
            layer.bindPopup("<strong>Barangay:</strong> " + barangayName);

            // Add hover and click functionality
            layer.on({
              mouseover: e => {
                if (selectedBarangay !== barangayName) {
                  const layer = e.target;
                  layer.setStyle({
                    fillOpacity: 0.6,
                    weight: 3,
                    color: "#0d8017"
                  });
                  layer.bindTooltip(barangayName, {
                    permanent: false,
                    direction: "center",
                    className: "leaflet-tooltip",
                  }).openTooltip();
                }
              },
              mouseout: e => {
                if (selectedBarangay !== barangayName) {
                  const layer = e.target;
                  layer.setStyle({
                    fillOpacity: 0.3,
                    weight: 2,
                    color: "black"
                  });
                  layer.closeTooltip();
                }
              },
              click: e => {
                // Stop propagation
                if (e.originalEvent) {
                  e.originalEvent.stopPropagation();
                }
                onBarangayClick(barangayName, e.target);

                // Show panel
                if (!isPanelVisible) {
                  togglePanel();
                }
              },
            });
          },
        });

        // Add to layer group
        barangayLayer.addTo(brgyLayerGroup);
        console.log("Created " + barangayPolygons.size + " barangay polygons");
        
        // Force map refresh
        map.invalidateSize();
      })
      .catch(error => {
        console.error("Error fetching barangay GeoJSON data:", error);
      });

      // Load marker data for schools
      loadMarkerData(placeClicked);
    }

    // Handle barangay click
    function onBarangayClick(barangayName, layer) {
      console.log("Barangay clicked: " + barangayName);

      // Reset previous selected barangay
      if (selectedBarangay && selectedBarangay !== barangayName) {
        const prevLayer = barangayPolygons.get(selectedBarangay);
        if (prevLayer) {
          prevLayer.setStyle({
            fillOpacity: 0.3,
            weight: 2,
            color: "black",
          });
        }
      }

      // Set new selected barangay
      selectedBarangay = barangayName;

      // Highlight selected barangay
      layer.setStyle({
        fillOpacity: 0.6,
        weight: 4,
        color: "#0d8017",
      });

      // Zoom to barangay
      map.fitBounds(layer.getBounds());

      // Filter schools by barangay
      filterSchoolsByBarangay(barangayName);

      // Update panel
      updateBarangayPanel(barangayName);

      // Get center for Google Maps link
      const bounds = layer.getBounds();
      const center = bounds.getCenter();

      // Show Google Maps button
      const googleMapsBtn = document.getElementById("barangay-google-maps");
      googleMapsBtn.style.display = "block";
      googleMapsBtn.onclick = function () {
        window.open(
          `https://www.google.com/maps?q=${center.lat},${center.lng}`,
          "_blank"
        );
      };
      
      // Show markers for this barangay based on current filter
      refreshMarkers();
    }

    // Filter schools by barangay
    function filterSchoolsByBarangay(barangayName) {
      // Hide all markers
      markersMap.forEach(markerData => {
        markerData.marker.remove();
      });

      // Count schools in barangay
      let schoolCount = 0;
      let elementaryCount = 0;
      let secondaryCount = 0;

      // Show only markers for schools in this barangay
      markersMap.forEach((markerData, schoolId) => {
        const schoolInfo = schoolData.get(schoolId);
        if (schoolInfo && schoolInfo.barangay_name === barangayName) {
          schoolCount++;

          // Count by school level
          if (schoolInfo.schoolLevel === "elementary") {
            elementaryCount++;
          } else if (schoolInfo.schoolLevel === "secondary") {
            secondaryCount++;
          }
        }
      });

      // Update school count in panel
      document.getElementById("barangay-school-count").textContent = schoolCount;
      document.getElementById("barangay-elementary-schools").textContent = elementaryCount;
      document.getElementById("barangay-secondary-schools").textContent = secondaryCount;

      // Show message if no schools found
      if (schoolCount === 0) {
        Swal.fire({
          title: "No Schools Found",
          text: "No schools found in Barangay " + barangayName,
          icon: "info",
          confirmButtonText: "OK",
        });
      }
    }

    // Update barangay panel
    function updateBarangayPanel(barangayName) {
      // Update barangay info
      document.getElementById("barangay-name").textContent = barangayName;
      document.getElementById("barangay-info").style.display = "block";

      // Hide other panels
      document.getElementById("school-info").style.display = "none";
      document.getElementById("card-container").style.display = "none";

      // Clear schools list
      const schoolsList = document.getElementById("barangay-schools-list");
      schoolsList.innerHTML = "";

      let hasSchools = false;
      let elementaryCount = 0;
      let secondaryCount = 0;

      // Add schools to list
      schoolData.forEach(school => {
        if (school.barangay_name === barangayName) {
          // Apply filter
          if (
            currentFilter !== "all" &&
            school.schoolLevel !== currentFilter
          ) {
            return; // Skip schools that don't match filter
          }

          hasSchools = true;

          // Count by level
          if (school.schoolLevel === "elementary") {
            elementaryCount++;
          } else if (school.schoolLevel === "secondary") {
            secondaryCount++;
          }

          // Create school item
          const schoolItem = document.createElement("div");
          schoolItem.className = `school-item ${school.schoolLevel}`;
          schoolItem.innerHTML = `
            <strong>${school.schoolName || "N/A"}</strong>
            <div>School ID: ${school.schoolID || "N/A"}</div>
          `;

          schoolItem.addEventListener("click", () => {
            const markerData = markersMap.get(school.schoolID);
            if (markerData) {
              // Zoom to marker
              zoomToMarker(markerData.position[0], markerData.position[1]);

              // Store coordinates for Google Maps
              currentMarkerCoords = {
                lat: markerData.position[0],
                lng: markerData.position[1],
              };

              // Update panel
              updatePanelFromServer(school.schoolID);

              // Reset previous active marker
              if (activeMarker && activeMarker !== markerData.marker) {
                const prevMarkerData = Array.from(markersMap.values()).find(
                  m => m.marker === activeMarker
                );
                if (prevMarkerData) {
                  activeMarker.setIcon(prevMarkerData.normalIcon);
                }
              }

              // Set active marker
              activeMarker = markerData.marker;
              markerData.marker.setIcon(markerData.largeIcon);

              // Add pulse animation
              const markerElement = markerData.marker.getElement();
              if (markerElement) {
                markerElement.classList.add("marker-pulse");
                setTimeout(() => {
                  markerElement.classList.remove("marker-pulse");
                }, 1000);
              }
            }
          });

          // Add to list
          schoolsList.appendChild(schoolItem);
        }
      });

      // Update counts
      document.getElementById("barangay-elementary-schools").textContent = elementaryCount;
      document.getElementById("barangay-secondary-schools").textContent = secondaryCount;

      // Show schools list
      if (hasSchools) {
        document.getElementById("schools-in-barangay").style.display = "block";
      } else {
        // Show no results message
        const noResults = document.createElement("div");
        noResults.className = "text-center p-4 text-gray-500";
        noResults.textContent = "No schools match the selected filter";
        schoolsList.appendChild(noResults);
        document.getElementById("schools-in-barangay").style.display = "block";
      }
    }

    // Back button handler
    document.getElementById("back-to-overview").addEventListener("click", () => {
      // Reset selected barangay
      if (selectedBarangay) {
        const layer = barangayPolygons.get(selectedBarangay);
        if (layer) {
          layer.setStyle({
            fillOpacity: 0.3,
            weight: 2,
            color: "black",
          });
        }
        selectedBarangay = null;
      }

      // Show all markers based on filter
      refreshMarkers();

      // Reset active marker
      if (activeMarker) {
        const markerData = Array.from(markersMap.values()).find(
          m => m.marker === activeMarker
        );
        if (markerData) {
          activeMarker.setIcon(markerData.normalIcon);
        }
        activeMarker = null;
      }

      // Reset current school ID and marker coordinates
      currentSchoolId = null;
      currentMarkerCoords = null;

      // Hide Google Maps buttons
      document.getElementById("open-google-maps").style.display = "none";
      document.getElementById("barangay-google-maps").style.display = "none";

      // Hide barangay info panel
      document.getElementById("barangay-info").style.display = "none";
      document.getElementById("school-info").style.display = "none";

      // Show overview panel
      showSchoolPanel(placeClicked);

      // Zoom out to previous view
      if (previousCenter && previousZoomLevel) {
        map.setView(previousCenter, previousZoomLevel);
      }
    });

    // Fixed loadMarkerData function
    function loadMarkerData(data, filter = "") {
      console.log("Loading marker data for:", data, "Filter:", filter);
      showSchoolPanel(data);
      
      // Reset school statistics
      schoolStats.total = 0;
      schoolStats.elementary = 0;
      schoolStats.secondary = 0;
      
      // Clear existing data
      markersMap.clear();
      schoolData.clear();
      
      fetch("phpp/map/fetchMarkerData.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({ 
          data: data,
          filter: filter
        }),
      })
      .then(response => {
        if (!response.ok) {
          throw new Error("HTTP error! Status: " + response.status);
        }
        return response.json();
      })
      .then(jsonData => {
        console.log("Received marker data:", jsonData);
        
        if (!Array.isArray(jsonData)) {
          console.error("Invalid marker data received:", jsonData);
          return;
        }
        
        if (jsonData.length === 0) {
          console.warn("No marker data received");
          return;
        }
        
        jsonData.forEach(item => {
          // Determine school level
          const schoolLevel = item.CurricularOffer ? 
            item.CurricularOffer.toLowerCase() : 
            getSchoolLevel(item.SchoolName, item);
          
          // Store school data
          schoolData.set(item.SchoolID, {
            schoolID: item.SchoolID,
            schoolName: item.SchoolName,
            barangay_name: item.barangay_name || "Unknown",
            schoolLevel: schoolLevel,
          });
          
          // Update statistics
          schoolStats.total++;
          if (schoolLevel === "elementary") {
            schoolStats.elementary++;
          } else if (schoolLevel === "secondary") {
            schoolStats.secondary++;
          }
          
          createMarker(item, schoolLevel);
        });
        
        console.log("Created", markersMap.size, "markers");
        console.log("School stats:", schoolStats);
        
        // Update statistics display
        updateSchoolStatistics();
        
        // Make sure filters are properly initialized
        if (mapFilter === undefined || currentFilter === undefined) {
          initializeFilters();
        }
        
        // Apply filters to show markers
        refreshMarkers();
      })
      .catch(error => {
        console.error("Error fetching marker data:", error);
      });
    }

    // Update school statistics display
    function updateSchoolStatistics() {
      document.getElementById("total-schools").textContent = schoolStats.total;
      document.getElementById("elementary-schools").textContent = schoolStats.elementary;
      document.getElementById("secondary-schools").textContent = schoolStats.secondary;
    }

    // Create marker for a school
    function createMarker(item, schoolLevel) {
      const latitude = parseFloat(item.latitude);
      const longitude = parseFloat(item.longitude);

      // Skip invalid coordinates
      if (isNaN(latitude) || isNaN(longitude)) {
        console.error("Invalid coordinates for school:", item.SchoolID, item.SchoolName);
        return;
      }

      // Use appropriate icon
      const normalIcon = schoolLevelIcons[schoolLevel] || schoolLevelIcons.elementary;
      const largeIcon = schoolLevelIcons[schoolLevel + "Large"] || schoolLevelIcons.elementaryLarge;

      // Create marker
      const marker = L.marker([latitude, longitude], { icon: normalIcon });

      // Store marker
      markersMap.set(item.SchoolID, {
        marker: marker,
        position: [latitude, longitude],
        normalIcon: normalIcon,
        largeIcon: largeIcon,
        barangay: item.barangay_name || "Unknown",
        schoolLevel: schoolLevel,
      });

      // Format school level for display
      const displayLevel = schoolLevel.charAt(0).toUpperCase() + schoolLevel.slice(1);

      // Add tooltip
      marker.bindTooltip(item.SchoolName, {
        permanent: false,
        direction: "top",
        className: "leaflet-tooltip",
      });

      // Add popup and click handler
      marker
        .bindPopup(
          "School ID: " + item.SchoolID +
          "<br>School Name: " + item.SchoolName +
          "<br>Level: " + displayLevel +
          "<br>Barangay: " + (item.barangay_name || item.adm3_en || "Unknown")
        )
        .on("click", function () {
          const sID = item.SchoolID;
          console.log("Marker clicked for school ID:", sID);

          // Store coordinates for Google Maps
          currentMarkerCoords = {
            lat: latitude,
            lng: longitude,
          };

          // Reset previous active marker
          if (activeMarker && activeMarker !== marker) {
            const prevMarkerData = Array.from(markersMap.values()).find(
              m => m.marker === activeMarker
            );
            if (prevMarkerData) {
              activeMarker.setIcon(prevMarkerData.normalIcon);
            }
          }

          // Set active marker
          activeMarker = marker;

          // Zoom to marker
          zoomToMarker(latitude, longitude);

          // Make marker larger
          marker.setIcon(largeIcon);

          // Add pulse animation
          const markerElement = marker.getElement();
          if (markerElement) {
            markerElement.classList.add("marker-pulse");
            setTimeout(() => {
              markerElement.classList.remove("marker-pulse");
            }, 1000);
          }

          // Update panel
          updatePanelFromServer(sID);

          // Show panel
          if (!isPanelVisible) {
            togglePanel();
          }
        });

      // Don't add to map yet - will be done in refreshMarkers()
    }

    // Fixed refreshMarkers function with better logic
    function refreshMarkers() {
  console.log("Refreshing markers - mapFilter:", mapFilter, "currentFilter:", currentFilter, "selectedBarangay:", selectedBarangay);
  
  // Remove all markers from the map first
  markersMap.forEach(markerData => {
    markerData.marker.remove();
  });
  
  // Make sure layer group is on map
  if (!map.hasLayer(brgyLayerGroup)) {
    brgyLayerGroup.addTo(map);
  }
  
  // If map filter is "hide", don't show any markers
  if (mapFilter === "hide") {
    console.log("Map filter is 'hide' - not showing any markers");
    return;
  }
  
  // Add markers based on filters
  let visibleMarkers = 0;
  
  markersMap.forEach((markerData, schoolId) => {
    const schoolInfo = schoolData.get(schoolId);
    if (!schoolInfo) {
      console.log("No school info found for ID:", schoolId);
      return;
    }
    
    // Check barangay filter (only apply if a barangay is selected)
    const matchesBarangay = !selectedBarangay || schoolInfo.barangay_name === selectedBarangay;
    
    // Use the synchronized filter (both panel and map filters should be the same now)
    const effectiveFilter = mapFilter !== "hide" ? mapFilter : currentFilter;
    
    // Check filter (unified between panel and map)
    const matchesFilter = 
      effectiveFilter === "all" || 
      effectiveFilter === schoolInfo.schoolLevel;
    
    // Show marker if it matches all active filters
    const shouldShow = matchesBarangay && matchesFilter;
    
    if (shouldShow) {
      markerData.marker.addTo(brgyLayerGroup);
      visibleMarkers++;
    }
  });
  
  console.log(`Showing ${visibleMarkers} markers out of ${markersMap.size} total markers`);
}

    // Zoom to marker with animation
    function zoomToMarker(lat, lng) {
      map.flyTo([lat, lng], 16, {
        animate: true,
        duration: 1.5, // seconds
      });
    }

    // Show school panel with data
    function showSchoolPanel(data) {
      fetch("phpp/map/fetchPolygonPanel.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({ data }),
      })
        .then(response => response.json())
        .then(data => {
          polygonPanel(data);
        })
        .catch(error => console.error("Error fetching data:", error));
    }

    // Update polygon panel with school data
    function polygonPanel(data) {
      const container = document.getElementById("card-container");
      container.innerHTML = ""; // Clear existing content

      if (!Array.isArray(data) || data.length === 0) {
        container.innerHTML = "<p class='text-gray-500 text-center mt-4'>No data available</p>";
        return;
      }

      let displayedCount = 0;

      data.forEach(item => {
        // Determine school level
        const schoolLevel = item.CurricularOffer ? 
          item.CurricularOffer.toLowerCase() : 
          getSchoolLevel(item.schoolName, item);

        // Apply filter
        if (currentFilter !== "all" && schoolLevel !== currentFilter) {
          return; // Skip schools that don't match filter
        }

        displayedCount++;

        const card = document.createElement("div");
        card.className = `card ${schoolLevel}`;

        // Create school level badge
        const levelBadge = `<span class="school-level-badge ${schoolLevel}">${
          schoolLevel.charAt(0).toUpperCase() + schoolLevel.slice(1)
        }</span>`;

        card.innerHTML =
          "<h5 class='text-lg font-semibold'>" +
          (item.schoolName || "N/A") +
          " " +
          levelBadge +
          "</h5>" +
          "<p class='text-sm'>School ID: <span class='font-medium'>" +
          (item.schoolID || "N/A") +
          "</span></p>" +
          "<p class='text-sm'>District: <span class='font-medium'>" +
          (item.district || "N/A") +
          "</span></p>" +
          "<p class='text-sm'>Barangay: <span class='font-medium'>" +
          (item.barangay_name || "N/A") +
          "</span></p>";

        card.addEventListener("click", () => {
          // Find marker for this school
          const markerData = markersMap.get(item.schoolID);
          if (markerData) {
            // Zoom to marker
            zoomToMarker(markerData.position[0], markerData.position[1]);

            // Store coordinates for Google Maps
            currentMarkerCoords = {
              lat: markerData.position[0],
              lng: markerData.position[1],
            };

            // Update panel
            updatePanelFromServer(item.schoolID);

            // Reset previous active marker
            if (activeMarker && activeMarker !== markerData.marker) {
              const prevMarkerData = Array.from(markersMap.values()).find(
                m => m.marker === activeMarker
              );
              if (prevMarkerData) {
                activeMarker.setIcon(prevMarkerData.normalIcon);
              }
            }

            // Set active marker
            activeMarker = markerData.marker;
            markerData.marker.setIcon(markerData.largeIcon);

            // Add pulse animation
            const markerElement = markerData.marker.getElement();
            if (markerElement) {
              markerElement.classList.add("marker-pulse");
              setTimeout(() => {
                markerElement.classList.remove("marker-pulse");
              }, 1000);
            }
          } else {
            Swal.fire({
              title: item.schoolName || "N/A",
              text:
                "School ID: " +
                (item.schoolID || "N/A") +
                "\nBarangay: " +
                (item.barangay_name || "N/A") +
                "\nLevel: " +
                schoolLevel.charAt(0).toUpperCase() +
                schoolLevel.slice(1),
              icon: "info",
              confirmButtonText: "OK",
            });
          }
        });

        container.appendChild(card);
      });

      // Show "no results" message if no schools match filter
      if (displayedCount === 0) {
        const noResults = document.createElement("div");
        noResults.className = "text-center p-4 text-gray-500";
        noResults.textContent = "No schools match the selected filter";
        container.appendChild(noResults);
      }

      document.getElementById("card-container").style.display = "block";
      document.getElementById("school-info").style.display = "none";
      document.getElementById("barangay-info").style.display = "none";

      // Hide Google Maps buttons
      document.getElementById("open-google-maps").style.display = "none";
      document.getElementById("barangay-google-maps").style.display = "none";
    }

    // Update panel with fetched data
    function updatePanelFromServer(schoolID) {
      // Don't fetch if already showing this school
      if (currentSchoolId === schoolID) {
        return;
      }

      // Update current school ID
      currentSchoolId = schoolID;

      // Show loading indicator
      const schoolInfoPanel = document.getElementById("school-info");
      const loadingIndicator = document.getElementById("school-info-loading");

      // Clear previous data
      document.getElementById("district").textContent = "";
      document.getElementById("school-id").textContent = "";
      document.getElementById("school-name").textContent = "";
      document.getElementById("school-level").textContent = "";
      document.getElementById("total-enrollees").textContent = "";
      document.getElementById("male-enrollees").textContent = "";
      document.getElementById("female-enrollees").textContent = "";
      document.getElementById("teachers").textContent = "";
      document.getElementById("barangay").textContent = "";

      // Show panel with loading indicator
      schoolInfoPanel.style.display = "block";
      loadingIndicator.style.display = "flex";

      // Hide other panels
      document.getElementById("card-container").style.display = "none";
      document.getElementById("barangay-info").style.display = "none";

      fetch("phpp/map/fetchPanelData.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: "SchoolID=" + encodeURIComponent(schoolID),
      })
        .then(response => {
          if (!response.ok) {
            throw new Error("Network response was not ok: " + response.status);
          }
          return response.json();
        })
        .then(data => {
          // Hide loading indicator
          loadingIndicator.style.display = "none";

          // Update panel
          updatePanel(data);

          // Show Google Maps button if we have coordinates
          const googleMapsBtn = document.getElementById("open-google-maps");
          if (currentMarkerCoords) {
            googleMapsBtn.style.display = "block";
            googleMapsBtn.onclick = function () {
              window.open(
                `https://www.google.com/maps?q=${currentMarkerCoords.lat},${currentMarkerCoords.lng}`,
                "_blank"
              );
            };
          } else {
            googleMapsBtn.style.display = "none";
          }
        })
        .catch(error => {
          console.error("Error fetching data:", error);
          loadingIndicator.style.display = "none";
          
          Swal.fire({
            title: "Error",
            text: "Failed to load school information. Please try again.",
            icon: "error",
            confirmButtonText: "OK",
          });
        });
    }

    // Update panel with school information
    function updatePanel(data) {
      // Check if data is valid
      if (!data || !data.school_info) {
        console.error("Invalid data received:", data);
        return;
      }

      // Determine school level
      const schoolName = data.school_info.schoolName || "";
      const schoolLevel = data.school_info.CurricularOffer ? 
        data.school_info.CurricularOffer.toLowerCase() : 
        getSchoolLevel(schoolName, data.school_info);

      // Format for display
      const displayLevel = schoolLevel.charAt(0).toUpperCase() + schoolLevel.slice(1);

      // Update fields
      document.getElementById("district").textContent = data.school_info.district || "N/A";
      document.getElementById("school-id").textContent = data.school_info.schoolID || "N/A";
      document.getElementById("school-name").textContent = data.school_info.schoolName || "N/A";
      document.getElementById("school-level").textContent = displayLevel;
      document.getElementById("school-level").className = schoolLevel; // Add class for styling
      document.getElementById("total-enrollees").textContent = data.enrollment_data.total_enrollees ?? "N/A";
      document.getElementById("male-enrollees").textContent = data.enrollment_data.total_male || "N/A";
      document.getElementById("female-enrollees").textContent = data.enrollment_data.total_female || "N/A";
      document.getElementById("teachers").textContent = data.employee_count ?? "N/A";
      document.getElementById("barangay").textContent = data.school_info.barangay_name || "N/A";

      // Show panel
      document.getElementById("school-info").style.display = "block";
      document.getElementById("card-container").style.display = "none";
      document.getElementById("barangay-info").style.display = "none";
    }

    // Load GeoJSON data for the map
    function loadGeoJSONData(data) {
      fetch("phpp/map/fetchMapData.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: new URLSearchParams({ data }),
      })
        .then(response => {
          if (!response.ok) {
            throw new Error("HTTP error! Status: " + response.status);
          }
          return response.json();
        })
        .then(geojsonData => {
          L.geoJSON(geojsonData, {
            style: feature => {
              const { color, borderColor } = getNextColor(feature.properties.CD_NUM);
              return {
                fillColor: color,
                color: borderColor,
                weight: 1,
                fillOpacity: 0.175,
              };
            },
            onEachFeature: (feature, layer) => {
              // Add hover functionality
              layer.on({
                mouseover: e => {
                  const layer = e.target;
                  layer.setStyle({
                    fillOpacity: 0.5,
                    weight: 2,
                  });
                  layer
                    .bindTooltip(feature.properties.adm3_en || "Unknown", {
                      permanent: false,
                      direction: "center",
                      className: "leaflet-tooltip",
                    })
                    .openTooltip();
                },
                mouseout: e => {
                  const layer = e.target;
                  layer.setStyle({
                    fillOpacity: 0.175,
                    weight: 1,
                  });
                  layer.closeTooltip();
                },
                click: e => {
                  onPolygonMapClick(e.target, feature);
                },
              });
            },
          }).addTo(geoJSONLayerGroup);
        })
        .catch(error => {
          console.error("Error fetching GeoJSON data:", error);
        });
    }

    // Load initial GeoJSON data
    loadGeoJSONData(placeClicked);

    // Handle right-click to reset view
    let lastRightClickTime = 0;

    document.addEventListener("contextmenu", event => {
      const currentTime = Date.now();
      const timeDifference = currentTime - lastRightClickTime;
      
      if (timeDifference >= 1000) {
        event.preventDefault();
        resetMapView();
        lastRightClickTime = currentTime;
      }
    });

    // Fixed resetMapView function
    function resetMapView() {
  console.log("Resetting map view");
  
  // Hide panels
  document.getElementById("school-info").style.display = "none";
  document.getElementById("card-container").style.display = "none";
  document.getElementById("barangay-info").style.display = "none";
  
  // Hide Google Maps buttons
  document.getElementById("open-google-maps").style.display = "none";
  document.getElementById("barangay-google-maps").style.display = "none";

  // Reset map and layers
  geoJSONLayerGroup.clearLayers();
  brgyLayerGroup.clearLayers();

  // Reset state variables
  activeMarker = null;
  selectedBarangay = null;
  currentSchoolId = null;
  currentMarkerCoords = null;
  markersMap.clear();
  barangayPolygons.clear();
  schoolData.clear();
  placeClicked = "NUEVAECIJA";

  // Reset school statistics
  schoolStats.total = 0;
  schoolStats.elementary = 0;
  schoolStats.secondary = 0;
  updateSchoolStatistics();

  // Reset municipality name
  document.getElementById("municipality-name").textContent = "Nueva Ecija";

  // Reset filters to default - synchronize both
  currentFilter = "all";
  mapFilter = "all";
  
  // Update filter UI - both panel and dropdown
  document.getElementById("filter-all").classList.add("active");
  document.getElementById("filter-elementary").classList.remove("active");
  document.getElementById("filter-secondary").classList.remove("active");
  updateMapFilterDropdown("all");
  
  // Enable all filter buttons
  document.getElementById("filter-all").classList.remove("disabled");
  document.getElementById("filter-elementary").classList.remove("disabled");
  document.getElementById("filter-secondary").classList.remove("disabled");

  // Reload initial data
  loadGeoJSONData("NUEVAECIJA");

  // Reset view to default
  const defaultCenter = [15.666687955868635, 121.0108536597733];
  const defaultZoomLevel = 10;
  map.setView(defaultCenter, defaultZoomLevel);
  
  console.log("Map view reset complete - filters synchronized");
}

    // Add Reset Context Button functionality
    const resetContextBtn = document.getElementById("reset-context-btn");
    resetContextBtn.addEventListener("click", function() {
      resetMapView();
      
      // Clear search input
      document.getElementById("searchInput").value = "";
      document.querySelector(".search-clear").style.display = "none";
      
      // Hide the panel
      if (isPanelVisible) {
        togglePanel();
      }
    });

    // Add reset button to map
    const resetButton = L.control({ position: "topright" });
    resetButton.onAdd = () => {
      const div = L.DomUtil.create("div", "reset-button");
      div.innerHTML = '<button class="btn btn-sm btn-light" style="padding: 6px 10px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);"><i class="bi bi-arrows-fullscreen"></i></button>';
      div.onclick = () => {
        // Reset active marker
        if (activeMarker) {
          const markerData = Array.from(markersMap.values()).find(
            m => m.marker === activeMarker
          );
          if (markerData) {
            activeMarker.setIcon(markerData.normalIcon);
          }
          activeMarker = null;
        }

        // Reset current school ID and marker coordinates
        currentSchoolId = null;
        currentMarkerCoords = null;

        // Hide Google Maps buttons
        document.getElementById("open-google-maps").style.display = "none";
        document.getElementById("barangay-google-maps").style.display = "none";

        // Reset selected barangay
        if (selectedBarangay) {
          const layer = barangayPolygons.get(selectedBarangay);
          if (layer) {
            layer.setStyle({
              fillOpacity: 0.3,
              weight: 2,
              color: "black",
            });
          }
          selectedBarangay = null;

          // Show all markers based on filter
          refreshMarkers();

          // Hide barangay info panel
          document.getElementById("barangay-info").style.display = "none";

          // Show overview panel
          showSchoolPanel(placeClicked);
        }

        // Return to previous view
        if (previousCenter && previousZoomLevel) {
          map.setView(previousCenter, previousZoomLevel);
        } else {
          const defaultCenter = [15.666687955868635, 121.0108536597733];
          const defaultZoomLevel = 10;
          map.setView(defaultCenter, defaultZoomLevel);
        }
      };
      return div;
    };
    resetButton.addTo(map);

    // Panel dragging functionality for mobile
    const panel = document.querySelector(".panel");
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
        const deltaY = startY - (event.touches ? event.touches[0].clientY : event.clientY);
        const newHeight = window.innerHeight - startPanelY + deltaY;
        const maxHeight = window.innerHeight * 0.85;
        panel.style.height = Math.min(Math.max(newHeight, 150), maxHeight) + "px";
        map.invalidateSize();
      }
    }

    function onDragEnd() {
      isDragging = false;
      panel.classList.remove("draggable");
    }

    // Add event listeners for panel dragging
    panel.addEventListener("touchstart", onDragStart);
    document.addEventListener("touchmove", onDragMove);
    document.addEventListener("touchend", onDragEnd);

    panel.addEventListener("mousedown", onDragStart);
    document.addEventListener("mousemove", onDragMove);
    document.addEventListener("mouseup", onDragEnd);

    // Reset panel height on window resize
    function resetPanelHeightForDesktop() {
      if (window.innerWidth > 768) {
        panel.style.height = "calc(100vh - 40px)";
      } else {
        panel.style.height = "150px";
      }
    }

    window.addEventListener("resize", resetPanelHeightForDesktop);
    resetPanelHeightForDesktop();

    // Fixed setFilter function
    function setFilter(filter) {
  console.log("Setting panel filter to:", filter);
  
  // Update current filter
  currentFilter = filter;

  // Update active button state
  document.getElementById("filter-all").classList.toggle("active", filter === "all");
  document.getElementById("filter-elementary").classList.toggle("active", filter === "elementary");
  document.getElementById("filter-secondary").classList.toggle("active", filter === "secondary");

  // Synchronize map filter dropdown with panel filter
  // Only sync if map filter is not "hide" - preserve "hide" state
  if (mapFilter !== "hide") {
    mapFilter = filter;
    updateMapFilterDropdown(filter);
  }

  // Only refresh markers if map filter is not "hide"
  if (mapFilter !== "hide") {
    refreshMarkers();
  }

  // Update panel content
  if (selectedBarangay) {
    updateBarangayPanel(selectedBarangay);
  } else {
    showSchoolPanel(placeClicked);
  }
}

    // Fixed applyMapFilter function
    function applyMapFilter(filter) {
  console.log("Applying map filter:", filter);
  
  // Update current map filter
  mapFilter = filter;

  // Update dropdown UI
  updateMapFilterDropdown(filter);

  // Make sure layer group is on map
  if (!map.hasLayer(brgyLayerGroup)) {
    brgyLayerGroup.addTo(map);
  }

  // Handle different filter cases
  if (filter === "hide") {
    // Hide all markers
    markersMap.forEach(markerData => {
      markerData.marker.remove();
    });
    
    // Show message when "hide" is selected
    const container = document.getElementById("card-container");
    if (container) {
      container.innerHTML = "<div class='filter-message'><i class='bi bi-info-circle'></i> All school markers are currently hidden. Use the filter to show schools.</div>";
      container.style.display = "block";
    }
    
    // Hide info panels
    document.getElementById("school-info").style.display = "none";
    document.getElementById("barangay-info").style.display = "none";
    
    // Disable filter buttons but don't change their active state
    document.getElementById("filter-all").classList.add("disabled");
    document.getElementById("filter-elementary").classList.add("disabled");
    document.getElementById("filter-secondary").classList.add("disabled");
    
    // Close dropdown
    filterDropdown.classList.remove("show");
    return;
  }

  // Enable all filter buttons for non-hide filters
  document.getElementById("filter-all").classList.remove("disabled");
  document.getElementById("filter-elementary").classList.remove("disabled");
  document.getElementById("filter-secondary").classList.remove("disabled");

  // Synchronize panel filter with map filter
  currentFilter = filter;
  
  // Update panel button active states
  document.getElementById("filter-all").classList.toggle("active", filter === "all");
  document.getElementById("filter-elementary").classList.toggle("active", filter === "elementary");
  document.getElementById("filter-secondary").classList.toggle("active", filter === "secondary");

  // Refresh markers
  refreshMarkers();
  
  // Update panel content
  if (selectedBarangay) {
    updateBarangayPanel(selectedBarangay);
  } else {
    showSchoolPanel(placeClicked);
  }
  
  // Close dropdown
  filterDropdown.classList.remove("show");
}

    // Update map filter dropdown UI
    function updateMapFilterDropdown(filter) {
  console.log("Updating map filter dropdown to:", filter);
  
  // Update active state
  const items = document.querySelectorAll(".filter-dropdown-item");
  items.forEach(item => {
    item.classList.remove("active");
    if (item.dataset.filter === filter) {
      item.classList.add("active");
    }
  });
}

    // Add event listeners for filter buttons
    document.getElementById("filter-all").addEventListener("click", () => setFilter("all"));
    document.getElementById("filter-elementary").addEventListener("click", () => setFilter("elementary"));
    document.getElementById("filter-secondary").addEventListener("click", () => setFilter("secondary"));

    // Filter map button and dropdown
    const filterMapBtn = document.getElementById("filter-map-btn");
    const filterDropdown = document.getElementById("filter-dropdown");

    // Toggle dropdown
    filterMapBtn.addEventListener("click", e => {
      e.stopPropagation();
      filterDropdown.classList.toggle("show");
    });

    // Close dropdown when clicking outside
    document.addEventListener("click", () => {
      filterDropdown.classList.remove("show");
    });

    // Prevent dropdown from closing when clicking inside
    filterDropdown.addEventListener("click", e => {
      e.stopPropagation();
    });

    // Add event listeners to dropdown items
    document.querySelectorAll(".filter-dropdown-item").forEach(item => {
      item.addEventListener("click", () => {
        const filter = item.dataset.filter;
        console.log("Filter dropdown clicked:", filter);
        
        // Show panel if not visible
        if (!isPanelVisible) {
          togglePanel();
        }
        
        // Apply filter
        applyMapFilter(filter);
        
        // Close dropdown
        filterDropdown.classList.remove("show");
      });
    });

    // Login functionality
    document.getElementById("nav-login").addEventListener("click", () => {
      window.location.href = 'auth/pages/login.php';
    });

    // Search functionality
    const searchInput = document.getElementById("searchInput");
    const clearSearchBtn = document.querySelector(".search-clear");

    // Clear search function
    function clearSearch() {
      searchInput.value = "";
      clearSearchBtn.style.display = "none";
      lastSearchText = ""; // Reset last search text
      
      // Show loading indicator
      const searchSpinner = document.querySelector(".search-spinner");
      searchSpinner.style.display = "block";
      
      // Reset search and reload panel content
      if (selectedBarangay) {
        updateBarangayPanel(selectedBarangay);
      } else {
        showSchoolPanel(placeClicked);
      }
      
      // Show card container
      document.getElementById("card-container").style.display = "block";
      
      // Refresh markers
      refreshMarkers();
      
      // Hide loading indicator
      searchSpinner.style.display = "none";
    }

    // Add event listener to clear button
    clearSearchBtn.addEventListener("click", clearSearch);

    // Debounce function
    function debounce(func, wait) {
      let timeout;
      return function (...args) {
        const context = this;
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(context, args), wait);
      };
    }

    // Debounced search
    const debouncedSearch = debounce(function () {
      performSearch(searchInput.value);
    }, 300);

    // Main search function
    function performSearch(searchText) {
  const searchSpinner = document.querySelector(".search-spinner");
  searchSpinner.style.display = "block";

  // Normalize search text
  searchText = searchText.toLowerCase().trim();

  // Show clear button if search text exists
  clearSearchBtn.style.display = searchText ? "block" : "none";

  // If no search text, restore original view
  if (!searchText) {
    clearSearch();
    return;
  }

  // Always reset the markers and refresh the view when searching
  brgyLayerGroup.clearLayers();
  
  // Get cards
  const cardContainer = document.getElementById("card-container");
  cardContainer.style.display = "block";
  cardContainer.innerHTML = ""; // Clear existing cards
  
  // Filter schools
  let matchCount = 0;

  // Use synchronized filter
  const effectiveFilter = mapFilter !== "hide" ? mapFilter : currentFilter;

  // Filter markers on map
  markersMap.forEach((markerData, schoolId) => {
    const schoolInfo = schoolData.get(schoolId);
    if (!schoolInfo) return;

    const schoolName = (schoolInfo.schoolName || "").toLowerCase();
    const barangayName = (schoolInfo.barangay_name || "").toLowerCase();
    const schoolIdStr = schoolId.toString();

    const matchesSearch =
      schoolName.includes(searchText) ||
      barangayName.includes(searchText) ||
      schoolIdStr.includes(searchText);

    const matchesFilter =
      effectiveFilter === "all" ||
      effectiveFilter === schoolInfo.schoolLevel;

    const matchesBarangay = !selectedBarangay || schoolInfo.barangay_name === selectedBarangay;

    const matchesMapFilter = mapFilter !== "hide";

    if (matchesSearch && matchesFilter && matchesBarangay && matchesMapFilter) {
      markerData.marker.addTo(brgyLayerGroup);
      matchCount++;
      
      // Create card for this school
      const card = document.createElement("div");
      card.className = `card ${schoolInfo.schoolLevel}`;
      
      // Create school level badge
      const levelBadge = `<span class="school-level-badge ${schoolInfo.schoolLevel}">${
        schoolInfo.schoolLevel.charAt(0).toUpperCase() + schoolInfo.schoolLevel.slice(1)
      }</span>`;
      
      card.innerHTML =
        "<h5 class='text-lg font-semibold'>" +
        (schoolInfo.schoolName || "N/A") +
        " " +
        levelBadge +
        "</h5>" +
        "<p class='text-sm'>School ID: <span class='font-medium'>" +
        (schoolInfo.schoolID || "N/A") +
        "</span></p>" +
        "<p class='text-sm'>Barangay: <span class='font-medium'>" +
        (schoolInfo.barangay_name || "N/A") +
        "</span></p>";
        
      // Highlight search terms
      const regex = new RegExp(searchText.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&'), 'gi');
      card.innerHTML = card.innerHTML.replace(regex, match => `<span class="search-highlight">${match}</span>`);
      
      card.addEventListener("click", () => {
        // Zoom to marker
        zoomToMarker(markerData.position[0], markerData.position[1]);
        
        // Store coordinates for Google Maps
        currentMarkerCoords = {
          lat: markerData.position[0],
          lng: markerData.position[1],
        };
        
        // Update panel
        updatePanelFromServer(schoolId);
        
        // Reset previous active marker
        if (activeMarker && activeMarker !== markerData.marker) {
          const prevMarkerData = Array.from(markersMap.values()).find(
            m => m.marker === activeMarker
          );
          if (prevMarkerData) {
            activeMarker.setIcon(prevMarkerData.normalIcon);
          }
        }
        
        // Set active marker
        activeMarker = markerData.marker;
        markerData.marker.setIcon(markerData.largeIcon);
        
        // Add pulse animation
        const markerElement = markerData.marker.getElement();
        if (markerElement) {
          markerElement.classList.add("marker-pulse");
          setTimeout(() => {
            markerElement.classList.remove("marker-pulse");
          }, 1000);
        }
      });
      
      cardContainer.appendChild(card);
    } else {
      markerData.marker.remove();
    }
  });

  // Show "no results" message
  if (matchCount === 0) {
    cardContainer.innerHTML = "<div class='filter-message'><i class='bi bi-info-circle'></i> No schools match your search criteria. Try a different search term or check your spelling.</div>";
    
    // Hide other panels
    document.getElementById("school-info").style.display = "none";
    document.getElementById("barangay-info").style.display = "none";
  }
  
  // Update last search text
  lastSearchText = searchText;
  
  // Hide loading indicator
  searchSpinner.style.display = "none";
  
  // Show panel if not visible
  if (!isPanelVisible) {
    togglePanel();
  }
}

    // Add event listener to search input
    searchInput.addEventListener("input", debouncedSearch);

    // Add event listener for Enter key
    searchInput.addEventListener("keydown", function (e) {
      if (e.key === "Enter") {
        e.preventDefault();
        performSearch(searchInput.value);
      }

      // Clear on Escape key
      if (e.key === "Escape") {
        clearSearch();
      }
    });

    // Legend toggle functionality
    const legendToggle = document.getElementById("legend-toggle");
    const mapLegend = document.querySelector(".map-legend");

    // Initialize legend visibility
    function initLegendVisibility() {
      if (window.innerWidth <= 768) {
        mapLegend.classList.remove("visible");
      }
    }

    // Toggle legend visibility
    legendToggle.addEventListener("click", () => {
      mapLegend.classList.toggle("visible");
      
      // Change icon based on visibility
      if (mapLegend.classList.contains("visible")) {
        legendToggle.innerHTML = '<i class="bi bi-x-lg"></i>';
      } else {
        legendToggle.innerHTML = '<i class="bi bi-info-circle"></i>';
      }
    });

    // Update legend visibility on resize
    window.addEventListener("resize", initLegendVisibility);
    
    // Initialize on page load
    initLegendVisibility();

    // Initialize filters when page loads
    document.addEventListener('DOMContentLoaded', function() {
      initializeFilters();
    });

    // Also initialize when loading initial data
    initializeFilters();

    function addFilterSyncIndicator() {
  // Add a small indicator to show filters are synchronized
  const filterButtons = document.querySelectorAll('.filter-btn');
  filterButtons.forEach(btn => {
    btn.title = "Panel and map filters are synchronized";
  });
  
  const dropdownItems = document.querySelectorAll('.filter-dropdown-item');
  dropdownItems.forEach(item => {
    if (item.dataset.filter !== 'hide') {
      item.title = "Panel and map filters are synchronized";
    }
  });
}

// Initialize synchronized filters when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
  initializeFilters();
  addFilterSyncIndicator();
  
  console.log("Filter synchronization initialized");
});

// Re-bind event listeners with synchronization
document.getElementById("filter-all").addEventListener("click", () => {
  console.log("Panel 'All' button clicked - synchronizing with map filter");
  setFilter("all");
});

document.getElementById("filter-elementary").addEventListener("click", () => {
  console.log("Panel 'Elementary' button clicked - synchronizing with map filter");
  setFilter("elementary");
});

document.getElementById("filter-secondary").addEventListener("click", () => {
  console.log("Panel 'Secondary' button clicked - synchronizing with map filter");
  setFilter("secondary");
});

// Enhanced dropdown event listeners
document.querySelectorAll(".filter-dropdown-item").forEach(item => {
  item.addEventListener("click", () => {
    const filter = item.dataset.filter;
    console.log("Map filter dropdown clicked:", filter, "- synchronizing with panel buttons");
    
    // Show panel if not visible
    if (!isPanelVisible) {
      togglePanel();
    }
    
    // Apply filter (this will sync both map and panel)
    applyMapFilter(filter);
    loadMarkerData(placeClicked);
    // Close dropdown
    filterDropdown.classList.remove("show");
  });
});

console.log("Filter binding and synchronization loaded successfully");
  </script>
</body>
</html>
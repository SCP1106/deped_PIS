<?php
// Prevent browser caching of authenticated pages
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/pages/login.php');
    exit;
}
require_once 'auth/core/database.php';
$user_id = $_SESSION['user_id'];

// Handle profile update POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['firstName'] ?? '');
    $middle_name = trim($_POST['middleName'] ?? '');
    $last_name = trim($_POST['lastName'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $profilePicDir = __DIR__ . '/uploads/profile_pictures/';
    $allowed_types = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
    $max_size = 2 * 1024 * 1024; // 2MB
    // Basic validation (expand as needed)
    if ($first_name && $last_name && $email) {
        // Handle profile picture upload
        if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['profile_picture']['tmp_name'];
            $file_type = mime_content_type($file_tmp);
            $file_size = $_FILES['profile_picture']['size'];
            if (!array_key_exists($file_type, $allowed_types)) {
                $update_error = 'Only JPG and PNG images are allowed.';
            } elseif ($file_size > $max_size) {
                $update_error = 'Profile picture must be less than 2MB.';
            } else {
                // Remove old profile picture if exists
                foreach (["jpg", "png"] as $ext) {
                    $old_path = $profilePicDir . "user_{$user_id}.{$ext}";
                    if (file_exists($old_path)) unlink($old_path);
                }
                $ext = $allowed_types[$file_type];
                $new_path = $profilePicDir . "user_{$user_id}.{$ext}";
                move_uploaded_file($file_tmp, $new_path);
            }
        }
        if (empty($update_error)) {
            // Only update first_name, middle_name, last_name
            $stmt = $mysqli->prepare('UPDATE users SET first_name=?, middle_name=?, last_name=? WHERE id=?');
            $stmt->bind_param('sssi', $first_name, $middle_name, $last_name, $user_id);
            if (!$stmt->execute()) {
                $update_error = 'Database error: ' . $stmt->error;
            }
            $stmt->close();
            if (empty($update_error)) {
                // Remove ?edit=1 from the URL after a successful update
                header('Location: profile.php?success=1');
                exit;
            }
        }
    } else {
        $update_error = 'Please fill in all required fields.';
    }
}

// Fetch user info (fresh after update)
$user = null;
if ($stmt = $mysqli->prepare('SELECT u.first_name, u.middle_name, u.last_name, u.email, ur.role FROM users u LEFT JOIN user_roles ur ON u.id = ur.user_id WHERE u.id = ? LIMIT 1')) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($first_name, $middle_name, $last_name, $email, $role);
    if ($stmt->fetch()) {
        $user = [
            'first_name' => $first_name,
            'middle_name' => $middle_name,
            'last_name' => $last_name,
            'email' => $email,
            'role' => $role
        ];
    }
    $stmt->close();
}
if (!$user) {
    // User not found or error
    header('Location: auth/pages/login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>DepEd: Registration</title>
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
        background: url("data:image/svg+xml,%3Csvg width='100%' height='200' viewBox='100 50 1100 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M0,100 C200,250 400,-50 600,100 C800,250 1000,-50 1200,100' fill='none' stroke='%232e8b57' stroke-width='3' stroke-linecap='round'/%3E%3C/svg%3E")
          repeat-x;
        background-size: cover;
        background-position: center;
      }
      #sidebar-container .sidebar {
        height: 100vh;
        overflow-y: auto; /* Allows vertical scrolling */
      }

      .settings-card {
        background-color: #ffffff;
        border-radius: 15px;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
        padding: 2rem;
        max-width: 500px;
        max-height: 750px;
        width: 100%;
        transition: all 0.3s ease;
      }

      .settings-card:hover {
        transform: translateY(-10px);
      }

      .settings-card h2 {
        color: #2e8b57;
        margin-bottom: 1.5rem;
        text-align: center;
        font-size: 2rem;
      }

      .form-control,
      .form-select {
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e0e0e0;
        transition: all 0.3s ease;
      }

      .form-control:focus,
      .form-select:focus {
        border-color: #2e8b57;
        box-shadow: 0 0 0 0.2rem rgba(46, 139, 87, 0.25);
      }

      .btn-primary {
        background-color: #2e8b57;
        border-color: #2e8b57;
        border-radius: 8px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
      }

      .btn-primary:hover {
        background-color: #246c46;
        border-color: #246c46;
      }

      .form-label {
        font-weight: 500;
        color: #333;
      }

      .profile-picture {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e0e0e0;
        margin-bottom: 1rem;
      }

      .invalid-feedback {
        font-size: 0.875rem;
      }

      .invalid-feedback {
        font-size: 0.875rem;
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
      <div class="settings-card text-center">
        <h2>Account Settings</h2>
        <?php
            $profilePicDir = __DIR__ . '/uploads/profile_pictures/';
            $profilePicWeb = 'uploads/profile_pictures/';
            $picPathJpg = $profilePicDir . 'user_' . $user_id . '.jpg';
            $picPathPng = $profilePicDir . 'user_' . $user_id . '.png';
            $picWebJpg = $profilePicWeb . 'user_' . $user_id . '.jpg';
            $picWebPng = $profilePicWeb . 'user_' . $user_id . '.png';
            $defaultPic = 'https://png.pngtree.com/png-clipart/20230927/original/pngtree-man-avatar-image-for-profile-png-image_13001877.png';
            $profilePicUrl = file_exists($picPathJpg) ? $picWebJpg : (file_exists($picPathPng) ? $picWebPng : $defaultPic);
        ?>
        <img src="<?php echo htmlspecialchars($profilePicUrl); ?>" alt="Profile Picture" class="profile-picture mb-3" />
        <?php if (isset($_GET['edit']) && $_GET['edit'] == 1) { ?>
        <form id="accountSettingsForm" method="POST" enctype="multipart/form-data" autocomplete="off">
            <div class="mb-3">
                <label for="profile_picture" class="form-label">Change Profile Picture (JPG/PNG, max 2MB):</label>
                <input type="file" class="form-control" id="profile_picture" name="profile_picture" accept="image/jpeg, image/png" />
            </div>
            <?php if (!empty($update_error)) { ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($update_error); ?></div>
            <?php } ?>
            <?php if (isset($_GET['success'])) { ?>
              <div class="alert alert-success">Profile updated successfully.</div>
            <?php } ?>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="firstName" name="firstName" placeholder="First Name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required />
                <label for="firstName">First Name</label>
                <div class="invalid-feedback">Please provide your First name.</div>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="middleName" name="middleName" placeholder="Middle Name" value="<?php echo htmlspecialchars($user['middle_name']); ?>" />
                <label for="middleName">Middle Name</label>
            </div>
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="lastName" name="lastName" placeholder="Last Name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required />
                <label for="lastName">Last Name</label>
                <div class="invalid-feedback">Please provide your Last name.</div>
            </div>
            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="Email address" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly />
                <label for="email">Email address</label>
                <div class="invalid-feedback">Please provide a valid email address.</div>
            </div>
            <div class="form-floating mb-4">
                <input type="text" class="form-control" id="role" name="role" value="<?php echo htmlspecialchars(ucfirst($user['role'])); ?>" readonly />
                <label for="role">Position</label>
            </div>
            <button type="submit" class="btn btn-primary w-100">Save Changes</button>
            <a href="profile.php" class="btn btn-secondary w-100 mt-2">Cancel</a>
        </form>
        <?php } else { ?>
            <?php if (!empty($update_error)) { ?>
              <div class="alert alert-danger"><?php echo htmlspecialchars($update_error); ?></div>
            <?php } ?>
            <?php if (isset($_GET['success'])) { ?>
              <div class="alert alert-success">Profile updated successfully.</div>
            <?php } ?>
            <div class="mb-3 text-start">
                <label class="fw-bold">First Name:</label>
                <div><?php echo htmlspecialchars($user['first_name']); ?></div>
            </div>
            <div class="mb-3 text-start">
                <label class="fw-bold">Middle Name:</label>
                <div><?php echo htmlspecialchars($user['middle_name']); ?></div>
            </div>
            <div class="mb-3 text-start">
                <label class="fw-bold">Last Name:</label>
                <div><?php echo htmlspecialchars($user['last_name']); ?></div>
            </div>
            <div class="mb-3 text-start">
                <label class="fw-bold">Email:</label>
                <div><?php echo htmlspecialchars($user['email']); ?></div>
            </div>
            <div class="mb-4 text-start">
                <label class="fw-bold">Position:</label>
                <div><?php echo htmlspecialchars(ucfirst($user['role'])); ?></div>
            </div>
            <a href="profile.php?edit=1" class="btn btn-primary w-100">Edit Profile</a>
        <?php } ?>
      </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="js/side-topbar.js"></script>
    <script>
      // Removed JS that blocks form submission so PHP can process the update.
      // If you want to use SweetAlert, show it after page reload when ?success=1 is present.
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
    </script>
  </body>
</html>

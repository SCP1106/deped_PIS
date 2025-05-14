<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: ..auth/pages/login.php');
    exit;
}
require_once 'auth/core/database.php';
$user_id = $_SESSION['user_id'];
$user = null;
if ($stmt = $mysqli->prepare('SELECT u.first_name, u.middle_name, u.last_name, ur.role FROM users u LEFT JOIN user_roles ur ON u.id = ur.user_id WHERE u.id = ? LIMIT 1')) {
    $stmt->bind_param('i', $user_id);
    $stmt->execute();
    $stmt->bind_result($first_name, $middle_name, $last_name, $role);
    if ($stmt->fetch()) {
        $full_name = trim($first_name . ' ' . ($middle_name ? $middle_name[0] . '. ' : '') . $last_name);
        $user_role = $role ? ucfirst($role) : 'User';
    } else {
        $full_name = 'User';
        $user_role = 'User';
    }
    $stmt->close();
} else {
    $full_name = 'User';
    $user_role = 'User';
}
?>
<div class="sidebar">
  <a href="profile.php" style="text-decoration: none; color: inherit;">
    <div class="profile-section">
      <div class="d-flex align-items-center gap-3" style="cursor: pointer">
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
        <img
          src="<?php echo htmlspecialchars($profilePicUrl); ?>"
          alt="Profile"
          class="rounded-circle"
          width="60"
          height="60"
          style="border: 3px solid rgba(255, 255, 255, 0.3)"
        />
        <div>
          <h6 class="mb-0 fw-bold"><?php echo htmlspecialchars($full_name); ?></h6>
          <small><?php echo htmlspecialchars($user_role); ?></small>
        </div>
      </div>
    </div>
  </a>
  <nav class="mt-4">
    <a href="dashboard.php" class="nav-link" id="dashboardLink">
      <i class="bi bi-grid-1x2-fill"></i>
      Dashboard
    </a>
    <a href="map-db.php" class="nav-link" id="mapLink">
      <i class="bi bi-map"></i>
      Map
    </a>
    <a href="schoolInfo.php" class="nav-link" id="schoolInfoLink">
      <i class="bi bi-building"></i>
      School Information
    </a>
    <a href="teacherAnalysis.php" class="nav-link submenu-link" id="teacherLink">
      <i class="bi bi-person-workspace"></i>
      Teacher Analysis
    </a>
    <a href="schoolLandOwnership.php" class="nav-link" id="ownerLink">
      <i class="bi bi-people-fill"></i>
      School Land Ownership
    </a>
    <a href="crucialResources.php" class="nav-link submenu-link" id="resourcesLink">
      <i class="bi bi-box-seam"></i>
      Crucial Resources
    </a>
    <a href="groupPolicy.php" class="nav-link" id="groupLink">
      <i class="bi bi-shield-lock-fill"></i>
      Group Policy
    </a>
    <a href="auditTrail.php" class="nav-link" id="auditLink">
      <i class="bi bi-clock-history"></i>
      Audit Trail
    </a>
    <a href="register.php" class="nav-link" id="registrationLink">
      <i class="bi bi-person-plus-fill"></i>
      Registration
    </a>
    <a href="#" class="nav-link" id="logoutLink">
      <i class="bi bi-box-arrow-in-right"></i>
      Logout
    </a>
  </nav>
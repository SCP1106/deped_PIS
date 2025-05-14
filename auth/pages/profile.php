<?php
// Start session
session_start();

// Include database connection and session manager
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../core/session.php';

// Create session manager
$sessionManager = new SessionManager($mysqli);

// Validate session
$user = $sessionManager->validateSession();

// Redirect to login if not logged in
if (!$user) {
    header("Location: login.php");
    exit;
}

// Determine edit mode
$edit_mode = isset($_GET['edit']) && $_GET['edit'] == '1';

// CSRF protection
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$errors = [];
$success = false;

// Process form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    // Verify CSRF token
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        die("CSRF token validation failed");
    }

    // Handle profile picture upload
    if (isset(
        $_FILES['profile_picture']) && 
        $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK
    ) {
        $allowedTypes = ['image/jpeg' => 'jpg', 'image/png' => 'png'];
        $fileType = mime_content_type($_FILES['profile_picture']['tmp_name']);
        $maxSize = 2 * 1024 * 1024; // 2MB
        if (!array_key_exists($fileType, $allowedTypes)) {
            $errors[] = "Profile picture must be a JPG or PNG image.";
        } elseif ($_FILES['profile_picture']['size'] > $maxSize) {
            $errors[] = "Profile picture must not exceed 2MB.";
        } else {
            $ext = $allowedTypes[$fileType];
            $uploadDir = __DIR__ . '/../../uploads/profile_pictures/';
            $filename = 'user_' . $user['id'] . '.' . $ext;
            // Remove old files (jpg/png)
            foreach (["jpg", "png"] as $oldExt) {
                $oldFile = $uploadDir . 'user_' . $user['id'] . '.' . $oldExt;
                if (file_exists($oldFile)) unlink($oldFile);
            }
            move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadDir . $filename);
        }
    }

    // Validate form data
    $first_name = filter_input(INPUT_POST, "first_name", FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty($first_name)) {
        $errors[] = "First name is required";
    }

    $last_name = filter_input(INPUT_POST, "last_name", FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty($last_name)) {
        $errors[] = "Last name is required";
    }

    $middle_name = filter_input(INPUT_POST, "middle_name", FILTER_SANITIZE_SPECIAL_CHARS);

    $birthday = filter_input(INPUT_POST, "birthday", FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty($birthday)) {
        $errors[] = "Birthday is required";
    }

    $gender = filter_input(INPUT_POST, "gender", FILTER_SANITIZE_SPECIAL_CHARS);
    if (empty($gender)) {
        $errors[] = "Gender is required";
    }

    // Update user profile if no errors
    if (empty($errors)) {
        $stmt = $mysqli->prepare("UPDATE users SET first_name = ?, last_name = ?, middle_name = ?, birthday = ?, gender = ? WHERE id = ?");
        $stmt->bind_param("sssssi", $first_name, $last_name, $middle_name, $birthday, $gender, $user["id"]);
        $stmt->execute();
        $stmt->close();

        // Update user data
        $user["first_name"] = $first_name;
        $user["last_name"] = $last_name;
        $user["middle_name"] = $middle_name;
        $user["birthday"] = $birthday;
        $user["gender"] = $gender;

        $success = true;
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile | Your Website</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container">
            <a class="navbar-brand" href="index.php">Your Website</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Welcome, <?= htmlspecialchars($user["first_name"]) ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item active" href="profile.php">Profile</a></li>
                            <li><a class="dropdown-item" href="../../logout.php">Log out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4 py-md-5">
        <div class="row justify-content-center">
            <div class="col-12 col-md-10 col-lg-8">
                <h1 class="mb-4">Profile</h1>

                <?php if ($success): ?>
                    <div class="alert alert-success mb-4">
                        Your profile has been updated successfully!
                    </div>
                <?php endif; ?>

                <?php if (!empty($errors)): ?>
                    <div class="alert alert-danger mb-4">
                        <ul class="mb-0">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card shadow-lg border-0 rounded-4">
                    <div class="card-body p-3 p-sm-4 p-md-5">
                        <?php if (!$edit_mode): ?>
    <a href="profile.php?edit=1" class="btn btn-warning mb-3">Edit Profile</a>
<?php endif; ?>
<!-- Profile Picture Section -->
<?php
    $profilePicDir = __DIR__ . '/../../uploads/profile_pictures/';
    $profilePicWeb = '../../uploads/profile_pictures/';
    $userId = $user['id'];
    $picPathJpg = $profilePicDir . 'user_' . $userId . '.jpg';
    $picPathPng = $profilePicDir . 'user_' . $userId . '.png';
    $picWebJpg = $profilePicWeb . 'user_' . $userId . '.jpg';
    $picWebPng = $profilePicWeb . 'user_' . $userId . '.png';
    $defaultPic = 'https://png.pngtree.com/png-clipart/20230927/original/pngtree-man-avatar-image-for-profile-png-image_13001877.png';
    $profilePicUrl = file_exists($picPathJpg) ? $picWebJpg : (file_exists($picPathPng) ? $picWebPng : $defaultPic);
?>
<div class="mb-4 text-center">
    <img src="<?= htmlspecialchars($profilePicUrl) ?>" alt="Profile Picture" class="rounded-circle mb-2" style="width: 100px; height: 100px; object-fit: cover; border: 2px solid #e0e0e0;">
    <?php if ($edit_mode): ?>
        <div class="mt-2">
            <input type="file" name="profile_picture" accept="image/jpeg,image/png" class="form-control" style="max-width: 300px; margin: 0 auto;">
            <div class="form-text">Allowed types: JPG, PNG. Max size: 2MB.</div>
        </div>
    <?php endif; ?>
</div>
                        <form method="post" enctype="multipart/form-data" novalidate> <!-- Added enctype for file upload -->
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label for="first_name" class="form-label required">First Name</label>
                                    <input type="text"  id="first_name" name="first_name" value="<?= htmlspecialchars($user["first_name"]) ?>" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input type="text"  id="middle_name" name="middle_name" value="<?= htmlspecialchars($user["middle_name"] ?? "") ?>">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="last_name" class="form-label required">Last Name</label>
                                    <input type="text"  id="last_name" name="last_name" value="<?= htmlspecialchars($user["last_name"]) ?>" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="birthday" class="form-label required">Birthday</label>
                                    <input type="date"  id="birthday" name="birthday" value="<?= htmlspecialchars($user["birthday"]) ?>" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="gender" class="form-label required">Gender</label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="" disabled>Select gender</option>
                                        <option value="male" <?= $user["gender"] === "male" ? "selected" : "" ?>>Male</option>
                                        <option value="female" <?= $user["gender"] === "female" ? "selected" : "" ?>>Female</option>
                                        <option value="other" <?= $user["gender"] === "other" ? "selected" : "" ?>>Other</option>
                                        <option value="prefer_not_to_say" <?= $user["gender"] === "prefer_not_to_say" ? "selected" : "" ?>>Prefer not to say</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label for="email" class="form-label">Email Address</label>
                                <input type="email"  id="email" value="<?= htmlspecialchars($user["email"]) ?>" disabled>
                                <div class="form-text">Email address cannot be changed.</div>
                            </div>

                            

                            <!-- CSRF Protection -->
                            <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">

                            <div class="d-flex justify-content-between">
                                <a href="index.php" class="btn btn-outline-secondary">Back to Home</a>
                                <?php if ($edit_mode): ?>
                                    <button type="submit" class="btn btn-primary">Update Profile</button>
                                    <a href="profile.php" class="btn btn-secondary ms-2">Cancel</a>
                                <?php endif; ?>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="mt-4">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-body p-3 p-sm-4">
                            <h5 class="card-title">Change Password</h5>
                            <p class="card-text">Want to update your password?</p>
                            <a href="change-password.php" class="btn btn-outline-primary">Change Password</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
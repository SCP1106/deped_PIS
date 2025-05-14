<?php
session_start();

// Include database connection and session manager
require_once 'database.php';
require_once 'session.php';
require_once 'permissions.php';

// Create session manager
$sessionManager = new SessionManager($mysqli);

// Validate session
$user = $sessionManager->validateSession();

// Check if user is authenticated
if (!isset($_SESSION['authenticated']) || !$user) {
    header("Location: login-simple.php");
    exit();
}

// Check if 2FA is enabled but not verified for this session
if (!isset($_SESSION['2fa_verified'])) {
    $stmt = $mysqli->prepare("SELECT google2fa_enabled FROM users WHERE id = ?");
    $stmt->bind_param("i", $user['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $user_2fa = $result->fetch_assoc();

    if ($user_2fa && $user_2fa['google2fa_enabled'] == 1) {
        // Redirect to 2FA verification
        header("Location: simple-2fa-verify.php");
        exit();
    }
}

// Add roles to user data for permission checking
$user['roles'] = ['user']; // Default role

// Check if user has admin role
$stmt = $mysqli->prepare("SELECT role FROM user_roles WHERE user_id = ?");
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $user['roles'][] = $row['role'];
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | DepEd</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .main-content {
            background-color: #f9f9f9;
            min-height: 100vh;
        }

        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            transition: transform 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
        }

        .nav-link {
            color: #333;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .nav-link:hover,
        .nav-link.active {
            background-color: #2e8b57;
            color: white;
        }

        .btn-primary {
            background-color: #2e8b57;
            border-color: #2e8b57;
        }

        .btn-primary:hover {
            background-color: #246c46;
            border-color: #246c46;
        }

        .btn-outline-danger {
            color: #dc3545;
            border-color: #dc3545;
        }

        .btn-outline-danger:hover {
            background-color: #dc3545;
            color: white;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="dashboard-simple.php">
                <span style="color: #2e8b57; font-weight: bold;">DepEd</span> Dashboard
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <?php if (hasRole($user, ['admin', 'manager'])): ?>
                        <li class="nav-item">
                            <a class="nav-link" href="admin/dashboard.php">Admin Dashboard</a>
                        </li>
                    <?php endif; ?>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            Welcome, <?= htmlspecialchars($user["first_name"]) ?>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="profile.php">Profile</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="../logout.php">Log out</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="main-content py-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">User Profile</h5>
                            <p class="card-text">
                                <strong>Name:</strong> <?= htmlspecialchars($user["first_name"] . " " . $user["last_name"]) ?><br>
                                <strong>Email:</strong> <?= htmlspecialchars($user["email"]) ?><br>
                                <strong>Roles:</strong> <?= htmlspecialchars(implode(", ", $user["roles"])) ?>
                            </p>
                            <a href="profile.php" class="btn btn-primary">Edit Profile</a>
                        </div>
                    </div>

                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Quick Links</h5>
                            <div class="list-group list-group-flush">
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="bi bi-house me-2"></i> Home
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="bi bi-file-earmark-text me-2"></i> Documents
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="bi bi-calendar-event me-2"></i> Calendar
                                </a>
                                <a href="#" class="list-group-item list-group-item-action">
                                    <i class="bi bi-chat-dots me-2"></i> Messages
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-8">
                    <div class="card shadow-sm mb-4">
                        <div class="card-body">
                            <h5 class="card-title">Welcome to Your Dashboard</h5>
                            <p class="card-text">
                                You are now logged in with our secure two-factor authentication system.
                            </p>

                            <?php if (hasRole($user, 'admin')): ?>
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Admin Access</h6>
                                    <p class="mb-0">You have admin privileges. You can manage users, roles, and permissions.</p>
                                </div>
                            <?php elseif (hasRole($user, 'manager')): ?>
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">Manager Access</h6>
                                    <p class="mb-0">You have manager privileges. You can view reports and manage content.</p>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info">
                                    <h6 class="alert-heading">User Access</h6>
                                    <p class="mb-0">You have basic user privileges. You can view the dashboard and edit your profile.</p>
                                </div>
                            <?php endif; ?>

                            <hr>

                            <h6>Your Permissions:</h6>
                            <ul class="list-group mb-3">
                                <?php
                                $permissions = ['view_dashboard', 'manage_users', 'edit_profile', 'view_reports', 'manage_content'];
                                foreach ($permissions as $permission):
                                    $hasPermission = hasPermission($user, $permission, $sessionManager);
                                ?>
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <?= htmlspecialchars($permission) ?>
                                        <?php if ($hasPermission): ?>
                                            <span class="badge bg-success rounded-pill">
                                                <i class="bi bi-check-lg"></i>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill">
                                                <i class="bi bi-x-lg"></i>
                                            </span>
                                        <?php endif; ?>
                                    </li>
                                <?php endforeach; ?>
                            </ul>

                            <a href="../logout.php" class="btn btn-outline-danger">Log out</a>
                        </div>
                    </div>

                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title">Recent Activity</h5>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Date</th>
                                            <th>Activity</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime('-1 day')) ?></td>
                                            <td>Login</td>
                                            <td><span class="badge bg-success">Success</span></td>
                                        </tr>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime('-3 day')) ?></td>
                                            <td>Profile Update</td>
                                            <td><span class="badge bg-success">Success</span></td>
                                        </tr>
                                        <tr>
                                            <td><?= date('M d, Y', strtotime('-5 day')) ?></td>
                                            <td>Password Change</td>
                                            <td><span class="badge bg-success">Success</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
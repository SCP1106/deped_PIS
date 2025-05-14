<?php
session_start();
require_once __DIR__ . '/../core/database.php';
require_once __DIR__ . '/../core/permissions.php';
require_once __DIR__ . '/../core/session.php';

// Initialize SessionManager
$sessionManager = new SessionManager($mysqli);

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Load user data with roles and permissions
$user = $sessionManager->loadUserData($_SESSION['user_id']);

if (!$user) {
    // Invalid user session
    session_destroy();
    header('Location: login.php');
    exit;
}

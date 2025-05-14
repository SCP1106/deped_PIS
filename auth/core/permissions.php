<?php

/**
 * Check if a user has a specific role
 * 
 * @param array $user User data
 * @param string|array $role Role or roles to check
 * @return bool True if user has the role, false otherwise
 */
function hasRole($user, $role)
{
    // If user has no roles defined, return false
    if (!isset($user['roles'])) {
        return false;
    }

    // If checking for multiple roles
    if (is_array($role)) {
        foreach ($role as $r) {
            if (in_array($r, $user['roles'])) {
                return true;
            }
        }
        return false;
    }

    // Checking for a single role
    return in_array($role, $user['roles']);
}

/**
 * Check if a user has a specific permission
 *
 * @param array $user User data containing ['id']
 * @param string $permissionName Permission name to check
 * @param mysqli $mysqli Database connection object
 * @return bool True if user has the permission, false otherwise
 */
function hasPermission($user, $permissionName, $mysqli)
{
    if (!isset($user['id'])) {
        error_log("User ID not found in user data for permission check.");
        return false;
    }

    $userId = $user['id'];

    $sql = "SELECT COUNT(*) as count
            FROM user_roles ur
            JOIN role_permissions rp ON ur.role_id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.permission_id
            WHERE ur.user_id = ? AND p.permission_name = ?";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        error_log("SQL prepare error (hasPermission): " . $mysqli->error);
        return false;
    }

    $stmt->bind_param("is", $userId, $permissionName);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    return ($row && $row['count'] > 0);
}

/**
 * Get all roles for a user
 *
 * @param int $userId User ID
 * @param mysqli $mysqli Database connection
 * @return array Array of role names
 */
function getUserRoles($userId, $mysqli)
{
    $roles = [];

    $sql = "SELECT r.role_name
            FROM user_roles ur
            JOIN roles r ON ur.role_id = r.role_id
            WHERE ur.user_id = ?";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        error_log("SQL prepare error (getUserRoles): " . $mysqli->error);
        return $roles;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $roles[] = $row['role_name'];
    }

    $stmt->close();
    return $roles;
}

/**
 * Get all permissions for a user
 *
 * @param int $userId User ID
 * @param mysqli $mysqli Database connection
 * @return array Array of permission names
 */
function getUserPermissions($userId, $mysqli)
{
    $permissions = [];

    $sql = "SELECT DISTINCT p.permission_name
            FROM user_roles ur
            JOIN role_permissions rp ON ur.role_id = rp.role_id
            JOIN permissions p ON rp.permission_id = p.permission_id
            WHERE ur.user_id = ?";

    $stmt = $mysqli->prepare($sql);
    if (!$stmt) {
        error_log("SQL prepare error (getUserPermissions): " . $mysqli->error);
        return $permissions;
    }

    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $permissions[] = $row['permission_name'];
    }

    $stmt->close();
    return $permissions;
}

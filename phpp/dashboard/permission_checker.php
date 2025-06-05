<?php
/**
 * Permission Checker Utility
 * Use this to check if a user has permission to access a specific page
 */

function hasPagePermission($user_role_id, $page_name, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT {$page_name} FROM role_policy WHERE role_id = ?");
        $stmt->execute([$user_role_id]);
        $result = $stmt->fetch();
        
        if ($result && isset($result[$page_name])) {
            return $result[$page_name] == 1;
        }
        
        return false; // Default to no permission if not found
    } catch (PDOException $e) {
        error_log("Permission check error: " . $e->getMessage());
        return false;
    }
}

/**
 * Check multiple permissions at once
 */
function getUserPermissions($user_role_id, $pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM role_policy WHERE role_id = ?");
        $stmt->execute([$user_role_id]);
        $permissions = $stmt->fetch();
        
        if ($permissions) {
            // Remove non-permission fields
            unset($permissions['id'], $permissions['role_id'], $permissions['created_at'], $permissions['updated_at']);
            return $permissions;
        }
        
        return [
            'dashboard_map' => 0,
            'school_information' => 0,
            'teacher_analysis' => 0,
            'school_land_ownership' => 0,
            'crucial_resources' => 0,
            'audit_trail' => 0,
            'registration' => 0
        ];
    } catch (PDOException $e) {
        error_log("Permission fetch error: " . $e->getMessage());
        return [];
    }
}

/**
 * Redirect if user doesn't have permission
 */
function requirePermission($user_role_id, $page_name, $pdo, $redirect_url = 'access_denied.php') {
    if (!hasPagePermission($user_role_id, $page_name, $pdo)) {
        header("Location: $redirect_url");
        exit;
    }
}
?>

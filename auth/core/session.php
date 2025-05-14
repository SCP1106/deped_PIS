<?php

/**
 * Session management class
 */
class SessionManager
{
    private $mysqli;

    /**
     * Constructor
     * 
     * @param mysqli $mysqli Database connection
     */
    public function __construct($mysqli)
    {
        $this->mysqli = $mysqli;
    }

    /**
     * Validate session
     * 
     * @return array|null User data if session is valid, null otherwise
     */
    public function validateSession()
    {
        if (!isset($_COOKIE['session_id'])) {
            return null;
        }

        $session_id = $_COOKIE['session_id'];

        // Get session from database
        $stmt = $this->mysqli->prepare("SELECT s.*, u.* FROM sessions s 
                                       JOIN users u ON s.user_id = u.id 
                                       WHERE s.id = ? AND s.expires_at > NOW()");
        $stmt->bind_param("s", $session_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $session = $result->fetch_assoc();
        $stmt->close();

        if (!$session) {
            // Invalid or expired session
            $this->destroySession();
            return null;
        }

        // Validate IP and user agent for security
        if ($session['ip_address'] !== $_SERVER['REMOTE_ADDR'] || $session['user_agent'] !== $_SERVER['HTTP_USER_AGENT']) {
            // Potential session hijacking attempt
            $this->destroySession();
            return null;
        }

        // Extend session if it's about to expire
        $expires_at = strtotime($session['expires_at']);
        if ($expires_at - time() < 3600) { // Less than 1 hour left
            $this->extendSession($session_id);
        }

        return [
            'id' => $session['user_id'],
            'first_name' => $session['first_name'],
            'last_name' => $session['last_name'],
            'email' => $session['email']
        ];
    }

    /**
     * Create a new session
     * 
     * @param int $user_id User ID
     * @return void
     * @throws Exception If session creation fails
     */
    public function createSession($user_id)
    {
        $session_id = session_id();
        $user_agent = $_SERVER['HTTP_USER_AGENT'];
        $ip_address = $_SERVER['REMOTE_ADDR'];
        $expires_at = date('Y-m-d H:i:s', time() + 86400); // 24 hours

        // Use the same table name as in validateSession (sessions)
        $stmt = $this->mysqli->prepare("INSERT INTO sessions 
            (id, user_id, user_agent, ip_address, expires_at) 
            VALUES (?, ?, ?, ?, ?)");

        if (!$stmt) {
            throw new Exception("Session creation failed: " . $this->mysqli->error);
        }

        $stmt->bind_param("sisss", $session_id, $user_id, $user_agent, $ip_address, $expires_at);
        if (!$stmt->execute()) {
            throw new Exception("Failed to create session: " . $stmt->error);
        }
        $stmt->close();

        // Set the session cookie
        setcookie("session_id", $session_id, time() + 86400, "/", "", true, true);
    }

    /**
     * Extend session
     * 
     * @param string $session_id Session ID
     * @return void
     */
    private function extendSession($session_id)
    {
        $expires_at = date('Y-m-d H:i:s', time() + 86400); // 24 hours

        $stmt = $this->mysqli->prepare("UPDATE sessions SET expires_at = ? WHERE id = ?");
        $stmt->bind_param("ss", $expires_at, $session_id);
        $stmt->execute();
        $stmt->close();

        // Update cookie
        setcookie("session_id", $session_id, time() + 86400, "/", "", true, true);
    }

    /**
     * Destroy session
     * 
     * @return void
     */
    public function destroySession()
    {
        if (isset($_COOKIE['session_id'])) {
            $session_id = $_COOKIE['session_id'];

            // Delete session from database
            $stmt = $this->mysqli->prepare("DELETE FROM sessions WHERE id = ?");
            $stmt->bind_param("s", $session_id);
            $stmt->execute();
            $stmt->close();

            // Delete cookie
            setcookie("session_id", "", time() - 3600, "/", "", true, true);
        }

        // Clear PHP session
        session_unset();
        session_destroy();
    }

    /**
     * Load user data including roles and permissions
     * 
     * @param int $user_id User ID
     * @return array User data with roles and permissions
     */
    public function loadUserData($user_id)
    {
        $userData = [];

        // Get basic user info
        $stmt = $this->mysqli->prepare("SELECT id, first_name, last_name, email FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $userData = $result->fetch_assoc();
        $stmt->close();

        if ($userData) {
            // Add roles
            $userData['roles'] = $this->getUserRoles($user_id);

            // Add permissions
            $userData['permissions'] = $this->getUserPermissions($user_id);
        }

        return $userData;
    }

    /**
     * Get user roles
     * 
     * @param int $user_id User ID
     * @return array User roles
     */
    private function getUserRoles($user_id)
    {
        $roles = [];

        $stmt = $this->mysqli->prepare("SELECT role FROM user_roles WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();

        while ($row = $result->fetch_assoc()) {
            $roles[] = $row['role'];
        }
        $stmt->close();

        return $roles;
    }

    /**
     * Get user permissions
     * 
     * @param int $user_id User ID
     * @return array User permissions
     */
    private function getUserPermissions($user_id)
    {
        $permissions = [];

        // Check if permissions table exists
        $result = $this->mysqli->query("SHOW TABLES LIKE 'permissions'");
        if ($result->num_rows > 0) {
            // Get permissions based on user roles
            $stmt = $this->mysqli->prepare("
                SELECT DISTINCT p.permission_name 
                FROM permissions p
                JOIN role_permissions rp ON p.id = rp.permission_id
                JOIN user_roles ur ON rp.role_id = ur.role
                WHERE ur.user_id = ?
            ");

            if ($stmt) {
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                while ($row = $result->fetch_assoc()) {
                    $permissions[] = $row['permission_name'];
                }
                $stmt->close();
            }
        }

        return $permissions;
    }
}

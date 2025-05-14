<?php

/**
 * Rate limiter class to prevent brute force attacks
 */
class RateLimiter
{
    private $mysqli;
    private $ip;
    private $email;
    private $maxAttempts;
    private $timeWindow;

    /**
     * Constructor
     * 
     * @param mysqli $mysqli Database connection
     * @param string $ip IP address
     * @param string $email Email address (optional)
     * @param int $maxAttempts Maximum number of attempts allowed
     * @param int $timeWindow Time window in seconds
     */
    public function __construct($mysqli, $ip, $email = null, $maxAttempts = 5, $timeWindow = 300)
    {
        $this->mysqli = $mysqli;
        $this->ip = $ip;
        $this->email = $email;
        $this->maxAttempts = $maxAttempts;
        $this->timeWindow = $timeWindow;
    }

    /**
     * Check if the user is rate limited
     * 
     * @return bool True if rate limited, false otherwise
     */
    public function isRateLimited()
    {
        $this->cleanupOldAttempts();

        $count = $this->getAttemptCount();

        return $count >= $this->maxAttempts;
    }

    /**
     * Record a login attempt
     * 
     * @return void
     */
    public function recordAttempt()
    {
        $stmt = $this->mysqli->prepare("INSERT INTO login_attempts (email, ip_address) VALUES (?, ?)");
        $stmt->bind_param("ss", $this->email, $this->ip);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Get the number of attempts within the time window
     * 
     * @return int Number of attempts
     */
    private function getAttemptCount()
    {
        $timeAgo = date('Y-m-d H:i:s', time() - $this->timeWindow);

        if ($this->email) {
            // If email is provided, count attempts for both IP and email
            $stmt = $this->mysqli->prepare("SELECT COUNT(*) FROM login_attempts 
                                           WHERE (ip_address = ? OR email = ?) 
                                           AND attempt_time > ?");
            $stmt->bind_param("sss", $this->ip, $this->email, $timeAgo);
        } else {
            // Otherwise, just count attempts for IP
            $stmt = $this->mysqli->prepare("SELECT COUNT(*) FROM login_attempts 
                                           WHERE ip_address = ? 
                                           AND attempt_time > ?");
            $stmt->bind_param("ss", $this->ip, $timeAgo);
        }

        $stmt->execute();
        $stmt->bind_result($count);
        $stmt->fetch();
        $stmt->close();

        return $count;
    }

    /**
     * Clean up old attempts
     * 
     * @return void
     */
    private function cleanupOldAttempts()
    {
        // Delete attempts older than the time window
        $timeAgo = date('Y-m-d H:i:s', time() - $this->timeWindow);

        $stmt = $this->mysqli->prepare("DELETE FROM login_attempts WHERE attempt_time < ?");
        $stmt->bind_param("s", $timeAgo);
        $stmt->execute();
        $stmt->close();
    }

    /**
     * Get remaining time until rate limit expires
     * 
     * @return int Seconds until rate limit expires
     */
    public function getTimeUntilUnlocked()
    {
        $timeAgo = date('Y-m-d H:i:s', time() - $this->timeWindow);

        if ($this->email) {
            $stmt = $this->mysqli->prepare("SELECT attempt_time FROM login_attempts 
                                           WHERE (ip_address = ? OR email = ?) 
                                           AND attempt_time > ? 
                                           ORDER BY attempt_time ASC 
                                           LIMIT 1");
            $stmt->bind_param("sss", $this->ip, $this->email, $timeAgo);
        } else {
            $stmt = $this->mysqli->prepare("SELECT attempt_time FROM login_attempts 
                                           WHERE ip_address = ? 
                                           AND attempt_time > ? 
                                           ORDER BY attempt_time ASC 
                                           LIMIT 1");
            $stmt->bind_param("ss", $this->ip, $timeAgo);
        }

        $stmt->execute();
        $stmt->bind_result($oldestAttempt);
        $stmt->fetch();
        $stmt->close();

        if (!$oldestAttempt) {
            return 0;
        }

        $oldestTime = strtotime($oldestAttempt);
        $unlockTime = $oldestTime + $this->timeWindow;
        $remainingTime = $unlockTime - time();

        return max(0, $remainingTime);
    }
}

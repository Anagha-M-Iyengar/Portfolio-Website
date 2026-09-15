<?php
/**
 * lockout.php
 * Tracks failed login attempts in MySQL (login_attempts table)
 * instead of the old lockouts.csv file.
 * Included by login.php.
 */

require_once __DIR__ . '/db_connect.php';

define('MAX_ATTEMPTS', 3);
define('LOCK_SECONDS', 30);

/**
 * Check if an IP is currently locked out.
 * $remaining is filled with seconds left in the lock (passed by reference).
 */
function lockout_is_locked(string $ip, int &$remaining = 0): bool {
    global $conn;

    $stmt = $conn->prepare("SELECT lock_until FROM login_attempts WHERE ip_address = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $row    = $result->fetch_assoc();
    $stmt->close();

    if (!$row) return false;  // No record for this IP — not locked

    if ($row['lock_until'] > time()) {
        $remaining = $row['lock_until'] - time();
        return true;
    }
    return false;
}

/**
 * Increment the attempt counter for an IP.
 * Returns the new total number of attempts.
 */
function lockout_increment(string $ip): int {
    global $conn;

    // If a previous lock has EXPIRED, reset the counter first
    $stmt = $conn->prepare(
        "INSERT INTO login_attempts (ip_address, attempts, lock_until)
         VALUES (?, 1, 0)
         ON DUPLICATE KEY UPDATE
           attempts   = IF(lock_until > 0 AND lock_until <= UNIX_TIMESTAMP(), 1, attempts + 1),
           lock_until = IF(lock_until > 0 AND lock_until <= UNIX_TIMESTAMP(), 0, lock_until)"
    );
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();

    // Read back the current attempt count
    $stmt = $conn->prepare("SELECT attempts FROM login_attempts WHERE ip_address = ?");
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    $row    = $result->fetch_assoc();
    $stmt->close();

    return (int)($row['attempts'] ?? 1);
}

/**
 * Lock an IP address for LOCK_SECONDS seconds.
 */
function lockout_lock(string $ip): void {
    global $conn;
    $lockUntil  = time() + LOCK_SECONDS;
    $maxAttempts = MAX_ATTEMPTS;   // assign constant to variable — bind_param needs a variable, not a constant

    $stmt = $conn->prepare(
        "INSERT INTO login_attempts (ip_address, attempts, lock_until)
         VALUES (?, ?, ?)
         ON DUPLICATE KEY UPDATE lock_until = ?"
    );
    $stmt->bind_param("siii", $ip, $maxAttempts, $lockUntil, $lockUntil);
    $stmt->execute();
    $stmt->close();
}

/**
 * Reset the attempt counter after a successful login.
 */
function lockout_reset(string $ip): void {
    global $conn;

    $stmt = $conn->prepare(
        "UPDATE login_attempts SET attempts = 0, lock_until = 0 WHERE ip_address = ?"
    );
    $stmt->bind_param("s", $ip);
    $stmt->execute();
    $stmt->close();
}
?>

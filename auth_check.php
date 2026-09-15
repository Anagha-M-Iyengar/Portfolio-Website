<?php
/**
 * auth_check.php
 * Included as the very first line of index.php.
 * 1. Sets 30-second session timeout
 * 2. Enforces intro gate
 * 3. Enforces login
 * 4. Auto-logs out on inactivity / server stop
 */

ini_set('session.gc_maxlifetime', 30);
session_set_cookie_params([
    'lifetime' => 30,
    'path'     => '/',
    'secure'   => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);
session_start();

// Manual timeout: if last_activity was more than 30s ago → force logout
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > 30) {
    session_unset();
    session_destroy();
    header("Location: login.php?timeout=1");
    exit;
}
$_SESSION['last_activity'] = time();

// Must have visited intro.php first
if (!isset($_SESSION['visited_intro']) || $_SESSION['visited_intro'] !== true) {
    header("Location: intro.php");
    exit;
}

// Must be logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: login.php");
    exit;
}
?>

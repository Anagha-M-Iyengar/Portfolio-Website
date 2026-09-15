<?php
/**
 * session_ping.php
 * Called every 20 seconds by JavaScript on index.php.
 * Refreshes last_activity so the session stays alive while page is open.
 * When browser closes / server stops → pings stop → session expires after 30s.
 */
ini_set('session.gc_maxlifetime', 30);
session_set_cookie_params(['lifetime'=>30,'path'=>'/','httponly'=>true,'samesite'=>'Lax']);
session_start();

header('Content-Type: application/json');

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status' => 'expired']);
    exit;
}

$_SESSION['last_activity'] = time();
echo json_encode(['status' => 'ok', 'username' => $_SESSION['username'] ?? '']);
exit;
?>

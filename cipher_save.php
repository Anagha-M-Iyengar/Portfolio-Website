<?php
/**
 * cipher_save.php
 * Called by tools.php (via fetch) after every encrypt / decrypt.
 * Saves the action, input, output, rails and session info to cipher_logs.
 *
 * Expected POST fields:
 *   action      — 'encrypt' or 'decrypt'
 *   rails       — integer 1–100
 *   input_text  — sanitised input message
 *   output_text — resulting cipher / plain text
 *   source      — 'typed', 'txt_upload', or 'pdf_upload'
 */

session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';

// Only accept POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status' => 'error', 'message' => 'POST only.']);
    exit;
}

// ── Read & validate inputs ─────────────────────────────────────────────────
$action      = $_POST['action']      ?? '';
$rails       = (int)($_POST['rails'] ?? 0);
$input_text  = trim($_POST['input_text']  ?? '');
$output_text = trim($_POST['output_text'] ?? '');
$source      = $_POST['source']      ?? 'typed';

$allowed_actions  = ['encrypt', 'decrypt'];
$allowed_sources  = ['typed', 'txt_upload', 'pdf_upload'];

if (!in_array($action, $allowed_actions)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid action.']); exit;
}
if ($rails < 1 || $rails > 100) {
    echo json_encode(['status' => 'error', 'message' => 'Rails out of range.']); exit;
}
if ($input_text === '' || $output_text === '') {
    echo json_encode(['status' => 'error', 'message' => 'Empty text.']); exit;
}
if (!in_array($source, $allowed_sources)) {
    $source = 'typed';
}

// ── Sanitise text — only keep A-Za-z0-9 and spaces ────────────────────────
$input_text  = preg_replace('/[^A-Za-z0-9 ]/', '', $input_text);
$output_text = preg_replace('/[^A-Za-z0-9 ]/', '', $output_text);

// ── Session info ───────────────────────────────────────────────────────────
$session_user = isset($_SESSION['username']) ? $_SESSION['username'] : 'guest';
$ip_address   = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$char_count   = strlen($input_text);

// ── Insert into cipher_logs ───────────────────────────────────────────────
$stmt = $conn->prepare(
    "INSERT INTO cipher_logs
        (session_user, ip_address, action, rails, input_text, output_text, char_count, source)
     VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param(
    "sssissis",
    $session_user,
    $ip_address,
    $action,
    $rails,
    $input_text,
    $output_text,
    $char_count,
    $source
);

if ($stmt->execute()) {
    $log_id = $conn->insert_id;
    $stmt->close();
    $conn->close();
    echo json_encode([
        'status' => 'ok',
        'log_id' => $log_id,
        'message' => 'Saved successfully.'
    ]);
} else {
    $err = $conn->error;
    $stmt->close();
    $conn->close();
    echo json_encode(['status' => 'error', 'message' => $err]);
}
exit;
?>

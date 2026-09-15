<?php
/**
 * rsa_log.php
 * Saves RSA/AES encrypt/decrypt activity to the rsa_logs table.
 * Called silently by rsa_aes.php after every operation.
 *
 * POST fields:
 *   action      — 'encrypt' or 'decrypt'
 *   filename    — the output filename (e.g. encrypted_dante.bin)
 *   keyname     — name of the key file used
 *   char_count  — size of input in bytes
 */

session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'POST only.']); exit;
}

$action      = in_array($_POST['action']??'', ['encrypt','decrypt']) ? $_POST['action'] : 'encrypt';
$filename    = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $_POST['filename']  ?? '');
$keyname     = preg_replace('/[^A-Za-z0-9_\-\.]/', '', $_POST['keyname']   ?? '');
$char_count  = (int)($_POST['char_count'] ?? 0);
$session_user = $_SESSION['username'] ?? 'guest';
$ip_address  = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';

$stmt = $conn->prepare(
    "INSERT INTO rsa_logs
        (session_user, ip_address, action, output_filename, key_filename, char_count)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("ssssi", $session_user, $ip_address, $action, $filename, $keyname, $char_count);

// Note: bind_param string has 6 chars for 6 params
$stmt = $conn->prepare(
    "INSERT INTO rsa_logs
        (session_user, ip_address, action, output_filename, key_filename, char_count)
     VALUES (?, ?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssssi", $session_user, $ip_address, $action, $filename, $keyname, $char_count);

if ($stmt->execute()) {
    $log_id = $conn->insert_id;
    $stmt->close(); $conn->close();
    echo json_encode(['status'=>'ok','log_id'=>$log_id]);
} else {
    $err = $conn->error;
    $stmt->close(); $conn->close();
    echo json_encode(['status'=>'error','message'=>$err]);
}
exit;
?>

<?php
/**
 * sig_verify.php — Digital Signature: Verify a file's integrity
 *
 * HOW VERIFICATION WORKS:
 * 1. Receiver has: original file + .sig signature + sender's public key
 * 2. OpenSSL decrypts the .sig using the public key → recovers original hash
 * 3. OpenSSL recomputes SHA-256 hash of the received file independently
 * 4. If both hashes match → AUTHENTIC (file unchanged, signed by correct person)
 * 5. If hashes differ → TAMPERED or wrong key
 *
 * OpenSSL command equivalent:
 *   openssl dgst -sha256 -verify public.key -signature file.sig file.txt
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['visited_intro']) || $_SESSION['visited_intro'] !== true) {
    echo json_encode(['status'=>'error','message'=>'Access denied.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'POST only.']); exit;
}

$OPENSSL = (strtoupper(substr(PHP_OS,0,3))==='WIN')
    ? '"C:\\xampp\\apache\\bin\\openssl.exe"'
    : 'openssl';

$fileUpload   = $_FILES['file']      ?? null;
$sigUpload    = $_FILES['signature'] ?? null;
$pubkeyUpload = $_FILES['pubkey']    ?? null;

if (!$fileUpload   || $fileUpload['error']   !== UPLOAD_ERR_OK) { echo json_encode(['status'=>'error','message'=>'File upload failed.']); exit; }
if (!$sigUpload    || $sigUpload['error']    !== UPLOAD_ERR_OK) { echo json_encode(['status'=>'error','message'=>'Signature upload failed.']); exit; }
if (!$pubkeyUpload || $pubkeyUpload['error'] !== UPLOAD_ERR_OK) { echo json_encode(['status'=>'error','message'=>'Public key upload failed.']); exit; }
if (!str_ends_with(strtolower($sigUpload['name']), '.sig'))  { echo json_encode(['status'=>'error','message'=>'Signature must be a .sig file.']); exit; }
if (!str_ends_with(strtolower($pubkeyUpload['name']), '.key')){ echo json_encode(['status'=>'error','message'=>'Key must be a .key file.']); exit; }

$tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'pone_ver_' . session_id();
if (!is_dir($tmpDir)) mkdir($tmpDir, 0750, true);

$filePath   = $tmpDir . DIRECTORY_SEPARATOR . 'input_file';
$sigPath    = $tmpDir . DIRECTORY_SEPARATOR . 'signature.sig';
$pubkeyPath = $tmpDir . DIRECTORY_SEPARATOR . 'pubkey.key';

move_uploaded_file($fileUpload['tmp_name'],   $filePath);
move_uploaded_file($sigUpload['tmp_name'],    $sigPath);
move_uploaded_file($pubkeyUpload['tmp_name'], $pubkeyPath);

// Recompute SHA-256 hash of the received file
$hash = hash_file('sha256', $filePath);

// Verify: openssl dgst -sha256 -verify public.key -signature file.sig input_file
$verCmd = $OPENSSL . ' dgst -sha256 -verify ' . escapeshellarg($pubkeyPath)
        . ' -signature ' . escapeshellarg($sigPath)
        . ' '            . escapeshellarg($filePath)
        . ' 2>&1';

exec($verCmd, $cmdOut, $retCode);
$output = implode(' ', $cmdOut);

// OpenSSL prints "Verified OK" on success
$verified = ($retCode === 0 && strpos($output, 'Verified OK') !== false);

// Save to DB
require_once __DIR__ . '/db_connect.php';
$sessionUser = $_SESSION['username'] ?? 'guest';
$ip          = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$action      = 'verify';
$result      = $verified ? 'valid' : 'invalid';
$stmt = $conn->prepare("INSERT INTO signature_logs (session_user,ip_address,action,file_hash,key_filename,verify_result) VALUES (?,?,?,?,?,?)");
$stmt->bind_param("ssssss", $sessionUser, $ip, $action, $hash, $pubkeyUpload['name'], $result);
$stmt->execute(); $stmt->close(); $conn->close();

cleanupDir($tmpDir);

if ($verified) {
    echo json_encode(['status'=>'ok', 'hash'=>$hash,
        'message'=>'Verified OK — file is authentic and unmodified.']);
} else {
    echo json_encode(['status'=>'error', 'hash'=>$hash,
        'message'=>'Verification FAILED — file may have been tampered with, '
                 . 'or this is not the correct public key. Output: '.$output]);
}

function cleanupDir($dir){
    if(!is_dir($dir)) return;
    foreach(glob($dir.DIRECTORY_SEPARATOR.'*') as $f){ if(is_file($f)) unlink($f); }
    rmdir($dir);
}
exit;
?>

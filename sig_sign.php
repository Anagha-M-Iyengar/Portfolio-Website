<?php
/**
 * sig_sign.php — Digital Signature: Sign a file
 *
 * HOW DIGITAL SIGNATURES WORK:
 * 1. Compute SHA-256 hash of the file (a unique 256-bit fingerprint)
 * 2. Encrypt that hash with your RSA PRIVATE key → this is the "signature"
 * 3. Anyone with your PUBLIC key can decrypt the signature to get the hash
 * 4. They also compute the hash of the received file independently
 * 5. If both hashes match → file is authentic and unmodified
 *
 * OpenSSL command equivalent:
 *   openssl dgst -sha256 -sign private.key -out file.sig file.txt
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['visited_intro']) || $_SESSION['visited_intro'] !== true) {
    echo json_encode(['status'=>'error','message'=>'Access denied.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'POST only.']); exit;
}

// OpenSSL path
$OPENSSL = (strtoupper(substr(PHP_OS,0,3))==='WIN')
    ? '"C:\\xampp\\apache\\bin\\openssl.exe"'
    : 'openssl';

$fileUpload    = $_FILES['file']    ?? null;
$privkeyUpload = $_FILES['privkey'] ?? null;
$passphrase    = preg_replace('/[^A-Za-z0-9@#_\-!.]/', '', $_POST['passphrase'] ?? '');

if (!$fileUpload    || $fileUpload['error']    !== UPLOAD_ERR_OK) { echo json_encode(['status'=>'error','message'=>'File upload failed.']); exit; }
if (!$privkeyUpload || $privkeyUpload['error'] !== UPLOAD_ERR_OK) { echo json_encode(['status'=>'error','message'=>'Private key upload failed.']); exit; }
if (!str_ends_with(strtolower($privkeyUpload['name']), '.key')) { echo json_encode(['status'=>'error','message'=>'Only .key files allowed.']); exit; }

$tmpDir   = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'pone_sig_' . session_id();
if (!is_dir($tmpDir)) mkdir($tmpDir, 0750, true);

$filePath    = $tmpDir . DIRECTORY_SEPARATOR . 'input_file';
$privkeyPath = $tmpDir . DIRECTORY_SEPARATOR . 'privkey.key';
$sigPath     = $tmpDir . DIRECTORY_SEPARATOR . 'signature.sig';

move_uploaded_file($fileUpload['tmp_name'],    $filePath);
move_uploaded_file($privkeyUpload['tmp_name'], $privkeyPath);

// Passphrase argument
$passinArg = $passphrase !== '' ? ' -passin pass:' . escapeshellarg($passphrase) : '';

// Step 1: Compute SHA-256 hash of the file (for display)
$hash = hash_file('sha256', $filePath);

// Step 2: Sign the file — creates a binary signature file
// openssl dgst -sha256 -sign private.key -passin pass:xyz -out file.sig input_file
$signCmd = $OPENSSL . ' dgst -sha256 -sign ' . escapeshellarg($privkeyPath)
         . $passinArg
         . ' -out ' . escapeshellarg($sigPath)
         . ' '      . escapeshellarg($filePath)
         . ' 2>&1';

exec($signCmd, $cmdOut, $retCode);

if ($retCode !== 0 || !file_exists($sigPath) || filesize($sigPath) === 0) {
    cleanupDir($tmpDir);
    echo json_encode(['status'=>'error',
        'message'=>'Signing failed: '.implode(' ',$cmdOut)
                 .' | Check passphrase and ensure private key is valid.']); exit;
}

// Read signature and return as base64
$sigData = file_get_contents($sigPath);

// Save to DB
require_once __DIR__ . '/db_connect.php';
$sessionUser = $_SESSION['username'] ?? 'guest';
$ip          = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$stmt = $conn->prepare("INSERT INTO signature_logs (session_user,ip_address,action,file_hash,key_filename) VALUES (?,?,?,?,?)");
$action = 'sign';
$stmt->bind_param("sssss", $sessionUser, $ip, $action, $hash, $privkeyUpload['name']);
$stmt->execute(); $stmt->close(); $conn->close();

cleanupDir($tmpDir);

echo json_encode([
    'status'    => 'ok',
    'hash'      => $hash,
    'signature' => base64_encode($sigData)
]);

function cleanupDir($dir){
    if(!is_dir($dir)) return;
    foreach(glob($dir.DIRECTORY_SEPARATOR.'*') as $f){ if(is_file($f)) unlink($f); }
    rmdir($dir);
}
exit;
?>

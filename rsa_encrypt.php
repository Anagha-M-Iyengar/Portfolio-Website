<?php
/**
 * rsa_encrypt.php — Fixed for XAMPP Windows
 *
 * WHY THE ERROR HAPPENED:
 * When PHP runs exec("openssl ..."), Windows looks for "openssl.exe"
 * in the folders listed in the system PATH variable.
 * XAMPP does NOT automatically add OpenSSL to PATH.
 * So Windows says "openssl is not recognized" — it simply can't find the file.
 *
 * THE FIX:
 * We tell PHP the EXACT full location of openssl.exe on disk.
 * Instead of exec("openssl ...") we use exec("C:\xampp\apache\bin\openssl.exe ...")
 * Now Windows knows exactly where to look — no PATH needed.
 */
 
session_start();
header('Content-Type: application/json');
 
// ── Gate check ────────────────────────────────────────────────────────────────
if (!isset($_SESSION['visited_intro']) || $_SESSION['visited_intro'] !== true) {
    echo json_encode(['status'=>'error','message'=>'Access denied.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'POST only.']); exit;
}
 
// ══════════════════════════════════════════════════════════════════
// OPENSSL PATH — FULL PATH TO openssl.exe ON YOUR XAMPP MACHINE
//
// This is the key fix. We detect Windows vs Linux automatically.
//
// On XAMPP Windows: C:\xampp\apache\bin\openssl.exe
// On Linux/Mac:     just "openssl" (it's in PATH by default)
//
// If your XAMPP is installed in a different drive (e.g. D:\xampp),
// change the path below accordingly.
// ══════════════════════════════════════════════════════════════════
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    // Windows — use full path to XAMPP's openssl.exe
    // The quotes around the path handle spaces in directory names
    $OPENSSL = '"C:\\xampp\\apache\\bin\\openssl.exe"';
} else {
    // Linux / Mac — openssl is in PATH by default
    $OPENSSL = 'openssl';
}
 
// ── Read uploads ──────────────────────────────────────────────────────────────
$pubkeyUpload  = $_FILES['pubkey']  ?? null;
$msgFileUpload = $_FILES['msgfile'] ?? null;
$savename      = preg_replace('/[^A-Za-z0-9_\-]/', '', $_POST['savename'] ?? 'encrypted_message');
 
if (!$pubkeyUpload || $pubkeyUpload['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status'=>'error','message'=>'Public key upload failed.']); exit;
}
if (!$msgFileUpload || $msgFileUpload['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status'=>'error','message'=>'Message file upload failed.']); exit;
}
 
// ── Validate extensions ───────────────────────────────────────────────────────
if (!str_ends_with(strtolower($pubkeyUpload['name']), '.key')) {
    echo json_encode(['status'=>'error','message'=>'Only .key files allowed for public key.']); exit;
}
$allowedMsgExt = ['txt','pdf','doc','docx'];
$msgExt = strtolower(pathinfo($msgFileUpload['name'], PATHINFO_EXTENSION));
if (!in_array($msgExt, $allowedMsgExt)) {
    echo json_encode(['status'=>'error','message'=>'Message file must be .txt, .pdf, .doc, or .docx.']); exit;
}
 
// ── Set up temp directory ─────────────────────────────────────────────────────
$tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'poneglyph_' . session_id();
if (!is_dir($tmpDir)) mkdir($tmpDir, 0750, true);
 
$pubkeyPath    = $tmpDir . DIRECTORY_SEPARATOR . 'pubkey.key';
$msgPath       = $tmpDir . DIRECTORY_SEPARATOR . 'message.' . $msgExt;
$outPath       = $tmpDir . DIRECTORY_SEPARATOR . $savename  . '.bin';
$plaintextPath = $tmpDir . DIRECTORY_SEPARATOR . 'plaintext.txt';
 
move_uploaded_file($pubkeyUpload['tmp_name'],  $pubkeyPath);
move_uploaded_file($msgFileUpload['tmp_name'], $msgPath);
 
// ── Extract plain text from file ──────────────────────────────────────────────
if ($msgExt === 'txt') {
    copy($msgPath, $plaintextPath);
} else {
    // For PDF/DOC just use raw bytes — works fine for our purposes
    copy($msgPath, $plaintextPath);
}
 
// ── VERIFY OpenSSL is actually reachable before trying to use it ──────────────
// This gives a much clearer error message if the path is wrong
$testCmd    = $OPENSSL . ' version 2>&1';
$testOutput = [];
$testRet    = 0;
exec($testCmd, $testOutput, $testRet);
 
if ($testRet !== 0) {
    cleanup($tmpDir);
    echo json_encode([
        'status'  => 'error',
        'message' => 'OpenSSL not found at expected path. '
                   . 'Please check that XAMPP is installed at C:\\xampp. '
                   . 'If installed elsewhere, edit rsa_encrypt.php and update the $OPENSSL path. '
                   . 'Test output: ' . implode(' ', $testOutput)
    ]);
    exit;
}
 
// ── Decide: direct RSA (small) or hybrid RSA+AES (large) ────────────────────
$fileSize = filesize($plaintextPath);
 
if ($fileSize > 190) {
    // ── HYBRID: AES encrypts the message, RSA encrypts the AES key ──────────
    // This mirrors real-world TLS/PGP behaviour.
    //
    // Step 1: Generate a random 32-byte (256-bit) AES key
    // Step 2: Encrypt plaintext with AES-256-CBC using that key
    // Step 3: Encrypt the AES key itself with the RSA public key
    // Step 4: Bundle both into one .bin file
 
    $aesKeyPath    = $tmpDir . DIRECTORY_SEPARATOR . 'aes.key';
    $aesEncPath    = $tmpDir . DIRECTORY_SEPARATOR . 'aes_encrypted.bin';
    $rsaKeyEncPath = $tmpDir . DIRECTORY_SEPARATOR . 'rsa_encrypted_key.bin';
 
    // Step 1 — generate AES key
    $genCmd = $OPENSSL . ' rand -out ' . escapeshellarg($aesKeyPath) . ' 32 2>&1';
    exec($genCmd, $out1, $ret1);
 
    // Step 2 — encrypt message with AES-256-CBC
    $aesCmd = $OPENSSL . ' enc -aes-256-cbc -pbkdf2'
            . ' -in '   . escapeshellarg($plaintextPath)
            . ' -out '  . escapeshellarg($aesEncPath)
            . ' -pass file:' . escapeshellarg($aesKeyPath) . ' 2>&1';
    exec($aesCmd, $out2, $ret2);
 
    // Step 3 — encrypt AES key with RSA public key
    $rsaCmd = $OPENSSL . ' pkeyutl -encrypt -pubin'
            . ' -inkey ' . escapeshellarg($pubkeyPath)
            . ' -in '    . escapeshellarg($aesKeyPath)
            . ' -out '   . escapeshellarg($rsaKeyEncPath) . ' 2>&1';
    exec($rsaCmd, $out3, $ret3);
 
    if ($ret2 !== 0 || $ret3 !== 0 || !file_exists($aesEncPath) || !file_exists($rsaKeyEncPath)) {
        cleanup($tmpDir);
        $errDetail = implode(' ', array_merge($out2, $out3));
        echo json_encode(['status'=>'error','message'=>'Hybrid encryption failed: '.$errDetail]); exit;
    }
 
    // Step 4 — combine into one .bin: [4-byte RSA key length][RSA-enc key][AES-enc content]
    $rsaKeyData = file_get_contents($rsaKeyEncPath);
    $aesData    = file_get_contents($aesEncPath);
    $combined   = pack('N', strlen($rsaKeyData)) . $rsaKeyData . $aesData;
    file_put_contents($outPath, $combined);
    $method = 'hybrid_rsa_aes';
 
} else {
    // ── DIRECT RSA: small message, encrypt directly with public key ──────────
    $encCmd = $OPENSSL . ' pkeyutl -encrypt -pubin'
            . ' -inkey ' . escapeshellarg($pubkeyPath)
            . ' -in '    . escapeshellarg($plaintextPath)
            . ' -out '   . escapeshellarg($outPath) . ' 2>&1';
    exec($encCmd, $cmdOut, $retCode);
 
    if ($retCode !== 0 || !file_exists($outPath)) {
        cleanup($tmpDir);
        echo json_encode(['status'=>'error','message'=>'RSA encryption failed: '.implode(' ',$cmdOut)]); exit;
    }
    $method = 'rsa_direct';
}
 
// ── Return result as base64 JSON ──────────────────────────────────────────────
$encryptedData = file_get_contents($outPath);
$filename      = $savename . '.bin';
cleanup($tmpDir);
 
echo json_encode([
    'status'     => 'ok',
    'file'       => base64_encode($encryptedData),
    'filename'   => $filename,
    'char_count' => $fileSize,
    'method'     => $method
]);
 
// ── Helper: delete temp directory ─────────────────────────────────────────────
function cleanup($dir) {
    if (!is_dir($dir)) return;
    foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $f) {
        if (is_file($f)) unlink($f);
    }
    rmdir($dir);
}
exit;
?>

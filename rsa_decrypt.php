<?php
/**
 * rsa_decrypt.php — Fixed with passphrase support
 *
 * WHY "INVALID" WAS HAPPENING:
 * When a private key is generated with the -aes256 flag:
 *   openssl genpkey -algorithm RSA -out private.key -aes256
 * The key file itself is encrypted with a password.
 * When PHP runs exec("openssl pkeyutl -decrypt -inkey private.key ..."),
 * OpenSSL tries to read the key and asks for the password interactively.
 * But exec() has no terminal — so OpenSSL gets no password, fails, and
 * the decryption returns "invalid" or empty output.
 *
 * THE FIX:
 * We now accept the passphrase from the form and pass it to OpenSSL via:
 *   -passin pass:thepassword
 * This tells OpenSSL the key password non-interactively.
 *
 * If the key has NO password (generated without -aes256), leave blank.
 */

session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['visited_intro']) || $_SESSION['visited_intro'] !== true) {
    echo json_encode(['status'=>'error','message'=>'Access denied.']); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['status'=>'error','message'=>'POST only.']); exit;
}

// ── OpenSSL full path (XAMPP Windows fix) ─────────────────────────────────────
if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
    $OPENSSL = '"C:\\xampp\\apache\\bin\\openssl.exe"';
} else {
    $OPENSSL = 'openssl';
}

// ── Read uploads ──────────────────────────────────────────────────────────────
$privkeyUpload = $_FILES['privkey'] ?? null;
$binFileUpload = $_FILES['binfile'] ?? null;
$savename      = preg_replace('/[^A-Za-z0-9_\-]/', '', $_POST['savename'] ?? 'decrypted_message');
$format        = in_array($_POST['format'] ?? 'txt', ['txt','pdf','doc']) ? $_POST['format'] : 'txt';

// ── Passphrase — used if private key was generated with -aes256 ───────────────
// Sanitise: remove shell-dangerous characters
$passphrase    = preg_replace('/[^A-Za-z0-9@#_\-!.]/', '', $_POST['passphrase'] ?? '');

if (!$privkeyUpload || $privkeyUpload['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status'=>'error','message'=>'Private key upload failed.']); exit;
}
if (!$binFileUpload || $binFileUpload['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['status'=>'error','message'=>'.bin file upload failed.']); exit;
}
if (!str_ends_with(strtolower($privkeyUpload['name']), '.key')) {
    echo json_encode(['status'=>'error','message'=>'Only .key files allowed for private key.']); exit;
}
if (!str_ends_with(strtolower($binFileUpload['name']), '.bin')) {
    echo json_encode(['status'=>'error','message'=>'Only .bin encrypted files are allowed.']); exit;
}

// ── Temp directory ────────────────────────────────────────────────────────────
$tmpDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'poneglyph_dec_' . session_id();
if (!is_dir($tmpDir)) mkdir($tmpDir, 0750, true);

$privkeyPath   = $tmpDir . DIRECTORY_SEPARATOR . 'privkey.key';
$binPath       = $tmpDir . DIRECTORY_SEPARATOR . 'encrypted.bin';
$outPath       = $tmpDir . DIRECTORY_SEPARATOR . $savename . '.' . $format;
$plaintextPath = $tmpDir . DIRECTORY_SEPARATOR . 'plaintext.txt';

move_uploaded_file($privkeyUpload['tmp_name'], $privkeyPath);
move_uploaded_file($binFileUpload['tmp_name'], $binPath);

// ── Verify OpenSSL is reachable ───────────────────────────────────────────────
$testOut=[]; $testRet=0;
exec($OPENSSL . ' version 2>&1', $testOut, $testRet);
if ($testRet !== 0) {
    cleanup($tmpDir);
    echo json_encode(['status'=>'error',
        'message'=>'OpenSSL not found at C:\\xampp\\apache\\bin\\openssl.exe. '
                 . 'Output: ' . implode(' ', $testOut)]); exit;
}

// ── Build the -passin argument ─────────────────────────────────────────────────
// If passphrase is empty → no -passin flag (key has no password)
// If passphrase is set → -passin pass:thepassword
$passinArg = '';
if ($passphrase !== '') {
    $passinArg = ' -passin pass:' . escapeshellarg($passphrase);
}

// ── Detect file type: hybrid (web-app) vs direct RSA (terminal) ──────────────
$binData  = file_get_contents($binPath);
$binSize  = strlen($binData);
$isHybrid = false;

if ($binSize >= 4) {
    $first4 = unpack('N', substr($binData, 0, 4))[1];
    // Hybrid: first 4 bytes = RSA output size (256 for RSA-2048, 512 for RSA-4096)
    // AND file is larger than just that RSA block
    if (($first4 === 256 && $binSize > 260) || ($first4 === 512 && $binSize > 516)) {
        $isHybrid = true;
    }
}

$decryptedText = null;
$lastError     = '';

if (!$isHybrid) {
    // ────────────────────────────────────────────────────────────────────────
    // DIRECT RSA DECRYPT
    // Handles files encrypted via terminal:
    //   openssl pkeyutl -encrypt -pubin -inkey anagha_public.key
    //           -in confidential.txt -out encrypted_neha.bin
    //
    // Equivalent decrypt:
    //   openssl pkeyutl -decrypt -inkey private.key -passin pass:xyz
    //           -in encrypted_neha.bin -out decrypted.txt
    // ────────────────────────────────────────────────────────────────────────
    $decCmd = $OPENSSL . ' pkeyutl -decrypt'
            . ' -inkey ' . escapeshellarg($privkeyPath)
            . $passinArg
            . ' -in '    . escapeshellarg($binPath)
            . ' -out '   . escapeshellarg($plaintextPath)
            . ' 2>&1';
    exec($decCmd, $cmdOut, $retCode);

    if ($retCode === 0 && file_exists($plaintextPath) && filesize($plaintextPath) > 0) {
        $decryptedText = file_get_contents($plaintextPath);
    } else {
        $lastError = 'RSA decrypt failed: ' . implode(' ', $cmdOut)
                   . ' | Check: (1) Is the passphrase correct? '
                   . '(2) Does private.key match the public key used to encrypt?';
    }
}

if ($isHybrid) {
    // ────────────────────────────────────────────────────────────────────────
    // HYBRID RSA+AES DECRYPT
    // Handles files encrypted by this web app.
    // Structure: [4 bytes: len N][N bytes: RSA-enc AES key][rest: AES-enc content]
    //
    // Step 1: Extract RSA-encrypted AES key
    // Step 2: Decrypt AES key using RSA private key (+ passphrase if needed)
    // Step 3: Decrypt content using the recovered AES key
    // ────────────────────────────────────────────────────────────────────────
    $rsaKeyLen     = unpack('N', substr($binData, 0, 4))[1];
    $rsaKeyEncData = substr($binData, 4, $rsaKeyLen);
    $aesEncData    = substr($binData, 4 + $rsaKeyLen);

    $rsaKeyEncPath = $tmpDir . DIRECTORY_SEPARATOR . 'rsa_key_enc.bin';
    $aesKeyPath    = $tmpDir . DIRECTORY_SEPARATOR . 'aes.key';
    $aesEncPath    = $tmpDir . DIRECTORY_SEPARATOR . 'aes_content.bin';

    file_put_contents($rsaKeyEncPath, $rsaKeyEncData);
    file_put_contents($aesEncPath,    $aesEncData);

    // Step 2: Decrypt AES key with RSA private key
    $rsaCmd = $OPENSSL . ' pkeyutl -decrypt'
            . ' -inkey ' . escapeshellarg($privkeyPath)
            . $passinArg
            . ' -in '    . escapeshellarg($rsaKeyEncPath)
            . ' -out '   . escapeshellarg($aesKeyPath)
            . ' 2>&1';
    exec($rsaCmd, $out1, $ret1);

    if ($ret1 !== 0 || !file_exists($aesKeyPath) || filesize($aesKeyPath) === 0) {
        cleanup($tmpDir);
        echo json_encode(['status'=>'error',
            'message'=>'Hybrid decrypt step 1 failed (RSA key decryption): '
                     . implode(' ', $out1)
                     . ' | Check passphrase and ensure private key matches.']); exit;
    }

    // Step 3: Decrypt content with AES key
    $aesCmd = $OPENSSL . ' enc -d -aes-256-cbc -pbkdf2'
            . ' -in '    . escapeshellarg($aesEncPath)
            . ' -out '   . escapeshellarg($plaintextPath)
            . ' -pass file:' . escapeshellarg($aesKeyPath)
            . ' 2>&1';
    exec($aesCmd, $out2, $ret2);

    if ($ret2 !== 0 || !file_exists($plaintextPath) || filesize($plaintextPath) === 0) {
        cleanup($tmpDir);
        echo json_encode(['status'=>'error',
            'message'=>'Hybrid decrypt step 2 failed (AES decryption): '.implode(' ',$out2)]); exit;
    }

    $decryptedText = file_get_contents($plaintextPath);
}

// ── If direct RSA failed, return meaningful error ─────────────────────────────
if ($decryptedText === null) {
    cleanup($tmpDir);
    echo json_encode(['status'=>'error','message'=>$lastError]); exit;
}

// ── Convert plaintext to requested output format ──────────────────────────────
buildOutputFile($decryptedText, $outPath, $format);

$filename = $savename . '.' . $format;
$outData  = file_get_contents($outPath);
cleanup($tmpDir);

echo json_encode([
    'status'     => 'ok',
    'file'       => base64_encode($outData),
    'filename'   => $filename,
    'char_count' => strlen($decryptedText)
]);


// ── Output file builder ───────────────────────────────────────────────────────
function buildOutputFile($content, $outPath, $format) {
    if ($format === 'txt') {
        // Plain text — always works, always readable
        file_put_contents($outPath, $content);

    } elseif ($format === 'pdf') {
        // Build a valid PDF with correct xref byte offsets
        $safe   = str_replace(['\\','(',')'], ['\\\\','\\(','\\)'], $content);
        // Strip non-printable characters that break PDF text streams
        $safe   = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', ' ', $safe);
        $lines  = explode("\n", $safe);
        $stream = '';
        $y      = 750;
        foreach ($lines as $line) {
            $line    = trim($line);
            $stream .= "BT /F1 11 Tf 50 {$y} Td ({$line}) Tj ET\n";
            $y -= 16;
            if ($y < 50) { $y = 750; } // simple page overflow handling
        }

        // Build each PDF object and track byte offsets for xref
        $obj1 = "1 0 obj<</Type/Catalog/Pages 2 0 R>>\nendobj\n";
        $obj2 = "2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>\nendobj\n";
        $obj3 = "3 0 obj<</Type/Page/MediaBox[0 0 612 792]/Parent 2 0 R/Contents 4 0 R/Resources<</Font<</F1<</Type/Font/Subtype/Type1/BaseFont/Helvetica>>>>>>>> \nendobj\n";
        $obj4 = "4 0 obj<</Length ".strlen($stream).">>\nstream\n".$stream."\nendstream\nendobj\n";

        $header   = "%PDF-1.4\n";
        $off1     = strlen($header);
        $off2     = $off1 + strlen($obj1);
        $off3     = $off2 + strlen($obj2);
        $off4     = $off3 + strlen($obj3);
        $xrefPos  = $off4 + strlen($obj4);

        $xref = "xref\n0 5\n"
              . "0000000000 65535 f \n"
              . str_pad($off1,10,'0',STR_PAD_LEFT)." 00000 n \n"
              . str_pad($off2,10,'0',STR_PAD_LEFT)." 00000 n \n"
              . str_pad($off3,10,'0',STR_PAD_LEFT)." 00000 n \n"
              . str_pad($off4,10,'0',STR_PAD_LEFT)." 00000 n \n";

        $trailer = "trailer<</Size 5/Root 1 0 R>>\nstartxref\n{$xrefPos}\n%%EOF";

        $pdf = $header . $obj1 . $obj2 . $obj3 . $obj4 . $xref . $trailer;
        file_put_contents($outPath, $pdf);

    } elseif ($format === 'doc') {
        // RTF format — opens correctly in Word and LibreOffice as .doc
        $safe = preg_replace('/[^\x09\x0A\x0D\x20-\x7E]/', ' ', $content);
        $safe = str_replace(['\\','{','}'], ['\\\\','\\{','\\}'], $safe);
        $rtf  = "{\\rtf1\\ansi\\ansicpg1252\\deff0\n"
              . "{\\fonttbl{\\f0\\froman\\fcharset0 Times New Roman;}}\n"
              . "{\\f0\\fs24 "
              . str_replace(["\r\n","\n","\r"], ["\\par\n","\\par\n","\\par\n"], $safe)
              . "}}";
        file_put_contents($outPath, $rtf);
    }
}

function cleanup($dir) {
    if (!is_dir($dir)) return;
    foreach (glob($dir . DIRECTORY_SEPARATOR . '*') as $f) {
        if (is_file($f)) unlink($f);
    }
    rmdir($dir);
}
exit;
?>

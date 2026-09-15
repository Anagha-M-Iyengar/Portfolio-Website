<?php
/**
 * rsa_aes.php — Poneglyph Vault
 * RSA Public/Private Key Encryption & Decryption via OpenSSL (PHP backend)
 * Gate: must have visited intro (session check)
 */
session_start();
if (!isset($_SESSION['visited_intro']) || $_SESSION['visited_intro'] !== true) {
    header("Location: intro.php"); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Poneglyph Vault — RSA · AES Encryption</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Jost:wght@300;400;500;600&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
<style>
:root{
  --sea0:#080f18;--sea1:#0d1f2e;--sea2:#0f2d45;
  --teal:#1a5c6e;--teal2:#2a8fa8;--teal3:#4ab8cc;
  --gold:#d4a843;--goldl:#f0cc70;--goldd:#8a6010;
  --stone:#b8a88a;--stonel:#e8dcc8;
  --mist:#d6eff8;--foam:#e8f6fc;
  --err:#c0392b;--ok:#00875a;
  --ink:#0d1f2e;
}
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
 
body{
  font-family:'Jost',sans-serif;min-height:100vh;
  background:
    radial-gradient(ellipse at 15% 25%,rgba(26,92,110,.25) 0%,transparent 45%),
    radial-gradient(ellipse at 85% 75%,rgba(42,143,168,.18) 0%,transparent 40%),
    linear-gradient(170deg,#050d14 0%,#0a1825 35%,#0d2236 65%,#060e18 100%);
  color:var(--stonel);
  overflow-x:hidden;
  position:relative;
}
 
/* ── Stone texture overlay ── */
body::before{
  content:'';position:fixed;inset:0;pointer-events:none;z-index:0;
  background-image:
    repeating-linear-gradient(0deg,transparent,transparent 40px,rgba(184,168,138,.02) 40px,rgba(184,168,138,.02) 41px),
    repeating-linear-gradient(90deg,transparent,transparent 60px,rgba(184,168,138,.015) 60px,rgba(184,168,138,.015) 61px);
}
 
/* ── Floating glyphs background ── */
.glyph-bg{position:fixed;inset:0;pointer-events:none;z-index:0;overflow:hidden;opacity:.04;}
.glyph-bg span{position:absolute;font-family:'Courier Prime',monospace;font-size:clamp(10px,1.5vw,18px);color:var(--goldl);animation:glyphDrift linear infinite;}
@keyframes glyphDrift{0%{transform:translateY(100vh) rotate(0deg);opacity:0}10%{opacity:1}90%{opacity:1}100%{transform:translateY(-20vh) rotate(360deg);opacity:0}}
 
/* ══ HEADER ══ */
.header{position:relative;z-index:10;text-align:center;padding:44px 20px 28px;}
.poneglyph-icon{
  width:80px;height:80px;margin:0 auto 16px;
  background:linear-gradient(135deg,var(--goldd),var(--gold),var(--goldl));
  border-radius:6px;
  display:flex;align-items:center;justify-content:center;
  font-size:2.2rem;
  box-shadow:0 8px 32px rgba(212,168,67,.4),0 0 60px rgba(212,168,67,.1);
  animation:glowPulse 3s ease-in-out infinite;
  transform:rotate(3deg);
}
@keyframes glowPulse{0%,100%{box-shadow:0 8px 32px rgba(212,168,67,.4),0 0 60px rgba(212,168,67,.1)}50%{box-shadow:0 8px 48px rgba(212,168,67,.6),0 0 90px rgba(212,168,67,.2)}}
.brand{font-family:'Cinzel',serif;font-size:clamp(1.8rem,5.5vw,3rem);font-weight:900;letter-spacing:.15em;background:linear-gradient(135deg,var(--stone),var(--goldl),var(--stonel),var(--gold));background-size:300%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:shimmer 5s ease infinite;margin-bottom:4px;}
@keyframes shimmer{0%,100%{background-position:0%}50%{background-position:100%}}
.brand-sub{font-family:'Cinzel',serif;font-size:.7rem;letter-spacing:.35em;text-transform:uppercase;color:var(--teal3);margin-bottom:22px;}
 
.desc-card{
  max-width:760px;margin:0 auto;
  background:rgba(13,31,46,.7);
  border:1px solid rgba(212,168,67,.2);
  border-radius:16px;padding:22px 28px;
  font-size:.88rem;line-height:1.85;color:var(--stone);
  position:relative;overflow:hidden;
}
.desc-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(to right,transparent,var(--gold),var(--teal3),var(--gold),transparent);}
.desc-card::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(to right,transparent,rgba(212,168,67,.3),transparent);}
.desc-card strong{color:var(--goldl);}
.desc-card em{color:var(--teal3);font-style:normal;}
.algo-badges{display:flex;flex-wrap:wrap;gap:8px;margin-top:14px;justify-content:center;}
.algo-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 14px;border-radius:20px;font-size:.72rem;font-weight:600;letter-spacing:.06em;}
.badge-rsa{background:rgba(212,168,67,.12);border:1px solid rgba(212,168,67,.35);color:var(--goldl);}
.badge-aes{background:rgba(74,184,204,.1);border:1px solid rgba(74,184,204,.3);color:var(--teal3);}
.badge-ossl{background:rgba(184,168,138,.08);border:1px solid rgba(184,168,138,.25);color:var(--stone);}
 
/* ══ VAULT COLUMNS ══ */
.vault{
  max-width:1200px;margin:0 auto;padding:0 18px 20px;
  display:grid;grid-template-columns:1fr 1fr;gap:24px;
  position:relative;z-index:10;
}
@media(max-width:800px){.vault{grid-template-columns:1fr;}}
 
/* ══ PANEL ══ */
.panel{
  background:rgba(8,15,24,.75);
  backdrop-filter:blur(14px);
  border:1px solid rgba(212,168,67,.15);
  border-radius:20px;overflow:hidden;
  box-shadow:0 16px 48px rgba(0,0,0,.5),inset 0 1px 0 rgba(212,168,67,.08);
  animation:riseUp .7s cubic-bezier(.34,1.4,.64,1) both;
}
.panel:nth-child(2){animation-delay:.12s;}
@keyframes riseUp{from{opacity:0;transform:translateY(30px)}to{opacity:1;transform:translateY(0)}}
 
.panel-head{
  padding:18px 22px 14px;
  background:linear-gradient(135deg,rgba(13,31,46,.9),rgba(8,15,24,.7));
  border-bottom:1px solid rgba(212,168,67,.12);
  position:relative;
}
.panel-head::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(to right,transparent,rgba(212,168,67,.3),transparent);}
.panel-title{font-family:'Cinzel',serif;font-size:1rem;font-weight:700;letter-spacing:.1em;display:flex;align-items:center;gap:10px;}
.panel-title.enc-title{color:var(--goldl);}
.panel-title.dec-title{color:var(--teal3);}
.panel-body{padding:22px;}
 
/* ── Form labels ── */
.field-label{display:block;font-size:.68rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--teal3);margin-bottom:7px;margin-top:16px;}
.field-label:first-child{margin-top:0;}
.field-label .req{color:var(--gold);margin-left:3px;}
 
/* ── Textarea / input ── */
textarea.vi,input.vi{
  width:100%;background:rgba(5,12,22,.7);
  border:1.5px solid rgba(212,168,67,.18);
  border-radius:11px;color:var(--stonel);
  font-family:'Courier Prime',monospace;font-size:.88rem;
  padding:11px 13px;outline:none;
  transition:border-color .2s,box-shadow .2s;resize:vertical;
}
textarea.vi{min-height:100px;}
textarea.vi:focus,input.vi:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(212,168,67,.1);}
textarea.vi::placeholder,input.vi::placeholder{color:rgba(184,168,138,.3);}
 
/* ── Upload zone ── */
.upload-zone{
  border:1.5px dashed rgba(212,168,67,.25);border-radius:11px;
  padding:14px 16px;text-align:center;cursor:pointer;
  background:rgba(5,12,22,.5);transition:all .2s;
  font-size:.8rem;color:var(--stone);
}
.upload-zone:hover{border-color:var(--gold);background:rgba(212,168,67,.05);}
.upload-zone input[type=file]{display:none;}
.uz-icon{font-size:1.4rem;display:block;margin-bottom:4px;}
.uz-sub{font-size:.7rem;color:rgba(184,168,138,.5);margin-top:3px;}
.uz-name{margin-top:5px;font-size:.73rem;color:var(--goldl);word-break:break-all;}
 
/* ── Key upload (special .key only) ── */
.key-zone{
  border:1.5px dashed rgba(74,184,204,.3);border-radius:11px;
  padding:13px 16px;text-align:center;cursor:pointer;
  background:rgba(5,12,22,.5);transition:all .2s;
  font-size:.8rem;color:var(--teal3);
}
.key-zone:hover{border-color:var(--teal3);background:rgba(74,184,204,.05);}
.key-zone input[type=file]{display:none;}
.key-name{margin-top:5px;font-size:.73rem;color:var(--teal3);word-break:break-all;}
 
/* ── Filename input ── */
.filename-row{display:flex;align-items:center;gap:8px;margin-top:0;}
.filename-prefix{font-family:'Courier Prime',monospace;font-size:.82rem;color:var(--gold);font-weight:700;white-space:nowrap;padding:9px 10px;background:rgba(212,168,67,.08);border:1.5px solid rgba(212,168,67,.18);border-right:none;border-radius:10px 0 0 10px;}
.filename-input{flex:1;background:rgba(5,12,22,.7);border:1.5px solid rgba(212,168,67,.18);border-left:none;border-radius:0 10px 10px 0;color:var(--stonel);font-family:'Courier Prime',monospace;font-size:.88rem;padding:9px 12px;outline:none;transition:border-color .2s;}
.filename-input:focus{border-color:var(--gold);}
.filename-ext{font-family:'Courier Prime',monospace;font-size:.78rem;color:rgba(184,168,138,.5);margin-top:4px;padding-left:2px;}
 
/* ── Format selector (decrypt) ── */
.format-row{display:flex;gap:10px;flex-wrap:wrap;}
.fmt-opt{display:none;}
.fmt-opt+label{
  padding:7px 16px;border-radius:20px;
  border:1.5px solid rgba(74,184,204,.25);
  font-size:.78rem;font-weight:600;color:var(--stone);
  cursor:pointer;transition:all .2s;background:rgba(5,12,22,.5);
}
.fmt-opt:checked+label{background:rgba(74,184,204,.15);border-color:var(--teal3);color:var(--teal3);}
.fmt-opt+label:hover{border-color:var(--teal3);}
 
/* ── Action button ── */
.btn-action{
  display:block;width:100%;margin-top:18px;
  padding:13px 20px;border:none;border-radius:13px;
  font-family:'Cinzel',serif;font-size:.88rem;font-weight:700;letter-spacing:.1em;
  cursor:pointer;transition:transform .2s,box-shadow .2s,opacity .2s;
}
.btn-enc{background:linear-gradient(135deg,var(--goldd),var(--gold),#e8b830);color:#0a1020;box-shadow:0 6px 24px rgba(212,168,67,.35);}
.btn-enc:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 10px 32px rgba(212,168,67,.5);}
.btn-dec{background:linear-gradient(135deg,var(--teal),var(--teal2),var(--teal3));color:#fff;box-shadow:0 6px 24px rgba(42,143,168,.35);}
.btn-dec:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 10px 32px rgba(42,143,168,.5);}
.btn-action:disabled{opacity:.45;cursor:not-allowed;transform:none !important;}
 
/* ── Progress indicator ── */
.progress-bar{
  height:3px;background:rgba(212,168,67,.1);border-radius:3px;
  margin-top:10px;overflow:hidden;display:none;
}
.progress-fill{height:100%;border-radius:3px;animation:progressAnim 1.5s ease-in-out infinite;}
.progress-fill.gold{background:linear-gradient(to right,var(--goldd),var(--goldl),var(--goldd));background-size:200%;}
.progress-fill.teal{background:linear-gradient(to right,var(--teal),var(--teal3),var(--teal));background-size:200%;}
@keyframes progressAnim{0%{background-position:0%}100%{background-position:200%}}
 
/* ── Error ── */
.err-msg{margin-top:10px;background:rgba(192,57,43,.12);border:1px solid rgba(192,57,43,.3);border-radius:10px;padding:9px 13px;font-size:.78rem;color:#ff8a80;display:none;}
.err-msg.show{display:block;}
 
/* ══ SUCCESS OVERLAY ══ */
.success-overlay{
  display:none;position:fixed;inset:0;
  background:rgba(5,12,22,.92);backdrop-filter:blur(8px);
  z-index:9998;align-items:center;justify-content:center;
  animation:fadeIn .3s ease;
}
.success-overlay.show{display:flex;}
@keyframes fadeIn{from{opacity:0}to{opacity:1}}
.success-card{
  background:linear-gradient(160deg,rgba(13,31,46,.98),rgba(8,15,24,.98));
  border:1px solid rgba(212,168,67,.3);
  border-radius:24px;padding:44px 48px;max-width:480px;width:90%;
  text-align:center;position:relative;
  box-shadow:0 24px 80px rgba(0,0,0,.6),0 0 120px rgba(212,168,67,.08);
  animation:cardBounce .5s cubic-bezier(.34,1.4,.64,1) both;
}
@keyframes cardBounce{from{opacity:0;transform:scale(.8) translateY(40px)}to{opacity:1;transform:scale(1) translateY(0)}}
.success-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(to right,transparent,var(--gold),var(--teal3),var(--gold),transparent);border-radius:24px 24px 0 0;}
 
.pirate-ascii{
  font-family:'Courier Prime',monospace;
  font-size:.72rem;line-height:1.4;
  color:var(--goldl);margin-bottom:16px;
  white-space:pre;display:inline-block;
  animation:pirateWave 2s ease-in-out infinite;
}
@keyframes pirateWave{0%,100%{transform:rotate(-1deg)}50%{transform:rotate(1deg)}}
 
.success-title{font-family:'Cinzel',serif;font-size:1.4rem;font-weight:700;color:var(--goldl);margin-bottom:8px;letter-spacing:.08em;}
.success-msg{font-size:.85rem;color:var(--stone);line-height:1.7;margin-bottom:24px;}
.success-msg strong{color:var(--stonel);}
 
.btn-download{
  display:inline-flex;align-items:center;gap:10px;
  background:linear-gradient(135deg,var(--goldd),var(--gold));
  color:#0a1020;border:none;padding:13px 32px;border-radius:50px;
  font-family:'Cinzel',serif;font-size:.88rem;font-weight:700;letter-spacing:.08em;
  cursor:pointer;box-shadow:0 6px 24px rgba(212,168,67,.4);
  transition:transform .2s,box-shadow .2s;text-decoration:none;
}
.btn-download:hover{transform:translateY(-2px);box-shadow:0 10px 32px rgba(212,168,67,.6);}
.btn-close-success{
  display:block;margin:14px auto 0;
  background:none;border:1px solid rgba(184,168,138,.2);
  border-radius:20px;padding:7px 20px;
  font-family:'Jost',sans-serif;font-size:.78rem;color:var(--stone);
  cursor:pointer;transition:border-color .2s;
}
.btn-close-success:hover{border-color:var(--stone);}
 
/* ══ FOOTER ══ */
.page-footer{text-align:center;padding:20px;font-size:.72rem;color:rgba(184,168,138,.35);position:relative;z-index:10;letter-spacing:.06em;}
.page-footer a{color:var(--teal2);text-decoration:none;border-bottom:1px dashed rgba(42,143,168,.3);}
 
@media(max-width:560px){.vault{padding:0 12px;}.panel-body{padding:16px;}.success-card{padding:32px 24px;}}
</style>
</head>
<body>
 
<!-- Floating glyphs background -->
<div class="glyph-bg" id="glyphBg"></div>
 
<!-- ══ HEADER ══ -->
<header class="header">
  <div class="poneglyph-icon">📜</div>
  <h1 class="brand">PONEGLYPH VAULT</h1>
  <p class="brand-sub">⚓ RSA · AES · OpenSSL Encryption ⚓</p>
  <div class="desc-card">
    <p>
      In the world of the Grand Line, <strong>Poneglyphs</strong> are indestructible stone tablets engraved with
      ancient truths — readable only by those with the rightful key. This vault works the same way.
    </p>
    <p style="margin-top:10px;">
      <strong>RSA (Rivest–Shamir–Adleman)</strong> is an <em>asymmetric</em> encryption algorithm — it uses
      a mathematically linked <strong>key pair</strong>: a <strong>Public Key</strong> (shared with the world, used to lock)
      and a <strong>Private Key</strong> (kept secret by you alone, used to unlock). What one key encrypts,
      only the other can decrypt. No two key pairs are ever identical — yours is as unique as your fingerprint.
    </p>
    <p style="margin-top:10px;">
      <strong>AES (Advanced Encryption Standard)</strong> is the gold standard of <em>symmetric</em> encryption —
      lightning-fast, military-grade, used by governments and banks worldwide. In hybrid encryption (as used here),
      AES encrypts the actual message while RSA encrypts the AES key — giving you the speed of AES and the
      security of RSA together. Powered by <strong>OpenSSL</strong> running on the server.
    </p>
    <div class="algo-badges">
      <span class="algo-badge badge-rsa">🔑 RSA-2048 Asymmetric</span>
      <span class="algo-badge badge-aes">⚡ AES-256 Symmetric</span>
      <span class="algo-badge badge-ossl">🛡️ OpenSSL Backend</span>
    </div>
  </div>
</header>
 
<!-- ══ VAULT PANELS ══ -->
<div class="vault">
 
  <!-- ─── ENCRYPT PANEL ─── -->
  <div class="panel">
    <div class="panel-head">
      <div class="panel-title enc-title">🔐 Encrypt Message</div>
    </div>
    <div class="panel-body">
 
      <label class="field-label">Message to Encrypt <span class="req">*</span></label>
      <textarea class="vi" id="enc-text" placeholder="Type your message here… or upload a file below."
        oninput="this.value=this.value.replace(/[<>]/g,'')"></textarea>
 
      <label class="field-label" style="margin-top:12px;">— OR — Upload File</label>
      <div class="upload-zone" onclick="document.getElementById('enc-file').click()">
        <input type="file" id="enc-file" accept=".txt,.pdf,.doc,.docx"
               onchange="handleMsgFile(event,'enc-text','enc-fname')">
        <span class="uz-icon">📂</span>
        <span>Browse .txt · .pdf · .doc</span>
        <div class="uz-name" id="enc-fname"></div>
      </div>
 
      <label class="field-label" style="margin-top:16px;">Upload Receiver's Public Key <span class="req">*</span></label>
      <div class="key-zone" onclick="document.getElementById('enc-pubkey').click()">
        <input type="file" id="enc-pubkey" accept=".key"
               onchange="handleKeyFile(event,'enc-keyname')">
        <span class="uz-icon">🗝️</span>
        <span>Upload <strong>.key</strong> file only</span>
        <div class="key-name" id="enc-keyname"></div>
      </div>
 
      <label class="field-label" style="margin-top:16px;">Save Encrypted File As</label>
      <div class="filename-row">
        <span class="filename-prefix">encrypted_</span>
        <input type="text" class="filename-input" id="enc-savename"
               placeholder="filename" maxlength="40"
               oninput="this.value=this.value.replace(/[^A-Za-z0-9_\-]/g,'')">
      </div>
      <div class="filename-ext">→ Will save as: <span id="enc-preview">encrypted_filename.bin</span></div>
 
      <button class="btn-action btn-enc" id="enc-btn" onclick="runEncrypt()" disabled>
        🔐 Encrypt with Public Key
      </button>
      <div class="progress-bar" id="enc-progress"><div class="progress-fill gold"></div></div>
      <div class="err-msg" id="enc-err"></div>
 
    </div>
  </div>
 
  <!-- ─── DECRYPT PANEL ─── -->
  <div class="panel">
    <div class="panel-head">
      <div class="panel-title dec-title">🔓 Decrypt Message</div>
    </div>
    <div class="panel-body">
 
      <label class="field-label">Upload Encrypted File (.bin) <span class="req">*</span></label>
      <div class="upload-zone" onclick="document.getElementById('dec-file').click()">
        <input type="file" id="dec-file" accept=".bin"
               onchange="handleBinFile(event,'dec-fname')">
        <span class="uz-icon">📦</span>
        <span>Upload <strong>.bin</strong> encrypted file</span>
        <div class="uz-name" id="dec-fname"></div>
      </div>
 
      <label class="field-label" style="margin-top:16px;">Upload Your Private Key <span class="req">*</span></label>
      <div class="key-zone" onclick="document.getElementById('dec-privkey').click()">
        <input type="file" id="dec-privkey" accept=".key"
               onchange="handleKeyFile(event,'dec-keyname')">
        <span class="uz-icon">🗝️</span>
        <span>Upload <strong>.key</strong> private key file</span>
        <div class="key-name" id="dec-keyname"></div>
      </div>
 
      <label class="field-label" style="margin-top:16px;">
        Private Key Passphrase
        <span style="font-size:.62rem;color:rgba(184,168,138,.5);font-weight:400;text-transform:none;letter-spacing:0;margin-left:6px;">
          (only if key was generated with -aes256 password — leave blank otherwise)
        </span>
      </label>
      <input type="password" class="vi" id="dec-passphrase"
             placeholder="Enter passphrase if your key is password-protected…"
             style="min-height:unset;resize:none;">
 
      <label class="field-label" style="margin-top:16px;">Save Decrypted File As</label>
      <div class="filename-row">
        <span class="filename-prefix">decrypted_</span>
        <input type="text" class="filename-input" id="dec-savename"
               placeholder="filename" maxlength="40"
               oninput="this.value=this.value.replace(/[^A-Za-z0-9_\-]/g,'')">
      </div>
 
      <label class="field-label" style="margin-top:14px;">Output Format</label>
      <div class="format-row">
        <input type="radio" name="dec-fmt" id="fmt-txt" value="txt" class="fmt-opt" checked>
        <label for="fmt-txt">.txt</label>
        <input type="radio" name="dec-fmt" id="fmt-pdf" value="pdf" class="fmt-opt">
        <label for="fmt-pdf">.pdf</label>
        <input type="radio" name="dec-fmt" id="fmt-doc" value="doc" class="fmt-opt">
        <label for="fmt-doc">.doc</label>
      </div>
      <div class="filename-ext">→ Will save as: <span id="dec-preview">decrypted_filename.txt</span></div>
 
      <button class="btn-action btn-dec" id="dec-btn" onclick="runDecrypt()" disabled>
        🔓 Decrypt with Private Key
      </button>
      <div class="progress-bar" id="dec-progress"><div class="progress-fill teal"></div></div>
      <div class="err-msg" id="dec-err"></div>
 
    </div>
  </div>
 
</div><!-- /vault -->
 
<!-- ══ SUCCESS OVERLAY — ENCRYPT ══ -->
<div class="success-overlay" id="enc-success">
  <div class="success-card">
    <pre class="pirate-ascii">    .     .
  .  \   /  .
 .    \ /    .
  \    X    /
   \  / \  /
    \/   \/
   /|\   /|\
  🏴‍☠️ ARRR! 🏴‍☠️</pre>
    <div class="success-title">Message Sealed!</div>
    <div class="success-msg" id="enc-success-msg">
      Yer message has been locked in an iron chest, matey!<br>
      Only the holder of the matching private key can break this seal.
    </div>
    <a href="#" class="btn-download" id="enc-dl-btn" download>⬇️ Download Encrypted File</a>
    <button class="btn-close-success" onclick="closeSuccess('enc')">← Back to Vault</button>
  </div>
</div>
 
<!-- ══ SUCCESS OVERLAY — DECRYPT ══ -->
<div class="success-overlay" id="dec-success">
  <div class="success-card">
    <pre class="pirate-ascii">   _____
  /     \
 | (^)(^)|
  \  __  /
   |    |
   |____|
  🔓 OPEN! 🔓</pre>
    <div class="success-title">Treasure Revealed!</div>
    <div class="success-msg" id="dec-success-msg">
      The seal is broken! Yer message has been decrypted<br>
      and awaits ye below, brave soul.
    </div>
    <a href="#" class="btn-download" id="dec-dl-btn" download>⬇️ Download Decrypted File</a>
    <button class="btn-close-success" onclick="closeSuccess('dec')">← Back to Vault</button>
  </div>
</div>
 
<div class="page-footer">
  <p>📜 Poneglyph Vault &nbsp;·&nbsp; <a href="index.php">← Back to Portfolio</a> &nbsp;·&nbsp;
  Powered by OpenSSL · RSA-2048 · AES-256</p>
</div>
 
<script>
/* ═══════════════════════════════════
   FLOATING GLYPHS
═══════════════════════════════════ */
(function(){
  const glyphs = ['∀','∃','∈','∉','∮','∯','∰','⊕','⊗','⊙','⊛','⊞','⊟','◈','◉','◊','◌','◍','◎','●','■','▲','◆','★','☆','⚡','⚓','🗝','📜','⚔'];
  const container = document.getElementById('glyphBg');
  for(let i=0;i<30;i++){
    const s=document.createElement('span');
    s.textContent=glyphs[Math.floor(Math.random()*glyphs.length)];
    s.style.cssText=`left:${Math.random()*100}%;animation-duration:${15+Math.random()*25}s;animation-delay:${Math.random()*15}s;font-size:${10+Math.random()*16}px;`;
    container.appendChild(s);
  }
})();
 
/* ═══════════════════════════════════
   STATE
═══════════════════════════════════ */
let encMsgFile  = null;  // uploaded message file for encrypt
let encPubKey   = null;  // uploaded .key public key
let decBinFile  = null;  // uploaded .bin encrypted file
let decPrivKey  = null;  // uploaded .key private key
 
/* ═══════════════════════════════════
   FILE HANDLERS
═══════════════════════════════════ */
function handleMsgFile(event, textareaId, nameId){
  const file = event.target.files[0];
  if(!file) return;
  encMsgFile = file;
  document.getElementById(nameId).textContent = '📄 '+file.name;
  document.getElementById(textareaId).value = '';
  document.getElementById(textareaId).placeholder = 'File loaded: '+file.name+' — leave this empty to use file';
  checkEncReady();
}
 
function handleKeyFile(event, nameId){
  const file = event.target.files[0];
  if(!file) return;
  if(!file.name.endsWith('.key')){
    alert('Only .key files are allowed!');
    event.target.value='';return;
  }
  // Assign to correct slot
  if(nameId==='enc-keyname'){ encPubKey=file; document.getElementById(nameId).textContent='🗝️ '+file.name; checkEncReady(); }
  if(nameId==='dec-keyname'){ decPrivKey=file; document.getElementById(nameId).textContent='🗝️ '+file.name; checkDecReady(); }
}
 
function handleBinFile(event, nameId){
  const file = event.target.files[0];
  if(!file) return;
  if(!file.name.endsWith('.bin')){
    alert('Only .bin encrypted files are allowed!');
    event.target.value='';return;
  }
  decBinFile = file;
  document.getElementById(nameId).textContent='📦 '+file.name;
  checkDecReady();
}
 
/* ═══════════════════════════════════
   BUTTON ENABLE/DISABLE
═══════════════════════════════════ */
function checkEncReady(){
  const hasMsg  = encMsgFile || document.getElementById('enc-text').value.trim();
  const hasKey  = encPubKey !== null;
  document.getElementById('enc-btn').disabled = !(hasMsg && hasKey);
}
function checkDecReady(){
  const hasBin  = decBinFile !== null;
  const hasKey  = decPrivKey !== null;
  document.getElementById('dec-btn').disabled = !(hasBin && hasKey);
}
 
// Also enable encrypt button when typing in textarea
document.getElementById('enc-text').addEventListener('input', checkEncReady);
 
/* ═══════════════════════════════════
   FILENAME PREVIEWS
═══════════════════════════════════ */
document.getElementById('enc-savename').addEventListener('input', function(){
  const name = this.value.trim() || 'filename';
  document.getElementById('enc-preview').textContent = 'encrypted_'+name+'.bin';
});
document.getElementById('dec-savename').addEventListener('input', function(){
  const name = this.value.trim() || 'filename';
  const fmt  = document.querySelector('input[name="dec-fmt"]:checked').value;
  document.getElementById('dec-preview').textContent = 'decrypted_'+name+'.'+fmt;
});
document.querySelectorAll('input[name="dec-fmt"]').forEach(r=>r.addEventListener('change',()=>{
  const name = document.getElementById('dec-savename').value.trim() || 'filename';
  const fmt  = r.value;
  document.getElementById('dec-preview').textContent = 'decrypted_'+name+'.'+fmt;
}));
 
/* ═══════════════════════════════════
   ENCRYPT — sends to rsa_encrypt.php
═══════════════════════════════════ */
async function runEncrypt(){
  hideErr('enc-err');
  const textMsg  = document.getElementById('enc-text').value.trim();
  const saveName = document.getElementById('enc-savename').value.trim() || 'message';
 
  if(!encPubKey){ showErr('enc-err','Please upload the receiver\'s public key (.key file).'); return; }
  if(!textMsg && !encMsgFile){ showErr('enc-err','Please type a message or upload a file.'); return; }
 
  const fd = new FormData();
  fd.append('pubkey',   encPubKey);
  fd.append('savename', 'encrypted_'+saveName);
  if(encMsgFile){
    fd.append('msgfile', encMsgFile);
  } else {
    // Create a blob from the typed text
    fd.append('msgfile', new Blob([textMsg],{type:'text/plain'}), 'message.txt');
  }
 
  // Show progress
  showProgress('enc-progress');
  document.getElementById('enc-btn').disabled=true;
 
  try{
    const res  = await fetch('rsa_encrypt.php', {method:'POST', body:fd});
    const data = await res.json();
 
    hideProgress('enc-progress');
    document.getElementById('enc-btn').disabled=false;
 
    if(data.status==='ok'){
      // data.file = base64 encoded .bin
      const blob = b64toBlob(data.file, 'application/octet-stream');
      const url  = URL.createObjectURL(blob);
      const dlBtn = document.getElementById('enc-dl-btn');
      dlBtn.href     = url;
      dlBtn.download = data.filename;
      document.getElementById('enc-success-msg').innerHTML =
        'Yer message has been locked, matey!<br>File: <strong>'+data.filename+'</strong><br>'+
        'Only the holder of the matching private key can break this seal.';
      document.getElementById('enc-success').classList.add('show');
      // Save to DB
      saveLog('encrypt', data.filename, encPubKey.name, data.char_count||0);
    } else {
      showErr('enc-err', data.message || 'Encryption failed. Check your key file and try again.');
    }
  }catch(e){
    hideProgress('enc-progress');
    document.getElementById('enc-btn').disabled=false;
    showErr('enc-err','Server error: '+e.message+'. Make sure OpenSSL is available on the server.');
  }
}
 
/* ═══════════════════════════════════
   DECRYPT — sends to rsa_decrypt.php
═══════════════════════════════════ */
async function runDecrypt(){
  hideErr('dec-err');
  const saveName = document.getElementById('dec-savename').value.trim() || 'message';
  const fmt      = document.querySelector('input[name="dec-fmt"]:checked').value;
 
  if(!decBinFile){ showErr('dec-err','Please upload an encrypted .bin file.'); return; }
  if(!decPrivKey){ showErr('dec-err','Please upload your private key (.key file).'); return; }
 
  const fd = new FormData();
  fd.append('privkey',     decPrivKey);
  fd.append('binfile',     decBinFile);
  fd.append('savename',    'decrypted_'+saveName);
  fd.append('format',      fmt);
  fd.append('passphrase',  document.getElementById('dec-passphrase').value);
 
  showProgress('dec-progress');
  document.getElementById('dec-btn').disabled=true;
 
  try{
    const res  = await fetch('rsa_decrypt.php', {method:'POST', body:fd});
    const data = await res.json();
 
    hideProgress('dec-progress');
    document.getElementById('dec-btn').disabled=false;
 
    if(data.status==='ok'){
      const mimeMap = {txt:'text/plain', pdf:'application/pdf', doc:'application/msword'};
      const blob    = b64toBlob(data.file, mimeMap[fmt]||'text/plain');
      const url     = URL.createObjectURL(blob);
      const dlBtn   = document.getElementById('dec-dl-btn');
      dlBtn.href     = url;
      dlBtn.download = data.filename;
      document.getElementById('dec-success-msg').innerHTML =
        'The seal is broken! Yer message has been revealed.<br>File: <strong>'+data.filename+'</strong>';
      document.getElementById('dec-success').classList.add('show');
      saveLog('decrypt', data.filename, decPrivKey.name, data.char_count||0);
    } else {
      showErr('dec-err', data.message || 'Decryption failed. Ensure you are using the correct private key.');
    }
  }catch(e){
    hideProgress('dec-progress');
    document.getElementById('dec-btn').disabled=false;
    showErr('dec-err','Server error: '+e.message);
  }
}
 
/* ═══════════════════════════════════
   HELPERS
═══════════════════════════════════ */
function showErr(id,msg){ const el=document.getElementById(id); el.textContent='⚠️ '+msg; el.classList.add('show'); }
function hideErr(id){ document.getElementById(id).classList.remove('show'); }
function showProgress(id){ const el=document.getElementById(id); el.style.display='block'; }
function hideProgress(id){ const el=document.getElementById(id); el.style.display='none'; }
function closeSuccess(side){ document.getElementById(side+'-success').classList.remove('show'); }
 
function b64toBlob(b64Data, contentType='', sliceSize=512){
  const byteChars = atob(b64Data);
  const byteArrays = [];
  for(let offset=0;offset<byteChars.length;offset+=sliceSize){
    const slice = byteChars.slice(offset,offset+sliceSize);
    const byteNumbers = new Array(slice.length);
    for(let i=0;i<slice.length;i++) byteNumbers[i]=slice.charCodeAt(i);
    byteArrays.push(new Uint8Array(byteNumbers));
  }
  return new Blob(byteArrays,{type:contentType});
}
 
/* ═══════════════════════════════════
   SAVE LOG TO DB (silent)
═══════════════════════════════════ */
async function saveLog(action, filename, keyname, charCount){
  try{
    const fd=new FormData();
    fd.append('action',action);
    fd.append('filename',filename);
    fd.append('keyname',keyname);
    fd.append('char_count',charCount);
    await fetch('rsa_log.php',{method:'POST',body:fd});
  }catch(e){}
}
</script>
</body>
</html>

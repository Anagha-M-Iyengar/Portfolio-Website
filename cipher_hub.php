<?php
/**
 * cipher_hub.php — The Corsair's Cipher Hub
 * One page, four tabs: Rail Fence · Caesar · RSA/AES (OpenSSL) · Digital Signature
 * Gate: session visited_intro must be true
 */
session_start();
if (!isset($_SESSION['visited_intro']) || $_SESSION['visited_intro'] !== true) {
    header("Location: intro.php"); exit;
}

// OpenSSL path — detected server-side and passed to JS
$opensslBin = (strtoupper(substr(PHP_OS,0,3))==='WIN')
    ? 'C:\\xampp\\apache\\bin\\openssl.exe'
    : 'openssl';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>The Corsair's Cipher Hub</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Jost:wght@300;400;500;600&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
<style>
:root{
  --bg0:#060d16;--bg1:#0a1825;--bg2:#0d2236;
  --teal:#1a5c6e;--teal2:#2a8fa8;--teal3:#4ab8cc;
  --gold:#d4a843;--goldl:#f0cc70;--goldd:#8a6010;
  --stone:#b8a88a;--stonel:#e8dcc8;
  --mist:#d6eff8;--foam:#e8f6fc;
  --err:#c0392b;--ok:#00875a;
  --rf:#4a9cc7;--cs:#9b59b6;--ossl:#d4a843;--sig:#27ae60;
}
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'Jost',sans-serif;min-height:100vh;
  background:linear-gradient(170deg,var(--bg0) 0%,var(--bg1) 40%,var(--bg2) 70%,var(--bg0) 100%);
  color:var(--stonel);overflow-x:hidden;}
body::before{content:'';position:fixed;inset:0;pointer-events:none;z-index:0;
  background:radial-gradient(ellipse at 15% 25%,rgba(26,92,110,.2) 0%,transparent 45%),
             radial-gradient(ellipse at 85% 75%,rgba(42,143,168,.15) 0%,transparent 40%);}

/* ── HEADER ── */
.header{position:relative;z-index:10;text-align:center;padding:36px 20px 0;}
.hub-icon{font-size:2.6rem;display:block;margin-bottom:8px;animation:sway 3s ease-in-out infinite;}
@keyframes sway{0%,100%{transform:rotate(-4deg)}50%{transform:rotate(4deg)}}
.brand{font-family:'Cinzel',serif;font-size:clamp(1.6rem,5vw,2.8rem);font-weight:900;
  letter-spacing:.12em;
  background:linear-gradient(135deg,var(--stone),var(--goldl),var(--teal3),var(--goldl));
  background-size:300%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;
  background-clip:text;animation:shimmer 5s ease infinite;margin-bottom:4px;}
@keyframes shimmer{0%,100%{background-position:0%}50%{background-position:100%}}
.brand-sub{font-family:'Cinzel',serif;font-size:.65rem;letter-spacing:.3em;
  text-transform:uppercase;color:var(--teal3);margin-bottom:0;}

/* ── TAB NAV ── */
.tab-nav{
  display:flex;justify-content:center;flex-wrap:wrap;gap:6px;
  padding:20px 18px 0;position:relative;z-index:10;
  max-width:900px;margin:0 auto;
}
.tab-btn{
  display:flex;align-items:center;gap:7px;
  padding:9px 20px;border-radius:30px;
  border:1.5px solid rgba(255,255,255,.1);
  background:rgba(255,255,255,.04);
  font-family:'Cinzel',serif;font-size:.72rem;font-weight:600;
  letter-spacing:.08em;cursor:pointer;
  color:rgba(232,220,200,.5);
  transition:all .25s;
}
.tab-btn:hover{background:rgba(255,255,255,.08);color:var(--stonel);}
.tab-btn.active{color:#fff;border-color:var(--active-color,var(--gold));
  background:rgba(255,255,255,.07);
  box-shadow:0 0 20px rgba(255,255,255,.05);}
.tab-btn[data-tab="rf"].active{--active-color:var(--rf);color:var(--rf);}
.tab-btn[data-tab="caesar"].active{--active-color:var(--cs);color:var(--cs);}
.tab-btn[data-tab="ossl"].active{--active-color:var(--gold);color:var(--goldl);}
.tab-btn[data-tab="sig"].active{--active-color:var(--sig);color:#2ecc71;}
.tab-dot{width:8px;height:8px;border-radius:50%;background:currentColor;flex-shrink:0;}

/* ── TAB INDICATOR LINE ── */
.tab-line{height:2px;max-width:900px;margin:10px auto 0;
  background:rgba(255,255,255,.06);border-radius:2px;position:relative;z-index:10;
  overflow:hidden;}
.tab-line-fill{height:100%;border-radius:2px;
  transition:all .3s ease;background:var(--line-color,var(--gold));}

/* ── TAB PANELS ── */
.tab-panels{max-width:1100px;margin:0 auto;padding:24px 18px 48px;
  position:relative;z-index:10;}
.tab-panel{display:none;animation:fadeIn .3s ease;}
.tab-panel.active{display:block;}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}

/* ── PANEL HEADER ── */
.panel-header{text-align:center;margin-bottom:24px;}
.panel-title{font-family:'Cinzel',serif;font-size:1.3rem;font-weight:700;
  letter-spacing:.1em;margin-bottom:8px;}
.panel-desc{max-width:700px;margin:0 auto;font-size:.85rem;line-height:1.8;
  color:var(--stone);background:rgba(13,31,46,.5);
  border-radius:12px;padding:14px 20px;
  border:1px solid rgba(255,255,255,.06);}
.panel-desc strong{color:var(--goldl);}

/* ── TWO-COL LAYOUT ── */
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:22px;}
@media(max-width:750px){.two-col{grid-template-columns:1fr;}}

/* ── CARD ── */
.vault-card{background:rgba(8,15,24,.75);backdrop-filter:blur(12px);
  border:1px solid rgba(255,255,255,.07);border-radius:18px;overflow:hidden;
  box-shadow:0 12px 40px rgba(0,0,0,.4);}
.vault-card-head{padding:15px 20px 12px;
  background:rgba(13,31,46,.6);border-bottom:1px solid rgba(255,255,255,.05);}
.vault-card-title{font-family:'Cinzel',serif;font-size:.9rem;font-weight:700;
  letter-spacing:.1em;display:flex;align-items:center;gap:9px;}
.vault-card-body{padding:20px;}

/* ── FORM ELEMENTS (shared) ── */
.field-label{display:block;font-size:.66rem;font-weight:700;letter-spacing:.1em;
  text-transform:uppercase;color:var(--teal3);margin-bottom:6px;margin-top:14px;}
.field-label:first-child{margin-top:0;}
.field-label .req{color:var(--gold);}
textarea.vi,input.vi{width:100%;background:rgba(5,12,22,.7);
  border:1.5px solid rgba(255,255,255,.1);border-radius:10px;color:var(--stonel);
  font-family:'Courier Prime',monospace;font-size:.87rem;
  padding:10px 12px;outline:none;transition:border-color .2s;resize:vertical;}
textarea.vi{min-height:90px;}
textarea.vi:focus,input.vi:focus{border-color:var(--gold);}
textarea.vi::placeholder,input.vi::placeholder{color:rgba(184,168,138,.3);}
.key-row{display:flex;align-items:center;gap:8px;margin-top:0;}
.key-num{width:80px;flex-shrink:0;background:rgba(5,12,22,.7);
  border:1.5px solid rgba(255,255,255,.1);border-radius:10px;color:var(--stonel);
  font-family:'Courier Prime',monospace;font-size:1rem;font-weight:700;
  padding:9px 11px;text-align:center;outline:none;transition:border-color .2s;}
.key-num:focus{border-color:var(--gold);}
.key-label{font-size:.78rem;color:var(--stone);}
.key-label small{display:block;font-size:.67rem;color:rgba(184,168,138,.5);margin-top:1px;}
.upload-zone{border:1.5px dashed rgba(255,255,255,.12);border-radius:10px;
  padding:12px 14px;text-align:center;cursor:pointer;
  background:rgba(5,12,22,.4);transition:all .2s;font-size:.78rem;color:var(--teal3);}
.upload-zone:hover{border-color:var(--teal3);background:rgba(74,184,204,.05);}
.upload-zone input[type=file]{display:none;}
.uz-icon{font-size:1.3rem;display:block;margin-bottom:3px;}
.uz-name{margin-top:4px;font-size:.71rem;color:var(--goldl);word-break:break-all;}
.key-zone{border:1.5px dashed rgba(212,168,67,.25);border-radius:10px;
  padding:12px 14px;text-align:center;cursor:pointer;
  background:rgba(5,12,22,.4);transition:all .2s;font-size:.78rem;color:var(--gold);}
.key-zone:hover{border-color:var(--gold);background:rgba(212,168,67,.05);}
.key-zone input[type=file]{display:none;}
.key-name{margin-top:4px;font-size:.71rem;color:var(--goldl);word-break:break-all;}
.filename-row{display:flex;align-items:center;gap:0;}
.filename-prefix{font-family:'Courier Prime',monospace;font-size:.8rem;color:var(--gold);
  font-weight:700;white-space:nowrap;padding:9px 10px;
  background:rgba(212,168,67,.08);border:1.5px solid rgba(212,168,67,.18);
  border-right:none;border-radius:10px 0 0 10px;}
.filename-input{flex:1;background:rgba(5,12,22,.7);border:1.5px solid rgba(212,168,67,.18);
  border-left:none;border-radius:0 10px 10px 0;color:var(--stonel);
  font-family:'Courier Prime',monospace;font-size:.87rem;padding:9px 12px;outline:none;}
.filename-input:focus{border-color:var(--gold);}
.filename-ext{font-size:.7rem;color:rgba(184,168,138,.45);margin-top:4px;padding-left:2px;}
.pass-field{position:relative;}
.pass-field input{padding-right:36px;}
.pass-eye{position:absolute;right:10px;top:50%;transform:translateY(-50%);
  cursor:pointer;font-size:.9rem;color:var(--stone);}
.format-row{display:flex;gap:8px;flex-wrap:wrap;}
.fmt-opt{display:none;}
.fmt-opt+label{padding:6px 14px;border-radius:20px;
  border:1.5px solid rgba(74,184,204,.2);font-size:.75rem;font-weight:600;
  color:var(--stone);cursor:pointer;transition:all .2s;background:rgba(5,12,22,.4);}
.fmt-opt:checked+label{background:rgba(74,184,204,.12);border-color:var(--teal3);color:var(--teal3);}

/* ── BUTTONS ── */
.btn-action{display:block;width:100%;margin-top:16px;padding:12px 20px;border:none;
  border-radius:12px;font-family:'Cinzel',serif;font-size:.85rem;font-weight:700;
  letter-spacing:.09em;cursor:pointer;transition:transform .2s,box-shadow .2s,opacity .2s;}
.btn-action:disabled{opacity:.4;cursor:not-allowed;transform:none!important;}
.btn-gold{background:linear-gradient(135deg,var(--goldd),var(--gold),#e8b830);
  color:#0a1020;box-shadow:0 5px 20px rgba(212,168,67,.3);}
.btn-gold:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 8px 28px rgba(212,168,67,.45);}
.btn-teal{background:linear-gradient(135deg,var(--teal),var(--teal2),var(--teal3));
  color:#fff;box-shadow:0 5px 20px rgba(42,143,168,.3);}
.btn-teal:hover:not(:disabled){transform:translateY(-2px);box-shadow:0 8px 28px rgba(42,143,168,.45);}
.btn-rf{background:linear-gradient(135deg,#2a6a8a,var(--rf));color:#fff;}
.btn-rf:hover:not(:disabled){transform:translateY(-2px);}
.btn-cs{background:linear-gradient(135deg,#6c3483,var(--cs));color:#fff;}
.btn-cs:hover:not(:disabled){transform:translateY(-2px);}
.btn-sig{background:linear-gradient(135deg,#1a7a40,var(--sig));color:#fff;}
.btn-sig:hover:not(:disabled){transform:translateY(-2px);}

/* ── RESULT / ERROR ── */
.result-card{margin-top:12px;background:rgba(5,12,22,.7);border:1px solid rgba(255,255,255,.08);
  border-radius:12px;overflow:hidden;display:none;}
.result-card.show{display:block;animation:popIn .35s cubic-bezier(.34,1.4,.64,1);}
@keyframes popIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
.result-head{background:rgba(13,31,46,.5);padding:8px 14px;display:flex;
  align-items:center;justify-content:space-between;border-bottom:1px solid rgba(255,255,255,.06);}
.result-title{font-family:'Cinzel',serif;font-size:.75rem;letter-spacing:.1em;color:var(--teal3);}
.copy-btn{background:rgba(74,184,204,.12);border:1px solid rgba(74,184,204,.25);
  border-radius:20px;padding:3px 10px;font-size:.68rem;font-weight:600;
  color:var(--teal3);cursor:pointer;transition:background .2s;}
.copy-btn:hover{background:rgba(74,184,204,.25);}
.result-text{padding:12px 14px;font-family:'Courier Prime',monospace;font-size:.85rem;
  color:var(--goldl);line-height:1.7;word-break:break-all;white-space:pre-wrap;
  max-height:160px;overflow-y:auto;}
.save-status{font-size:.69rem;color:var(--teal3);margin-top:5px;min-height:14px;font-style:italic;}
.err-msg{margin-top:8px;background:rgba(192,57,43,.12);border:1px solid rgba(192,57,43,.28);
  border-radius:10px;padding:8px 12px;font-size:.76rem;color:#ff8a80;display:none;}
.err-msg.show{display:block;}
.progress-bar{height:3px;background:rgba(255,255,255,.05);border-radius:3px;
  margin-top:8px;overflow:hidden;display:none;}
.progress-fill{height:100%;border-radius:3px;background:linear-gradient(to right,var(--gold),var(--teal3),var(--gold));
  background-size:200%;animation:progressAnim 1.5s linear infinite;}
@keyframes progressAnim{0%{background-position:0%}100%{background-position:200%}}

/* ── SUCCESS OVERLAY ── */
.success-overlay{display:none;position:fixed;inset:0;
  background:rgba(5,12,22,.93);backdrop-filter:blur(8px);
  z-index:9998;align-items:center;justify-content:center;}
.success-overlay.show{display:flex;}
.success-card{background:linear-gradient(160deg,rgba(13,31,46,.98),rgba(8,15,24,.98));
  border:1px solid rgba(212,168,67,.25);border-radius:22px;padding:40px 44px;
  max-width:460px;width:90%;text-align:center;position:relative;
  box-shadow:0 24px 80px rgba(0,0,0,.6);
  animation:cardBounce .5s cubic-bezier(.34,1.4,.64,1);}
@keyframes cardBounce{from{opacity:0;transform:scale(.8) translateY(30px)}to{opacity:1;transform:scale(1) translateY(0)}}
.success-card::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;
  background:linear-gradient(to right,transparent,var(--gold),var(--teal3),var(--gold),transparent);
  border-radius:22px 22px 0 0;}
.success-pirate{font-size:3rem;display:block;margin-bottom:12px;
  animation:pirateWave 2s ease-in-out infinite;}
@keyframes pirateWave{0%,100%{transform:rotate(-3deg)}50%{transform:rotate(3deg)}}
.success-title{font-family:'Cinzel',serif;font-size:1.3rem;font-weight:700;
  color:var(--goldl);margin-bottom:8px;letter-spacing:.08em;}
.success-msg{font-size:.83rem;color:var(--stone);line-height:1.7;margin-bottom:22px;}
.success-msg strong{color:var(--stonel);}
.btn-download{display:inline-flex;align-items:center;gap:8px;
  background:linear-gradient(135deg,var(--goldd),var(--gold));color:#0a1020;
  border:none;padding:12px 28px;border-radius:50px;font-family:'Cinzel',serif;
  font-size:.83rem;font-weight:700;letter-spacing:.07em;cursor:pointer;
  box-shadow:0 5px 20px rgba(212,168,67,.35);transition:transform .2s;text-decoration:none;}
.btn-download:hover{transform:translateY(-2px);}
.btn-close-ok{display:block;margin:12px auto 0;background:none;
  border:1px solid rgba(184,168,138,.2);border-radius:20px;
  padding:6px 18px;font-size:.76rem;color:var(--stone);cursor:pointer;}
.btn-close-ok:hover{border-color:var(--stone);}

/* ── SIGNATURE RESULT ── */
.sig-result-box{background:rgba(5,12,22,.7);border:1px solid rgba(39,174,96,.2);
  border-radius:12px;padding:14px;margin-top:12px;display:none;}
.sig-result-box.show{display:block;}
.sig-hash{font-family:'Courier Prime',monospace;font-size:.78rem;color:#2ecc71;
  word-break:break-all;line-height:1.6;}
.sig-status{font-size:.85rem;font-weight:700;margin-bottom:8px;}
.sig-ok{color:#2ecc71;}.sig-fail{color:#e74c3c;}

/* ── FOOTER ── */
.hub-footer{text-align:center;padding:16px;font-size:.7rem;
  color:rgba(184,168,138,.3);letter-spacing:.06em;}
.hub-footer a{color:var(--teal2);text-decoration:none;}

/* ── TOAST ── */
.toast{position:fixed;bottom:24px;left:50%;transform:translateX(-50%) translateY(16px);
  background:rgba(0,135,90,.92);color:#fff;font-size:.78rem;font-weight:600;
  padding:7px 18px;border-radius:30px;opacity:0;pointer-events:none;
  transition:opacity .25s,transform .25s;z-index:9999;}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0);}
</style>
</head>
<body>

<!-- ══ HEADER ══ -->
<header class="header">
  <span class="hub-icon">🏴‍☠️</span>
  <h1 class="brand">The Corsair's Cipher Hub</h1>
  <p class="brand-sub">⚓ Rail Fence · Caesar · RSA/AES · Digital Signature ⚓</p>
</header>

<!-- ══ TAB NAV ══ -->
<nav class="tab-nav">
  <button class="tab-btn active" data-tab="rf">
    <span class="tab-dot"></span>🚂 Rail Fence
  </button>
  <button class="tab-btn" data-tab="caesar">
    <span class="tab-dot"></span>⚔️ Caesar Cipher
  </button>
  <button class="tab-btn" data-tab="ossl">
    <span class="tab-dot"></span>📜 RSA · AES (OpenSSL)
  </button>
  <button class="tab-btn" data-tab="sig">
    <span class="tab-dot"></span>🖊️ Digital Signature
  </button>
</nav>
<div class="tab-line"><div class="tab-line-fill" id="tabLine" style="width:25%;background:var(--rf);"></div></div>

<!-- ══ TAB PANELS ══ -->
<div class="tab-panels">

  <!-- ═══════════════════════ RAIL FENCE TAB ═══════════════════════ -->
  <div class="tab-panel active" id="panel-rf">
    <div class="panel-header">
      <div class="panel-title" style="color:var(--rf);">🚂 Rail Fence Cipher — XoX_Pirate</div>
      <div class="panel-desc">
        The <strong>Rail Fence Cipher</strong> is a classical transposition cipher where characters zigzag
        across a set number of rails, then are read off row by row to produce the cipher text.
        Choose a key (number of rails) smaller than your message length for meaningful scrambling.
      </div>
    </div>
    <div class="two-col">
      <!-- RF Encrypt -->
      <div class="vault-card">
        <div class="vault-card-head"><div class="vault-card-title" style="color:#f0cc70;">🔐 Encrypt</div></div>
        <div class="vault-card-body">
          <label class="field-label">Message</label>
          <textarea class="vi" id="rf-enc-input" placeholder="Type your message…" oninput="this.value=this.value.replace(/[^A-Za-z0-9 ]/g,'')"></textarea>
          <label class="field-label">Number of Rails (Key)</label>
          <div class="key-row">
            <input type="number" class="key-num" id="rf-enc-key" value="3" min="2" max="100">
            <div class="key-label">Rails <small>must be less than message length</small></div>
          </div>
          <button class="btn-action btn-rf" onclick="rfEncrypt()">🔐 Encrypt</button>
          <div class="err-msg" id="rf-enc-err"></div>
          <div class="result-card" id="rf-enc-result">
            <div class="result-head"><span class="result-title">⚓ Encrypted</span><button class="copy-btn" onclick="copyEl('rf-enc-out')">Copy</button></div>
            <div class="result-text" id="rf-enc-out"></div>
          </div>
          <!-- Zigzag diagram — shows HOW the Rail Fence worked -->
          <div id="rf-diagram" style="display:none;background:rgba(5,12,22,.6);border:1px solid rgba(74,156,199,.15);border-radius:10px;padding:12px 14px;margin-top:8px;"></div>
          <div class="save-status" id="rf-enc-ss"></div>
        </div>
      </div>
      <!-- RF Decrypt -->
      <div class="vault-card">
        <div class="vault-card-head"><div class="vault-card-title" style="color:var(--teal3);">🔓 Decrypt</div></div>
        <div class="vault-card-body">
          <label class="field-label">Encrypted Message</label>
          <textarea class="vi" id="rf-dec-input" placeholder="Paste encrypted text…" oninput="this.value=this.value.replace(/[^A-Za-z0-9 ]/g,'')"></textarea>
          <label class="field-label">Number of Rails (Key)</label>
          <div class="key-row">
            <input type="number" class="key-num" id="rf-dec-key" value="3" min="2" max="100">
            <div class="key-label">Rails <small>must match encrypt key</small></div>
          </div>
          <button class="btn-action btn-teal" onclick="rfDecrypt()">🔓 Decrypt</button>
          <div class="err-msg" id="rf-dec-err"></div>
          <div class="result-card" id="rf-dec-result">
            <div class="result-head"><span class="result-title">⚓ Decrypted</span><button class="copy-btn" onclick="copyEl('rf-dec-out')">Copy</button></div>
            <div class="result-text" id="rf-dec-out"></div>
          </div>
          <div class="save-status" id="rf-dec-ss"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════ CAESAR TAB ═══════════════════════ -->
  <div class="tab-panel" id="panel-caesar">
    <div class="panel-header">
      <div class="panel-title" style="color:var(--cs);">⚔️ Caesar Cipher — Caesar_Pirate</div>
      <div class="panel-desc">
        The <strong>Caesar Cipher</strong> shifts each letter of your message forward (encrypt) or backward
        (decrypt) by a fixed number of positions in the alphabet. Numbers and spaces pass through unchanged.
        With shift 3: A→D, B→E … Z→C.
      </div>
    </div>
    <div class="two-col">
      <!-- Caesar Encrypt -->
      <div class="vault-card">
        <div class="vault-card-head"><div class="vault-card-title" style="color:#f0cc70;">🔐 Encrypt</div></div>
        <div class="vault-card-body">
          <label class="field-label">Message</label>
          <textarea class="vi" id="cs-enc-input" placeholder="Type your message…" oninput="this.value=this.value.replace(/[^A-Za-z0-9 ]/g,'')"></textarea>
          <label class="field-label">Shift Key</label>
          <div class="key-row">
            <input type="number" class="key-num" id="cs-enc-key" value="3" min="1" max="100">
            <div class="key-label">Shift <small>1–100</small></div>
          </div>
          <button class="btn-action btn-cs" onclick="csEncrypt()">🔐 Encrypt</button>
          <div class="err-msg" id="cs-enc-err"></div>
          <div class="result-card" id="cs-enc-result">
            <div class="result-head"><span class="result-title">⚓ Encrypted</span><button class="copy-btn" onclick="copyEl('cs-enc-out')">Copy</button></div>
            <div class="result-text" id="cs-enc-out"></div>
          </div>
          <div class="save-status" id="cs-enc-ss"></div>
        </div>
      </div>
      <!-- Caesar Decrypt -->
      <div class="vault-card">
        <div class="vault-card-head"><div class="vault-card-title" style="color:var(--teal3);">🔓 Decrypt</div></div>
        <div class="vault-card-body">
          <label class="field-label">Encrypted Message</label>
          <textarea class="vi" id="cs-dec-input" placeholder="Paste encrypted text…" oninput="this.value=this.value.replace(/[^A-Za-z0-9 ]/g,'')"></textarea>
          <label class="field-label">Shift Key</label>
          <div class="key-row">
            <input type="number" class="key-num" id="cs-dec-key" value="3" min="1" max="100">
            <div class="key-label">Shift <small>must match encrypt key</small></div>
          </div>
          <button class="btn-action btn-teal" onclick="csDecrypt()">🔓 Decrypt</button>
          <div class="err-msg" id="cs-dec-err"></div>
          <div class="result-card" id="cs-dec-result">
            <div class="result-head"><span class="result-title">⚓ Decrypted</span><button class="copy-btn" onclick="copyEl('cs-dec-out')">Copy</button></div>
            <div class="result-text" id="cs-dec-out"></div>
          </div>
          <div class="save-status" id="cs-dec-ss"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════ OPENSSL TAB ═══════════════════════ -->
  <div class="tab-panel" id="panel-ossl">
    <div class="panel-header">
      <div class="panel-title" style="color:var(--goldl);">📜 Poneglyph Vault — RSA · AES · OpenSSL</div>
      <div class="panel-desc">
        Every corsair carries a unique seal — no two alike, no two interchangeable.
        <strong>RSA</strong> is an asymmetric cipher: a mathematically linked key pair where your
        <strong>Public Key</strong> locks the chest and only your <strong>Private Key</strong> can open it.
        For large messages, <strong>AES-256</strong> encrypts the content at speed, while RSA secures the AES key itself —
        the same hybrid method used in real-world secure communications. Powered by <strong>OpenSSL</strong>.
      </div>
    </div>
    <div class="two-col">
      <!-- OSSL Encrypt -->
      <div class="vault-card">
        <div class="vault-card-head"><div class="vault-card-title" style="color:#f0cc70;">🔐 Encrypt with Public Key</div></div>
        <div class="vault-card-body">
          <label class="field-label">Message to Encrypt</label>
          <textarea class="vi" id="ossl-enc-text" placeholder="Type your message… or upload a file below."
            oninput="this.value=this.value.replace(/[<>]/g,'');osslCheckEnc()"></textarea>

          <label class="field-label" style="margin-top:10px;">— OR — Upload File (.txt · .pdf · .doc)</label>
          <div class="upload-zone" onclick="document.getElementById('ossl-enc-file').click()">
            <input type="file" id="ossl-enc-file" accept=".txt,.pdf,.doc,.docx"
                   onchange="osslHandleMsgFile(event)">
            <span class="uz-icon">📂</span><span>Browse .txt · .pdf · .doc</span>
            <div class="uz-name" id="ossl-enc-fname"></div>
          </div>

          <label class="field-label" style="margin-top:12px;">Upload Receiver's Public Key <span class="req">*</span></label>
          <div class="key-zone" onclick="document.getElementById('ossl-enc-pubkey').click()">
            <input type="file" id="ossl-enc-pubkey" accept=".key"
                   onchange="osslHandlePubKey(event)">
            <span class="uz-icon">🗝️</span><span>Upload <strong>.key</strong> file only</span>
            <div class="key-name" id="ossl-enc-keyname"></div>
          </div>

          <label class="field-label" style="margin-top:12px;">Save Encrypted File As</label>
          <div class="filename-row">
            <span class="filename-prefix">encrypted_</span>
            <input type="text" class="filename-input" id="ossl-enc-savename"
                   placeholder="filename" maxlength="40"
                   oninput="this.value=this.value.replace(/[^A-Za-z0-9_\-]/g,'');updateEncPreview()">
          </div>
          <div class="filename-ext">→ <span id="ossl-enc-preview">encrypted_filename.bin</span></div>

          <button class="btn-action btn-gold" id="ossl-enc-btn" onclick="osslEncrypt()" disabled>
            🔐 Encrypt with Public Key
          </button>
          <div class="progress-bar" id="ossl-enc-prog"><div class="progress-fill"></div></div>
          <div class="err-msg" id="ossl-enc-err"></div>
        </div>
      </div>

      <!-- OSSL Decrypt -->
      <div class="vault-card">
        <div class="vault-card-head"><div class="vault-card-title" style="color:var(--teal3);">🔓 Decrypt with Private Key</div></div>
        <div class="vault-card-body">
          <label class="field-label">Upload Encrypted File (.bin) <span class="req">*</span></label>
          <div class="upload-zone" onclick="document.getElementById('ossl-dec-bin').click()">
            <input type="file" id="ossl-dec-bin" accept=".bin"
                   onchange="osslHandleBin(event)">
            <span class="uz-icon">📦</span><span>Upload <strong>.bin</strong> encrypted file</span>
            <div class="uz-name" id="ossl-dec-fname"></div>
          </div>

          <label class="field-label" style="margin-top:12px;">Upload Your Private Key <span class="req">*</span></label>
          <div class="key-zone" onclick="document.getElementById('ossl-dec-privkey').click()">
            <input type="file" id="ossl-dec-privkey" accept=".key"
                   onchange="osslHandlePrivKey(event)">
            <span class="uz-icon">🗝️</span><span>Upload <strong>.key</strong> private key</span>
            <div class="key-name" id="ossl-dec-keyname"></div>
          </div>

          <label class="field-label" style="margin-top:12px;">Private Key Passphrase</label>
          <p style="font-size:.68rem;color:rgba(184,168,138,.5);margin-bottom:6px;">
            Only if your key was generated with <code style="color:var(--goldl);">-aes256</code> password protection — leave blank otherwise
          </p>
          <div class="pass-field">
            <input type="password" class="vi" id="ossl-dec-pass"
                   placeholder="Enter passphrase if key is password-protected…"
                   style="min-height:unset;height:42px;resize:none;">
            <span class="pass-eye" onclick="togglePass('ossl-dec-pass',this)">👁️</span>
          </div>

          <label class="field-label" style="margin-top:12px;">Save Decrypted File As</label>
          <div class="filename-row">
            <span class="filename-prefix">decrypted_</span>
            <input type="text" class="filename-input" id="ossl-dec-savename"
                   placeholder="filename" maxlength="40"
                   oninput="this.value=this.value.replace(/[^A-Za-z0-9_\-]/g,'');updateDecPreview()">
          </div>

          <label class="field-label" style="margin-top:12px;">Output Format</label>
          <div class="format-row">
            <input type="radio" name="dec-fmt" id="fmt-txt" value="txt" class="fmt-opt" checked>
            <label for="fmt-txt">.txt</label>
            <input type="radio" name="dec-fmt" id="fmt-pdf" value="pdf" class="fmt-opt">
            <label for="fmt-pdf">.pdf</label>
            <input type="radio" name="dec-fmt" id="fmt-doc" value="doc" class="fmt-opt">
            <label for="fmt-doc">.doc</label>
          </div>
          <div class="filename-ext">→ <span id="ossl-dec-preview">decrypted_filename.txt</span></div>

          <button class="btn-action btn-teal" id="ossl-dec-btn" onclick="osslDecrypt()" disabled>
            🔓 Decrypt with Private Key
          </button>
          <div class="progress-bar" id="ossl-dec-prog"><div class="progress-fill"></div></div>
          <div class="err-msg" id="ossl-dec-err"></div>
        </div>
      </div>
    </div>
  </div>

  <!-- ═══════════════════════ DIGITAL SIGNATURE TAB ═══════════════════════ -->
  <div class="tab-panel" id="panel-sig">
    <div class="panel-header">
      <div class="panel-title" style="color:#2ecc71;">🖊️ Digital Signature — Integrity Vault</div>
      <div class="panel-desc">
        A <strong>digital signature</strong> proves a message has not been tampered with in transit.
        When you sign a file, it is <strong>hashed</strong> using SHA-256 (a unique fingerprint of the content)
        and that hash is then <strong>encrypted with your private key</strong>.
        The receiver uses your <strong>public key to decrypt the signature</strong> and recomputes the hash
        from the received file — if both hashes match, the file is authentic and unaltered.
        One changed byte = completely different hash = forgery detected.
      </div>
    </div>
    <div class="two-col">
      <!-- Sign -->
      <div class="vault-card">
        <div class="vault-card-head"><div class="vault-card-title" style="color:#2ecc71;">🖊️ Sign a File</div></div>
        <div class="vault-card-body">
          <label class="field-label">File to Sign <span class="req">*</span></label>
          <div class="upload-zone" onclick="document.getElementById('sig-file').click()">
            <input type="file" id="sig-file" accept=".txt,.pdf,.doc,.docx,.bin"
                   onchange="sigHandleFile(event,'sig-fname');sigCheckReady()">
            <span class="uz-icon">📄</span><span>Upload any file to sign</span>
            <div class="uz-name" id="sig-fname"></div>
          </div>

          <label class="field-label" style="margin-top:12px;">Your Private Key <span class="req">*</span></label>
          <div class="key-zone" onclick="document.getElementById('sig-privkey').click()">
            <input type="file" id="sig-privkey" accept=".key"
                   onchange="sigHandleKey(event,'sig-keyname');sigCheckReady()">
            <span class="uz-icon">🗝️</span><span>Upload <strong>.key</strong> private key</span>
            <div class="key-name" id="sig-keyname"></div>
          </div>

          <label class="field-label" style="margin-top:12px;">Private Key Passphrase</label>
          <div class="pass-field">
            <input type="password" class="vi" id="sig-pass"
                   placeholder="Leave blank if not password-protected…"
                   style="min-height:unset;height:42px;resize:none;">
            <span class="pass-eye" onclick="togglePass('sig-pass',this)">👁️</span>
          </div>

          <button class="btn-action btn-sig" id="sig-sign-btn" onclick="runSign()" disabled>
            🖊️ Sign File
          </button>
          <div class="progress-bar" id="sig-sign-prog"><div class="progress-fill" style="background:linear-gradient(to right,#1a7a40,#2ecc71,#1a7a40);background-size:200%"></div></div>
          <div class="err-msg" id="sig-sign-err"></div>

          <div class="sig-result-box" id="sig-sign-result">
            <div class="sig-status sig-ok">✅ File signed successfully</div>
            <div style="font-size:.72rem;color:var(--stone);margin-bottom:6px;">SHA-256 hash of your file:</div>
            <div class="sig-hash" id="sig-hash-out"></div>
            <a href="#" class="btn-download" id="sig-dl-btn" download style="margin-top:14px;font-size:.78rem;padding:9px 22px;">⬇️ Download Signature (.sig)</a>
          </div>
        </div>
      </div>

      <!-- Verify -->
      <div class="vault-card">
        <div class="vault-card-head"><div class="vault-card-title" style="color:var(--teal3);">🔍 Verify a Signature</div></div>
        <div class="vault-card-body">
          <label class="field-label">Original File <span class="req">*</span></label>
          <div class="upload-zone" onclick="document.getElementById('ver-file').click()">
            <input type="file" id="ver-file" accept=".txt,.pdf,.doc,.docx,.bin"
                   onchange="sigHandleFile(event,'ver-fname');verCheckReady()">
            <span class="uz-icon">📄</span><span>Upload the original file</span>
            <div class="uz-name" id="ver-fname"></div>
          </div>

          <label class="field-label" style="margin-top:12px;">Signature File (.sig) <span class="req">*</span></label>
          <div class="upload-zone" onclick="document.getElementById('ver-sig').click()">
            <input type="file" id="ver-sig" accept=".sig"
                   onchange="sigHandleFile(event,'ver-signame');verCheckReady()">
            <span class="uz-icon">🖊️</span><span>Upload <strong>.sig</strong> signature file</span>
            <div class="uz-name" id="ver-signame"></div>
          </div>

          <label class="field-label" style="margin-top:12px;">Sender's Public Key <span class="req">*</span></label>
          <div class="key-zone" onclick="document.getElementById('ver-pubkey').click()">
            <input type="file" id="ver-pubkey" accept=".key"
                   onchange="sigHandleKey(event,'ver-keyname');verCheckReady()">
            <span class="uz-icon">🗝️</span><span>Upload <strong>.key</strong> public key</span>
            <div class="key-name" id="ver-keyname"></div>
          </div>

          <button class="btn-action btn-teal" id="ver-btn" onclick="runVerify()" disabled>
            🔍 Verify Integrity
          </button>
          <div class="progress-bar" id="ver-prog"><div class="progress-fill"></div></div>
          <div class="err-msg" id="ver-err"></div>

          <div class="sig-result-box" id="ver-result">
            <div class="sig-status" id="ver-status"></div>
            <div style="font-size:.72rem;color:var(--stone);margin-bottom:6px;">Recomputed SHA-256 hash:</div>
            <div class="sig-hash" id="ver-hash-out"></div>
          </div>
        </div>
      </div>
    </div>
  </div>

</div><!-- /tab-panels -->

<!-- ══ SUCCESS OVERLAY ══ -->
<div class="success-overlay" id="successOverlay">
  <div class="success-card">
    <span class="success-pirate" id="successEmoji">🏴‍☠️</span>
    <div class="success-title" id="successTitle">Done!</div>
    <div class="success-msg" id="successMsg"></div>
    <a href="#" class="btn-download" id="successDlBtn" download>⬇️ Download</a>
    <button class="btn-close-ok" onclick="closeSuccess()">← Back to Vault</button>
  </div>
</div>

<div class="hub-footer">
  🏴‍☠️ The Corsair's Cipher Hub &nbsp;·&nbsp; <a href="index.php">← Back to Portfolio</a>
  &nbsp;·&nbsp; Rail Fence · Caesar · RSA-2048 · AES-256 · SHA-256
</div>
<div class="toast" id="toast">✅ Copied!</div>

<script>
// ════════════════════════════════════════════════
// TAB SWITCHING
// ════════════════════════════════════════════════
const tabColors = {rf:'#4a9cc7', caesar:'#9b59b6', ossl:'#d4a843', sig:'#27ae60'};
const tabWidths = {rf:'25%', caesar:'50%', ossl:'75%', sig:'100%'};

document.querySelectorAll('.tab-btn').forEach(btn => {
  btn.addEventListener('click', () => {
    document.querySelectorAll('.tab-btn').forEach(b=>b.classList.remove('active'));
    document.querySelectorAll('.tab-panel').forEach(p=>p.classList.remove('active'));
    btn.classList.add('active');
    const tab = btn.dataset.tab;
    document.getElementById('panel-'+tab).classList.add('active');
    const line = document.getElementById('tabLine');
    line.style.width  = tabWidths[tab];
    line.style.background = tabColors[tab];
  });
});

// ════════════════════════════════════════════════
// HELPERS
// ════════════════════════════════════════════════
function showErr(id,msg){ const e=document.getElementById(id); e.textContent='⚠️ '+msg; e.classList.add('show'); }
function hideErr(id){ document.getElementById(id).classList.remove('show'); }
function showProg(id){ document.getElementById(id).style.display='block'; }
function hideProg(id){ document.getElementById(id).style.display='none'; }
function copyEl(id){
  const t=document.getElementById(id).textContent;
  navigator.clipboard.writeText(t).then(()=>{
    const toast=document.getElementById('toast'); toast.classList.add('show');
    setTimeout(()=>toast.classList.remove('show'),2000);
  });
}
function b64toBlob(b64,type='application/octet-stream'){
  const bytes=atob(b64); const arr=[];
  for(let i=0;i<bytes.length;i+=512){
    const sl=bytes.slice(i,i+512);
    const nums=new Array(sl.length);
    for(let j=0;j<sl.length;j++) nums[j]=sl.charCodeAt(j);
    arr.push(new Uint8Array(nums));
  }
  return new Blob(arr,{type});
}
function togglePass(id,el){
  const inp=document.getElementById(id);
  inp.type=inp.type==='password'?'text':'password';
  el.textContent=inp.type==='password'?'👁️':'🙈';
}
function closeSuccess(){ document.getElementById('successOverlay').classList.remove('show'); }
function showSuccess(emoji,title,msg,dlUrl,dlName){
  document.getElementById('successEmoji').textContent=emoji;
  document.getElementById('successTitle').textContent=title;
  document.getElementById('successMsg').innerHTML=msg;
  const btn=document.getElementById('successDlBtn');
  if(dlUrl){ btn.href=dlUrl; btn.download=dlName; btn.style.display='inline-flex'; }
  else { btn.style.display='none'; }
  document.getElementById('successOverlay').classList.add('show');
}
async function saveLog(endpoint, data){
  try{
    const fd=new FormData();
    Object.keys(data).forEach(k=>fd.append(k,data[k]));
    await fetch(endpoint,{method:'POST',body:fd});
  }catch(e){}
}

// ════════════════════════════════════════════════
// RAIL FENCE ALGORITHM
// Algorithm functions named with _algo suffix to
// avoid clashing with the UI button handler functions
// ════════════════════════════════════════════════
function rfEncryptAlgo(text, rails){
  if(rails<2) return text;
  const fence=Array.from({length:rails},()=>[]);
  let r=0,d=1;
  for(let i=0;i<text.length;i++){
    fence[r].push(text[i]);
    if(r===0)d=1; else if(r===rails-1)d=-1;
    r+=d;
  }
  return fence.map(row=>row.join('')).join('');
}
function rfDecryptAlgo(cipher, rails){
  if(rails<2) return cipher;
  const n=cipher.length;
  const pat=new Array(n);
  let r=0,d=1;
  for(let i=0;i<n;i++){
    pat[i]=r;
    if(r===0)d=1; else if(r===rails-1)d=-1;
    r+=d;
  }
  const lens=new Array(rails).fill(0);
  for(let i=0;i<n;i++) lens[pat[i]]++;
  const rows=[]; let idx=0;
  for(let row=0;row<rails;row++){ rows.push(cipher.slice(idx,idx+lens[row]).split('')); idx+=lens[row]; }
  const ptrs=new Array(rails).fill(0);
  let res='';
  for(let i=0;i<n;i++){ const rr=pat[i]; res+=rows[rr][ptrs[rr]++]; }
  return res;
}

// UI button handler functions — called by onclick in HTML
function rfEncrypt(){
  hideErr('rf-enc-err');
  const text=document.getElementById('rf-enc-input').value.trim().replace(/ /g,'');
  const rails=parseInt(document.getElementById('rf-enc-key').value)||3;
  if(!text){ showErr('rf-enc-err','Please enter a message.'); return; }
  if(rails>=text.length){
    showErr('rf-enc-err','Key ('+rails+') must be less than message length ('+text.length+' chars). Try key ≤ '+Math.max(2,Math.floor(text.length/2))+'.');
    return;
  }
  const result=rfEncryptAlgo(text,rails);
  document.getElementById('rf-enc-out').textContent=result;
  document.getElementById('rf-enc-result').classList.add('show');
  // Also show the zigzag diagram below the result
  showRFDiagram(text, rails, result);
  saveLog('cipher_save.php',{action:'encrypt',rails,input_text:text,output_text:result,source:'typed'});
}
function rfDecrypt(){
  hideErr('rf-dec-err');
  const text=document.getElementById('rf-dec-input').value.trim().replace(/ /g,'');
  const rails=parseInt(document.getElementById('rf-dec-key').value)||3;
  if(!text){ showErr('rf-dec-err','Please enter a message.'); return; }
  const result=rfDecryptAlgo(text,rails);
  document.getElementById('rf-dec-out').textContent=result;
  document.getElementById('rf-dec-result').classList.add('show');
  saveLog('cipher_save.php',{action:'decrypt',rails,input_text:text,output_text:result,source:'typed'});
}

// Show zigzag diagram so user can see HOW Rail Fence worked
function showRFDiagram(text, rails, encrypted){
  const diagEl=document.getElementById('rf-diagram');
  if(!diagEl) return;
  const n=text.length;
  const cap=Math.min(n,20); // cap at 20 chars for display
  const sample=text.slice(0,cap);
  // Build grid
  const grid=Array.from({length:rails},()=>Array(cap).fill(''));
  let r=0,d=1;
  for(let i=0;i<cap;i++){
    grid[r][i]=sample[i];
    if(r===0)d=1; else if(r===rails-1)d=-1;
    r+=d;
  }
  let html='<div style="font-family:Courier Prime,monospace;font-size:.78rem;margin-top:10px;color:var(--stone);">';
  html+='<div style="color:var(--goldl);font-size:.72rem;margin-bottom:6px;">Zigzag pattern (first '+cap+' chars):</div>';
  for(let row=0;row<rails;row++){
    html+='<div style="letter-spacing:3px;">';
    for(let col=0;col<cap;col++){
      if(grid[row][col]){
        html+='<span style="color:var(--goldl);font-weight:700;">'+grid[row][col]+'</span>';
      } else {
        html+='<span style="color:rgba(184,168,138,.2);">·</span>';
      }
    }
    html+='</div>';
  }
  html+='<div style="margin-top:6px;color:var(--teal3);font-size:.72rem;">Encrypted: '+encrypted+'</div>';
  html+='</div>';
  diagEl.innerHTML=html;
  diagEl.style.display='block';
}

// ════════════════════════════════════════════════
// CAESAR ALGORITHM
// ════════════════════════════════════════════════
function caesarShift(text,shift){
  shift=((shift%26)+26)%26;
  let res='';
  for(const c of text){
    if(c>='A'&&c<='Z') res+=String.fromCharCode(((c.charCodeAt(0)-65+shift)%26)+65);
    else if(c>='a'&&c<='z') res+=String.fromCharCode(((c.charCodeAt(0)-97+shift)%26)+97);
    else res+=c;
  }
  return res;
}
function csEncrypt(){
  hideErr('cs-enc-err');
  const text=document.getElementById('cs-enc-input').value.trim();
  const shift=parseInt(document.getElementById('cs-enc-key').value)||3;
  if(!text){ showErr('cs-enc-err','Please enter a message.'); return; }
  const result=caesarShift(text,shift);
  document.getElementById('cs-enc-out').textContent=result;
  document.getElementById('cs-enc-result').classList.add('show');
  saveLog('caesar_save.php',{action:'encrypt',shift,input_text:text,output_text:result,source:'typed'});
}
function csDecrypt(){
  hideErr('cs-dec-err');
  const text=document.getElementById('cs-dec-input').value.trim();
  const shift=parseInt(document.getElementById('cs-dec-key').value)||3;
  if(!text){ showErr('cs-dec-err','Please enter a message.'); return; }
  const result=caesarShift(text,26-shift%26);
  document.getElementById('cs-dec-out').textContent=result;
  document.getElementById('cs-dec-result').classList.add('show');
  saveLog('caesar_save.php',{action:'decrypt',shift,input_text:text,output_text:result,source:'typed'});
}

// ════════════════════════════════════════════════
// OPENSSL (RSA/AES) — server-side via fetch
// ════════════════════════════════════════════════
let osslMsgFile=null, osslPubKey=null, osslBinFile=null, osslPrivKey=null;

function osslHandleMsgFile(e){
  const f=e.target.files[0]; if(!f) return;
  const fname=f.name.toLowerCase();
  const allowedExt=['.txt','.pdf','.doc','.docx'];
  if(!allowedExt.some(ext=>fname.endsWith(ext))){
    alert('Only .txt, .pdf, .doc, or .docx files are allowed.');
    e.target.value=''; return;
  }
  osslMsgFile=f;
  document.getElementById('ossl-enc-fname').textContent='📄 '+f.name;

  if(fname.endsWith('.txt')){
    // Read text file and display content in the textarea
    const reader=new FileReader();
    reader.onload=function(ev){
      document.getElementById('ossl-enc-text').value=ev.target.result.replace(/[<>]/g,'');
    };
    reader.readAsText(f);
  } else if(fname.endsWith('.pdf')){
    document.getElementById('ossl-enc-text').value='';
    document.getElementById('ossl-enc-text').placeholder='PDF loaded: '+f.name+' — extracting text…';
    if(!window.pdfjsLib){
      const s=document.createElement('script');
      s.src='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
      s.onload=()=>extractPDFForDisplay(f);
      document.head.appendChild(s);
    } else { extractPDFForDisplay(f); }
  } else {
    document.getElementById('ossl-enc-text').value='';
    document.getElementById('ossl-enc-text').placeholder='Document loaded: '+f.name+' (will encrypt the file directly)';
  }
  osslCheckEnc();
}

async function extractPDFForDisplay(file){
  try{
    pdfjsLib.GlobalWorkerOptions.workerSrc=
      'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    const ab=await file.arrayBuffer();
    const pdf=await pdfjsLib.getDocument({data:ab}).promise;
    let txt='';
    for(let p=1;p<=pdf.numPages;p++){
      const page=await pdf.getPage(p);
      const content=await page.getTextContent();
      txt+=content.items.map(i=>i.str).join(' ')+'\n';
    }
    document.getElementById('ossl-enc-text').value=txt.trim();
  }catch(ex){
    document.getElementById('ossl-enc-text').placeholder='PDF loaded: '+file.name+' (preview unavailable — encrypting raw file)';
  }
}
function osslHandlePubKey(e){
  const f=e.target.files[0]; if(!f) return;
  if(!f.name.endsWith('.key')){ alert('Only .key files allowed!'); e.target.value=''; return; }
  osslPubKey=f;
  document.getElementById('ossl-enc-keyname').textContent='🗝️ '+f.name;
  osslCheckEnc();
}
function osslHandleBin(e){
  const f=e.target.files[0]; if(!f) return;
  if(!f.name.endsWith('.bin')){ alert('Only .bin files allowed!'); e.target.value=''; return; }
  osslBinFile=f;
  document.getElementById('ossl-dec-fname').textContent='📦 '+f.name;
  osslCheckDec();
}
function osslHandlePrivKey(e){
  const f=e.target.files[0]; if(!f) return;
  if(!f.name.endsWith('.key')){ alert('Only .key files allowed!'); e.target.value=''; return; }
  osslPrivKey=f;
  document.getElementById('ossl-dec-keyname').textContent='🗝️ '+f.name;
  osslCheckDec();
}
function osslCheckEnc(){
  const hasMsg=osslMsgFile||document.getElementById('ossl-enc-text').value.trim();
  document.getElementById('ossl-enc-btn').disabled=!(hasMsg&&osslPubKey);
}
function osslCheckDec(){
  document.getElementById('ossl-dec-btn').disabled=!(osslBinFile&&osslPrivKey);
}
function updateEncPreview(){
  const n=document.getElementById('ossl-enc-savename').value.trim()||'filename';
  document.getElementById('ossl-enc-preview').textContent='encrypted_'+n+'.bin';
}
function updateDecPreview(){
  const n=document.getElementById('ossl-dec-savename').value.trim()||'filename';
  const f=document.querySelector('input[name="dec-fmt"]:checked').value;
  document.getElementById('ossl-dec-preview').textContent='decrypted_'+n+'.'+f;
}
document.querySelectorAll('input[name="dec-fmt"]').forEach(r=>r.addEventListener('change',updateDecPreview));
document.getElementById('ossl-enc-text').addEventListener('input',osslCheckEnc);

async function osslEncrypt(){
  hideErr('ossl-enc-err');
  const textMsg=document.getElementById('ossl-enc-text').value.trim();
  const saveName=document.getElementById('ossl-enc-savename').value.trim()||'message';
  if(!osslPubKey){ showErr('ossl-enc-err','Upload the receiver\'s public key (.key).'); return; }
  if(!textMsg&&!osslMsgFile){ showErr('ossl-enc-err','Type a message or upload a file.'); return; }
  const fd=new FormData();
  fd.append('pubkey',osslPubKey);
  fd.append('savename','encrypted_'+saveName);
  fd.append('msgfile', osslMsgFile||new Blob([textMsg],{type:'text/plain'}),'message.txt');
  showProg('ossl-enc-prog');
  document.getElementById('ossl-enc-btn').disabled=true;
  try{
    const res=await fetch('rsa_encrypt.php',{method:'POST',body:fd});
    const data=await res.json();
    hideProg('ossl-enc-prog');
    document.getElementById('ossl-enc-btn').disabled=false;
    if(data.status==='ok'){
      const blob=b64toBlob(data.file,'application/octet-stream');
      const url=URL.createObjectURL(blob);
      showSuccess('🔐','Message Sealed!',
        'Yer message has been locked with the public key, matey!<br>File: <strong>'+data.filename+'</strong>',
        url, data.filename);
      saveLog('rsa_log.php',{action:'encrypt',filename:data.filename,keyname:osslPubKey.name,char_count:data.char_count||0});
    } else { showErr('ossl-enc-err',data.message||'Encryption failed.'); }
  }catch(e){ hideProg('ossl-enc-prog'); document.getElementById('ossl-enc-btn').disabled=false; showErr('ossl-enc-err','Server error: '+e.message); }
}

async function osslDecrypt(){
  hideErr('ossl-dec-err');
  const saveName=document.getElementById('ossl-dec-savename').value.trim()||'message';
  const fmt=document.querySelector('input[name="dec-fmt"]:checked').value;
  const passphrase=document.getElementById('ossl-dec-pass').value;
  if(!osslBinFile){ showErr('ossl-dec-err','Upload an encrypted .bin file.'); return; }
  if(!osslPrivKey){ showErr('ossl-dec-err','Upload your private key (.key).'); return; }
  const fd=new FormData();
  fd.append('privkey',osslPrivKey);
  fd.append('binfile',osslBinFile);
  fd.append('savename','decrypted_'+saveName);
  fd.append('format',fmt);
  fd.append('passphrase',passphrase);
  showProg('ossl-dec-prog');
  document.getElementById('ossl-dec-btn').disabled=true;
  try{
    const res=await fetch('rsa_decrypt.php',{method:'POST',body:fd});
    const data=await res.json();
    hideProg('ossl-dec-prog');
    document.getElementById('ossl-dec-btn').disabled=false;
    if(data.status==='ok'){
      const mimes={txt:'text/plain',pdf:'application/pdf',doc:'application/msword'};
      const blob=b64toBlob(data.file,mimes[fmt]||'text/plain');
      const url=URL.createObjectURL(blob);
      showSuccess('🔓','Treasure Revealed!',
        'The seal is broken! Yer message has been decrypted.<br>File: <strong>'+data.filename+'</strong>',
        url, data.filename);
      saveLog('rsa_log.php',{action:'decrypt',filename:data.filename,keyname:osslPrivKey.name,char_count:data.char_count||0});
    } else { showErr('ossl-dec-err',data.message||'Decryption failed.'); }
  }catch(e){ hideProg('ossl-dec-prog'); document.getElementById('ossl-dec-btn').disabled=false; showErr('ossl-dec-err','Server error: '+e.message); }
}

// ════════════════════════════════════════════════
// DIGITAL SIGNATURE — server-side via fetch
// ════════════════════════════════════════════════
let sigFile=null, sigPrivKey=null, verFile=null, verSigFile=null, verPubKey=null;

function sigHandleFile(e,nameId){
  const f=e.target.files[0]; if(!f) return;
  if(nameId==='sig-fname') sigFile=f;
  if(nameId==='ver-fname') verFile=f;
  if(nameId==='ver-signame') verSigFile=f;
  document.getElementById(nameId).textContent='📄 '+f.name;
}
function sigHandleKey(e,nameId){
  const f=e.target.files[0]; if(!f) return;
  if(!f.name.endsWith('.key')){ alert('Only .key files allowed!'); e.target.value=''; return; }
  if(nameId==='sig-keyname') sigPrivKey=f;
  if(nameId==='ver-keyname') verPubKey=f;
  document.getElementById(nameId).textContent='🗝️ '+f.name;
}
function sigCheckReady(){ document.getElementById('sig-sign-btn').disabled=!(sigFile&&sigPrivKey); }
function verCheckReady(){ document.getElementById('ver-btn').disabled=!(verFile&&verSigFile&&verPubKey); }

async function runSign(){
  hideErr('sig-sign-err');
  document.getElementById('sig-sign-result').classList.remove('show');
  const pass=document.getElementById('sig-pass').value;
  const fd=new FormData();
  fd.append('file',sigFile);
  fd.append('privkey',sigPrivKey);
  fd.append('passphrase',pass);
  showProg('sig-sign-prog');
  document.getElementById('sig-sign-btn').disabled=true;
  try{
    const res=await fetch('sig_sign.php',{method:'POST',body:fd});
    const data=await res.json();
    hideProg('sig-sign-prog'); document.getElementById('sig-sign-btn').disabled=false;
    if(data.status==='ok'){
      document.getElementById('sig-hash-out').textContent=data.hash;
      const blob=b64toBlob(data.signature,'application/octet-stream');
      const url=URL.createObjectURL(blob);
      const dlBtn=document.getElementById('sig-dl-btn');
      dlBtn.href=url; dlBtn.download=sigFile.name+'.sig';
      document.getElementById('sig-sign-result').classList.add('show');
    } else { showErr('sig-sign-err',data.message||'Signing failed.'); }
  }catch(e){ hideProg('sig-sign-prog'); document.getElementById('sig-sign-btn').disabled=false; showErr('sig-sign-err','Server error: '+e.message); }
}

async function runVerify(){
  hideErr('ver-err');
  document.getElementById('ver-result').classList.remove('show');
  const fd=new FormData();
  fd.append('file',verFile);
  fd.append('signature',verSigFile);
  fd.append('pubkey',verPubKey);
  showProg('ver-prog');
  document.getElementById('ver-btn').disabled=true;
  try{
    const res=await fetch('sig_verify.php',{method:'POST',body:fd});
    const data=await res.json();
    hideProg('ver-prog'); document.getElementById('ver-btn').disabled=false;
    const box=document.getElementById('ver-result');
    const st=document.getElementById('ver-status');
    document.getElementById('ver-hash-out').textContent=data.hash||'';
    if(data.status==='ok'){
      st.className='sig-status sig-ok';
      st.textContent='✅ AUTHENTIC — Signature valid. File has not been tampered with.';
    } else {
      st.className='sig-status sig-fail';
      st.textContent='❌ INVALID — '+( data.message||'Signature does not match. File may be tampered.');
    }
    box.classList.add('show');
  }catch(e){ hideProg('ver-prog'); document.getElementById('ver-btn').disabled=false; showErr('ver-err','Server error: '+e.message); }
}
</script>
</body>
</html>

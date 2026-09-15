<?php
/**
 * tools.php — XoX_Pirate Cipher Tool
 * Rail Fence (Zigzag) Encryption & Decryption
 * Gate: must have visited intro (session visited_intro = true)
 */
session_start();

// Gate check — must go through intro first
if (!isset($_SESSION['visited_intro']) || $_SESSION['visited_intro'] !== true) {
    header("Location: intro.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>XoX_Pirate — Cipher Vault</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Jost:wght@300;400;500;600&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
<style>
/* ═══════════════════════════════════════════════
   ROOT & RESET
═══════════════════════════════════════════════ */
:root{
  --sea0:#0a1f2e;
  --sea1:#0d2b3e;
  --sea2:#1a4a6b;
  --sea3:#2a7fa8;
  --sea4:#4a9cc7;
  --sea5:#8ecae6;
  --foam:#c5e8f5;
  --mist:#e8f6fc;
  --ink:#0d2b3e;
  --pale:#d6eff8;
  --gold:#d4a843;
  --goldl:#f0cc70;
  --err:#c0392b;
  --ok:#00875a;
  --plank:#1e3a50;
}
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{
  font-family:'Jost',sans-serif;
  min-height:100vh;
  background:linear-gradient(170deg,#0a1825 0%,#0d2b3e 30%,#12374f 60%,#0a2033 100%);
  color:var(--mist);
  position:relative;
  overflow-x:hidden;
  padding-bottom:60px;
}

/* ── Starfield / wave background ── */
body::before{
  content:'';position:fixed;inset:0;pointer-events:none;z-index:0;
  background:
    radial-gradient(ellipse at 20% 80%,rgba(74,156,199,.12) 0%,transparent 55%),
    radial-gradient(ellipse at 80% 10%,rgba(42,127,168,.1) 0%,transparent 50%);
}
.wave-bg{position:fixed;bottom:0;left:0;right:0;height:180px;pointer-events:none;z-index:0;opacity:.18;}

/* ── Floating particles ── */
.particle{position:fixed;border-radius:50%;pointer-events:none;animation:floatUp linear infinite;opacity:0;}
@keyframes floatUp{0%{transform:translateY(0) scale(1);opacity:0}20%{opacity:.5}80%{opacity:.3}100%{transform:translateY(-100vh) scale(.4);opacity:0}}

/* ═══════════════════════════════════════════════
   HEADER
═══════════════════════════════════════════════ */
.header{
  position:relative;z-index:10;
  text-align:center;
  padding:52px 20px 36px;
}
.skull-icon{font-size:3.2rem;display:block;margin-bottom:10px;animation:sway 3s ease-in-out infinite;}
@keyframes sway{0%,100%{transform:rotate(-5deg)}50%{transform:rotate(5deg)}}
.brand{
  font-family:'Cinzel',serif;
  font-size:clamp(2rem,6vw,3.4rem);
  font-weight:900;
  letter-spacing:.12em;
  background:linear-gradient(135deg,var(--goldl),var(--gold),var(--sea5),var(--goldl));
  background-size:300% 100%;
  -webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;
  animation:shimmer 4s ease infinite;
  line-height:1;
  margin-bottom:6px;
}
@keyframes shimmer{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
.brand-sub{
  font-family:'Cinzel',serif;
  font-size:.75rem;letter-spacing:.3em;text-transform:uppercase;
  color:var(--sea4);margin-bottom:22px;
}
.desc-card{
  max-width:680px;margin:0 auto;
  background:rgba(26,74,107,.35);
  border:1px solid rgba(74,156,199,.25);
  border-radius:16px;
  padding:22px 28px;
  font-size:.92rem;
  line-height:1.85;
  color:var(--foam);
  position:relative;overflow:hidden;
}
.desc-card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:2px;
  background:linear-gradient(to right,transparent,var(--gold),var(--sea4),var(--gold),transparent);
}
.desc-card strong{color:var(--goldl);font-weight:600;}
.rf-badge{
  display:inline-block;margin-top:12px;
  background:rgba(74,156,199,.15);border:1px solid rgba(74,156,199,.3);
  border-radius:20px;padding:4px 14px;font-size:.76rem;color:var(--sea5);font-weight:600;letter-spacing:.05em;
}

/* ═══════════════════════════════════════════════
   MAIN LAYOUT
═══════════════════════════════════════════════ */
.vault{
  max-width:1100px;margin:0 auto;padding:0 18px;
  display:grid;grid-template-columns:1fr 1fr;gap:26px;
  position:relative;z-index:10;
}
@media(max-width:760px){.vault{grid-template-columns:1fr;}}

/* ═══════════════════════════════════════════════
   PANEL CARDS
═══════════════════════════════════════════════ */
.panel{
  background:rgba(13,43,62,.75);
  backdrop-filter:blur(12px);
  border:1px solid rgba(74,156,199,.2);
  border-radius:20px;
  overflow:hidden;
  box-shadow:0 12px 40px rgba(0,0,0,.35),0 2px 8px rgba(0,0,0,.2);
  animation:riseUp .6s cubic-bezier(.34,1.4,.64,1) both;
}
.panel:nth-child(2){animation-delay:.1s;}
@keyframes riseUp{from{opacity:0;transform:translateY(28px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}

.panel-head{
  padding:18px 22px 14px;
  border-bottom:1px solid rgba(74,156,199,.18);
  background:linear-gradient(135deg,rgba(26,74,107,.6),rgba(10,31,50,.4));
  position:relative;
}
.panel-head::after{
  content:'';position:absolute;bottom:0;left:0;right:0;height:1px;
  background:linear-gradient(to right,transparent,rgba(212,168,67,.4),transparent);
}
.panel-title{
  font-family:'Cinzel',serif;font-size:1.05rem;font-weight:700;
  letter-spacing:.1em;
  display:flex;align-items:center;gap:10px;
}
.panel-title.enc{color:var(--goldl);}
.panel-title.dec{color:var(--sea5);}
.panel-icon{font-size:1.3rem;}
.panel-body{padding:22px;}

/* ── Form elements ── */
.field-label{
  display:block;font-size:.7rem;font-weight:700;
  letter-spacing:.1em;text-transform:uppercase;
  color:var(--sea4);margin-bottom:7px;margin-top:16px;
}
.field-label:first-child{margin-top:0;}

textarea.vault-input,input.vault-input{
  width:100%;
  background:rgba(10,31,46,.6);
  border:1.5px solid rgba(74,156,199,.25);
  border-radius:11px;
  color:var(--mist);
  font-family:'Courier Prime',monospace;
  font-size:.9rem;
  padding:12px 14px;
  outline:none;
  transition:border-color .2s,box-shadow .2s;
  resize:vertical;
}
textarea.vault-input{min-height:110px;}
textarea.vault-input:focus,input.vault-input:focus{
  border-color:var(--sea4);
  box-shadow:0 0 0 3px rgba(74,156,199,.12);
}
textarea.vault-input::placeholder,input.vault-input::placeholder{color:rgba(142,202,230,.3);}

/* ── Upload zone ── */
.upload-zone{
  border:1.5px dashed rgba(74,156,199,.3);
  border-radius:11px;
  padding:14px 16px;
  text-align:center;
  cursor:pointer;
  transition:border-color .2s,background .2s;
  background:rgba(10,31,46,.4);
  font-size:.82rem;color:var(--sea4);
}
.upload-zone:hover{border-color:var(--sea4);background:rgba(74,156,199,.07);}
.upload-zone input[type=file]{display:none;}
.upload-zone .uz-icon{font-size:1.4rem;display:block;margin-bottom:4px;}
.upload-zone .uz-name{margin-top:5px;font-size:.75rem;color:var(--goldl);word-break:break-all;}

/* ── Key row ── */
.key-row{display:flex;align-items:center;gap:10px;margin-top:0;}
.key-num{
  width:90px;flex-shrink:0;
  background:rgba(10,31,46,.6);
  border:1.5px solid rgba(74,156,199,.25);
  border-radius:10px;color:var(--mist);
  font-family:'Courier Prime',monospace;font-size:1.1rem;font-weight:700;
  padding:10px 12px;text-align:center;
  outline:none;transition:border-color .2s,box-shadow .2s;
}
.key-num:focus{border-color:var(--sea4);box-shadow:0 0 0 3px rgba(74,156,199,.12);}
.key-label{font-size:.82rem;color:var(--sea5);font-weight:600;letter-spacing:.06em;}
.key-label small{display:block;font-size:.69rem;color:var(--sea4);font-weight:400;margin-top:1px;}

/* ── Action button ── */
.btn-action{
  display:block;width:100%;margin-top:18px;
  padding:13px 20px;border:none;border-radius:12px;
  font-family:'Cinzel',serif;font-size:.9rem;font-weight:700;letter-spacing:.1em;
  cursor:pointer;
  transition:transform .2s,box-shadow .2s,opacity .2s;
}
.btn-action.enc{
  background:linear-gradient(135deg,#8a6010,var(--gold),#c8981a);
  color:#0a1825;
  box-shadow:0 6px 22px rgba(212,168,67,.3);
}
.btn-action.enc:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(212,168,67,.45);}
.btn-action.dec{
  background:linear-gradient(135deg,var(--sea2),var(--sea3),var(--sea4));
  color:#fff;
  box-shadow:0 6px 22px rgba(74,156,199,.3);
}
.btn-action.dec:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(74,156,199,.45);}

/* ── Result card ── */
.result-card{
  margin-top:16px;
  background:rgba(10,31,46,.7);
  border:1px solid rgba(74,156,199,.2);
  border-radius:14px;
  overflow:hidden;
  display:none;
  animation:popIn .4s cubic-bezier(.34,1.4,.64,1) both;
}
@keyframes popIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
.result-card.visible{display:block;}
.result-head{
  background:rgba(26,74,107,.4);
  padding:10px 16px;
  display:flex;align-items:center;justify-content:space-between;
  border-bottom:1px solid rgba(74,156,199,.15);
}
.result-title{font-family:'Cinzel',serif;font-size:.8rem;letter-spacing:.1em;color:var(--sea5);}
.copy-btn{
  background:rgba(74,156,199,.15);border:1px solid rgba(74,156,199,.3);
  border-radius:20px;padding:4px 12px;
  font-family:'Jost',sans-serif;font-size:.72rem;font-weight:600;color:var(--sea4);
  cursor:pointer;transition:background .2s;
}
.copy-btn:hover{background:rgba(74,156,199,.3);}
.result-text{
  padding:16px;
  font-family:'Courier Prime',monospace;font-size:.9rem;
  color:var(--goldl);line-height:1.7;
  word-break:break-all;white-space:pre-wrap;
  max-height:200px;overflow-y:auto;
}
.result-text::-webkit-scrollbar{width:4px;}
.result-text::-webkit-scrollbar-thumb{background:rgba(74,156,199,.25);border-radius:4px;}

/* Error message */
.err-msg{
  margin-top:10px;
  background:rgba(192,57,43,.15);border:1px solid rgba(192,57,43,.3);
  border-radius:10px;padding:10px 14px;
  font-size:.8rem;color:#ff8a80;display:none;
}
.err-msg.visible{display:block;}

/* ═══════════════════════════════════════════════
   RF DIAGRAM SECTION
═══════════════════════════════════════════════ */
.diagram-section{
  max-width:1100px;margin:32px auto 0;padding:0 18px;
  position:relative;z-index:10;
}
.diagram-card{
  background:rgba(13,43,62,.7);backdrop-filter:blur(10px);
  border:1px solid rgba(74,156,199,.2);border-radius:20px;
  padding:26px 28px;
  animation:riseUp .6s .2s cubic-bezier(.34,1.4,.64,1) both;
}
.diagram-title{
  font-family:'Cinzel',serif;font-size:1rem;font-weight:700;
  color:var(--goldl);letter-spacing:.1em;margin-bottom:16px;
  display:flex;align-items:center;gap:10px;
}
.demo-table{width:100%;border-collapse:collapse;font-family:'Courier Prime',monospace;font-size:.88rem;}
.demo-table td{
  width:30px;height:30px;text-align:center;vertical-align:middle;
  border:1px solid rgba(74,156,199,.15);color:var(--sea5);
}
.demo-table td.active{background:rgba(74,156,199,.2);color:var(--goldl);font-weight:700;}
.demo-table td.rail0{color:#f0cc70;}
.demo-table td.rail1{color:#8ecae6;}
.demo-table td.rail2{color:#a8d8ea;}
.demo-desc{font-size:.83rem;color:var(--sea5);line-height:1.75;margin-top:14px;}
.demo-desc strong{color:var(--goldl);}

/* ═══════════════════════════════════════════════
   FOOTER
═══════════════════════════════════════════════ */
.page-footer{
  text-align:center;margin-top:44px;
  font-size:.75rem;color:rgba(142,202,230,.4);
  position:relative;z-index:10;
  letter-spacing:.08em;
}
.page-footer a{color:var(--sea4);text-decoration:none;border-bottom:1px dashed rgba(74,156,199,.3);}
.page-footer a:hover{color:var(--foam);}

/* ── Copy toast ── */
.toast{
  position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(20px);
  background:rgba(0,135,90,.92);color:#fff;
  font-family:'Jost',sans-serif;font-size:.82rem;font-weight:600;
  padding:8px 20px;border-radius:30px;letter-spacing:.04em;
  opacity:0;pointer-events:none;transition:opacity .25s,transform .25s;z-index:9999;
}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0);}
</style>
</head>
<body>

<!-- Particles -->
<div id="particles"></div>

<!-- Background waves SVG -->
<svg class="wave-bg" viewBox="0 0 1440 180" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M0,80 C240,140 480,20 720,80 C960,140 1200,20 1440,80 L1440,180 L0,180Z" fill="rgba(74,156,199,.15)"/>
  <path d="M0,110 C360,60 720,160 1080,100 C1260,70 1380,130 1440,110 L1440,180 L0,180Z" fill="rgba(42,127,168,.1)"/>
</svg>

<!-- ═══ HEADER ═══ -->
<header class="header">
  <span class="skull-icon">🏴‍☠️</span>
  <h1 class="brand">XoX_Pirate</h1>
  <p class="brand-sub">⚓ Cipher Vault ⚓</p>
  <div class="desc-card">
    <p>
      Welcome to the <strong>Cipher Vault</strong> — your personal stronghold for encoding and decoding messages with the craft of a true buccaneer.
      Here, text is transformed through the art of <strong>encryption &amp; decryption</strong>, ciphered entirely at your command.
    </p>
    <p style="margin-top:10px;">
      We sail the waters of the <strong>Rail Fence (Zigzag) Cipher</strong> — a classical transposition algorithm where characters zigzag across a set number of rails (rows) like a flag in the wind, then are read off row by row. The more rails you choose, the deeper the cipher.
      Pair it with a key between <strong>1–100</strong> to control how many rails your message is scattered across.
    </p>
    <span class="rf-badge">⚙️ Algorithm: Rail Fence (Zigzag) Transposition</span>
  </div>
</header>

<!-- ═══ VAULT PANELS ═══ -->
<div class="vault">

  <!-- ─── ENCRYPT PANEL ─── -->
  <div class="panel">
    <div class="panel-head">
      <div class="panel-title enc"><span class="panel-icon">🔐</span> Encrypt Message</div>
    </div>
    <div class="panel-body">

      <label class="field-label">Enter Message to Encrypt</label>
      <textarea class="vault-input" id="enc-input" placeholder="Type or paste your message here... (letters &amp; numbers only)"
        oninput="sanitizeInput(this)"></textarea>

      <label class="field-label" style="margin-top:14px;">Or Upload a File (.txt / .pdf)</label>
      <div class="upload-zone" id="enc-dropzone" onclick="document.getElementById('enc-file').click()">
        <input type="file" id="enc-file" accept=".txt,.pdf" onchange="handleFile(event,'enc-input','enc-filename')">
        <span class="uz-icon">📂</span>
        <span>Click to upload .txt or .pdf</span>
        <div class="uz-name" id="enc-filename"></div>
      </div>

      <label class="field-label" style="margin-top:18px;">Number of Rails (Key)</label>
      <div class="key-row">
        <input type="number" class="key-num" id="enc-key" value="3" min="1" max="100"
               oninput="this.value=Math.min(100,Math.max(1,parseInt(this.value)||1))">
        <div class="key-label">Rails
          <small>1 – 100 &nbsp;·&nbsp; more rails = deeper cipher</small>
        </div>
      </div>

      <button class="btn-action enc" onclick="runEncrypt()">🔐 Encrypt</button>

      <div class="err-msg" id="enc-err"></div>

      <div class="result-card" id="enc-result">
        <div class="result-head">
          <span class="result-title">⚓ Encrypted Output</span>
          <button class="copy-btn" onclick="copyResult('enc-out')">Copy 📋</button>
        </div>
        <div class="result-text" id="enc-out"></div>
      </div>

    </div>
  </div>

  <!-- ─── DECRYPT PANEL ─── -->
  <div class="panel">
    <div class="panel-head">
      <div class="panel-title dec"><span class="panel-icon">🔓</span> Decrypt Message</div>
    </div>
    <div class="panel-body">

      <label class="field-label">Enter Message to Decrypt</label>
      <textarea class="vault-input" id="dec-input" placeholder="Paste your encrypted message here... (letters &amp; numbers only)"
        oninput="sanitizeInput(this)"></textarea>

      <label class="field-label" style="margin-top:14px;">Or Upload a File (.txt / .pdf)</label>
      <div class="upload-zone" id="dec-dropzone" onclick="document.getElementById('dec-file').click()">
        <input type="file" id="dec-file" accept=".txt,.pdf" onchange="handleFile(event,'dec-input','dec-filename')">
        <span class="uz-icon">📂</span>
        <span>Click to upload .txt or .pdf</span>
        <div class="uz-name" id="dec-filename"></div>
      </div>

      <label class="field-label" style="margin-top:18px;">Number of Rails (Key)</label>
      <div class="key-row">
        <input type="number" class="key-num" id="dec-key" value="3" min="1" max="100"
               oninput="this.value=Math.min(100,Math.max(1,parseInt(this.value)||1))">
        <div class="key-label">Rails
          <small>1 – 100 &nbsp;·&nbsp; must match encrypt key</small>
        </div>
      </div>

      <button class="btn-action dec" onclick="runDecrypt()">🔓 Decrypt</button>

      <div class="err-msg" id="dec-err"></div>

      <div class="result-card" id="dec-result">
        <div class="result-head">
          <span class="result-title">⚓ Decrypted Output</span>
          <button class="copy-btn" onclick="copyResult('dec-out')">Copy 📋</button>
        </div>
        <div class="result-text" id="dec-out"></div>
      </div>

    </div>
  </div>

</div><!-- /vault -->

<!-- ═══ RF DIAGRAM ═══ -->
<div class="diagram-section">
  <div class="diagram-card">
    <div class="diagram-title">⚙️ How Rail Fence Works — Visual Example</div>
    <table class="demo-table" id="rfDemo">
      <!-- Filled by JS -->
    </table>
    <div class="demo-desc" id="rfDesc"></div>
  </div>
</div>

<div class="page-footer" style="margin-top:28px;">
  <p>🏴‍☠️ XoX_Pirate &nbsp;·&nbsp; <a href="index.php">← Back to Portfolio</a></p>
</div>

<div class="toast" id="toast">✅ Copied to clipboard!</div>

<script>
/* ═══════════════════════════════════════════════
   PARTICLES
═══════════════════════════════════════════════ */
(function(){
  const colors=['rgba(74,156,199,',  'rgba(142,202,230,', 'rgba(212,168,67,'];
  const cont = document.getElementById('particles');
  for(let i=0;i<18;i++){
    const d=document.createElement('div');
    d.className='particle';
    const size=Math.random()*4+2;
    const col=colors[Math.floor(Math.random()*colors.length)];
    d.style.cssText=`
      width:${size}px;height:${size}px;
      background:${col}0.6)};
      left:${Math.random()*100}%;
      bottom:${Math.random()*20}%;
      animation-duration:${8+Math.random()*14}s;
      animation-delay:${Math.random()*8}s;
    `;
    cont.appendChild(d);
  }
})();

/* ═══════════════════════════════════════════════
   SOURCE TRACKING
   Tracks whether the text came from typing or a file upload
═══════════════════════════════════════════════ */
let encSource = 'typed';
let decSource = 'typed';

/* ═══════════════════════════════════════════════
   INPUT SANITISER — letters & numbers only
═══════════════════════════════════════════════ */
function sanitizeInput(el){
  // If user types directly, reset source to 'typed'
  if(el.id === 'enc-input') encSource = 'typed';
  if(el.id === 'dec-input') decSource = 'typed';
  el.value = el.value.replace(/[^A-Za-z0-9 ]/g,'');
}

/* ═══════════════════════════════════════════════
   FILE UPLOAD HANDLER
═══════════════════════════════════════════════ */
function handleFile(event, targetId, nameId){
  const file = event.target.files[0];
  if(!file) return;
  document.getElementById(nameId).textContent = '📄 ' + file.name;

  // Track upload source so saveToDB knows where input came from
  const isPDF = file.type === 'application/pdf' || file.name.endsWith('.pdf');
  if(targetId === 'enc-input') encSource = isPDF ? 'pdf_upload' : 'txt_upload';
  if(targetId === 'dec-input') decSource = isPDF ? 'pdf_upload' : 'txt_upload';

  if(file.type === 'text/plain' || file.name.endsWith('.txt')){
    const reader = new FileReader();
    reader.onload = function(e){
      let text = e.target.result.replace(/[^A-Za-z0-9 ]/g,'');
      document.getElementById(targetId).value = text;
    };
    reader.readAsText(file);
  } else if(file.type === 'application/pdf' || file.name.endsWith('.pdf')){
    // Use PDF.js CDN to extract text
    const script = document.getElementById('pdfjsScript');
    if(!script){
      const s = document.createElement('script');
      s.id = 'pdfjsScript';
      s.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
      s.onload = () => extractPDF(file, targetId, nameId);
      document.head.appendChild(s);
    } else {
      extractPDF(file, targetId, nameId);
    }
  }
}

async function extractPDF(file, targetId, nameId){
  try{
    pdfjsLib.GlobalWorkerOptions.workerSrc =
      'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    const arrayBuffer = await file.arrayBuffer();
    const pdf = await pdfjsLib.getDocument({data: arrayBuffer}).promise;
    let fullText = '';
    for(let p=1;p<=pdf.numPages;p++){
      const page = await pdf.getPage(p);
      const content = await page.getTextContent();
      fullText += content.items.map(i=>i.str).join(' ') + ' ';
    }
    // Sanitise
    fullText = fullText.replace(/[^A-Za-z0-9 ]/g,'').trim();
    document.getElementById(targetId).value = fullText;
    document.getElementById(nameId).textContent = '✅ Text extracted from PDF';
  }catch(e){
    document.getElementById(nameId).textContent = '❌ Could not extract PDF text. Please paste manually.';
  }
}

/* ═══════════════════════════════════════════════
   RAIL FENCE ALGORITHM
═══════════════════════════════════════════════ */

/**
 * railFenceEncrypt(text, rails)
 * Classic Rail Fence / Zigzag cipher.
 * Spaces are preserved, only A-Za-z0-9 + space accepted.
 */
function railFenceEncrypt(text, rails){
  if(rails < 2) return text;           // 1 rail = no change
  const n = text.length;
  const fence = Array.from({length:rails}, () => []);
  let rail = 0, direction = 1;

  for(let i=0;i<n;i++){
    fence[rail].push(text[i]);
    if(rail === 0) direction = 1;
    else if(rail === rails-1) direction = -1;
    rail += direction;
  }
  return fence.map(r=>r.join('')).join('');
}

/**
 * railFenceDecrypt(cipher, rails)
 * Reconstructs the zigzag pattern to reverse the encryption.
 */
function railFenceDecrypt(cipher, rails){
  if(rails < 2) return cipher;
  const n = cipher.length;

  // 1. Build index pattern (which rail each position belongs to)
  const pattern = new Array(n);
  let rail = 0, direction = 1;
  for(let i=0;i<n;i++){
    pattern[i] = rail;
    if(rail === 0) direction = 1;
    else if(rail === rails-1) direction = -1;
    rail += direction;
  }

  // 2. Count chars per rail
  const lengths = new Array(rails).fill(0);
  for(let i=0;i<n;i++) lengths[pattern[i]]++;

  // 3. Slice cipher into per-rail arrays
  const railArrays = [];
  let idx = 0;
  for(let r=0;r<rails;r++){
    railArrays.push(cipher.slice(idx, idx+lengths[r]).split(''));
    idx += lengths[r];
  }

  // 4. Read off in zigzag order
  const railIdxs = new Array(rails).fill(0);
  let result = '';
  for(let i=0;i<n;i++){
    const r = pattern[i];
    result += railArrays[r][railIdxs[r]++];
  }
  return result;
}

/* ═══════════════════════════════════════════════
   UI ACTIONS
═══════════════════════════════════════════════ */
function showErr(id, msg){
  const el = document.getElementById(id);
  el.textContent = '⚠️ ' + msg;
  el.classList.add('visible');
}
function hideErr(id){ document.getElementById(id).classList.remove('visible'); }

function runEncrypt(){
  hideErr('enc-err');
  const text = document.getElementById('enc-input').value.trim();
  const rails = parseInt(document.getElementById('enc-key').value) || 3;

  if(!text){ showErr('enc-err','Please enter or upload a message to encrypt.'); return; }
  if(rails < 1 || rails > 100){ showErr('enc-err','Key must be between 1 and 100.'); return; }

  const result = railFenceEncrypt(text, rails);

  const card = document.getElementById('enc-result');
  document.getElementById('enc-out').textContent = result;
  card.classList.add('visible');
  card.scrollIntoView({behavior:'smooth',block:'nearest'});

  // Save to DB
  saveToDB('encrypt', rails, text, result, encSource);

  // Update diagram
  buildDiagram(text.replace(/ /g,'').slice(0,20), rails);
}

function runDecrypt(){
  hideErr('dec-err');
  const text = document.getElementById('dec-input').value.trim();
  const rails = parseInt(document.getElementById('dec-key').value) || 3;

  if(!text){ showErr('dec-err','Please enter or upload a message to decrypt.'); return; }
  if(rails < 1 || rails > 100){ showErr('dec-err','Key must be between 1 and 100.'); return; }

  const result = railFenceDecrypt(text, rails);

  const card = document.getElementById('dec-result');
  document.getElementById('dec-out').textContent = result;
  card.classList.add('visible');
  card.scrollIntoView({behavior:'smooth',block:'nearest'});

  // Save to DB
  saveToDB('decrypt', rails, text, result, decSource);
}

/* ═══════════════════════════════════════════════
   SAVE TO DATABASE
   Posts cipher activity to cipher_save.php
═══════════════════════════════════════════════ */
async function saveToDB(action, rails, inputText, outputText, source){
  try{
    const fd = new FormData();
    fd.append('action',      action);
    fd.append('rails',       rails);
    fd.append('input_text',  inputText);
    fd.append('output_text', outputText);
    fd.append('source',      source || 'typed');
    await fetch('cipher_save.php', { method:'POST', body:fd });
    // Silent save — no UI feedback needed, errors are non-blocking
  }catch(e){
    // Fail silently so cipher tool still works even if DB is down
  }
}

function copyResult(id){
  const text = document.getElementById(id).textContent;
  navigator.clipboard.writeText(text).then(()=>{
    const t = document.getElementById('toast');
    t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'),2000);
  });
}

/* ═══════════════════════════════════════════════
   RF DIAGRAM BUILDER
═══════════════════════════════════════════════ */
function buildDiagram(sample, rails){
  // Default sample
  if(!sample || sample.length < 4) sample = 'HELLOPIRATE';
  sample = sample.slice(0,16).toUpperCase();
  const n = sample.length;
  rails   = Math.min(rails, n, 6); // cap for display

  const grid = Array.from({length:rails}, ()=>Array(n).fill(''));
  let rail=0, dir=1;
  const railOf = new Array(n);
  for(let i=0;i<n;i++){
    railOf[i]=rail;
    grid[rail][i]=sample[i];
    if(rail===0) dir=1;
    else if(rail===rails-1) dir=-1;
    rail+=dir;
  }

  const table = document.getElementById('rfDemo');
  let html='';
  for(let r=0;r<rails;r++){
    html+='<tr>';
    for(let c=0;c<n;c++){
      const cls=grid[r][c]?`rail${r}`:'';
      html+=`<td class="${cls}">${grid[r][c]||'·'}</td>`;
    }
    html+='</tr>';
  }
  table.innerHTML=html;

  const cipher = railFenceEncrypt(sample, rails);
  document.getElementById('rfDesc').innerHTML=
    `<strong>Original:</strong> ${sample}<br>`+
    `<strong>Rails:</strong> ${rails} &nbsp;|&nbsp; `+
    `<strong>Encrypted:</strong> <span style="color:var(--goldl);font-family:'Courier Prime',monospace;">${cipher}</span><br><br>`+
    `Characters are placed diagonally across <strong>${rails} rails</strong> in a zigzag pattern. `+
    `Then each rail is read left-to-right and concatenated. `+
    `To decrypt, the process is reversed using the same number of rails.`;
}

// Initial diagram on load
buildDiagram('HELLOPIRATE', 3);
</script>
</body>
</html>

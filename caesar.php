<?php
/**
 * caesar.php — Caesar_Pirate Cipher Tool
 * Caesar (Letter Shift) Encryption & Decryption
 * Gate: must have visited intro (session visited_intro = true)
 */
session_start();

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
<title>Caesar_Pirate — Cipher Vault</title>
<link href="https://fonts.googleapis.com/css2?family=Cinzel:wght@400;600;700;900&family=Jost:wght@300;400;500;600&family=Courier+Prime:wght@400;700&display=swap" rel="stylesheet">
<style>
:root{
  --sea0:#0a1f2e;--sea1:#0d2b3e;--sea2:#1a4a6b;--sea3:#2a7fa8;--sea4:#4a9cc7;--sea5:#8ecae6;
  --foam:#c5e8f5;--mist:#e8f6fc;--gold:#d4a843;--goldl:#f0cc70;--err:#c0392b;--ok:#00875a;
}
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'Jost',sans-serif;min-height:100vh;background:linear-gradient(170deg,#0a1825 0%,#0d2b3e 30%,#12374f 60%,#0a2033 100%);color:var(--mist);position:relative;overflow-x:hidden;padding-bottom:60px;}
body::before{content:'';position:fixed;inset:0;pointer-events:none;z-index:0;background:radial-gradient(ellipse at 20% 80%,rgba(74,156,199,.12) 0%,transparent 55%),radial-gradient(ellipse at 80% 10%,rgba(42,127,168,.1) 0%,transparent 50%);}
.wave-bg{position:fixed;bottom:0;left:0;right:0;height:180px;pointer-events:none;z-index:0;opacity:.18;}
.particle{position:fixed;border-radius:50%;pointer-events:none;animation:floatUp linear infinite;opacity:0;}
@keyframes floatUp{0%{transform:translateY(0) scale(1);opacity:0}20%{opacity:.5}80%{opacity:.3}100%{transform:translateY(-100vh) scale(.4);opacity:0}}

/* HEADER */
.header{position:relative;z-index:10;text-align:center;padding:48px 20px 32px;}
.skull-icon{font-size:3rem;display:block;margin-bottom:10px;animation:sway 3s ease-in-out infinite;}
@keyframes sway{0%,100%{transform:rotate(-5deg)}50%{transform:rotate(5deg)}}
.brand{font-family:'Cinzel',serif;font-size:clamp(1.8rem,6vw,3.2rem);font-weight:900;letter-spacing:.12em;background:linear-gradient(135deg,var(--goldl),var(--gold),var(--sea5),var(--goldl));background-size:300% 100%;-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;animation:shimmer 4s ease infinite;line-height:1;margin-bottom:6px;}
@keyframes shimmer{0%,100%{background-position:0% 50%}50%{background-position:100% 50%}}
.brand-sub{font-family:'Cinzel',serif;font-size:.73rem;letter-spacing:.3em;text-transform:uppercase;color:var(--sea4);margin-bottom:20px;}
.desc-card{max-width:680px;margin:0 auto;background:rgba(26,74,107,.35);border:1px solid rgba(74,156,199,.25);border-radius:16px;padding:20px 26px;font-size:.9rem;line-height:1.85;color:var(--foam);position:relative;overflow:hidden;}
.desc-card::before{content:'';position:absolute;top:0;left:0;right:0;height:2px;background:linear-gradient(to right,transparent,var(--gold),var(--sea4),var(--gold),transparent);}
.desc-card strong{color:var(--goldl);}
.algo-badge{display:inline-block;margin-top:10px;background:rgba(74,156,199,.15);border:1px solid rgba(74,156,199,.3);border-radius:20px;padding:4px 14px;font-size:.74rem;color:var(--sea5);font-weight:600;letter-spacing:.05em;}

/* VAULT PANELS */
.vault{max-width:1100px;margin:0 auto;padding:0 18px;display:grid;grid-template-columns:1fr 1fr;gap:24px;position:relative;z-index:10;}
@media(max-width:760px){.vault{grid-template-columns:1fr;}}

.panel{background:rgba(13,43,62,.75);backdrop-filter:blur(12px);border:1px solid rgba(74,156,199,.2);border-radius:20px;overflow:hidden;box-shadow:0 12px 40px rgba(0,0,0,.35);animation:riseUp .6s cubic-bezier(.34,1.4,.64,1) both;}
.panel:nth-child(2){animation-delay:.1s;}
@keyframes riseUp{from{opacity:0;transform:translateY(28px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
.panel-head{padding:17px 22px 13px;border-bottom:1px solid rgba(74,156,199,.18);background:linear-gradient(135deg,rgba(26,74,107,.6),rgba(10,31,50,.4));position:relative;}
.panel-head::after{content:'';position:absolute;bottom:0;left:0;right:0;height:1px;background:linear-gradient(to right,transparent,rgba(212,168,67,.4),transparent);}
.panel-title{font-family:'Cinzel',serif;font-size:1rem;font-weight:700;letter-spacing:.1em;display:flex;align-items:center;gap:10px;}
.panel-title.enc{color:var(--goldl);}
.panel-title.dec{color:var(--sea5);}
.panel-body{padding:20px;}

/* Form elements */
.field-label{display:block;font-size:.69rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;color:var(--sea4);margin-bottom:6px;margin-top:14px;}
.field-label:first-child{margin-top:0;}
textarea.vault-input,input.vault-input{width:100%;background:rgba(10,31,46,.6);border:1.5px solid rgba(74,156,199,.25);border-radius:11px;color:var(--mist);font-family:'Courier Prime',monospace;font-size:.88rem;padding:11px 13px;outline:none;transition:border-color .2s,box-shadow .2s;resize:vertical;}
textarea.vault-input{min-height:100px;}
textarea.vault-input:focus,input.vault-input:focus{border-color:var(--sea4);box-shadow:0 0 0 3px rgba(74,156,199,.12);}
textarea.vault-input::placeholder,input.vault-input::placeholder{color:rgba(142,202,230,.3);}

/* Upload zone */
.upload-zone{border:1.5px dashed rgba(74,156,199,.3);border-radius:11px;padding:12px 14px;text-align:center;cursor:pointer;transition:border-color .2s,background .2s;background:rgba(10,31,46,.4);font-size:.8rem;color:var(--sea4);}
.upload-zone:hover{border-color:var(--sea4);background:rgba(74,156,199,.07);}
.upload-zone input[type=file]{display:none;}
.upload-zone .uz-icon{font-size:1.3rem;display:block;margin-bottom:3px;}
.uz-name{margin-top:4px;font-size:.73rem;color:var(--goldl);word-break:break-all;}

/* Key row */
.key-row{display:flex;align-items:center;gap:10px;}
.key-num{width:85px;flex-shrink:0;background:rgba(10,31,46,.6);border:1.5px solid rgba(74,156,199,.25);border-radius:10px;color:var(--mist);font-family:'Courier Prime',monospace;font-size:1.05rem;font-weight:700;padding:9px 11px;text-align:center;outline:none;transition:border-color .2s,box-shadow .2s;}
.key-num:focus{border-color:var(--sea4);box-shadow:0 0 0 3px rgba(74,156,199,.12);}
.key-label{font-size:.8rem;color:var(--sea5);font-weight:600;}
.key-label small{display:block;font-size:.67rem;color:var(--sea4);font-weight:400;margin-top:1px;}

/* Action button */
.btn-action{display:block;width:100%;margin-top:16px;padding:12px 20px;border:none;border-radius:12px;font-family:'Cinzel',serif;font-size:.88rem;font-weight:700;letter-spacing:.1em;cursor:pointer;transition:transform .2s,box-shadow .2s;}
.btn-action.enc{background:linear-gradient(135deg,#8a6010,var(--gold),#c8981a);color:#0a1825;box-shadow:0 6px 22px rgba(212,168,67,.3);}
.btn-action.enc:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(212,168,67,.45);}
.btn-action.dec{background:linear-gradient(135deg,var(--sea2),var(--sea3),var(--sea4));color:#fff;box-shadow:0 6px 22px rgba(74,156,199,.3);}
.btn-action.dec:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(74,156,199,.45);}

/* Result card */
.result-card{margin-top:14px;background:rgba(10,31,46,.7);border:1px solid rgba(74,156,199,.2);border-radius:14px;overflow:hidden;display:none;animation:popIn .4s cubic-bezier(.34,1.4,.64,1) both;}
@keyframes popIn{from{opacity:0;transform:scale(.95)}to{opacity:1;transform:scale(1)}}
.result-card.visible{display:block;}
.result-head{background:rgba(26,74,107,.4);padding:9px 15px;display:flex;align-items:center;justify-content:space-between;border-bottom:1px solid rgba(74,156,199,.15);}
.result-title{font-family:'Cinzel',serif;font-size:.78rem;letter-spacing:.1em;color:var(--sea5);}
.copy-btn{background:rgba(74,156,199,.15);border:1px solid rgba(74,156,199,.3);border-radius:20px;padding:3px 11px;font-family:'Jost',sans-serif;font-size:.7rem;font-weight:600;color:var(--sea4);cursor:pointer;transition:background .2s;}
.copy-btn:hover{background:rgba(74,156,199,.3);}
.result-text{padding:14px;font-family:'Courier Prime',monospace;font-size:.88rem;color:var(--goldl);line-height:1.7;word-break:break-all;white-space:pre-wrap;max-height:180px;overflow-y:auto;}
.result-text::-webkit-scrollbar{width:4px;}
.result-text::-webkit-scrollbar-thumb{background:rgba(74,156,199,.25);border-radius:4px;}

/* Save status badge */
.save-status{font-size:.7rem;color:var(--sea4);margin-top:6px;min-height:16px;font-style:italic;padding-left:2px;}

/* Error */
.err-msg{margin-top:8px;background:rgba(192,57,43,.15);border:1px solid rgba(192,57,43,.3);border-radius:10px;padding:9px 13px;font-size:.78rem;color:#ff8a80;display:none;}
.err-msg.visible{display:block;}

/* Caesar shift demo */
.demo-section{max-width:1100px;margin:28px auto 0;padding:0 18px;position:relative;z-index:10;}
.demo-card{background:rgba(13,43,62,.7);backdrop-filter:blur(10px);border:1px solid rgba(74,156,199,.2);border-radius:20px;padding:24px 26px;animation:riseUp .6s .2s cubic-bezier(.34,1.4,.64,1) both;}
.demo-title{font-family:'Cinzel',serif;font-size:.98rem;font-weight:700;color:var(--goldl);letter-spacing:.1em;margin-bottom:14px;display:flex;align-items:center;gap:10px;}
.alphabet-row{display:flex;flex-wrap:nowrap;overflow-x:auto;gap:2px;margin-bottom:6px;padding-bottom:4px;}
.alpha-cell{min-width:26px;height:26px;display:flex;align-items:center;justify-content:center;font-family:'Courier Prime',monospace;font-size:.78rem;border-radius:5px;flex-shrink:0;}
.alpha-cell.plain{background:rgba(74,156,199,.18);color:var(--sea5);}
.alpha-cell.cipher{background:rgba(212,168,67,.18);color:var(--goldl);}
.alpha-label{font-size:.68rem;color:var(--sea4);letter-spacing:.08em;text-transform:uppercase;margin-bottom:4px;}
.demo-desc{font-size:.82rem;color:var(--sea5);line-height:1.75;margin-top:12px;}
.demo-desc strong{color:var(--goldl);}

/* Footer */
.page-footer{text-align:center;margin-top:40px;font-size:.73rem;color:rgba(142,202,230,.4);position:relative;z-index:10;letter-spacing:.08em;}
.page-footer a{color:var(--sea4);text-decoration:none;border-bottom:1px dashed rgba(74,156,199,.3);}

/* Toast */
.toast{position:fixed;bottom:28px;left:50%;transform:translateX(-50%) translateY(20px);background:rgba(0,135,90,.92);color:#fff;font-family:'Jost',sans-serif;font-size:.8rem;font-weight:600;padding:7px 18px;border-radius:30px;opacity:0;pointer-events:none;transition:opacity .25s,transform .25s;z-index:9999;}
.toast.show{opacity:1;transform:translateX(-50%) translateY(0);}
</style>
</head>
<body>
<div id="particles"></div>
<svg class="wave-bg" viewBox="0 0 1440 180" preserveAspectRatio="none" xmlns="http://www.w3.org/2000/svg">
  <path d="M0,80 C240,140 480,20 720,80 C960,140 1200,20 1440,80 L1440,180 L0,180Z" fill="rgba(74,156,199,.15)"/>
  <path d="M0,110 C360,60 720,160 1080,100 C1260,70 1380,130 1440,110 L1440,180 L0,180Z" fill="rgba(42,127,168,.1)"/>
</svg>

<header class="header">
  <span class="skull-icon">⚔️</span>
  <h1 class="brand">Caesar_Pirate</h1>
  <p class="brand-sub">⚓ Cipher Vault ⚓</p>
  <div class="desc-card">
    <p>
      Welcome to the <strong>Caesar_Pirate Vault</strong> — where every letter walks the plank and lands somewhere new.
      This is your personal chamber for <strong>encrypting &amp; decrypting</strong> messages with the craft of a true corsair.
    </p>
    <p style="margin-top:10px;">
      We wield the ancient <strong>Caesar Cipher</strong> — one of history's oldest and most elegant substitution ciphers,
      reportedly used by Julius Caesar himself to protect military dispatches. Each letter in your message is shifted
      forward (or backward to decrypt) by a fixed number of positions in the alphabet — your chosen <strong>key (1–100)</strong>.
      Numbers stay unchanged; only letters walk the plank. With a key of 3, A becomes D, B becomes E, and Z wraps around to C.
    </p>
    <span class="algo-badge">⚙️ Algorithm: Caesar Cipher (Modular Letter Substitution)</span>
  </div>
</header>

<div class="vault">

  <!-- ENCRYPT -->
  <div class="panel">
    <div class="panel-head">
      <div class="panel-title enc"><span>🔐</span> Encrypt Message</div>
    </div>
    <div class="panel-body">

      <label class="field-label">Enter Message to Encrypt</label>
      <textarea class="vault-input" id="enc-input" placeholder="Type or paste your message here… (letters &amp; numbers only)"
        oninput="sanitizeInput(this,'enc')"></textarea>

      <label class="field-label" style="margin-top:12px;">Or Upload a File (.txt / .pdf)</label>
      <div class="upload-zone" onclick="document.getElementById('enc-file').click()">
        <input type="file" id="enc-file" accept=".txt,.pdf" onchange="handleFile(event,'enc-input','enc-filename','enc')">
        <span class="uz-icon">📂</span>
        <span>Click to upload .txt or .pdf</span>
        <div class="uz-name" id="enc-filename"></div>
      </div>

      <label class="field-label" style="margin-top:16px;">Shift Key</label>
      <div class="key-row">
        <input type="number" class="key-num" id="enc-key" value="3" min="1" max="100"
               oninput="this.value=Math.min(100,Math.max(1,parseInt(this.value)||1));updateDemo()">
        <div class="key-label">Shift
          <small>1 – 100 &nbsp;·&nbsp; letters shift by this many positions</small>
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
      <div class="save-status" id="enc-save-status"></div>

    </div>
  </div>

  <!-- DECRYPT -->
  <div class="panel">
    <div class="panel-head">
      <div class="panel-title dec"><span>🔓</span> Decrypt Message</div>
    </div>
    <div class="panel-body">

      <label class="field-label">Enter Message to Decrypt</label>
      <textarea class="vault-input" id="dec-input" placeholder="Paste your encrypted message here… (letters &amp; numbers only)"
        oninput="sanitizeInput(this,'dec')"></textarea>

      <label class="field-label" style="margin-top:12px;">Or Upload a File (.txt / .pdf)</label>
      <div class="upload-zone" onclick="document.getElementById('dec-file').click()">
        <input type="file" id="dec-file" accept=".txt,.pdf" onchange="handleFile(event,'dec-input','dec-filename','dec')">
        <span class="uz-icon">📂</span>
        <span>Click to upload .txt or .pdf</span>
        <div class="uz-name" id="dec-filename"></div>
      </div>

      <label class="field-label" style="margin-top:16px;">Shift Key</label>
      <div class="key-row">
        <input type="number" class="key-num" id="dec-key" value="3" min="1" max="100"
               oninput="this.value=Math.min(100,Math.max(1,parseInt(this.value)||1))">
        <div class="key-label">Shift
          <small>1 – 100 &nbsp;·&nbsp; must match the encrypt key</small>
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
      <div class="save-status" id="dec-save-status"></div>

    </div>
  </div>

</div><!-- /vault -->

<!-- ALPHABET SHIFT DEMO -->
<div class="demo-section">
  <div class="demo-card">
    <div class="demo-title">⚙️ How Caesar Cipher Works — Live Alphabet Map</div>
    <div class="alpha-label">Plain Alphabet</div>
    <div class="alphabet-row" id="plainRow"></div>
    <div class="alpha-label" style="margin-top:8px;">Cipher Alphabet (shifted by key)</div>
    <div class="alphabet-row" id="cipherRow"></div>
    <div class="demo-desc" id="demoDesc"></div>
  </div>
</div>

<div class="page-footer" style="margin-top:28px;">
  <p>⚔️ Caesar_Pirate &nbsp;·&nbsp; <a href="index.php">← Back to Portfolio</a> &nbsp;·&nbsp; <a href="tools.php" target="_blank">Rail Fence →</a></p>
</div>

<div class="toast" id="toast">✅ Copied to clipboard!</div>

<script>
/* PARTICLES */
(function(){
  const colors=['rgba(74,156,199,','rgba(142,202,230,','rgba(212,168,67,'];
  const cont=document.getElementById('particles');
  for(let i=0;i<16;i++){
    const d=document.createElement('div');d.className='particle';
    const size=Math.random()*4+2;
    const col=colors[Math.floor(Math.random()*colors.length)];
    d.style.cssText=`width:${size}px;height:${size}px;background:${col}0.6);left:${Math.random()*100}%;bottom:${Math.random()*20}%;animation-duration:${8+Math.random()*14}s;animation-delay:${Math.random()*8}s;`;
    cont.appendChild(d);
  }
})();

/* SOURCE TRACKING */
let encSource='typed', decSource='typed';

/* SANITISE — letters, numbers, spaces only */
function sanitizeInput(el, side){
  if(side==='enc') encSource='typed';
  if(side==='dec') decSource='typed';
  el.value=el.value.replace(/[^A-Za-z0-9 ]/g,'');
}

/* FILE UPLOAD */
function handleFile(event, targetId, nameId, side){
  const file=event.target.files[0];
  if(!file) return;
  document.getElementById(nameId).textContent='📄 '+file.name;
  const isPDF=file.name.endsWith('.pdf');
  if(side==='enc') encSource=isPDF?'pdf_upload':'txt_upload';
  if(side==='dec') decSource=isPDF?'pdf_upload':'txt_upload';

  if(file.name.endsWith('.txt')){
    const reader=new FileReader();
    reader.onload=function(e){
      document.getElementById(targetId).value=e.target.result.replace(/[^A-Za-z0-9 ]/g,'');
    };
    reader.readAsText(file);
  } else if(isPDF){
    if(!window.pdfjsLib){
      const s=document.createElement('script');
      s.src='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
      s.onload=()=>extractPDF(file,targetId,nameId);
      document.head.appendChild(s);
    } else { extractPDF(file,targetId,nameId); }
  }
}

async function extractPDF(file,targetId,nameId){
  try{
    pdfjsLib.GlobalWorkerOptions.workerSrc='https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
    const ab=await file.arrayBuffer();
    const pdf=await pdfjsLib.getDocument({data:ab}).promise;
    let txt='';
    for(let p=1;p<=pdf.numPages;p++){
      const page=await pdf.getPage(p);
      const content=await page.getTextContent();
      txt+=content.items.map(i=>i.str).join(' ')+' ';
    }
    document.getElementById(targetId).value=txt.replace(/[^A-Za-z0-9 ]/g,'').trim();
    document.getElementById(nameId).textContent+=' ✅ Text extracted';
  }catch(e){
    document.getElementById(nameId).textContent+=' ❌ Could not read PDF. Paste manually.';
  }
}

/* ═══════════════════════════════════════════
   CAESAR CIPHER ALGORITHM
   
   How it works:
   - Each LETTER is shifted forward by `shift` positions in the alphabet
   - A–Z and a–z are treated separately (case preserved)
   - Numbers and spaces are NOT shifted — they pass through unchanged
   - Wraps around: Z shifted by 3 → C  (modulo 26)
   - To decrypt: shift backward by the same key (or shift forward by 26-key)
   
   Example with shift=3:
     Plain:  A B C ... X Y Z
     Cipher: D E F ... A B C
═══════════════════════════════════════════ */
function caesarEncrypt(text, shift){
  shift = ((shift % 26) + 26) % 26; // normalise to 0-25
  let result = '';
  for(let i=0; i<text.length; i++){
    const c = text[i];
    if(c >= 'A' && c <= 'Z'){
      // Uppercase: shift within A-Z
      result += String.fromCharCode(((c.charCodeAt(0) - 65 + shift) % 26) + 65);
    } else if(c >= 'a' && c <= 'z'){
      // Lowercase: shift within a-z
      result += String.fromCharCode(((c.charCodeAt(0) - 97 + shift) % 26) + 97);
    } else {
      // Numbers and spaces: pass through unchanged
      result += c;
    }
  }
  return result;
}

function caesarDecrypt(text, shift){
  // Decrypting = encrypting with (26 - shift)
  return caesarEncrypt(text, 26 - (shift % 26));
}

/* UI helpers */
function showErr(id,msg){ const el=document.getElementById(id); el.textContent='⚠️ '+msg; el.classList.add('visible'); }
function hideErr(id){ document.getElementById(id).classList.remove('visible'); }

function runEncrypt(){
  hideErr('enc-err');
  const text=document.getElementById('enc-input').value.trim();
  const shift=parseInt(document.getElementById('enc-key').value)||3;
  if(!text){ showErr('enc-err','Please enter or upload a message to encrypt.'); return; }
  if(shift<1||shift>100){ showErr('enc-err','Key must be between 1 and 100.'); return; }

  const result=caesarEncrypt(text,shift);
  document.getElementById('enc-out').textContent=result;
  const card=document.getElementById('enc-result');
  card.classList.add('visible');
  card.scrollIntoView({behavior:'smooth',block:'nearest'});
  saveToDB('encrypt',shift,text,result,encSource,'enc-save-status');
  updateDemo();
}

function runDecrypt(){
  hideErr('dec-err');
  const text=document.getElementById('dec-input').value.trim();
  const shift=parseInt(document.getElementById('dec-key').value)||3;
  if(!text){ showErr('dec-err','Please enter or upload a message to decrypt.'); return; }
  if(shift<1||shift>100){ showErr('dec-err','Key must be between 1 and 100.'); return; }

  const result=caesarDecrypt(text,shift);
  document.getElementById('dec-out').textContent=result;
  const card=document.getElementById('dec-result');
  card.classList.add('visible');
  card.scrollIntoView({behavior:'smooth',block:'nearest'});
  saveToDB('decrypt',shift,text,result,decSource,'dec-save-status');
}

function copyResult(id){
  const text=document.getElementById(id).textContent;
  navigator.clipboard.writeText(text).then(()=>{
    const t=document.getElementById('toast');
    t.classList.add('show');
    setTimeout(()=>t.classList.remove('show'),2000);
  });
}

/* SAVE TO DATABASE */
async function saveToDB(action,shift,inputText,outputText,source,statusId){
  const statusEl=document.getElementById(statusId);
  statusEl.textContent='Saving…';
  try{
    const fd=new FormData();
    fd.append('action',action);
    fd.append('shift',shift);
    fd.append('input_text',inputText);
    fd.append('output_text',outputText);
    fd.append('source',source||'typed');
    const res=await fetch('caesar_save.php',{method:'POST',body:fd});
    const data=await res.json();
    statusEl.textContent=data.status==='ok' ? '✅ Saved to database (log #'+data.log_id+')' : '⚠️ Save failed: '+data.message;
    setTimeout(()=>statusEl.textContent='',4000);
  }catch(e){
    statusEl.textContent='⚠️ Could not reach server.';
    setTimeout(()=>statusEl.textContent='',4000);
  }
}

/* LIVE ALPHABET DEMO */
const ALPHA='ABCDEFGHIJKLMNOPQRSTUVWXYZ';
function updateDemo(){
  const shift=((parseInt(document.getElementById('enc-key').value)||3)%26+26)%26;
  const plainRow=document.getElementById('plainRow');
  const cipherRow=document.getElementById('cipherRow');
  plainRow.innerHTML='';
  cipherRow.innerHTML='';
  for(let i=0;i<26;i++){
    const p=document.createElement('div'); p.className='alpha-cell plain'; p.textContent=ALPHA[i]; plainRow.appendChild(p);
    const c=document.createElement('div'); c.className='alpha-cell cipher'; c.textContent=ALPHA[(i+shift)%26]; cipherRow.appendChild(c);
  }
  const ex='HELLO';
  const exEnc=caesarEncrypt(ex,shift);
  document.getElementById('demoDesc').innerHTML=
    `With a shift of <strong>${shift}</strong>: each plain letter maps to the cipher letter directly below it.<br>`+
    `Example → Plain: <strong style="color:var(--sea5);font-family:'Courier Prime',monospace;">${ex}</strong> `+
    `&nbsp;|&nbsp; Encrypted: <strong style="color:var(--goldl);font-family:'Courier Prime',monospace;">${exEnc}</strong><br>`+
    `To decrypt, use the <strong>same key (${shift})</strong> and the shift reverses automatically.`;
}

// Sync demo when encrypt key changes
document.getElementById('enc-key').addEventListener('input',updateDemo);
updateDemo();
</script>
</body>
</html>

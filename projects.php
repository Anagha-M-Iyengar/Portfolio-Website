<?php
/**
 * projects.php — Anagha M Iyengar | Project Portfolio
 * Public page — no login required
 */
session_start();
$_SESSION['visited_intro'] = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Projects — Anagha M Iyengar</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --b1:#4a9cc7;--b2:#6ab4d8;--b3:#8ecae6;--b4:#b8dff0;
  --ink:#0d2b3e;--muted:#4a7a94;--deep:#1a4a6b;
  --ok:#1a7a40;--card:#fff;
}
*{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{
  font-family:'Jost',sans-serif;min-height:100vh;
  background:linear-gradient(160deg,#c5e8f5 0%,#d6eff8 28%,#e8f6fc 58%,#cce9f5 100%);
  color:var(--ink);padding-bottom:60px;
}
body::before{content:'';position:fixed;width:520px;height:520px;border-radius:50%;
  background:radial-gradient(circle,rgba(142,202,230,.2) 0%,transparent 70%);
  top:-110px;left:-110px;pointer-events:none;z-index:0;}
body::after{content:'';position:fixed;width:400px;height:400px;border-radius:50%;
  background:radial-gradient(circle,rgba(106,180,216,.15) 0%,transparent 70%);
  bottom:-80px;right:-80px;pointer-events:none;z-index:0;}

/* ── TOPBAR ── */
.topbar{
  position:sticky;top:0;z-index:100;
  background:rgba(255,255,255,.85);backdrop-filter:blur(14px);
  border-bottom:1px solid rgba(142,202,230,.4);
  padding:11px 28px;display:flex;justify-content:space-between;align-items:center;
}
.tb-name{font-family:'Cormorant Garamond',serif;font-size:1rem;font-weight:700;color:var(--deep);}
.tb-sub{font-size:.72rem;color:var(--muted);}
.tb-btn{
  display:inline-flex;align-items:center;gap:5px;
  background:transparent;border:1px solid rgba(74,156,199,.35);
  border-radius:20px;padding:6px 16px;
  font-family:'Jost',sans-serif;font-size:.75rem;font-weight:600;
  color:var(--b1);cursor:pointer;text-decoration:none;transition:background .2s;
}
.tb-btn:hover{background:rgba(142,202,230,.2);}

/* ── HERO ── */
.hero{
  position:relative;z-index:10;text-align:center;
  padding:44px 20px 32px;
}
.hero-badge{
  display:inline-flex;align-items:center;gap:6px;
  background:rgba(255,255,255,.7);border:1px solid rgba(142,202,230,.45);
  border-radius:20px;padding:5px 16px;font-size:.73rem;font-weight:600;
  color:var(--b1);margin-bottom:18px;letter-spacing:.05em;
}
.hero-title{
  font-family:'Cormorant Garamond',serif;font-size:clamp(1.7rem,4vw,2.4rem);
  font-weight:700;color:var(--ink);letter-spacing:.02em;margin-bottom:8px;
}
.hero-sub{font-size:.82rem;color:var(--muted);margin-bottom:18px;letter-spacing:.04em;}
.hero-desc{
  max-width:640px;margin:0 auto;
  font-size:.87rem;line-height:1.85;color:#2a4f65;
  background:rgba(255,255,255,.75);border:1px solid rgba(142,202,230,.4);
  border-radius:14px;padding:18px 24px;
}
.hero-desc a{color:var(--b1);font-weight:600;text-decoration:none;
  border-bottom:1px solid rgba(74,156,199,.35);}
.hero-desc a:hover{color:var(--deep);}

/* ── STATS ROW ── */
.stats-row{
  display:flex;justify-content:center;gap:0;flex-wrap:wrap;
  max-width:600px;margin:24px auto 0;
  background:rgba(255,255,255,.75);border:1px solid rgba(142,202,230,.4);
  border-radius:14px;overflow:hidden;position:relative;z-index:10;
}
.stat-item{
  flex:1;min-width:120px;text-align:center;padding:14px 10px;
  border-right:1px solid rgba(142,202,230,.3);
}
.stat-item:last-child{border-right:none;}
.stat-n{font-family:'Cormorant Garamond',serif;font-size:1.5rem;font-weight:700;
  color:var(--deep);display:block;}
.stat-l{font-size:.67rem;font-weight:600;letter-spacing:.07em;
  text-transform:uppercase;color:var(--muted);margin-top:2px;display:block;}

/* ── SECTION LABEL ── */
.section-label{
  max-width:900px;margin:32px auto 16px;padding:0 18px;
  font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;
  color:var(--b1);display:flex;align-items:center;gap:10px;
  position:relative;z-index:10;
}
.section-label::after{content:'';flex:1;height:1px;
  background:linear-gradient(to right,rgba(74,156,199,.3),transparent);}

/* ── PROJECT CARDS ── */
.projects{
  max-width:900px;margin:0 auto;padding:0 18px;
  display:flex;flex-direction:column;gap:20px;
  position:relative;z-index:10;
}

.proj-card{
  background:rgba(255,255,255,.9);
  border:1.5px solid rgba(142,202,230,.4);
  border-radius:18px;overflow:hidden;
  box-shadow:0 4px 24px rgba(13,92,130,.08);
  transition:box-shadow .25s,transform .25s;
}
.proj-card:hover{box-shadow:0 10px 38px rgba(13,92,130,.14);transform:translateY(-3px);}
.proj-card:nth-child(n){animation:slideIn .45s ease both;}
.proj-card:nth-child(2){animation-delay:.06s;}
.proj-card:nth-child(3){animation-delay:.12s;}
.proj-card:nth-child(4){animation-delay:.18s;}
.proj-card:nth-child(5){animation-delay:.24s;}
.proj-card:nth-child(6){animation-delay:.30s;}
@keyframes slideIn{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}

/* Card header — clickable to expand */
.proj-header{
  display:flex;align-items:center;gap:14px;
  padding:18px 22px;cursor:pointer;
  user-select:none;
  background:linear-gradient(to right,rgba(214,239,248,.45),rgba(232,246,252,.2));
  transition:background .2s;
}
.proj-header:hover{background:rgba(214,239,248,.65);}
.proj-num{
  width:40px;height:40px;border-radius:50%;flex-shrink:0;
  background:linear-gradient(135deg,var(--b1),var(--b2));
  display:flex;align-items:center;justify-content:center;
  font-family:'Cormorant Garamond',serif;font-size:1.05rem;font-weight:700;color:#fff;
  box-shadow:0 3px 10px rgba(74,156,199,.3);
}
.proj-icon{font-size:1.5rem;flex-shrink:0;}
.proj-meta{flex:1;}
.proj-title{
  font-family:'Cormorant Garamond',serif;font-size:1.05rem;font-weight:700;
  color:var(--deep);line-height:1.3;
}
.proj-tags{display:flex;flex-wrap:wrap;gap:5px;margin-top:6px;}
.tag{
  background:rgba(142,202,230,.2);border:1px solid rgba(74,156,199,.22);
  border-radius:20px;padding:2px 10px;font-size:.67rem;font-weight:600;color:var(--deep);
}
.tag.highlight{background:rgba(26,122,64,.1);border-color:rgba(26,122,64,.3);color:var(--ok);}
.proj-chevron{
  font-size:1rem;color:var(--muted);transition:transform .3s;flex-shrink:0;
}
.proj-card.open .proj-chevron{transform:rotate(180deg);}

/* Card body — collapsible */
.proj-body{
  padding:0 22px;max-height:0;overflow:hidden;
  transition:max-height .35s ease,padding .25s ease;
  border-top:0px solid rgba(142,202,230,.2);
}
.proj-card.open .proj-body{
  max-height:600px;
  padding:16px 22px 20px;
  border-top-width:1px;
}
.proj-desc{font-size:.86rem;line-height:1.82;color:#2a4f65;margin-bottom:12px;}

/* Links */
.proj-links{display:flex;flex-wrap:wrap;gap:8px;margin-top:4px;}
.proj-link{
  display:inline-flex;align-items:center;gap:6px;
  padding:7px 15px;border-radius:20px;
  background:rgba(74,156,199,.1);border:1px solid rgba(74,156,199,.28);
  font-size:.76rem;font-weight:600;color:var(--b1);
  text-decoration:none;transition:all .2s;
}
.proj-link:hover{background:rgba(74,156,199,.22);color:var(--deep);transform:translateY(-1px);}
.proj-link.green{background:rgba(26,122,64,.08);border-color:rgba(26,122,64,.28);color:var(--ok);}
.proj-link.green:hover{background:rgba(26,122,64,.18);}

/* Note box */
.proj-note{
  display:flex;align-items:flex-start;gap:9px;
  background:rgba(214,239,248,.4);border:1px solid rgba(74,156,199,.2);
  border-radius:10px;padding:10px 14px;font-size:.79rem;
  color:var(--deep);margin-top:10px;line-height:1.65;
}
.proj-note a{color:var(--b1);font-weight:600;text-decoration:none;}
.note-icon{flex-shrink:0;font-size:1rem;margin-top:1px;}

/* Live client badge */
.live-badge{
  display:inline-flex;align-items:center;gap:5px;
  background:rgba(26,122,64,.1);border:1px solid rgba(26,122,64,.3);
  border-radius:20px;padding:3px 11px;font-size:.69rem;font-weight:700;
  color:var(--ok);margin-left:8px;vertical-align:middle;
}
.live-dot{width:7px;height:7px;border-radius:50%;background:var(--ok);
  animation:blink 1.4s ease-in-out infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.25}}

/* ── CLOSING ── */
.closing{
  max-width:640px;margin:36px auto 0;padding:0 18px;
  text-align:center;position:relative;z-index:10;
}
.closing-card{
  background:rgba(255,255,255,.88);
  border:1.5px solid rgba(142,202,230,.45);
  border-radius:18px;padding:28px 32px;
  box-shadow:0 4px 24px rgba(13,92,130,.08);
}
.closing-card::before{
  content:'';display:block;width:44px;height:3px;
  background:linear-gradient(to right,var(--b1),var(--b3));
  margin:0 auto 16px;border-radius:3px;
}
.closing-title{font-family:'Cormorant Garamond',serif;font-size:1.3rem;font-weight:700;
  color:var(--deep);margin-bottom:10px;}
.closing-card p{font-size:.87rem;line-height:1.85;color:#2a4f65;}
.closing-card a{color:160deg,#c5e8f5 0%,#d6eff8 30%,#e8f6fc 60%,#cce9f5 100%);padding:24px 18px;
  border-bottom:1px solid rgba(74,156,199,.35);}
.closing-btn{
  display:inline-flex;align-items:center;gap:8px;
  margin-top:18px;background:linear-gradient(135deg,var(--b1),var(--b2));
  color:fff;padding:11px 28px;border-radius:50px;text-decoration:none;
  font-size:.86rem;font-weight:600;box-shadow:0 5px 18px rgba(74,156,199,.32);
  transition:transform .2s,box-shadow .2s;
}
.closing-btn:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(74,156,199,.45);}

.page-footer{text-align:center;margin-top:24px;font-size:.72rem;
  color:var(--muted);position:relative;z-index:10;}
.page-footer a{color:var(--b1);text-decoration:none;}

@media(max-width:560px){
  .proj-header{padding:14px 16px;}
  .proj-card.open .proj-body{padding:14px 16px 18px;}
  .topbar{padding:10px 16px;}
}
</style>
</head>
<body>

<!-- TOPBAR -->
<div class="topbar">
  <div>
    <div class="tb-name">Anagha M Iyengar</div>
    <div class="tb-sub">Project Portfolio</div>
  </div>
  <a href="index.php" class="tb-btn">🏠 Home</a>
</div>

<!-- HERO -->
<div class="hero">
  <div class="hero-badge">📁 Academic &amp; Practical Projects</div>
  <h1 class="hero-title">Project Portfolio</h1>
  <p class="hero-sub">Anagha M Iyengar &nbsp;·&nbsp; BSc Forensic Science &nbsp;·&nbsp; Jain University, 2026</p>
  <div class="hero-desc">
    A collection of academic and practical projects undertaken during my BSc in Forensic Science,
    spanning digital forensics, cybersecurity, full-stack development, and cryptography.<br><br>
    For enquiries or collaborations, please
    <a href="login.php">log in to my portfolio</a> and reach out via the contact form.
  </div>
</div>

<!-- STATS -->
<div class="stats-row">
  <div class="stat-item"><span class="stat-n">6</span><span class="stat-l">Projects</span></div>
  <div class="stat-item"><span class="stat-n">2</span><span class="stat-l">Live Cases</span></div>
  <div class="stat-item"><span class="stat-n">3+</span><span class="stat-l">Internships</span></div>
  <div class="stat-item"><span class="stat-n">4+</span><span class="stat-l">Certifications</span></div>
</div>

<div class="section-label">⚓ Click any project to expand details</div>

<!-- PROJECTS -->
<div class="projects">

  <!-- 1 -->
  <div class="proj-card" id="p1">
    <div class="proj-header" onclick="toggle('p1')">
      <div class="proj-num">1</div>
      <span class="proj-icon">🖥️</span>
      <div class="proj-meta">
        <div class="proj-title">Hardware &amp; Digital Forensics Fundamentals</div>
        <div class="proj-tags">
          <span class="tag">Digital Forensics</span>
          <span class="tag">Hardware</span>
          <span class="tag">Evidence Handling</span>
          <span class="tag">HDD / SSD</span>
        </div>
      </div>
      <span class="proj-chevron">▾</span>
    </div>
    <div class="proj-body">
      <p class="proj-desc">
        Gained practical experience in CPU dismantling and reassembly, forensic seizure of Windows
        systems, and examination of HDD and SSD internals. Applied forensic best practices to handle
        physical storage media while preserving evidence integrity — a fundamental requirement in any
        real-world forensic investigation. Developed foundational competency in digital evidence
        acquisition and chain-of-custody procedures.
      </p>
    </div>
  </div>

  <!-- 2 -->
  <div class="proj-card" id="p2">
    <div class="proj-header" onclick="toggle('p2')">
      <div class="proj-num">2</div>
      <span class="proj-icon">🌐</span>
      <div class="proj-meta">
        <div class="proj-title">Full-Stack Web Application Development</div>
        <div class="proj-tags">
          <span class="tag">PHP</span><span class="tag">MySQL</span>
          <span class="tag">HTML / CSS / JS</span><span class="tag">XAMPP</span><span class="tag">Ngrok</span>
        </div>
      </div>
      <span class="proj-chevron">▾</span>
    </div>
    <div class="proj-body">
      <p class="proj-desc">
        Designed and built a complete, multi-page web application comprising a CV portfolio, a secure
        login and registration system with CAPTCHA, an interactive contact form, and a dynamic homepage.
        Integrated a MySQL relational database via phpMyAdmin for structured data storage, replacing
        CSV-based storage for improved reliability and scalability. Deployed locally using XAMPP and
        established temporary public access via Ngrok for demonstration purposes.
      </p>
      <div class="proj-note">
        <span class="note-icon">💡</span>
        <span>To access the live portfolio and interact directly, please
        <a href="login.php">sign in</a> and use the chat feature on the CV page.</span>
      </div>
    </div>
  </div>

  <!-- 3 -->
  <div class="proj-card" id="p3">
    <div class="proj-header" onclick="toggle('p3')">
      <div class="proj-num">3</div>
      <span class="proj-icon">🛡️</span>
      <div class="proj-meta">
        <div class="proj-title">Web Application Security &amp; Hardening</div>
        <div class="proj-tags">
          <span class="tag">SHA-256</span><span class="tag">Burp Suite</span>
          <span class="tag">SQL Injection</span><span class="tag">CAPTCHA</span>
          <span class="tag">Penetration Testing</span>
        </div>
      </div>
      <span class="proj-chevron">▾</span>
    </div>
    <div class="proj-body">
      <p class="proj-desc">
        Implemented SHA-256 password hashing, input sanitisation, and CAPTCHA verification to protect
        user credentials against brute-force and injection attacks. Conducted offensive security
        assessments using Burp Suite to simulate attack vectors on the hosted application, including
        brute-force login attempts and parameter manipulation. Remediated all identified vulnerabilities,
        enforced prepared statements throughout to eliminate SQL injection risks, and secured all
        unsecured access points.
      </p>
    </div>
  </div>

  <!-- 4 -->
  <div class="proj-card" id="p4">
    <div class="proj-header" onclick="toggle('p4')">
      <div class="proj-num">4</div>
      <span class="proj-icon">🔐</span>
      <div class="proj-meta">
        <div class="proj-title">Cryptography &amp; Encryption Tools</div>
        <div class="proj-tags">
          <span class="tag">RSA-2048</span><span class="tag">AES-256</span>
          <span class="tag">OpenSSL</span><span class="tag">Digital Signature</span>
          <span class="tag">Rail Fence</span><span class="tag">Caesar Cipher</span>
        </div>
      </div>
      <span class="proj-chevron">▾</span>
    </div>
    <div class="proj-body">
      <p class="proj-desc">
        Studied and implemented classical ciphers — Rail Fence (zigzag transposition) and Caesar
        (letter-shift substitution) — as fully interactive, browser-based tools integrated into the
        web application. Extended the project to asymmetric cryptography by generating RSA-2048
        public-private key pairs using OpenSSL via Git Bash, and implementing hybrid RSA+AES-256
        file encryption and decryption with passphrase-protected key support. Integrated a digital
        signature module enabling SHA-256-based file signing and integrity verification.
      </p>
      <div class="proj-note">
        <span class="note-icon">🏴‍☠️</span>
        <span>All cipher and encryption tools are live — press
        <a href="index.php">Tools on the home page</a> to explore them interactively.</span>
      </div>
    </div>
  </div>

  <!-- 5 -->
  <div class="proj-card" id="p5">
    <div class="proj-header" onclick="toggle('p5')">
      <div class="proj-num">5</div>
      <span class="proj-icon">🔍</span>
      <div class="proj-meta">
        <div class="proj-title">Log Analysis &amp; Digital Investigation</div>
        <div class="proj-tags">
          <span class="tag">Autopsy</span><span class="tag">FTK Imager</span>
          <span class="tag">Python</span><span class="tag">File Carving</span>
          <span class="tag">Event Viewer</span>
        </div>
      </div>
      <span class="proj-chevron">▾</span>
    </div>
    <div class="proj-body">
      <p class="proj-desc">
        Performed detailed log analysis using Windows Event Viewer and developed a Python script to
        monitor and record system-level trigger events, categorised by Warning, Critical, and Error
        severity. Conducted file signature analysis and file carving to recover and examine digital
        artefacts from storage media. Applied industry-standard forensic tools — Autopsy and
        FTK Imager — to investigate disk image files and extract relevant evidence.
      </p>
      <div class="proj-links">
        <a href="https://docs.google.com/spreadsheets/d/1bPCIBEVzzZ6SkDSW3l3TnxIDiif5T8o1Rpo16JEao1o/edit?usp=sharing"
           target="_blank" class="proj-link">📊 File Signature Analysis →</a>
        <a href="https://docs.google.com/spreadsheets/d/1csh8JcWKM1vunSLDIot7Inhr9e1oarg1WO-PvrVWL6c/edit?usp=sharing"
           target="_blank" class="proj-link">📁 File Carving Report →</a>
        <a href="https://docs.google.com/document/d/1v4yP0VoKQjircMc27DXYvKd01wPMcvvOajpksWiPji0/edit?usp=sharing"
           target="_blank" class="proj-link">📄 Log Analysis Report →</a>
      </div>
    </div>
  </div>

  <!-- 6 — LIVE CLIENT CASES -->
  <div class="proj-card" id="p6">
    <div class="proj-header" onclick="toggle('p6')">
      <div class="proj-num">6</div>
      <span class="proj-icon">⚖️</span>
      <div class="proj-meta">
        <div class="proj-title">
          Live Client Forensic Case Investigations
          <span class="live-badge"><span class="live-dot"></span>Live Cases</span>
        </div>
        <div class="proj-tags">
          <span class="tag highlight">Real-World Cases</span>
          <span class="tag">Audio Forensics</span>
          <span class="tag">Integrity Analysis</span>
          <span class="tag">Evidence Reporting</span>
          <span class="tag">Chain of Custody</span>
        </div>
      </div>
      <span class="proj-chevron">▾</span>
    </div>
    <div class="proj-body">
      <p class="proj-desc">
        Engaged in two real-world client forensic case investigations under the supervision of
        <strong>Dr. Abdul Shareef</strong>, applying academic forensic training to live evidentiary
        matters.
      </p>
      <div class="proj-note">
        <span class="note-icon">🔒</span>
        <span>All client case details, findings, and documentation are confidential and are not
        publicly disclosed in accordance with professional ethics and client privacy obligations.</span>
      </div>
    </div>
  </div>
<!-- CLOSING -->
<div class="closing">
  <div class="closing-card">
    <div class="closing-title">Thank You for Visiting</div>
    <p>
      I appreciate you taking the time to review my work. Each of these projects reflects my
      commitment to building practical expertise in <strong>cybersecurity and digital forensics</strong>.<br><br>
      If any project resonates with you, or if you have an opportunity you would like to discuss,
      I would be very glad to hear from you.
    </p>
    <a href="login.php" class="closing-btn">💬 Get In Touch →</a>
  </div>
</div>

<div class="page-footer" style="margin-top:24px;">
  <p>© 2026 Anagha M Iyengar &nbsp;·&nbsp;
  <a href="index.php">Home</a> &nbsp;·&nbsp;
  <a href="login.php">Portfolio</a></p>
</div>

<script>
function toggle(id){
  const card=document.getElementById(id);
  card.classList.toggle('open');
}
// Open first card by default so the page doesn't look empty on load
document.addEventListener('DOMContentLoaded',()=>{
  document.getElementById('p1').classList.add('open');
});
</script>
</body>
</html>

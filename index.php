<?php
/**
 * index.php — The one and only landing page.
 *
 * HOW TO DEPLOY:
 *   1. Delete intro.php from your anagha/ folder (not needed anymore)
 *   2. Replace index.php with this file
 *   3. Visit 192.168.x.x/anagha/ — this page opens automatically
 *
 * Everything gate_check.php and auth_check.php redirect to "index.php" now.
 */
session_start();
$_SESSION['visited_intro'] = true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Anagha M Iyengar – Portfolio</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --b1:#4a9cc7;--b2:#6ab4d8;--b3:#8ecae6;--b4:#b8dff0;
  --ink:#0d2b3e;--muted:#4a7a94;--deep:#1a4a6b;
}
*{box-sizing:border-box;margin:0;padding:0;}
html,body{min-height:100vh;}

/* ── BACKGROUND ── */
body{
  font-family:'Jost',sans-serif;
  background:linear-gradient(160deg,#c5e8f5 0%,#d6eff8 30%,#e8f6fc 60%,#cce9f5 100%);
  /* Use min-height + flex so card is centred but page can scroll if needed */
  display:flex;align-items:flex-start;justify-content:center;
  padding:32px 20px 40px;
  position:relative;overflow-x:hidden;
}
body::before{content:'';position:fixed;width:500px;height:500px;border-radius:50%;
  background:radial-gradient(circle,rgba(142,202,230,.22) 0%,transparent 70%);
  top:-120px;left:-120px;pointer-events:none;}
body::after{content:'';position:fixed;width:400px;height:400px;border-radius:50%;
  background:radial-gradient(circle,rgba(106,180,216,.18) 0%,transparent 70%);
  bottom:-80px;right:-80px;pointer-events:none;}
.shape{position:fixed;border-radius:50%;opacity:.18;pointer-events:none;
  animation:drift ease-in-out infinite;}
@keyframes drift{0%,100%{transform:translateY(0) scale(1)}50%{transform:translateY(-24px) scale(1.04)}}

/* ── CARD ── */
/* max-width:580px keeps it readable on any screen.
   No fixed height — card grows with content.
   align-self:flex-start prevents stretching on tall screens. */
.card{
  background:rgba(255,255,255,.88);
  border:1.5px solid rgba(142,202,230,.5);
  border-radius:24px;
  max-width:580px;width:100%;
  padding:36px 48px 32px;
  box-shadow:0 16px 48px rgba(13,92,130,.12);
  position:relative;z-index:10;
  align-self:flex-start;   /* don't stretch to full viewport height */
  animation:rise .7s cubic-bezier(.34,1.4,.64,1) both;
}
@keyframes rise{from{opacity:0;transform:translateY(36px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}
.card::before{
  content:'';position:absolute;top:0;left:0;right:0;height:4px;
  background:linear-gradient(to right,var(--b1),var(--b3),var(--b4));
  border-radius:24px 24px 0 0;
}

/* ── HEADER SECTION ── */
.initials{
  width:62px;height:62px;border-radius:50%;
  background:linear-gradient(135deg,var(--b1),var(--b3));
  display:flex;align-items:center;justify-content:center;
  font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-weight:700;color:#fff;
  margin:0 auto 16px;box-shadow:0 6px 20px rgba(74,156,199,.3);
}
.greeting{
  font-size:.7rem;font-weight:600;letter-spacing:.18em;text-transform:uppercase;
  color:var(--b1);text-align:center;margin-bottom:8px;
}
h1{
  font-family:'Cormorant Garamond',serif;font-size:2rem;font-weight:700;
  color:var(--ink);text-align:center;letter-spacing:.02em;margin-bottom:6px;line-height:1.15;
}
.role{
  text-align:center;font-size:.76rem;font-weight:500;letter-spacing:.08em;
  text-transform:uppercase;color:var(--muted);margin-bottom:18px;
}
.div-line{
  height:1px;
  background:linear-gradient(to right,transparent,rgba(74,156,199,.3),transparent);
  margin-bottom:16px;
}

/* ── BIO ── */
.bio{
  font-size:.89rem;line-height:1.78;color:#2a4f65;
  text-align:center;margin-bottom:16px;
}

/* ── SKILLS ── */
.skills{
  display:flex;flex-wrap:wrap;justify-content:center;
  gap:7px;margin-bottom:14px;
}
.skill{
  background:rgba(142,202,230,.18);border:1px solid rgba(74,156,199,.3);
  border-radius:20px;padding:5px 14px;font-size:.73rem;font-weight:600;color:var(--deep);
}

/* ── PUBLIC KEY ROW ── */
.pubkey-row{
  text-align:center;margin-bottom:18px;
  padding:9px 16px;
  background:rgba(214,239,248,.35);border:1px dashed rgba(74,156,199,.35);
  border-radius:10px;font-size:.79rem;color:var(--muted);
}
.pubkey-row a{
  color:var(--deep);font-weight:700;text-decoration:none;
  border-bottom:1px solid rgba(26,74,107,.3);transition:color .2s;
}
.pubkey-row a:hover{color:var(--b1);}

/* ── CTA BUTTONS ── */
.cta{
  display:flex;flex-direction:column;align-items:center;gap:10px;
  margin-bottom:4px;
}
/* All three buttons same width for visual consistency */
.btn-main,
.btn-tools,
.btn-projects{
  display:inline-flex;align-items:center;justify-content:center;gap:10px;
  width:240px;           /* fixed width so all buttons are the same size */
  padding:13px 0;
  border-radius:50px;border:none;cursor:pointer;
  font-family:'Jost',sans-serif;font-size:.92rem;font-weight:600;
  transition:transform .2s,box-shadow .2s;text-decoration:none;
}
.btn-main{
  background:linear-gradient(135deg,var(--b1),var(--b2));color:#fff;
  box-shadow:0 6px 22px rgba(74,156,199,.38);
}
.btn-main:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(74,156,199,.5);}
.btn-tools{
  background:linear-gradient(135deg,#1a5c6e,#2a8fa8);color:#fff;
  box-shadow:0 6px 22px rgba(26,92,110,.32);
}
.btn-tools:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(26,92,110,.48);}
.btn-projects{
  background:linear-gradient(135deg,#2e7d5e,#3dab7e);color:#fff;
  box-shadow:0 6px 22px rgba(46,125,94,.3);
}
.btn-projects:hover{transform:translateY(-2px);box-shadow:0 10px 30px rgba(46,125,94,.45);}

.tools-hint{font-size:.73rem;color:var(--muted);text-align:center;}
.tools-hint span{color:#2a8fa8;font-weight:600;}
.auth-hint{font-size:.76rem;color:var(--muted);text-align:center;}
.auth-hint a{
  color:var(--b1);font-weight:600;text-decoration:none;
  border-bottom:1px solid rgba(74,156,199,.4);
}
.auth-hint a:hover{color:var(--deep);}

/* ── STATS ── */
.stats{
  display:flex;justify-content:center;gap:26px;flex-wrap:wrap;
  margin-top:20px;padding-top:18px;
  border-top:1px solid rgba(142,202,230,.3);
}
.stat{text-align:center;}
.stat-num{
  font-family:'Cormorant Garamond',serif;font-size:1.45rem;font-weight:700;
  color:var(--deep);display:block;line-height:1;
}
.stat-lbl{
  font-size:.68rem;font-weight:600;letter-spacing:.06em;text-transform:uppercase;
  color:var(--muted);margin-top:4px;display:block;
}

/* ── RESPONSIVE ── */
/* On small phones, reduce side padding */
@media(max-width:480px){
  .card{padding:28px 20px 24px;}
  h1{font-size:1.7rem;}
  .btn-main,.btn-tools,.btn-projects{width:100%;max-width:280px;}
  .stats{gap:16px;}
}
/* On tablet/desktop, keep card centred with breathing room */
@media(min-width:768px){
  body{align-items:center;padding:40px 20px;}
}
</style>
</head>
<body>
<div class="shape" style="width:300px;height:300px;background:#8ecae6;top:5%;right:8%;animation-duration:9s;"></div>
<div class="shape" style="width:220px;height:220px;background:#6ab4d8;bottom:10%;left:6%;animation-duration:11s;animation-delay:2s;"></div>

<div class="card">

  <!-- Header -->
  <div class="initials">AI</div>
  <div class="greeting">Welcome</div>
  <h1>Anagha M Iyengar</h1>
  <p class="role">BSc Forensic Science &nbsp;·&nbsp; Jain University, Bengaluru &nbsp;·&nbsp; 2026</p>
  <div class="div-line"></div>

  <!-- Bio -->
  <p class="bio">
    I am a final-year BSc Forensic Science student aspiring to pursue my career in the cybersecurity
    field. With practical internship experience and a focus on digital forensics and ethical security
    practice, I am actively seeking opportunities to contribute meaningfully to the industry.
  </p>

  <!-- Skills -->
  <div class="skills">
    <span class="skill">🔐 Cybersecurity &amp; Ethical Hacking</span>
    <span class="skill">🔬 Digital Forensics &amp; DFIR</span>
    <span class="skill">🌐 Web Application Security</span>
  </div>

  <!-- Public Key -->
  <div class="pubkey-row">
    🔑 &nbsp;<a href="anagha_public.key" download>Click here to download my Public Key</a>
  </div>

  <!-- CTA Buttons -->
  <div class="cta">
    <a href="<?= isset($_SESSION['logged_in']) && $_SESSION['logged_in']===true ? 'cv.php' : 'login.php' ?>"
       class="btn-main">View Portfolio &nbsp;→</a>
    <a href="cipher_hub.php" target="_blank"
       class="btn-tools">🏴‍☠️ Tools &nbsp;→</a>
    <a href="projects.php" target="_blank"
       class="btn-projects">📁 Projects &nbsp;→</a>
    <p class="tools-hint">Ciphers, encryption &amp; more — <span>Click Tools ↑</span></p>
    <p class="auth-hint">
      New here? <a href="signup.php">Create an account</a>
      &nbsp;·&nbsp; Already registered? <a href="login.php">Sign in</a>
    </p>
  </div>

  <!-- Stats -->
  <div class="stats">
    <div class="stat"><span class="stat-num">3+</span><span class="stat-lbl">Internships</span></div>
    <div class="stat"><span class="stat-num">4+</span><span class="stat-lbl">Certifications</span></div>
    <div class="stat"><span class="stat-num">2026</span><span class="stat-lbl">Graduating</span></div>
    <div class="stat"><span class="stat-num">2+</span><span class="stat-lbl">Years Experience</span></div>
  </div>

</div><!-- /card -->
</body>
</html>

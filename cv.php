<?php require_once 'auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Anagha M Iyengar – Portfolio</title>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{
  --sea1:#c8e6f5;--sea2:#d6eff8;--sea3:#e8f6fc;--sea4:#f0faff;
  --deep:#1a4a6b;--mid:#2e7da8;--soft:#5ba8cc;--mist:#a8d8ea;
  --ink:#122b3e;--muted:#5a7f94;--accent:#0d7ab5;
  --err:#c0392b;--ok:#00875a;
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
html{scroll-behavior:smooth;}
body{font-family:'Jost',sans-serif;background:linear-gradient(160deg,#dff2fb 0%,#c5e8f5 30%,#e8f6fc 60%,#d0edf8 100%);min-height:100vh;color:var(--ink);padding:0 0 80px;}

/* TOP BAR */
.topbar{background:rgba(255,255,255,.82);backdrop-filter:blur(12px);border-bottom:1px solid rgba(200,230,245,.7);padding:11px 28px;display:flex;justify-content:space-between;align-items:center;position:sticky;top:0;z-index:100;}
.tb-name{font-family:'Cormorant Garamond',serif;font-size:1rem;font-weight:700;color:var(--deep);}
.tb-user{font-size:.75rem;color:var(--muted);}
.tb-actions{display:flex;gap:10px;}
.tb-btn{display:inline-flex;align-items:center;gap:5px;background:transparent;border:1px solid rgba(91,168,204,.4);border-radius:20px;padding:6px 16px;font-family:'Jost',sans-serif;font-size:.75rem;font-weight:600;color:var(--mid);cursor:pointer;text-decoration:none;transition:background .2s,transform .2s;}
.tb-btn:hover{background:rgba(200,230,245,.5);transform:translateY(-1px);}
.tb-btn.logout{color:var(--err);border-color:rgba(192,57,43,.25);}
.tb-btn.logout:hover{background:rgba(192,57,43,.05);}

.page{max-width:820px;margin:0 auto;padding:26px 16px 0;}

/* HERO */
.hero{position:relative;background:linear-gradient(135deg,#0d5c82 0%,#1a7aab 40%,#2e9fcc 100%);border-radius:22px;overflow:hidden;padding:40px 40px 32px;margin-bottom:22px;box-shadow:0 12px 48px rgba(13,92,130,.22);animation:fadeDown .6s ease both;}
@keyframes fadeDown{from{opacity:0;transform:translateY(-16px)}to{opacity:1;transform:translateY(0)}}
.hero::before{content:'';position:absolute;inset:0;background:radial-gradient(ellipse at 30% 50%,rgba(255,255,255,.08) 0%,transparent 60%);}
.hero-content{position:relative;z-index:2;max-width:580px;}
.hero-name{font-family:'Cormorant Garamond',serif;font-size:2.6rem;font-weight:700;color:#fff;line-height:1.1;letter-spacing:.01em;margin-bottom:6px;}
.hero-tagline{color:var(--mist);font-size:.95rem;font-weight:400;letter-spacing:.06em;text-transform:uppercase;margin-bottom:16px;}
.hero-contacts{display:flex;flex-wrap:wrap;gap:10px 22px;}
.contact-pill{display:flex;align-items:center;gap:6px;color:#e8f6fc;font-size:.85rem;text-decoration:none;}
.contact-pill svg{width:14px;height:14px;fill:var(--mist);flex-shrink:0;}

/* GRID */
.grid{display:grid;grid-template-columns:1fr 1fr;gap:18px;margin-bottom:18px;animation:fadeUp .6s .1s ease both;}
@keyframes fadeUp{from{opacity:0;transform:translateY(18px)}to{opacity:1;transform:translateY(0)}}
.grid-full{grid-column:1/-1;}

/* CARDS */
.card{background:rgba(255,255,255,.78);backdrop-filter:blur(8px);border-radius:16px;border:1px solid rgba(200,230,245,.7);padding:22px 24px;box-shadow:0 4px 20px rgba(13,92,130,.07);transition:box-shadow .2s;}
.card:hover{box-shadow:0 8px 32px rgba(13,92,130,.13);}
.card-title{font-family:'Cormorant Garamond',serif;font-size:1.05rem;font-weight:700;color:var(--deep);margin-bottom:14px;padding-bottom:8px;border-bottom:2px solid var(--sea1);display:flex;align-items:center;gap:8px;}

.edu-name{font-weight:600;color:var(--deep);font-size:.93rem;}
.edu-sub{color:var(--muted);font-size:.82rem;margin:2px 0 6px;line-height:1.5;}
.badge{display:inline-block;background:linear-gradient(135deg,var(--mid),var(--soft));color:#fff;font-size:.72rem;font-weight:600;padding:3px 10px;border-radius:50px;letter-spacing:.04em;}

.skills-group{margin-bottom:10px;}
.skills-label{font-size:.75rem;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);font-weight:600;margin-bottom:5px;}
.tag-row{display:flex;flex-wrap:wrap;gap:5px;}
.tag{background:var(--sea2);border:1px solid var(--sea1);color:var(--deep);font-size:.79rem;padding:3px 10px;border-radius:50px;font-weight:500;}

.exp-item{margin-bottom:16px;}.exp-item:last-child{margin-bottom:0;}
.exp-role{font-weight:600;color:var(--ink);font-size:.9rem;}
.exp-meta{display:flex;justify-content:space-between;font-size:.78rem;color:var(--muted);margin:2px 0 6px;flex-wrap:wrap;gap:2px;}
.exp-bullets{padding-left:14px;}
.exp-bullets li{font-size:.82rem;color:#2a4f65;line-height:1.6;margin-bottom:2px;}
.exp-divider{border:none;border-top:1px dashed var(--sea1);margin:12px 0;}

.project-title{font-weight:600;color:var(--ink);font-size:.9rem;margin-bottom:2px;}
.project-tech{font-size:.76rem;color:var(--soft);font-weight:500;margin-bottom:6px;font-style:italic;}

.cert-item{display:flex;gap:10px;margin-bottom:10px;align-items:flex-start;}
.cert-dot{width:8px;height:8px;border-radius:50%;background:var(--mid);margin-top:5px;flex-shrink:0;}
.cert-name{font-size:.84rem;font-weight:600;color:var(--ink);line-height:1.4;}
.cert-issuer{font-size:.76rem;color:var(--muted);margin-top:1px;}

.ach-item{background:linear-gradient(135deg,rgba(200,230,245,.4),rgba(224,243,251,.3));border-left:3px solid var(--mid);border-radius:0 10px 10px 0;padding:10px 14px;margin-bottom:8px;font-size:.83rem;}
.ach-item strong{color:var(--deep);display:block;margin-bottom:2px;font-size:.87rem;}
.ach-item span{color:var(--muted);font-size:.77rem;}
.vol-item{margin-bottom:8px;}
.vol-title{font-weight:600;color:var(--ink);font-size:.86rem;}
.vol-org{font-size:.78rem;color:var(--soft);}
.vol-desc{font-size:.79rem;color:#3d6278;line-height:1.5;margin-top:2px;}

/* CONTACT FORM */
.form-card{background:linear-gradient(160deg,rgba(200,230,245,.5),rgba(224,243,251,.6));border:1px solid rgba(91,168,204,.3);border-radius:20px;padding:36px 36px 32px;animation:fadeUp .6s .3s ease both;margin-top:4px;}
.form-title{font-family:'Cormorant Garamond',serif;font-size:1.55rem;font-weight:700;color:var(--deep);margin-bottom:6px;}
.form-subtitle{font-size:.85rem;color:var(--muted);margin-bottom:26px;}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px;}
.form-group{display:flex;flex-direction:column;gap:4px;}
.form-group label{font-size:.75rem;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:var(--muted);}
.form-group input,.form-group textarea{padding:10px 14px;border:1.5px solid rgba(91,168,204,.35);border-radius:10px;font-family:'Jost',sans-serif;font-size:.88rem;color:var(--ink);background:rgba(255,255,255,.88);outline:none;transition:border-color .2s,box-shadow .2s;}
.form-group input:focus,.form-group textarea:focus{border-color:var(--mid);box-shadow:0 0 0 3px rgba(46,125,168,.1);}
.form-group input.invalid{border-color:var(--err);}
.form-group textarea{min-height:90px;resize:vertical;}
.err-p{color:var(--err);font-size:.72rem;margin-top:2px;min-height:14px;}
.submit-btn{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--deep),var(--mid));color:#fff;border:none;padding:12px 32px;border-radius:50px;font-family:'Jost',sans-serif;font-size:.92rem;font-weight:600;cursor:pointer;box-shadow:0 6px 20px rgba(13,92,130,.22);transition:transform .2s,box-shadow .2s;margin-top:6px;}
.submit-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(13,92,130,.32);}
.submit-btn svg{width:15px;height:15px;stroke:currentColor;fill:none;stroke-width:2.5;stroke-linecap:round;stroke-linejoin:round;}

#thankYou{display:none;text-align:center;padding:28px 0 8px;}
.penguin-form-wrap svg{width:100px;height:auto;animation:wd 1.6s ease-in-out infinite;}
@keyframes wd{0%,100%{transform:rotate(-5deg)}50%{transform:rotate(5deg)}}
.ty-title{font-family:'Cormorant Garamond',serif;font-size:1.4rem;font-weight:700;color:var(--deep);margin:12px 0 5px;}
.ty-msg{font-size:.85rem;color:var(--muted);line-height:1.7;}

/* ═══════════════════════════════════════
   FLOATING PENGUIN BOT
═══════════════════════════════════════ */
#penguinBot{
  position:fixed;bottom:28px;right:28px;
  z-index:9000;cursor:pointer;
  filter:drop-shadow(0 6px 18px rgba(13,92,130,.28));
  animation:bounce 2.5s ease-in-out infinite;
  transition:transform .2s;
}
#penguinBot:hover{transform:scale(1.1);}
@keyframes bounce{0%,100%{transform:translateY(0)}50%{transform:translateY(-10px)}}

/* tooltip above penguin */
#penguinBot .bot-tip{
  position:absolute;bottom:115%;right:0;
  background:var(--deep);color:#fff;
  font-family:'Jost',sans-serif;font-size:.73rem;font-weight:600;
  padding:6px 12px;border-radius:10px;white-space:nowrap;
  opacity:0;pointer-events:none;transition:opacity .2s;
}
#penguinBot .bot-tip::after{content:'';position:absolute;top:100%;right:18px;border:6px solid transparent;border-top-color:var(--deep);}
#penguinBot:hover .bot-tip{opacity:1;}

/* notification badge */
#botBadge{
  position:absolute;top:-4px;right:-4px;
  width:18px;height:18px;background:#e05555;border-radius:50%;
  border:2px solid #fff;display:none;
  align-items:center;justify-content:center;
  font-size:.62rem;color:#fff;font-weight:800;
}

/* ═══════════════════════════════════════
   CHAT WINDOW (slides up from bot)
═══════════════════════════════════════ */
#chatWindow{
  position:fixed;bottom:110px;right:28px;
  width:340px;max-height:500px;
  z-index:9001;
  border-radius:18px;overflow:hidden;
  box-shadow:0 16px 48px rgba(13,92,130,.22),0 4px 16px rgba(13,92,130,.1);
  display:none;flex-direction:column;
  animation:chatPop .35s cubic-bezier(.34,1.4,.64,1) both;
}
@keyframes chatPop{from{opacity:0;transform:translateY(20px) scale(.95)}to{opacity:1;transform:translateY(0) scale(1)}}
#chatWindow.open{display:flex;}

.cw-header{
  background:linear-gradient(135deg,var(--deep),var(--mid));
  padding:12px 16px;display:flex;align-items:center;gap:10px;
  flex-shrink:0;
}
.cw-avatar{
  width:34px;height:34px;border-radius:50%;
  background:rgba(255,255,255,.2);
  display:flex;align-items:center;justify-content:center;flex-shrink:0;
}
.cw-avatar svg{width:28px;height:28px;}
.cw-title{font-family:'Cormorant Garamond',serif;font-size:1rem;font-weight:700;color:#fff;}
.cw-sub{font-size:.7rem;color:rgba(255,255,255,.65);}
.cw-close{margin-left:auto;cursor:pointer;color:rgba(255,255,255,.7);font-size:1.1rem;line-height:1;padding:2px 6px;border-radius:6px;transition:background .2s;}
.cw-close:hover{background:rgba(255,255,255,.18);color:#fff;}

.cw-log{
  flex:1;overflow-y:auto;padding:12px;
  display:flex;flex-direction:column;gap:9px;
  background:rgba(240,250,255,.82);
  scroll-behavior:smooth;min-height:180px;
}
.cw-log::-webkit-scrollbar{width:3px;}
.cw-log::-webkit-scrollbar-thumb{background:rgba(46,125,168,.22);border-radius:3px;}

.cw-empty{color:var(--muted);font-size:.78rem;text-align:center;margin:auto;padding:20px 0;font-style:italic;}

.cw-msg{max-width:85%;animation:msgIn .25s ease both;}
@keyframes msgIn{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
.cw-bubble{padding:7px 11px;border-radius:13px;font-size:.82rem;line-height:1.5;word-break:break-word;}
.cw-meta{font-size:.64rem;color:var(--muted);margin-top:2px;}
.cw-msg.mine{align-self:flex-end;text-align:right;}
.cw-msg.mine .cw-bubble{background:linear-gradient(135deg,var(--deep),var(--mid));color:#fff;border-bottom-right-radius:4px;}
.cw-msg.admin{align-self:flex-start;}
.cw-msg.admin .cw-bubble{background:rgba(255,255,255,.9);border:1px solid rgba(74,156,199,.25);color:var(--ink);border-bottom-left-radius:4px;}
.cw-msg.notify{align-self:flex-start;}
.cw-msg.notify .cw-bubble{background:rgba(214,239,248,.6);border:1px dashed rgba(74,156,199,.35);color:var(--deep);font-size:.78rem;font-style:italic;border-radius:10px;}

/* count bar */
.cw-count{background:rgba(255,255,255,.75);padding:5px 12px;font-size:.69rem;color:var(--muted);font-weight:600;text-align:right;border-top:1px solid rgba(200,230,245,.5);}
.cw-count .used{color:var(--mid);font-weight:700;}

/* limit notice */
.cw-limit-notice{background:rgba(214,239,248,.7);padding:10px 14px;font-size:.78rem;color:var(--deep);text-align:center;border-top:1px solid rgba(91,168,204,.25);}

.cw-input-row{display:flex;border-top:1px solid rgba(200,230,245,.6);background:rgba(255,255,255,.92);flex-shrink:0;}
.cw-input{flex:1;padding:11px 13px;border:none;font-family:'Jost',sans-serif;font-size:.85rem;color:var(--ink);background:transparent;outline:none;resize:none;height:42px;}
.cw-send{padding:0 16px;background:linear-gradient(135deg,var(--deep),var(--mid));border:none;color:#fff;font-family:'Jost',sans-serif;font-weight:700;font-size:.82rem;cursor:pointer;transition:opacity .2s;white-space:nowrap;}
.cw-send:hover{opacity:.88;}
.cw-send:disabled{opacity:.45;cursor:not-allowed;}

@media(max-width:620px){
  .hero{padding:28px 20px;}
  .grid{grid-template-columns:1fr;}.grid-full{grid-column:1;}
  .form-card{padding:22px 18px;}.form-row{grid-template-columns:1fr;}
  #chatWindow{width:calc(100vw - 32px);right:16px;bottom:96px;}
  #penguinBot{bottom:18px;right:18px;}
}
</style>
</head>
<body>

<!-- TOP BAR -->
<div class="topbar">
  <div>
    <div class="tb-name">Anagha M Iyengar</div>
    <div class="tb-user">Signed in as <strong><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong></div>
  </div>
  <div class="tb-actions">
    <a href="logout.php" class="tb-btn logout">Sign Out</a>
  </div>
</div>

<div class="page">

  <!-- HERO -->
  <header class="hero">
    <div class="hero-content">
      <h1 class="hero-name">Anagha M Iyengar</h1>
      <p class="hero-tagline">Cybersecurity &amp; Digital Forensics &nbsp;·&nbsp; BSc Forensic Science</p>
      <div class="hero-contacts">
        <a class="contact-pill" href="tel:+918792885310">
          <svg viewBox="0 0 24 24"><path d="M6.6 10.8c1.4 2.8 3.8 5.1 6.6 6.6l2.2-2.2c.3-.3.7-.4 1-.2 1.1.4 2.3.6 3.6.6.6 0 1 .4 1 1V20c0 .6-.4 1-1 1C10.6 21 3 13.4 3 4c0-.6.4-1 1-1h3.5c.6 0 1 .4 1 1 0 1.3.2 2.5.6 3.6.1.3 0 .7-.2 1L6.6 10.8z"/></svg>
          +91 87928 85310
        </a>
        <a class="contact-pill" href="mailto:miyengaranagha@gmail.com">
          <svg viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-2 .9-2 2v12c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
          miyengaranagha@gmail.com
        </a>
        <a class="contact-pill" href="https://linkedin.com/in/anagha-m-iyengar-769a0526b" target="_blank">
          <svg viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zM9 17H6.5v-7H9v7zm-1.25-8a1.25 1.25 0 110-2.5 1.25 1.25 0 010 2.5zM18 17h-2.5v-3.5c0-.83-.67-1.5-1.5-1.5s-1.5.67-1.5 1.5V17H10v-7h2.5v1.1c.46-.69 1.23-1.1 2-1.1 1.65 0 3 1.35 3 3V17z"/></svg>
          LinkedIn
        </a>
        <span class="contact-pill">
          <svg viewBox="0 0 24 24"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5S10.62 6.5 12 6.5s2.5 1.12 2.5 2.5S13.38 11.5 12 11.5z"/></svg>
          Bengaluru, Karnataka
        </span>
      </div>
    </div>
  </header>

  <!-- GRID -->
  <div class="grid">

    <div class="card">
      <h2 class="card-title"><span>🎓</span> Education</h2>
      <p class="edu-name">Bachelor of Science in Forensic Science</p>
      <p class="edu-sub">Jain (Deemed-to-be) University, School of Sciences<br>Bengaluru, Karnataka &nbsp;·&nbsp; Expected May 2026</p>
      <span class="badge">CGPA: 9.1 / 10</span>
    </div>

    <div class="card">
      <h2 class="card-title"><span>⚙️</span> Technical Skills</h2>
      <div class="skills-group"><p class="skills-label">Security Tools</p><div class="tag-row"><span class="tag">Wireshark</span><span class="tag">Kali Linux</span><span class="tag">Burp Suite</span></div></div>
      <div class="skills-group"><p class="skills-label">Languages</p><div class="tag-row"><span class="tag">HTML</span><span class="tag">CSS</span><span class="tag">JavaScript</span><span class="tag">SQL</span></div></div>
      <div class="skills-group"><p class="skills-label">Domains</p><div class="tag-row"><span class="tag">Web App Security</span><span class="tag">Digital Forensics</span><span class="tag">Network Forensics</span><span class="tag">Ethical Hacking</span><span class="tag">DFIR</span></div></div>
    </div>

    <div class="card grid-full">
      <h2 class="card-title"><span>🛠️</span> Projects</h2>
      <div class="project-title">Secure CV Portfolio Web Application</div>
      <p class="project-tech">HTML · CSS · JavaScript · PHP</p>
      <ul class="exp-bullets">
        <li>Multi-page secure web application with SHA-256 password hashing, session management, and IP-based login lockout after 3 failed attempts.</li>
        <li>Math captcha, input sanitisation, and XSS mitigation controls across all forms.</li>
        <li>Private per-user live chat with 10-message rate limit and automatic admin notification.</li>
      </ul>
    </div>

    <div class="card grid-full">
      <h2 class="card-title"><span>💼</span> Internship Experience</h2>
      <div class="exp-item">
        <p class="exp-role">Cybersecurity &amp; Digital Forensics Intern</p>
        <div class="exp-meta"><span>Talfor · Bengaluru, Karnataka</span><span>Mar 2025 – Present</span></div>
        <ul class="exp-bullets">
          <li>Executing structured digital forensics investigations and producing detailed forensic reports.</li>
          <li>Conducting web penetration testing and analysing real-world network forensics scenarios.</li>
          <li>Identifying and documenting cybersecurity threats as part of incident response workflow.</li>
        </ul>
      </div>
      <hr class="exp-divider">
      <div class="exp-item">
        <p class="exp-role">Cybersecurity Intern</p>
        <div class="exp-meta"><span>Reinfosec · Bengaluru, Karnataka</span><span>2024 · 1 Month</span></div>
        <ul class="exp-bullets">
          <li>Hands-on exposure to cybersecurity fundamentals and ethical hacking through simulated scenarios.</li>
          <li>Practical vulnerability identification and remediation skills development.</li>
        </ul>
      </div>
      <hr class="exp-divider">
      <div class="exp-item">
        <p class="exp-role">Cybersecurity Intern</p>
        <div class="exp-meta"><span>Intrainz · Remote</span><span>Apr – Jun 2024</span></div>
        <ul class="exp-bullets">
          <li>Two-month structured internship covering core security concepts; certified on completion.</li>
        </ul>
      </div>
    </div>

    <div class="card">
      <h2 class="card-title"><span>📜</span> Certifications</h2>
      <div class="cert-item"><div class="cert-dot"></div><div><p class="cert-name">Excel Basics for Data Analysis</p><p class="cert-issuer">IBM / Coursera</p></div></div>
      <div class="cert-item"><div class="cert-dot"></div><div><p class="cert-name">Copilot in Excel: Building Dynamic Dashboards</p><p class="cert-issuer">Ann K. Emery</p></div></div>
      <div class="cert-item"><div class="cert-dot"></div><div><p class="cert-name">DFIR Workshop</p><p class="cert-issuer">Cisco Networking Academy</p></div></div>
    </div>

    <div class="card">
      <h2 class="card-title"><span>🏆</span> Achievements</h2>
      <div class="ach-item"><strong>Hackathon Participant</strong><span>Jain School of Sciences × ISACA Bangalore</span></div>
      <div class="vol-item"><p class="vol-title">Core Member — Utkriti Student Council</p><p class="vol-org">Jain University</p><p class="vol-desc">Organised cultural fests and events; demonstrated leadership and coordination.</p></div>
      <div class="vol-item"><p class="vol-title">Volunteer — INFUSE-2025 &amp; INAAC 4.0</p><p class="vol-desc">Supported international academic conferences; networked with research professionals.</p></div>
    </div>

  </div><!-- /grid -->

  <!-- CONTACT FORM -->
  <div class="form-card">
    <h2 class="form-title">Get In Touch</h2>
    <p class="form-subtitle">Have an opportunity or want to connect? Drop a message below.</p>
    <div id="formWrap">
      <div class="form-row">
        <div class="form-group"><label>Your Name</label><input type="text" id="f-name" placeholder="Full name" autocomplete="off"><p class="err-p" id="err-name"></p></div>
        <div class="form-group"><label>Email Address</label><input type="text" id="f-email" placeholder="someone@gmail.com" autocomplete="off"><p class="err-p" id="err-email"></p></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Mobile Number</label><input type="text" id="f-mobile" placeholder="Digits only" autocomplete="off"><p class="err-p" id="err-mobile"></p></div>
        <div class="form-group"><label>Company Name</label><input type="text" id="f-company" placeholder="Your company" autocomplete="off"><p class="err-p" id="err-company"></p></div>
      </div>
      <div class="form-group" style="margin-bottom:6px;"><label>Your Message</label><textarea id="f-msg" placeholder="Alphabets and numbers only"></textarea><p class="err-p" id="err-msg"></p></div>
      <button class="submit-btn" onclick="submitForm()">
        Send Message
        <svg viewBox="0 0 24 24"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
      </button>
    </div>
    <div id="thankYou">
      <div class="penguin-form-wrap">
        <svg viewBox="0 0 120 140" xmlns="http://www.w3.org/2000/svg">
          <ellipse cx="60" cy="88" rx="36" ry="42" fill="#1a1a2e"/>
          <ellipse cx="60" cy="95" rx="22" ry="30" fill="#e8f6fc"/>
          <ellipse cx="60" cy="46" rx="28" ry="28" fill="#1a1a2e"/>
          <ellipse cx="60" cy="50" rx="18" ry="18" fill="#e8f6fc"/>
          <circle cx="53" cy="44" r="5" fill="white"/><circle cx="67" cy="44" r="5" fill="white"/>
          <circle cx="54" cy="44" r="3" fill="#1a1a2e"/><circle cx="68" cy="44" r="3" fill="#1a1a2e"/>
          <circle cx="55" cy="43" r="1" fill="white"/><circle cx="69" cy="43" r="1" fill="white"/>
          <ellipse cx="60" cy="56" rx="7" ry="4" fill="#e67e22"/>
          <ellipse cx="47" cy="53" rx="5" ry="3" fill="#f9a8b8" opacity="0.6"/>
          <ellipse cx="73" cy="53" rx="5" ry="3" fill="#f9a8b8" opacity="0.6"/>
          <ellipse cx="26" cy="90" rx="11" ry="26" fill="#1a1a2e" transform="rotate(-15 26 90)"/>
          <ellipse cx="94" cy="90" rx="11" ry="26" fill="#1a1a2e" transform="rotate(15 94 90)"/>
          <ellipse cx="47" cy="130" rx="12" ry="6" fill="#e67e22"/>
          <ellipse cx="73" cy="130" rx="12" ry="6" fill="#e67e22"/>
          <polygon points="60,5 45,35 75,35" fill="#0d7ab5"/><circle cx="60" cy="5" r="4" fill="#ffd700"/>
          <line x1="45" y1="35" x2="75" y2="35" stroke="#ffd700" stroke-width="2"/>
        </svg>
      </div>
      <h3 class="ty-title">Thank You! 🌊</h3>
      <p class="ty-msg">Your message has been received. Anagha will get back to you soon.</p>
    </div>
  </div>

</div><!-- /page -->

<!-- ═══════════════════════════════════════
     FLOATING PENGUIN BOT
═══════════════════════════════════════ -->
<div id="penguinBot" onclick="toggleChat()" title="Chat with Anagha">
  <span class="bot-tip">💬 Message Anagha</span>
  <div id="botBadge">!</div>
  <!-- Penguin SVG -->
  <svg width="72" height="84" viewBox="0 0 120 140" xmlns="http://www.w3.org/2000/svg">
    <ellipse cx="60" cy="88" rx="36" ry="42" fill="#1a1a2e"/>
    <ellipse cx="60" cy="95" rx="22" ry="30" fill="#e8f6fc"/>
    <ellipse cx="60" cy="46" rx="28" ry="28" fill="#1a1a2e"/>
    <ellipse cx="60" cy="50" rx="18" ry="18" fill="#e8f6fc"/>
    <circle cx="53" cy="44" r="5" fill="white"/><circle cx="67" cy="44" r="5" fill="white"/>
    <circle cx="54" cy="44" r="3" fill="#1a1a2e"/><circle cx="68" cy="44" r="3" fill="#1a1a2e"/>
    <circle cx="55" cy="43" r="1" fill="white"/><circle cx="69" cy="43" r="1" fill="white"/>
    <ellipse cx="60" cy="56" rx="7" ry="4" fill="#e67e22"/>
    <ellipse cx="47" cy="53" rx="5" ry="3" fill="#f9a8b8" opacity="0.6"/>
    <ellipse cx="73" cy="53" rx="5" ry="3" fill="#f9a8b8" opacity="0.6"/>
    <ellipse cx="26" cy="90" rx="11" ry="26" fill="#1a1a2e" transform="rotate(-15 26 90)"/>
    <ellipse cx="94" cy="90" rx="11" ry="26" fill="#1a1a2e" transform="rotate(15 94 90)"/>
    <!-- waving flipper -->
    <ellipse cx="108" cy="76" rx="7" ry="4" fill="#e67e22" transform="rotate(-25 108 76)"/>
    <ellipse cx="47" cy="130" rx="12" ry="6" fill="#e67e22"/>
    <ellipse cx="73" cy="130" rx="12" ry="6" fill="#e67e22"/>
    <!-- small hat -->
    <rect x="44" y="20" width="32" height="5" rx="2.5" fill="#0d5c82"/>
    <rect x="50" y="8" width="20" height="14" rx="4" fill="#0d5c82"/>
    <circle cx="60" cy="8" r="3" fill="#6ab4d8"/>
  </svg>
</div>

<!-- ═══════════════════════════════════════
     CHAT WINDOW
═══════════════════════════════════════ -->
<div id="chatWindow">
  <div class="cw-header">
    <div class="cw-avatar">
      <svg viewBox="0 0 120 140" xmlns="http://www.w3.org/2000/svg">
        <ellipse cx="60" cy="88" rx="36" ry="42" fill="#1a1a2e"/>
        <ellipse cx="60" cy="95" rx="22" ry="30" fill="#e8f6fc"/>
        <ellipse cx="60" cy="46" rx="28" ry="28" fill="#1a1a2e"/>
        <ellipse cx="60" cy="50" rx="18" ry="18" fill="#e8f6fc"/>
        <circle cx="53" cy="44" r="5" fill="white"/><circle cx="67" cy="44" r="5" fill="white"/>
        <circle cx="54" cy="44" r="3" fill="#1a1a2e"/><circle cx="68" cy="44" r="3" fill="#1a1a2e"/>
        <ellipse cx="60" cy="56" rx="7" ry="4" fill="#e67e22"/>
      </svg>
    </div>
    <div>
      <div class="cw-title">Anagha's Chat</div>
      <div class="cw-sub">Private · only you &amp; Anagha can see</div>
    </div>
    <span class="cw-close" onclick="toggleChat()">✕</span>
  </div>

  <div class="cw-log" id="cwLog">
    <div class="cw-empty" id="cwEmpty">No messages yet — say hello! 👋</div>
  </div>

  <div class="cw-count">Messages used: <span class="used" id="cwUsed">0</span> / 10</div>

  <div class="cw-input-row" id="cwInputRow">
    <textarea class="cw-input" id="cwMsg"
              placeholder="Type a message…" maxlength="300"
              oninput="this.value=this.value.replace(/[^A-Za-z0-9 ]/g,'')"
              onkeypress="return /[A-Za-z0-9 ]/.test(String.fromCharCode(event.which||event.keyCode))"
              onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendChat();}"></textarea>
    <button class="cw-send" id="cwSendBtn" onclick="sendChat()">Send</button>
  </div>
</div>

<script>
// ═══════════ CONTACT FORM ═══════════
function blockInput(id, re) {
  const el = document.getElementById(id);
  el.addEventListener('input', function() {
    let out = '';
    for (const c of this.value) if (re.test(c)) out += c;
    this.value = out;
  });
}
blockInput('f-name',    /[A-Za-z ]/);
blockInput('f-mobile',  /[0-9]/);
blockInput('f-msg',     /[A-Za-z0-9 ]/);
blockInput('f-company', /[A-Za-z0-9 ]/);

// Email: allow letters, digits, @, ., hyphens, underscores ONLY
// Blocks: < > { } ( ) $ ? / \ and all other symbols
document.getElementById('f-email').addEventListener('input', function(){
  this.value = this.value.replace(/[^A-Za-z0-9@.\-_]/g, '');
});;

function setErr(id, msg) {
  const el = document.getElementById(id);
  el.textContent = msg;
  const inp = document.getElementById(id.replace('err-', 'f-'));
  msg ? inp.classList.add('invalid') : inp.classList.remove('invalid');
}
function clearErrs() { ['name','email','mobile','company','msg'].forEach(k => setErr('err-'+k, '')); }

function submitForm() {
  clearErrs();
  const name    = document.getElementById('f-name').value.trim();
  const email   = document.getElementById('f-email').value.trim();
  const mobile  = document.getElementById('f-mobile').value.trim();
  const company = document.getElementById('f-company').value.trim();
  const msg     = document.getElementById('f-msg').value.trim();
  let v = true;
  if (!name)    { setErr('err-name',    'Name is required'); v = false; }
  else if (!/^[A-Za-z ]+$/.test(name)) { setErr('err-name', 'Letters only'); v = false; }
  if (!email)   { setErr('err-email',   'Email is required'); v = false; }
  else if (!/^[^\s@]+@gmail\.com$/i.test(email)) { setErr('err-email', 'Must be @gmail.com'); v = false; }
  if (!mobile)  { setErr('err-mobile',  'Mobile required'); v = false; }
  else if (!/^\d{7,15}$/.test(mobile))  { setErr('err-mobile',  'Digits only (7–15)'); v = false; }
  if (!company) { setErr('err-company', 'Company required'); v = false; }
  if (!msg)     { setErr('err-msg',     'Message required'); v = false; }
  if (!v) return;
  const fd = new FormData();
  fd.append('name',name); fd.append('email',email); fd.append('mobile',mobile);
  fd.append('company',company); fd.append('message',msg);
  fetch('save_contact.php',{method:'POST',body:fd})
    .then(() => { document.getElementById('formWrap').style.display='none'; document.getElementById('thankYou').style.display='block'; })
    .catch(() => { document.getElementById('formWrap').style.display='none'; document.getElementById('thankYou').style.display='block'; });
}

// ═══════════ PENGUIN BOT / CHAT ═══════════
let chatOpen  = false;
let lastTs    = '';
let msgCount  = 0;
const LIMIT   = 10;

function toggleChat() {
  chatOpen = !chatOpen;
  const win = document.getElementById('chatWindow');
  if (chatOpen) {
    win.classList.add('open');
    document.getElementById('botBadge').style.display = 'none';
    fetchMessages();
    setTimeout(() => { const log=document.getElementById('cwLog'); log.scrollTop=log.scrollHeight; }, 200);
  } else {
    win.classList.remove('open');
  }
}

function escHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;'); }
function fmtTime(ts) { try { return new Date(ts.replace(' ','T')).toLocaleTimeString([],{hour:'2-digit',minute:'2-digit'}); } catch(e){ return ts; } }

function renderMsg(m) {
  const div = document.createElement('div');
  const isAdmin  = m.type && (m.type.startsWith('admin_reply') || m.type.startsWith('admin_notify'));
  const isNotify = m.type && m.type.startsWith('admin_notify');
  div.className  = 'cw-msg ' + (m.is_mine ? 'mine' : (isNotify ? 'notify' : 'admin'));
  const who = m.is_mine ? 'You' : '🐧 Anagha';
  div.innerHTML = `<div class="cw-bubble">${escHtml(m.msg)}</div><div class="cw-meta">${who} · ${fmtTime(m.ts)}</div>`;
  return div;
}

async function fetchMessages() {
  try {
    const res  = await fetch('chat_fetch.php?after=' + encodeURIComponent(lastTs));
    const data = await res.json();
    const msgs = data.messages || [];
    msgCount   = data.count    || 0;
    document.getElementById('cwUsed').textContent = Math.min(msgCount, LIMIT);

    if (msgs.length > 0) {
      const log   = document.getElementById('cwLog');
      const empty = document.getElementById('cwEmpty');
      if (empty) empty.remove();
      msgs.forEach(m => { log.appendChild(renderMsg(m)); lastTs = m.ts; });
      log.scrollTop = log.scrollHeight;

      // Show badge if chat is closed
      if (!chatOpen) {
        const badge = document.getElementById('botBadge');
        badge.style.display = 'flex';
      }
    }

    if (msgCount >= LIMIT) disableChat();
  } catch(e) {}
}

function disableChat() {
  const inp = document.getElementById('cwMsg');
  const btn = document.getElementById('cwSendBtn');
  const row = document.getElementById('cwInputRow');
  if (inp) inp.disabled = true;
  if (btn) btn.disabled = true;
  if (row && !document.getElementById('cwLimitNotice')) {
    const n = document.createElement('div');
    n.id = 'cwLimitNotice'; n.className = 'cw-limit-notice';
    n.textContent = '✅ Chat limit reached. Anagha has been notified and will contact you soon. 💙';
    row.parentNode.insertBefore(n, row);
  }
}

async function sendChat() {
  const msgEl = document.getElementById('cwMsg');
  const msg   = msgEl.value.trim();
  if (!msg) return;

  try {
    const fd = new FormData(); fd.append('message', msg);
    const res  = await fetch('chat_send.php', {method:'POST', body:fd});
    const data = await res.json();
    msgEl.value = '';

    if (data.status === 'ok') {
      msgCount = data.count || msgCount;
      document.getElementById('cwUsed').textContent = Math.min(msgCount, LIMIT);
      if (data.remaining === 0) disableChat();
      await fetchMessages();
    } else if (data.status === 'limit') {
      disableChat();
    }
  } catch(e) {}
}

// Poll every 8 seconds for new admin replies
fetchMessages();
setInterval(fetchMessages, 8000);

// ═══════════ SESSION KEEP-ALIVE & 30-SECOND AUTO-LOGOUT ═══════════
// While this tab is VISIBLE: ping session_ping.php every 20s to stay alive.
// When tab is HIDDEN (switched/minimised): start a 30-second countdown.
// If still hidden after 30 seconds: auto sign out.
let pingInterval = null;
let awayTimer    = null;

async function pingSession(){
  try{
    const res=await fetch('session_ping.php');
    const data=await res.json();
    if(data.status==='expired') window.location.href='login.php?timeout=1';
  }catch(e){}
}
function startPinging(){
  if(pingInterval) clearInterval(pingInterval);
  pingInterval=setInterval(pingSession,20000);
  pingSession();
}
function stopPinging(){
  if(pingInterval){ clearInterval(pingInterval); pingInterval=null; }
}
function startAwayTimer(){
  if(awayTimer) clearTimeout(awayTimer);
  awayTimer=setTimeout(()=>{ window.location.href='logout.php'; },30000);
}
function stopAwayTimer(){
  if(awayTimer){ clearTimeout(awayTimer); awayTimer=null; }
}
document.addEventListener('visibilitychange',()=>{
  if(document.hidden){ stopPinging(); startAwayTimer(); }
  else { stopAwayTimer(); startPinging(); }
});
startPinging();
</script>
</body>
</html>

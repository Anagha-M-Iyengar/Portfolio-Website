<?php
session_start();
require_once 'gate_check.php';
require_once 'db_connect.php';
require_once 'lockout.php';

if (isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true) {
    header("Location: cv.php"); exit;
}

$ip      = $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
$error   = '';
$lockSec = 0;
$locked  = lockout_is_locked($ip, $lockSec);

if (!isset($_SESSION['ca'])) { $_SESSION['ca'] = rand(2,9); $_SESSION['cb'] = rand(1,9); }

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$locked) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password']       ?? '';
    $captcha  = trim($_POST['captcha']   ?? '');
    $expected = $_SESSION['ca'] + $_SESSION['cb'];

    $_SESSION['ca'] = rand(2,9); $_SESSION['cb'] = rand(1,9);

    if ((int)$captcha !== $expected) {
        $error = 'captcha';
    } elseif ($username === '' || $password === '') {
        $error = 'empty';
    } else {
        $hashed = hash('sha256', $password);

        // ── Look up username in MySQL ──────────────────────────────────────
        $stmt = $conn->prepare(
            "SELECT userid, username, password_hash FROM users WHERE username = ?"
        );
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();
        $user   = $result->fetch_assoc();
        $stmt->close();

        if (!$user) {
            // Username not found in database
            $error = 'no_user';
        } elseif ($user['password_hash'] === $hashed) {
            // ✅ Correct password
            lockout_reset($ip);
            $_SESSION['logged_in'] = true;
            $_SESSION['username']  = $user['username'];
            $_SESSION['userid']    = $user['userid'];
            $conn->close();
            header("Location: cv.php"); exit;
        } else {
            // ❌ Wrong password
            $attempts = lockout_increment($ip);
            $left     = MAX_ATTEMPTS - $attempts;
            if ($left <= 0) {
                lockout_lock($ip);
                lockout_is_locked($ip, $lockSec);
                $locked = true; $error = 'locked';
            } else {
                $error = 'wrong_' . $left;
            }
        }
    }
    if (!$locked) $locked = lockout_is_locked($ip, $lockSec);
}
$conn->close();

$ca = $_SESSION['ca']; $cb = $_SESSION['cb'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Sign In – Anagha's Portfolio</title>
<?php if ($locked && $lockSec > 0): ?><meta http-equiv="refresh" content="<?= $lockSec + 1 ?>"><?php endif; ?>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--b1:#4a9cc7;--b2:#6ab4d8;--b3:#8ecae6;--b4:#b8dff0;--ink:#0d2b3e;--muted:#4a7a94;--deep:#1a4a6b;--err:#c0392b;--warn:#c67c00;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Jost',sans-serif;min-height:100vh;background:linear-gradient(160deg,#c5e8f5 0%,#d6eff8 30%,#e8f6fc 60%,#cce9f5 100%);display:flex;align-items:center;justify-content:center;padding:30px 20px;position:relative;overflow:hidden;}
body::before{content:'';position:fixed;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(142,202,230,.22) 0%,transparent 70%);top:-100px;left:-100px;pointer-events:none;}
body::after{content:'';position:fixed;width:340px;height:340px;border-radius:50%;background:radial-gradient(circle,rgba(106,180,216,.18) 0%,transparent 70%);bottom:-70px;right:-70px;pointer-events:none;}
.card{background:rgba(255,255,255,.88);border:1.5px solid rgba(142,202,230,.5);border-radius:22px;padding:44px 40px 40px;width:100%;max-width:420px;box-shadow:0 14px 44px rgba(13,92,130,.12);position:relative;z-index:10;animation:rise .7s cubic-bezier(.34,1.4,.64,1) both;}
@keyframes rise{from{opacity:0;transform:translateY(36px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}
.card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(to right,var(--b1),var(--b3),var(--b4));border-radius:22px 22px 0 0;}
.logo{width:60px;height:60px;border-radius:50%;background:linear-gradient(135deg,var(--b1),var(--b3));display:flex;align-items:center;justify-content:center;font-family:'Cormorant Garamond',serif;font-size:1.35rem;font-weight:700;color:#fff;margin:0 auto 18px;box-shadow:0 6px 18px rgba(74,156,199,.28);}
h1{font-family:'Cormorant Garamond',serif;font-size:1.7rem;font-weight:700;color:var(--ink);text-align:center;margin-bottom:4px;}
.sub{text-align:center;color:var(--muted);font-size:.82rem;margin-bottom:24px;}
.alert{border-radius:10px;padding:10px 14px;font-size:.83rem;margin-bottom:14px;text-align:center;font-weight:500;animation:shk .35s ease;}
@keyframes shk{0%,100%{transform:translateX(0)}25%{transform:translateX(-5px)}75%{transform:translateX(5px)}}
.ae{background:#fdf0f0;border:1px solid rgba(192,57,43,.22);color:var(--err);}
.aw{background:#fffbf0;border:1px solid rgba(198,124,0,.22);color:var(--warn);}
.ai{background:#f0f7fb;border:1px solid rgba(74,156,199,.28);color:var(--deep);}
.no-user-box{background:#f0f7fb;border:1px solid rgba(74,156,199,.3);border-radius:11px;padding:14px 16px;margin-bottom:16px;text-align:center;}
.no-user-box p{font-size:.83rem;color:var(--deep);margin-bottom:10px;}
.btn-signup-prompt{display:inline-block;background:linear-gradient(135deg,var(--b1),var(--b2));color:#fff;border:none;padding:9px 22px;border-radius:20px;font-family:'Jost',sans-serif;font-size:.82rem;font-weight:600;cursor:pointer;text-decoration:none;transition:transform .2s;}
.btn-signup-prompt:hover{transform:translateY(-2px);}
.lock-box{text-align:center;padding:10px 0;}
.lk-icon{font-size:2.8rem;display:block;margin-bottom:8px;animation:wig 1.2s ease infinite;}
@keyframes wig{0%,100%{transform:rotate(0)}25%{transform:rotate(-9deg)}75%{transform:rotate(9deg)}}
.lock-box h3{font-size:1.05rem;color:var(--ink);margin-bottom:5px;font-family:'Cormorant Garamond',serif;font-weight:700;}
.lock-box p{color:var(--muted);font-size:.82rem;}
#cd{font-size:2.4rem;font-weight:700;color:var(--b1);display:block;margin:5px 0;font-family:'Cormorant Garamond',serif;}
.field{margin-bottom:14px;position:relative;}
.field label{display:block;font-size:.7rem;font-weight:600;color:var(--muted);margin-bottom:5px;letter-spacing:.07em;text-transform:uppercase;}
.ico{position:absolute;left:12px;top:34px;font-size:.88rem;pointer-events:none;}
.field input{width:100%;padding:11px 38px 11px 36px;border:1.5px solid rgba(142,202,230,.65);border-radius:11px;font-family:'Jost',sans-serif;font-size:.92rem;color:var(--ink);background:#fff;outline:none;transition:border-color .2s,box-shadow .2s;}
.field input:focus{border-color:var(--b1);box-shadow:0 0 0 3px rgba(74,156,199,.15);}
.eye{position:absolute;right:12px;top:34px;cursor:pointer;font-size:.88rem;color:var(--muted);user-select:none;}
.captcha-box{background:rgba(214,239,248,.35);border:1px solid rgba(74,156,199,.25);border-radius:11px;padding:12px 16px;margin-bottom:14px;}
.captcha-q{font-size:.88rem;font-weight:600;color:var(--deep);margin-bottom:8px;}
.captcha-box input{width:80px;padding:8px 10px;border:1.5px solid rgba(142,202,230,.65);border-radius:9px;font-family:'Jost',sans-serif;font-size:.92rem;color:var(--ink);background:#fff;outline:none;text-align:center;transition:border-color .2s;}
.captcha-box input:focus{border-color:var(--b1);}
.captcha-hint{font-size:.7rem;color:var(--muted);margin-top:5px;}
.btn-main{display:block;width:100%;padding:13px;border:none;border-radius:11px;font-family:'Jost',sans-serif;font-size:.95rem;font-weight:600;cursor:pointer;background:linear-gradient(135deg,var(--b1),var(--b2));color:#fff;box-shadow:0 6px 18px rgba(74,156,199,.3);transition:transform .2s,box-shadow .2s;margin-top:4px;letter-spacing:.03em;text-align:center;}
.btn-main:hover{transform:translateY(-2px);box-shadow:0 10px 26px rgba(74,156,199,.42);}
.divider{display:flex;align-items:center;gap:10px;margin:14px 0;color:var(--muted);font-size:.75rem;}
.divider::before,.divider::after{content:'';flex:1;height:1px;background:rgba(142,202,230,.5);}
.btn-out{display:block;width:100%;padding:12px;border:1.5px solid rgba(142,202,230,.65);border-radius:11px;font-family:'Jost',sans-serif;font-size:.88rem;font-weight:600;cursor:pointer;color:var(--b1);background:rgba(255,255,255,.7);transition:background .2s,transform .2s;text-align:center;}
.btn-out:hover{background:rgba(214,239,248,.5);transform:translateY(-1px);}
.back-link{text-align:center;margin-top:14px;font-size:.78rem;color:var(--muted);}
.back-link a{color:var(--b1);font-weight:600;text-decoration:none;}
.back-link a:hover{color:var(--deep);}
</style>
</head>
<body>
<div class="card">
  <div class="logo">AI</div>
  <h1>Sign In</h1>
  <p class="sub">Access Anagha's portfolio</p>

  <?php if ($locked): ?>
    <div class="alert ai">🔒 Account temporarily locked.</div>
    <div class="lock-box">
      <span class="lk-icon">🔒</span><h3>Please wait</h3><p>Try again in</p>
      <span id="cd"><?= $lockSec ?></span><p>seconds</p>
    </div>
    <script>let s=<?= $lockSec ?>;const el=document.getElementById('cd');const t=setInterval(()=>{s--;el.textContent=s;if(s<=0){clearInterval(t);location.href='login.php';}},1000);</script>

  <?php elseif ($error === 'no_user'): ?>
    <div class="no-user-box">
      <p>🔍 No account found for that username.</p>
      <a href="signup.php" class="btn-signup-prompt">Create an Account →</a>
    </div>
  <?php elseif ($error === 'captcha'): ?>
    <div class="alert aw">🤖 Captcha incorrect — please try again.</div>
  <?php elseif ($error === 'empty'): ?>
    <div class="alert ae">⚠️ Please fill in both fields.</div>
  <?php elseif (str_starts_with($error, 'wrong_')): ?>
    <?php $left = (int)str_replace('wrong_', '', $error); ?>
    <div class="alert <?= $left===1?'ae':'aw' ?>">
      ❌ Incorrect password — <strong><?= $left ?> attempt<?= $left===1?'':'s' ?></strong> remaining.
    </div>
  <?php endif; ?>

  <?php if (!$locked): ?>
  <form method="POST" action="login.php" autocomplete="off">
    <div class="field">
      <label>Username</label>
      <span class="ico">👤</span>
      <input type="text" name="username" placeholder="Your username"
             value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
             oninput="this.value=this.value.replace(/[^A-Za-z]/g,'')" required>
    </div>
    <div class="field">
      <label>Password</label>
      <span class="ico">🔑</span>
      <input type="password" name="password" id="pwd" placeholder="Your password"
             oninput="this.value=this.value.replace(/[^A-Za-z0-9@#_]/g,'')"
             onkeypress="return /[A-Za-z0-9@#_]/.test(String.fromCharCode(event.which||event.keyCode))" required>
      <span class="eye" id="eyeBtn" onclick="toggleEye()">👁️</span>
    </div>
    <div class="captcha-box">
      <p class="captcha-q">🤖 Prove you're human: What is <?= $ca ?> + <?= $cb ?> ?</p>
      <input type="number" name="captcha" placeholder="?" min="0" max="99" required>
      <p class="captcha-hint">Solve the sum to continue.</p>
    </div>
    <button type="submit" class="btn-main">Sign In →</button>
  </form>
  <div class="divider">or</div>
  <button class="btn-out" onclick="location.href='signup.php'">Create an Account</button>
  <?php else: ?>
  <div class="divider" style="margin-top:16px;">or</div>
  <button class="btn-out" onclick="location.href='signup.php'">Create an Account</button>
  <?php endif; ?>
  <div class="back-link"><a href="intro.php">← Back to Introduction</a></div>
</div>
<script>function toggleEye(){const p=document.getElementById('pwd');const e=document.getElementById('eyeBtn');if(!p)return;p.type=p.type==='password'?'text':'password';e.textContent=p.type==='password'?'👁️':'🙈';}</script>
</body>
</html>

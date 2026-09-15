<?php
session_start();
require_once 'gate_check.php';
require_once 'db_connect.php';

$errors = []; $success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $age      = trim($_POST['age']      ?? '');
    $username = trim($_POST['username'] ?? '');
    $mobile   = trim($_POST['mobile']   ?? '');
    $cc       = trim($_POST['cc']       ?? '+91');
    $pwd      = $_POST['pwd']           ?? '';
    $cpwd     = $_POST['cpwd']          ?? '';

    // ── Validation ──────────────────────────────────────────────────────────
    if ($name === '' || !preg_match('/^[A-Za-z ]+$/', $name))
        $errors['name'] = 'Full name required (letters only).';

    $atPos = strpos($email, '@gmail'); $comPos = strpos($email, '.com');
    if ($email === '')
        $errors['email'] = 'Email is required.';
    elseif ($atPos === false || $comPos === false || $comPos < $atPos)
        $errors['email'] = 'Must be you@gmail.com (@ before .com).';

    if ($age === '' || !preg_match('/^[0-9]{1,2}$/', $age))
        $errors['age'] = 'Valid age required (1–2 digits).';

    if ($username === '' || !preg_match('/^[A-Za-z]+$/', $username))
        $errors['username'] = 'Username: letters only, no spaces.';

    if ($mobile === '' || !preg_match('/^[0-9]{10}$/', $mobile))
        $errors['mobile'] = 'Exactly 10 digits required.';

    if ($pwd === '')
        $errors['pwd'] = 'Password required.';
    elseif (strlen($pwd) < 7)
        $errors['pwd'] = 'Minimum 7 characters.';
    elseif (!preg_match('/[A-Z]/', $pwd))
        $errors['pwd'] = 'Include at least one uppercase letter.';
    elseif (!preg_match('/[a-z]/', $pwd))
        $errors['pwd'] = 'Include at least one lowercase letter.';
    elseif (!preg_match('/[0-9@#_]/', $pwd))
        $errors['pwd'] = 'Include at least one number or @, #, _.';

    if ($cpwd === '')
        $errors['cpwd'] = 'Please confirm your password.';
    elseif (strlen($cpwd) <= 6)
        $errors['cpwd'] = 'Must be more than 6 characters.';
    elseif ($cpwd !== $pwd)
        $errors['cpwd'] = 'Passwords do not match.';

    if (empty($errors)) {
        $fullMobile = $cc . $mobile;
        $hashedPwd  = hash('sha256', $pwd);

        // ── Check for duplicate username ───────────────────────────────────
        $stmt = $conn->prepare("SELECT userid FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $errors['username'] = 'Username already taken. Please choose another.';
            $stmt->close();
        } else {
            $stmt->close();

            // ── Check for duplicate email ──────────────────────────────────
            $stmt2 = $conn->prepare("SELECT userid FROM users WHERE email = ?");
            $stmt2->bind_param("s", $email);
            $stmt2->execute();
            $stmt2->store_result();

            if ($stmt2->num_rows > 0) {
                $errors['email'] = 'This email is already registered.';
                $stmt2->close();
            } else {
                $stmt2->close();

                // ── Insert new user ────────────────────────────────────────
                // userid and Timestamp are set automatically by MySQL
                $stmt3 = $conn->prepare(
                    "INSERT INTO users (name, email, age, username, mobile_number, password_hash)
                     VALUES (?, ?, ?, ?, ?, ?)"
                );
                $stmt3->bind_param("ssisss", $name, $email, $age, $username, $fullMobile, $hashedPwd);

                if ($stmt3->execute()) {
                    $success = true;
                } else {
                    $errors['general'] = 'Registration failed. Please try again.';
                }
                $stmt3->close();
            }
        }
    }
}
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account – Anagha's Portfolio</title>
<?php if ($success): ?><meta http-equiv="refresh" content="2;url=login.php"><?php endif; ?>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;600;700&family=Jost:wght@300;400;500;600&display=swap" rel="stylesheet">
<style>
:root{--b1:#4a9cc7;--b2:#6ab4d8;--b3:#8ecae6;--b4:#b8dff0;--ink:#0d2b3e;--muted:#4a7a94;--deep:#1a4a6b;--err:#c0392b;--ok:#00875a;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Jost',sans-serif;min-height:100vh;background:linear-gradient(160deg,#c5e8f5 0%,#d6eff8 35%,#e8f6fc 65%,#cce9f5 100%);display:flex;align-items:flex-start;justify-content:center;padding:32px 20px 48px;overflow-x:hidden;position:relative;}
body::before{content:'';position:fixed;width:380px;height:380px;border-radius:50%;background:radial-gradient(circle,rgba(142,202,230,.2) 0%,transparent 70%);top:-90px;right:-90px;pointer-events:none;}
body::after{content:'';position:fixed;width:300px;height:300px;border-radius:50%;background:radial-gradient(circle,rgba(106,180,216,.16) 0%,transparent 70%);bottom:-60px;left:-60px;pointer-events:none;}
.card{background:rgba(255,255,255,.88);border:1.5px solid rgba(142,202,230,.55);border-radius:22px;padding:38px 38px 36px;width:100%;max-width:480px;box-shadow:0 14px 44px rgba(13,92,130,.13),0 2px 8px rgba(13,92,130,.06);position:relative;z-index:10;animation:rise .7s cubic-bezier(.34,1.4,.64,1) both;}
@keyframes rise{from{opacity:0;transform:translateY(36px) scale(.96)}to{opacity:1;transform:translateY(0) scale(1)}}
.card::before{content:'';position:absolute;top:0;left:0;right:0;height:4px;background:linear-gradient(to right,var(--b1),var(--b3),var(--b4));border-radius:22px 22px 0 0;}
.logo{width:58px;height:58px;border-radius:50%;background:linear-gradient(135deg,var(--b1),var(--b3));display:flex;align-items:center;justify-content:center;font-family:'Cormorant Garamond',serif;font-size:1.25rem;font-weight:700;color:#fff;margin:0 auto 14px;box-shadow:0 5px 16px rgba(74,156,199,.26);}
h1{font-family:'Cormorant Garamond',serif;font-size:1.6rem;font-weight:700;color:var(--ink);text-align:center;margin-bottom:3px;}
.sub{text-align:center;color:var(--muted);font-size:.8rem;margin-bottom:22px;}
.success-box{background:#f0faf5;border:1px solid rgba(0,135,90,.25);color:var(--ok);border-radius:11px;padding:14px;text-align:center;font-weight:600;font-size:.9rem;margin-bottom:10px;}
.err-box{background:#fdf0f0;border:1px solid rgba(192,57,43,.22);color:var(--err);border-radius:11px;padding:12px;text-align:center;font-size:.86rem;margin-bottom:14px;}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:12px;}
@media(max-width:420px){.two-col{grid-template-columns:1fr;}}
.field{margin-bottom:14px;position:relative;}
.field label{display:flex;align-items:center;gap:5px;font-size:.69rem;font-weight:700;color:var(--muted);margin-bottom:5px;letter-spacing:.07em;text-transform:uppercase;}
.req{background:linear-gradient(135deg,var(--b1),var(--b2));color:#fff;font-size:.57rem;padding:2px 7px;border-radius:20px;font-weight:700;}
.auto{background:rgba(142,202,230,.28);border:1px solid rgba(74,156,199,.3);color:var(--deep);font-size:.57rem;padding:2px 7px;border-radius:20px;font-weight:600;}
.ico{position:absolute;left:12px;top:35px;font-size:.88rem;pointer-events:none;}
.field input,.field select{width:100%;padding:11px 14px 11px 36px;border:1.5px solid rgba(142,202,230,.65);border-radius:11px;font-family:'Jost',sans-serif;font-size:.9rem;color:var(--ink);background:#fff;outline:none;transition:border-color .2s,box-shadow .2s;}
.field input:focus,.field select:focus{border-color:var(--b1);box-shadow:0 0 0 3px rgba(74,156,199,.15);}
.field input.ef{border-color:var(--err);}
.eye{position:absolute;right:12px;top:35px;cursor:pointer;font-size:.9rem;color:var(--muted);user-select:none;}
.phone-row{display:flex;gap:8px;}.phone-row select{width:116px;flex-shrink:0;padding:11px 8px;}.phone-row input{flex:1;}
.err-txt{color:var(--err);font-size:.71rem;margin-top:4px;padding-left:2px;}
.strength{height:3px;border-radius:3px;background:rgba(142,202,230,.3);margin-top:6px;overflow:hidden;}
.sf{height:100%;border-radius:3px;transition:width .4s,background .4s;width:0;}
.btn-wrap{margin-top:20px;padding-bottom:4px;}
.btn-submit{display:block;width:100%;padding:14px 20px;border:none;border-radius:12px;font-family:'Jost',sans-serif;font-size:1rem;font-weight:700;cursor:pointer;color:#fff;background:linear-gradient(135deg,#1a6ecf,#2589e8,#3fa0f5);box-shadow:0 6px 20px rgba(26,110,207,.38);transition:transform .2s,box-shadow .2s;text-align:center;}
.btn-submit:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(26,110,207,.52);}
.login-link{text-align:center;margin-top:16px;font-size:.8rem;color:var(--muted);}
.login-link a{color:var(--b1);font-weight:600;text-decoration:none;}
.login-link a:hover{color:var(--deep);}
</style>
</head>
<body>
<div class="card">
  <div class="logo">AI</div>
  <h1>Create Account</h1>
  <p class="sub">Register to access the portfolio</p>

  <?php if ($success): ?>
    <div class="success-box">✅ Account created! Redirecting to sign in…</div>
  <?php else: ?>
  <?php if (isset($errors['general'])): ?>
    <div class="err-box">❌ <?= htmlspecialchars($errors['general']) ?></div>
  <?php endif; ?>

  <form method="POST" action="signup.php" autocomplete="off">

    <div class="field">
      <label>Full Name <span class="req">Required</span></label>
      <span class="ico">👤</span>
      <input type="text" name="name" placeholder="Letters and spaces only"
             value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
             oninput="this.value=this.value.replace(/[^A-Za-z ]/g,'')"
             class="<?= isset($errors['name']) ? 'ef' : '' ?>">
      <?php if (isset($errors['name'])): ?><div class="err-txt">⚠️ <?= $errors['name'] ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label>Email Address <span class="req">Required</span></label>
      <span class="ico">📧</span>
      <input type="text" name="email" placeholder="you@gmail.com"
             value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
             class="<?= isset($errors['email']) ? 'ef' : '' ?>">
      <?php if (isset($errors['email'])): ?><div class="err-txt">⚠️ <?= $errors['email'] ?></div><?php endif; ?>
    </div>

    <div class="two-col">
      <div class="field">
        <label>Age</label>
        <span class="ico">🎂</span>
        <input type="text" name="age" placeholder="e.g. 21" maxlength="2"
               value="<?= htmlspecialchars($_POST['age'] ?? '') ?>"
               oninput="this.value=this.value.replace(/[^0-9]/g,'')"
               class="<?= isset($errors['age']) ? 'ef' : '' ?>">
        <?php if (isset($errors['age'])): ?><div class="err-txt">⚠️ <?= $errors['age'] ?></div><?php endif; ?>
      </div>
      <div class="field">
        <label>Username <span class="auto">Auto-ID by MySQL</span></label>
        <span class="ico">🏷️</span>
        <input type="text" name="username" placeholder="Letters only"
               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
               oninput="this.value=this.value.replace(/[^A-Za-z]/g,'')"
               class="<?= isset($errors['username']) ? 'ef' : '' ?>">
        <?php if (isset($errors['username'])): ?><div class="err-txt">⚠️ <?= $errors['username'] ?></div><?php endif; ?>
      </div>
    </div>

    <div class="field">
      <label>Mobile Number <span class="req">Required</span></label>
      <div class="phone-row">
        <select name="cc">
          <?php
          $codes=['+91'=>'🇮🇳 +91','+1'=>'🇺🇸 +1','+44'=>'🇬🇧 +44','+61'=>'🇦🇺 +61','+971'=>'🇦🇪 +971','+65'=>'🇸🇬 +65'];
          $selCC=$_POST['cc']??'+91';
          foreach($codes as $v=>$l) echo "<option value='$v'".($selCC===$v?' selected':'').">$l</option>";
          ?>
        </select>
        <input type="text" name="mobile" placeholder="10-digit number" maxlength="10"
               value="<?= htmlspecialchars($_POST['mobile'] ?? '') ?>"
               oninput="this.value=this.value.replace(/[^0-9]/g,'')"
               class="<?= isset($errors['mobile']) ? 'ef' : '' ?>">
      </div>
      <?php if (isset($errors['mobile'])): ?><div class="err-txt">⚠️ <?= $errors['mobile'] ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label>Create Password <span class="req">Required</span></label>
      <span class="ico">🔑</span>
      <input type="password" name="pwd" id="pwd"
             placeholder="Min 7 · A-Z a-z 0-9 or @#_"
             oninput="pwdStrength(this.value); this.value=this.value.replace(/[^A-Za-z0-9@#_]/g,'')"
             onkeypress="return /[A-Za-z0-9@#_]/.test(String.fromCharCode(event.which||event.keyCode))"
             class="<?= isset($errors['pwd']) ? 'ef' : '' ?>">
      <span class="eye" id="e1" onclick="toggleEye('pwd','e1')">👁️</span>
      <div class="strength"><div class="sf" id="sf"></div></div>
      <?php if (isset($errors['pwd'])): ?><div class="err-txt">⚠️ <?= $errors['pwd'] ?></div><?php endif; ?>
    </div>

    <div class="field">
      <label>Confirm Password <span class="req">Required</span></label>
      <span class="ico">🔒</span>
      <input type="password" name="cpwd" id="cpwd"
             placeholder="Re-enter your password"
             oninput="this.value=this.value.replace(/[^A-Za-z0-9@#_]/g,'')"
             onkeypress="return /[A-Za-z0-9@#_]/.test(String.fromCharCode(event.which||event.keyCode))"
             class="<?= isset($errors['cpwd']) ? 'ef' : '' ?>">
      <span class="eye" id="e2" onclick="toggleEye('cpwd','e2')">👁️</span>
      <?php if (isset($errors['cpwd'])): ?><div class="err-txt">⚠️ <?= $errors['cpwd'] ?></div><?php endif; ?>
    </div>

    <div class="btn-wrap">
      <button type="submit" class="btn-submit">Create Account →</button>
    </div>
  </form>
  <?php endif; ?>
  <div class="login-link">Already registered? <a href="login.php">Sign in →</a></div>
</div>
<script>
function toggleEye(id,btn){const el=document.getElementById(id);const b=document.getElementById(btn);el.type=el.type==='password'?'text':'password';b.textContent=el.type==='password'?'👁️':'🙈';}
function pwdStrength(v){let s=0;if(v.length>=7)s++;if(/[a-z]/.test(v))s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[@#_]/.test(v))s++;const sf=document.getElementById('sf');sf.style.width=(s/5*100)+'%';sf.style.background=s<=2?'#e07070':s<=3?'#e8a838':'#2e7da8';}
</script>
</body>
</html>

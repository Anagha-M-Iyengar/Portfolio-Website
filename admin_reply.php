<?php
/**
 * admin_reply.php — MySQL version
 * Access: http://localhost/anagha/admin_reply.php?key=anagha2026
 */
require_once __DIR__ . '/db_connect.php';

define('ADMIN_KEY', 'anagha2026');

if (($_GET['key'] ?? '') !== ADMIN_KEY) {
    http_response_code(403); die('Access denied. Add ?key=anagha2026 to the URL.');
}

// ── AJAX reply POST ───────────────────────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    header('Content-Type: application/json');
    $toUser  = strtolower(trim($_POST['to_user'] ?? ''));
    $message = trim($_POST['message'] ?? '');
    $message = preg_replace('/[^A-Za-z0-9 .,!?\'"\\-]/', '', $message);

    if ($toUser === '' || $message === '') {
        echo json_encode(['status'=>'error','message'=>'Empty fields.']); exit;
    }

    $type = 'admin_reply_' . $toUser;
    $stmt = $conn->prepare(
        "INSERT INTO chat_messages (username, message, type) VALUES ('Anagha', ?, ?)"
    );
    $stmt->bind_param("ss", $message, $type);

    if ($stmt->execute()) {
        $stmt2 = $conn->prepare("SELECT sent_at FROM chat_messages WHERE message_id = ?");
        $id    = $conn->insert_id;
        $stmt2->bind_param("i", $id);
        $stmt2->execute();
        $tsRow = $stmt2->get_result()->fetch_assoc();
        $stmt2->close();
        $stmt->close(); $conn->close();
        echo json_encode(['status'=>'ok','sent_at'=>$tsRow['sent_at'],'message'=>$message]);
    } else {
        $stmt->close(); $conn->close();
        echo json_encode(['status'=>'error','message'=>$conn->error]);
    }
    exit;
}

// ── AJAX fetch new messages for a user ───────────────────────────────────────
if (isset($_GET['fetch_user'])) {
    header('Content-Type: application/json');
    $targetUser  = strtolower(trim($_GET['fetch_user']));
    $after       = trim($_GET['after'] ?? '');
    $adminReply  = 'admin_reply_'  . $targetUser;
    $adminNotify = 'admin_notify_' . $targetUser;

    $sql = "SELECT sent_at, username, message, type FROM chat_messages
            WHERE (
                (LOWER(username)=? AND type='user')
                OR type=? OR type=?
            )";
    if ($after !== '') {
        $sql .= " AND sent_at > ?";
        $sql .= " ORDER BY sent_at ASC, message_id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $targetUser, $adminReply, $adminNotify, $after);
    } else {
        $sql .= " ORDER BY sent_at ASC, message_id ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $targetUser, $adminReply, $adminNotify);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $rows   = [];
    while ($row = $result->fetch_assoc()) {
        $rows[] = [
            'ts'      => $row['sent_at'],
            'from'    => $row['username'],
            'msg'     => $row['message'],
            'type'    => $row['type'],
            'is_user' => (strtolower($row['username']) === $targetUser && $row['type'] === 'user')
        ];
    }
    $stmt->close(); $conn->close();
    echo json_encode($rows);
    exit;
}

// ── Load all conversations (page render) ─────────────────────────────────────
$conversations = [];

// Get all distinct usernames who have sent messages (type='user')
$result = $conn->query(
    "SELECT DISTINCT LOWER(username) as uname FROM chat_messages WHERE type='user' ORDER BY uname"
);
while ($row = $result->fetch_assoc()) {
    $uname = $row['uname'];
    $adminReply  = 'admin_reply_'  . $uname;
    $adminNotify = 'admin_notify_' . $uname;

    $stmt = $conn->prepare(
        "SELECT sent_at, username, message, type FROM chat_messages
         WHERE (LOWER(username)=? AND type='user') OR type=? OR type=?
         ORDER BY sent_at ASC, message_id ASC"
    );
    $stmt->bind_param("sss", $uname, $adminReply, $adminNotify);
    $stmt->execute();
    $res2 = $stmt->get_result();
    while ($m = $res2->fetch_assoc()) {
        $conversations[$uname][] = $m;
    }
    $stmt->close();
}

$firstUser = array_key_first($conversations) ?? '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Chat – Anagha</title>
<link href="https://fonts.googleapis.com/css2?family=Jost:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
:root{--b1:#4a9cc7;--b2:#6ab4d8;--b3:#8ecae6;--ink:#0d2b3e;--muted:#4a7a94;--deep:#1a4a6b;--ok:#00875a;--err:#c0392b;}
*{box-sizing:border-box;margin:0;padding:0;}
body{font-family:'Jost',sans-serif;min-height:100vh;background:linear-gradient(160deg,#c5e8f5 0%,#d6eff8 35%,#e8f6fc 70%,#cce9f5 100%);padding:24px 18px;}
h1{font-size:1.3rem;font-weight:700;color:var(--deep);margin-bottom:3px;}
.tagline{font-size:.78rem;color:var(--muted);margin-bottom:18px;}
.live-dot{display:inline-block;width:8px;height:8px;background:#27ae60;border-radius:50%;margin-right:5px;animation:blink 1.4s ease-in-out infinite;}
@keyframes blink{0%,100%{opacity:1}50%{opacity:.25}}
.tabs{display:flex;flex-wrap:wrap;gap:7px;margin-bottom:16px;}
.tab{padding:5px 14px;border-radius:20px;border:1px solid rgba(74,156,199,.4);font-size:.79rem;font-weight:600;color:var(--b1);cursor:pointer;background:rgba(255,255,255,.7);transition:all .18s;}
.tab.active,.tab:hover{background:var(--b1);color:#fff;border-color:var(--b1);}
.tab .unread{display:inline-block;background:#e05555;color:#fff;border-radius:50%;width:16px;height:16px;font-size:.62rem;text-align:center;line-height:16px;margin-left:5px;vertical-align:middle;}
.panel{display:none;background:rgba(255,255,255,.88);border:1px solid rgba(142,202,230,.5);border-radius:16px;overflow:hidden;max-width:680px;}
.panel.active{display:block;}
.panel-header{background:linear-gradient(135deg,var(--deep),var(--b1));padding:11px 16px;color:#fff;font-weight:700;font-size:.9rem;display:flex;align-items:center;gap:8px;}
.panel-header small{margin-left:auto;font-size:.7rem;font-weight:400;opacity:.7;}
.chat-log{height:340px;overflow-y:auto;padding:14px;display:flex;flex-direction:column;gap:9px;background:rgba(240,250,255,.55);scroll-behavior:smooth;}
.chat-log::-webkit-scrollbar{width:4px;}
.chat-log::-webkit-scrollbar-thumb{background:rgba(74,156,199,.25);border-radius:4px;}
.msg{max-width:80%;}
.msg-bubble{padding:8px 12px;border-radius:13px;font-size:.83rem;line-height:1.55;word-break:break-word;}
.msg-meta{font-size:.65rem;color:var(--muted);margin-top:3px;}
.msg.user-msg{align-self:flex-start;}
.msg.user-msg .msg-bubble{background:rgba(255,255,255,.92);border:1px solid rgba(74,156,199,.22);color:var(--ink);border-bottom-left-radius:4px;}
.msg.admin-msg{align-self:flex-end;text-align:right;}
.msg.admin-msg .msg-bubble{background:linear-gradient(135deg,var(--deep),var(--b1));color:#fff;border-bottom-right-radius:4px;}
.msg.notify-msg{align-self:flex-start;}
.msg.notify-msg .msg-bubble{background:rgba(214,239,248,.6);border:1px dashed rgba(74,156,199,.4);color:var(--deep);font-style:italic;font-size:.79rem;border-radius:10px;}
.reply-form{padding:12px 14px;background:rgba(255,255,255,.88);border-top:1px solid rgba(200,230,245,.6);display:flex;gap:9px;align-items:flex-end;}
.reply-form textarea{flex:1;padding:9px 12px;border:1.5px solid rgba(142,202,230,.65);border-radius:10px;font-family:'Jost',sans-serif;font-size:.86rem;color:var(--ink);background:#fff;outline:none;resize:none;height:42px;transition:border-color .2s;}
.reply-form textarea:focus{border-color:var(--b1);}
.reply-form button{padding:9px 20px;background:linear-gradient(135deg,var(--deep),var(--b1));color:#fff;border:none;border-radius:10px;font-family:'Jost',sans-serif;font-size:.86rem;font-weight:700;cursor:pointer;white-space:nowrap;transition:transform .2s,opacity .2s;}
.reply-form button:hover{transform:translateY(-1px);}
.reply-form button:disabled{opacity:.5;cursor:not-allowed;transform:none;}
.send-status{font-size:.72rem;padding:3px 12px 5px;min-height:20px;color:var(--ok);font-weight:600;}
.empty-state{color:var(--muted);font-size:.88rem;padding:50px 0;text-align:center;}
</style>
</head>
<body>
<h1>🐧 Admin Chat Panel</h1>
<p class="tagline"><span class="live-dot"></span>Live updates every 2 seconds · MySQL powered</p>

<?php if (empty($conversations)): ?>
  <div class="empty-state">No chat messages yet. Visitors will appear here after they message you. 💙</div>
<?php else: ?>

<div class="tabs">
  <?php foreach (array_keys($conversations) as $uname): ?>
    <div class="tab <?= $uname===$firstUser?'active':'' ?>"
         id="tab-<?= htmlspecialchars($uname) ?>"
         onclick="switchUser('<?= htmlspecialchars($uname) ?>', this)">
      👤 <?= htmlspecialchars($uname) ?>
    </div>
  <?php endforeach; ?>
</div>

<?php foreach ($conversations as $uname => $msgs): ?>
<div class="panel <?= $uname===$firstUser?'active':'' ?>" id="panel-<?= htmlspecialchars($uname) ?>">
  <div class="panel-header">
    💬 <?= htmlspecialchars($uname) ?>
    <small id="status-<?= htmlspecialchars($uname) ?>">polling…</small>
  </div>
  <div class="chat-log" id="log-<?= htmlspecialchars($uname) ?>">
    <?php foreach ($msgs as $m):
      $isUser   = ($m['type'] === 'user');
      $isAdmin  = str_starts_with($m['type'], 'admin_reply_');
      $isNotify = str_starts_with($m['type'], 'admin_notify_');
      $cls      = $isUser ? 'user-msg' : ($isAdmin ? 'admin-msg' : 'notify-msg');
      $who      = $isUser ? htmlspecialchars($m['username']) : ($isAdmin ? 'You (Anagha)' : '🤖 Auto-notice');
    ?>
      <div class="msg <?= $cls ?>" data-ts="<?= htmlspecialchars($m['sent_at']) ?>">
        <div class="msg-bubble"><?= htmlspecialchars($m['message']) ?></div>
        <div class="msg-meta"><?= $who ?> · <?= htmlspecialchars($m['sent_at']) ?></div>
      </div>
    <?php endforeach; ?>
  </div>
  <div class="send-status" id="ss-<?= htmlspecialchars($uname) ?>"></div>
  <div class="reply-form">
    <textarea id="reply-<?= htmlspecialchars($uname) ?>"
              placeholder="Reply to <?= htmlspecialchars($uname) ?>…"
              onkeydown="if(event.key==='Enter'&&!event.shiftKey){event.preventDefault();sendReply('<?= htmlspecialchars($uname) ?>');}"></textarea>
    <button id="btn-<?= htmlspecialchars($uname) ?>"
            onclick="sendReply('<?= htmlspecialchars($uname) ?>')">Reply ✈️</button>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>

<script>
const KEY='<?= ADMIN_KEY ?>';
let activeUser='<?= htmlspecialchars($firstUser) ?>';
const lastTs={};
<?php foreach (array_keys($conversations) as $uname): ?>
lastTs['<?= htmlspecialchars($uname) ?>']='<?php
  $msgs=$conversations[$uname];
  echo !empty($msgs)?end($msgs)['sent_at']:'';
?>';
<?php endforeach; ?>

function switchUser(name,el){
  document.querySelectorAll('.panel').forEach(p=>p.classList.remove('active'));
  document.querySelectorAll('.tab').forEach(t=>t.classList.remove('active'));
  document.getElementById('panel-'+name)?.classList.add('active');
  el.classList.add('active');
  el.querySelector('.unread')?.remove();
  activeUser=name;scrollLog(name);
}
function scrollLog(n){const l=document.getElementById('log-'+n);if(l)l.scrollTop=l.scrollHeight;}
function esc(s){return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');}
function appendMsg(username,m){
  const log=document.getElementById('log-'+username);if(!log)return;
  const isUser=(m.type==='user');const isAdmin=m.type&&m.type.startsWith('admin_reply_');const isNotify=m.type&&m.type.startsWith('admin_notify_');
  const cls=isUser?'user-msg':(isAdmin?'admin-msg':'notify-msg');
  const who=isUser?esc(m.from||m.username||username):(isAdmin?'You (Anagha)':'🤖 Auto-notice');
  const div=document.createElement('div');div.className='msg '+cls;div.dataset.ts=m.ts||m.sent_at;
  div.innerHTML=`<div class="msg-bubble">${esc(m.msg||m.message)}</div><div class="msg-meta">${who} · ${esc(m.ts||m.sent_at)}</div>`;
  log.appendChild(div);log.scrollTop=log.scrollHeight;
}
async function sendReply(username){
  const ta=document.getElementById('reply-'+username);const btn=document.getElementById('btn-'+username);const ss=document.getElementById('ss-'+username);
  const msg=ta.value.trim();if(!msg)return;
  btn.disabled=true;btn.textContent='Sending…';ss.textContent='';
  const fd=new FormData();fd.append('ajax','1');fd.append('to_user',username);fd.append('message',msg);
  try{
    const res=await fetch('admin_reply.php?key='+KEY,{method:'POST',body:fd});
    const data=await res.json();
    if(data.status==='ok'){ta.value='';ss.textContent='✅ Sent!';setTimeout(()=>ss.textContent='',2000);
      appendMsg(username,{ts:data.sent_at,from:'Anagha',msg:data.message,type:'admin_reply_'+username});
      if(!lastTs[username]||data.sent_at>lastTs[username])lastTs[username]=data.sent_at;
    }else{ss.textContent='❌ '+(data.message||'Error');}
  }catch(e){ss.textContent='❌ Network error';}
  finally{btn.disabled=false;btn.textContent='Reply ✈️';ta.focus();}
}
async function pollMessages(){
  for(const username of Object.keys(lastTs)){
    try{
      const url=`admin_reply.php?key=${KEY}&fetch_user=${encodeURIComponent(username)}&after=${encodeURIComponent(lastTs[username]||'')}`;
      const res=await fetch(url);const msgs=await res.json();
      if(!Array.isArray(msgs)||msgs.length===0)continue;
      msgs.forEach(m=>{appendMsg(username,m);if(!lastTs[username]||m.ts>lastTs[username])lastTs[username]=m.ts;});
      if(msgs.some(m=>m.is_user||m.type==='user')&&username!==activeUser){
        const tab=document.getElementById('tab-'+username);
        if(tab&&!tab.querySelector('.unread')){const b=document.createElement('span');b.className='unread';b.textContent='!';tab.appendChild(b);}
      }
      const st=document.getElementById('status-'+username);if(st)st.textContent='Updated '+new Date().toLocaleTimeString();
    }catch(e){}
  }
}
document.querySelectorAll('.chat-log').forEach(l=>l.scrollTop=l.scrollHeight);
pollMessages();
setInterval(pollMessages,2000);
</script>
</body>
</html>

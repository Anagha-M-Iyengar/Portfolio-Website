<?php
/**
 * chat_send.php — saves to MySQL chat_messages table
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['status'=>'error','message'=>'Not logged in.']); exit;
}

$username = $_SESSION['username'] ?? '';
$message  = trim($_POST['message'] ?? '');

if ($message === '') {
    echo json_encode(['status'=>'error','message'=>'Empty message.']); exit;
}

// Strip everything except letters, numbers, spaces
$message = preg_replace('/[^A-Za-z0-9 ]/', '', $message);
if ($message === '') {
    echo json_encode(['status'=>'error','message'=>'Letters and numbers only.']); exit;
}

// Count this user's sent messages (type = 'user' only)
$stmt = $conn->prepare(
    "SELECT COUNT(*) as cnt FROM chat_messages WHERE username = ? AND type = 'user'"
);
$stmt->bind_param("s", $username);
$stmt->execute();
$row      = $stmt->get_result()->fetch_assoc();
$stmt->close();
$userCount = (int)$row['cnt'];

if ($userCount >= 10) {
    $conn->close();
    echo json_encode(['status'=>'limit',
        'message'=>'You have reached the 10-message limit. Anagha has been notified and will contact you soon.']);
    exit;
}

// Save the user message — sent_at is set automatically by MySQL
$stmt = $conn->prepare(
    "INSERT INTO chat_messages (username, message, type) VALUES (?, ?, 'user')"
);
$stmt->bind_param("ss", $username, $message);
$stmt->execute();
$stmt->close();

$newCount = $userCount + 1;

// On the 10th message, add an automatic admin notification
if ($newCount === 10) {
    $notifyMsg  = "Hi {$username}! You've reached the chat limit. Anagha will review your messages and get back to you soon. Thank you! 💙";
    $notifyType = 'admin_notify_' . strtolower($username);
    $stmt2 = $conn->prepare(
        "INSERT INTO chat_messages (username, message, type) VALUES ('Anagha', ?, ?)"
    );
    $stmt2->bind_param("ss", $notifyMsg, $notifyType);
    $stmt2->execute();
    $stmt2->close();
}

$conn->close();
echo json_encode(['status'=>'ok','count'=>$newCount,'remaining'=>(10 - $newCount)]);
exit;
?>

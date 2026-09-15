<?php
/**
 * chat_fetch.php — reads from MySQL chat_messages table
 * Returns only messages for the logged-in user (private per user)
 */
session_start();
header('Content-Type: application/json');
require_once __DIR__ . '/db_connect.php';

if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    echo json_encode(['messages'=>[],'count'=>0,'limit'=>10]); exit;
}

$username = strtolower($_SESSION['username'] ?? '');
$after    = trim($_GET['after'] ?? '');   // timestamp string e.g. "2026-03-29 10:22:01"

// Build query — only get messages belonging to this user
// Using GREATEST to handle both user messages and admin messages tagged for this user
$sql = "SELECT message_id, sent_at, username, message, type
        FROM chat_messages
        WHERE (
            (LOWER(username) = ? AND type = 'user')
            OR type = ?
            OR type = ?
        )";

$adminReply  = 'admin_reply_'  . $username;
$adminNotify = 'admin_notify_' . $username;

// Add the "after" filter if a timestamp was provided
if ($after !== '') {
    $sql .= " AND sent_at > ?";
    $sql .= " ORDER BY sent_at ASC, message_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $username, $adminReply, $adminNotify, $after);
} else {
    $sql .= " ORDER BY sent_at ASC, message_id ASC";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $username, $adminReply, $adminNotify);
}

$stmt->execute();
$result = $stmt->get_result();
$rows   = [];

while ($row = $result->fetch_assoc()) {
    $isMyMsg = (strtolower($row['username']) === $username && $row['type'] === 'user');
    $rows[]  = [
        'ts'      => $row['sent_at'],
        'name'    => $row['username'],
        'msg'     => $row['message'],
        'type'    => $row['type'],
        'is_mine' => $isMyMsg
    ];
}
$stmt->close();

// Get this user's total sent message count for the counter badge
$stmt2 = $conn->prepare(
    "SELECT COUNT(*) as cnt FROM chat_messages WHERE LOWER(username) = ? AND type = 'user'"
);
$stmt2->bind_param("s", $username);
$stmt2->execute();
$countRow  = $stmt2->get_result()->fetch_assoc();
$stmt2->close();
$conn->close();

echo json_encode([
    'messages' => $rows,
    'count'    => (int)$countRow['cnt'],
    'limit'    => 10
]);
exit;
?>

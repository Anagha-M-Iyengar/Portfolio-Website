<?php
/**
 * save_contact.php
 * Saves "Get In Touch" form to MySQL contacts table instead of contacts.csv
 */
require_once __DIR__ . '/db_connect.php';

header('Content-Type: text/plain');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { echo "ready"; exit; }

$name    = trim($_POST['name']    ?? '');
$email   = trim($_POST['email']   ?? '');
$mobile  = trim($_POST['mobile']  ?? '');
$message = trim($_POST['message'] ?? '');
$company = trim($_POST['company'] ?? '');

if (!$name || !$email || !$mobile || !$message || !$company) {
    echo "error: missing fields"; exit;
}

// INSERT into contacts table — submitted_at is set automatically by MySQL
$stmt = $conn->prepare(
    "INSERT INTO contacts (name, email, mobile, message, company)
     VALUES (?, ?, ?, ?, ?)"
);
$stmt->bind_param("sssss", $name, $email, $mobile, $message, $company);

if ($stmt->execute()) {
    echo "success";
} else {
    echo "error: " . $conn->error;
}

$stmt->close();
$conn->close();
?>

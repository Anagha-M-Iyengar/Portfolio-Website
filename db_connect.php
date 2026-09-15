<?php
/**
 * db_connect.php
 * One file that holds the database connection.
 * Every other PHP file includes this instead of writing connection code repeatedly.
 * If you change your password or database name, you only change it HERE.
 */

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', 'Anaghaa@25');          // ✅ empty
define('DB_NAME', 'anaghaz');
// Create the connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

// Check if connection failed
if ($conn->connect_error) {
    // In production you'd log this — never show raw DB errors to users
    die(json_encode([
        'status'  => 'error',
        'message' => 'Database connection failed. Make sure XAMPP MySQL is running.'
    ]));
}

// Set character encoding to UTF-8 so names with special characters save correctly
$conn->set_charset('utf8mb4');
?>

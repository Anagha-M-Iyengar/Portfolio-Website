<?php
// session_start() is called by the parent file (login.php / signup.php)

$current = basename($_SERVER['PHP_SELF']);

if (!isset($_SESSION['visited_intro'])) {
    if (!in_array($current, ['login.php', 'signup.php'])) {
        header("Location: intro.php");
        exit;
    }
}
?>
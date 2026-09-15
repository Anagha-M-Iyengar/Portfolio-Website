<?php
/**
 * logout.php
 * Destroys the session completely and sends user back to intro/landing page.
 * Called when user clicks Sign Out on cv.php.
 */
session_start();
session_unset();
session_destroy();

// Delete the session cookie explicitly for immediate effect
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Redirect to index.php (the landing/home page)
header("Location: index.php");
exit;
?>

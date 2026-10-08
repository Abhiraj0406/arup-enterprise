<?php
// admin/logout.php - Logout Processing
session_start();

// Clear all session variables
$_SESSION = array();

// Delete session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Destroy the session
session_destroy();

// Unset session
unset($_SESSION);

// Redirect to admin login
header("Location: login.php?logout=success");
exit();
?>
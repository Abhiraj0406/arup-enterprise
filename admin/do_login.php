<?php
// ============================================================
// admin/do_login.php — Login Processing (with rate limiting)
// ============================================================
session_start();
require_once "../includes/db.php";

// ---- Rate limiting: max 5 failed attempts per 15 minutes ----
$max_attempts  = 5;
$lockout_time  = 15 * 60; // 15 minutes in seconds
$attempt_key   = 'login_attempts';
$lockout_key   = 'login_lockout_until';

$now = time();

// Clear old lockout if expired
if (!empty($_SESSION[$lockout_key]) && $_SESSION[$lockout_key] <= $now) {
    unset($_SESSION[$lockout_key], $_SESSION[$attempt_key]);
}

// Block if still locked out
if (!empty($_SESSION[$lockout_key]) && $_SESSION[$lockout_key] > $now) {
    $wait = ceil(($_SESSION[$lockout_key] - $now) / 60);
    $_SESSION['error'] = "Too many failed attempts. Please wait {$wait} minute(s) and try again.";
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email']    ?? '');
    $password = trim($_POST['password'] ?? '');

    // Basic input check
    if (empty($email) || empty($password)) {
        $_SESSION['error'] = "Email and password are required.";
        header("Location: login.php");
        exit();
    }

    // ✅ Check admin in database
    $stmt = $conn->prepare("SELECT * FROM admin_users WHERE email = ? LIMIT 1");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        // ✅ Verify hashed password
        if (password_verify($password, $user['password'])) {
            // ✅ Successful login — clear rate limit counters
            unset($_SESSION[$attempt_key], $_SESSION[$lockout_key]);

            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);

            // ✅ Set session variables
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id']        = $user['id'];
            $_SESSION['admin_name']      = $user['full_name'];
            $_SESSION['admin_email']     = $user['email'];

            $stmt->close();
            header("Location: dashboard.php");
            exit();
        }
    }

    // ❌ Failed login — increment counter
    $stmt->close();
    $_SESSION[$attempt_key] = ($_SESSION[$attempt_key] ?? 0) + 1;

    if ($_SESSION[$attempt_key] >= $max_attempts) {
        $_SESSION[$lockout_key] = $now + $lockout_time;
        unset($_SESSION[$attempt_key]);
        $_SESSION['error'] = "Too many failed attempts. You are locked out for 15 minutes.";
    } else {
        $remaining = $max_attempts - $_SESSION[$attempt_key];
        $_SESSION['error'] = "Invalid email or password. {$remaining} attempt(s) remaining.";
    }

    header("Location: login.php");
    exit();

} else {
    header("Location: login.php");
    exit();
}
?>
<?php
// ============================================================
// admin/includes/db.php — Database Connection (env-powered)
// ============================================================

// Load env parser + values from .env
require_once __DIR__ . '/../../includes/env.php';

// Check root directory first (outside public_html), then fallback to local
if (file_exists(__DIR__ . '/../../../.env')) {
    load_env(__DIR__ . '/../../../.env');
} elseif (file_exists(__DIR__ . '/../../.env')) {
    load_env(__DIR__ . '/../../.env');
}

// ----- Environment mode -----
$app_env = env('APP_ENV', 'production');
$is_dev  = ($app_env === 'development');

// ----- Error display: ON in dev, OFF in production -----
ini_set('display_errors',         $is_dev ? '1' : '0');
ini_set('display_startup_errors', $is_dev ? '1' : '0');
error_reporting($is_dev ? E_ALL : E_ALL & ~E_DEPRECATED & ~E_STRICT);

// ----- Timezone -----
date_default_timezone_set(env('APP_TIMEZONE', 'Asia/Kolkata'));

// ----- Database credentials from .env -----
$host    = env('DB_HOST',    'localhost');
$db      = env('DB_NAME',    '');
$user    = env('DB_USER',    '');
$pass    = env('DB_PASS',    '');
$charset = env('DB_CHARSET', 'utf8mb4');

// ----- Create connection -----
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    if ($is_dev) {
        die("DB Connection failed: " . $conn->connect_error);
    } else {
        die("A technical error occurred. Please try again later.");
    }
}

// ----- Set MySQL timezone & charset -----
$conn->query("SET time_zone = '+05:30'");
$conn->set_charset($charset);
?>
<?php
// ============================================================
// env.php — Lightweight .env file parser (no Composer needed)
// Include this ONCE before anything else.
// ============================================================

function load_env(string $path): void
{
    if (!file_exists($path)) {
        // On production without .env, environment variables may come from server
        return;
    }

    $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($lines as $line) {
        // Skip comments
        $line = trim($line);
        if ($line === '' || $line[0] === '#') {
            continue;
        }

        // Split on first '=' only
        $parts = explode('=', $line, 2);
        if (count($parts) !== 2) {
            continue;
        }

        $key   = trim($parts[0]);
        $value = trim($parts[1]);

        // Strip surrounding quotes (single or double)
        if (strlen($value) >= 2) {
            $first = $value[0];
            $last  = $value[strlen($value) - 1];
            if (($first === '"' && $last === '"') || ($first === "'" && $last === "'")) {
                $value = substr($value, 1, -1);
            }
        }

        // Set into environment superglobals (both for portability)
        if (!array_key_exists($key, $_ENV)) {
            $_ENV[$key] = $value;
            putenv("$key=$value");
        }
    }
}

/**
 * Retrieve an env variable with an optional default fallback.
 * Usage: env('DB_HOST', 'localhost')
 */
function env(string $key, $default = null)
{
    $val = $_ENV[$key] ?? getenv($key);
    return ($val !== false && $val !== null && $val !== '') ? $val : $default;
}

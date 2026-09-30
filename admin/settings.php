<?php
// admin/settings.php - Company & Account Settings
session_start();
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once "../includes/db.php";
date_default_timezone_set('Asia/Kolkata');

// Self-healing setup: create the company settings table if it doesn't exist
try {
    $conn->query("CREATE TABLE IF NOT EXISTS site_settings (
        id INT PRIMARY KEY,
        company_name VARCHAR(150) NOT NULL DEFAULT 'Arup Enterprise',
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
    )");
} catch (mysqli_sql_exception $e) {
    // Could not auto-create
}

// CSRF token for the forms on this page
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$admin_id = isset($_SESSION['admin_id']) ? (int)$_SESSION['admin_id'] : null;

$errors = [];
$success = [];

// ---- Load current admin record ----
$admin = null;
if ($admin_id) {
    try {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    } catch (mysqli_sql_exception $e) {
        $errors[] = "Could not load your admin record.";
    }
}

// ---- Load company settings (single row, id = 1) ----
$company_name = "Arup Enterprise";
$settings_row = null;
try {
    $settings_check = $conn->query("SELECT * FROM site_settings WHERE id = 1 LIMIT 1");
    if ($settings_check && $settings_check->num_rows > 0) {
        $settings_row = $settings_check->fetch_assoc();
        if (!empty($settings_row['company_name'])) {
            $company_name = $settings_row['company_name'];
        }
    }
} catch (mysqli_sql_exception $e) {
    // Table doesn't exist yet
}

// ====================== HANDLE FORM SUBMISSIONS ======================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $errors[] = "Your session expired. Please try again.";
    } else {
        $action = $_POST['action'] ?? '';

        // ---------------- Update company name ----------------
        if ($action === 'update_company') {
            $new_company = trim($_POST['company_name'] ?? '');
            if ($new_company === '') {
                $errors[] = "Company name cannot be empty.";
            } elseif (mb_strlen($new_company) > 150) {
                $errors[] = "Company name is too long.";
            } else {
                try {
                    $exists = $conn->query("SELECT id FROM site_settings WHERE id = 1");
                    if ($exists && $exists->num_rows > 0) {
                        $upd = $conn->prepare("UPDATE site_settings SET company_name = ? WHERE id = 1");
                        $upd->bind_param("s", $new_company);
                        $upd->execute();
                        $upd->close();
                    } else {
                        $ins = $conn->prepare("INSERT INTO site_settings (id, company_name) VALUES (1, ?)");
                        $ins->bind_param("s", $new_company);
                        $ins->execute();
                        $ins->close();
                    }
                    $company_name = $new_company;
                    $success[] = "Company name updated successfully.";
                } catch (mysqli_sql_exception $e) {
                    $errors[] = "Could not save company name.";
                }
            }
        }

        // ---------------- Update login email ----------------
        elseif ($action === 'update_email') {
            if (!$admin_id) {
                $errors[] = "Could not identify your admin account.";
            } else {
                $new_email = trim($_POST['new_email'] ?? '');
                $current_password = $_POST['current_password_email'] ?? '';

                if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "Please enter a valid email address.";
                } else {
                    $stmt = $conn->prepare("SELECT password FROM admin_users WHERE id = ?");
                    $stmt->bind_param("i", $admin_id);
                    $stmt->execute();
                    $row = $stmt->get_result()->fetch_assoc();
                    $stmt->close();

                    if (!$row || !password_verify($current_password, $row['password'])) {
                        $errors[] = "Current password is incorrect.";
                    } else {
                        $check = $conn->prepare("SELECT id FROM admin_users WHERE email = ? AND id != ?");
                        $check->bind_param("si", $new_email, $admin_id);
                        $check->execute();
                        $taken = $check->get_result()->num_rows > 0;
                        $check->close();

                        if ($taken) {
                            $errors[] = "That email is already in use by another account.";
                        } else {
                            $upd = $conn->prepare("UPDATE admin_users SET email = ? WHERE id = ?");
                            $upd->bind_param("si", $new_email, $admin_id);
                            $upd->execute();
                            $upd->close();
                            $admin['email'] = $new_email;
                            $success[] = "Login email updated successfully.";
                        }
                    }
                }
            }
        }

        // ---------------- Update password ----------------
        elseif ($action === 'update_password') {
            if (!$admin_id) {
                $errors[] = "Could not identify your admin account.";
            } else {
                $current_password = $_POST['current_password'] ?? '';
                $new_password = $_POST['new_password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';

                $stmt = $conn->prepare("SELECT password FROM admin_users WHERE id = ?");
                $stmt->bind_param("i", $admin_id);
                $stmt->execute();
                $row = $stmt->get_result()->fetch_assoc();
                $stmt->close();

                if (!$row || !password_verify($current_password, $row['password'])) {
                    $errors[] = "Current password is incorrect.";
                } elseif (strlen($new_password) < 8) {
                    $errors[] = "New password must be at least 8 characters.";
                } elseif ($new_password !== $confirm_password) {
                    $errors[] = "New password and confirmation do not match.";
                } else {
                    $hashed = password_hash($new_password, PASSWORD_BCRYPT);
                    $upd = $conn->prepare("UPDATE admin_users SET password = ? WHERE id = ?");
                    $upd->bind_param("si", $hashed, $admin_id);
                    $upd->execute();
                    $upd->close();
                    $success[] = "Password updated successfully.";
                }
            }
        }
    }

    // Refresh admin data after any update
    if ($admin_id) {
        $stmt = $conn->prepare("SELECT * FROM admin_users WHERE id = ?");
        $stmt->bind_param("i", $admin_id);
        $stmt->execute();
        $admin = $stmt->get_result()->fetch_assoc();
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings | <?php echo htmlspecialchars($company_name); ?> Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700;800;900&family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        :root {
            --gold: #DC2626;
            --gold-dark: #B91C1C;
            --gold-light: #EF4444;
            --cream: #FFF5F5;
            --ivory: #FEE2E2;
            --charcoal: #111827;
            --mid-gray: #4B5563;
            --light-gray: #FECACA;
            --white: #ffffff;
            --shadow-gold: 0 4px 20px rgba(220,38,38,0.2);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.06);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--cream);
            color: var(--charcoal);
            font-size: 14px;
        }

        .ultra-header {
            background: var(--white);
            border-radius: 14px;
            padding: 16px 24px;
            margin-bottom: 20px;
            border: 1px solid rgba(220,38,38,0.12);
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }
        .ultra-header::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light), var(--gold));
        }
        .ultra-header h1 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 800;
            color: var(--charcoal);
            margin: 0;
        }
        .ultra-header h1 i { color: var(--gold); margin-right: 8px; }
        .ultra-header .subtitle { color: var(--mid-gray); font-size: 0.75rem; margin: 2px 0 0; }
        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            padding: 5px 14px;
            border-radius: 20px;
            background: var(--light-gray);
            color: var(--charcoal);
            font-size: 0.7rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        .btn-back:hover { background: #fca5a5; transform: translateY(-2px); }

        .settings-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }

        .settings-card {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid rgba(220,38,38,0.08);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            padding: 18px 20px;
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .settings-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border-color: rgba(220,38,38,0.15);
        }
        .settings-card::before {
            content: '';
            position: absolute;
            top: 0; left: 0;
            width: 3px; height: 100%;
            background: linear-gradient(to bottom, var(--gold), var(--gold-light));
        }
        .settings-card h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1rem;
            font-weight: 700;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--charcoal);
        }
        .settings-card h3 i { color: var(--gold); }
        .settings-card .desc {
            color: var(--mid-gray);
            font-size: 0.72rem;
            margin-bottom: 14px;
        }

        .field-label {
            font-size: 0.65rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: var(--gold-dark);
            margin-bottom: 4px;
            display: block;
        }
        .field-input-wrap { position: relative; margin-bottom: 12px; }
        .field-input {
            width: 100%;
            height: 40px;
            background: var(--cream);
            border: 2px solid var(--light-gray);
            border-radius: 8px;
            padding: 0 40px 0 12px;
            font-size: 0.82rem;
            color: var(--charcoal);
            transition: all 0.3s;
            outline: none;
        }
        .field-input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(220,38,38,0.08);
            background: var(--white);
        }
        .field-eye {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--mid-gray);
            cursor: pointer;
            font-size: 0.8rem;
            padding: 4px;
        }
        .field-eye:hover { color: var(--gold); }

        .current-value {
            background: var(--cream);
            border: 1px dashed var(--light-gray);
            border-radius: 8px;
            padding: 8px 12px;
            font-size: 0.72rem;
            color: var(--mid-gray);
            margin-bottom: 12px;
        }
        .current-value strong { color: var(--charcoal); }

        .btn-save {
            width: 100%;
            height: 40px;
            background: linear-gradient(135deg, var(--gold), var(--gold-dark));
            border: none;
            border-radius: 8px;
            color: var(--white);
            font-weight: 700;
            font-size: 0.72rem;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            cursor: pointer;
            transition: all 0.3s;
        }
        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(220,38,38,0.3);
        }
        .btn-save:disabled {
            opacity: 0.5;
            cursor: not-allowed;
            transform: none;
        }

        .alert-box {
            border-radius: 10px;
            padding: 10px 14px;
            margin-bottom: 14px;
            font-size: 0.78rem;
            display: flex;
            align-items: flex-start;
            gap: 8px;
            border-left: 3px solid;
        }
        .alert-error { background: #fef2f2; color: #b91c1c; border-left-color: #b91c1c; }
        .alert-success { background: #dcfce7; color: #166534; border-left-color: #16a34a; }
        .alert-box ul { margin: 0; padding-left: 16px; }

        .setup-note {
            background: #fff5f5;
            border: 1px dashed var(--gold);
            border-radius: 10px;
            padding: 10px 14px;
            font-size: 0.72rem;
            color: #b91c1c;
            margin-bottom: 16px;
        }
        .setup-note code {
            background: rgba(0,0,0,0.06);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.68rem;
        }

        .content {
            padding: 16px 20px 30px;
        }

        @media (max-width: 992px) {
            .settings-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 768px) {
            .ultra-header { padding: 14px 18px; }
            .ultra-header h1 { font-size: 1.1rem; }
            .settings-grid {
                grid-template-columns: 1fr;
            }
            .settings-card { padding: 14px 16px; }
            .content { padding: 12px 14px 20px; }
        }

        @media (max-width: 480px) {
            .ultra-header h1 { font-size: 0.95rem; }
            .settings-card h3 { font-size: 0.9rem; }
            .field-input { height: 36px; font-size: 0.78rem; }
            .btn-save { height: 36px; font-size: 0.68rem; }
        }
    </style>
</head>
<body>

    <?php include "includes/navbar.php"; ?>

    <div class="content">
        <div class="container-fluid">

            <div class="ultra-header">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h1><i class="fas fa-cog"></i> Account &amp; Company Settings</h1>
                        <p class="subtitle">Manage your business name, login email, and password.</p>
                    </div>
                    <div class="col-md-4 text-md-end mt-2 mt-md-0">
                        <a href="dashboard.php" class="btn-back"><i class="fas fa-arrow-left"></i> Dashboard</a>
                    </div>
                </div>
            </div>

            <?php if (!$admin_id): ?>
                <div class="setup-note">
                    <i class="fas fa-info-circle"></i>
                    The email and password forms below need to know which admin is logged in.
                    Add this line to <code>do_login.php</code> right after a successful login check:
                    <code>$_SESSION['admin_id'] = $admin['id'];</code>
                </div>
            <?php endif; ?>

            <?php if (!empty($errors)): ?>
                <div class="alert-box alert-error">
                    <i class="fas fa-exclamation-circle"></i>
                    <ul>
                        <?php foreach ($errors as $e): ?>
                            <li><?php echo htmlspecialchars($e); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <?php if (!empty($success)): ?>
                <div class="alert-box alert-success">
                    <i class="fas fa-check-circle"></i>
                    <ul>
                        <?php foreach ($success as $s): ?>
                            <li><?php echo htmlspecialchars($s); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div class="settings-grid">

                <!-- ===== COMPANY NAME ===== -->
                <div class="settings-card">
                    <h3><i class="fas fa-building"></i> Company Name</h3>
                    <p class="desc">This name appears across the admin dashboard and your public site.</p>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="action" value="update_company">
                        <label class="field-label">Company Name</label>
                        <div class="field-input-wrap">
                            <input type="text" name="company_name" class="field-input" value="<?php echo htmlspecialchars($company_name); ?>" maxlength="150" required>
                        </div>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Save Company</button>
                    </form>
                </div>

                <!-- ===== LOGIN EMAIL ===== -->
                <div class="settings-card">
                    <h3><i class="fas fa-id-badge"></i> Login Email</h3>
                    <p class="desc">This is the email address you use to log in.</p>
                    <div class="current-value">
                        Logged in as: <strong><?php echo htmlspecialchars($admin['full_name'] ?? $_SESSION['admin_name'] ?? 'Admin'); ?></strong><br>
                        Current email: <strong><?php echo htmlspecialchars($admin['email'] ?? 'Not available'); ?></strong>
                    </div>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="action" value="update_email">

                        <label class="field-label">New Email</label>
                        <div class="field-input-wrap">
                            <input type="email" name="new_email" class="field-input" placeholder="new@email.com" required <?php echo !$admin_id ? 'disabled' : ''; ?>>
                        </div>

                        <label class="field-label">Current Password</label>
                        <div class="field-input-wrap">
                            <input type="password" name="current_password_email" id="pw1" class="field-input" placeholder="Enter current password" required <?php echo !$admin_id ? 'disabled' : ''; ?>>
                            <button type="button" class="field-eye" onclick="toggleEye('pw1', this)"><i class="fas fa-eye"></i></button>
                        </div>

                        <button type="submit" class="btn-save" <?php echo !$admin_id ? 'disabled' : ''; ?>>
                            <i class="fas fa-save"></i> Update Email
                        </button>
                    </form>
                </div>

                <!-- ===== PASSWORD ===== -->
                <div class="settings-card">
                    <h3><i class="fas fa-lock"></i> Password</h3>
                    <p class="desc">Use a strong password — at least 8 characters.</p>
                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
                        <input type="hidden" name="action" value="update_password">

                        <label class="field-label">Current Password</label>
                        <div class="field-input-wrap">
                            <input type="password" name="current_password" id="pw2" class="field-input" placeholder="Enter current password" required <?php echo !$admin_id ? 'disabled' : ''; ?>>
                            <button type="button" class="field-eye" onclick="toggleEye('pw2', this)"><i class="fas fa-eye"></i></button>
                        </div>

                        <label class="field-label">New Password</label>
                        <div class="field-input-wrap">
                            <input type="password" name="new_password" id="pw3" class="field-input" placeholder="At least 8 characters" minlength="8" required <?php echo !$admin_id ? 'disabled' : ''; ?>>
                            <button type="button" class="field-eye" onclick="toggleEye('pw3', this)"><i class="fas fa-eye"></i></button>
                        </div>

                        <label class="field-label">Confirm New Password</label>
                        <div class="field-input-wrap">
                            <input type="password" name="confirm_password" id="pw4" class="field-input" placeholder="Re-enter new password" minlength="8" required <?php echo !$admin_id ? 'disabled' : ''; ?>>
                            <button type="button" class="field-eye" onclick="toggleEye('pw4', this)"><i class="fas fa-eye"></i></button>
                        </div>

                        <button type="submit" class="btn-save" <?php echo !$admin_id ? 'disabled' : ''; ?>>
                            <i class="fas fa-save"></i> Update Password
                        </button>
                    </form>
                </div>

            </div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleEye(inputId, btn) {
            const input = document.getElementById(inputId);
            const icon = btn.querySelector('i');
            if (input.type === 'password') {
                input.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        }

        // Auto-hide alerts
        setTimeout(() => {
            document.querySelectorAll('.alert-box').forEach(el => {
                el.style.transition = '0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 4000);
    </script>
</body>
</html>
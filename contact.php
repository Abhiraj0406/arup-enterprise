<?php
// ============================================================
// contact.php — Arup Enterprise (COMPLETE FIXED)
// ============================================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- CSRF token ----
if (empty($_SESSION['contact_csrf_token'])) {
    $_SESSION['contact_csrf_token'] = bin2hex(random_bytes(32));
}
ob_start();

require_once "includes/db.php";

// ============================================================
// ✅ FORCE ADD THE MISSING COLUMN
// ============================================================
if (isset($conn) && !$conn->connect_error) {
    $checkColumn = $conn->query("SHOW COLUMNS FROM contact_messages LIKE 'ip_address'");
    if (!$checkColumn || $checkColumn->num_rows === 0) {
        $conn->query("ALTER TABLE contact_messages ADD COLUMN ip_address VARCHAR(50) DEFAULT NULL AFTER status");
    }
    
    $conn->query("CREATE TABLE IF NOT EXISTS contact_messages (
        id         INT AUTO_INCREMENT PRIMARY KEY,
        name       VARCHAR(150) NOT NULL,
        phone      VARCHAR(30)  NOT NULL,
        email      VARCHAR(150) NOT NULL,
        subject    VARCHAR(200) DEFAULT 'General Inquiry',
        message    TEXT         NOT NULL,
        status     VARCHAR(20)  DEFAULT 'new',
        ip_address VARCHAR(50)  DEFAULT NULL,
        created_at TIMESTAMP    DEFAULT CURRENT_TIMESTAMP,
        INDEX idx_status  (status),
        INDEX idx_created (created_at)
    )");
}

$errors      = [];
$form_sent   = false;
$success_name = '';

// ============================================================
// ✅ CHECK FOR SUCCESS MESSAGE FROM SESSION (After redirect)
// ============================================================
if (isset($_SESSION['contact_success'])) {
    $form_sent = true;
    $success_name = $_SESSION['contact_success'];
    unset($_SESSION['contact_success']);
}

// ============================================================
// FORM PROCESSING
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ---- CSRF validation ----
    if (
        empty($_POST['csrf_token']) ||
        !hash_equals($_SESSION['contact_csrf_token'] ?? '', $_POST['csrf_token'])
    ) {
        $errors[] = "Invalid request. Please reload the page and try again.";
    }

    $fullname_raw = trim($_POST['fullname'] ?? '');
    $phone_raw    = trim($_POST['phone']    ?? '');
    $email_raw    = trim($_POST['email']    ?? '');
    $subject_raw  = trim($_POST['subject']  ?? 'General Inquiry');
    $message_raw  = trim($_POST['message']  ?? '');

    if (empty($fullname_raw))                                      $errors[] = "Full name is required.";
    if (empty($phone_raw))                                         $errors[] = "Phone number is required.";
    if (empty($email_raw) || !filter_var($email_raw, FILTER_VALIDATE_EMAIL))
                                                                   $errors[] = "Valid email address is required.";
    if (empty($message_raw))                                       $errors[] = "Message is required.";
    if (strlen($message_raw) > 1000)                               $errors[] = "Message exceeds 1000 characters.";

    if (empty($errors)) {
        $fullname = htmlspecialchars($fullname_raw, ENT_QUOTES, 'UTF-8');
        $phone    = htmlspecialchars($phone_raw,    ENT_QUOTES, 'UTF-8');
        $email    = htmlspecialchars($email_raw,    ENT_QUOTES, 'UTF-8');
        $subject  = htmlspecialchars($subject_raw,  ENT_QUOTES, 'UTF-8');
        $message  = htmlspecialchars($message_raw,  ENT_QUOTES, 'UTF-8');

        if (isset($conn) && !$conn->connect_error) {

            $ip  = $_SERVER['REMOTE_ADDR'] ?? '';
            
            $stmt = $conn->prepare(
                "INSERT INTO contact_messages
                 (name, phone, email, subject, message, status, ip_address, created_at)
                 VALUES (?, ?, ?, ?, ?, 'new', ?, NOW())"
            );

            if ($stmt) {
                $stmt->bind_param("ssssss", $fullname, $phone, $email, $subject, $message, $ip);

                if ($stmt->execute()) {

                    // ============================================================
                    // ✅ EMAIL NOTIFICATION — sent to both the business inbox and
                    // operations@ai-digitalsolution.com, as one nicely formatted
                    // HTML email. This is the ONLY part of the file that changed —
                    // database save, admin panel, and all page CSS/JS are untouched.
                    // ============================================================
                    $to_recipients = [
                        $site_settings['email'] ?? env('MAIL_NOTIFY_TO', 'enterprisearup@gmail.com'),
                    ];
                    $to = implode(", ", $to_recipients);

                    $mail_subject = "New Contact Enquiry — " . $subject_raw . " (" . $fullname_raw . ")";

                    $submitted_at = date('d M Y');
                    $ip_display   = htmlspecialchars($ip ?: 'Unknown');

                    // Build a clean, table-based HTML email (table layout = max compatibility
                    // across Gmail/Outlook/etc, no external CSS files so nothing can conflict
                    // with the website's own stylesheet).
                    $mail_body_html = '
                    <!DOCTYPE html>
                    <html>
                    <head><meta charset="UTF-8"></head>
                    <body style="margin:0;padding:0;background:#F5EED8;font-family:Arial,Helvetica,sans-serif;">
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5EED8;padding:32px 16px;">
                            <tr>
                                <td align="center">
                                    <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#FFFFFF;border-radius:12px;overflow:hidden;border:1px solid rgba(239,68,68,0.25);max-width:600px;">

                                        <!-- Header -->
                                        <tr>
                                            <td style="background:#1a1410;padding:24px 32px;border-bottom:3px solid #C9920A;">
                                                <span style="color:#C9920A;font-size:12px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">' . htmlspecialchars($site_settings['company_name'] ?? 'Arup Enterprise') . '</span>
                                                <h1 style="color:#FFFFFF;font-size:20px;margin:6px 0 0;font-family:Arial,Helvetica,sans-serif;">New Contact Enquiry</h1>
                                            </td>
                                        </tr>

                                        <!-- Intro -->
                                        <tr>
                                            <td style="padding:24px 32px 0;">
                                                <p style="margin:0;color:#4a3f37;font-size:14px;line-height:1.6;">
                                                    A new message was submitted through the website contact form on <strong>' . $submitted_at . '</strong>.
                                                </p>
                                            </td>
                                        </tr>

                                        <!-- Details table -->
                                        <tr>
                                            <td style="padding:20px 32px;">
                                                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                    <tr>
                                                        <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;width:130px;vertical-align:top;">Full Name</td>
                                                        <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#1e1e1e;font-size:14px;vertical-align:top;">' . $fullname . '</td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;">Phone</td>
                                                        <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#1e1e1e;font-size:14px;vertical-align:top;"><a href="tel:' . $phone . '" style="color:#1e1e1e;text-decoration:none;">' . $phone . '</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;">Email</td>
                                                        <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#1e1e1e;font-size:14px;vertical-align:top;"><a href="mailto:' . $email . '" style="color:#1e1e1e;text-decoration:none;">' . $email . '</a></td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;">Subject</td>
                                                        <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;vertical-align:top;">
                                                            <span style="display:inline-block;background:rgba(239,68,68,0.12);color:#8B6508;font-size:12px;font-weight:bold;padding:4px 12px;border-radius:20px;">' . $subject . '</span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td style="padding:10px 0;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;">IP Address</td>
                                                        <td style="padding:10px 0;color:#999999;font-size:12px;vertical-align:top;">' . $ip_display . '</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>

                                        <!-- Message -->
                                        <tr>
                                            <td style="padding:0 32px 28px;">
                                                <div style="background:#F5EED8;border-left:4px solid #C9920A;border-radius:8px;padding:16px 18px;">
                                                    <p style="margin:0 0 8px;color:#8B6508;font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:0.6px;">Message</p>
                                                    <p style="margin:0;color:#1e1e1e;font-size:14px;line-height:1.7;white-space:pre-wrap;">' . nl2br($message) . '</p>
                                                </div>
                                            </td>
                                        </tr>

                                        <!-- CTA -->
                                        <tr>
                                            <td style="padding:0 32px 32px;">
                                                <a href="mailto:' . $email . '?subject=Re:%20' . rawurlencode($subject_raw) . '" style="display:inline-block;background:linear-gradient(135deg,#C9920A,#8B6508);background-color:#C9920A;color:#FFFFFF;text-decoration:none;font-size:14px;font-weight:bold;padding:12px 26px;border-radius:8px;">Reply to ' . $fullname . '</a>
                                            </td>
                                        </tr>

                                        <!-- Footer -->
                                        <tr>
                                            <td style="background:#FAF6EE;padding:16px 32px;border-top:1px solid #f0e6d2;">
                                                <p style="margin:0;color:#999999;font-size:11px;line-height:1.6;">
                                                    This enquiry was also saved to the admin dashboard. Sent automatically from the Arup Enterprise website contact form — please do not reply directly to this notification address.
                                                </p>
                                            </td>
                                        </tr>

                                    </table>
                                </td>
                            </tr>
                        </table>
                    </body>
                    </html>';

                    // Plain-text fallback (for mail clients that block/strip HTML)
                    $mail_body_text = "New Contact Enquiry\n\n"
                        . "Name:    $fullname_raw\n"
                        . "Phone:   $phone_raw\n"
                        . "Email:   $email_raw\n"
                        . "Subject: $subject_raw\n"
                        . "Date:    $submitted_at\n"
                        . "IP:      " . ($ip ?: 'Unknown') . "\n\n"
                        . "Message:\n$message_raw\n";

                    $boundary = md5(uniqid((string)time(), true));

                    $mail_from_addr = env('MAIL_FROM_ADDRESS', 'noreply@arup-enterprise.com');
                    $mail_from_name = env('MAIL_FROM_NAME', 'Arup Enterprise Website');
                    $headers  = "From: {$mail_from_name} <{$mail_from_addr}>\r\n";

                    $headers .= "Reply-To: $fullname_raw <$email_raw>\r\n";
                    $headers .= "MIME-Version: 1.0\r\n";
                    $headers .= "Content-Type: multipart/alternative; boundary=\"$boundary\"\r\n";

                    $mail_payload  = "--$boundary\r\n";
                    $mail_payload .= "Content-Type: text/plain; charset=UTF-8\r\n";
                    $mail_payload .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                    $mail_payload .= $mail_body_text . "\r\n\r\n";
                    $mail_payload .= "--$boundary\r\n";
                    $mail_payload .= "Content-Type: text/html; charset=UTF-8\r\n";
                    $mail_payload .= "Content-Transfer-Encoding: 7bit\r\n\r\n";
                    $mail_payload .= $mail_body_html . "\r\n\r\n";
                    $mail_payload .= "--$boundary--";

                    mail($to, $mail_subject, $mail_payload, $headers);

                    $_SESSION['contact_success'] = $fullname_raw;
                    $stmt->close();

                    // PRG: Redirect to prevent resubmit on refresh
                    ob_end_clean();
                    header("Location: contact.php?sent=1#contact-form");
                    exit();

                } else {
                    $errors[] = "Could not save your message. Please try again or call us directly.";
                    $stmt->close();
                }
            } else {
                $errors[] = "Database error: " . htmlspecialchars($conn->error);
            }

        } else {
            $errors[] = "Database connection unavailable. Please call us or use WhatsApp.";
        }
    }
}

$page_title = "Contact Us";
include 'includes/header.php';
?>

<!-- Google Fonts -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- ═══════════════════════════════════════════════════════════
     CONTACT HERO
════════════════════════════════════════════════════════════ -->
<section class="ct-hero">
  <canvas class="ct-sparks" id="ctSparks" aria-hidden="true"></canvas>
  <div class="ct-grid-overlay" aria-hidden="true"></div>

  <div class="ct-hero-machine" aria-hidden="true">
    <svg viewBox="0 0 580 440" xmlns="http://www.w3.org/2000/svg" class="ct-machine-svg">
      <defs>
        <linearGradient id="wbody" x1="0%" y1="0%" x2="100%" y2="100%">
          <stop offset="0%" stop-color="#2e2b24"/><stop offset="100%" stop-color="#1a1714"/>
        </linearGradient>
        <linearGradient id="wtop" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="#3a3530"/><stop offset="100%" stop-color="#2a2520"/>
        </linearGradient>
        <linearGradient id="woodGrain" x1="0%" y1="0%" x2="0%" y2="100%">
          <stop offset="0%" stop-color="#8B5E2A"/><stop offset="30%" stop-color="#A06B30"/>
          <stop offset="60%" stop-color="#8B5E2A"/><stop offset="100%" stop-color="#7A5025"/>
        </linearGradient>
        <radialGradient id="crtGlow" cx="50%" cy="50%" r="50%">
          <stop offset="0%" stop-color="#EF4444" stop-opacity="0.3"/>
          <stop offset="100%" stop-color="#EF4444" stop-opacity="0"/>
        </radialGradient>
        <filter id="glow4"><feGaussianBlur stdDeviation="3" result="b"/>
          <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
        <filter id="softglow"><feGaussianBlur stdDeviation="6" result="b"/>
          <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
      </defs>

      <rect x="40" y="170" width="260" height="200" rx="10" fill="url(#wbody)" stroke="#EF4444" stroke-width="1"/>
      <rect x="52" y="182" width="108" height="76" rx="5" fill="rgba(255,255,255,0.03)" stroke="#EF4444" stroke-width="0.6" stroke-dasharray="4 2" opacity="0.5"/>
      <rect x="172" y="182" width="108" height="76" rx="5" fill="rgba(255,255,255,0.03)" stroke="#EF4444" stroke-width="0.6" stroke-dasharray="4 2" opacity="0.5"/>
      <rect x="60" y="272" width="80" height="3" rx="1" fill="#EF4444" opacity="0.25"/>
      <rect x="60" y="278" width="80" height="3" rx="1" fill="#EF4444" opacity="0.2"/>
      <rect x="196" y="272" width="80" height="3" rx="1" fill="#EF4444" opacity="0.25"/>
      <rect x="196" y="278" width="80" height="3" rx="1" fill="#EF4444" opacity="0.2"/>
      <rect x="30" y="158" width="280" height="20" rx="4" fill="url(#wtop)" stroke="#EF4444" stroke-width="1"/>
      <line x1="60" y1="162" x2="60" y2="174" stroke="#EF4444" stroke-width="0.5" opacity="0.4"/>
      <line x1="90" y1="162" x2="90" y2="174" stroke="#EF4444" stroke-width="0.5" opacity="0.4"/>
      <line x1="120" y1="162" x2="120" y2="174" stroke="#EF4444" stroke-width="0.5" opacity="0.4"/>
      <line x1="150" y1="162" x2="150" y2="174" stroke="#EF4444" stroke-width="0.5" opacity="0.4"/>
      <line x1="180" y1="162" x2="180" y2="174" stroke="#EF4444" stroke-width="0.5" opacity="0.4"/>
      <line x1="210" y1="162" x2="210" y2="174" stroke="#EF4444" stroke-width="0.5" opacity="0.4"/>
      <line x1="240" y1="162" x2="240" y2="174" stroke="#EF4444" stroke-width="0.5" opacity="0.4"/>
      <rect x="30" y="130" width="480" height="30" rx="3" fill="url(#woodGrain)" opacity="0.85"/>
      <line x1="30" y1="136" x2="510" y2="136" stroke="#6B3E1A" stroke-width="0.5" opacity="0.4"/>
      <line x1="30" y1="141" x2="510" y2="141" stroke="#9B6830" stroke-width="0.8" opacity="0.3"/>
      <line x1="30" y1="146" x2="510" y2="146" stroke="#6B3E1A" stroke-width="0.5" opacity="0.4"/>
      <line x1="30" y1="151" x2="510" y2="151" stroke="#9B6830" stroke-width="0.6" opacity="0.3"/>
      <ellipse cx="200" cy="145" rx="12" ry="7" fill="none" stroke="#5A3010" stroke-width="0.8" opacity="0.5"/>
      <circle cx="170" cy="148" r="38" fill="none" stroke="#EF4444" stroke-width="1.4" stroke-dasharray="5 3" filter="url(#glow4)" opacity="0.85"/>
      <circle cx="170" cy="148" r="28" fill="none" stroke="#EF4444" stroke-width="0.8" stroke-dasharray="3 4" opacity="0.5"/>
      <circle cx="170" cy="148" r="16" fill="rgba(239,68,68,0.08)" stroke="#EF4444" stroke-width="1" opacity="0.7"/>
      <circle cx="170" cy="148" r="5" fill="#EF4444" opacity="0.8"/>
      <circle cx="170" cy="110" r="3" fill="#EF4444" opacity="0.6"/>
      <circle cx="197" cy="121" r="3" fill="#EF4444" opacity="0.6"/>
      <circle cx="206" cy="150" r="3" fill="#EF4444" opacity="0.6"/>
      <circle cx="197" cy="177" r="3" fill="#EF4444" opacity="0.6"/>
      <circle cx="143" cy="121" r="3" fill="#EF4444" opacity="0.6"/>
      <line x1="170" y1="125" x2="170" y2="175" stroke="#E8B84B" stroke-width="2" opacity="0.9" filter="url(#softglow)" class="ct-cut-line"/>
      <circle cx="170" cy="130" r="1.5" fill="#E8B84B" opacity="0.9"/>
      <circle cx="165" cy="127" r="1" fill="#EF4444" opacity="0.7"/>
      <circle cx="175" cy="128" r="1" fill="#EF4444" opacity="0.7"/>
      <rect x="30" y="108" width="480" height="8" rx="3" fill="url(#wtop)" stroke="#EF4444" stroke-width="0.8" opacity="0.9"/>
      <rect x="30" y="160" width="480" height="5" rx="2" fill="#EF4444" opacity="0.4" filter="url(#glow4)"/>
      <rect x="30" y="60" width="480" height="14" rx="4" fill="url(#wtop)" stroke="#EF4444" stroke-width="1"/>
      <rect x="30" y="60" width="14" height="110" rx="3" fill="url(#wbody)" stroke="#EF4444" stroke-width="0.8"/>
      <rect x="496" y="60" width="14" height="110" rx="3" fill="url(#wbody)" stroke="#EF4444" stroke-width="0.8"/>
      <rect x="320" y="165" width="50" height="70" rx="6" fill="url(#wbody)" stroke="#EF4444" stroke-width="1"/>
      <rect x="327" y="172" width="36" height="28" rx="3" fill="#0d1a0d" stroke="#27ae60" stroke-width="0.8"/>
      <rect x="329" y="174" width="32" height="24" rx="2" fill="url(#crtGlow)" opacity="0.8"/>
      <text x="345" y="184" fill="#00ff88" font-size="5.5" text-anchor="middle" font-family="monospace" opacity="0.9">RPM 4200</text>
      <text x="345" y="191" fill="#00ff88" font-size="5" text-anchor="middle" font-family="monospace" opacity="0.9">FEED 18mm</text>
      <text x="345" y="197" fill="#00cc55" font-size="4.5" text-anchor="middle" font-family="monospace" opacity="0.8">● RUNNING</text>
      <circle cx="333" cy="208" r="4" fill="rgba(39,174,96,0.3)" stroke="#27ae60" stroke-width="0.8"/>
      <circle cx="345" cy="208" r="4" fill="rgba(239,68,68,0.3)" stroke="#EF4444" stroke-width="0.8"/>
      <circle cx="357" cy="208" r="4" fill="rgba(231,76,60,0.3)" stroke="#e74c3c" stroke-width="0.8"/>
      <circle cx="345" cy="222" r="7" fill="rgba(231,76,60,0.15)" stroke="#e74c3c" stroke-width="1.2"/>
      <text x="345" y="225" fill="#e74c3c" font-size="4" text-anchor="middle" font-family="Inter,sans-serif" font-weight="700">STOP</text>
      <path d="M 310 200 Q 290 220 270 210 Q 250 200 240 220" fill="none" stroke="#EF4444" stroke-width="4" stroke-linecap="round" opacity="0.4"/>
      <rect x="400" y="180" width="140" height="170" rx="8" fill="url(#wbody)" stroke="#EF4444" stroke-width="0.9" opacity="0.9"/>
      <rect x="395" y="172" width="150" height="16" rx="4" fill="url(#wtop)" stroke="#EF4444" stroke-width="0.8" opacity="0.8"/>
      <ellipse cx="430" cy="172" rx="18" ry="8" fill="rgba(239,68,68,0.12)" stroke="#EF4444" stroke-width="1" opacity="0.7"/>
      <ellipse cx="520" cy="220" rx="22" ry="22" fill="none" stroke="#EF4444" stroke-width="1.2" stroke-dasharray="5 3" opacity="0.6"/>
      <ellipse cx="520" cy="220" rx="14" ry="14" fill="rgba(239,68,68,0.07)" stroke="#EF4444" stroke-width="0.8" opacity="0.6"/>
      <ellipse cx="520" cy="220" rx="5" ry="5" fill="#EF4444" opacity="0.5"/>
      <line x1="395" y1="238" x2="540" y2="238" stroke="#EF4444" stroke-width="1.8" opacity="0.7" filter="url(#glow4)"/>
      <rect x="405" y="230" width="12" height="16" rx="3" fill="rgba(239,68,68,0.12)" stroke="#EF4444" stroke-width="0.8" opacity="0.7"/>
      <rect x="425" y="230" width="12" height="16" rx="3" fill="rgba(239,68,68,0.12)" stroke="#EF4444" stroke-width="0.8" opacity="0.7"/>
      <rect x="408" y="254" width="86" height="70" rx="5" fill="#111" stroke="#EF4444" stroke-width="0.6" opacity="0.8"/>
      <rect x="414" y="259" width="50" height="32" rx="3" fill="#0d1a0d" stroke="#27ae60" stroke-width="0.7"/>
      <text x="439" y="270" fill="#00ff88" font-size="5" text-anchor="middle" font-family="monospace" opacity="0.85">GLUE TEMP</text>
      <text x="439" y="278" fill="#E8B84B" font-size="7" text-anchor="middle" font-family="monospace" font-weight="700" opacity="0.9">185°C</text>
      <text x="439" y="287" fill="#00cc55" font-size="4" text-anchor="middle" font-family="monospace" opacity="0.7">● READY</text>
      <rect x="52" y="368" width="20" height="8" rx="2" fill="#111" stroke="#EF4444" stroke-width="0.5"/>
      <rect x="268" y="368" width="20" height="8" rx="2" fill="#111" stroke="#EF4444" stroke-width="0.5"/>
      <rect x="410" y="348" width="18" height="8" rx="2" fill="#111" stroke="#EF4444" stroke-width="0.5"/>
      <rect x="468" y="348" width="18" height="8" rx="2" fill="#111" stroke="#EF4444" stroke-width="0.5"/>
      <line x1="20" y1="376" x2="560" y2="376" stroke="#EF4444" stroke-width="0.8" opacity="0.25"/>
      <line x1="40" y1="45" x2="510" y2="45" stroke="#EF4444" stroke-width="0.6" opacity="0.3"/>
      <line x1="40" y1="41" x2="40" y2="49" stroke="#EF4444" stroke-width="0.6" opacity="0.3"/>
      <line x1="510" y1="41" x2="510" y2="49" stroke="#EF4444" stroke-width="0.6" opacity="0.3"/>
      <text x="275" y="43" fill="#EF4444" font-size="8" text-anchor="middle" font-family="Inter,sans-serif" opacity="0.45" letter-spacing="1">3600mm PANEL WORKING WIDTH</text>
      <rect x="0" y="393" width="580" height="22" fill="rgba(239,68,68,0.05)"/>
      <text x="290" y="407" fill="#EF4444" font-size="8" text-anchor="middle" font-family="Inter,sans-serif" font-weight="700" letter-spacing="3.5" opacity="0.55">DIPBAN PANEL SAW + EDGE BANDING LINE  ·  HOWRAH</text>
    </svg>
  </div>

  <div class="ct-hero-content container">
    <nav class="ct-breadcrumb">
      <a href="index.php">Home</a>
      <svg width="10" height="10" viewBox="0 0 10 10"><path d="M3 2l4 3-4 3" stroke="currentColor" stroke-width="1.4" fill="none"/></svg>
      <span>Contact Us</span>
    </nav>
    <div class="ct-hero-badge">
      <span class="ct-badge-dot"></span>
      Team Online · Avg Response &lt; 2 Hours
    </div>
    <h1 class="ct-hero-title">
      <span class="ct-tl1">Let's</span>
      <span class="ct-tl2">Connect</span>
    </h1>
    <p class="ct-hero-sub">Have a question about our machinery? Need a custom quote or on-site demo? Our engineers are standing by — reach out through any channel below.</p>
    <div class="ct-hero-actions">
      <a href="#contact-form" class="ct-btn-primary"><i class="fas fa-paper-plane"></i> Send a Message</a>
      <a href="tel:+918013635806" class="ct-btn-secondary"><i class="fas fa-phone-alt"></i> Call Now</a>
    </div>
    <div class="ct-hero-trust">
      <span><i class="fas fa-check-circle"></i> 24/7 Support</span>
      <span><i class="fas fa-check-circle"></i> Reply within 24hrs</span>
      <span><i class="fas fa-check-circle"></i> Pan-India Service</span>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════════════════════
     FORM + INFO
════════════════════════════════════════════════════════════ -->
<section class="ct-main" id="contact-form">
  <div class="container">
    <div class="ct-grid">

      <!-- FORM PANEL -->
      <div class="ct-form-panel">
        <div class="ct-form-top">
          <div>
            <h2 class="ct-form-title">Send Us a Message</h2>
            <p class="ct-form-sub">Fill in the details below — we'll respond within 24 hours.</p>
          </div>
          <div class="ct-online-badge">
            <span class="ct-online-dot"></span> Team Online
          </div>
        </div>

        <!-- ✅ SUCCESS MESSAGE -->
        <?php if ($form_sent && !empty($success_name)): ?>
        <div class="ct-success-card" id="ctSuccessCard">
          <div class="ct-success-icon">
            <svg width="28" height="28" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="white" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
          </div>
          <div class="ct-success-text">
            <strong>Message Sent, <?php echo htmlspecialchars($success_name); ?>!</strong>
            <span>Your enquiry has been received. Our team will contact you within 24 hours. You can also reach us on WhatsApp for a faster response.</span>
          </div>
          <a href="https://wa.me/918013635806" target="_blank" class="ct-success-wa">
            <i class="fab fa-whatsapp"></i> Chat Now
          </a>
        </div>
        <?php endif; ?>

        <?php if (!empty($errors)): ?>
        <div class="ct-error-card">
          <i class="fas fa-exclamation-triangle"></i>
          <div><?php foreach($errors as $err): ?><div><?php echo htmlspecialchars($err); ?></div><?php endforeach; ?></div>
        </div>
        <?php endif; ?>

        <!-- ✅ FORM -->
        <form class="ct-form" method="POST" action="" id="ctForm">
          <input type="hidden" name="_form_submitted" value="1">
          <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['contact_csrf_token'] ?? ''); ?>">

          <div class="ct-form-row">
            <div class="ct-field">
              <label for="ct_fullname">Full Name <span>*</span></label>
              <input type="text" id="ct_fullname" name="fullname" placeholder="Ramesh Kumar"
                value="<?php echo isset($_POST['fullname']) && !$form_sent ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
            </div>
            <div class="ct-field">
              <label for="ct_phone">Phone Number <span>*</span></label>
              <input type="tel" id="ct_phone" name="phone" placeholder="+91 98765 43210"
                value="<?php echo isset($_POST['phone']) && !$form_sent ? htmlspecialchars($_POST['phone']) : ''; ?>" required>
            </div>
          </div>

          <div class="ct-form-row">
            <div class="ct-field">
              <label for="ct_email">Email Address <span>*</span></label>
              <input type="email" id="ct_email" name="email" placeholder="you@company.com"
                value="<?php echo isset($_POST['email']) && !$form_sent ? htmlspecialchars($_POST['email']) : ''; ?>" required>
            </div>
            <div class="ct-field">
              <label for="ct_subject">Subject</label>
              <div class="ct-select-wrap">
                <select id="ct_subject" name="subject">
                  <option value="General Inquiry">General Inquiry</option>
                  <option value="Quote Request">Quote Request</option>
                  <option value="Product Inquiry">Product Inquiry</option>
                  <option value="Service & Support">Service &amp; Support</option>
                  <option value="Spare Parts">Spare Parts</option>
                  <option value="Feedback">Feedback</option>
                </select>
                <svg class="ct-select-arrow" width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
              </div>
            </div>
          </div>

          <div class="ct-field">
            <label for="ct_msgBox">Your Message <span>*</span></label>
            <textarea id="ct_msgBox" name="message" rows="5"
              placeholder="Tell us about your machinery requirements, production volume, floor space, or any questions..."
              maxlength="1000" required><?php echo isset($_POST['message']) && !$form_sent ? htmlspecialchars($_POST['message']) : ''; ?></textarea>
            <div class="ct-char-count"><span id="charNum">0</span> / 1000 characters</div>
          </div>

          <div class="ct-agree">
            <input type="checkbox" id="ct_agree" name="agree" required>
            <label for="ct_agree">I agree to the <a href="#">Privacy Policy</a> and consent to being contacted by Arup Enterprise's team.</label>
          </div>

          <button type="submit" class="ct-submit" id="ctSubmitBtn">
            <i class="fas fa-paper-plane"></i>
            <span id="ctBtnLabel">Send Message</span>
          </button>
          <p class="ct-form-note"><i class="fas fa-lock"></i> Your information is private and never shared.</p>
        </form>
      </div>

      <!-- INFO COLUMN -->
      <div class="ct-info-col">
        <h2 class="ct-info-title">Reach Out Directly</h2>
        <p class="ct-info-sub">Multiple ways to connect — pick what's most convenient for you.</p>

        <a href="https://wa.me/918013635806" target="_blank" class="ct-wa-card">
          <div class="ct-wa-icon"><i class="fab fa-whatsapp"></i></div>
          <div class="ct-wa-text">
            <span>Fastest Response</span>
            <strong>Chat on WhatsApp</strong>
          </div>
          <div class="ct-wa-arrow">
            <svg width="20" height="20" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </div>
        </a>

        <div class="ct-info-items">
          <div class="ct-info-item">
            <div class="ct-info-ico"><i class="fas fa-map-marker-alt"></i></div>
            <div class="ct-info-body">
              <strong>Office Address</strong>
              <span>Arup Enterprise<br>Liluah, Howrah, West Bengal</span>
            </div>
          </div>
          <div class="ct-info-item">
            <div class="ct-info-ico"><i class="fas fa-phone-alt"></i></div>
            <div class="ct-info-body">
              <strong>Phone Numbers</strong>
              <span><a href="tel:+918013635806">+91 99031 26940</a></span>
              <span><a href="tel:+91 8839019950">+91 8839019950</a></span>
            </div>
          </div>
          <div class="ct-info-item">
            <div class="ct-info-ico"><i class="fas fa-envelope"></i></div>
            <div class="ct-info-body">
              <strong>Email Address</strong>
              <span><a href="mailto:enterprisearup@gmail.com">enterprisearup@gmail.com</a></span>
            </div>
          </div>
          <div class="ct-info-item">
            <div class="ct-info-ico"><i class="fas fa-clock"></i></div>
            <div class="ct-info-body">
              <strong>Business Hours</strong>
              <span>Mon – Sat: 10:30 AM – 6:00 PM</span>
              <span>Sunday: Closed</span>
            </div>
          </div>
        </div>

        <div class="ct-social">
          <p class="ct-social-label">Follow Us</p>
          <div class="ct-social-row">
            <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
            <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
          </div>
        </div>

        <a href= "assets/folder/Dipban-Technical-Services-Brochure.pdf" download class="ct-pdf">
          <i class="fas fa-file-pdf ct-pdf-icon"></i>
          <div><span>Download Our</span><strong>Product Catalogue (PDF)</strong></div>
          <i class="fas fa-arrow-down ct-pdf-arrow"></i>
        </a>
      </div>

    </div>
  </div>
</section>

<!-- MAP -->
<section class="ct-map-section">
  <div class="container">
    <div class="ct-map-wrap">
      <svg viewBox="0 0 1200 380" xmlns="http://www.w3.org/2000/svg" style="width:100%;display:block;">
        <defs>
          <linearGradient id="mapbg" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" stop-color="#f5ede1"/><stop offset="100%" stop-color="#ede0cc"/>
          </linearGradient>
        </defs>
        <rect width="1200" height="380" fill="url(#mapbg)" rx="16"/>
        <line x1="0" y1="190" x2="1200" y2="190" stroke="#d4c5b2" stroke-width="7"/>
        <line x1="400" y1="0" x2="400" y2="380" stroke="#d4c5b2" stroke-width="7"/>
        <line x1="800" y1="0" x2="800" y2="380" stroke="#d4c5b2" stroke-width="7"/>
        <line x1="0" y1="110" x2="1200" y2="110" stroke="#d4c5b2" stroke-width="4" opacity="0.6"/>
        <line x1="0" y1="270" x2="1200" y2="270" stroke="#d4c5b2" stroke-width="4" opacity="0.6"/>
        <line x1="200" y1="0" x2="200" y2="380" stroke="#d4c5b2" stroke-width="4" opacity="0.6"/>
        <line x1="600" y1="0" x2="600" y2="380" stroke="#d4c5b2" stroke-width="4" opacity="0.6"/>
        <line x1="1000" y1="0" x2="1000" y2="380" stroke="#d4c5b2" stroke-width="4" opacity="0.6"/>
        <rect x="30" y="25" width="100" height="65" rx="4" fill="#ddd0b8" stroke="#c9b89a" stroke-width="1.5"/>
        <rect x="145" y="25" width="75" height="65" rx="4" fill="#d9ccb4" stroke="#c9b89a" stroke-width="1.5"/>
        <rect x="430" y="25" width="120" height="65" rx="4" fill="#d9ccb4" stroke="#c9b89a" stroke-width="1.5"/>
        <rect x="830" y="25" width="95" height="65" rx="4" fill="#ddd0b8" stroke="#c9b89a" stroke-width="1.5"/>
        <rect x="1050" y="25" width="115" height="65" rx="4" fill="#ddd0b8" stroke="#c9b89a" stroke-width="1.5"/>
        <rect x="30" y="210" width="140" height="80" rx="4" fill="#d9ccb4" stroke="#c9b89a" stroke-width="1.5"/>
        <rect x="830" y="210" width="110" height="80" rx="4" fill="#ddd0b8" stroke="#c9b89a" stroke-width="1.5"/>
        <ellipse cx="320" cy="310" rx="55" ry="35" fill="#d4d9b0" opacity="0.5"/>
        <circle cx="240" cy="190" r="90" fill="#C9920A" opacity="0.04"/>
        <circle cx="240" cy="190" r="60" fill="#C9920A" opacity="0.05"/>
        <circle cx="240" cy="190" r="32" fill="#C9920A" opacity="0.08"/>
        <circle cx="240" cy="172" r="22" fill="#C9920A" stroke="white" stroke-width="4"/>
        <circle cx="240" cy="172" r="9" fill="white"/>
        <path d="M240 194 L226 218 L240 210 L254 218 Z" fill="#C9920A" stroke="white" stroke-width="2"/>
        <rect x="272" y="155" width="230" height="56" rx="8" fill="#1a1a1a" opacity="0.9"/>
        <text x="387" y="178" fill="#C9920A" font-size="13" text-anchor="middle" font-family="Inter,sans-serif" font-weight="800"><?php echo strtoupper(htmlspecialchars($site_settings['company_name'] ?? 'Arup Enterprise')); ?></text>
        <text x="387" y="197" fill="#aaa" font-size="9.5" text-anchor="middle" font-family="Inter,sans-serif"><?php echo htmlspecialchars(substr($site_settings['address'] ?? 'West Bengal', 0, 40)); ?></text>
        <text x="1190" y="368" fill="#c9b89a" font-size="9" text-anchor="end" font-family="Inter,sans-serif">Virtual Map · <?php echo htmlspecialchars($site_settings['company_name'] ?? 'Arup Enterprise'); ?></text>
      </svg>
      <a href="https://maps.google.com/?q=Liluah+Howrah+West+Bengal" target="_blank" class="ct-map-btn">
        <i class="fas fa-map-marked-alt"></i> Get Directions
      </a>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="ct-faq">
  <div class="container">
    <div class="ct-sec-head">
      <span class="ct-eyebrow">Common Questions</span>
      <h2 class="ct-h2">Frequently Asked <em>Questions</em></h2>
    </div>
    <div class="ct-faq-grid">
      <?php
      $faqs = [];
      if (isset($conn)) {
          $res = $conn->query("SELECT * FROM faqs WHERE status='active' ORDER BY sort_order ASC, id ASC");
          if ($res) {
              while ($row = $res->fetch_assoc()) {
                  $faqs[] = ['q' => $row['question'], 'a' => $row['answer']];
              }
          }
      }
      if (empty($faqs)) {
          $faqs = [
              ['q'=>'What types of machinery do you specialize in?', 'a'=>'We specialize in premium magnetic separation equipment.'],
              ['q'=>'Do you provide installation and training?', 'a'=>'Yes! Our team provides complete installation, commissioning, and on-site training for all machinery we supply.'],
              ['q'=>'How can I request a quote?', 'a'=>'Fill out the contact form on this page or call us directly.']
          ];
      }
      foreach($faqs as $f): ?>
      <div class="ct-faq-item">
        <button class="ct-faq-q" type="button">
          <span><?php echo htmlspecialchars($f['q']); ?></span>
          <svg class="ct-faq-icon" width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </button>
        <div class="ct-faq-a"><p><?php echo htmlspecialchars($f['a']); ?></p></div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA BAND -->
<section class="ct-cta">
  <div class="container">
    <div class="ct-cta-inner">
      <div>
        <span class="ct-eyebrow" style="color:#E8B84B">Need Immediate Help?</span>
        <h2 class="ct-cta-title">Our Team is Ready Right Now</h2>
        <p>Expert advice, quotes, and technical support — one call away.</p>
      </div>
      <div class="ct-cta-btns">
        <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $site_settings['phone'] ?? '')); ?>" class="ct-btn-primary"><i class="fas fa-phone"></i> Call Now</a>
        <a href="mailto:<?php echo htmlspecialchars($site_settings['email'] ?? ''); ?>" class="ct-btn-secondary"><i class="fas fa-envelope"></i> Email Us</a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<style>
:root {
  --gold:    #EF4444;
  --gold-lt: #F87171;
  --gold-dk: #DC2626;
  --char:    #0E0E0E;
  --cream:   #FFF5F5;
  --ivory:   #FEE2E2;
  --smoke:   #6B6560;
  --white:   #FFFFFF;
  --border:  rgba(239,68,68,0.15);
  --font-d:  'Bebas Neue','Impact',sans-serif;
  --font-b:  'Inter',-apple-system,sans-serif;
  --ease:    cubic-bezier(0.4,0,0.2,1);
  --sg:      0 8px 32px rgba(239,68,68,0.22);
}
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
body{font-family:var(--font-b);}
a{text-decoration:none;color:inherit;}
.container{max-width:1240px;margin:0 auto;padding:0 24px;}

.ct-eyebrow{display:inline-block;font-size:.67rem;font-weight:700;letter-spacing:.22em;text-transform:uppercase;color:var(--gold);background:rgba(239,68,68,.09);border:1px solid rgba(239,68,68,.22);border-radius:100px;padding:4px 14px;margin-bottom:12px;}
.ct-sec-head{text-align:center;margin-bottom:52px;}
.ct-h2{font-family:var(--font-d);font-size:clamp(2rem,4vw,3rem);letter-spacing:.02em;color:var(--char);line-height:1.1;}
.ct-h2 em{color:var(--gold);font-style:normal;}

/* HERO */
.ct-hero{position:relative;min-height:100vh;background:var(--char);display:flex;align-items:center;overflow:hidden;border-bottom:2px solid var(--gold);}
.ct-sparks{position:absolute;inset:0;width:100%;height:100%;pointer-events:none;opacity:.65;}
.ct-grid-overlay{position:absolute;inset:0;background-image:linear-gradient(rgba(239,68,68,.035) 1px,transparent 1px),linear-gradient(90deg,rgba(239,68,68,.035) 1px,transparent 1px);background-size:54px 54px;pointer-events:none;}
.ct-hero-machine{position:absolute;right:-30px;top:50%;transform:translateY(-50%);width:clamp(360px,48vw,620px);opacity:.36;pointer-events:none;filter:drop-shadow(0 0 40px rgba(239,68,68,.14));}
.ct-machine-svg{width:100%;}
.ct-cut-line{animation:cutPulse 1.8s ease-in-out infinite;}
@keyframes cutPulse{0%,100%{opacity:.5;stroke-width:1.5;}50%{opacity:1;stroke-width:2.5;}}
.ct-hero-content{position:relative;z-index:2;padding:120px 24px 80px;max-width:620px;}
.ct-breadcrumb{display:flex;align-items:center;gap:8px;font-size:.78rem;color:#666;margin-bottom:24px;}
.ct-breadcrumb a{color:var(--gold);}
.ct-breadcrumb a:hover{color:var(--gold-lt);}
.ct-hero-badge{display:inline-flex;align-items:center;gap:8px;font-size:.72rem;font-weight:700;letter-spacing:.16em;text-transform:uppercase;color:var(--gold);border:1px solid rgba(239,68,68,.3);border-radius:100px;padding:6px 18px;margin-bottom:24px;background:rgba(239,68,68,.07);}
.ct-badge-dot{width:7px;height:7px;background:#27ae60;border-radius:50%;box-shadow:0 0 8px #27ae60;animation:dotPulse 2s ease-in-out infinite;}
@keyframes dotPulse{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.6;transform:scale(1.4);}}
.ct-hero-title{display:flex;flex-direction:column;margin-bottom:20px;}
.ct-tl1{font-family:var(--font-d);font-size:clamp(2.5rem,6vw,5rem);letter-spacing:.08em;color:rgba(255,255,255,.45);line-height:1;}
.ct-tl2{font-family:var(--font-d);font-size:clamp(5rem,14vw,10.5rem);letter-spacing:.01em;color:var(--gold);line-height:.9;text-shadow:0 0 80px rgba(239,68,68,.32);}
.ct-hero-sub{color:rgba(255,255,255,.56);font-size:1rem;line-height:1.72;max-width:480px;margin-bottom:36px;}
.ct-hero-actions{display:flex;gap:14px;flex-wrap:wrap;margin-bottom:28px;}
.ct-btn-primary{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--gold) 0%,var(--gold-dk) 100%);color:white;font-weight:700;font-size:.92rem;padding:13px 26px;border-radius:10px;box-shadow:var(--sg);transition:all .25s var(--ease);}
.ct-btn-primary:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(239,68,68,.4);}
.ct-btn-secondary{display:inline-flex;align-items:center;gap:8px;border:2px solid rgba(255,255,255,.3);color:rgba(255,255,255,.85);font-weight:600;font-size:.92rem;padding:13px 26px;border-radius:10px;transition:all .25s var(--ease);}
.ct-btn-secondary:hover{border-color:var(--gold);color:var(--gold-lt);}
.ct-hero-trust{display:flex;gap:20px;flex-wrap:wrap;}
.ct-hero-trust span{display:flex;align-items:center;gap:6px;font-size:.78rem;color:rgba(255,255,255,.45);}
.ct-hero-trust i{color:var(--gold);font-size:.72rem;}

/* MAIN */
.ct-main{padding:80px 0;background:var(--cream);}
.ct-grid{display:grid;grid-template-columns:1.1fr .9fr;gap:52px;align-items:start;}

/* Form panel */
.ct-form-panel{background:var(--white);border-radius:20px;padding:38px 40px;border:1px solid var(--border);box-shadow:0 16px 56px rgba(0,0,0,.07);}
.ct-form-top{display:flex;justify-content:space-between;align-items:flex-start;gap:16px;margin-bottom:28px;padding-bottom:24px;border-bottom:1px solid rgba(239,68,68,.1);}
.ct-form-title{font-family:var(--font-b);font-size:1.5rem;font-weight:900;color:var(--char);margin-bottom:4px;}
.ct-form-sub{font-size:.86rem;color:var(--smoke);}
.ct-online-badge{display:inline-flex;align-items:center;gap:7px;background:rgba(39,174,96,.09);border:1px solid rgba(39,174,96,.25);color:#1a7a44;font-size:.7rem;font-weight:700;padding:5px 12px;border-radius:100px;white-space:nowrap;flex-shrink:0;}
.ct-online-dot{width:7px;height:7px;background:#27ae60;border-radius:50%;animation:dotPulse 2s ease-in-out infinite;}

/* Success */
.ct-success-card{display:flex;align-items:center;gap:16px;background:linear-gradient(135deg,rgba(39,174,96,.08),rgba(39,174,96,.04));border:1px solid rgba(39,174,96,.25);border-left:4px solid #27ae60;border-radius:14px;padding:18px 20px;margin-bottom:24px;animation:slideInDown .5s var(--ease);flex-wrap:wrap;}
@keyframes slideInDown{from{opacity:0;transform:translateY(-16px);}to{opacity:1;transform:translateY(0);}}
.ct-success-icon{width:44px;height:44px;background:linear-gradient(135deg,#27ae60,#1a7a44);border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;box-shadow:0 4px 14px rgba(39,174,96,.35);}
.ct-success-text{flex:1;}
.ct-success-text strong{display:block;font-size:1rem;font-weight:800;color:#1a4a2a;margin-bottom:4px;}
.ct-success-text span{font-size:.84rem;color:var(--smoke);line-height:1.6;}
.ct-success-wa{display:inline-flex;align-items:center;gap:7px;background:#25D366;color:white;font-weight:700;font-size:.8rem;padding:8px 16px;border-radius:8px;flex-shrink:0;transition:all .25s;}
.ct-success-wa:hover{background:#1ea855;transform:translateY(-1px);}

/* Error */
.ct-error-card{display:flex;gap:12px;background:rgba(185,28,46,.06);border:1px solid rgba(185,28,46,.2);border-left:4px solid #b91c2e;border-radius:12px;padding:14px 18px;margin-bottom:20px;color:#8b1020;font-size:.86rem;}
.ct-error-card i{color:#b91c2e;font-size:1rem;flex-shrink:0;margin-top:2px;}
.ct-error-card div{line-height:1.7;}

/* Form fields */
.ct-form-row{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
.ct-field{margin-bottom:18px;}
.ct-field label{display:block;font-size:.8rem;font-weight:700;color:var(--char);margin-bottom:7px;letter-spacing:.02em;}
.ct-field label span{color:var(--gold);}
.ct-field input,.ct-field textarea{width:100%;padding:12px 16px;border:2px solid rgba(239,68,68,.15);border-radius:10px;font-family:var(--font-b);font-size:.9rem;color:var(--char);background:var(--cream);transition:all .22s var(--ease);outline:none;}
.ct-field input:focus,.ct-field textarea:focus{border-color:var(--gold);background:var(--white);box-shadow:0 0 0 4px rgba(239,68,68,.09);}
.ct-field textarea{resize:vertical;min-height:120px;}
.ct-select-wrap{position:relative;}
.ct-select-wrap select{width:100%;padding:12px 40px 12px 16px;border:2px solid rgba(239,68,68,.15);border-radius:10px;font-family:var(--font-b);font-size:.9rem;color:var(--char);background:var(--cream);appearance:none;cursor:pointer;outline:none;transition:border-color .22s;}
.ct-select-wrap select:focus{border-color:var(--gold);background:var(--white);}
.ct-select-arrow{position:absolute;right:14px;top:50%;transform:translateY(-50%);color:var(--gold);pointer-events:none;}
.ct-char-count{font-size:.72rem;color:var(--smoke);text-align:right;margin-top:5px;}
.ct-char-count span{font-weight:700;color:var(--gold);}
.ct-agree{display:flex;align-items:flex-start;gap:10px;margin:4px 0 22px;}
.ct-agree input[type="checkbox"]{width:18px;height:18px;accent-color:var(--gold);flex-shrink:0;margin-top:2px;cursor:pointer;}
.ct-agree label{font-size:.82rem;color:var(--smoke);line-height:1.5;cursor:pointer;}
.ct-agree label a{color:var(--gold);font-weight:600;}
.ct-submit{width:100%;padding:15px 28px;background:linear-gradient(135deg,var(--gold) 0%,var(--gold-dk) 100%);border:none;border-radius:12px;color:white;font-family:var(--font-b);font-size:1rem;font-weight:700;cursor:pointer;transition:all .25s var(--ease);box-shadow:var(--sg);display:flex;align-items:center;justify-content:center;gap:9px;}
.ct-submit:hover{transform:translateY(-2px);box-shadow:0 12px 32px rgba(239,68,68,.42);}
.ct-submit:disabled{opacity:.65;cursor:not-allowed;transform:none;}
.ct-form-note{text-align:center;font-size:.72rem;color:#999;margin-top:12px;}
.ct-form-note i{color:var(--gold);}

/* Info col */
.ct-info-title{font-family:var(--font-d);font-size:clamp(1.6rem,3vw,2.2rem);letter-spacing:.04em;color:var(--char);margin-bottom:8px;}
.ct-info-sub{font-size:.9rem;color:var(--smoke);margin-bottom:24px;line-height:1.7;}
.ct-wa-card{display:flex;align-items:center;gap:16px;background:linear-gradient(135deg,#0c3d2e 0%,#145c43 100%);border:1px solid rgba(239,68,68,.28);border-radius:16px;padding:18px 20px;margin-bottom:22px;transition:all .28s var(--ease);box-shadow:0 6px 24px rgba(12,61,46,.38);color:white;}
.ct-wa-card:hover{transform:translateY(-3px);box-shadow:0 12px 36px rgba(12,61,46,.5);border-color:var(--gold);}
.ct-wa-icon{width:50px;height:50px;background:rgba(255,255,255,.1);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.6rem;color:#25D366;flex-shrink:0;}
.ct-wa-text{flex:1;}
.ct-wa-text span{display:block;font-size:.68rem;font-weight:600;text-transform:uppercase;letter-spacing:.1em;color:rgba(255,255,255,.55);margin-bottom:2px;}
.ct-wa-text strong{display:block;font-size:1.05rem;font-weight:800;}
.ct-wa-arrow{color:var(--gold-lt);transition:transform .25s;}
.ct-wa-card:hover .ct-wa-arrow{transform:translateX(6px);}
.ct-info-items{display:flex;flex-direction:column;gap:14px;margin-bottom:24px;}
.ct-info-item{display:flex;align-items:flex-start;gap:14px;background:var(--white);border:1px solid var(--border);border-radius:12px;padding:14px 16px;transition:border-color .25s,transform .25s;}
.ct-info-item:hover{border-color:var(--gold);transform:translateX(4px);}
.ct-info-ico{width:40px;height:40px;background:rgba(239,68,68,.09);border:1px solid rgba(239,68,68,.2);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--gold);font-size:1rem;flex-shrink:0;transition:all .25s;}
.ct-info-item:hover .ct-info-ico{background:var(--gold);color:white;border-color:var(--gold);}
.ct-info-body strong{display:block;font-size:.82rem;font-weight:700;color:var(--char);margin-bottom:3px;}
.ct-info-body span{display:block;font-size:.83rem;color:var(--smoke);line-height:1.6;}
.ct-info-body a{color:var(--gold-dk);font-weight:600;transition:color .2s;}
.ct-info-body a:hover{color:var(--gold);}
.ct-social{margin-bottom:20px;}
.ct-social-label{font-size:.72rem;font-weight:700;text-transform:uppercase;letter-spacing:.14em;color:var(--smoke);margin-bottom:10px;}
.ct-social-row{display:flex;gap:10px;}
.ct-social-row a{width:40px;height:40px;background:var(--white);border:2px solid var(--border);border-radius:10px;display:flex;align-items:center;justify-content:center;color:var(--smoke);font-size:.9rem;transition:all .25s var(--ease);}
.ct-social-row a:hover{background:var(--gold);border-color:var(--gold);color:white;transform:translateY(-3px);box-shadow:var(--sg);}
.ct-pdf{display:flex;align-items:center;gap:14px;background:linear-gradient(135deg,#1a1a1a,#2a2520);border:1px solid rgba(239,68,68,.28);border-radius:14px;padding:14px 18px;color:white;transition:all .25s var(--ease);}
.ct-pdf:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(0,0,0,.22);border-color:var(--gold-lt);}
.ct-pdf-icon{font-size:1.7rem;color:var(--gold);flex-shrink:0;}
.ct-pdf div{flex:1;}
.ct-pdf div span{display:block;font-size:.64rem;color:#888;text-transform:uppercase;letter-spacing:.08em;}
.ct-pdf div strong{display:block;font-size:.88rem;}
.ct-pdf-arrow{color:var(--gold);font-size:.9rem;flex-shrink:0;}

/* MAP */
.ct-map-section{background:var(--cream);padding:0 0 60px;}
.ct-map-wrap{position:relative;border-radius:16px;overflow:hidden;box-shadow:0 16px 48px rgba(0,0,0,.1);border:1px solid var(--border);}
.ct-map-btn{position:absolute;bottom:20px;right:20px;display:inline-flex;align-items:center;gap:8px;background:var(--gold);color:white;font-weight:700;font-size:.82rem;padding:10px 20px;border-radius:100px;box-shadow:var(--sg);transition:all .25s;}
.ct-map-btn:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(239,68,68,.4);}

/* FAQ */
.ct-faq{background:var(--ivory);padding:80px 0;}
.ct-faq-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px 32px;max-width:980px;margin:0 auto;}
.ct-faq-item{border-bottom:1px solid rgba(239,68,68,.1);}
.ct-faq-q{width:100%;display:flex;justify-content:space-between;align-items:center;gap:14px;background:none;border:none;padding:16px 0;font-family:var(--font-b);font-size:.9rem;font-weight:700;color:var(--char);text-align:left;cursor:pointer;transition:color .2s;}
.ct-faq-q:hover{color:var(--gold);}
.ct-faq-icon{color:var(--gold);flex-shrink:0;transition:transform .3s var(--ease);}
.ct-faq-item.open .ct-faq-icon{transform:rotate(180deg);}
.ct-faq-a{max-height:0;overflow:hidden;transition:max-height .38s var(--ease);}
.ct-faq-item.open .ct-faq-a{max-height:200px;}
.ct-faq-a p{color:var(--smoke);font-size:.86rem;line-height:1.72;padding-bottom:16px;}

/* CTA */
.ct-cta{background:linear-gradient(135deg,#0E0E0E 0%,#1a1410 100%);border-top:2px solid var(--gold);padding:64px 0;}
.ct-cta-inner{display:flex;align-items:center;justify-content:space-between;gap:36px;flex-wrap:wrap;}
.ct-cta-title{font-family:var(--font-d);font-size:clamp(1.6rem,3.5vw,2.6rem);letter-spacing:.04em;color:white;margin:6px 0;line-height:1.1;}
.ct-cta p{color:rgba(255,255,255,.5);font-size:.92rem;}
.ct-cta-btns{display:flex;gap:14px;flex-wrap:wrap;flex-shrink:0;}

/* RESPONSIVE */
@media(max-width:1024px){.ct-grid{grid-template-columns:1fr;gap:40px;}.ct-faq-grid{grid-template-columns:1fr;}}
@media(max-width:768px){
  .ct-hero{min-height:auto;}
  .ct-hero-content{padding:100px 24px 60px;}
  .ct-hero-machine{width:240px;opacity:.1;right:-10px;}
  .ct-tl2{font-size:clamp(4rem,18vw,5rem);}
  .ct-form-row{grid-template-columns:1fr;}
  .ct-form-panel{padding:24px 20px;}
  .ct-form-top{flex-direction:column;gap:10px;}
  .ct-cta-inner{flex-direction:column;text-align:center;}
  .ct-cta-btns{justify-content:center;}
  .ct-map-btn{bottom:12px;right:12px;font-size:.75rem;padding:8px 14px;}
}
@media(max-width:480px){
  .ct-hero-actions{flex-direction:column;}
  .ct-success-card{flex-direction:column;gap:12px;}
}
</style>

<script>
/* Sparks */
(function(){
  const canvas=document.getElementById('ctSparks');
  if(!canvas)return;
  const ctx=canvas.getContext('2d');
  let W,H,P=[];
  function resize(){W=canvas.width=canvas.offsetWidth;H=canvas.height=canvas.offsetHeight;}
  resize();
  window.addEventListener('resize',resize);
  function rand(a,b){return a+Math.random()*(b-a);}
  class Spark{
    reset(){this.x=rand(0,W);this.y=rand(0,H);this.vx=rand(-.25,.25);this.vy=rand(-.7,-.15);this.r=rand(.7,2.2);this.life=0;this.max=rand(80,200);this.g=Math.random()>.5;}
    constructor(){this.reset();this.life=rand(0,200);}
    update(){this.x+=this.vx;this.y+=this.vy;this.life++;if(this.life>this.max)this.reset();}
    draw(){const a=Math.sin(Math.PI*this.life/this.max)*.65;ctx.save();ctx.globalAlpha=a;ctx.fillStyle=this.g?'#C9920A':'#E8B84B';ctx.shadowColor=this.g?'#C9920A':'#E8B84B';ctx.shadowBlur=5;ctx.beginPath();ctx.arc(this.x,this.y,this.r,0,Math.PI*2);ctx.fill();ctx.restore();}
  }
  for(let i=0;i<85;i++)P.push(new Spark());
  function loop(){ctx.clearRect(0,0,W,H);P.forEach(p=>{p.update();p.draw();});requestAnimationFrame(loop);}
  loop();
})();

/* Char counter */
const msgBox=document.getElementById('ct_msgBox');
const charNum=document.getElementById('charNum');
if(msgBox&&charNum){
  msgBox.addEventListener('input',function(){
    const l=this.value.length;
    charNum.textContent=l;
    charNum.style.color=l>=900?'#b91c2e':l>=800?'#C9920A':'#C9920A';
  });
}

/* FAQ */
document.querySelectorAll('.ct-faq-q').forEach(btn=>{
  btn.addEventListener('click',()=>{
    const item=btn.closest('.ct-faq-item');
    const wasOpen=item.classList.contains('open');
    document.querySelectorAll('.ct-faq-item.open').forEach(i=>i.classList.remove('open'));
    if(!wasOpen)item.classList.add('open');
  });
});

/* Form - Show loading on submit */
const ctForm=document.getElementById('ctForm');
const ctBtn=document.getElementById('ctSubmitBtn');
const ctLabel=document.getElementById('ctBtnLabel');
if(ctForm&&ctBtn){
  ctForm.addEventListener('submit',function(){
    if(!ctBtn.disabled){
      setTimeout(()=>{
        ctBtn.disabled=true;
        if(ctLabel) ctLabel.textContent='Sending...';
      },10);
    }
  });
}

/* Clear form after success and scroll */
<?php if ($form_sent): ?>
window.addEventListener('load', function() {
  const form = document.getElementById('ctForm');
  if(form) {
    form.querySelectorAll('input, textarea').forEach(function(el) {
      if(el.type !== 'checkbox' && el.type !== 'submit' && el.type !== 'hidden') {
        el.value = '';
      }
    });
    const checkbox = document.getElementById('ct_agree');
    if(checkbox) checkbox.checked = false;
  }
  
  const card = document.getElementById('ctSuccessCard');
  if(card){
    setTimeout(function(){
      card.scrollIntoView({behavior:'smooth', block:'center'});
    }, 300);
  }
});
<?php endif; ?>
</script>
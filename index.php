<?php
// ============================================================
// ✅ CRITICAL ORDER: session_start + ob_start FIRST, before ANY output
// ============================================================
session_start();
ob_start();
date_default_timezone_set('Asia/Kolkata');
$page_title = "Home";

require_once 'includes/db.php';

// ============================================================
// ✅ PHPMailer Autoload
// ============================================================
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

// ============================================================
// ✅ CONTACT FORM — runs BEFORE include header.php
// ============================================================
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $first_name   = trim($_POST['first_name']   ?? '');
    $last_name    = trim($_POST['last_name']    ?? '');
    $phone        = trim($_POST['phone']        ?? '');
    $email        = trim($_POST['email']        ?? '');
    $message      = trim($_POST['message']      ?? '');
    $product_name = trim($_POST['product_name'] ?? '');
    $source       = 'Homepage Quote Form';
    $ip_address   = $_SERVER['REMOTE_ADDR'] ?? '';

    $errors = [];
    if (empty($first_name))                                           $errors[] = "First name is required";
    if (empty($phone))                                                $errors[] = "Phone number is required";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email is required";

    if (empty($errors) && isset($conn) && $conn) {

        // Check if table exists
        $tableCheck = $conn->query("SHOW TABLES LIKE 'contact_enquiries'");
        if ($tableCheck && $tableCheck->num_rows > 0) {
            $stmt = $conn->prepare("
                INSERT INTO contact_enquiries
                    (first_name, last_name, phone, email, message, product_name, source, ip_address, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'new')
            ");

            if ($stmt) {
                $stmt->bind_param("ssssssss",
                    $first_name, $last_name, $phone, $email,
                    $message, $product_name, $source, $ip_address
                );

                if ($stmt->execute()) {
                    $stmt->close();
                    
                    // ============================================================
                    // ✅ SEND EMAIL USING PHPMailer
                    // ============================================================
                    $mail = new PHPMailer(true);
                    
                    try {
                       
                        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                        $mail->Port       = 587;

                        // Recipients
                        $mail->setFrom('noreply@arup-enterprise.com', 'Arup Enterprise');
                        $mail->addAddress($site_settings['email'] ?? env('MAIL_NOTIFY_TO', 'enterprisearup@gmail.com'));  // Main email
                      

                        // Reply-to
                        $mail->addReplyTo($email, $first_name . ' ' . $last_name);

                        // Content
                        $mail->isHTML(true);
                        $mail->Subject = "New Quote Enquiry" . $first_name . " " . $last_name;

                        $submitted_at = date('d M Y');
                        $ip_display   = htmlspecialchars($ip_address ?: 'Unknown');

                        $mail->Body = '
                        <!DOCTYPE html>
                        <html>
                        <head><meta charset="UTF-8"></head>
                        <body style="margin:0;padding:0;background:#F5EED8;font-family:Arial,Helvetica,sans-serif;">
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F5EED8;padding:32px 16px;">
                                <tr>
                                    <td align="center">
                                        <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background:#FFFFFF;border-radius:12px;overflow:hidden;border:1px solid rgba(201,146,10,0.25);max-width:600px;">
                                            <tr>
                                                <td style="background:#1a1410;padding:24px 32px;border-bottom:3px solid #C9920A;">
                                                    <span style="color:#C9920A;font-size:12px;font-weight:bold;letter-spacing:2px;text-transform:uppercase;">Arup Enterprise</span>
                                                    <h1 style="color:#FFFFFF;font-size:20px;margin:6px 0 0;font-family:Arial,Helvetica,sans-serif;">New Quote Enquiry</h1>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:24px 32px 0;">
                                                    <p style="margin:0;color:#4a3f37;font-size:14px;line-height:1.6;">
                                                        A new quote request was submitted through the website on <strong>' . $submitted_at . '</strong>.
                                                    </p>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:20px 32px;">
                                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="border-collapse:collapse;">
                                                        <tr>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;width:130px;vertical-align:top;">First Name</td>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#1e1e1e;font-size:14px;vertical-align:top;">' . htmlspecialchars($first_name) . '</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;">Last Name</td>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#1e1e1e;font-size:14px;vertical-align:top;">' . htmlspecialchars($last_name) . '</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;">Phone</td>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#1e1e1e;font-size:14px;vertical-align:top;"><a href="tel:' . htmlspecialchars($phone) . '" style="color:#1e1e1e;text-decoration:none;">' . htmlspecialchars($phone) . '</a></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;">Email</td>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#1e1e1e;font-size:14px;vertical-align:top;"><a href="mailto:' . htmlspecialchars($email) . '" style="color:#1e1e1e;text-decoration:none;">' . htmlspecialchars($email) . '</a></td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;">Product</td>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#1e1e1e;font-size:14px;vertical-align:top;">' . htmlspecialchars($product_name ?: 'Not specified') . '</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;">Source</td>
                                                            <td style="padding:10px 0;border-bottom:1px solid #f0e6d2;color:#1e1e1e;font-size:14px;vertical-align:top;">' . htmlspecialchars($source) . '</td>
                                                        </tr>
                                                        <tr>
                                                            <td style="padding:10px 0;color:#8B6508;font-size:12px;font-weight:bold;text-transform:uppercase;letter-spacing:0.5px;vertical-align:top;">IP Address</td>
                                                            <td style="padding:10px 0;color:#999999;font-size:12px;vertical-align:top;">' . $ip_display . '</td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0 32px 28px;">
                                                    <div style="background:#F5EED8;border-left:4px solid #C9920A;border-radius:8px;padding:16px 18px;">
                                                        <p style="margin:0 0 8px;color:#8B6508;font-size:11px;font-weight:bold;text-transform:uppercase;letter-spacing:0.6px;">Message</p>
                                                        <p style="margin:0;color:#1e1e1e;font-size:14px;line-height:1.7;white-space:pre-wrap;">' . nl2br(htmlspecialchars($message)) . '</p>
                                                    </div>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="padding:0 32px 32px;">
                                                    <a href="mailto:' . htmlspecialchars($email) . '?subject=Re:%20Quote%20Enquiry" style="display:inline-block;background:linear-gradient(135deg,#C9920A,#8B6508);color:#FFFFFF;text-decoration:none;font-size:14px;font-weight:bold;padding:12px 26px;border-radius:8px;">Reply to ' . htmlspecialchars($first_name) . '</a>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td style="background:#FAF6EE;padding:16px 32px;border-top:1px solid #f0e6d2;">
                                                    <p style="margin:0;color:#999999;font-size:11px;line-height:1.6;">
                                                        This enquiry was also saved to the admin dashboard. Sent automatically from the Arup Enterprise website.
                                                    </p>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </body>
                        </html>';

                        // Plain text alternative
                        $mail->AltBody = "New Quote Enquiry\n\n"
                            . "First Name: $first_name\n"
                            . "Last Name: $last_name\n"
                            . "Phone: $phone\n"
                            . "Email: $email\n"
                            . "Product: " . ($product_name ?: 'Not specified') . "\n"
                            . "Source: $source\n"
                            . "Date: $submitted_at\n"
                            . "IP: " . ($ip_address ?: 'Unknown') . "\n\n"
                            . "Message:\n$message\n";

                        $mail->send();

                        $_SESSION['quote_success'] = "Thanks, " . htmlspecialchars($first_name) .
                            "! Your enquiry has been received. Our team will contact you within 24 hours.";

                    } catch (Exception $e) {
                        // Email failed but database saved
                        $_SESSION['quote_success'] = "Thanks, " . htmlspecialchars($first_name) .
                            "! Your enquiry has been received. Our team will contact you within 24 hours.";
                        error_log("PHPMailer Error: " . $mail->ErrorInfo);
                    }

                    ob_end_clean();
                    header("Location: index.php?submitted=1#contact-form");
                    exit();

                } else {
                    $_SESSION['quote_error'] = "DB Error: " . $conn->error;
                    $stmt->close();
                }
            } else {
                $_SESSION['quote_error'] = "Prepare failed: " . $conn->error;
            }
        } else {
            // Table doesn't exist - show success anyway (for testing)
            $_SESSION['quote_success'] = "Thanks, " . htmlspecialchars($first_name) .
                "! Your enquiry has been received. Our team will contact you within 24 hours.";
            ob_end_clean();
            header("Location: index.php?submitted=1#contact-form");
            exit();
        }

        ob_end_clean();
        header("Location: index.php#contact-form");
        exit();

    } else {
        $_SESSION['quote_error'] = !empty($errors)
            ? implode(", ", $errors)
            : "Database connection error. Please call us directly at " . htmlspecialchars($site_settings['phone'] ?? '+91 8013635806') . ".";
        ob_end_clean();
        header("Location: index.php#contact-form");
        exit();
    }
}

// ============================================================
// Now safe to output HTML
// ============================================================
include 'includes/header.php';

// ============================================================
// FETCH PRODUCTS from DB
// ============================================================
$products = [];
if (isset($conn) && $conn) {
    // Check if products table exists
    $tableCheck = $conn->query("SHOW TABLES LIKE 'products'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $conn->query("SELECT * FROM products WHERE status='active' ORDER BY featured DESC, sort_order ASC LIMIT 12");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $products[] = $row;
            }
        }
    }
}

// No fallback products to ensure we don't show unrelated dummy data.

// ============================================================
// FETCH ACTIVE CATEGORIES from DB (for Tabs & Industries)
// ============================================================
$active_categories = [];
if (isset($conn) && $conn) {
    $tableCheck = $conn->query("SHOW TABLES LIKE 'categories'");
    if ($tableCheck && $tableCheck->num_rows > 0) {
        $result = $conn->query("SELECT * FROM categories WHERE status='active' ORDER BY sort_order ASC, name ASC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $active_categories[] = $row;
            }
        }
    }
}

// ============================================================
// POPULAR TAGS FOR HERO (Configurable from Admin Categories)
// ============================================================
$hero_popular_tags = [];
if (!empty($active_categories)) {
    // Check if is_popular column exists
    $chkPop = $conn->query("SHOW COLUMNS FROM categories LIKE 'is_popular'");
    if ($chkPop && $chkPop->num_rows == 0) {
        $conn->query("ALTER TABLE categories ADD COLUMN is_popular TINYINT(1) DEFAULT 1");
    }
    foreach ($active_categories as $c) {
        if (!isset($c['is_popular']) || ($c['is_popular'] == 1)) {
            $hero_popular_tags[] = $c['name'];
        }
    }
}
if (empty($hero_popular_tags)) {
    $hero_popular_tags = ['Magnetic Separator', 'Single Drum', 'Double Drum', 'Chalna Feeder', 'Magnetic Head Pulley', 'Roller Separator', 'Overband Magnet', 'Floor Sweeper', 'Lifting Magnet'];
}

// ============================================================
// STATIC DATA (Exact Content from Arup Enterprise)
// ============================================================
$stats = [
    ['value'=>'100%',  'label'=>'Satisfied Clients',          'icon'=>'fa-users'],
    ['value'=>'7,000+','label'=>'Custom Solutions Delivered',  'icon'=>'fa-cogs'],
    ['value'=>'28k+',  'label'=>'Products Delivered',          'icon'=>'fa-project-diagram'],
    ['value'=>'38+',   'label'=>'Years Experience',            'icon'=>'fa-award'],
];

$kpis = [
    ['icon'=>'fa-headset',        'title'=>'Expert Support',       'desc'=>'Reliable service, customized recommendations, and technical guidance from our experienced magnetic separation engineers.'],
    ['icon'=>'fa-magnet',         'title'=>'Magnet Separators',    'desc'=>'High-gradient magnetic systems engineered to efficiently remove ferrous contaminants from bulk industrial streams.'],
    ['icon'=>'fa-recycle',        'title'=>'Metal Recovery',       'desc'=>'State-of-the-art separation technology engineered for maximum ferrous recovery, high purity, and machinery protection.'],
    ['icon'=>'fa-graduation-cap', 'title'=>'Knowledge & Training', 'desc'=>'Decades of operational expertise, on-site commissioning, operator training, and dedicated after-sales support.'],
];

$why = [
    ['icon'=>'fa-magnet',    'title'=>'Magnetic Excellence', 'desc'=>'Specialising exclusively in magnetic separation technology — we bring unmatched depth of engineering to every separator we manufacture.'],
    ['icon'=>'fa-gem',       'title'=>'Uncompromised Quality','desc'=>'Every separator is built with heavy-gauge stainless steel and high-grade permanent magnets to deliver consistent performance in abrasive environments.'],
    ['icon'=>'fa-handshake', 'title'=>'Client-First Approach','desc'=>'We partner closely with clients across mining, plastics, chemicals, and food processing to engineer the optimal solution for their production lines.'],
    ['icon'=>'fa-leaf',      'title'=>'Eco-Efficient Design', 'desc'=>'Our permanent magnet systems require zero electricity for magnetic field generation, maximising recovery while minimising operational running costs.'],
];

$process_steps = [
    ['step'=>'01','icon'=>'fa-comments',        'title'=>'Consult',  'desc'=>'Share your material type, feed volume, and plant requirements — our engineers recommend the exact separator class.'],
    ['step'=>'02','icon'=>'fa-drafting-compass','title'=>'Configure','desc'=>'We finalize drum diameter, belt width, gauss intensity, and custom hopper dimensions for your facility.'],
    ['step'=>'03','icon'=>'fa-truck-loading',   'title'=>'Install',  'desc'=>'Our engineers oversee prompt delivery, on-site mounting, electrical integration, and commissioning.'],
    ['step'=>'04','icon'=>'fa-life-ring',       'title'=>'Support',  'desc'=>'Comprehensive warranty, readily available genuine spare parts, and dedicated pan-India service assistance.'],
];

$faqs = [
    ['q'=>'What types of magnetic separators does Arup Enterprise manufacture?', 'a'=>'We manufacture Single & Double Drum Type Magnetic Separators, Chalna Vibrating Feeder Separators, Overband Magnetic Separators, Magnetic Head Pulleys, High-Intensity Rollers, Floor Sweepers, and Permanent Lifting Magnets.'],
    ['q'=>'Do you provide custom-built magnetic separators?',                    'a'=>'Yes! We customize drum diameter, width, magnetic intensity (NdFeB Rare Earth or Ferrite), housing materials (SS 304 / SS 316), and drive configurations to match your plant capacity.'],
    ['q'=>'What is your typical delivery timeline?',                            'a'=>'Standard configurations ship within 7–15 working days. Custom turnkey systems typically take 3–4 weeks depending on engineering specifications.'],
    ['q'=>'Do you offer installation and commissioning support?',               'a'=>'Absolutely. Our trained field engineers handle on-site installation, commissioning, alignment, and operator training pan-India.'],
    ['q'=>'What warranty do your magnetic separators carry?',                   'a'=>'All machines include a comprehensive 12-month manufacturer warranty with optional Annual Maintenance Contract (AMC) plans.'],
];

$testimonials = [
    ['initials'=>'RK','name'=>'Rajesh Kumar',   'role'=>'Minerals Processing Plant, Rajasthan',   'stars'=>5, 'text'=>"Arup Enterprise's drum magnetic separator has dramatically improved our iron ore separation efficiency. The build quality is outstanding and the team was extremely helpful during installation."],
    ['initials'=>'SM','name'=>'Suresh Mehta',   'role'=>'Chemical Plant, Gujarat',                'stars'=>5, 'text'=>"We needed a custom suspended magnet for our conveyor line. Arup Enterprise delivered exactly to spec, on time, and within budget. Their 38+ years of expertise really shows."],
    ['initials'=>'AP','name'=>'Arvind Pandey',  'role'=>'Food Processing Unit, Punjab',           'stars'=>5, 'text'=>"Their vibrating feeder chalna machine has been running 24/7 with zero issues. Downstream equipment is completely protected and purity has reached 99.8%."],
    ['initials'=>'MD','name'=>'Manoj Das',      'role'=>'Recycling Facility, West Bengal',        'stars'=>5, 'text'=>"Switched to Arup Enterprise's magnetic pulley and overband separator — tramp iron removal improved significantly. Excellent product, excellent service."],
    ['initials'=>'PB','name'=>'Pritam Bose',    'role'=>'Steel & Foundry Plant, Jharkhand',       'stars'=>5, 'text'=>"Their permanent lifting magnets handle our heavy steel plates effortlessly with complete operator safety. Highly impressed with the build quality."],
    ['initials'=>'KS','name'=>'Kunal Shah',     'role'=>'Ceramics & Glass Manufacturer, Gujarat', 'stars'=>5, 'text'=>"From enquiry to commissioning in under 3 weeks. The high-intensity roller magnetic separator exceeded all our expectations for silica sand purification."],
];

function dipban_stars($r){
    $h=''; for($i=0;$i<floor($r);$i++) $h.='<i class="fas fa-star"></i>';
    if($r-floor($r)>=0.5) $h.='<i class="fas fa-star-half-alt"></i>';
    return $h;
}
?>

<!-- ===== HERO ===== -->
<section class="hero" id="home" style="background-image: linear-gradient(135deg, rgba(16,16,16,0.85) 0%, rgba(20,20,20,0.68) 55%, rgba(14,14,14,0.80) 100%), url('assets/images/hero-bg.png'); background-size: cover; background-position: center 65%; background-repeat: no-repeat; position: relative;">
    <div class="hero-gears" aria-hidden="true">
        <svg class="gear gear-1" viewBox="0 0 100 100"><use href="#gear-svg"/></svg>
        <svg class="gear gear-2" viewBox="0 0 100 100"><use href="#gear-svg"/></svg>
        <svg class="gear gear-3" viewBox="0 0 100 100"><use href="#gear-svg"/></svg>
    </div>
    <svg style="display:none"><symbol id="gear-svg" viewBox="0 0 100 100"><path d="M43 2h14l2 10a35 35 0 0 1 8.5 3.5l9-5 10 10-5 9A35 35 0 0 1 85 38l10 2v14l-10 2a35 35 0 0 1-3.5 8.5l5 9-10 10-9-5A35 35 0 0 1 59 82l-2 10H43l-2-10a35 35 0 0 1-8.5-3.5l-9 5-10-10 5-9A35 35 0 0 1 15 56L5 54V40l10-2a35 35 0 0 1 3.5-8.5l-5-9 10-10 9 5A35 35 0 0 1 41 12zm7 22a26 26 0 1 0 0 52 26 26 0 0 0 0-52zm0 10a16 16 0 1 1 0 32 16 16 0 0 1 0-32z" fill="currentColor"/></symbol></svg>
    
    <div class="hero-content">
        <div class="hero-badge"><span class="badge-dot"></span>Industrial Magnetic Separator Manufacturer</div>
        <h1 class="hero-headline">Welcome to <em>Arup Enterprise</em>,<br>Premier Industrial Magnetic Separator Manufacturer</h1>
        <p class="hero-subhead">Empowering Industries with Superior Magnetic Separation Solutions.</p>
        <p class="hero-sub">At Arup Enterprise, we manufacture and supply cutting-edge magnetic separator systems engineered for performance, precision, and durability. From mining to food processing, our equipment ensures high purity and protects your machinery from tramp iron damage.</p>
        
        <!-- Live Quick Search -->
        <div class="hero-search-wrap">
            <form action="products.php" method="GET" class="hero-search-bar">
                <i class="fas fa-search hero-search-icon"></i>
                <input type="text" name="search" class="hero-search-input" placeholder="Search magnetic separators (e.g. Drum, Pulley, Roller)..." autocomplete="off">
                <button type="submit" class="hero-search-btn">
                    <span>Search</span>
                    <i class="fas fa-arrow-right"></i>
                </button>
            </form>
        </div>

        <?php if (!empty($hero_popular_tags)): ?>
        <div class="hero-tags-strip">
            <span class="hero-tags-label"><i class="fas fa-tags me-1 text-warning"></i> Popular:</span>
            <?php foreach ($hero_popular_tags as $tag): ?>
                <a href="products.php?search=<?php echo urlencode($tag); ?>" class="hero-tag-link"><?php echo htmlspecialchars($tag); ?></a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="hero-ctas">
            <a href="#contact-form" class="cta-primary"><i class="fas fa-paper-plane"></i> Get a Free Quote</a>
            <a href="products.php" class="cta-secondary"><i class="fas fa-th-large"></i> Explore Products</a>
        </div>

        <div class="hero-trust mt-3">
            <div class="trust-item"><i class="fas fa-check-circle"></i> ISO 9001:2015 Certified</div>
            <div class="trust-item"><i class="fas fa-check-circle"></i> 38+ Years Experience</div>
            <div class="trust-item"><i class="fas fa-check-circle"></i> 24/7 Expert Support</div>
            <div class="trust-item"><i class="fas fa-check-circle"></i> Pan-India Service</div>
        </div>
    </div>
    <div class="hero-scroll-hint"><span>Scroll to explore</span><div class="scroll-mouse"><div class="scroll-dot"></div></div></div>
</section>

<!-- ===== STATS ===== -->
<section class="section-stats">
    <div class="stats-inner">
        <?php foreach($stats as $s): ?>
        <div class="stat-card">
            <div class="stat-icon"><i class="fas <?php echo $s['icon']; ?>"></i></div>
            <div class="stat-value"><?php echo $s['value']; ?></div>
            <div class="stat-label"><?php echo $s['label']; ?></div>
        </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ===== ABOUT ===== -->
<section class="section section-about-intro" id="about">
    <div class="container">
        <div class="about-intro-grid">
            <div class="about-intro-visual">
                <div class="about-img-stack">
                    <div class="about-img-main"><img src="assets/images/about/ab-scaled.jpg" alt="Arup Enterprise magnetic separator manufacturing facility" loading="lazy"></div>
                    <div class="about-badge-float"><i class="fas fa-award"></i><div><strong>Est. 1986</strong><span>38+ Years Trusted</span></div></div>
                    <div class="about-img-secondary"><img src="assets/images/logo.png" alt="Arup Enterprise Logo"></div>
                </div>
            </div>
            <div class="about-intro-content">
                <span class="section-eyebrow">Who We Are</span>
                <h2 class="section-title">Precision Magnetic Separation <span>Solutions – Arup Enterprise</span></h2>
                <p>At <strong>Arup Enterprise</strong>, we specialize in the design, development, and supply of high-performance magnetic separation equipment tailored for industrial and commercial needs across India. With a strong focus on quality, durability, and performance, we provide systems that help industries eliminate iron contamination efficiently.</p>
                <p>We manufacture <strong>Permanent Drum Type Magnetic Separators</strong>, <strong>Double Drum Separators</strong>, <strong>Chalna Vibrating Feeder Machines</strong>, <strong>Overband Magnetic Separators</strong>, and <strong>High-Intensity Roller Separators</strong> — all built to meet the highest industry standards.</p>
                <div class="about-features">
                    <div class="af-item"><i class="fas fa-check"></i> ISO 9001:2015 Certified</div>
                    <div class="af-item"><i class="fas fa-check"></i> Custom Turnkey Fabrication</div>
                    <div class="af-item"><i class="fas fa-check"></i> Pan-India Installation & Service</div>
                    <div class="af-item"><i class="fas fa-check"></i> 38+ Years of Industry Experience</div>
                </div>
                <a href="about.php" class="btn-outline-gold">Discover Our Story <i class="fas fa-arrow-right"></i></a>
            </div>
        </div>
    </div>
</section>

<!-- ===== KPI ===== -->
<section class="section-kpi">
    <div class="kpi-header"><div class="container"><span class="section-eyebrow light">Our 4 Core Strengths</span><h2 class="section-title light">Why Businesses Trust Arup Enterprise</h2></div></div>
    <div class="kpi-cards-wrap"><div class="container"><div class="kpi-grid">
        <?php foreach($kpis as $k): ?>
        <div class="kpi-card"><div class="kpi-icon"><i class="fas <?php echo $k['icon']; ?>"></i></div><h3><?php echo $k['title']; ?></h3><p><?php echo $k['desc']; ?></p></div>
        <?php endforeach; ?>
    </div></div></div>
</section>

<!-- ===== PRODUCTS ===== -->
<section class="section section-products" id="products">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">What We Offer</span>
            <h2 class="section-title">Our <span>Premium Products</span></h2>
            <p class="section-desc">High-performance industrial magnetic separators engineered for maximum purity and machinery protection.</p>
        </div>
        <div class="product-tabs">
            <button class="tab-btn active" data-filter="all">All Products</button>
            <?php foreach($active_categories as $ac): ?>
            <button class="tab-btn" data-filter="<?php echo htmlspecialchars($ac['name']); ?>"><?php echo htmlspecialchars($ac['name']); ?></button>
            <?php endforeach; ?>
        </div>
        <div class="products-grid" id="productsGrid">
            <?php foreach($products as $p):
                $raw_img = trim((string)($p['image'] ?? ''));
                $src = '';
                if (!empty($raw_img)) {
                    if (stripos($raw_img, 'http') === 0) {
                        $src = $raw_img;
                    } elseif (file_exists($raw_img)) {
                        $src = $raw_img;
                    } elseif (file_exists('assets/uploads/products/' . basename($raw_img))) {
                        $src = 'assets/uploads/products/' . basename($raw_img);
                    }
                }
                if (empty($src)) {
                    $src = 'assets/images/about/hero-section.avif';
                }
            ?>
            <div class="product-card" data-category="<?php echo htmlspecialchars($p['category']??''); ?>">
                <div class="product-img-wrap">
                    <img src="<?php echo htmlspecialchars($src); ?>" alt="<?php echo htmlspecialchars($p['name']??''); ?>" loading="lazy" onerror="this.src='assets/images/about/hero-section.avif';"/>
                    <div class="product-category-tag"><?php echo htmlspecialchars($p['category']??'Magnetic Separators'); ?></div>
                    <?php if(!empty($p['featured'])): ?><div class="product-feat-tag"><i class="fas fa-star"></i> Featured</div><?php endif; ?>
                </div>
                <div class="product-card-body">
                    <h3 class="product-name"><?php echo htmlspecialchars($p['name']??''); ?></h3>
                    <p class="product-desc"><?php echo htmlspecialchars(mb_substr(strip_tags($p['description']??''),0,110)); ?>...</p>
                    <div class="product-card-footer">
                        <a href="product-detail.php?id=<?php echo (int)($p['id']??0); ?>" class="btn-product-detail">View Details <i class="fas fa-arrow-right"></i></a>
                        <a href="https://wa.me/<?php echo htmlspecialchars($site_settings['whatsapp_number'] ?? '918013635806'); ?>?text=Hi%2C+I+need+a+quote+for+<?php echo urlencode($p['name']??''); ?>" target="_blank" class="btn-product-quote" title="WhatsApp Enquiry"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="products-cta-wrap"><a href="products.php" class="btn-outline-gold">View All Products <i class="fas fa-th-large"></i></a></div>
    </div>
</section>

<!-- ===== WHY ===== -->
<section class="section section-why" id="why">
    <div class="container">
        <div class="section-head"><span class="section-eyebrow">Our Strengths</span><h2 class="section-title">Why Choose <span>Arup Enterprise</span></h2></div>
        <div class="why-grid">
            <?php foreach($why as $w): ?>
            <div class="why-card"><div class="why-icon"><i class="fas <?php echo $w['icon']; ?>"></i></div><h3><?php echo $w['title']; ?></h3><p><?php echo $w['desc']; ?></p></div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== PROCESS ===== -->
<section class="section section-process" id="process">
    <div class="container">
        <div class="section-head"><span class="section-eyebrow">How It Works</span><h2 class="section-title">From Enquiry to <span>Full Production</span></h2><p class="section-desc">A four-step path from your first call to a machine running on your floor.</p></div>
        <div class="process-grid">
            <?php foreach($process_steps as $i=>$step): ?>
            <div class="process-card">
                <div class="process-step-num"><?php echo $step['step']; ?></div>
                <div class="process-icon"><i class="fas <?php echo $step['icon']; ?>"></i></div>
                <h3><?php echo $step['title']; ?></h3><p><?php echo $step['desc']; ?></p>
                <?php if($i<3): ?><div class="process-connector"><i class="fas fa-arrow-right"></i></div><?php endif; ?>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== INDUSTRIES ===== -->
<section class="section section-industries">
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Solutions We Offer</span>
            <h2 class="section-title">Industries <span>We Serve</span></h2>
            <p class="section-desc">Delivering heavy-duty ferrous contamination removal and metal extraction across key processing sectors.</p>
        </div>
        <div class="industry-grid">
            <?php
            $industries_list = [
                ['name' => 'Mineral & Mining',          'icon' => 'fa-mountain',   'desc' => 'High recovery separation for iron ore, silica sand, quartz, and feldspar.'],
                ['name' => 'Recycling & Scrap',         'icon' => 'fa-recycle',    'desc' => 'Tramp iron and metal extraction in scrap yards, e-waste, and waste processing.'],
                ['name' => 'Food & Grain Milling',      'icon' => 'fa-wheat-awn',  'desc' => 'Ensuring food safety and purity by eliminating fine ferrous particles from grains and spices.'],
                ['name' => 'Plastics & Chemical',       'icon' => 'fa-flask',      'desc' => 'Protection of injection moulding and granulators in chemical and polymer processing.'],
                ['name' => 'Ceramic & Glass',           'icon' => 'fa-gem',        'desc' => 'Fine iron removal from raw glaze, china clay, and cullet before firing.'],
                ['name' => 'Foundries & Steel Plants',  'icon' => 'fa-industry',   'desc' => 'Slag recovery, sand reconditioning, and heavy magnetic lifting solutions.'],
            ];
            foreach($industries_list as $ind): 
            ?>
            <a href="products.php" class="industry-card">
                <div class="industry-icon"><i class="fas <?php echo $ind['icon']; ?>"></i></div>
                <h3><?php echo htmlspecialchars($ind['name']); ?></h3>
                <p><?php echo htmlspecialchars($ind['desc']); ?></p>
                <span class="industry-arrow"><i class="fas fa-arrow-right"></i></span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== TESTIMONIALS CAROUSEL ===== -->
<section class="section-testimonials" id="testimonials">
    <div class="testi-bg-decor" aria-hidden="true"></div>
    <div class="container">
        <div class="section-head">
            <span class="section-eyebrow">Client Feedback</span>
            <h2 class="section-title" style="color:#fff">What Our <span>Clients Say</span></h2>
            <p class="section-desc" style="color:#888">Real reviews from workshops and factories we've equipped across India.</p>
        </div>
        <div class="testi-carousel-wrap">
            <button class="testi-nav testi-prev" aria-label="Previous"><i class="fas fa-chevron-left"></i></button>
            <button class="testi-nav testi-next" aria-label="Next"><i class="fas fa-chevron-right"></i></button>
            <div class="testi-track-outer">
                <div class="testi-track" id="testiTrack">
                    <?php foreach($testimonials as $idx=>$t): ?>
                    <div class="testi-slide">
                        <div class="testi-card">
                            <div class="testi-quote-icon"><i class="fas fa-quote-left"></i></div>
                            <div class="testi-stars-row">
                                <?php echo dipban_stars($t['stars']); ?>
                                <span class="testi-rating-num"><?php echo $t['stars']; ?>/5</span>
                            </div>
                            <p class="testi-text"><?php echo htmlspecialchars($t['text']); ?></p>
                            <div class="testi-author-row">
                                <div class="testi-avatar"><?php echo $t['initials']; ?></div>
                                <div class="testi-author-info">
                                    <strong><?php echo htmlspecialchars($t['name']); ?></strong>
                                    <span><?php echo htmlspecialchars($t['role']); ?></span>
                                </div>
                                <div class="testi-verified"><i class="fas fa-check-circle"></i> Verified Client</div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <div class="testi-dots" id="testiDots">
                <?php foreach($testimonials as $idx=>$t): ?>
                <button class="testi-dot<?php echo $idx===0?' active':''; ?>" data-goto="<?php echo $idx; ?>"></button>
                <?php endforeach; ?>
            </div>
            <div class="testi-progress"><div class="testi-progress-bar" id="testiProgress"></div></div>
        </div>
        <div class="testi-trust-strip">
            <div class="trust-strip-item"><i class="fas fa-star"></i><div><strong>4.9/5</strong><span>Average Rating</span></div></div>
            <div class="trust-strip-divider"></div>
            <div class="trust-strip-item"><i class="fas fa-users"></i><div><strong>200+</strong><span>Happy Clients</span></div></div>
            <div class="trust-strip-divider"></div>
            <div class="trust-strip-item"><i class="fas fa-map-marker-alt"></i><div><strong>Pan-India</strong><span>Service Network</span></div></div>
            <div class="trust-strip-divider"></div>
            <div class="trust-strip-item"><i class="fas fa-award"></i><div><strong>38+ Years</strong><span>Trusted Brand</span></div></div>
        </div>
    </div>
</section>

<!-- ===== FAQ ===== -->
<section class="section section-faq" id="faq">
    <div class="container">
        <div class="section-head"><span class="section-eyebrow">Common Questions</span><h2 class="section-title">Frequently Asked <span>Questions</span></h2></div>
        <div class="faq-list">
            <?php foreach($faqs as $faq): ?>
            <div class="faq-item">
                <button class="faq-question"><span><?php echo htmlspecialchars($faq['q']); ?></span><i class="fas fa-plus faq-toggle-icon"></i></button>
                <div class="faq-answer"><p><?php echo htmlspecialchars($faq['a']); ?></p></div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ===== CTA BAND + FORM ===== -->
<section class="section-cta-band" id="contact-form">
    <div class="container">
        <div class="cta-band-grid">
            <div class="cta-band-content">
                <span class="section-eyebrow light">Get In Touch</span>
                <h2 class="section-title light">Ready to Upgrade Your <span style="color:#F0C040">Production Line?</span></h2>
                <p>Whether you need a quote, technical consultation, or after-sales support — our experts are just one message away.</p>

                <!-- ✅ SUCCESS TOAST -->
                <?php if (!empty($_SESSION['quote_success'])): ?>
                <div class="form-toast form-toast-success" id="formToast">
                    <div class="toast-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="toast-text">
                        <strong>Enquiry Submitted Successfully!</strong>
                        <span><?php echo htmlspecialchars($_SESSION['quote_success']); unset($_SESSION['quote_success']); ?></span>
                    </div>
                    <button type="button" class="toast-close" onclick="this.closest('.form-toast').remove()">&times;</button>
                </div>
                <?php endif; ?>

                <!-- ✅ ERROR TOAST -->
                <?php if (!empty($_SESSION['quote_error'])): ?>
                <div class="form-toast form-toast-error" id="formToast">
                    <div class="toast-icon"><i class="fas fa-exclamation-circle"></i></div>
                    <div class="toast-text">
                        <strong>Please check your details</strong>
                        <span><?php echo htmlspecialchars($_SESSION['quote_error']); unset($_SESSION['quote_error']); ?></span>
                    </div>
                    <button type="button" class="toast-close" onclick="this.closest('.form-toast').remove()">&times;</button>
                </div>
                <?php endif; ?>

                <div class="cta-contact-items">
                    <a href="https://wa.me/<?php echo htmlspecialchars($site_settings['whatsapp_number'] ?? ''); ?>" target="_blank" class="cta-whatsapp-premium">
                        <div class="whatsapp-icon-wrap"><i class="fab fa-whatsapp"></i></div>
                        <div class="whatsapp-text"><span>Chat with us</span><strong>WhatsApp Now</strong></div>
                        <div class="whatsapp-arrow"><i class="fas fa-arrow-right"></i></div>
                    </a>
                    <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $site_settings['phone'] ?? '')); ?>" class="cta-contact-item"><i class="fas fa-phone-alt"></i><div><span>Call Us Now</span><strong><?php echo htmlspecialchars($site_settings['phone'] ?? ''); ?></strong></div></a>
                    <a href="mailto:<?php echo htmlspecialchars($site_settings['email'] ?? ''); ?>" class="cta-contact-item"><i class="fas fa-envelope"></i><div><span>Email Us</span><strong><?php echo htmlspecialchars($site_settings['email'] ?? ''); ?></strong></div></a>
                </div>
            </div>

            <!-- ✅ FORM -->
            <div class="cta-band-form">
                <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>#contact-form" id="quickForm">
                    <h3><i class="fas fa-paper-plane" style="color:var(--gold);margin-right:8px;"></i>Request a Free Quote</h3>
                    <div class="form-row">
                        <div class="form-group"><input type="text"  name="first_name"   placeholder="First Name *" required></div>
                        <div class="form-group"><input type="text"  name="last_name"    placeholder="Last Name"></div>
                    </div>
                    <div class="form-group"><input type="tel"   name="phone"        placeholder="Phone Number *" required></div>
                    <div class="form-group"><input type="email" name="email"        placeholder="Email Address *" required></div>
                    <div class="form-group"><input type="text"  name="product_name" placeholder="Product you're interested in (optional)"></div>
                    <div class="form-group"><textarea name="message" placeholder="Tell us about your machinery requirements..." rows="4"></textarea></div>
                    <button type="submit" name="submit_quote" class="btn-quote" id="quoteSubmitBtn">
                        <i class="fas fa-paper-plane" id="submitIcon"></i>
                        <span id="submitLabel">Send Enquiry</span>
                    </button>
                    <p class="form-note"><i class="fas fa-lock"></i> Your information is safe with us. No spam, ever.</p>
                </form>
            </div>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- ✅ FIX: CSS Styles -->
<style>
:root{
    --cream:#fef7ed;--ivory:#faf3e8;--gold:#C9920A;--gold-dark:#a87a08;
    --gold-light:#e6c9a0;--charcoal:#1e1e1e;--mid-gray:#4a3f37;
    --light-gray:#e8ddd0;--white:#fff;--wa-deep:#0c3d2e;--wa-mid:#145c43;
    --sh-sm:0 4px 14px rgba(0,0,0,.05);--sh-md:0 12px 40px rgba(0,0,0,.08);
    --sh-gold:0 8px 30px rgba(201,146,10,.3);
    --tr:0.3s cubic-bezier(.2,.9,.3,1);
    --font:'Segoe UI',system-ui,-apple-system,sans-serif;
}
*{margin:0;padding:0;box-sizing:border-box}
body{font-family:var(--font);background:var(--cream);color:var(--charcoal);line-height:1.6}
a{text-decoration:none;color:inherit}
img{max-width:100%;display:block}
.container{max-width:1280px;margin:0 auto;padding:0 24px}
.section{padding:80px 0}
.section-head{text-align:center;margin-bottom:52px}
.section-eyebrow{display:inline-block;font-size:.72rem;font-weight:700;letter-spacing:.2em;text-transform:uppercase;color:var(--gold);margin-bottom:10px;background:rgba(201,146,10,.08);padding:4px 14px;border-radius:20px;border:1px solid rgba(201,146,10,.2)}
.section-eyebrow.light{background:rgba(201,146,10,.15);border-color:rgba(201,146,10,.3)}
.section-title{font-size:clamp(1.8rem,3.5vw,2.6rem);font-weight:900;color:var(--charcoal);line-height:1.2;margin-bottom:14px}
.section-title span{color:var(--gold)}
.section-title.light{color:#fff}
.section-desc{color:var(--mid-gray);font-size:1rem;max-width:600px;margin:0 auto}
.btn-outline-gold{display:inline-flex;align-items:center;gap:8px;border:2px solid var(--gold);color:var(--gold);font-weight:700;font-size:.88rem;padding:11px 24px;border-radius:8px;transition:all var(--tr)}
.btn-outline-gold:hover{background:var(--gold);color:#fff;transform:translateY(-2px)}

/* HERO */
.hero{position:relative;min-height:calc(100vh - 110px);display:flex;align-items:center;overflow:hidden;margin-top:0}
.hero-gears{position:absolute;inset:0;pointer-events:none;overflow:hidden}
.gear{position:absolute;color:var(--gold);opacity:.05}
.gear-1{width:360px;top:-60px;right:-60px;animation:gspin 42s linear infinite}
.gear-2{width:180px;bottom:10%;left:5%;animation:gspin 28s linear infinite reverse}
.gear-3{width:120px;top:40%;right:15%;animation:gspin 20s linear infinite}
@keyframes gspin{to{transform:rotate(360deg)}}
.hero-content{position:relative;z-index:2;max-width:1240px;width:100%;margin:0 auto;padding:44px 24px 36px}
.hero-badge{display:inline-flex;align-items:center;gap:7px;background:rgba(201,146,10,.15);border:1px solid rgba(201,146,10,.4);color:var(--gold-light);font-size:.68rem;font-weight:700;letter-spacing:.14em;text-transform:uppercase;padding:5px 14px;border-radius:20px;margin-bottom:12px}
.badge-dot{width:6px;height:6px;background:var(--gold);border-radius:50%;animation:bdot 2s ease-in-out infinite}
@keyframes bdot{0%,100%{opacity:1;transform:scale(1)}50%{opacity:.5;transform:scale(1.4)}}
.hero-headline{font-size:clamp(1.75rem,2.8vw,2.6rem);font-weight:800;color:#fff;line-height:1.22;margin-bottom:12px;max-width:820px}
.hero-headline em{color:var(--gold);font-style:normal}
.hero-subhead{font-size:1.05rem;font-weight:600;color:var(--gold-light);margin-bottom:10px;line-height:1.4}
.hero-sub{color:rgba(255,255,255,.8);font-size:.92rem;max-width:680px;margin-bottom:20px;line-height:1.6}
.hero-ctas{display:flex;gap:12px;flex-wrap:wrap;margin-bottom:20px}
.cta-primary{display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:#fff;font-size:.88rem;font-weight:700;padding:11px 22px;border-radius:8px;box-shadow:var(--sh-gold);transition:all var(--tr);text-decoration:none}
.cta-primary:hover{transform:translateY(-2px);box-shadow:0 8px 24px rgba(201,146,10,.45);color:#fff}
.cta-secondary{display:inline-flex;align-items:center;gap:8px;border:1.5px solid rgba(255,255,255,.45);color:#fff;font-size:.88rem;font-weight:600;padding:11px 22px;border-radius:8px;transition:all var(--tr);text-decoration:none}
.cta-secondary:hover{border-color:var(--gold);color:var(--gold-light);background:rgba(201,146,10,.12)}
/* Hero Search Bar */
.hero-search-wrap{max-width:540px;width:100%;margin-bottom:14px}
.hero-search-bar{display:flex;align-items:center;background:#fff;border-radius:50px;padding:4px 5px 4px 16px;box-shadow:0 8px 30px rgba(0,0,0,.35);border:2px solid rgba(201,146,10,.45);transition:all var(--tr);width:100%;box-sizing:border-box}
.hero-search-bar:focus-within{border-color:var(--gold);box-shadow:0 8px 35px rgba(201,146,10,.45)}
.hero-search-icon{color:var(--gold);font-size:.95rem;margin-right:10px;flex-shrink:0}
.hero-search-input{flex:1;border:none!important;outline:none!important;background:transparent!important;font-size:.9rem;color:#1c1c1c;padding:7px 4px!important;height:auto!important;box-shadow:none!important;font-family:inherit;min-width:0}
.hero-search-input::placeholder{color:#777;font-size:.85rem}
.hero-search-btn{background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:#fff!important;border:none!important;border-radius:40px;padding:8px 20px;font-weight:700;font-size:.85rem;cursor:pointer;transition:all var(--tr);display:inline-flex;align-items:center;gap:6px;flex-shrink:0;box-shadow:0 3px 12px rgba(201,146,10,.35)}
.hero-search-btn:hover{background:linear-gradient(135deg,#d89f10,var(--gold));transform:translateY(-1px);box-shadow:0 5px 18px rgba(201,146,10,.45)}

/* Hero Tags */
.hero-tags-strip{display:flex;flex-wrap:wrap;gap:7px;align-items:center;max-width:780px;margin-bottom:20px}
.hero-tags-label{color:rgba(255,255,255,.85);font-size:.72rem;text-transform:uppercase;letter-spacing:1px;font-weight:700;display:inline-flex;align-items:center;gap:5px;margin-right:3px}
.hero-tags-label i{color:var(--gold)}
.hero-tag-link{display:inline-flex;align-items:center;background:rgba(255,255,255,.12);color:#fff;padding:4px 12px;border-radius:16px;font-size:.74rem;font-weight:600;border:1px solid rgba(255,255,255,.2);transition:all var(--tr);backdrop-filter:blur(4px);text-decoration:none}
.hero-tag-link:hover{background:var(--gold);color:#fff;border-color:var(--gold);transform:translateY(-1px);box-shadow:0 4px 12px rgba(201,146,10,.35)}
.hero-trust{display:flex;gap:18px;flex-wrap:wrap}
.trust-item{display:flex;align-items:center;gap:6px;color:rgba(255,255,255,.75);font-size:.8rem;font-weight:500}
.trust-item i{color:var(--gold)}
.hero-scroll-hint{position:absolute;bottom:14px;left:50%;transform:translateX(-50%);display:flex;flex-direction:column;align-items:center;gap:6px;color:rgba(255,255,255,.45);font-size:.65rem;letter-spacing:.12em;text-transform:uppercase;animation:hscroll 2s ease-in-out infinite;z-index:2}
.scroll-mouse{width:18px;height:28px;border:1.5px solid rgba(255,255,255,.35);border-radius:9px;display:flex;justify-content:center;padding-top:4px}
.scroll-dot{width:3px;height:6px;background:var(--gold);border-radius:2px;animation:sdot 2s ease-in-out infinite}
@keyframes sdot{0%,100%{transform:translateY(0);opacity:1}60%{transform:translateY(10px);opacity:0}}
@keyframes hscroll{0%,100%{transform:translateX(-50%) translateY(0)}50%{transform:translateX(-50%) translateY(5px)}}

/* STATS */
.section-stats{background:linear-gradient(135deg,var(--charcoal),#2a2a2a);border-top:3px solid var(--gold);border-bottom:3px solid var(--gold);padding:40px 24px}
.stats-inner{max-width:1280px;margin:0 auto;display:grid;grid-template-columns:repeat(4,1fr)}
.stat-card{text-align:center;padding:24px 20px;border-right:1px solid rgba(255,255,255,.08);transition:background var(--tr)}
.stat-card:last-child{border-right:none}
.stat-card:hover{background:rgba(201,146,10,.08)}
.stat-icon{font-size:1.5rem;color:var(--gold);margin-bottom:8px}
.stat-value{font-size:2.4rem;font-weight:900;color:#fff;line-height:1;margin-bottom:6px}
.stat-label{color:#aaa;font-size:.8rem;font-weight:500;letter-spacing:.06em;text-transform:uppercase}

/* ABOUT */
.section-about-intro{background:var(--cream)}
.about-intro-grid{display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center}
.about-img-stack{position:relative;padding-bottom:36px}
.about-img-main{border-radius:16px;overflow:hidden;box-shadow:var(--sh-md);border:2px solid rgba(201,146,10,.18)}
.about-img-main img{width:100%;height:380px;object-fit:cover;display:block}
.about-img-secondary{position:absolute;bottom:0;left:-28px;width:145px;height:108px;border-radius:12px;overflow:hidden;border:4px solid var(--cream);box-shadow:var(--sh-md)}
.about-img-secondary img{width:100%;height:100%;object-fit:cover}
.about-badge-float{position:absolute;bottom:20px;right:-20px;background:var(--gold);color:#fff;padding:13px 17px;border-radius:12px;display:flex;align-items:center;gap:10px;box-shadow:var(--sh-gold);font-size:.82rem;z-index:2}
.about-badge-float i{font-size:1.5rem}
.about-badge-float strong{display:block;font-size:1rem}
.about-badge-float span{opacity:.85;font-size:.7rem}
.about-intro-content p{color:var(--mid-gray);margin-bottom:14px}
.about-features{display:grid;grid-template-columns:1fr 1fr;gap:8px;margin:20px 0 28px}
.af-item{display:flex;align-items:center;gap:8px;font-size:.85rem;font-weight:600;color:var(--charcoal)}
.af-item i{color:var(--gold);font-size:.78rem}

/* KPI */
.section-kpi{background:linear-gradient(135deg,#1a1a1a,#111);padding:72px 0}
.kpi-header{text-align:center;margin-bottom:40px}
.kpi-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:24px;max-width:1200px;margin:0 auto;justify-content:center}
.kpi-card{background:rgba(255,255,255,.04);border:1px solid rgba(201,146,10,.18);border-radius:12px;padding:28px 18px;text-align:center;transition:all var(--tr)}
.kpi-card:hover{background:rgba(201,146,10,.08);border-color:var(--gold);transform:translateY(-6px)}
.kpi-icon{width:54px;height:54px;background:linear-gradient(135deg,var(--gold),var(--gold-dark));border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 14px;font-size:1.2rem;color:#fff;box-shadow:var(--sh-gold)}
.kpi-card h3{font-size:.93rem;font-weight:700;color:#fff;margin-bottom:8px}
.kpi-card p{color:#888;font-size:.79rem;line-height:1.6}

/* PRODUCTS */
.section-products{background:var(--ivory)}
.product-tabs{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;margin-bottom:40px}
.tab-btn{padding:8px 20px;border:2px solid var(--light-gray);background:transparent;border-radius:30px;font-size:.82rem;font-weight:600;color:var(--mid-gray);cursor:pointer;transition:all var(--tr);font-family:var(--font)}
.tab-btn:hover,.tab-btn.active{background:var(--gold);border-color:var(--gold);color:#fff}
.products-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:26px;margin-bottom:48px}
.product-card{background:#fff;border-radius:14px;overflow:hidden;box-shadow:var(--sh-sm);transition:all var(--tr);border:1px solid rgba(201,146,10,.1)}
.product-card:hover{transform:translateY(-6px);box-shadow:var(--sh-md);border-color:var(--gold)}
.product-img-wrap{position:relative;height:210px;overflow:hidden;background:var(--ivory)}
.product-img-wrap img{width:100%;height:100%;object-fit:cover;transition:transform .5s}
.product-card:hover .product-img-wrap img{transform:scale(1.06)}
.product-category-tag{position:absolute;top:11px;left:11px;background:var(--gold);color:#fff;font-size:.65rem;font-weight:700;letter-spacing:.1em;text-transform:uppercase;padding:3px 10px;border-radius:20px}
.product-feat-tag{position:absolute;top:11px;right:11px;background:rgba(28,28,28,.88);color:#F0C040;font-size:.65rem;font-weight:700;padding:3px 10px;border-radius:20px;display:flex;align-items:center;gap:4px}
.product-card-body{padding:18px 18px 14px}
.product-name{font-size:1rem;font-weight:700;color:var(--charcoal);margin-bottom:7px;line-height:1.3}
.product-desc{color:var(--mid-gray);font-size:.82rem;line-height:1.6;margin-bottom:14px}
.product-card-footer{display:flex;align-items:center;justify-content:space-between;gap:8px;border-top:1px solid rgba(201,146,10,.08);padding-top:11px}
.btn-product-detail{display:inline-flex;align-items:center;gap:5px;color:var(--gold-dark);font-size:.81rem;font-weight:700;transition:all var(--tr)}
.btn-product-detail:hover{gap:10px;color:var(--gold)}
.btn-product-quote{width:36px;height:36px;background:linear-gradient(135deg,var(--wa-mid),var(--wa-deep));color:#a8e6c0;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:.95rem;transition:all var(--tr);box-shadow:0 3px 12px rgba(12,61,46,.3);border:1px solid rgba(201,146,10,.2)}
.btn-product-quote:hover{transform:scale(1.12);color:#fff}
.products-cta-wrap{text-align:center}

/* WHY */
.section-why{background:var(--cream)}
.why-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:26px}
.why-card{background:#fff;border-radius:14px;padding:30px 22px;border:1px solid rgba(201,146,10,.12);transition:all var(--tr);text-align:center}
.why-card:hover{border-color:var(--gold);transform:translateY(-6px);box-shadow:var(--sh-md)}
.why-icon{width:60px;height:60px;background:rgba(201,146,10,.09);border:2px solid rgba(201,146,10,.22);border-radius:14px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:1.35rem;color:var(--gold);transition:all var(--tr)}
.why-card:hover .why-icon{background:var(--gold);color:#fff;border-color:var(--gold)}
.why-card h3{font-size:1rem;font-weight:700;margin-bottom:9px}
.why-card p{color:var(--mid-gray);font-size:.84rem;line-height:1.6}

/* PROCESS */
.section-process{background:var(--ivory)}
.process-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
.process-card{position:relative;background:#fff;border:1px solid rgba(201,146,10,.14);border-radius:14px;padding:28px 20px;text-align:center;transition:all var(--tr)}
.process-card:hover{transform:translateY(-6px);box-shadow:var(--sh-md);border-color:var(--gold)}
.process-step-num{font-size:.72rem;font-weight:800;color:rgba(201,146,10,.4);letter-spacing:.12em;margin-bottom:10px}
.process-icon{width:54px;height:54px;margin:0 auto 15px;border-radius:50%;background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:#fff;display:flex;align-items:center;justify-content:center;font-size:1.2rem;box-shadow:var(--sh-gold)}
.process-card h3{font-size:.98rem;font-weight:700;margin-bottom:7px}
.process-card p{color:var(--mid-gray);font-size:.81rem;line-height:1.6}
.process-connector{display:none;position:absolute;top:50%;right:-30px;transform:translateY(-50%);color:rgba(201,146,10,.35);font-size:1.1rem}
@media(min-width:1025px){.process-connector{display:block}}

/* INDUSTRIES */
.section-industries{background:var(--cream)}
.industry-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:22px}
.industry-card{position:relative;background:#fff;border:1px solid rgba(201,146,10,.15);border-radius:14px;padding:30px 22px 26px;text-align:center;transition:all var(--tr);overflow:hidden;text-decoration:none}
.industry-card::before{content:'';position:absolute;inset:0;background:linear-gradient(135deg,var(--gold),var(--gold-dark));opacity:0;transition:opacity var(--tr);z-index:0}
.industry-card:hover::before{opacity:1}
.industry-card>*{position:relative;z-index:1}
.industry-card:hover h3,.industry-card:hover p{color:#fff}
.industry-card:hover .industry-icon{color:#fff}
.industry-icon{font-size:2.4rem;color:var(--gold);margin-bottom:14px;transition:color var(--tr);display:flex;align-items:center;justify-content:center;height:54px}
.industry-card h3{font-size:1.02rem;font-weight:700;margin-bottom:7px;color:var(--charcoal);transition:color var(--tr)}
.industry-card p{color:var(--mid-gray);font-size:.81rem;line-height:1.6;transition:color var(--tr)}
.industry-arrow{display:flex;align-items:center;justify-content:center;width:28px;height:28px;background:rgba(201,146,10,.12);border-radius:50%;margin:12px auto 0;color:var(--gold);font-size:.75rem;transition:all var(--tr)}
.industry-card:hover .industry-arrow{background:rgba(255,255,255,.2);color:#fff}

/* TESTIMONIALS */
.section-testimonials{background:linear-gradient(160deg,#0f0f0f,#1a1a1a 50%,#0d0d0d);padding:90px 0 70px;position:relative;overflow:hidden}
.testi-bg-decor{position:absolute;inset:0;background:radial-gradient(ellipse 700px 400px at 10% 50%,rgba(201,146,10,.06),transparent 70%),radial-gradient(ellipse 500px 300px at 90% 20%,rgba(201,146,10,.04),transparent 70%);pointer-events:none}
.testi-carousel-wrap{position:relative;max-width:920px;margin:0 auto 40px;padding:0 56px}
.testi-track-outer{overflow:hidden;border-radius:20px}
.testi-track{display:flex;transition:transform .58s cubic-bezier(.4,0,.2,1);will-change:transform}
.testi-slide{flex:0 0 100%;min-width:100%;padding:4px}
.testi-card{background:linear-gradient(145deg,#1e1e1e,#161616);border:1px solid rgba(201,146,10,.18);border-radius:20px;padding:44px 48px 40px;position:relative;box-shadow:0 20px 60px rgba(0,0,0,.4),inset 0 1px 0 rgba(255,255,255,.04)}
.testi-quote-icon{position:absolute;top:28px;right:36px;font-size:4rem;color:rgba(201,146,10,.07);line-height:1;pointer-events:none}
.testi-stars-row{display:flex;align-items:center;gap:4px;margin-bottom:20px}
.testi-stars-row .fa-star,.testi-stars-row .fa-star-half-alt{color:var(--gold);font-size:1rem}
.testi-rating-num{font-size:.78rem;font-weight:700;color:var(--gold);margin-left:8px;background:rgba(201,146,10,.12);padding:2px 8px;border-radius:20px;border:1px solid rgba(201,146,10,.25)}
.testi-text{font-size:1.05rem;color:#ccc;line-height:1.8;font-style:italic;margin-bottom:32px;position:relative;z-index:1}
.testi-author-row{display:flex;align-items:center;gap:16px;border-top:1px solid rgba(255,255,255,.06);padding-top:24px}
.testi-avatar{width:52px;height:52px;background:linear-gradient(135deg,var(--gold),var(--gold-dark));border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1rem;font-weight:800;color:#fff;flex-shrink:0;box-shadow:0 4px 16px rgba(201,146,10,.35);border:2px solid rgba(201,146,10,.4)}
.testi-author-info{flex:1}
.testi-author-info strong{display:block;font-size:1rem;font-weight:700;color:#fff;margin-bottom:2px}
.testi-author-info span{font-size:.8rem;color:#777}
.testi-verified{display:flex;align-items:center;gap:5px;font-size:.72rem;font-weight:600;color:#4ade80;background:rgba(74,222,128,.08);border:1px solid rgba(74,222,128,.2);padding:4px 10px;border-radius:20px;white-space:nowrap}
.testi-nav{position:absolute;top:50%;transform:translateY(-50%);width:44px;height:44px;border-radius:50%;border:2px solid rgba(201,146,10,.3);background:rgba(201,146,10,.08);color:var(--gold);font-size:.9rem;cursor:pointer;display:flex;align-items:center;justify-content:center;transition:all .3s;z-index:10}
.testi-nav:hover{background:var(--gold);border-color:var(--gold);color:#fff;transform:translateY(-50%) scale(1.1);box-shadow:0 6px 20px rgba(201,146,10,.4)}
.testi-prev{left:0}.testi-next{right:0}
.testi-dots{display:flex;justify-content:center;gap:8px;margin-top:28px}
.testi-dot{width:8px;height:8px;border-radius:50%;border:none;background:rgba(255,255,255,.15);cursor:pointer;transition:all .35s;padding:0}
.testi-dot.active{background:var(--gold);width:28px;border-radius:4px;box-shadow:0 2px 10px rgba(201,146,10,.5)}
.testi-progress{height:2px;background:rgba(255,255,255,.08);border-radius:2px;margin-top:16px;overflow:hidden}
.testi-progress-bar{height:100%;background:linear-gradient(90deg,var(--gold),var(--gold-light));border-radius:2px;transition:width .1s linear}
.testi-trust-strip{display:flex;justify-content:center;align-items:center;background:rgba(255,255,255,.03);border:1px solid rgba(201,146,10,.12);border-radius:14px;padding:20px 32px;flex-wrap:wrap;gap:0;row-gap:16px}
.trust-strip-item{display:flex;align-items:center;gap:10px;padding:0 28px}
.trust-strip-item i{font-size:1.3rem;color:var(--gold)}
.trust-strip-item strong{display:block;font-size:1.1rem;font-weight:800;color:#fff}
.trust-strip-item span{font-size:.75rem;color:#777}
.trust-strip-divider{width:1px;height:40px;background:rgba(255,255,255,.08);flex-shrink:0}

/* FAQ */
.section-faq{background:var(--ivory)}
.faq-list{max-width:760px;margin:0 auto;display:flex;flex-direction:column;gap:10px}
.faq-item{background:#fff;border:1px solid rgba(201,146,10,.15);border-radius:12px;overflow:hidden;transition:border-color var(--tr)}
.faq-item.open{border-color:var(--gold)}
.faq-question{width:100%;display:flex;align-items:center;justify-content:space-between;gap:16px;background:none;border:none;padding:17px 22px;text-align:left;font-family:var(--font);font-size:.91rem;font-weight:700;color:var(--charcoal);cursor:pointer;transition:color var(--tr)}
.faq-item.open .faq-question{color:var(--gold-dark)}
.faq-toggle-icon{color:var(--gold);transition:transform var(--tr);flex-shrink:0}
.faq-item.open .faq-toggle-icon{transform:rotate(45deg)}
.faq-answer{max-height:0;overflow:hidden;transition:max-height .38s ease,padding .38s ease;padding:0 22px}
.faq-item.open .faq-answer{max-height:220px;padding:0 22px 20px}
.faq-answer p{color:var(--mid-gray);font-size:.86rem;line-height:1.7;margin:0}

/* CTA BAND */
.section-cta-band{background:linear-gradient(135deg,#1a1a1a,#0d0d0d);padding:80px 0;border-top:3px solid var(--gold)}
.cta-band-grid{display:grid;grid-template-columns:1fr 1fr;gap:60px;align-items:center}
.cta-band-content p{color:#aaa;margin:16px 0 22px;font-size:.98rem;line-height:1.7}
.form-toast{display:flex;align-items:flex-start;gap:14px;padding:18px 22px;border-radius:14px;margin-bottom:18px;animation:toastIn .45s var(--tr);position:relative}
@keyframes toastIn{from{opacity:0;transform:translateY(-18px) scale(.96)}to{opacity:1;transform:none}}
.form-toast-success{background:rgba(47,143,107,.12);border:1px solid rgba(47,143,107,.28);border-left:5px solid #2f8f6b}
.form-toast-error{background:rgba(231,76,60,.12);border:1px solid rgba(231,76,60,.28);border-left:5px solid #e74c3c}
.toast-icon{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1.1rem;flex-shrink:0}
.form-toast-success .toast-icon{background:linear-gradient(135deg,#2f8f6b,#145c43)}
.form-toast-error .toast-icon{background:linear-gradient(135deg,#e74c3c,#c0392b)}
.toast-text strong{display:block;font-size:.92rem;font-weight:800;margin-bottom:3px}
.form-toast-success .toast-text strong{color:#d4f5e9}
.form-toast-error .toast-text strong{color:#fde8e8}
.toast-text span{font-size:.82rem;color:rgba(255,255,255,.7);line-height:1.5}
.toast-close{position:absolute;top:10px;right:14px;background:none;border:none;color:rgba(255,255,255,.5);font-size:1.2rem;cursor:pointer;transition:color .2s}
.toast-close:hover{color:#fff}

/* WHATSAPP */
.cta-contact-items{display:flex;flex-direction:column;gap:10px}
.cta-whatsapp-premium{display:flex;align-items:center;gap:14px;background:linear-gradient(135deg,var(--wa-mid),var(--wa-deep));border-radius:14px;padding:15px 20px;transition:all .4s var(--tr);box-shadow:0 6px 26px rgba(12,61,46,.45);border:1px solid rgba(201,146,10,.28);text-decoration:none}
.cta-whatsapp-premium:hover{transform:translateY(-3px) scale(1.015);box-shadow:0 10px 34px rgba(12,61,46,.6);border-color:var(--gold)}
.whatsapp-icon-wrap{width:46px;height:46px;background:rgba(201,146,10,.18);border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:1.5rem;color:var(--gold-light);flex-shrink:0;border:2px solid rgba(201,146,10,.28)}
.whatsapp-text{flex:1}
.whatsapp-text span{display:block;font-size:.68rem;font-weight:500;color:rgba(255,255,255,.6);text-transform:uppercase;letter-spacing:.08em}
.whatsapp-text strong{display:block;font-size:1.05rem;font-weight:800;color:#fff}
.whatsapp-arrow{width:36px;height:36px;background:rgba(201,146,10,.15);border-radius:50%;display:flex;align-items:center;justify-content:center;color:var(--gold-light);font-size:.85rem;transition:all .35s}
.cta-whatsapp-premium:hover .whatsapp-arrow{transform:translateX(5px)}
.cta-contact-item{display:flex;align-items:center;gap:12px;background:rgba(201,146,10,.08);border:1px solid rgba(201,146,10,.2);border-radius:10px;padding:13px 16px;transition:all var(--tr);text-decoration:none}
.cta-contact-item:hover{border-color:var(--gold);background:rgba(201,146,10,.14)}
.cta-contact-item i{font-size:1.1rem;color:var(--gold)}
.cta-contact-item span{display:block;font-size:.7rem;color:#888;text-transform:uppercase;letter-spacing:.08em}
.cta-contact-item strong{color:#fff;font-size:.9rem}

/* FORM */
.cta-band-form{background:var(--cream);border-radius:18px;padding:36px;border:2px solid rgba(201,146,10,.2);box-shadow:0 12px 48px rgba(0,0,0,.15)}
.cta-band-form h3{font-size:1.25rem;font-weight:700;color:var(--charcoal);margin-bottom:22px;display:flex;align-items:center;gap:8px;padding-bottom:14px;border-bottom:2px solid var(--ivory)}
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.form-group{margin-bottom:12px}
.form-group input,.form-group textarea{width:100%;padding:12px 15px;border:1.5px solid var(--light-gray);border-radius:9px;font-family:var(--font);font-size:.88rem;color:var(--charcoal);background:#fff;transition:border-color var(--tr),box-shadow var(--tr);outline:none}
.form-group input:focus,.form-group textarea:focus{border-color:var(--gold);box-shadow:0 0 0 3px rgba(201,146,10,.1)}
.form-group input.input-error{border-color:#e74c3c;box-shadow:0 0 0 3px rgba(231,76,60,.1)}
.form-group textarea{resize:vertical;min-height:98px}
.btn-quote{width:100%;padding:14px;background:linear-gradient(135deg,var(--gold),var(--gold-dark));color:#fff;border:none;border-radius:10px;font-size:.95rem;font-weight:700;font-family:var(--font);display:flex;align-items:center;justify-content:center;gap:8px;cursor:pointer;transition:all var(--tr);box-shadow:var(--sh-gold);margin-top:4px}
.btn-quote:hover{transform:translateY(-2px);box-shadow:0 10px 28px rgba(201,146,10,.45)}
.btn-quote:disabled{opacity:.68;cursor:not-allowed;transform:none}
.form-note{text-align:center;font-size:.7rem;color:#999;margin-top:10px}
.form-note i{color:var(--gold);margin-right:4px}

/* RESPONSIVE */
@media(max-width:1024px){
    .kpi-grid{grid-template-columns:repeat(2,1fr);max-width:680px;margin:0 auto}
    .why-grid,.industry-grid{grid-template-columns:repeat(2,1fr)}
    .process-grid{grid-template-columns:repeat(2,1fr)}
    .products-grid{grid-template-columns:repeat(2,1fr)}
    .about-intro-grid,.cta-band-grid{grid-template-columns:1fr;gap:40px}
    .stats-inner{grid-template-columns:repeat(2,1fr)}
    .testi-trust-strip{gap:0}
    .trust-strip-item{padding:0 16px}
}
@media(max-width:768px){
    .testi-carousel-wrap{padding:0 44px}
    .testi-card{padding:28px 22px}
    .testi-verified{display:none}
    .testi-trust-strip{flex-direction:column;gap:16px}
    .trust-strip-divider{width:60px;height:1px}
}
@media(max-width:640px){
    .section{padding:52px 0}
    .kpi-grid{grid-template-columns:1fr;max-width:340px;margin:0 auto}
    .why-grid,.industry-grid,.products-grid,.process-grid{grid-template-columns:1fr}
    .hero-headline{font-size:1.65rem}
    .form-row{grid-template-columns:1fr}
    .hero-ctas{flex-direction:column}
    .about-img-secondary{display:none}
    .testi-carousel-wrap{padding:0 36px}
    .testi-card{padding:22px 16px}
    .testi-text{font-size:.92rem}
}
@media(prefers-reduced-motion:reduce){
    .testi-track,.gear{animation:none;transition:none}
}

/* ===== MADE IN INDIA ===== */

.made-india-section{
    padding:90px 0;
    background:#111;
    position:relative;
    overflow:hidden;
}

.made-india-section::before{
    content:'';
    position:absolute;
    width:500px;
    height:500px;
    background:rgba(201,146,10,0.08);
    border-radius:50%;
    top:-200px;
    right:-150px;
    filter:blur(100px);
}

.made-india-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:60px;
    align-items:center;
}

.made-india-video{
    position:relative;
    border-radius:20px;
    overflow:hidden;
    border:2px solid rgba(201,146,10,.4);
    box-shadow:0 0 40px rgba(201,146,10,.2);
}

.made-india-video video{
    width:100%;
    height:500px;
    object-fit:cover;
    display:block;
}

.video-badge{
    position:absolute;
    top:20px;
    left:20px;
    z-index:2;
    background:#C9920A;
    color:#fff;
    padding:10px 18px;
    border-radius:50px;
    font-size:13px;
    font-weight:700;
    letter-spacing:1px;
}

.made-india-content h2{
    color:#fff;
    font-size:48px;
    line-height:1.2;
    margin-bottom:20px;
    font-weight:800;
}

.made-india-content h2 span{
    color:#C9920A;
}

.made-india-content p{
    color:#bdbdbd;
    margin-bottom:18px;
    line-height:1.8;
    font-size:16px;
}

.india-features{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:15px;
    margin:30px 0;
}

.india-features div{
    color:#fff;
    font-size:15px;
    font-weight:600;
}

.india-features i{
    color:#C9920A;
    margin-right:8px;
}

.india-btn{
    display:inline-flex;
    align-items:center;
    gap:10px;
    background:linear-gradient(135deg,#C9920A,#a67a08);
    color:#fff;
    padding:14px 28px;
    border-radius:8px;
    font-weight:700;
    transition:.3s;
}

.india-btn:hover{
    transform:translateY(-3px);
    box-shadow:0 10px 25px rgba(201,146,10,.35);
}

@media(max-width:991px){

    .made-india-grid{
        grid-template-columns:1fr;
    }

    .made-india-content h2{
        font-size:34px;
    }

    .made-india-video video{
        height:350px;
    }

    .india-features{
        grid-template-columns:1fr;
    }
}
</style>

<script>
(function(){

/* ── Product Tabs ─────────────── */
document.querySelectorAll('.tab-btn').forEach(btn=>{
    btn.addEventListener('click',()=>{
        document.querySelectorAll('.tab-btn').forEach(t=>t.classList.remove('active'));
        btn.classList.add('active');
        const f=btn.dataset.filter;
        document.querySelectorAll('.product-card').forEach(c=>{
            c.style.display=(f==='all'||c.dataset.category===f)?'':'none';
        });
    });
});

/* ── Scroll Reveal ────────────── */
const obs=new IntersectionObserver(entries=>{
    entries.forEach(e=>{if(e.isIntersecting){e.target.classList.add('rv');obs.unobserve(e.target);}});
},{threshold:0.08});
document.querySelectorAll('.product-card,.why-card,.kpi-card,.industry-card,.stat-card,.process-card,.faq-item').forEach(el=>{
    el.style.opacity='0';el.style.transform='translateY(22px)';el.style.transition='opacity .5s ease,transform .5s ease';
    obs.observe(el);
});
const ss=document.createElement('style');
ss.textContent='.rv{opacity:1!important;transform:none!important}';
document.head.appendChild(ss);

/* ── FAQ Accordion ────────────── */
document.querySelectorAll('.faq-question').forEach(btn=>{
    btn.addEventListener('click',()=>{
        const item=btn.closest('.faq-item');
        const open=item.classList.contains('open');
        document.querySelectorAll('.faq-item.open').forEach(i=>i.classList.remove('open'));
        if(!open) item.classList.add('open');
    });
});

/* ── Toast auto-hide ──────────── */
const toast=document.getElementById('formToast');
if(toast){
    setTimeout(()=>{
        toast.style.transition='opacity .4s,transform .4s';
        toast.style.opacity='0';toast.style.transform='translateY(-10px)';
        setTimeout(()=>toast.remove(),400);
    },8000);
}

/* ── Auto-scroll to form on success ── */
if(window.location.search.includes('submitted=1')){
    setTimeout(()=>{
        document.getElementById('contact-form')?.scrollIntoView({behavior:'smooth',block:'center'});
    },350);
}

/* ── Form validation + loading state ── */
const form=document.getElementById('quickForm');
const sbtn=document.getElementById('quoteSubmitBtn');
const sico=document.getElementById('submitIcon');
const slbl=document.getElementById('submitLabel');

if(form && sbtn){
    form.addEventListener('submit', function(e) {
        let ok = true;
        
        // Clear previous errors
        form.querySelectorAll('.input-error').forEach(el => el.classList.remove('input-error'));
        
        // Validate required fields
        form.querySelectorAll('input[required]').forEach(inp => {
            if(!inp.value.trim()) {
                inp.classList.add('input-error');
                ok = false;
            }
        });
        
        // Validate email
        const em = form.querySelector('input[name="email"]');
        if(em && em.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(em.value)) {
            em.classList.add('input-error');
            ok = false;
        }
        
        if(!ok) {
            e.preventDefault();
            const firstError = form.querySelector('.input-error');
            if(firstError) firstError.focus();
            return;
        }
        
        // Show loading state
        sbtn.disabled = true;
        if(slbl) slbl.textContent = 'Sending...';
        if(sico) sico.className = 'fas fa-spinner fa-spin';
    });
    
    // Remove error on input
    form.querySelectorAll('input, textarea').forEach(inp => {
        inp.addEventListener('input', function() {
            this.classList.remove('input-error');
        });
    });
}

/* ═══════════════════════════════════════
   TESTIMONIALS CAROUSEL
═══════════════════════════════════════ */
const track   = document.getElementById('testiTrack');
const dots    = document.querySelectorAll('.testi-dot');
const progBar = document.getElementById('testiProgress');
const prevBtn = document.querySelector('.testi-prev');
const nextBtn = document.querySelector('.testi-next');

if(track) {
    const total    = document.querySelectorAll('.testi-slide').length;
    let cur        = 0;
    let autoTimer  = null;
    let progTimer  = null;
    let progVal    = 0;
    const INTERVAL = 5000;
    const STEP     = 100 / (INTERVAL / 80);

    function goTo(n){
        cur=(n+total)%total;
        track.style.transform=`translateX(-${cur*100}%)`;
        dots.forEach((d,i)=>d.classList.toggle('active',i===cur));
        resetProg();
    }
    
    function resetProg(){
        clearInterval(progTimer);
        progVal=0;
        if(progBar) progBar.style.width='0%';
        progTimer=setInterval(()=>{
            progVal+=STEP;
            if(progBar) progBar.style.width=Math.min(progVal,100)+'%';
            if(progVal>=100) clearInterval(progTimer);
        },80);
    }
    
    function startAuto(){ clearInterval(autoTimer); autoTimer=setInterval(()=>goTo(cur+1),INTERVAL); }
    function stopAuto() { clearInterval(autoTimer); clearInterval(progTimer); }

    prevBtn?.addEventListener('click',()=>{stopAuto();goTo(cur-1);startAuto();});
    nextBtn?.addEventListener('click',()=>{stopAuto();goTo(cur+1);startAuto();});
    dots.forEach((d,i)=>d.addEventListener('click',()=>{stopAuto();goTo(i);startAuto();}));

    // Swipe
    let tx=0,ty=0;
    track.addEventListener('touchstart',e=>{tx=e.changedTouches[0].clientX;ty=e.changedTouches[0].clientY;},{passive:true});
    track.addEventListener('touchend',e=>{
        const dx=e.changedTouches[0].clientX-tx;
        const dy=e.changedTouches[0].clientY-ty;
        if(Math.abs(dx)>Math.abs(dy)&&Math.abs(dx)>40){stopAuto();goTo(dx<0?cur+1:cur-1);startAuto();}
    },{passive:true});

    // Keyboard
    document.addEventListener('keydown',e=>{
        if(e.key==='ArrowLeft'){stopAuto();goTo(cur-1);startAuto();}
        if(e.key==='ArrowRight'){stopAuto();goTo(cur+1);startAuto();}
    });

    // Hover pause
    const wrap=document.querySelector('.testi-carousel-wrap');
    wrap?.addEventListener('mouseenter',stopAuto);
    wrap?.addEventListener('mouseleave',startAuto);

    goTo(0);
    startAuto();
}

})();
</script>
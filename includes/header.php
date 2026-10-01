<?php
// includes/header.php - Premium Frontend Header with Announcement Strip
require_once __DIR__ . '/db.php';

$current_page = basename($_SERVER['PHP_SELF'], '.php');
$is_products_page = in_array($current_page, ['products', 'product-detail']);
$is_home_page     = ($current_page === 'index');
$is_about_page    = ($current_page === 'about');
$is_contact_page  = ($current_page === 'contact');
$is_gallery_page  = ($current_page === 'gallery');

$base_url = rtrim(env('APP_URL', ''), '/');

// ============================================================
// FETCH SITE SETTINGS
// ============================================================
$site_settings = [];
if (isset($conn) && $conn) {
    try {
        $res = $conn->query("SELECT * FROM site_settings WHERE id = 1 LIMIT 1");
        if ($res && $res->num_rows > 0) {
            $site_settings = $res->fetch_assoc();
        }
    } catch (Exception $e) {
        $site_settings = [];
    }
}

// ============================================================
// FETCH ACTIVE CATEGORIES
// ============================================================
$categories = [];
$pinned_categories = [];
if (isset($conn) && $conn) {
    try {
        // Check if columns exist
        $chk1 = $conn->query("SHOW COLUMNS FROM categories LIKE 'show_in_menu'");
        $chk2 = $conn->query("SHOW COLUMNS FROM categories LIKE 'pin_to_menu'");
        $hasShowInMenu = ($chk1 && $chk1->num_rows > 0);
        $hasPinToMenu = ($chk2 && $chk2->num_rows > 0);
        
        // Fetch all active categories
        $sql = "SELECT * FROM categories WHERE status='active' ORDER BY sort_order ASC, name ASC";
        $res = $conn->query($sql);
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $categories[] = $r;
                
                // Collect pinned categories
                if ($hasPinToMenu && ($r['pin_to_menu'] ?? 0) == 1) {
                    $pinned_categories[] = $r;
                }
            }
        }
    } catch (Exception $e) {
        $categories = [];
        $pinned_categories = [];
    }
}

// ============================================================
// BUILD DROPDOWN ITEMS FROM CATEGORIES (show_in_menu = 1)
// ============================================================
$product_dropdown = [];
foreach ($categories as $cat) {
    // Only show in dropdown if show_in_menu = 1
    if (($cat['show_in_menu'] ?? 1) == 1) {
        $product_dropdown[] = [
            'href'  => 'products.php?category=' . urlencode($cat['name']),
            'label' => $cat['name'],
            'icon'  => !empty($cat['icon']) ? $cat['icon'] : 'fa-tag',
        ];
    }
}

// Fallback removed to rely entirely on dynamic database categories

// ============================================================
// FETCH ANNOUNCEMENT STRIPS
// ============================================================
$announcement_strips = [];
if (isset($conn) && $conn) {
    try {
        $sql = "SELECT * FROM announcement_strips WHERE status='active' ORDER BY sort_order ASC, id DESC LIMIT 1";
        $res = $conn->query($sql);
        if ($res) {
            while ($r = $res->fetch_assoc()) {
                $announcement_strips[] = $r;
            }
        }
    } catch (Exception $e) {
        $announcement_strips = [];
    }
}

// Logo path helper
function getLogoPath() {
    $paths = [
        __DIR__ . '/../admin/images/bg_logo.png',
        __DIR__ . '/../admin/images/logo.png',
        __DIR__ . '/../assets/images/logo.png',
        __DIR__ . '/../assets/images/logo.jpg',
        __DIR__ . '/../assets/images/logo.webp',
        __DIR__ . '/../assets/images/logo.svg',
        __DIR__ . '/../images/logo.png',
    ];
    foreach ($paths as $p) {
        if (file_exists($p)) {
            $rel = str_replace(__DIR__ . '/..', '', $p);
            return ltrim($rel, '/');
        }
    }
    return false;
}
$logo_path = getLogoPath();

// ============================================================
// CHECK IF CURRENT PAGE IS A PINNED CATEGORY
// ============================================================
$current_category = isset($_GET['category']) ? $_GET['category'] : '';
$is_pinned_category_page = false;
foreach ($pinned_categories as $pc) {
    if (strtolower($current_category) == strtolower($pc['name'])) {
        $is_pinned_category_page = true;
        break;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="description" content="<?php echo htmlspecialchars($site_settings['meta_description'] ?? 'Arup Enterprise'); ?>"/>
    <meta name="theme-color" content="#DC2626"/>
    <title><?php echo isset($page_title) ? $page_title . ' | ' . htmlspecialchars($site_settings['company_name'] ?? 'Arup Enterprise') : htmlspecialchars($site_settings['meta_title'] ?? 'Arup Enterprise'); ?></title>

    <link rel="icon" type="image/png" href="assets/images/logo.png">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>

    <style>
    /* =============================================
       RESET - Remove All Unwanted Margins
    ============================================= */
    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
    }

    html, body {
        margin: 0 !important;
        padding: 0 !important;
        overflow-x: hidden !important;
        width: 100% !important;
    }

    :root {
        --gold:        #DC2626;
        --gold-dark:   #B91C1C;
        --gold-light:  #EF4444;
        --gold-glow:   rgba(220,38,38,0.18);
        --charcoal:    #111827;
        --charcoal2:   #1F2937;
        --mid:         #4B5563;
        --lightgray:   #FECACA;
        --white:       #FFFFFF;
        --cream:       #FFF5F5;
        --ivory:       #FEE2E2;
        --header-h:    76px;
        --topbar-h:    36px;
        --font-serif:  'Playfair Display', Georgia, serif;
        --font-sans:   'Inter', system-ui, -apple-system, sans-serif;
        --ease:        0.3s cubic-bezier(0.4,0,0.2,1);
        --shadow-sm:   0 2px 12px rgba(0,0,0,0.07);
        --shadow-md:   0 6px 28px rgba(0,0,0,0.10);
        --shadow-gold: 0 4px 20px rgba(220,38,38,0.28);
    }

    /* =============================================
       TOPBAR - Fully Responsive
    ============================================= */
    .db-topbar {
        background: var(--charcoal);
        border-bottom: 2px solid var(--gold);
        height: var(--topbar-h);
        display: flex;
        align-items: center;
        position: relative;
        z-index: 1001;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .db-topbar-inner {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
        width: 100%;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-sizing: border-box;
    }
    .db-topbar-left, .db-topbar-right {
        display: flex;
        align-items: center;
        gap: 20px;
        flex-wrap: wrap;
    }
    .db-topbar a {
        display: flex;
        align-items: center;
        gap: 6px;
        color: var(--lightgray);
        font-family: var(--font-sans);
        font-size: 0.72rem;
        font-weight: 500;
        text-decoration: none;
        transition: color var(--ease);
        letter-spacing: 0.02em;
        white-space: nowrap;
    }
    .db-topbar a:hover { color: var(--gold-light); }
    .db-topbar a i { color: var(--gold); font-size: 0.78rem; }
    .db-topbar-sep {
        width: 1px;
        height: 14px;
        background: rgba(255,255,255,0.12);
    }
    .db-topbar-badge {
        background: var(--gold);
        color: #fff;
        font-family: var(--font-sans);
        font-size: 0.6rem;
        font-weight: 800;
        padding: 2px 8px;
        border-radius: 20px;
        letter-spacing: 0.08em;
        text-transform: uppercase;
    }

    /* =============================================
       MAIN HEADER - Fully Responsive
    ============================================= */
    .db-header {
        position: sticky;
        top: 0;
        z-index: 1000;
        background: var(--white);
        height: var(--header-h);
        display: flex;
        align-items: center;
        box-shadow: var(--shadow-sm);
        border-bottom: 3px solid var(--gold);
        width: 100%;
        transition: background var(--ease), box-shadow var(--ease), height var(--ease);
        margin: 0;
        padding: 0;
    }
    .db-header.scrolled {
        background: rgba(255,255,255,0.97);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        box-shadow: 0 4px 32px rgba(0,0,0,0.13);
        height: 68px;
    }
    .db-header-inner {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
        width: 100%;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-sizing: border-box;
    }

    /* =============================================
       LOGO
    ============================================= */
    .db-logo {
        display: flex;
        align-items: center;
        gap: 12px;
        text-decoration: none;
        flex-shrink: 0;
    }
    .db-logo-img-wrap {
        height: 58px;
        display: flex;
        align-items: center;
        justify-content: flex-start;
        flex-shrink: 0;
    }
    .db-logo-img-wrap img {
        max-height: 100%;
        max-width: 250px;
        width: auto;
        object-fit: contain;
        display: block;
        transition: transform var(--ease);
    }
    .db-logo:hover .db-logo-img-wrap img { transform: scale(1.04); }
    @media (max-width: 768px) {
        .db-logo-img-wrap {
            height: 44px;
        }
        .db-logo-img-wrap img {
            max-width: 180px;
        }
    }

    .db-logo-text-wrap {
        display: flex;
        flex-direction: column;
        line-height: 1;
        gap: 3px;
    }
    .db-logo-name {
        font-family: var(--font-serif);
        font-size: 1.55rem;
        font-weight: 900;
        color: var(--charcoal);
        letter-spacing: -0.02em;
        line-height: 1;
    }
    .db-logo-name em { color: var(--gold); font-style: normal; }
    .db-logo-tag {
        font-family: var(--font-sans);
        font-size: 0.58rem;
        font-weight: 700;
        letter-spacing: 0.2em;
        text-transform: uppercase;
        color: var(--mid);
        line-height: 1;
    }

    .db-logo-icon {
        width: 54px;
        height: 54px;
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        border-radius: 13px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: var(--shadow-gold);
        transition: transform var(--ease), box-shadow var(--ease);
    }
    .db-logo:hover .db-logo-icon {
        transform: rotate(-6deg) scale(1.05);
        box-shadow: 0 8px 28px rgba(239,68,68,0.42);
    }
    .db-logo-icon svg { width: 28px; height: 28px; fill: #fff; }

    /* =============================================
       NAVIGATION
    ============================================= */
    .db-nav {
        display: flex;
        align-items: center;
        gap: 2px;
        flex: 1;
        justify-content: center;
        flex-wrap: wrap;
    }
    .db-nav-item { position: relative; }

    .db-nav-link {
        display: flex;
        align-items: center;
        gap: 5px;
        padding: 9px 15px;
        font-family: var(--font-sans);
        font-size: 0.855rem;
        font-weight: 600;
        color: var(--charcoal);
        border-radius: 8px;
        text-decoration: none;
        transition: all var(--ease);
        white-space: nowrap;
        position: relative;
    }
    .db-nav-link::after {
        content: '';
        position: absolute;
        bottom: 4px;
        left: 15px;
        right: 15px;
        height: 2px;
        background: var(--gold);
        border-radius: 2px;
        transform: scaleX(0);
        transform-origin: left;
        transition: transform var(--ease);
    }
    .db-nav-link:hover::after,
    .db-nav-link.active::after { transform: scaleX(1); }
    .db-nav-link:hover,
    .db-nav-link.active {
        color: var(--gold-dark);
        background: var(--ivory);
    }
    .db-nav-link .chev {
        font-size: 0.6rem;
        color: var(--gold);
        transition: transform var(--ease);
    }
    .db-nav-item:hover .db-nav-link .chev { transform: rotate(180deg); }
    .db-nav-link .pin-indicator {
        font-size: 0.5rem;
        color: var(--gold);
        margin-left: 2px;
    }

    /* =============================================
       DROPDOWN
    ============================================= */
    .db-dropdown {
        position: absolute;
        top: calc(100% + 8px);
        left: 50%;
        transform: translateX(-50%) translateY(-10px);
        background: var(--white);
        border: 1px solid rgba(239,68,68,0.15);
        border-top: 3px solid var(--gold);
        border-radius: 14px;
        box-shadow: var(--shadow-md);
        min-width: 240px;
        opacity: 0;
        visibility: hidden;
        transition: all var(--ease);
        z-index: 999;
        overflow: hidden;
    }
    .db-nav-item:hover .db-dropdown {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(0);
    }
    .db-dropdown-head {
        padding: 10px 16px 8px;
        font-family: var(--font-sans);
        font-size: 0.6rem;
        font-weight: 800;
        color: var(--gold);
        letter-spacing: 0.2em;
        text-transform: uppercase;
        border-bottom: 1px solid var(--ivory);
    }
    .db-dropdown-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        font-family: var(--font-sans);
        font-size: 0.83rem;
        font-weight: 500;
        color: var(--mid);
        text-decoration: none;
        transition: all var(--ease);
        border-bottom: 1px solid rgba(240,232,208,0.6);
    }
    .db-dropdown-item:last-child { border-bottom: none; }
    .db-dropdown-item .ddi-icon {
        width: 30px;
        height: 30px;
        background: var(--gold-glow);
        border-radius: 7px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.75rem;
        color: var(--gold);
        flex-shrink: 0;
        transition: all var(--ease);
    }
    .db-dropdown-item:hover {
        background: var(--ivory);
        color: var(--gold-dark);
        padding-left: 22px;
    }
    .db-dropdown-item:hover .ddi-icon {
        background: var(--gold);
        color: #fff;
    }
    .db-dropdown-divider { height: 1px; background: var(--ivory); margin: 4px 0; }
    .db-dropdown-all {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 10px 16px;
        font-family: var(--font-sans);
        font-size: 0.78rem;
        font-weight: 700;
        color: var(--gold-dark);
        text-decoration: none;
        background: linear-gradient(135deg, var(--ivory), var(--cream));
        transition: all var(--ease);
    }
    .db-dropdown-all:hover { background: var(--gold); color: #fff; }
    .db-dropdown-all i { font-size: 0.7rem; transition: transform var(--ease); }
    .db-dropdown-all:hover i { transform: translateX(4px); }

    /* =============================================
       HEADER ACTIONS
    ============================================= */
    .db-header-actions {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-shrink: 0;
    }
    .db-btn-call {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        border: 2px solid rgba(239,68,68,0.35);
        background: transparent;
        color: var(--charcoal);
        font-family: var(--font-sans);
        font-size: 0.78rem;
        font-weight: 700;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        transition: all var(--ease);
        white-space: nowrap;
    }
    .db-btn-call i { color: var(--gold); font-size: 0.8rem; }
    .db-btn-call:hover {
        border-color: var(--gold);
        background: var(--ivory);
        color: var(--gold-dark);
    }
    .db-btn-quote {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
        color: #fff;
        font-family: var(--font-sans);
        font-size: 0.8rem;
        font-weight: 700;
        padding: 10px 20px;
        border-radius: 8px;
        text-decoration: none;
        letter-spacing: 0.04em;
        text-transform: uppercase;
        transition: all var(--ease);
        box-shadow: var(--shadow-gold);
        white-space: nowrap;
        border: none;
        cursor: pointer;
    }
    .db-btn-quote:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(239,68,68,0.45);
        color: #fff;
    }

    /* =============================================
       HAMBURGER
    ============================================= */
    .db-hamburger {
        display: none;
        flex-direction: column;
        gap: 5px;
        cursor: pointer;
        padding: 8px;
        background: none;
        border: none;
        border-radius: 8px;
        transition: background var(--ease);
        flex-shrink: 0;
    }
    .db-hamburger:hover { background: var(--ivory); }
    .db-hamburger span {
        display: block;
        width: 24px;
        height: 2px;
        background: var(--charcoal);
        border-radius: 2px;
        transition: all var(--ease);
    }
    .db-hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
    .db-hamburger.open span:nth-child(2) { opacity: 0; transform: scaleX(0); }
    .db-hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

    /* =============================================
       MOBILE OVERLAY
    ============================================= */
    .db-mobile-overlay {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 1098;
        backdrop-filter: blur(3px);
        -webkit-backdrop-filter: blur(3px);
        animation: fadeOverlay .25s ease;
    }
    .db-mobile-overlay.open { display: block; }
    @keyframes fadeOverlay { from { opacity:0; } to { opacity:1; } }

    /* =============================================
       MOBILE NAV
    ============================================= */
    .db-mobile-nav {
        position: fixed;
        top: 0;
        right: -100%;
        width: min(320px, 88vw);
        height: 100dvh;
        background: var(--white);
        border-left: 3px solid var(--gold);
        z-index: 1099;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        transition: right 0.38s cubic-bezier(0.4,0,0.2,1);
        box-shadow: -8px 0 40px rgba(0,0,0,0.18);
    }
    .db-mobile-nav.open { right: 0; }

    .db-mn-head {
        background: #ffffff;
        padding: 20px 20px 18px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        border-bottom: 2px solid var(--gold);
        flex-shrink: 0;
    }
    .db-mn-head .db-mn-logo {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
    }
    .db-mn-logo-img {
        height: 48px;
        width: auto;
        max-width: 150px;
        object-fit: contain;
        display: block;
    }
    .db-mn-logo-text {
        font-family: var(--font-serif);
        font-size: 1.2rem;
        font-weight: 900;
        color: var(--charcoal);
        line-height: 1;
    }
    .db-mn-logo-text em { color: var(--gold); font-style: normal; }
    .db-mn-logo-sub {
        font-family: var(--font-sans);
        font-size: 0.55rem;
        font-weight: 600;
        letter-spacing: 0.18em;
        text-transform: uppercase;
        color: rgba(14,14,14,0.5);
        margin-top: 2px;
        display: block;
    }
    .db-mn-close {
        width: 36px;
        height: 36px;
        background: rgba(14,14,14,0.05);
        border: 1px solid rgba(14,14,14,0.1);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: rgba(14,14,14,0.7);
        font-size: 0.9rem;
        cursor: pointer;
        transition: all var(--ease);
        flex-shrink: 0;
    }
    .db-mn-close:hover { background: rgba(239,68,68,0.1); color: var(--gold); border-color: var(--gold); }

    .db-mn-body {
        flex: 1;
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        overscroll-behavior: contain;
    }
    .db-mn-body::-webkit-scrollbar { width: 3px; }
    .db-mn-body::-webkit-scrollbar-thumb { background: rgba(239,68,68,0.3); border-radius: 2px; }

    .db-mn-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 15px 22px;
        font-family: var(--font-sans);
        font-size: 0.92rem;
        font-weight: 600;
        color: var(--charcoal);
        border-bottom: 1px solid rgba(240,232,208,0.7);
        text-decoration: none;
        transition: all var(--ease);
        cursor: pointer;
        background: none;
        width: 100%;
        border-left: none;
        border-right: none;
        border-top: none;
        text-align: left;
    }
    .db-mn-link .db-mn-link-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    .db-mn-link .db-mn-icon {
        width: 34px;
        height: 34px;
        background: var(--gold-glow);
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        color: var(--gold);
        flex-shrink: 0;
        transition: all var(--ease);
    }
    .db-mn-link:hover,
    .db-mn-link.mn-active {
        color: var(--gold-dark);
        background: rgba(240,232,208,0.5);
    }
    .db-mn-link:hover .db-mn-icon,
    .db-mn-link.mn-active .db-mn-icon {
        background: var(--gold);
        color: #fff;
    }
    .db-mn-chev {
        font-size: 0.7rem;
        color: var(--gold);
        transition: transform var(--ease);
        flex-shrink: 0;
    }
    .db-mn-product-toggle.open .db-mn-chev { transform: rotate(180deg); }

    .db-mn-sub {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.35s cubic-bezier(0.4,0,0.2,1);
        background: var(--ivory);
    }
    .db-mn-sub.open { max-height: 500px; }
    .db-mn-sub a {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 11px 22px 11px 36px;
        font-family: var(--font-sans);
        font-size: 0.84rem;
        font-weight: 500;
        color: var(--mid);
        border-bottom: 1px solid rgba(213,205,184,0.5);
        text-decoration: none;
        transition: all var(--ease);
    }
    .db-mn-sub a i { color: var(--gold); width: 16px; font-size: 0.78rem; }
    .db-mn-sub a:hover { color: var(--gold-dark); background: rgba(239,68,68,0.07); padding-left: 44px; }
    .db-mn-sub a:last-child { border-bottom: none; }

    .db-mn-footer {
        padding: 16px 20px;
        border-top: 2px solid var(--ivory);
        background: var(--cream);
        flex-shrink: 0;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }
    .db-mn-btn-wa {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        background: linear-gradient(135deg, #25D366, #128C7E);
        color: #fff;
        font-family: var(--font-sans);
        font-size: 0.88rem;
        font-weight: 700;
        padding: 13px;
        border-radius: 10px;
        text-decoration: none;
        transition: all var(--ease);
        box-shadow: 0 4px 16px rgba(37,211,102,0.3);
    }
    .db-mn-btn-wa:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(37,211,102,0.45); }
    .db-mn-btn-quote {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 9px;
        background: linear-gradient(135deg, var(--gold), var(--gold-dark));
        color: #fff;
        font-family: var(--font-sans);
        font-size: 0.85rem;
        font-weight: 700;
        padding: 12px;
        border-radius: 10px;
        text-decoration: none;
        transition: all var(--ease);
        box-shadow: var(--shadow-gold);
    }
    .db-mn-btn-quote:hover { transform: translateY(-2px); box-shadow: 0 8px 28px rgba(239,68,68,0.45); }
    .db-mn-contact-row {
        display: flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
        flex-wrap: wrap;
    }
    .db-mn-contact-row a {
        display: flex;
        align-items: center;
        gap: 5px;
        font-family: var(--font-sans);
        font-size: 0.72rem;
        font-weight: 600;
        color: var(--mid);
        text-decoration: none;
        transition: color var(--ease);
    }
    .db-mn-contact-row a i { color: var(--gold); font-size: 0.75rem; }
    .db-mn-contact-row a:hover { color: var(--gold-dark); }
    .db-mn-contact-row .sep { color: var(--lightgray); font-size: 0.7rem; }

    /* ============================================================
       PREMIUM ANNOUNCEMENT STRIP - FULLY RESPONSIVE
       ============================================================ */
    .db-announcement-strip {
        width: 100%;
        padding: 10px 20px;
        position: relative;
        z-index: 999;
        border-bottom: 2px solid rgba(255,255,255,0.15);
        animation: slideDown 0.6s ease;
        margin: 0;
        display: block;
        box-shadow: 0 2px 15px rgba(0,0,0,0.08);
    }

    @keyframes slideDown {
        from { 
            transform: translateY(-100%); 
            opacity: 0; 
            max-height: 0;
        }
        to { 
            transform: translateY(0); 
            opacity: 1; 
            max-height: 100px;
        }
    }

    .db-announcement-inner {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 15px;
        width: 100%;
        box-sizing: border-box;
    }

    .db-announcement-content {
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
        font-family: var(--font-sans);
        font-size: 0.9rem;
        flex: 1;
    }

    .db-announcement-content i {
        font-size: 1.1rem;
        opacity: 0.9;
        flex-shrink: 0;
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.2); }
    }

    .db-announcement-title {
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        font-size: 0.8rem;
        flex-shrink: 0;
    }

    .db-announcement-message {
        font-weight: 500;
        opacity: 0.95;
        font-size: 0.85rem;
    }

    .db-announcement-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-weight: 700;
        text-decoration: none;
        padding: 5px 14px;
        border: 2px solid;
        border-radius: 25px;
        font-size: 0.75rem;
        transition: all 0.3s ease;
        opacity: 0.95;
        flex-shrink: 0;
        white-space: nowrap;
    }

    .db-announcement-link:hover {
        opacity: 1;
        transform: scale(1.05);
        background: rgba(255,255,255,0.12);
    }

    .db-announcement-close {
        background: none;
        border: none;
        font-size: 1.1rem;
        cursor: pointer;
        opacity: 0.6;
        transition: all 0.3s ease;
        padding: 6px 10px;
        border-radius: 6px;
        flex-shrink: 0;
        line-height: 1;
    }

    .db-announcement-close:hover {
        opacity: 1;
        background: rgba(0,0,0,0.08);
        transform: rotate(90deg);
    }

    /* ============================================================
       RESPONSIVE - PREMIUM ANNOUNCEMENT STRIP
    ============================================================ */
    @media (max-width: 992px) {
        .db-announcement-strip {
            padding: 8px 16px;
        }
        .db-announcement-content {
            font-size: 0.8rem;
            gap: 8px;
        }
        .db-announcement-title {
            font-size: 0.7rem;
        }
        .db-announcement-message {
            font-size: 0.75rem;
        }
        .db-announcement-link {
            font-size: 0.65rem;
            padding: 4px 12px;
        }
        .db-announcement-content i {
            font-size: 0.9rem;
        }
        .db-announcement-close {
            font-size: 0.9rem;
            padding: 4px 8px;
        }
    }

    @media (max-width: 768px) {
        .db-announcement-strip {
            padding: 8px 14px;
        }
        .db-announcement-inner {
            gap: 10px;
        }
        .db-announcement-content {
            font-size: 0.7rem;
            gap: 6px;
        }
        .db-announcement-title {
            font-size: 0.65rem;
        }
        .db-announcement-message {
            font-size: 0.68rem;
        }
        .db-announcement-link {
            font-size: 0.6rem;
            padding: 3px 10px;
            border-width: 1.5px;
        }
        .db-announcement-content i {
            font-size: 0.8rem;
        }
        .db-announcement-close {
            font-size: 0.8rem;
            padding: 4px 6px;
        }
    }

    @media (max-width: 576px) {
        .db-announcement-strip {
            padding: 6px 10px;
        }
        .db-announcement-inner {
            gap: 6px;
        }
        .db-announcement-content {
            font-size: 0.6rem;
            gap: 4px;
        }
        .db-announcement-title {
            font-size: 0.55rem;
            letter-spacing: 0.04em;
        }
        .db-announcement-message {
            font-size: 0.58rem;
        }
        .db-announcement-link {
            font-size: 0.5rem;
            padding: 2px 8px;
            border-width: 1.5px;
            gap: 3px;
        }
        .db-announcement-content i {
            font-size: 0.7rem;
        }
        .db-announcement-close {
            font-size: 0.7rem;
            padding: 3px 5px;
        }
    }

    @media (max-width: 400px) {
        .db-announcement-strip {
            padding: 4px 8px;
        }
        .db-announcement-content {
            font-size: 0.5rem;
            gap: 3px;
        }
        .db-announcement-title {
            font-size: 0.5rem;
        }
        .db-announcement-message {
            font-size: 0.5rem;
        }
        .db-announcement-link {
            font-size: 0.45rem;
            padding: 2px 6px;
        }
        .db-announcement-content i {
            font-size: 0.6rem;
        }
        .db-announcement-close {
            font-size: 0.6rem;
            padding: 2px 4px;
        }
    }

    /* ============================================================
       MAIN RESPONSIVE BREAKPOINTS
    ============================================================ */
    @media (max-width: 1100px) {
        .db-btn-call { display: none; }
        .db-nav { gap: 0; }
        .db-nav-link { padding: 8px 11px; font-size: 0.82rem; }
    }

    @media (max-width: 960px) {
        .db-nav,
        .db-btn-call,
        .db-btn-quote { display: none; }
        .db-hamburger { display: flex; }
        .db-topbar-left { display: none; }
        .db-topbar-inner { justify-content: center; }
        .db-topbar-right { width: 100%; justify-content: center; }
        .db-topbar-right .db-topbar-sep:last-child { display: none; }
    }

    @media (max-width: 768px) {
        :root { --header-h: 68px; --topbar-h: 32px; }
        .db-header-inner { padding: 0 16px; }
        .db-logo-img-wrap { height: 50px; }
        .db-logo-img-wrap img { height: 50px; max-width: 180px; }
        .db-logo-name { font-size: 1.3rem; }
        .db-topbar a { font-size: 0.68rem; }
        .db-topbar-inner { padding: 0 16px; }
        .db-topbar-left { display: none; }
        .db-topbar-right { width: 100%; justify-content: center; gap: 12px; }
        .db-topbar-right .db-topbar-sep { display: none; }
    }

    @media (max-width: 480px) {
        :root { --header-h: 62px; }
        .db-logo-img-wrap { height: 44px; }
        .db-logo-img-wrap img { height: 44px; max-width: 160px; }
        .db-logo-name { font-size: 1.15rem; }
        .db-topbar { display: none; }
        .db-header-inner { padding: 0 14px; }
        .db-topbar-right { display: none; }
    }

    @media (max-width: 360px) {
        .db-logo-img-wrap { height: 38px; }
        .db-logo-img-wrap img { max-width: 140px; }
        .db-logo-name { font-size: 1rem; }
        .db-logo-tag { font-size: 0.5rem; }
    }
    </style>
</head>
<body>

<!-- ===== TOPBAR ===== -->
<div class="db-topbar">
    <div class="db-topbar-inner">
        <div class="db-topbar-left">
            <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $site_settings['phone'] ?? '+918013635806')); ?>"><i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($site_settings['phone'] ?? '+91 80136 35806'); ?></a>
            
            <div class="db-topbar-sep"></div>
            <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $site_settings['phone2'] ?? '+919231646429')); ?>"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($site_settings['phone2'] ?? '+91 92316 46429'); ?></a>
            
            <div class="db-topbar-sep"></div>
            <a href="mailto:<?php echo htmlspecialchars($site_settings['email'] ?? 'enterprisearup@gmail.com'); ?>"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($site_settings['email'] ?? 'enterprisearup@gmail.com'); ?></a>
        </div>
        <div class="db-topbar-right">
            <?php if(!empty($site_settings['facebook_url'])): ?>
            <a href="<?php echo htmlspecialchars($site_settings['facebook_url']); ?>" target="_blank" rel="noopener" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
            <?php endif; ?>
            
            <?php if(!empty($site_settings['instagram_url'])): ?>
            <div class="db-topbar-sep"></div>
            <a href="<?php echo htmlspecialchars($site_settings['instagram_url']); ?>" target="_blank" rel="noopener" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            <?php endif; ?>
            
            <?php if(!empty($site_settings['linkedin_url'])): ?>
            <div class="db-topbar-sep"></div>
            <a href="<?php echo htmlspecialchars($site_settings['linkedin_url']); ?>" target="_blank" rel="noopener" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
            <?php endif; ?>

            <div class="db-topbar-sep"></div>
            <a href="https://wa.me/<?php echo htmlspecialchars(preg_replace('/[^0-9]/', '', $site_settings['whatsapp_number'] ?? '918013635806')); ?>?text=Hi%2C+I+have+an+enquiry." target="_blank" rel="noopener" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i> WhatsApp</a>
            
            <div class="db-topbar-sep"></div>
            <a href="<?php echo htmlspecialchars($site_settings['youtube_url'] ?? 'https://youtube.com/'); ?>" target="_blank" rel="noopener" aria-label="YouTube"><i class="fab fa-youtube"></i> YouTube</a>
        </div>
    </div>
</div>

<!-- ===== MAIN HEADER ===== -->
<header class="db-header" id="dbHeader">
    <div class="db-header-inner">

        <!-- LOGO -->
        <a href="./" class="db-logo" aria-label="Arup Enterprise — Home">
            <?php if ($logo_path): ?>
                <div class="db-logo-img-wrap">
                    <img src="<?php echo htmlspecialchars($logo_path); ?>"
                         alt="Arup Enterprise" class="img-fluid">
                </div>
            <?php else: ?>
                <div class="db-logo-icon" aria-hidden="true">
                    <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.5 1h5l.7 3.5a11 11 0 0 1 2.8 1.15l3-1.65 3.55 3.55-1.65 3A11 11 0 0 1 28.1 13.6l3.4.7v5l-3.4.7a11 11 0 0 1-1.15 2.8l1.65 3L25.05 29.3l-3-1.65a11 11 0 0 1-2.8 1.15L18.5 32h-5l-.7-3.5a11 11 0 0 1-2.8-1.15l-3 1.65L3.45 25.45l1.65-3A11 11 0 0 1 3.95 19.7L.5 18.6v-5l3.45-.7a11 11 0 0 1 1.15-2.8L3.45 7.1 7 3.55l3 1.65A11 11 0 0 1 12.8 4.1zm2.5 7a8 8 0 1 0 0 16A8 8 0 0 0 16 8zm0 3a5 5 0 1 1 0 10A5 5 0 0 1 16 11z"/>
                    </svg>
                </div>
                <div class="db-logo-text-wrap">
                    <span class="db-logo-name"><?php echo htmlspecialchars($site_settings['company_name'] ?? 'Arup Enterprise'); ?></span>
                    <span class="db-logo-tag"><?php echo htmlspecialchars($site_settings['tagline'] ?? 'Magnetic Separators'); ?></span>
                </div>
            <?php endif; ?>
        </a>

        <!-- DESKTOP NAV -->
        <nav class="db-nav" role="navigation" aria-label="Main navigation">
            <!-- Home -->
            <div class="db-nav-item">
                <a href="./" class="db-nav-link <?php echo $is_home_page?'active':''; ?>">
                    Home
                </a>
            </div>

            <!-- Application Dropdown -->
            <div class="db-nav-item">
                <a href="products" class="db-nav-link" aria-haspopup="true">
                    Application <i class="fas fa-chevron-down chev" aria-hidden="true"></i>
                </a>
                <div class="db-dropdown" role="menu">
                    <div class="db-dropdown-head">Machine Categories</div>
                    <?php foreach($product_dropdown as $pd): ?>
                    <a href="<?php echo htmlspecialchars($pd['href']); ?>" class="db-dropdown-item" role="menuitem">
                        <span class="ddi-icon"><i class="fas <?php echo htmlspecialchars($pd['icon']); ?>" aria-hidden="true"></i></span>
                        <?php echo htmlspecialchars($pd['label']); ?>
                    </a>
                    <?php endforeach; ?>
                    <div class="db-dropdown-divider"></div>
                    <a href="products" class="db-dropdown-all">
                        View All Products <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>

            <!-- Pinned Categories -->
            <?php foreach ($pinned_categories as $pc): ?>
            <div class="db-nav-item">
                <a href="products?category=<?php echo urlencode($pc['name']); ?>" class="db-nav-link <?php echo (isset($_GET['category']) && strtolower($_GET['category']) == strtolower($pc['name'])) ? 'active' : ''; ?>">
                    <?php echo htmlspecialchars($pc['name']); ?>
                </a>
            </div>
            <?php endforeach; ?>

            <!-- Our Products -->
            <div class="db-nav-item">
                <a href="products" class="db-nav-link <?php echo $is_products_page?'active':''; ?>">
                    Our Products
                </a>
            </div>

            <!-- Gallery -->
            <div class="db-nav-item">
                <a href="gallery" class="db-nav-link <?php echo $is_gallery_page?'active':''; ?>">
                    Gallery
                </a>
            </div>

            <!-- About Us -->
            <div class="db-nav-item">
                <a href="about" class="db-nav-link <?php echo $is_about_page?'active':''; ?>">
                    About Us
                </a>
            </div>

            <!-- Contact Us -->
            <div class="db-nav-item">
                <a href="contact" class="db-nav-link <?php echo $is_contact_page?'active':''; ?>">
                    Contact Us
                </a>
            </div>
        </nav>

        <!-- RIGHT ACTIONS -->
        <div class="db-header-actions">
            <a href="products" title="Search Products" class="db-btn-quote" style="border-radius: 50%; padding: 12px 14px; background: #DC2626;">
                <i class="fas fa-search" aria-hidden="true"></i>
            </a>
            <button class="db-hamburger" id="dbHamburger" aria-label="Open menu" aria-expanded="false" aria-controls="dbMobileNav">
                <span></span><span></span><span></span>
            </button>
        </div>

    </div>
</header>

<!-- ============================================================
     PREMIUM ANNOUNCEMENT STRIP - Fully Responsive
     ============================================================ -->
<?php if (!empty($announcement_strips)): ?>
    <?php foreach ($announcement_strips as $strip): ?>
    <div class="db-announcement-strip" id="announcementStrip" style="background:<?php echo $strip['bg_color'] ?? '#DC2626'; ?>;color:<?php echo $strip['text_color'] ?? '#FFFFFF'; ?>;">
        <div class="db-announcement-inner">
            <div class="db-announcement-content">
                <i class="fas <?php echo $strip['icon'] ?? 'fa-bullhorn'; ?>" aria-hidden="true"></i>
                <span class="db-announcement-title"><?php echo htmlspecialchars($strip['title']); ?></span>
                <span class="db-announcement-message"><?php echo htmlspecialchars($strip['message']); ?></span>
                <?php if (!empty($strip['link_url']) && !empty($strip['link_text'])): ?>
                    <a href="<?php echo htmlspecialchars($strip['link_url']); ?>" 
                       class="db-announcement-link" 
                       style="color:<?php echo $strip['text_color']; ?>;border-color:<?php echo $strip['text_color']; ?>;">
                        <?php echo htmlspecialchars($strip['link_text']); ?> <i class="fas fa-arrow-right"></i>
                    </a>
                <?php endif; ?>
            </div>
            
        </div>
    </div>
    <?php endforeach; ?>
<?php endif; ?>

<!-- ===== MOBILE OVERLAY ===== -->
<div class="db-mobile-overlay" id="dbOverlay" role="presentation"></div>

<!-- ===== MOBILE DRAWER ===== -->
<nav class="db-mobile-nav" id="dbMobileNav" aria-label="Mobile navigation" aria-hidden="true">

    <div class="db-mn-head">
        <a href="./" class="db-mn-logo" aria-label="Arup Enterprise — Home">
            <?php if ($logo_path): ?>
                <img src="<?php echo htmlspecialchars($logo_path); ?>"
                     alt="Arup Enterprise"
                     class="db-mn-logo-img">
            <?php else: ?>
                <div>
                    <div class="db-mn-logo-text">Arup<em>Enterprise</em></div>
                    <span class="db-mn-logo-sub">Magnetic Separators</span>
                </div>
            <?php endif; ?>
        </a>
        <button class="db-mn-close" id="dbMnClose" aria-label="Close menu">
            <i class="fas fa-times" aria-hidden="true"></i>
        </button>
    </div>

    <div class="db-mn-body">

        <a href="./" class="db-mn-link <?php echo $is_home_page?'mn-active':''; ?>">
            <span class="db-mn-link-left">
                Home
            </span>
        </a>

        <!-- Application accordion -->
        <button class="db-mn-link db-mn-product-toggle" id="dbMnProductToggle" aria-expanded="false">
            <span class="db-mn-link-left">
                Application
            </span>
            <i class="fas fa-chevron-down db-mn-chev" aria-hidden="true"></i>
        </button>
        <div class="db-mn-sub" id="dbMnProductSub" role="menu">
            <?php foreach($product_dropdown as $pd): ?>
            <a href="<?php echo htmlspecialchars($pd['href']); ?>" role="menuitem">
                <i class="fas <?php echo htmlspecialchars($pd['icon']); ?>" aria-hidden="true"></i>
                <?php echo htmlspecialchars($pd['label']); ?>
            </a>
            <?php endforeach; ?>
            <a href="products" role="menuitem">
                All Applications
            </a>
        </div>

        <?php foreach ($pinned_categories as $pc): ?>
        <a href="products?category=<?php echo urlencode($pc['name']); ?>" class="db-mn-link">
            <span class="db-mn-link-left">
                <i class="fas <?php echo !empty($pc['icon']) ? htmlspecialchars($pc['icon']) : 'fa-tag'; ?>" style="color:var(--gold); width:20px;"></i>
                <?php echo htmlspecialchars($pc['name']); ?>
            </span>
        </a>
        <?php endforeach; ?>

        <a href="products" class="db-mn-link <?php echo $is_products_page?'mn-active':''; ?>">
            <span class="db-mn-link-left">
                Our Products
            </span>
        </a>

        <a href="gallery" class="db-mn-link <?php echo $is_gallery_page?'mn-active':''; ?>">
            <span class="db-mn-link-left">
                Gallery
            </span>
        </a>

        <a href="about" class="db-mn-link <?php echo $is_about_page?'mn-active':''; ?>">
            <span class="db-mn-link-left">
                About Us
            </span>
        </a>

        <a href="contact" class="db-mn-link <?php echo $is_contact_page?'mn-active':''; ?>">
            <span class="db-mn-link-left">
                Contact Us
            </span>
        </a>

    </div>

    <div class="db-mn-footer">
        <a href="https://wa.me/918013635806?text=Hi%2C+I+need+a+quote+for+machinery."
           target="_blank" rel="noopener" class="db-mn-btn-wa">
            <i class="fab fa-whatsapp"></i> Chat on WhatsApp
        </a>
        <a href="contact" class="db-mn-btn-quote">
            <i class="fas fa-paper-plane"></i> Get a Free Quote
        </a>
        <div class="db-mn-contact-row">
            <a href="tel:<?php echo htmlspecialchars(preg_replace('/[^0-9+]/', '', $site_settings['phone'] ?? '+918013635806')); ?>"><i class="fas fa-phone-alt"></i> <?php echo htmlspecialchars($site_settings['phone'] ?? '+91 8013635806'); ?></a>
            <span class="sep">·</span>
            <a href="mailto:<?php echo htmlspecialchars($site_settings['email'] ?? 'enterprisearup@gmail.com'); ?>"><i class="fas fa-envelope"></i> Email</a>
        </div>
    </div>

</nav>

<script>
(function () {
    'use strict';

    var header   = document.getElementById('dbHeader');
    var ham      = document.getElementById('dbHamburger');
    var overlay  = document.getElementById('dbOverlay');
    var mobileNav= document.getElementById('dbMobileNav');
    var mnClose  = document.getElementById('dbMnClose');
    var prodToggle = document.getElementById('dbMnProductToggle');
    var prodSub    = document.getElementById('dbMnProductSub');

    var ticking = false;
    window.addEventListener('scroll', function () {
        if (!ticking) {
            requestAnimationFrame(function () {
                header.classList.toggle('scrolled', window.scrollY > 50);
                ticking = false;
            });
            ticking = true;
        }
    }, { passive: true });

    function openMenu() {
        ham.classList.add('open');
        ham.setAttribute('aria-expanded', 'true');
        mobileNav.classList.add('open');
        mobileNav.setAttribute('aria-hidden', 'false');
        overlay.classList.add('open');
        document.body.style.overflow = 'hidden';
        mnClose.focus();
    }
    function closeMenu() {
        ham.classList.remove('open');
        ham.setAttribute('aria-expanded', 'false');
        mobileNav.classList.remove('open');
        mobileNav.setAttribute('aria-hidden', 'true');
        overlay.classList.remove('open');
        document.body.style.overflow = '';
        ham.focus();
    }

    ham.addEventListener('click', function () {
        mobileNav.classList.contains('open') ? closeMenu() : openMenu();
    });
    mnClose.addEventListener('click', closeMenu);
    overlay.addEventListener('click', closeMenu);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mobileNav.classList.contains('open')) closeMenu();
    });

    prodToggle.addEventListener('click', function () {
        var isOpen = prodSub.classList.contains('open');
        prodSub.classList.toggle('open', !isOpen);
        prodToggle.classList.toggle('open', !isOpen);
        prodToggle.setAttribute('aria-expanded', String(!isOpen));
    });

    mobileNav.querySelectorAll('.db-mn-link:not(.db-mn-product-toggle), .db-mn-sub a, .db-mn-btn-wa, .db-mn-btn-quote, .db-mn-contact-row a').forEach(function (link) {
        link.addEventListener('click', function () {
            if (!this.classList.contains('db-mn-product-toggle')) {
                closeMenu();
            }
        });
    });

})();
</script>

</body>
</html>
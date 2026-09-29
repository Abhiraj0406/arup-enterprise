<?php
// admin/dashboard.php - Admin Dashboard
session_start();

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: login.php");
    exit();
}

require_once "../includes/db.php";

date_default_timezone_set('Asia/Kolkata');

// Check if $conn is defined
if (!isset($conn) || $conn->connect_error) {
    die("Database connection error. Please check db.php");
}

$page_title = "Dashboard";
$page_icon = "chart-pie";

// ================= STATISTICS =================

// Total Products
$total_products = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM products");
if ($result) {
    $row = $result->fetch_assoc();
    $total_products = $row['total'] ?? 0;
}

// Active Products
$active_products = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM products WHERE status = 'active'");
if ($result) {
    $row = $result->fetch_assoc();
    $active_products = $row['total'] ?? 0;
}

// Total Categories
$total_categories = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM categories WHERE status = 'active'");
if ($result) {
    $row = $result->fetch_assoc();
    $total_categories = $row['total'] ?? 0;
}

// Contact Messages (New)
$new_messages = 0;
$result = $conn->query("SELECT COUNT(*) as total FROM contact_messages WHERE status = 'new'");
if ($result) {
    $row = $result->fetch_assoc();
    $new_messages = $row['total'] ?? 0;
}

// ================= RECENT PRODUCTS =================
$recent_products = [];
$result = $conn->query("SELECT * FROM products ORDER BY id DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_products[] = $row;
    }
}

// ================= RECENT CONTACT MESSAGES =================
$recent_messages = [];
$result = $conn->query("SELECT * FROM contact_messages ORDER BY id DESC LIMIT 5");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $recent_messages[] = $row;
    }
}

// ================= CATEGORY WISE PRODUCT COUNT =================
$category_counts = [];
$result = $conn->query("SELECT category, COUNT(*) as count FROM products WHERE status = 'active' GROUP BY category ORDER BY count DESC");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $category_counts[] = $row;
    }
}

// ================= ADMIN NAME =================
$admin_name = $_SESSION['admin_name'] ?? 'Administrator';

// ✅ Include admin navbar
include 'includes/navbar.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard | DipBan Admin</title>
    <link rel="icon" type="image/png" href="../assets/images/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

    <style>
        /* =============================================
           GLOBAL
        ============================================= */
        :root {
            --gold: #C9920A;
            --gold-dark: #a87a08;
            --gold-light: #e6c9a0;
            --cream: #fef7ed;
            --ivory: #faf3e8;
            --charcoal: #1e1e1e;
            --mid-gray: #4a3f37;
            --light-gray: #e8ddd0;
            --white: #ffffff;
            --shadow-gold: 0 4px 20px rgba(201,146,10,0.15);
            --shadow-md: 0 8px 30px rgba(0,0,0,0.06);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--cream);
            color: var(--charcoal);
            font-size: 14px;
        }

        /* =============================================
           CONTENT WRAPPER - FIXED SIDEBAR MARGIN
        ============================================= */
        .dashboard-content {
            margin-left: 280px;
            padding: 16px 24px 30px;
            min-height: 100vh;
        }

        @media (max-width: 768px) {
            .dashboard-content {
                margin-left: 0;
                padding: 16px 14px 20px;
            }
        }

        /* =============================================
           DASHBOARD HEADER - NO EXTRA TOP MARGIN
        ============================================= */
        .dash-header {
            background: var(--white);
            border-radius: 12px;
            padding: 12px 18px;
            margin-bottom: 18px;
            border: 1px solid rgba(201,146,10,0.12);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            position: relative;
            overflow: hidden;
        }
        .dash-header::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold-dark), var(--gold), var(--gold-light));
        }
        .dash-header .title {
            font-size: 1.1rem;
            font-weight: 900;
            color: var(--charcoal);
            margin: 0;
        }
        .dash-header .title i {
            color: var(--gold);
            margin-right: 8px;
        }
        .dash-header .subtitle {
            font-size: 0.7rem;
            color: var(--mid-gray);
            margin: 0;
        }
        .dash-header .time {
            font-size: 0.65rem;
            color: var(--mid-gray);
            background: var(--cream);
            padding: 3px 12px;
            border-radius: 20px;
            border: 1px solid rgba(201,146,10,0.08);
            display: inline-block;
        }

        /* =============================================
           WELCOME BANNER
        ============================================= */
        .welcome-banner {
            background: linear-gradient(135deg, #1e1e1e, #2a2a2a);
            border-radius: 12px;
            padding: 14px 20px;
            margin-bottom: 16px;
            border: 1px solid rgba(201,146,10,0.15);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        .welcome-banner h2 {
            color: var(--white);
            font-weight: 800;
            font-size: 1.1rem;
            margin: 0;
        }
        .welcome-banner h2 span {
            color: var(--gold);
        }
        .welcome-banner p {
            color: rgba(255,255,255,0.6);
            margin: 0;
            font-size: 0.75rem;
        }
        .quick-actions {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-top: 4px;
        }
        .quick-actions a {
            display: inline-flex;
            align-items: center;
            gap: 4px;
            padding: 4px 12px;
            border-radius: 6px;
            font-size: 0.65rem;
            font-weight: 600;
            text-decoration: none;
            transition: all 0.3s;
        }
        .quick-actions .qa-primary {
            background: var(--gold);
            color: white;
            box-shadow: var(--shadow-gold);
        }
        .quick-actions .qa-primary:hover {
            background: var(--gold-dark);
            transform: translateY(-2px);
        }
        .quick-actions .qa-secondary {
            background: rgba(255,255,255,0.06);
            color: var(--white);
            border: 1px solid rgba(255,255,255,0.1);
        }
        .quick-actions .qa-secondary:hover {
            background: rgba(255,255,255,0.12);
            transform: translateY(-2px);
        }

        /* =============================================
           STAT CARDS
        ============================================= */
        .stat-card {
            background: var(--white);
            border-radius: 12px;
            padding: 16px 18px;
            border: 1px solid rgba(201,146,10,0.08);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            height: 100%;
        }
        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light));
        }
        .stat-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
            border-color: rgba(201,146,10,0.15);
        }
        .stat-card .stat-icon {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: var(--gold);
            background: rgba(201,146,10,0.08);
            margin-bottom: 6px;
        }
        .stat-card .stat-number {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--charcoal);
            line-height: 1.1;
        }
        .stat-card .stat-label {
            font-size: 0.72rem;
            color: var(--mid-gray);
            font-weight: 500;
        }
        .stat-card .stat-trend {
            font-size: 0.55rem;
            font-weight: 600;
            padding: 2px 10px;
            border-radius: 20px;
            display: inline-block;
            margin-top: 4px;
        }
        .stat-trend.up { background: #dcfce7; color: #166534; }
        .stat-trend.down { background: #fef2f2; color: #991b1b; }

        .stat-card.gold .stat-icon { background: var(--gold); color: white; }
        .stat-card.gold::before { background: linear-gradient(90deg, var(--gold-dark), var(--gold)); }

        /* =============================================
           WIDGET CARDS
        ============================================= */
        .widget-card {
            background: var(--white);
            border-radius: 12px;
            border: 1px solid rgba(201,146,10,0.08);
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            overflow: hidden;
            transition: all 0.3s ease;
        }
        .widget-card:hover {
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        .widget-card .widget-header {
            padding: 10px 16px;
            border-bottom: 1px solid rgba(201,146,10,0.06);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: linear-gradient(135deg, var(--cream), var(--ivory));
        }
        .widget-card .widget-header h6 {
            font-weight: 700;
            margin: 0;
            font-size: 0.78rem;
            color: var(--charcoal);
        }
        .widget-card .widget-header h6 i {
            color: var(--gold);
            margin-right: 6px;
        }
        .widget-card .widget-body {
            padding: 10px 16px;
            max-height: 280px;
            overflow-y: auto;
        }

        /* =============================================
           RECENT ITEMS
        ============================================= */
        .recent-item {
            display: flex;
            align-items: center;
            padding: 6px 0;
            border-bottom: 1px solid rgba(201,146,10,0.05);
            gap: 10px;
        }
        .recent-item:last-child { border-bottom: none; }
        .recent-item:hover { background: rgba(201,146,10,0.03); padding-left: 4px; }
        .recent-item .item-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            background: rgba(201,146,10,0.08);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--gold);
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        .recent-item .item-info {
            flex: 1;
            min-width: 0;
        }
        .recent-item .item-info .item-title {
            font-weight: 600;
            font-size: 0.75rem;
            color: var(--charcoal);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .recent-item .item-info .item-meta {
            font-size: 0.6rem;
            color: var(--mid-gray);
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .recent-item .item-status {
            font-size: 0.48rem;
            font-weight: 700;
            text-transform: uppercase;
            padding: 2px 10px;
            border-radius: 20px;
            flex-shrink: 0;
            letter-spacing: 0.3px;
        }
        .item-status.active { background: #dcfce7; color: #166534; }
        .item-status.draft { background: #fef3c7; color: #b45309; }
        .item-status.inactive { background: #fef2f2; color: #991b1b; }
        .item-status.new { background: #dbeafe; color: #1d4ed8; }
        .item-status.called { background: #dcfce7; color: #15803d; }
        .item-status.resolved { background: #f3e8ff; color: #7c3aed; }

        /* =============================================
           BUTTONS
        ============================================= */
        .btn-gold-sm {
            background: var(--gold);
            color: white;
            border: none;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.55rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-gold-sm:hover {
            background: var(--gold-dark);
            color: white;
        }
        .btn-outline-gold-sm {
            border: 2px solid var(--gold);
            color: var(--gold);
            background: transparent;
            padding: 2px 10px;
            border-radius: 20px;
            font-size: 0.55rem;
            font-weight: 600;
            transition: all 0.3s;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }
        .btn-outline-gold-sm:hover {
            background: var(--gold);
            color: white;
        }

        /* =============================================
           CATEGORY CHART
        ============================================= */
        .category-bar {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 4px 0;
        }
        .category-bar .cat-name {
            font-size: 0.7rem;
            font-weight: 500;
            color: var(--charcoal);
            min-width: 85px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .category-bar .cat-name i {
            color: var(--gold);
            font-size: 0.5rem;
            margin-right: 4px;
        }
        .category-bar .cat-bar {
            flex: 1;
            height: 5px;
            background: var(--light-gray);
            border-radius: 3px;
            overflow: hidden;
        }
        .category-bar .cat-bar .fill {
            height: 100%;
            border-radius: 3px;
            background: linear-gradient(90deg, var(--gold), var(--gold-light));
            transition: width 1s ease;
        }
        .category-bar .cat-count {
            font-size: 0.6rem;
            font-weight: 600;
            color: var(--mid-gray);
            min-width: 20px;
            text-align: right;
        }

        /* =============================================
           SCROLLBAR
        ============================================= */
        .widget-card .widget-body::-webkit-scrollbar {
            width: 3px;
        }
        .widget-card .widget-body::-webkit-scrollbar-track {
            background: var(--cream);
        }
        .widget-card .widget-body::-webkit-scrollbar-thumb {
            background: var(--gold);
            border-radius: 3px;
        }

        /* =============================================
           RESPONSIVE
        ============================================= */
        @media (max-width: 992px) {
            .stat-card .stat-number { font-size: 1.3rem; }
            .stat-card { padding: 14px 16px; }
        }

        @media (max-width: 768px) {
            .welcome-banner { padding: 12px 16px; flex-direction: column; text-align: center; }
            .welcome-banner h2 { font-size: 1rem; }
            .quick-actions { justify-content: center; }
            .stat-card .stat-number { font-size: 1.1rem; }
            .stat-card { padding: 10px 12px; }
            .stat-card .stat-icon { width: 32px; height: 32px; font-size: 0.9rem; }
            .dash-header { padding: 10px 14px; }
            .dash-header .title { font-size: 0.9rem; }
            .widget-card .widget-body { padding: 8px 12px; }
            .recent-item .item-icon { width: 28px; height: 28px; font-size: 0.7rem; }
            .recent-item .item-info .item-title { font-size: 0.7rem; }
            .recent-item .item-status { font-size: 0.42rem; padding: 1px 6px; }
            .btn-gold-sm, .btn-outline-gold-sm { font-size: 0.5rem; padding: 1px 8px; }
            .category-bar .cat-name { min-width: 55px; font-size: 0.6rem; }
            .dash-header .time { font-size: 0.55rem; padding: 2px 8px; }
        }

        @media (max-width: 480px) {
            .stat-card .stat-number { font-size: 0.95rem; }
            .stat-card .stat-label { font-size: 0.6rem; }
            .stat-card .stat-trend { font-size: 0.45rem; padding: 1px 6px; }
            .category-bar .cat-name { min-width: 40px; font-size: 0.55rem; }
            .category-bar .cat-count { font-size: 0.5rem; min-width: 16px; }
            .widget-card .widget-header h6 { font-size: 0.65rem; }
            .welcome-banner h2 { font-size: 0.85rem; }
            .welcome-banner p { font-size: 0.65rem; }
            .quick-actions a { font-size: 0.55rem; padding: 3px 8px; }
        }
    </style>
</head>
<body>

<!-- =============================================
     DASHBOARD CONTENT (NO EXTRA TOP MARGIN)
============================================= -->
<div class="dashboard-content">

    <!-- Dashboard Header -->
    <div class="dash-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="title">
                    <i class="fas fa-chart-pie"></i> Dashboard
                </div>
                <div class="subtitle">Overview of your business performance</div>
            </div>
            <div class="col-md-4 text-md-end mt-2 mt-md-0">
                <span class="time">
                    <i class="fas fa-calendar-alt me-1"></i> <?php echo date('d M Y'); ?>
                    <span style="margin:0 4px;">|</span>
                    <i class="fas fa-clock me-1"></i> <?php echo date('h:i A'); ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Welcome Banner -->
    <div class="welcome-banner">
        <div>
            <h2>Welcome back, <span><?php echo htmlspecialchars($admin_name); ?></span>!</h2>
            <p>Here's what's happening with your business today.</p>
        </div>
        <div class="quick-actions">
            <a href="add_product.php" class="qa-primary">
                <i class="fas fa-plus-circle"></i> Add Product
            </a>
            <a href="view_contact.php" class="qa-secondary">
                <i class="fas fa-envelope"></i> Messages
            </a>
            <a href="settings.php" class="qa-secondary">
                <i class="fas fa-cog"></i> Settings
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row g-3 mb-4">
        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-boxes"></i></div>
                <div class="stat-number"><?php echo number_format($total_products); ?></div>
                <div class="stat-label">Total Products</div>
                <span class="stat-trend up"><i class="fas fa-arrow-up"></i> <?php echo $active_products; ?> active</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="stat-card gold">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-number"><?php echo number_format($active_products); ?></div>
                <div class="stat-label">Active Products</div>
                <span class="stat-trend up"><i class="fas fa-check"></i> Live</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-tags"></i></div>
                <div class="stat-number"><?php echo number_format($total_categories); ?></div>
                <div class="stat-label">Categories</div>
                <span class="stat-trend up"><i class="fas fa-folder"></i> Organized</span>
            </div>
        </div>

        <div class="col-xl-3 col-lg-6 col-md-6 col-sm-6">
            <div class="stat-card">
                <div class="stat-icon"><i class="fas fa-envelope"></i></div>
                <div class="stat-number"><?php echo number_format($new_messages); ?></div>
                <div class="stat-label">New Messages</div>
                <span class="stat-trend <?php echo $new_messages > 0 ? 'up' : 'down'; ?>">
                    <i class="fas <?php echo $new_messages > 0 ? 'fa-arrow-up' : 'fa-circle'; ?>"></i>
                    <?php echo $new_messages > 0 ? $new_messages . ' unread' : 'All read'; ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Charts & Recent Activity -->
    <div class="row g-3">

        <!-- Category Distribution -->
        <div class="col-lg-5">
            <div class="widget-card">
                <div class="widget-header">
                    <h6><i class="fas fa-chart-pie"></i> Category Distribution</h6>
                    <span class="badge" style="background:rgba(201,146,10,0.12);color:var(--gold-dark);font-size:0.5rem;padding:2px 8px;">
                        <?php echo count($category_counts); ?>
                    </span>
                </div>
                <div class="widget-body">
                    <?php if (!empty($category_counts)): ?>
                        <?php 
                        $max_count = !empty($category_counts) ? max(array_column($category_counts, 'count')) : 1;
                        foreach ($category_counts as $cat):
                            $percentage = ($cat['count'] / $max_count) * 100;
                        ?>
                            <div class="category-bar">
                                <span class="cat-name"><i class="fas fa-tag"></i> <?php echo htmlspecialchars($cat['category']); ?></span>
                                <div class="cat-bar">
                                    <div class="fill" style="width: <?php echo $percentage; ?>%;"></div>
                                </div>
                                <span class="cat-count"><?php echo $cat['count']; ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-chart-pie" style="font-size:1.8rem;color:var(--light-gray);display:block;margin-bottom:4px;"></i>
                            <p style="color:var(--mid-gray);font-size:0.75rem;">No products added yet</p>
                            <a href="add_product.php" class="btn-gold-sm"><i class="fas fa-plus"></i> Add</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Products -->
        <div class="col-lg-7">
            <div class="widget-card">
                <div class="widget-header">
                    <h6><i class="fas fa-clock"></i> Recent Products</h6>
                    <a href="products.php" class="btn-outline-gold-sm"><i class="fas fa-eye"></i> View</a>
                </div>
                <div class="widget-body">
                    <?php if (!empty($recent_products)): ?>
                        <?php foreach ($recent_products as $p): ?>
                            <div class="recent-item">
                                <div class="item-icon">
                                    <?php if (!empty($p['image']) && file_exists("../" . $p['image'])): ?>
                                        <img src="../<?php echo $p['image']; ?>" style="width:32px;height:32px;object-fit:cover;border-radius:6px;">
                                    <?php else: ?>
                                        <i class="fas fa-cog"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="item-info">
                                    <div class="item-title"><?php echo htmlspecialchars($p['name']); ?></div>
                                    <div class="item-meta">
                                        <i class="fas fa-tag" style="font-size:0.45rem;"></i> <?php echo htmlspecialchars($p['category']); ?>
                                        <?php if ($p['featured']): ?>
                                            <span class="badge" style="background:var(--gold);color:white;font-size:0.4rem;margin-left:4px;padding:1px 5px;border-radius:10px;">
                                                <i class="fas fa-star"></i>
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <span class="item-status <?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-box-open" style="font-size:1.8rem;color:var(--light-gray);display:block;margin-bottom:4px;"></i>
                            <p style="color:var(--mid-gray);font-size:0.75rem;">No products yet</p>
                            <a href="add_product.php" class="btn-gold-sm"><i class="fas fa-plus"></i> Add</a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

    </div>

    <!-- Recent Contact Messages -->
    <div class="row g-3 mt-1">
        <div class="col-12">
            <div class="widget-card">
                <div class="widget-header">
                    <h6><i class="fas fa-envelope-open-text"></i> Recent Contact Messages</h6>
                    <a href="view_contact.php" class="btn-outline-gold-sm"><i class="fas fa-eye"></i> View</a>
                </div>
                <div class="widget-body">
                    <?php if (!empty($recent_messages)): ?>
                        <?php foreach ($recent_messages as $msg): ?>
                            <div class="recent-item">
                                <div class="item-icon" style="background:rgba(201,146,10,0.08);">
                                    <i class="fas fa-user" style="color:var(--gold);"></i>
                                </div>
                                <div class="item-info">
                                    <div class="item-title"><?php echo htmlspecialchars($msg['name']); ?></div>
                                    <div class="item-meta">
                                        <i class="fas fa-envelope" style="font-size:0.45rem;"></i> <?php echo htmlspecialchars($msg['email']); ?>
                                        <span style="margin-left:6px;">
                                            <i class="fas fa-clock" style="font-size:0.45rem;"></i>
                                            <?php 
                                            $date = new DateTime($msg['created_at'] ?? $msg['submitted_at'] ?? date('Y-m-d H:i:s'));
                                            echo $date->format('d M Y, h:i A');
                                            ?>
                                        </span>
                                    </div>
                                </div>
                                <span class="item-status <?php echo strtolower($msg['status'] ?? 'new'); ?>">
                                    <?php echo ucfirst($msg['status'] ?? 'New'); ?>
                                </span>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-inbox" style="font-size:1.8rem;color:var(--light-gray);display:block;margin-bottom:4px;"></i>
                            <p style="color:var(--mid-gray);font-size:0.75rem;">No messages yet</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
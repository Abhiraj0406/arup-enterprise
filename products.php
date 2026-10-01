<?php
// products.php - Products Listing Page
$page_title = "Products";
require_once 'includes/db.php';
include 'includes/header.php';

// ✅ Get category filter
$category_filter = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// ✅ Build query using $conn (not $pdo)
$sql = "SELECT * FROM products WHERE status = 'active'";
$params = [];
$types = "";

if (!empty($category_filter)) {
    $sql .= " AND category = ?";
    $params[] = $category_filter;
    $types .= "s";
}

if (!empty($search)) {
    $sql .= " AND (name LIKE ? OR description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $types .= "ss";
}

$sql .= " ORDER BY featured DESC, sort_order ASC, id DESC";

// ✅ Execute query using $conn (MySQLi)
$products = [];
if (isset($conn) && $conn) {
    try {
        $stmt = $conn->prepare($sql);
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $products[] = $row;
        }
        $stmt->close();
    } catch (Exception $e) {
        $products = [];
    }
}

// ✅ Get categories for filter dropdown using $conn
$categories = [];
if (isset($conn) && $conn) {
    try {
        $result = $conn->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row;
            }
        }
    } catch (Exception $e) {
        $categories = [];
    }
}

$total_count = count($products);
?>

<!-- =============================================
     HTML CONTENT
============================================= -->
<style>
    :root{
        --gold:#EF4444; --gold-dark:#DC2626; --gold-light:#F87171;
        --charcoal:#111827; --mid-gray:#4B5563; --cream:#FFF5F5; --ivory:#FEE2E2; --light-gray:#FECACA;
        /* premium "classic WhatsApp" accent — deep forest green + gold trim, not the stock neon green */
        --wa-deep:#0c3d2e; --wa-mid:#145c43; --wa-light:#2f8f6b;
    }

    /* =============================================
       PREMIUM HERO (replaces flat dark header)
    ============================================= */
    .page-header {
        position: relative;
        padding: 110px 0 130px;
        overflow: hidden;
        border-bottom: 4px solid var(--gold);
        isolation: isolate;
    }
    .page-header-bg {
        position: absolute; inset: 0; z-index: 0;
        width: 100%; height: 100%; object-fit: cover;
        transform: scale(1.2);
    }
    .page-header-overlay {
        position: absolute; inset: 0; z-index: 0;
        background: linear-gradient(135deg, rgba(20,20,20,0.93) 0%, rgba(20,20,20,0.74) 55%, rgba(239,68,68,0.18) 100%);
    }
    .page-header-gears { position: absolute; inset: 0; z-index: 0; pointer-events: none; opacity: 0.06; }
    .page-header-gears svg { position: absolute; color: var(--gold); }
    .page-header-gears .pg-1 { width: 280px; top: -60px; right: 6%; animation: pgspin 38s linear infinite; }
    .page-header-gears .pg-2 { width: 160px; bottom: -30px; left: 8%; animation: pgspin 26s linear infinite reverse; }
    @keyframes pgspin { to { transform: rotate(360deg); } }

    .page-header-content { position: relative; z-index: 1; }
    .ph-badge {
        display: inline-flex; align-items: center; gap: 8px;
        background: rgba(239,68,68,0.16); border: 1px solid rgba(239,68,68,0.4);
        color: var(--gold-light); font-size: 0.7rem; font-weight: 700;
        letter-spacing: 0.16em; text-transform: uppercase;
        padding: 6px 16px; border-radius: 20px; margin-bottom: 18px;
    }
    .ph-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: var(--gold); animation: pgpulse 2s ease-in-out infinite; }
    @keyframes pgpulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:.5;transform:scale(1.4)} }

    .page-header h1 {
        color: #ffffff;
        font-weight: 900;
        font-size: clamp(2.1rem, 4vw, 3rem);
        margin-bottom: 14px;
        max-width: 700px;
    }
    .page-header h1 span { color: var(--gold); }
    .page-header p {
        color: rgba(255,255,255,0.75);
        font-size: 1.05rem;
        max-width: 560px;
        margin-bottom: 0;
    }
    .ph-stats { display:flex; gap:28px; margin-top:30px; flex-wrap:wrap; }
    .ph-stat { color:#fff; }
    .ph-stat b { display:block; font-size:1.6rem; font-weight:900; color:var(--gold-light); }
    .ph-stat span { font-size:0.72rem; text-transform:uppercase; letter-spacing:.08em; color:rgba(255,255,255,0.6); }

    .container-custom {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 24px;
    }

    /* =============================================
       FILTER BAR — functional search + category select
    ============================================= */
    .filter-bar {
        background: #ffffff;
        border-radius: 16px;
        padding: 22px 26px;
        margin: -46px auto 40px;
        box-shadow: 0 16px 50px -10px rgba(30,20,5,0.18);
        border: 1px solid rgba(239,68,68,0.12);
        position: relative;
        z-index: 5;
    }
    .filter-form {
        display: grid;
        grid-template-columns: 1.4fr 1fr auto;
        gap: 14px;
        align-items: stretch;
    }
    .filter-bar .search-box { position: relative; }
    .filter-bar .search-box input {
        padding: 13px 18px 13px 46px;
        border: 2px solid var(--light-gray);
        border-radius: 10px;
        width: 100%;
        height: 100%;
        font-size: 0.92rem;
        font-family: inherit;
        background: var(--cream);
        outline: none;
        box-sizing: border-box;
    }
    .filter-bar .search-box input:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(239,68,68,0.12);
        background: #ffffff;
    }
    .filter-bar .search-box i {
        position: absolute;
        left: 17px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--mid-gray);
        pointer-events: none;
    }
    .filter-bar .category-filter select {
        padding: 13px 18px;
        border: 2px solid var(--light-gray);
        border-radius: 10px;
        width: 100%;
        height: 100%;
        font-size: 0.92rem;
        font-family: inherit;
        background: var(--cream) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%234a3f37' stroke-width='2'%3E%3Cpath d='M6 9l6 6 6-6'/%3E%3C/svg%3E") no-repeat right 14px center;
        background-size: 16px;
        cursor: pointer;
        appearance: none;
        outline: none;
        box-sizing: border-box;
    }
    .filter-bar .category-filter select:focus {
        border-color: var(--gold);
        box-shadow: 0 0 0 3px rgba(239,68,68,0.12);
        background-color: #ffffff;
    }

    .btn-gold {
        background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
        color: white;
        border: none;
        padding: 13px 28px;
        border-radius: 10px;
        font-weight: 700;
        font-size: 0.92rem;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        justify-content: center;
        transition: all 0.3s cubic-bezier(.2,.9,.3,1);
        box-shadow: 0 6px 22px rgba(239,68,68,0.3);
        white-space: nowrap;
    }
    .btn-gold:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 28px rgba(239,68,68,0.4);
    }

    .active-filters { display:flex; gap:10px; align-items:center; margin-top:14px; flex-wrap:wrap; }
    .active-filters .chip {
        display:inline-flex; align-items:center; gap:8px;
        background: var(--ivory); border:1px solid rgba(239,68,68,0.25);
        color: var(--mid-gray); font-size:0.8rem; font-weight:600;
        padding:6px 12px; border-radius:20px;
    }
    .active-filters .chip a { color:#b33; font-weight:800; text-decoration:none; }
    .results-count { color: var(--mid-gray); font-size:0.85rem; margin-bottom:22px; }
    .results-count strong { color: var(--charcoal); }

    .products-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 30px;
        margin-bottom: 50px;
    }

    .product-card {
        background: #ffffff;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        transition: all 0.4s ease;
        border: 1px solid rgba(239,68,68,0.06);
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 14px 38px rgba(0,0,0,0.12);
        border-color: rgba(239,68,68,0.25);
    }

    .product-img-wrap {
        position: relative;
        height: 220px;
        background: var(--ivory);
        overflow: hidden;
    }
    .product-img-wrap img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        transition: transform 0.5s ease;
    }
    .product-card:hover .product-img-wrap img {
        transform: scale(1.05);
    }

    .product-img-wrap .featured-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: var(--gold);
        color: white;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.6rem;
        font-weight: 700;
        text-transform: uppercase;
        box-shadow: 0 4px 20px rgba(239,68,68,0.3);
    }
    .product-img-wrap .featured-badge i {
        margin-right: 4px;
        font-size: 0.5rem;
    }

    .product-img-wrap .category-badge {
        position: absolute;
        bottom: 12px;
        left: 12px;
        background: rgba(0,0,0,0.72);
        color: white;
        padding: 4px 14px;
        border-radius: 20px;
        font-size: 0.6rem;
        font-weight: 600;
        text-transform: uppercase;
        backdrop-filter: blur(4px);
    }

    .product-body {
        padding: 20px 22px 22px;
    }
    .product-body h3 {
        font-size: 1.05rem;
        font-weight: 700;
        margin-bottom: 6px;
        color: var(--charcoal);
    }
    .product-body .product-category {
        font-size: 0.7rem;
        color: var(--gold-dark);
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .product-body .product-desc {
        color: var(--mid-gray);
        font-size: 0.85rem;
        margin: 10px 0 16px;
        line-height: 1.6;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
    .product-body .product-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        border-top: 1px solid var(--ivory);
        padding-top: 14px;
    }
    .product-body .product-footer .btn-detail {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: var(--gold-dark);
        font-weight: 700;
        font-size: 0.85rem;
        transition: all 0.3s;
        text-decoration: none;
    }
    .product-body .product-footer .btn-detail:hover {
        color: var(--gold);
        gap: 10px;
    }

    /* =============================================
       CLASSIC PREMIUM WHATSAPP BUTTON
       — deep forest green + gold trim, not stock neon
    ============================================= */
    .product-body .product-footer .btn-quote-small {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        height: 38px;
        padding: 0 14px 0 12px;
        background: linear-gradient(135deg, var(--wa-mid) 0%, var(--wa-deep) 100%);
        color: var(--gold-light);
        border: 1px solid rgba(239,68,68,0.35);
        border-radius: 20px;
        font-size: 0.78rem;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(.2,.9,.3,1);
        box-shadow: 0 4px 16px rgba(12,61,46,0.35);
    }
    .product-body .product-footer .btn-quote-small i { font-size: 0.95rem; color: var(--gold-light); }
    .product-body .product-footer .btn-quote-small span { white-space: nowrap; }
    .product-body .product-footer .btn-quote-small:hover {
        background: linear-gradient(135deg, var(--wa-deep) 0%, #08291f 100%);
        border-color: var(--gold);
        color: #ffffff;
        transform: translateY(-2px);
        box-shadow: 0 8px 22px rgba(12,61,46,0.5);
    }
    .product-body .product-footer .btn-quote-small:hover i { color: var(--gold); }

    .no-products {
        text-align: center;
        padding: 70px 20px;
        background: #fff;
        border-radius: 16px;
        border: 1px solid rgba(239,68,68,0.12);
        margin-bottom: 50px;
    }
    .no-products i {
        font-size: 3rem;
        color: var(--light-gray);
        display: block;
        margin-bottom: 16px;
    }
    .no-products h3 {
        font-weight: 700;
        color: var(--mid-gray);
        margin-bottom: 8px;
    }
    .no-products p {
        color: var(--mid-gray);
        margin-bottom: 20px;
    }

    @media (max-width: 1024px) {
        .products-grid { grid-template-columns: repeat(2, 1fr); }
        .filter-form { grid-template-columns: 1fr; }
    }
    @media (max-width: 768px) {
        .products-grid { grid-template-columns: 1fr; max-width: 420px; margin: 0 auto 30px; }
        .page-header { padding: 80px 0 100px; }
        .filter-bar { padding: 18px; margin-top: -36px; }
        .ph-stats { gap: 18px; }
    }
    @media (max-width: 480px) {
        .product-img-wrap { height: 180px; }
        .page-header h1 { font-size: 1.7rem; }
    }
</style>

<!-- =============================================
     PREMIUM PAGE HEADER / HERO
============================================= -->
<section class="page-header">
    <video class="page-header-bg" autoplay muted loop playsinline preload="auto">
        <source src="assets/video/machine-hero.mp4" type="video/mp4">
    </video>
    <div class="page-header-overlay"></div>
    <div class="page-header-gears" aria-hidden="true">
        <svg class="pg-1" viewBox="0 0 100 100"><use href="#gear-svg"/></svg>
        <svg class="pg-2" viewBox="0 0 100 100"><use href="#gear-svg"/></svg>
    </div>
    <svg style="display:none">
        <symbol id="gear-svg" viewBox="0 0 100 100">
            <path d="M43 2h14l2 10a35 35 0 0 1 8.5 3.5l9-5 10 10-5 9A35 35 0 0 1 85 38l10 2v14l-10 2a35 35 0 0 1-3.5 8.5l5 9-10 10-9-5A35 35 0 0 1 59 82l-2 10H43l-2-10a35 35 0 0 1-8.5-3.5l-9 5-10-10 5-9A35 35 0 0 1 15 56L5 54V40l10-2a35 35 0 0 1 3.5-8.5l-5-9 10-10 9 5A35 35 0 0 1 41 12zm7 22a26 26 0 1 0 0 52 26 26 0 0 0 0-52zm0 10a16 16 0 1 1 0 32 16 16 0 0 1 0-32z" fill="currentColor"/>
        </symbol>
    </svg>

    <div class="container-custom page-header-content">
        <div class="ph-badge"><span class="dot"></span> Manufacturer · Supplier · Service Provider</div>
        <h1>Our <span>Premium Products</span></h1>
        <p>Precision-engineered industrial machinery for woodworking and sheet-metal production — built to run hard, every shift, every day.</p>
        <div class="ph-stats">
            <div class="ph-stat"><b><?php echo (int)$total_count; ?>+</b><span>Machines Listed</span></div>
            <div class="ph-stat"><b><?php echo max(count($categories), 4); ?></b><span>Categories</span></div>
            <div class="ph-stat"><b>24/7</b><span>Support</span></div>
        </div>
    </div>
</section>

<!-- =============================================
     FILTER BAR — search + category, both wired to GET params above
============================================= -->
<div class="container-custom">
    <div class="filter-bar">
        <form method="GET" action="products.php" class="filter-form">
            <div class="search-box">
                <i class="fas fa-search"></i>
                <input type="text" name="search" placeholder="Search products by name or keyword..." value="<?php echo htmlspecialchars($search); ?>">
            </div>
            <div class="category-filter">
                <select name="category" onchange="this.form.submit()">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?php echo htmlspecialchars($cat['name']); ?>" <?php echo ($category_filter === $cat['name']) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <button type="submit" class="btn-gold">
                <i class="fas fa-filter"></i> Filter
            </button>
        </form>

        <?php if (!empty($search) || !empty($category_filter)): ?>
        <div class="active-filters">
            <span style="font-size:0.78rem;color:#888;">Active filters:</span>
            <?php if (!empty($search)): ?>
                <span class="chip">"<?php echo htmlspecialchars($search); ?>" <a href="products.php?category=<?php echo urlencode($category_filter); ?>" title="Remove">&times;</a></span>
            <?php endif; ?>
            <?php if (!empty($category_filter)): ?>
                <span class="chip"><?php echo htmlspecialchars($category_filter); ?> <a href="products.php?search=<?php echo urlencode($search); ?>" title="Remove">&times;</a></span>
            <?php endif; ?>
            <a href="products.php" style="font-size:0.78rem;color:var(--gold-dark);font-weight:700;text-decoration:underline;">Clear all</a>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- =============================================
     PRODUCTS GRID
============================================= -->
<section class="container-custom">
    <?php if (!empty($products)): ?>
        <p class="results-count">Showing <strong><?php echo $total_count; ?></strong> product<?php echo $total_count == 1 ? '' : 's'; ?><?php echo !empty($category_filter) ? ' in <strong>' . htmlspecialchars($category_filter) . '</strong>' : ''; ?><?php echo !empty($search) ? ' matching <strong>"' . htmlspecialchars($search) . '"</strong>' : ''; ?></p>
        <div class="products-grid">
            <?php foreach ($products as $p): ?>
                <div class="product-card">
                    <div class="product-img-wrap">
                        <?php
                        // ✅ null/empty-safe image check — avoids warnings & broken cards
                        // when an admin-added product has no image yet.
                        $image_path = isset($p['image']) ? (string)$p['image'] : '';
                        $is_remote_img = ($image_path !== '' && stripos($image_path, 'http') === 0);
                        $is_local_img  = ($image_path !== '' && !$is_remote_img && file_exists($image_path));
                        ?>
                        <?php if ($is_remote_img || $is_local_img): ?>
                            <img src="<?php echo htmlspecialchars($image_path); ?>" alt="<?php echo htmlspecialchars($p['name'] ?? 'Product'); ?>" loading="lazy">
                        <?php else: ?>
                            <div style="width:100%;height:100%;display:flex;align-items:center;justify-content:center;background:var(--ivory);color:var(--gold);font-size:3rem;">
                                <i class="fas fa-cogs"></i>
                            </div>
                        <?php endif; ?>
                        <?php if (!empty($p['featured']) && $p['featured'] == 1): ?>
                            <span class="featured-badge"><i class="fas fa-star"></i> Featured</span>
                        <?php endif; ?>
                        <span class="category-badge"><?php echo htmlspecialchars($p['category'] ?? 'Machinery'); ?></span>
                    </div>
                    <div class="product-body">
                        <h3><?php echo htmlspecialchars($p['name'] ?? 'Untitled Product'); ?></h3>
                        <div class="product-category"><?php echo htmlspecialchars($p['category'] ?? 'Machinery'); ?></div>
                        <p class="product-desc"><?php echo htmlspecialchars(substr((string)($p['description'] ?? ''), 0, 120)); ?>...</p>
                        <div class="product-footer">
                            <a href="product-detail.php?id=<?php echo (int)($p['id'] ?? 0); ?>" class="btn-detail">
                                View Details <i class="fas fa-arrow-right"></i>
                            </a>
                            <a href="https://wa.me/918013635806?text=Hi%2C%20I%20need%20a%20quote%20for%20<?php echo urlencode($p['name'] ?? 'this product'); ?>" target="_blank" class="btn-quote-small" title="Request Quote">
                                <i class="fab fa-whatsapp"></i> <span>Quote</span>
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-products">
            <i class="fas fa-box-open"></i>
            <h3>No Products Found</h3>
            <p>We couldn't find any products matching your criteria. Try a different search term or clear the filters.</p>
            <a href="products.php" class="btn-gold" style="display:inline-flex;">
                <i class="fas fa-undo"></i> Clear Filters
            </a>
        </div>
    <?php endif; ?>
</section>

<?php include 'includes/footer.php'; ?>
<?php
// product-detail.php — DipBan Technical Services
// Single Product Detail Page
// ============================================================

$page_title = "Product Detail";
require_once 'includes/db.php';
include 'includes/header.php';

// ── Get product ID from URL ──────────────────────────────────
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

$product = null;
if ($product_id > 0 && isset($conn) && $conn) {
    try {
        $stmt = $conn->prepare("SELECT * FROM products WHERE id = ? AND status = 'active'");
        $stmt->bind_param("i", $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $product = $result->fetch_assoc();
        $stmt->close();
    } catch (Exception $e) {
        $product = null;
    }
}

// ── Product not found ────────────────────────────────────────
if (!$product) { ?>
    <section style="padding:100px 0;text-align:center;background:#FAF6EE;">
        <div style="max-width:560px;margin:0 auto;padding:0 24px;">
            <i class="fas fa-box-open" style="font-size:3.5rem;color:#D5CDB8;display:block;margin-bottom:20px;"></i>
            <h2 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:900;color:#1C1C1C;margin-bottom:10px;">Product Not Found</h2>
            <p style="color:#4A4A4A;margin-bottom:28px;">The product you are looking for does not exist or has been removed.</p>
            <a href="products.php" style="display:inline-flex;align-items:center;gap:8px;background:linear-gradient(135deg,#C9920A,#8B6508);color:white;padding:13px 28px;border-radius:10px;font-weight:700;text-decoration:none;font-family:'Inter',sans-serif;">
                <i class="fas fa-arrow-left"></i> Back to Products
            </a>
        </div>
    </section>
<?php
    include 'includes/footer.php';
    exit();
}

// ── Related products (same category, exclude current) ────────
$related = [];
if (isset($conn) && $conn) {
    try {
        $stmt = $conn->prepare("
            SELECT * FROM products
            WHERE category = ? AND id != ? AND status = 'active'
            ORDER BY featured DESC, sort_order ASC
            LIMIT 4
        ");
        $stmt->bind_param("si", $product['category'], $product_id);
        $stmt->execute();
        $result = $stmt->get_result();
        while ($row = $result->fetch_assoc()) {
            $related[] = $row;
        }
        $stmt->close();
    } catch (Exception $e) { $related = []; }
}

// ── Parse features and specs ───────────────────────────────────
function parse_lines($str) {
    return array_filter(array_map('trim', explode("\n", (string)$str)));
}
$features = isset($product['features']) ? parse_lines($product['features']) : [];
$specs = isset($product['specifications']) ? parse_lines($product['specifications']) : [];

// ── Image path helper ────────────────────────────────────────
// ✅ null/empty-safe: works for both a full http(s) image URL and a local file path,
// and never crashes if 'image' is NULL or missing on this row.
$img_src = '';
$raw_img = isset($product['image']) ? (string)$product['image'] : '';
if ($raw_img !== '') {
    if (stripos($raw_img, 'http') === 0) {
        $img_src = $raw_img;
    } elseif (file_exists($raw_img)) {
        $img_src = $raw_img;
    }
}

// ── Stock Status - Default to In Stock if column doesn't exist ──
$in_stock = 1; // Default: In Stock
if (isset($product['in_stock'])) {
    $in_stock = intval($product['in_stock']);
}

// ── WhatsApp Message ──────────────────────────────────────────
$whatsapp_msg = "Hi DipBan Technical Services,%0A%0AI am interested in your product:%0A%0A📌 *" . urlencode($product['name']) . "*%0A📂 Category: " . urlencode($product['category']) . "%0A%0ACould you please share more details, price, and availability?%0A%0AThank you!";
$whatsapp_url = "https://wa.me/919903126940?text=" . $whatsapp_msg;
?>

<!-- ============================================================
     BREADCRUMB
============================================================ -->
<nav class="pd-breadcrumb">
    <div class="pd-container">
        <a href="index.php"><i class="fas fa-home"></i> Home</a>
        <i class="fas fa-chevron-right"></i>
        <a href="products.php">Products</a>
        <i class="fas fa-chevron-right"></i>
        <a href="products.php?category=<?php echo urlencode($product['category']); ?>"><?php echo htmlspecialchars($product['category']); ?></a>
        <i class="fas fa-chevron-right"></i>
        <span><?php echo htmlspecialchars($product['name']); ?></span>
    </div>
</nav>

<!-- ============================================================
     PRODUCT DETAIL — MAIN SECTION
============================================================ -->
<section class="pd-section">
    <div class="pd-container">
        <div class="pd-grid">

            <!-- ── LEFT: Image Panel ── -->
            <div class="pd-image-col">
                <div class="pd-image-card">
                    <?php if ($img_src): ?>
                        <img src="<?php echo htmlspecialchars($img_src); ?>"
                             alt="<?php echo htmlspecialchars($product['name']); ?>"
                             class="pd-main-img" id="mainProductImg" tabindex="0" role="button"
                             aria-label="Click to view full-size image">
                        <div class="pd-zoom-hint"><i class="fas fa-search-plus"></i> Click to zoom</div>
                    <?php else: ?>
                        <div class="pd-img-placeholder">
                            <i class="fas fa-cogs" style="font-size:5rem;color:#C9920A;opacity:0.5;"></i>
                        </div>
                    <?php endif; ?>

                    <!-- Badges overlay -->
                    <div class="pd-img-badges">
                        <span class="pd-cat-tag">
                            <i class="fas fa-tag"></i>
                            <?php echo htmlspecialchars($product['category']); ?>
                        </span>
                        <?php if (!empty($product['featured']) && $product['featured'] == 1): ?>
                            <span class="pd-featured-tag">
                                <i class="fas fa-star"></i> Featured
                            </span>
                        <?php endif; ?>
                        <!-- ✅ Always shows In Stock by default -->
                        <span class="pd-stock-tag">
                            <i class="fas fa-check-circle"></i> In Stock
                        </span>
                    </div>
                </div>

                <!-- Quick Action Buttons -->
                <div class="pd-quick-actions">
                    <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="pd-btn-whatsapp">
                        <span class="pd-wa-icon-wrap"><i class="fab fa-whatsapp"></i></span> Enquire on WhatsApp
                    </a>
                    <a href="tel:+919903126940" class="pd-btn-call">
                        <i class="fas fa-phone-alt"></i> Call Now
                    </a>
                </div>

                <!-- Trust Badges -->
                <div class="pd-trust-strip">
                    <div class="pd-trust-item">
                        <i class="fas fa-certificate"></i>
                        <span>ISO Certified</span>
                    </div>
                    <div class="pd-trust-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Quality Assured</span>
                    </div>
                    <div class="pd-trust-item">
                        <i class="fas fa-flag"></i>
                        <span>Made in India</span>
                    </div>
                    <div class="pd-trust-item">
                        <i class="fas fa-headset"></i>
                        <span>24/7 Support</span>
                    </div>
                </div>
            </div>

            <!-- ── RIGHT: Info Panel ── -->
            <div class="pd-info-col">

                <!-- Product Name -->
                <h1 class="pd-product-name">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h1>

                <!-- Meta Info -->
                <div class="pd-meta-row">
                    <span class="pd-meta-item">
                        <i class="fas fa-industry"></i> <?php echo htmlspecialchars($product['category']); ?>
                    </span>
                    <?php if (!empty($product['sub_category'])): ?>
                    <span class="pd-meta-item">
                        <i class="fas fa-layer-group"></i> <?php echo htmlspecialchars($product['sub_category']); ?>
                    </span>
                    <?php endif; ?>
                    <!-- ✅ Always shows In Stock -->
                    <span class="pd-meta-item pd-in-stock">
                        <i class="fas fa-circle"></i> In Stock
                    </span>
                </div>

                <!-- Description -->
                <div class="pd-description">
                    <?php
                    $desc = !empty($product['description'])
                        ? nl2br(htmlspecialchars($product['description']))
                        : 'Premium industrial machinery engineered for precision, durability, and efficiency. Built to international standards with premium components for long-term industrial use.';
                    echo $desc;
                    ?>
                </div>

                <!-- Key Features -->
                <?php if (!empty($features)): ?>
                <div class="pd-block">
                    <h3 class="pd-block-title">
                        <i class="fas fa-list-check"></i> Key Features
                    </h3>
                    <ul class="pd-features-list">
                        <?php foreach($features as $f): ?>
                        <li><i class="fas fa-check-circle"></i> <?php echo htmlspecialchars($f); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <!-- Technical Specifications -->
                <?php if (!empty($specs)): ?>
                <div class="pd-block">
                    <h3 class="pd-block-title">
                        <i class="fas fa-microchip"></i> Technical Specifications
                    </h3>
                    <div class="pd-specs-grid">
                        <?php foreach($specs as $s): ?>
                        <div class="pd-spec-row">
                            <i class="fas fa-cog"></i>
                            <?php echo htmlspecialchars($s); ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Quality Points -->
                <div class="pd-quality-points">
                    <div class="pd-qp-item">
                        <i class="fas fa-check-circle"></i> Heavy-duty industrial grade construction
                    </div>
                    <div class="pd-qp-item">
                        <i class="fas fa-check-circle"></i> Energy-efficient operation for reduced costs
                    </div>
                    <div class="pd-qp-item">
                        <i class="fas fa-check-circle"></i> Backed by DipBan 24/7 after-sales service
                    </div>
                    <div class="pd-qp-item">
                        <i class="fas fa-check-circle"></i> Genuine spare parts always available
                    </div>
                </div>

                <!-- CTA Buttons -->
                <div class="pd-cta-row">
                    <a href="<?php echo $whatsapp_url; ?>" target="_blank" class="pd-btn-primary">
                        <span class="pd-wa-icon-wrap"><i class="fab fa-whatsapp"></i></span> Get Quote on WhatsApp
                    </a>
                    <a href="products.php" class="pd-btn-outline">
                        <i class="fas fa-arrow-left"></i> All Products
                    </a>
                </div>

            </div><!-- /pd-info-col -->
        </div><!-- /pd-grid -->
    </div><!-- /pd-container -->
</section>

<!-- ============================================================
     RELATED PRODUCTS
============================================================ -->
<?php if (!empty($related)): ?>
<section class="pd-related">
    <div class="pd-container">
        <div class="pd-related-head">
            <h2>Related <span>Products</span></h2>
            <p>Explore more machines from the <?php echo htmlspecialchars($product['category']); ?> range</p>
        </div>
        <div class="pd-related-grid">
            <?php foreach($related as $r):
                $r_raw = isset($r['image']) ? (string)$r['image'] : '';
                $r_img = '';
                if ($r_raw !== '') {
                    if (stripos($r_raw, 'http') === 0) { $r_img = $r_raw; }
                    elseif (file_exists($r_raw)) { $r_img = $r_raw; }
                }
            ?>
            <a href="product-detail.php?id=<?php echo $r['id']; ?>" class="pd-related-card">
                <div class="pd-rc-img-wrap">
                    <?php if ($r_img): ?>
                        <img src="<?php echo htmlspecialchars($r_img); ?>" alt="<?php echo htmlspecialchars($r['name']); ?>">
                    <?php else: ?>
                        <div class="pd-rc-placeholder">
                            <i class="fas fa-cogs"></i>
                        </div>
                    <?php endif; ?>
                    <span class="pd-rc-cat"><?php echo htmlspecialchars($r['category']); ?></span>
                </div>
                <div class="pd-rc-body">
                    <h4><?php echo htmlspecialchars($r['name']); ?></h4>
                    <p><?php echo htmlspecialchars(substr($r['description'] ?? '', 0, 80)); ?>...</p>
                    <span class="pd-rc-link">View Details <i class="fas fa-arrow-right"></i></span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- ============================================================
     PRODUCT DETAIL PAGE CSS - PREMIUM DESIGN
============================================================ -->
<style>
/* ── Base ─────────────────────────────────────────────────── */
:root{
    --wa-deep:#0c3d2e; --wa-mid:#145c43; --wa-light:#2f8f6b; --gold-light:#e6c9a0;
}
.pd-container {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
}

/* ── Breadcrumb ──────────────────────────────────────────── */
.pd-breadcrumb {
    background: #1C1C1C;
    border-bottom: 3px solid #C9920A;
    padding: 14px 0;
}
.pd-breadcrumb .pd-container {
    display: flex;
    align-items: center;
    gap: 8px;
    flex-wrap: wrap;
}
.pd-breadcrumb a {
    color: #D5CDB8;
    font-size: 0.78rem;
    font-weight: 500;
    text-decoration: none;
    transition: color .3s;
}
.pd-breadcrumb a:hover { color: #C9920A; }
.pd-breadcrumb i.fa-chevron-right { color: #666; font-size: 0.6rem; }
.pd-breadcrumb span {
    color: #C9920A;
    font-size: 0.78rem;
    font-weight: 600;
}

/* ── Main Section ────────────────────────────────────────── */
.pd-section {
    background: linear-gradient(180deg, #FAF6EE 0%, #ffffff 100%);
    padding: 60px 0 80px;
}
.pd-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 60px;
    align-items: start;
}

/* ── Image Column ────────────────────────────────────────── */
.pd-image-card {
    border-radius: 20px;
    overflow: hidden;
    background: #ffffff;
    border: 2px solid rgba(201,146,10,0.15);
    box-shadow: 0 8px 40px rgba(0,0,0,0.08);
    position: relative;
    margin-bottom: 20px;
    transition: box-shadow .4s ease;
}
.pd-image-card:hover {
    box-shadow: 0 12px 56px rgba(201,146,10,0.15);
}
.pd-main-img {
    width: 100%;
    height: 460px;
    object-fit: cover;
    display: block;
    transition: transform .6s ease;
}
.pd-image-card:hover .pd-main-img { transform: scale(1.02); }
.pd-main-img:hover { transform: scale(1.02); cursor: zoom-in; }
.pd-img-placeholder {
    width: 100%;
    height: 460px;
    background: linear-gradient(135deg, #F0E8D0, #FAF6EE);
    display: flex;
    align-items: center;
    justify-content: center;
}

/* "Click to zoom" hint badge on the main product photo */
.pd-zoom-hint {
    position: absolute;
    bottom: 16px;
    right: 16px;
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(28,28,28,0.78);
    color: #F0D9A0;
    font-size: 0.7rem;
    font-weight: 700;
    letter-spacing: .03em;
    padding: 6px 12px;
    border-radius: 20px;
    backdrop-filter: blur(4px);
    pointer-events: none;
    opacity: 0;
    transform: translateY(4px);
    transition: all 0.25s ease;
}
.pd-image-card:hover .pd-zoom-hint { opacity: 1; transform: translateY(0); }

/* ============================================================
   IMAGE LIGHTBOX — full-screen zoomed preview
============================================================ */
.pd-lightbox {
    position: fixed;
    inset: 0;
    z-index: 9999;
    background: rgba(10,8,4,0.92);
    display: none;
    align-items: center;
    justify-content: center;
    padding: 40px 24px;
    cursor: zoom-out;
    opacity: 0;
    transition: opacity 0.25s ease;
}
.pd-lightbox.open {
    display: flex;
    opacity: 1;
}
.pd-lightbox img {
    max-width: min(92vw, 1100px);
    max-height: 86vh;
    object-fit: contain;
    border-radius: 10px;
    box-shadow: 0 30px 80px rgba(0,0,0,0.5);
    border: 1px solid rgba(201,146,10,0.3);
    transform: scale(0.96);
    transition: transform 0.25s ease;
    cursor: default;
}
.pd-lightbox.open img { transform: scale(1); }
.pd-lightbox-close {
    position: absolute;
    top: 22px;
    right: 26px;
    width: 44px;
    height: 44px;
    border-radius: 50%;
    border: 1.5px solid rgba(201,146,10,0.4);
    background: rgba(255,255,255,0.06);
    color: #F0E0B0;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    transition: all 0.25s ease;
    z-index: 1;
}
.pd-lightbox-close:hover {
    background: #C9920A;
    color: #1C1C1C;
    border-color: #C9920A;
    transform: rotate(90deg);
}
.pd-lightbox-hint {
    position: absolute;
    bottom: 24px;
    left: 50%;
    transform: translateX(-50%);
    color: rgba(255,255,255,0.55);
    font-size: 0.78rem;
    display: flex;
    align-items: center;
    gap: 6px;
    pointer-events: none;
}
@media (max-width: 640px) {
    .pd-lightbox-close { top: 14px; right: 14px; width: 38px; height: 38px; font-size: 1rem; }
    .pd-zoom-hint { font-size: 0.64rem; padding: 5px 10px; bottom: 12px; right: 12px; }
}

/* Badges */
.pd-img-badges {
    position: absolute;
    top: 16px;
    left: 16px;
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}
.pd-cat-tag {
    background: rgba(201,146,10,0.95);
    color: white;
    font-size: 0.65rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: 5px 14px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 16px rgba(201,146,10,0.3);
}
.pd-featured-tag {
    background: rgba(28,28,28,0.9);
    color: #F0C040;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 16px rgba(0,0,0,0.2);
}
.pd-stock-tag {
    background: rgba(39,174,96,0.95);
    color: white;
    font-size: 0.65rem;
    font-weight: 700;
    padding: 5px 14px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    gap: 6px;
    backdrop-filter: blur(4px);
    box-shadow: 0 4px 16px rgba(39,174,96,0.3);
}

/* Quick Actions */
.pd-quick-actions {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
    margin-bottom: 16px;
}

/* =============================================
   CLASSIC PREMIUM WHATSAPP BUTTON
   — deep forest-green base + gold trim & icon badge,
   replaces the stock neon-green WhatsApp look
============================================= */
.pd-btn-whatsapp {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    background: linear-gradient(135deg, var(--wa-mid) 0%, var(--wa-deep) 100%);
    color: #ffffff;
    padding: 12px 18px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.85rem;
    text-decoration: none;
    transition: all .3s cubic-bezier(.2,.9,.3,1);
    border: 1px solid rgba(201,146,10,0.35);
    box-shadow: 0 6px 22px rgba(12,61,46,0.4);
}
.pd-btn-whatsapp:hover {
    background: linear-gradient(135deg, var(--wa-deep) 0%, #08291f 100%);
    border-color: #C9920A;
    transform: translateY(-2px);
    box-shadow: 0 10px 28px rgba(12,61,46,0.55);
    color: #ffffff;
}
.pd-wa-icon-wrap {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 22px; height: 22px;
    border-radius: 50%;
    background: rgba(201,146,10,0.22);
    border: 1px solid rgba(201,146,10,0.4);
    flex-shrink: 0;
}
.pd-wa-icon-wrap i { font-size: 0.85rem; color: var(--gold-light); }
.pd-btn-whatsapp:hover .pd-wa-icon-wrap i,
.pd-btn-primary:hover .pd-wa-icon-wrap i { color: #C9920A; }

.pd-btn-call {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    background: #1C1C1C;
    color: white;
    padding: 12px 18px;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.85rem;
    text-decoration: none;
    transition: all .3s;
    border: 2px solid #1C1C1C;
}
.pd-btn-call:hover {
    background: transparent;
    color: #1C1C1C;
    transform: translateY(-2px);
}

/* Trust Strip */
.pd-trust-strip {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 8px;
}
.pd-trust-item {
    display: flex;
    align-items: center;
    gap: 8px;
    background: #ffffff;
    border: 1px solid rgba(201,146,10,0.12);
    border-radius: 10px;
    padding: 10px 14px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #1C1C1C;
    transition: all .3s;
}
.pd-trust-item:hover {
    border-color: #C9920A;
    background: rgba(201,146,10,0.05);
    transform: translateY(-2px);
}
.pd-trust-item i {
    color: #C9920A;
    font-size: 0.9rem;
    flex-shrink: 0;
}

/* ── Info Column ─────────────────────────────────────────── */
.pd-product-name {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(1.8rem, 3.2vw, 2.6rem);
    font-weight: 900;
    color: #1C1C1C;
    line-height: 1.2;
    margin-bottom: 12px;
}

/* Meta Row */
.pd-meta-row {
    display: flex;
    gap: 10px;
    flex-wrap: wrap;
    margin-bottom: 20px;
}
.pd-meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    background: #F0E8D0;
    border: 1px solid rgba(201,146,10,0.18);
    border-radius: 20px;
    padding: 5px 14px;
    font-size: 0.75rem;
    font-weight: 600;
    color: #4A4A4A;
}
.pd-meta-item i { color: #C9920A; font-size: 0.7rem; }
.pd-in-stock {
    color: #27ae60;
    background: #e8f8ef;
    border-color: #27ae60;
}
.pd-in-stock i { color: #27ae60; }
.pd-out-of-stock {
    color: #e74c3c;
    background: #fde8e8;
    border-color: #e74c3c;
}
.pd-out-of-stock i { color: #e74c3c; }

/* Description */
.pd-description {
    color: #4A4A4A;
    font-size: 0.95rem;
    line-height: 1.8;
    margin-bottom: 20px;
    padding: 16px 20px;
    background: rgba(201,146,10,0.04);
    border-radius: 12px;
    border-left: 4px solid #C9920A;
}

/* Block (features / specs) */
.pd-block {
    background: #ffffff;
    border: 1px solid rgba(201,146,10,0.1);
    border-radius: 14px;
    padding: 18px 22px;
    margin-bottom: 16px;
    box-shadow: 0 2px 12px rgba(0,0,0,0.04);
    transition: box-shadow .3s;
}
.pd-block:hover {
    box-shadow: 0 4px 20px rgba(201,146,10,0.08);
}
.pd-block-title {
    font-family: 'Playfair Display', serif;
    font-size: 1rem;
    font-weight: 700;
    color: #1C1C1C;
    margin-bottom: 12px;
    display: flex;
    align-items: center;
    gap: 8px;
    padding-bottom: 10px;
    border-bottom: 2px solid rgba(201,146,10,0.12);
}
.pd-block-title i { color: #C9920A; }

.pd-features-list { list-style: none; padding: 0; }
.pd-features-list li {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    padding: 8px 0;
    font-size: 0.87rem;
    color: #4A4A4A;
    border-bottom: 1px solid rgba(201,146,10,0.06);
    line-height: 1.5;
}
.pd-features-list li:last-child { border-bottom: none; }
.pd-features-list li i {
    color: #C9920A;
    margin-top: 2px;
    flex-shrink: 0;
}

.pd-specs-grid { display: flex; flex-direction: column; gap: 6px; }
.pd-spec-row {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 0.86rem;
    color: #4A4A4A;
    background: #FAF6EE;
    padding: 8px 14px;
    border-radius: 8px;
    border-left: 3px solid #C9920A;
}
.pd-spec-row i { color: #C9920A; flex-shrink: 0; }

/* Quality Points */
.pd-quality-points {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 8px;
    margin: 16px 0 24px;
    padding: 16px 18px;
    background: linear-gradient(135deg, rgba(201,146,10,0.06), rgba(201,146,10,0.02));
    border-radius: 12px;
    border: 1px solid rgba(201,146,10,0.1);
}
.pd-qp-item {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 0.82rem;
    font-weight: 500;
    color: #4A4A4A;
    line-height: 1.4;
}
.pd-qp-item i { color: #C9920A; flex-shrink: 0; }

/* CTA Row */
.pd-cta-row {
    display: flex;
    gap: 12px;
    flex-wrap: wrap;
}
.pd-btn-primary {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, var(--wa-mid) 0%, var(--wa-deep) 100%);
    color: #ffffff;
    padding: 14px 32px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all .3s cubic-bezier(.2,.9,.3,1);
    border: 1px solid rgba(201,146,10,0.35);
    box-shadow: 0 6px 26px rgba(12,61,46,0.42);
}
.pd-btn-primary:hover {
    background: linear-gradient(135deg, var(--wa-deep) 0%, #08291f 100%);
    border-color: #C9920A;
    transform: translateY(-3px);
    box-shadow: 0 10px 32px rgba(12,61,46,0.55);
    color: #ffffff;
}
.pd-btn-outline {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: 2px solid #C9920A;
    color: #C9920A;
    background: transparent;
    padding: 12px 28px;
    border-radius: 12px;
    font-weight: 700;
    font-size: 0.9rem;
    text-decoration: none;
    transition: all .3s;
}
.pd-btn-outline:hover {
    background: #C9920A;
    color: white;
    transform: translateY(-3px);
    box-shadow: 0 4px 20px rgba(201,146,10,0.3);
}

/* ── Related Products ────────────────────────────────────── */
.pd-related {
    background: #F0E8D0;
    padding: 60px 0 80px;
    border-top: 4px solid #C9920A;
}
.pd-related-head {
    margin-bottom: 40px;
}
.pd-related-head h2 {
    font-family: 'Playfair Display', serif;
    font-size: clamp(1.6rem, 3vw, 2.2rem);
    font-weight: 900;
    color: #1C1C1C;
    margin-bottom: 4px;
}
.pd-related-head h2 span { color: #C9920A; }
.pd-related-head p { color: #4A4A4A; font-size: 0.9rem; }

.pd-related-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
}
.pd-related-card {
    background: #ffffff;
    border-radius: 16px;
    overflow: hidden;
    border: 1px solid rgba(201,146,10,0.1);
    box-shadow: 0 2px 16px rgba(0,0,0,0.06);
    text-decoration: none;
    color: inherit;
    transition: all .4s;
    display: flex;
    flex-direction: column;
}
.pd-related-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0,0,0,0.12);
    border-color: #C9920A;
}
.pd-rc-img-wrap {
    position: relative;
    height: 180px;
    overflow: hidden;
    background: #F0E8D0;
}
.pd-rc-img-wrap img {
    width: 100%; height: 100%;
    object-fit: cover;
    transition: transform .5s ease;
}
.pd-related-card:hover .pd-rc-img-wrap img { transform: scale(1.06); }
.pd-rc-placeholder {
    width: 100%; height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #C9920A;
    font-size: 2.4rem;
    background: #F0E8D0;
}
.pd-rc-cat {
    position: absolute;
    top: 12px;
    left: 12px;
    background: rgba(201,146,10,0.92);
    color: white;
    font-size: 0.6rem;
    font-weight: 700;
    letter-spacing: .08em;
    text-transform: uppercase;
    padding: 4px 12px;
    border-radius: 20px;
}
.pd-rc-body {
    padding: 18px 20px 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.pd-rc-body h4 {
    font-family: 'Playfair Display', serif;
    font-size: 0.95rem;
    font-weight: 700;
    color: #1C1C1C;
    margin-bottom: 6px;
    line-height: 1.3;
}
.pd-rc-body p {
    font-size: 0.78rem;
    color: #4A4A4A;
    line-height: 1.5;
    margin-bottom: 12px;
    flex: 1;
}
.pd-rc-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 0.78rem;
    font-weight: 700;
    color: #C9920A;
    transition: gap .3s;
}
.pd-related-card:hover .pd-rc-link { gap: 12px; }

/* ── Responsive ──────────────────────────────────────────── */
@media (max-width: 1024px) {
    .pd-related-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 900px) {
    .pd-grid { grid-template-columns: 1fr; gap: 40px; }
    .pd-main-img, .pd-img-placeholder { height: 340px; }
    .pd-quick-actions { grid-template-columns: 1fr; }
}
@media (max-width: 640px) {
    .pd-trust-strip { grid-template-columns: 1fr 1fr; }
    .pd-quality-points { grid-template-columns: 1fr; }
    .pd-related-grid { grid-template-columns: 1fr; }
    .pd-cta-row a { flex: 1; justify-content: center; }
    .pd-main-img, .pd-img-placeholder { height: 260px; }
    .pd-product-name { font-size: 1.5rem; }
    .pd-btn-primary, .pd-btn-outline { padding: 12px 20px; font-size: 0.8rem; }
    .pd-block { padding: 14px 16px; }
    .pd-description { padding: 12px 16px; font-size: 0.88rem; }
}
</style>

<!-- ============================================================
     IMAGE LIGHTBOX — opens when the main product photo is clicked
============================================================ -->
<?php if ($img_src): ?>
<div class="pd-lightbox" id="pdLightbox" role="dialog" aria-modal="true" aria-label="Product image preview">
    <button type="button" class="pd-lightbox-close" id="pdLightboxClose" aria-label="Close image preview">
        <i class="fas fa-times"></i>
    </button>
    <div class="pd-lightbox-hint"><i class="fas fa-search-minus"></i> Click anywhere to close</div>
    <img src="<?php echo htmlspecialchars($img_src); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="pdLightboxImg">
</div>
<?php endif; ?>

<script>
// ============================================================
// IMAGE LIGHTBOX — click the product photo to view it full-size
// ============================================================
(function () {
    var mainImg   = document.getElementById('mainProductImg');
    var lightbox  = document.getElementById('pdLightbox');
    var closeBtn  = document.getElementById('pdLightboxClose');
    if (!mainImg || !lightbox) return;

    function openLightbox() {
        lightbox.classList.add('open');
        document.body.style.overflow = 'hidden'; // lock background scroll while open
        closeBtn.focus();
    }
    function closeLightbox() {
        lightbox.classList.remove('open');
        document.body.style.overflow = '';
        mainImg.focus();
    }

    mainImg.addEventListener('click', openLightbox);
    mainImg.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); openLightbox(); }
    });

    // Clicking the dark backdrop (anywhere that isn't the image itself) closes it
    lightbox.addEventListener('click', function (e) {
        if (e.target === lightbox) closeLightbox();
    });
    closeBtn.addEventListener('click', closeLightbox);

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && lightbox.classList.contains('open')) closeLightbox();
    });
})();
</script>

<?php include 'includes/footer.php'; ?>
<?php
$page_title = "Gallery | Arup Enterprise";
require_once 'includes/db.php';
include 'includes/header.php';

// ── Fetch active products that have an image, from the database ──────────────
$gallery_items = [];
if (isset($conn) && $conn) {
    try {
        $result = $conn->query("
            SELECT id, name, category, image, description
            FROM products
            WHERE status = 'active' AND image != '' AND image IS NOT NULL
            ORDER BY featured DESC, sort_order ASC, id DESC
        ");
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $gallery_items[] = $row;
            }
        }
    } catch (Exception $e) {
        $gallery_items = [];
    }
}

// ── Unique categories for the filter tabs ─────────────────────────────────
$unique_cats = array_unique(array_column($gallery_items, 'category'));
sort($unique_cats);
$total = count($gallery_items);
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
.gallery-hero {
    background: linear-gradient(135deg, rgba(20,20,20,0.88) 0%, rgba(20,20,20,0.65) 100%), url('assets/images/about/ab-scaled.jpg') center/cover no-repeat;
    padding: 80px 20px 60px;
    text-align: center;
    color: #fff;
    position: relative;
}
.gallery-hero h1 { font-family: 'Playfair Display', Georgia, serif; font-size: clamp(2rem, 4vw, 3.2rem); font-weight: 900; margin-bottom: 12px; }
.gallery-hero h1 span { color: var(--gold); }
.gallery-breadcrumb { display: inline-flex; align-items: center; gap: 8px; font-size: 0.88rem; color: rgba(255,255,255,0.75); }
.gallery-breadcrumb a { color: var(--gold-light); text-decoration: none; }
.gallery-breadcrumb a:hover { text-decoration: underline; }
.gallery-section { padding: 60px 0 80px; background: var(--cream); }
.gallery-intro { text-align: center; max-width: 720px; margin: 0 auto 32px; }
.gallery-intro p { font-size: 1rem; color: var(--mid-gray); line-height: 1.7; }
.gallery-filters { display: flex; flex-wrap: wrap; gap: 8px; justify-content: center; margin-bottom: 36px; }
.gf-btn { background: #fff; border: 2px solid rgba(239,68,68,0.2); color: var(--charcoal); font-size: 0.78rem; font-weight: 600; padding: 7px 18px; border-radius: 20px; cursor: pointer; transition: all 0.25s; font-family: 'Inter', sans-serif; }
.gf-btn:hover, .gf-btn.active { background: var(--gold); border-color: var(--gold); color: #fff; }
.gf-count { background: rgba(239,68,68,0.1); color: var(--gold); font-weight: 800; padding: 1px 8px; border-radius: 10px; margin-left: 4px; }
.gf-btn.active .gf-count { background: rgba(255,255,255,0.25); color: #fff; }
.gallery-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 26px; }
@media (max-width: 992px) { .gallery-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 600px)  { .gallery-grid { grid-template-columns: 1fr; } }
.gallery-card { background: #fff; border-radius: 12px; overflow: hidden; box-shadow: 0 2px 12px rgba(0,0,0,0.07); border: 1px solid rgba(239,68,68,0.15); transition: all 0.3s ease; display: flex; flex-direction: column; }
.gallery-card.hidden { display: none; }
.gallery-card:hover { transform: translateY(-5px); box-shadow: 0 8px 28px rgba(0,0,0,0.11); border-color: var(--gold); }
.gallery-img-wrap { position: relative; height: 240px; overflow: hidden; background: var(--ivory); }
.gallery-img-wrap img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.5s ease; display: block; }
.gallery-img-placeholder { width: 100%; height: 100%; display: flex; align-items: center; justify-content: center; font-size: 3rem; color: var(--gold); opacity: 0.4; background: var(--ivory); }
.gallery-card:hover .gallery-img-wrap img { transform: scale(1.08); }
.gallery-cat-badge { position: absolute; top: 12px; left: 12px; background: var(--gold); color: #fff; font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; padding: 4px 12px; border-radius: 20px; box-shadow: 0 2px 8px rgba(0,0,0,0.25); }
.gallery-card-body { padding: 20px; flex: 1; display: flex; flex-direction: column; }
.gallery-card-body h3 { font-size: 1rem; font-weight: 700; color: var(--charcoal); margin-bottom: 8px; line-height: 1.35; }
.gallery-card-body p { font-size: 0.84rem; color: #666; line-height: 1.55; margin-bottom: 16px; flex: 1; display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }
.gallery-card-footer { display: flex; align-items: center; justify-content: space-between; border-top: 1px solid rgba(239,68,68,0.1); padding-top: 12px; }
.gallery-card-footer a { color: var(--gold-dark); font-size: 0.81rem; font-weight: 700; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all 0.25s; }
.gallery-card-footer a:hover { color: var(--gold); transform: translateX(3px); }
.gallery-card-footer a.get-detail { color: var(--charcoal); }
.gallery-empty { text-align: center; padding: 70px 20px; background: #fff; border-radius: 16px; border: 1px solid rgba(239,68,68,0.12); grid-column: 1/-1; }
.gallery-empty i { font-size: 3.5rem; color: var(--gold); opacity: 0.4; display:block; margin-bottom: 16px; }
.gallery-empty h3 { font-size: 1.2rem; color: var(--charcoal); margin-bottom: 8px; }
.gallery-empty p { color: #888; font-size: 0.9rem; }
</style>

<!-- ===== HERO BANNER ===== -->
<section class="gallery-hero">
    <div class="container">
        <h1>Equipment <span>Gallery</span></h1>
        <div class="gallery-breadcrumb">
            <a href="index.php"><i class="fas fa-home me-1"></i> Home</a>
            <span>/</span>
            <span>Gallery</span>
        </div>
    </div>
</section>

<!-- ===== GALLERY SECTION ===== -->
<section class="gallery-section">
    <div class="container">
        <div class="gallery-intro">
            <span style="color:var(--gold);font-size:0.75rem;text-transform:uppercase;letter-spacing:2px;font-weight:800;display:block;margin-bottom:8px;">Visual Excellence</span>
            <h2 style="font-family:'Playfair Display',serif;font-size:2rem;font-weight:800;color:var(--charcoal);margin-bottom:12px;">Industrial Separation Machinery in Action</h2>
            <p>Explore our range of heavy-duty magnetic separators, drum assemblies, pulleys, and metal recycling equipment built for superior durability and maximum ferrous recovery.</p>
        </div>

        <?php if (!empty($unique_cats)): ?>
        <div class="gallery-filters">
            <button class="gf-btn active" data-filter="all">All <span class="gf-count"><?php echo $total; ?></span></button>
            <?php foreach ($unique_cats as $cat):
                $cat_count = count(array_filter($gallery_items, function($i) use ($cat){ return $i['category'] === $cat; })); ?>
            <button class="gf-btn" data-filter="<?php echo htmlspecialchars($cat, ENT_QUOTES); ?>"><?php echo htmlspecialchars($cat); ?> <span class="gf-count"><?php echo $cat_count; ?></span></button>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="gallery-grid" id="galleryGrid">
            <?php if (empty($gallery_items)): ?>
            <div class="gallery-empty">
                <i class="fas fa-images"></i>
                <h3>Gallery Coming Soon</h3>
                <p>Our product gallery is being updated. Please check back shortly.</p>
            </div>
            <?php else: ?>
            <?php foreach ($gallery_items as $item):
                $img = trim($item['image'] ?? '');
                $img_src = '';
                if ($img !== '') {
                    if (stripos($img, 'http') === 0) {
                        $img_src = $img;
                    } elseif (file_exists($img)) {
                        $img_src = $img;
                    } else {
                        $img_src = $img; // let browser try, onerror handles it
                    }
                }
                $desc = !empty($item['description'])
                    ? strip_tags($item['description'])
                    : 'High-quality industrial magnetic equipment built for durability and performance.';
            ?>
            <div class="gallery-card" data-category="<?php echo htmlspecialchars($item['category'], ENT_QUOTES); ?>">
                <div class="gallery-img-wrap">
                    <?php if ($img_src): ?>
                    <img src="<?php echo htmlspecialchars($img_src); ?>"
                         alt="<?php echo htmlspecialchars($item['name']); ?>"
                         loading="lazy"
                         onerror="this.style.display='none';this.parentElement.querySelector('.gallery-img-placeholder').style.display='flex';">
                    <div class="gallery-img-placeholder" style="display:none;"><i class="fas fa-cogs"></i></div>
                    <?php else: ?>
                    <div class="gallery-img-placeholder"><i class="fas fa-cogs"></i></div>
                    <?php endif; ?>
                    <span class="gallery-cat-badge"><?php echo htmlspecialchars($item['category']); ?></span>
                </div>
                <div class="gallery-card-body">
                    <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                    <p><?php echo htmlspecialchars($desc); ?></p>
                    <div class="gallery-card-footer">
                        <a href="products.php?category=<?php echo urlencode($item['category']); ?>">
                            View Products <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="product-detail.php?id=<?php echo intval($item['id']); ?>" class="get-detail">
                            View Details <i class="fas fa-external-link-alt"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<script>
(function(){
    var btns  = document.querySelectorAll('.gf-btn');
    var cards = document.querySelectorAll('#galleryGrid .gallery-card');
    btns.forEach(function(btn){
        btn.addEventListener('click', function(){
            btns.forEach(function(b){ b.classList.remove('active'); });
            btn.classList.add('active');
            var filter = btn.getAttribute('data-filter');
            cards.forEach(function(card){
                if (filter === 'all' || card.getAttribute('data-category') === filter) {
                    card.classList.remove('hidden');
                } else {
                    card.classList.add('hidden');
                }
            });
        });
    });
})();
</script>

<?php include 'includes/footer.php'; ?>

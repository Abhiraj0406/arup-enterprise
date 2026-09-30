<?php
$page_title = "Gallery | Arup Enterprise";
include 'includes/header.php';

// Gallery machinery images
$gallery_items = [
    [
        'title'    => 'Single Drum Magnetic Separator Machine',
        'category' => 'Drum Separator',
        'image'    => 'assets/uploads/products/single-drum-magnetic-separator-machine.jpg',
        'desc'     => 'Continuous automatic ferrous separation for granular minerals and slag.'
    ],
    [
        'title'    => 'Double Drum Magnetic Separator Machine',
        'category' => 'Drum Separator',
        'image'    => 'assets/uploads/products/double-drum-magnetic-separator-machine.jpg',
        'desc'     => 'Two-stage high-intensity magnetic separation for ultra-pure recovery.'
    ],
    [
        'title'    => 'Overband Magnetic Separator',
        'category' => 'Overband Magnet',
        'image'    => 'assets/uploads/products/overband-magnetic-separator.jpg',
        'desc'     => 'Cross-belt continuous tramp iron extraction from moving conveyors.'
    ],
    [
        'title'    => 'Chalna Vibrating Feeder Machine',
        'category' => 'Feeder Separator',
        'image'    => 'assets/uploads/products/chalna-vibrating-feeder-drum.jpeg',
        'desc'     => 'Even material distribution paired with magnetic drum purification.'
    ],
    [
        'title'    => 'Permanent Magnetic Head Pulley',
        'category' => 'Pulleys & Rollers',
        'image'    => 'assets/uploads/products/permanent-magnetic-head-pulley.jpg',
        'desc'     => 'Conveyor discharge protection against tramp iron contamination.'
    ],
    [
        'title'    => 'Roller Type High Intensity Magnetic Separator',
        'category' => 'Pulleys & Rollers',
        'image'    => 'assets/uploads/products/roller-type-magnetic-separator.jpeg',
        'desc'     => 'Rare earth magnetic roll for feebly magnetic mineral concentration.'
    ],
    [
        'title'    => 'Heavy Duty Industrial Lifting Magnet',
        'category' => 'Lifting & Handling',
        'image'    => 'assets/uploads/products/lifting-magnet.jpg',
        'desc'     => 'Safe and rapid handling of heavy steel plates, slabs, and billets.'
    ],
    [
        'title'    => 'Industrial Magnetic Floor Sweeper',
        'category' => 'Floor Sweepers',
        'image'    => 'assets/uploads/products/magnetic-floor-sweeper.jpg',
        'desc'     => 'Clear nails, screws, and tramp metal from workshops and plant floors.'
    ],
    [
        'title'    => 'Drum Magnet Unit Internal Assembly',
        'category' => 'Drum Separator',
        'image'    => 'assets/uploads/products/drum-magnet-unit.jpg',
        'desc'     => 'Precision engineered permanent magnetic core with stainless shell.'
    ],
    [
        'title'    => 'High-Volume Magnetic Drum Assembly',
        'category' => 'Drum Separator',
        'image'    => 'assets/uploads/products/magnetic-drum-assembly.jpeg',
        'desc'     => 'Turnkey drum assembly ready for plant integration.'
    ],
    [
        'title'    => 'Suspension Magnetic Box',
        'category' => 'Suspension Magnets',
        'image'    => 'assets/uploads/products/suspension-magnetic-box.jpeg',
        'desc'     => 'Deep-reach permanent suspension plate for chute and conveyor lines.'
    ],
    [
        'title'    => 'Fabrication & Commissioning Plant',
        'category' => 'Manufacturing',
        'image'    => 'assets/images/about/WhatsApp-Image-2025-05-13-at-11.50.29-AM-1024x1024.jpeg',
        'desc'     => 'In-house manufacturing facility ensuring precision tolerances.'
    ],
];
?>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
/* Gallery Page Styles */
.gallery-hero {
    background: linear-gradient(135deg, rgba(20,20,20,0.85) 0%, rgba(20,20,20,0.65) 100%), url('assets/images/about/ab-scaled.jpg') center/cover no-repeat;
    padding: 80px 20px 60px;
    text-align: center;
    color: #fff;
    position: relative;
}
.gallery-hero h1 {
    font-family: 'Playfair Display', Georgia, serif;
    font-size: clamp(2rem, 4vw, 3.2rem);
    font-weight: 900;
    margin-bottom: 12px;
}
.gallery-hero h1 span {
    color: var(--gold);
}
.gallery-breadcrumb {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    font-size: 0.88rem;
    color: rgba(255,255,255,0.75);
}
.gallery-breadcrumb a {
    color: var(--gold-light);
    text-decoration: none;
}
.gallery-breadcrumb a:hover {
    text-decoration: underline;
}
.gallery-section {
    padding: 60px 0 80px;
    background: var(--cream);
}
.gallery-intro {
    text-align: center;
    max-width: 720px;
    margin: 0 auto 40px;
}
.gallery-intro p {
    font-size: 1rem;
    color: var(--mid-gray);
    line-height: 1.7;
}
.gallery-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 26px;
}
@media (max-width: 992px) {
    .gallery-grid { grid-template-columns: repeat(2, 1fr); }
}
@media (max-width: 600px) {
    .gallery-grid { grid-template-columns: 1fr; }
}
.gallery-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: var(--shadow-sm);
    border: 1px solid rgba(239,68,68,0.15);
    transition: all var(--ease);
    display: flex;
    flex-direction: column;
}
.gallery-card:hover {
    transform: translateY(-5px);
    box-shadow: var(--shadow-md);
    border-color: var(--gold);
}
.gallery-img-wrap {
    position: relative;
    height: 240px;
    overflow: hidden;
    background: #111;
}
.gallery-img-wrap img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.5s ease;
    display: block;
}
.gallery-card:hover .gallery-img-wrap img {
    transform: scale(1.08);
}
.gallery-cat-badge {
    position: absolute;
    top: 12px;
    left: 12px;
    background: var(--gold);
    color: #fff;
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    padding: 4px 12px;
    border-radius: 20px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.25);
}
.gallery-card-body {
    padding: 20px;
    flex: 1;
    display: flex;
    flex-direction: column;
}
.gallery-card-body h3 {
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--charcoal);
    margin-bottom: 8px;
    line-height: 1.35;
}
.gallery-card-body p {
    font-size: 0.85rem;
    color: #666;
    line-height: 1.55;
    margin-bottom: 16px;
    flex: 1;
}
.gallery-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    border-top: 1px solid rgba(239,68,68,0.1);
    padding-top: 12px;
}
.gallery-card-footer a {
    color: var(--gold-dark);
    font-size: 0.82rem;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: all var(--ease);
}
.gallery-card-footer a:hover {
    color: var(--gold);
    transform: translateX(3px);
}
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
            <span class="section-eyebrow" style="color:var(--gold); font-size:0.75rem; text-transform:uppercase; letter-spacing:2px; font-weight:800; display:block; margin-bottom:8px;">Visual Excellence</span>
            <h2 style="font-family:'Playfair Display', serif; font-size:2rem; font-weight:800; color:var(--charcoal); margin-bottom:12px;">Industrial Separation Machinery in Action</h2>
            <p>Explore our range of heavy-duty magnetic separators, drum assemblies, pulleys, and metal recycling equipment built for superior durability, maximum ferrous recovery, and long operational lifespan.</p>
        </div>

        <div class="gallery-grid">
            <?php foreach ($gallery_items as $item): ?>
            <div class="gallery-card">
                <div class="gallery-img-wrap">
                    <img src="<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['title']); ?>" loading="lazy">
                    <span class="gallery-cat-badge"><?php echo htmlspecialchars($item['category']); ?></span>
                </div>
                <div class="gallery-card-body">
                    <h3><?php echo htmlspecialchars($item['title']); ?></h3>
                    <p><?php echo htmlspecialchars($item['desc']); ?></p>
                    <div class="gallery-card-footer">
                        <a href="products.php?search=<?php echo urlencode($item['category']); ?>">
                            View Products <i class="fas fa-arrow-right"></i>
                        </a>
                        <a href="contact.php?product=<?php echo urlencode($item['title']); ?>" style="color:var(--charcoal); font-weight:600;">
                            Get Quote <i class="fas fa-paper-plane"></i>
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>

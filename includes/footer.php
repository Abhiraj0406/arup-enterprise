<?php
// footer.php — Arup Enterprise
// PDF filenames (place your PDFs in /uploads/pdfs/ folder)
$pdf_dipban   = 'uploads/pdfs/dipban-technical-products.pdf';
$pdf_honney   = 'uploads/pdfs/honney-impex-products.pdf';
?>

<!-- ===== FOOTER ===== -->
<footer class="site-footer">

    <!-- Gold divider line with gear icon -->
    <div class="footer-divider">
        <span class="divider-line"></span>
        <span class="divider-icon"><i class="fas fa-cog fa-spin"></i></span>
        <span class="divider-line"></span>
    </div>

    <!-- PDF DOWNLOAD STRIP -->
    <div class="pdf-strip">
        <div class="pdf-strip-inner">
            <div class="pdf-strip-label">
                <i class="fas fa-file-pdf"></i>
                <span>Download Our Product Catalogues</span>
            </div>
            <div class="pdf-strip-buttons">
                <a href="assets/folder/Dipban-Technical-Services-Brochure.pdf" download class="pdf-btn">
                    <i class="fas fa-download"></i>
                    <div>
                        <span class="pdf-btn-title">Arup Enterprise Technical</span>
                        <span class="pdf-btn-sub">Product Catalogue PDF</span>
                    </div>
                </a>
                <a href="#" class="pdf-btn pdf-btn-alt">
                    <i class="fas fa-download"></i>
                    <div>
                        <span class="pdf-btn-title">Magnetic Separators</span>
                        <span class="pdf-btn-sub">Product Catalogue PDF</span>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- MAIN FOOTER GRID -->
    <div class="footer-main">
        <div class="footer-inner">

            <!-- COL 1: Brand -->
            <div class="footer-col footer-brand">
                <div class="footer-logo">
                    <div class="footer-logo-icon">
                        <?php 
                        $logo_path = 'assets/images/logo.png';
                        if (file_exists(__DIR__ . '/../' . $logo_path)): 
                        ?>
                            <img src="<?php echo $logo_path; ?>" alt="Arup Enterprise" style="width:100%;height:100%;object-fit:contain;background:#ffffff;border-radius:8px;padding:4px;">
                        <?php else: ?>
                            <svg viewBox="0 0 32 32" xmlns="http://www.w3.org/2000/svg">
                                <path d="M16 2a2 2 0 0 1 2 2v2.17A10 10 0 0 1 26 16a10 10 0 0 1-10 10A10 10 0 0 1 6 16a10 10 0 0 1 8-9.83V4a2 2 0 0 1 2-2zm0 7a7 7 0 1 0 0 14A7 7 0 0 0 16 9zm0 3a4 4 0 1 1 0 8 4 4 0 0 1 0-8z"/>
                            </svg>
                        <?php endif; ?>
                    </div>
                    <div>
                        <div class="footer-logo-name">Arup <span>Enterprise</span></div>
                        <div class="footer-logo-tag">Magnetic Separator Manufacturer</div>
                    </div>
                </div>
                <p class="footer-about-text">
                    Precision-engineered industrial magnetic separators.
                    Manufacturer, Supplier &amp; Service Provider since 1986 — built for performance, trusted for reliability.
                </p>
                <div class="footer-socials">
                    <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                    <a href="https://www.youtube.com/@dipbantechnicalservices" target="_blank" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                    <a href="https://wa.me/918013635806" target="_blank" aria-label="WhatsApp"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                </div>
                <div class="footer-badges">
                    <div class="badge"><i class="fas fa-certificate"></i><span>ISO Certified</span></div>
                    <div class="badge"><i class="fas fa-shield-alt"></i><span>Trusted 38+ Years</span></div>
                </div>
            </div>

            <!-- COL 2: Quick Links -->
            <div class="footer-col">
                <h4 class="footer-heading">Quick Links</h4>
                <ul class="footer-links">
                    <li><a href="<?php echo $base_url; ?>/index.php"><i class="fas fa-chevron-right"></i> Home</a></li>
                    <li><a href="<?php echo $base_url; ?>/about.php"><i class="fas fa-chevron-right"></i> About Us</a></li>
                    <li><a href="<?php echo $base_url; ?>/products.php"><i class="fas fa-chevron-right"></i> All Products</a></li>
                    <li><a href="<?php echo $base_url; ?>/contact.php"><i class="fas fa-chevron-right"></i> Contact Us</a></li>
                    <li><a href="<?php echo $base_url; ?>/contact.php#quote"><i class="fas fa-chevron-right"></i> Get a Quote</a></li>
                </ul>
            </div>

            <!-- COL 3: Products -->
            <div class="footer-col">
                <h4 class="footer-heading">Our Products</h4>
                <ul class="footer-links">
                    <?php
                    // Fetch active categories from database
                    $footer_categories = [];
                    if (isset($conn) && $conn) {
                        try {
                            $result = $conn->query("SELECT * FROM categories WHERE status = 'active' ORDER BY sort_order ASC LIMIT 6");
                            if ($result) {
                                while ($row = $result->fetch_assoc()) {
                                    $footer_categories[] = $row;
                                }
                            }
                        } catch (Exception $e) {
                            $footer_categories = [];
                        }
                    }
                    
                    // Fallback categories if database is empty
                    if (empty($footer_categories)) {
                        $footer_categories = [
                            ['name' => 'Wood Working', 'icon' => 'fa-tree'],
                            ['name' => 'Sheet Metal', 'icon' => 'fa-layer-group'],
                            ['name' => 'CNC Machines', 'icon' => 'fa-microchip'],
                            ['name' => 'Hydraulic Systems', 'icon' => 'fa-cogs'],
                            ['name' => 'Tools & Accessories', 'icon' => 'fa-tools'],
                        ];
                    }
                    
                    foreach ($footer_categories as $cat): 
                    ?>
                    <li>
                        <a href="<?php echo $base_url; ?>/products.php?category=<?php echo urlencode($cat['name']); ?>">
                            <i class="fas <?php echo $cat['icon'] ?? 'fa-tag'; ?>"></i> 
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- COL 4: Contact -->
            <div class="footer-col">
                <h4 class="footer-heading">Contact Us</h4>
                <ul class="footer-contact-list">
                    <li>
                        <span class="contact-icon"><i class="fas fa-map-marker-alt"></i></span>
                        <span>316, Bhattanagar, Liluah, Howrah – 711204, West Bengal</span>
                    </li>
                    <li>
                        <span class="contact-icon"><i class="fas fa-map-pin"></i></span>
                        <span>11/B Kundan Lane, Liluah, Howrah – 711204</span>
                    </li>
                    <li>
                        <span class="contact-icon"><i class="fas fa-phone-alt"></i></span>
                        <span>
                            <a href="tel:+918013635806">+91 8013635806</a><br>
                            <a href="tel:8839019950">+91 8839019950</a>
                        </span>
                    </li>
                    <li>
                        <span class="contact-icon"><i class="fas fa-envelope"></i></span>
                        <span>
                            <a href="mailto:enterprisearup@gmail.com">enterprisearup@gmail.com</a>
                        </span>
                    </li>
                    <li>
                        <span class="contact-icon"><i class="fab fa-whatsapp"></i></span>
                        <span>
                            <a href="https://wa.me/918013635806" target="_blank">WhatsApp: +91 8013635806</a>
                        </span>
                    </li>
                </ul>

                <!-- MAP EMBED -->
                <div class="footer-map">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3684.3!2d88.3166!3d22.6172!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjLCsDM3JzAyLjAiTiA4OMKwMTknMDAuMCJF!5e0!3m2!1sen!2sin!4v1710000000000"
                        width="100%" height="130" style="border:0;border-radius:8px;" allowfullscreen="" loading="lazy">
                    </iframe>
                </div>
            </div>

        </div>
    </div>

   <!-- ============================================================
     PARTNERS / ASSOCIATES SECTION - DYNAMIC LOGOS
============================================================ -->
<div class="partners-section">
    <div class="partners-inner">
        <div class="partners-label">
            <span class="partners-line"></span>
            <span class="partners-title">Our <span>Associates</span></span>
            <span class="partners-line"></span>
        </div>
        <div class="partners-grid">
            <?php
            // Fetch active associates logos from database
            $partner_logos = [];
            if (isset($conn) && $conn) {
                try {
                    $result = $conn->query("SELECT * FROM associates WHERE status = 'active' ORDER BY sort_order ASC");
                    if ($result) {
                        while ($row = $result->fetch_assoc()) {
                            $partner_logos[] = $row;
                        }
                    }
                } catch (Exception $e) {
                    $partner_logos = [];
                }
            }
            
            // If no logos in database, show fallback
            if (empty($partner_logos)) {
                $partner_logos = [
                    ['logo' => 'assets/images/associates/1.png'],
                    ['logo' => 'assets/images/associates/2.png'],
                    ['logo' => 'assets/images/associates/3.png'],
                ];
            }
            
            foreach ($partner_logos as $pl): 
            ?>
            <div class="partner-item">
                <div class="partner-logo">
                    <?php if (!empty($pl['logo']) && file_exists(__DIR__ . '/../' . $pl['logo'])): ?>
                        <img src="<?php echo htmlspecialchars($pl['logo']); ?>" alt="Partner Logo" loading="lazy">
                    <?php else: ?>
                        <span style="color:#999;font-size:0.7rem;">No Logo</span>
                    <?php endif; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

    <!-- FOOTER BOTTOM -->
    <div class="footer-bottom">
        <div class="footer-bottom-inner">
            <p class="footer-copy">
                &copy; <?php echo date('Y'); ?> Arup Enterprise. All rights reserved.
            </p>
            <p class="footer-credit">
                Powered by <a href="https://ai-digitalsolution.com/">AI Digital Innovation</a> &nbsp;|&nbsp; Designed with <i class="fas fa-heart" style="color:var(--gold)"></i>
            </p>
        </div>
    </div>

</footer>

<!-- Back-to-Top Button -->
<button class="back-to-top" id="backToTop" aria-label="Back to top">
    <i class="fas fa-chevron-up"></i>
</button>

<!-- ===== FOOTER CSS ===== -->
<style>
/* FOOTER DIVIDER */
.footer-divider {
    display: flex;
    align-items: center;
    gap: 16px;
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
    padding-top: 48px;
}
.divider-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, transparent, var(--gold));
}
.divider-line:last-child {
    background: linear-gradient(to left, transparent, var(--gold));
}
.divider-icon {
    color: var(--gold);
    font-size: 1.4rem;
    opacity: 0.8;
}

/* PDF STRIP */
.pdf-strip {
    background: linear-gradient(135deg, var(--charcoal) 0%, #2d2d2d 100%);
    border-top: 3px solid var(--gold);
    border-bottom: 1px solid #333;
    margin-top: 24px;
    padding: 22px 0;
}
.pdf-strip-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 20px;
    flex-wrap: wrap;
}
.pdf-strip-label {
    display: flex;
    align-items: center;
    gap: 12px;
    color: var(--ivory);
    font-weight: 700;
    font-size: 0.95rem;
    letter-spacing: 0.04em;
}
.pdf-strip-label .fa-file-pdf { color: #e74c3c; font-size: 1.5rem; }
.pdf-strip-buttons { display: flex; gap: 14px; flex-wrap: wrap; }
.pdf-btn {
    display: inline-flex;
    align-items: center;
    gap: 12px;
    background: var(--gold);
    color: var(--white);
    padding: 11px 20px;
    border-radius: 8px;
    font-size: 0.82rem;
    transition: all var(--transition);
    box-shadow: var(--shadow-gold);
    text-decoration: none;
}
.pdf-btn .fa-download { font-size: 1.1rem; flex-shrink: 0; }
.pdf-btn-title { display: block; font-weight: 700; font-size: 0.85rem; }
.pdf-btn-sub   { display: block; font-weight: 400; font-size: 0.7rem; opacity: 0.85; }
.pdf-btn:hover { background: var(--gold-light); transform: translateY(-2px); box-shadow: 0 8px 24px rgba(201,146,10,0.4); color: white; }
.pdf-btn-alt   { background: #1a1a1a; border: 1px solid var(--gold); }
.pdf-btn-alt:hover { background: var(--gold-dark); border-color: var(--gold-dark); }

/* MAIN FOOTER */
.footer-main {
    background: linear-gradient(170deg, #1a1a1a 0%, #111111 100%);
    padding: 60px 0 40px;
}
.footer-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
    display: grid;
    grid-template-columns: 1.5fr 1fr 1fr 1.4fr;
    gap: 40px;
}

/* BRAND COL */
.footer-logo {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 16px;
}
.footer-logo-icon {
    width: 52px;
    height: 52px;
    background: #ffffff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    border: 1px solid rgba(201,146,10,0.2);
    overflow: hidden;
}
.footer-logo-icon svg { width: 26px; height: 26px; fill: var(--gold); }
.footer-logo-icon img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    padding: 4px;
    background: #ffffff;
    border-radius: 8px;
}
.footer-logo-name {
    font-family: var(--font-display);
    font-size: 1.3rem;
    font-weight: 900;
    color: var(--white);
}
.footer-logo-name span { color: var(--gold); }
.footer-logo-tag {
    font-size: 0.62rem;
    font-weight: 600;
    letter-spacing: 0.14em;
    text-transform: uppercase;
    color: #aaa;
}
.footer-about-text {
    color: #aaa;
    font-size: 0.84rem;
    line-height: 1.7;
    margin-bottom: 18px;
}

/* SOCIALS */
.footer-socials {
    display: flex;
    gap: 10px;
    margin-bottom: 18px;
}
.footer-socials a {
    width: 36px;
    height: 36px;
    background: rgba(255,255,255,0.06);
    border: 1px solid rgba(201,146,10,0.3);
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #aaa;
    font-size: 0.85rem;
    transition: all var(--transition);
    text-decoration: none;
}
.footer-socials a:hover {
    background: var(--gold);
    color: white;
    border-color: var(--gold);
    transform: translateY(-3px);
}

/* BADGES */
.footer-badges { display: flex; gap: 10px; flex-wrap: wrap; }
.badge {
    display: flex;
    align-items: center;
    gap: 6px;
    background: rgba(201,146,10,0.12);
    border: 1px solid rgba(201,146,10,0.3);
    border-radius: 6px;
    padding: 5px 10px;
    font-size: 0.72rem;
    font-weight: 600;
    color: var(--gold-light);
    letter-spacing: 0.03em;
}
.badge i { font-size: 0.75rem; }

/* HEADINGS */
.footer-heading {
    font-family: var(--font-display);
    font-size: 1rem;
    font-weight: 700;
    color: var(--white);
    margin-bottom: 20px;
    padding-bottom: 10px;
    border-bottom: 2px solid var(--gold);
    position: relative;
}

/* LINKS */
.footer-links { list-style: none; padding: 0; }
.footer-links li { margin-bottom: 10px; }
.footer-links a {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #aaa;
    font-size: 0.84rem;
    transition: all var(--transition);
    text-decoration: none;
}
.footer-links a i { color: var(--gold); font-size: 0.7rem; }
.footer-links a:hover { color: var(--gold-light); padding-left: 4px; }

/* CONTACT LIST */
.footer-contact-list { list-style: none; padding: 0; }
.footer-contact-list li {
    display: flex;
    align-items: flex-start;
    gap: 12px;
    color: #aaa;
    font-size: 0.83rem;
    margin-bottom: 14px;
    line-height: 1.6;
}
.footer-contact-list a { color: #bbb; transition: color var(--transition); text-decoration: none; }
.footer-contact-list a:hover { color: var(--gold-light); }
.contact-icon {
    width: 30px;
    height: 30px;
    background: rgba(201,146,10,0.15);
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    margin-top: 2px;
}
.contact-icon i { color: var(--gold); font-size: 0.8rem; }
.footer-map { margin-top: 14px; border: 1px solid rgba(201,146,10,0.2); border-radius: 8px; overflow: hidden; }

/* ============================================================
   PARTNERS / ASSOCIATES SECTION
============================================================ */
.partners-section {
    background: #0d0d0d;
    border-top: 2px solid rgba(201,146,10,0.15);
    border-bottom: 1px solid rgba(201,146,10,0.08);
    padding: 30px 0;
}
.partners-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
}
.partners-label {
    display: flex;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
}
.partners-line {
    flex: 1;
    height: 1px;
    background: linear-gradient(to right, transparent, rgba(201,146,10,0.3));
}
.partners-line:last-child {
    background: linear-gradient(to left, transparent, rgba(201,146,10,0.3));
}
.partners-title {
    font-family: var(--font-display);
    font-size: 1.1rem;
    font-weight: 700;
    color: #aaa;
    letter-spacing: 0.08em;
    text-transform: uppercase;
    white-space: nowrap;
}
.partners-title span { color: var(--gold); }

.partners-grid {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 60px;
    flex-wrap: wrap;
}
.partner-item {
    display: flex;
    align-items: center;
    justify-content: center;
}
.partner-logo {
    width: 140px;
    height: 70px;
    background: #ffffff;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    border: 1px solid rgba(201,146,10,0.15);
    transition: all var(--transition);
    overflow: hidden;
    padding: 8px;
}
.partner-logo:hover {
    border-color: var(--gold);
    box-shadow: 0 4px 20px rgba(201,146,10,0.15);
    transform: translateY(-3px);
}
.partner-logo img {
    width: 100%;
    height: 100%;
    object-fit: contain;
    display: block;
}

/* FOOTER BOTTOM */
.footer-bottom {
    background: #0a0a0a;
    border-top: 1px solid rgba(201,146,10,0.2);
    padding: 16px 0;
}
.footer-bottom-inner {
    max-width: 1280px;
    margin: 0 auto;
    padding: 0 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.footer-copy, .footer-credit {
    color: #666;
    font-size: 0.78rem;
}
.footer-credit a { color: var(--gold); text-decoration: none; }
.footer-credit a:hover { color: var(--gold-light); }

/* BACK TO TOP */
.back-to-top {
    position: fixed;
    bottom: 28px;
    right: 28px;
    width: 44px;
    height: 44px;
    background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dark) 100%);
    color: white;
    border: none;
    border-radius: 50%;
    cursor: pointer;
    font-size: 0.9rem;
    box-shadow: 0 4px 20px rgba(201,146,10,0.45);
    opacity: 0;
    visibility: hidden;
    transition: all var(--transition);
    z-index: 999;
}
.back-to-top.visible {
    opacity: 1;
    visibility: visible;
}
.back-to-top:hover { transform: translateY(-4px); box-shadow: 0 8px 28px rgba(201,146,10,0.55); }

/* RESPONSIVE */
@media (max-width: 1024px) {
    .footer-inner { grid-template-columns: 1fr 1fr; }
    .partners-grid { gap: 30px; }
    .partner-logo { width: 120px; height: 60px; }
}
@media (max-width: 640px) {
    .footer-inner { grid-template-columns: 1fr; }
    .pdf-strip-inner { flex-direction: column; align-items: flex-start; }
    .pdf-strip-buttons { width: 100%; }
    .pdf-btn { flex: 1; min-width: 0; }
    .footer-bottom-inner { flex-direction: column; text-align: center; }
    .partners-grid { gap: 20px; }
    .partner-logo { width: 100px; height: 50px; }
    .partners-label { gap: 10px; }
    .partners-title { font-size: 0.85rem; }
}
</style>

<!-- BACK TO TOP JS -->
<script>
(function(){
    const btn = document.getElementById('backToTop');
    window.addEventListener('scroll', () => {
        btn.classList.toggle('visible', window.scrollY > 400);
    });
    btn.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
})();
</script>

</body>
</html>
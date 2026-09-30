<?php
$page_title = "About Us | Arup Enterprise";
include 'includes/header.php';
?>

<!-- Google Fonts: Bebas Neue + Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- ═══════════════════════════════════════════════
     ABOUT HERO  —  Real Background + Blueprint Sparks
════════════════════════════════════════════════ -->
<section class="ab-hero" id="ab-hero">
  <!-- Hero background photo with dark industrial gradient overlay -->
  <div class="ab-hero-bg" style="background-image: url('assets/images/about/ab-scaled.jpg');"></div>
  <div class="ab-hero-overlay"></div>

  <!-- Particle canvas (golden sparks) -->
  <canvas class="ab-sparks" id="sparksCanvas" aria-hidden="true"></canvas>

  <!-- Blueprint grid overlay -->
  <div class="ab-grid-overlay" aria-hidden="true"></div>

  <!-- Hero content -->
  <div class="ab-hero-content container">
    <nav class="ab-breadcrumb" aria-label="breadcrumb">
      <a href="index.php">Home</a>
      <svg width="10" height="10" viewBox="0 0 10 10"><path d="M3 2l4 3-4 3" stroke="currentColor" stroke-width="1.4" fill="none"/></svg>
      <span>About Us</span>
    </nav>

    <div class="ab-hero-badge">
      <i class="fas fa-certificate" style="color:var(--gold)"></i>
      Est. 1986 · Tangra, Kolkata
    </div>

    <h1 class="ab-hero-title">
      <span class="ab-title-line1">Driven by Innovation</span>
      <span class="ab-title-line2">Trusted for Quality</span>
      <span class="ab-title-line3">Since 1986 · 38+ Years</span>
    </h1>

    <p class="ab-hero-sub">
      Established in 1986, Arup Enterprise has been a trusted name in the field of magnetic equipment and industrial solutions. Based in Tangra, Kolkata, we serve clients across the city and throughout India with high-quality products, dedicated engineering service, and unmatched dependability.
    </p>

    <div class="ab-hero-stats">
      <div class="ab-hs">
        <span class="ab-hs-n" data-target="38">0</span><sup>+</sup>
        <span class="ab-hs-l">Years of Service</span>
      </div>
      <div class="ab-hs-div"></div>
      <div class="ab-hs">
        <span class="ab-hs-n" data-target="500">0</span><sup>+</sup>
        <span class="ab-hs-l">Industrial Clients</span>
      </div>
      <div class="ab-hs-div"></div>
      <div class="ab-hs">
        <span class="ab-hs-n" data-target="100">0</span><sup>%</sup>
        <span class="ab-hs-l">Satisfaction</span>
      </div>
    </div>

    <div class="ab-hero-scroll" aria-hidden="true">
      <span></span>Scroll to explore our story
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════
     CORE CAPABILITIES / SOLUTION CARDS
════════════════════════════════════════════════ -->
<section class="ab-section ab-capabilities">
  <div class="container">
    <div class="ab-section-head">
      <span class="ab-eyebrow">What We Deliver</span>
      <h2 class="ab-h2">High-Performance <em>Magnetic &amp; Industrial</em> Solutions</h2>
      <p class="ab-head-desc">Precision-engineered machinery built to withstand demanding industrial environments.</p>
    </div>

    <div class="ab-cap-layout">
      <!-- Showcase Image Column -->
      <div class="ab-cap-media">
        <div class="ab-media-card">
          <img src="assets/images/about/WhatsApp-Image-2025-05-13-at-11.50.07-AM.jpeg" alt="Arup Enterprise Manufacturing Equipment" class="ab-media-img" loading="lazy">
          <div class="ab-media-badge">
            <i class="fas fa-check-circle"></i>
            <span>Heavy-Duty Industrial Manufacturing</span>
          </div>
        </div>
      </div>

      <!-- 4 Solution Cards -->
      <div class="ab-cap-grid">
        <div class="ab-cap-card">
          <div class="ab-cap-icon">
            <i class="fas fa-magnet"></i>
          </div>
          <div class="ab-cap-content">
            <h3>Magnetic Separation Systems</h3>
            <p>Efficiently remove ferrous contaminants from bulk materials using advanced magnetic separation technology.</p>
          </div>
        </div>

        <div class="ab-cap-card">
          <div class="ab-cap-icon">
            <i class="fas fa-cog"></i>
          </div>
          <div class="ab-cap-content">
            <h3>Pulley &amp; Industrial Components</h3>
            <p>High-performance pulleys and components designed to ensure smooth industrial operations with long-lasting durability.</p>
          </div>
        </div>

        <div class="ab-cap-card">
          <div class="ab-cap-icon">
            <i class="fas fa-industry"></i>
          </div>
          <div class="ab-cap-content">
            <h3>Custom Steel Solutions</h3>
            <p>Tailor-made stainless steel products built to match your specific industrial requirements with precision and strength.</p>
          </div>
        </div>

        <div class="ab-cap-card">
          <div class="ab-cap-icon">
            <i class="fas fa-th-large"></i>
          </div>
          <div class="ab-cap-content">
            <h3>Magnetic Tools &amp; Grids</h3>
            <p>Reliable magnetic tools and grids for safe, easy handling and separation of metallic particles in various applications.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════
     OUR STORY & WORKSHOP SHOWCASE
════════════════════════════════════════════════ -->
<section class="ab-section ab-who">
  <div class="container">
    <div class="ab-who-grid">
      <!-- Text side -->
      <div class="ab-who-text">
        <span class="ab-eyebrow">Our Story</span>
        <h2 class="ab-h2">Powering Industries with <em>Precision &amp; Integrity</em></h2>
        
        <p class="ab-p-highlight">
          Established in 1986, Arup Enterprise has earned a strong reputation as a trusted name in the field of magnetic separation and industrial machinery manufacturing. Based in Tangra, Kolkata, we are known as one of the leading Magnetic Drum Manufacturers in the region, serving industries across India with reliable and effective solutions.
        </p>
        <p>
          Over the years, our growth has been driven by a firm commitment to quality, integrity, and customer satisfaction. We believe that true value comes not just from delivering high-quality products, but also from building lasting relationships with our clients. This approach has helped us build a loyal customer base in Kolkata and across the country.
        </p>
        <p>
          Our team consists of skilled professionals who are passionate about innovation, precision engineering, and delivering results. From product design to customer service, every process is handled with care, dedication, and professionalism.
        </p>

        <div class="ab-who-tags">
          <span><i class="fas fa-map-marker-alt"></i>Tangra, Kolkata</span>
          <span><i class="fas fa-calendar-check"></i>Est. 1986</span>
          <span><i class="fas fa-globe"></i>Pan-India Service</span>
          <span><i class="fas fa-shield-alt"></i>Tested Quality</span>
        </div>

        <div class="ab-who-actions">
          <a href="contact.php" class="ab-cta-inline">
            Get a Free Consultation
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
          </a>
        </div>
      </div>

      <!-- Workshop Gallery Showcase -->
      <div class="ab-who-gallery">
        <div class="ab-gallery-main">
          <img src="assets/images/about/WhatsApp-Image-2025-05-13-at-11.50.20-AM-1024x1024.jpeg" alt="Workshop Assembly" loading="lazy">
          <div class="ab-gallery-badge">
            <i class="fas fa-tools"></i> Kolkata Fabrication Facility
          </div>
        </div>
        <div class="ab-gallery-thumbs">
          <div class="ab-thumb">
            <img src="assets/images/about/WhatsApp-Image-2025-05-13-at-11.50.26-AM-1024x1024.jpeg" alt="Magnetic Drum Precision Fabrication" loading="lazy">
            <span class="ab-thumb-lbl">Precision Drum Assembly</span>
          </div>
          <div class="ab-thumb">
            <img src="assets/images/about/WhatsApp-Image-2025-05-13-at-11.50.29-AM-1024x1024.jpeg" alt="Industrial Drum Fabrication" loading="lazy">
            <span class="ab-thumb-lbl">Heavy Machine Component</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════
     INDUSTRIES WE SERVE
════════════════════════════════════════════════ -->
<section class="ab-section ab-industries">
  <div class="container">
    <div class="ab-section-head">
      <span class="ab-eyebrow">Sectors We Empower</span>
      <h2 class="ab-h2">Trusted Across <em>Heavy &amp; Processing</em> Industries</h2>
      <p class="ab-head-desc">We are proud to serve a wide range of industries with specialized iron recovery and tramp metal separation systems:</p>
    </div>

    <div class="ab-ind-grid">
      <div class="ab-ind-card">
        <div class="ab-ind-num">01</div>
        <div class="ab-ind-icon"><i class="fas fa-cubes"></i></div>
        <h3>Iron &amp; Steel Plants</h3>
        <p>Continuous tramp iron extraction from burden conveyors, scrap processing, and furnace charge protection.</p>
      </div>

      <div class="ab-ind-card">
        <div class="ab-ind-num">02</div>
        <div class="ab-ind-icon"><i class="fas fa-fire"></i></div>
        <h3>Casting Plants</h3>
        <p>Reclaiming sprue, gate metal, and cleansing moulding sand for recycled reuse in foundry processes.</p>
      </div>

      <div class="ab-ind-card">
        <div class="ab-ind-num">03</div>
        <div class="ab-ind-icon"><i class="fas fa-mountain"></i></div>
        <h3>Cement Plants</h3>
        <p>Protecting crushers, roller presses, and ball mills from destructive ferrous debris in raw material streams.</p>
      </div>

      <div class="ab-ind-card">
        <div class="ab-ind-num">04</div>
        <div class="ab-ind-icon"><i class="fas fa-layer-group"></i></div>
        <h3>Sponge Iron &amp; Pig Iron</h3>
        <p>High-gradient separation of metallized DRI fines, char separation, and iron yield optimization.</p>
      </div>

      <div class="ab-ind-card">
        <div class="ab-ind-num">05</div>
        <div class="ab-ind-icon"><i class="fas fa-dumpster"></i></div>
        <h3>Iron Slag Separation</h3>
        <p>Recovering high-purity metallic content from blast furnace and induction furnace slag waste.</p>
      </div>

      <div class="ab-ind-card">
        <div class="ab-ind-num">06</div>
        <div class="ab-ind-icon"><i class="fas fa-wind"></i></div>
        <h3>Sand Recovery from Castings</h3>
        <p>Complete de-ironing of thermal or mechanically reclaimed foundry sands for zero-defect casting production.</p>
      </div>

      <div class="ab-ind-card">
        <div class="ab-ind-num">07</div>
        <div class="ab-ind-icon"><i class="fas fa-recycle"></i></div>
        <h3>Recycling Units</h3>
        <p>Sorting shredded metal, plastic flakes, rubber crumbs, and municipal solid waste streams.</p>
      </div>

      <div class="ab-ind-card">
        <div class="ab-ind-num">08</div>
        <div class="ab-ind-icon"><i class="fas fa-microchip"></i></div>
        <h3>E-Waste Metal Recovery</h3>
        <p>Precision extraction of ferrous pins, brackets, and electronic shred residue in compliance with clean recovery standards.</p>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════
     OUR PROCESS: From Concept to Completion
════════════════════════════════════════════════ -->
<section class="ab-section ab-process" style="background-image: linear-gradient(rgba(14,14,14,0.92), rgba(14,14,14,0.92)), url('assets/images/about/hero-section.avif');">
  <div class="container">
    <div class="ab-section-head">
      <span class="ab-eyebrow">Our Workflow</span>
      <h2 class="ab-h2" style="color:#FFF;">From Concept to <em>Completion</em></h2>
      <p class="ab-head-desc" style="color:rgba(255,255,255,0.7);">
        At Arup Enterprise, we follow a structured and efficient workflow that ensures quality, customization, and timely delivery. From understanding your unique requirements to delivering reliable magnetic solutions, every stage is handled with precision and dedication.
      </p>
    </div>

    <div class="ab-proc-grid">
      <div class="ab-proc-card">
        <div class="ab-proc-step">STEP 01</div>
        <div class="ab-proc-icon"><i class="fas fa-clipboard-list"></i></div>
        <h3>Client Requirement Analysis</h3>
        <p>We analyze your specific raw material characteristics, flow rate, particle size distribution, and plant operating environment.</p>
      </div>

      <div class="ab-proc-card">
        <div class="ab-proc-step">STEP 02</div>
        <div class="ab-proc-icon"><i class="fas fa-pencil-ruler"></i></div>
        <h3>Design &amp; Engineering</h3>
        <p>Our engineering team models the optimal magnetic circuit, drum dimensions, and structural framework tailored to your layout.</p>
      </div>

      <div class="ab-proc-card">
        <div class="ab-proc-step">STEP 03</div>
        <div class="ab-proc-icon"><i class="fas fa-cogs"></i></div>
        <h3>Production &amp; Testing</h3>
        <p>In-house precision fabrication in Kolkata using high-grade magnetic cores and stainless steel, followed by rigorous Gauss testing.</p>
      </div>

      <div class="ab-proc-card">
        <div class="ab-proc-step">STEP 04</div>
        <div class="ab-proc-icon"><i class="fas fa-truck-loading"></i></div>
        <h3>Delivery &amp; Installation</h3>
        <p>Safe on-site dispatch across India accompanied by installation guidance, operational testing, and ongoing spare parts support.</p>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════════
     MISSION  VISION  VALUES
════════════════════════════════════════════════ -->
<section class="ab-section ab-mvv">
  <div class="container">
    <div class="ab-section-head">
      <span class="ab-eyebrow">Our Foundation</span>
      <h2 class="ab-h2">Mission, Vision &amp; <em>Values</em></h2>
    </div>

    <div class="ab-mvv-grid">
      <div class="ab-mvv-card ab-mvv-mission">
        <div class="ab-mvv-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="5" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="1.5" fill="currentColor"/></svg>
        </div>
        <h3>Our Mission</h3>
        <p>To empower industrial plants with durable, high-efficiency magnetic separation equipment backed by prompt engineering assistance, genuine spare components, and continuous operational reliability.</p>
      </div>

      <div class="ab-mvv-card ab-mvv-vision">
        <div class="ab-mvv-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
        </div>
        <h3>Our Vision</h3>
        <p>To remain the preferred manufacturer and technological authority in magnetic separation across India — recognized for precision engineering, product durability, and long-standing client relationships.</p>
      </div>

      <div class="ab-mvv-card ab-mvv-values">
        <div class="ab-mvv-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
        </div>
        <h3>Our Values</h3>
        <ul class="ab-values-list">
          <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#C9920A" stroke-width="2.5" stroke-linecap="round"/></svg> Precision in every magnetic core</li>
          <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#C9920A" stroke-width="2.5" stroke-linecap="round"/></svg> Integrity and transparency in client deals</li>
          <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#C9920A" stroke-width="2.5" stroke-linecap="round"/></svg> Custom engineering built for real site demands</li>
          <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#C9920A" stroke-width="2.5" stroke-linecap="round"/></svg> Rugged materials with zero compromise</li>
          <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#C9920A" stroke-width="2.5" stroke-linecap="round"/></svg> Dedicated after-sales service and support</li>
        </ul>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════
     TIMELINE: 1986 to Present
════════════════════════════════════════════ -->
<section class="ab-section ab-timeline-section">
  <div class="container">
    <div class="ab-section-head">
      <span class="ab-eyebrow">Our Journey</span>
      <h2 class="ab-h2">Milestones That <em>Define Us</em></h2>
    </div>

    <div class="ab-timeline">
      <div class="ab-tl-spine"></div>

      <div class="ab-tl-item">
        <div class="ab-tl-yr">1986</div>
        <div class="ab-tl-node"></div>
        <div class="ab-tl-card">
          <h4>Established in Kolkata</h4>
          <p>Arup Enterprise was founded in Tangra, Kolkata, starting with specialized manufacturing of magnetic separation machinery and magnetic drums for regional industries.</p>
        </div>
      </div>

      <div class="ab-tl-item ab-tl-right">
        <div class="ab-tl-yr">1995</div>
        <div class="ab-tl-node"></div>
        <div class="ab-tl-card">
          <h4>Expanding Industrial Capabilities</h4>
          <p>Expanded product lines to include heavy-duty pulleys, permanent magnetic head pulleys, and custom stainless steel separation equipment.</p>
        </div>
      </div>

      <div class="ab-tl-item">
        <div class="ab-tl-yr">2010</div>
        <div class="ab-tl-node"></div>
        <div class="ab-tl-card">
          <h4>Pan-India Supply Network</h4>
          <p>Extended our trusted delivery network to steel, cement, foundry, and recycling plants nationwide, becoming a recognized manufacturer across India.</p>
        </div>
      </div>

      <div class="ab-tl-item ab-tl-right">
        <div class="ab-tl-yr">2025+</div>
        <div class="ab-tl-node ab-tl-node-latest"></div>
        <div class="ab-tl-card ab-tl-card-latest">
          <span class="ab-tl-live">38+ YEARS</span>
          <h4>Continuous Engineering Innovation</h4>
          <p>Combining four decades of technical mastery with contemporary fabrication standards to provide high-Gauss, long-lasting industrial separation systems.</p>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════
     FREQUENTLY ASKED QUESTIONS
════════════════════════════════════════════ -->
<section class="ab-section ab-faqs">
  <div class="container">
    <div class="ab-section-head">
      <span class="ab-eyebrow">Need Answers?</span>
      <h2 class="ab-h2">Frequently Asked <em>Questions</em></h2>
      <p class="ab-head-desc">Common questions about our magnetic separation systems, custom options, and technical support.</p>
    </div>

    <div class="ab-faq-layout">
      <!-- Left side: visual box -->
      <div class="ab-faq-visual">
        <div class="ab-faq-img-card">
          <img src="assets/images/about/airport-security.avif" alt="Industrial Technical Inspection" loading="lazy">
          <div class="ab-faq-highlight">
            <div class="ab-faq-highlight-badge">38+ Years of Service</div>
            <h3>Magnetic Separation Solutions Since 1986</h3>
            <p>Delivering reliable magnetic separation solutions with unmatched industry experience and direct engineering support.</p>
          </div>
        </div>
      </div>

      <!-- Right side: Accordion -->
      <div class="ab-faq-list">
        <div class="ab-faq-item active">
          <button type="button" class="ab-faq-trigger" aria-expanded="true">
            <span>What is a magnetic separator and how does it work?</span>
            <i class="fas fa-chevron-down ab-faq-icon"></i>
          </button>
          <div class="ab-faq-body" style="display:block;">
            <p>A magnetic separator is a machine used to separate magnetic materials from non-magnetic ones, commonly used in metal recycling, waste processing, casting plants, and industrial material handling applications.</p>
          </div>
        </div>

        <div class="ab-faq-item">
          <button type="button" class="ab-faq-trigger" aria-expanded="false">
            <span>What types of magnetic separation equipment do you provide?</span>
            <i class="fas fa-chevron-down ab-faq-icon"></i>
          </button>
          <div class="ab-faq-body">
            <p>We offer Drum Magnetic Separators (Single &amp; Double Drum), Overband Magnetic Separators, Electromagnetic Separators, Permanent Magnetic Head Pulleys, and custom magnetic tools &amp; grids for various industrial needs.</p>
          </div>
        </div>

        <div class="ab-faq-item">
          <button type="button" class="ab-faq-trigger" aria-expanded="false">
            <span>Can your magnetic separators be customized?</span>
            <i class="fas fa-chevron-down ab-faq-icon"></i>
          </button>
          <div class="ab-faq-body">
            <p>Yes, we provide fully customizable magnetic separation systems based on your material type, bulk density, particle size, conveyor belt width, operating capacity, and industry requirements.</p>
          </div>
        </div>

        <div class="ab-faq-item">
          <button type="button" class="ab-faq-trigger" aria-expanded="false">
            <span>Do you provide installation and maintenance support?</span>
            <i class="fas fa-chevron-down ab-faq-icon"></i>
          </button>
          <div class="ab-faq-body">
            <p>Yes, we provide complete support including installation guidance, testing, genuine spare parts replacement, and periodic maintenance to ensure continuous long-term performance.</p>
          </div>
        </div>

        <div class="ab-faq-item">
          <button type="button" class="ab-faq-trigger" aria-expanded="false">
            <span>Why choose ARUP ENTERPRISE?</span>
            <i class="fas fa-chevron-down ab-faq-icon"></i>
          </button>
          <div class="ab-faq-body">
            <p>ARUP ENTERPRISE is a trusted manufacturer with over 38 years of proven experience since 1986. We engineer heavy-duty, high-Gauss solutions for iron contamination removal across minerals, steel, cement, foundry, chemicals, plastics, and recycling units.</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>


<!-- ═══════════════════════════════════════════
     CTA STRIP
════════════════════════════════════════════ -->
<section class="ab-cta-strip">
  <div class="container">
    <div class="ab-cta-inner">
      <div class="ab-cta-text">
        <span class="ab-eyebrow" style="color:#E8B84B; background:rgba(232,184,75,0.1); border-color:rgba(232,184,75,0.25);">Let's Work Together</span>
        <h2>Ready to Upgrade Your<br>Magnetic Separation Setup?</h2>
        <p>Talk to our Kolkata engineering team today. We'll identify the right magnetic separator or custom pulley for your specifications and budget.</p>
      </div>
      <div class="ab-cta-btns">
        <a href="contact.php#quote" class="ab-btn-primary">
          <i class="fas fa-paper-plane"></i> Request a Free Quote
        </a>
        <a href="tel:+918013635806" class="ab-btn-secondary">
          <i class="fas fa-phone-alt"></i> +91 8013635806
        </a>
      </div>
    </div>
  </div>
</section>

<?php include 'includes/footer.php'; ?>

<!-- ═══════════════════════════════════════════
     STYLES
════════════════════════════════════════════ -->
<style>
/* ── COLOR TOKENS ── */
:root {
  --gold:       #C9920A;
  --gold-lt:    #E8B84B;
  --gold-dk:    #8B6508;
  --char:       #0E0E0E;
  --ink:        #181818;
  --cream:      #F7F4EE;
  --ivory:      #FAFAF6;
  --steel:      #ECE8DF;
  --smoke:      #6B6560;
  --white:      #FFFFFF;
  --font-d:     'Bebas Neue', 'Impact', sans-serif;
  --font-b:     'Inter', -apple-system, sans-serif;
  --ease:       cubic-bezier(0.4, 0, 0.2, 1);
  --shadow-g:   0 8px 32px rgba(201,146,10,0.18);
  --radius:     12px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: var(--font-b); color: var(--ink); background: var(--ivory); }
.container { max-width: 1240px; margin: 0 auto; padding: 0 24px; }
a { text-decoration: none; color: inherit; }

/* ── TYPOGRAPHY & HEADINGS ── */
.ab-eyebrow {
  display: inline-block;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.2em;
  text-transform: uppercase;
  color: var(--gold);
  background: rgba(201,146,10,0.09);
  border: 1px solid rgba(201,146,10,0.25);
  border-radius: 100px;
  padding: 5px 16px;
  margin-bottom: 14px;
}
.ab-section-head { text-align: center; margin-bottom: 50px; }
.ab-h2 {
  font-family: var(--font-d);
  font-size: clamp(2.2rem, 4.5vw, 3.4rem);
  letter-spacing: 0.03em;
  color: var(--char);
  line-height: 1.1;
  text-transform: uppercase;
}
.ab-h2 em { color: var(--gold); font-style: normal; }
.ab-head-desc {
  max-width: 680px;
  margin: 12px auto 0;
  color: var(--smoke);
  font-size: 1rem;
  line-height: 1.6;
}
.ab-section { padding: 88px 0; }

/* ── HERO SECTION ── */
.ab-hero {
  position: relative;
  min-height: 88vh;
  background: var(--char);
  display: flex;
  align-items: center;
  overflow: hidden;
  border-bottom: 3px solid var(--gold);
}
.ab-hero-bg {
  position: absolute;
  inset: 0;
  background-size: cover;
  background-position: center;
  filter: brightness(0.35) contrast(1.1);
  transform: scale(1.02);
  transition: transform 8s ease;
}
.ab-hero-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(135deg, rgba(14,14,14,0.94) 0%, rgba(14,14,14,0.8) 55%, rgba(201,146,10,0.15) 100%);
}
.ab-sparks {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  opacity: 0.65;
  z-index: 1;
}
.ab-grid-overlay {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(201,146,10,0.05) 1px, transparent 1px),
    linear-gradient(90deg, rgba(201,146,10,0.05) 1px, transparent 1px);
  background-size: 50px 50px;
  pointer-events: none;
  z-index: 1;
}
.ab-hero-content {
  position: relative;
  z-index: 2;
  padding: 120px 24px 80px;
  max-width: 820px;
}
.ab-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.8rem;
  color: #888;
  margin-bottom: 20px;
}
.ab-breadcrumb a { color: var(--gold); font-weight: 500; }
.ab-breadcrumb a:hover { color: var(--gold-lt); }
.ab-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.16em;
  text-transform: uppercase;
  color: var(--gold-lt);
  border: 1px solid rgba(201,146,10,0.35);
  background: rgba(201,146,10,0.12);
  border-radius: 100px;
  padding: 6px 18px;
  margin-bottom: 22px;
  backdrop-filter: blur(8px);
}
.ab-hero-title {
  display: flex;
  flex-direction: column;
  margin-bottom: 20px;
}
.ab-title-line1 {
  font-family: var(--font-d);
  font-size: clamp(2.4rem, 5.5vw, 4.5rem);
  letter-spacing: 0.04em;
  color: rgba(255,255,255,0.7);
  line-height: 1;
}
.ab-title-line2 {
  font-family: var(--font-d);
  font-size: clamp(3.8rem, 9.5vw, 7.5rem);
  letter-spacing: 0.02em;
  color: var(--gold);
  line-height: 0.95;
  text-shadow: 0 0 60px rgba(201,146,10,0.4);
}
.ab-title-line3 {
  font-family: var(--font-d);
  font-size: clamp(1.8rem, 3.8vw, 3.2rem);
  letter-spacing: 0.08em;
  color: rgba(255,255,255,0.4);
  line-height: 1.1;
}
.ab-hero-sub {
  color: rgba(255,255,255,0.72);
  font-size: 1.05rem;
  line-height: 1.7;
  max-width: 660px;
  margin-bottom: 34px;
}
.ab-hero-stats {
  display: inline-flex;
  align-items: center;
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(201,146,10,0.22);
  border-radius: 14px;
  padding: 16px 28px;
  backdrop-filter: blur(10px);
  margin-bottom: 34px;
}
.ab-hs {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0 24px;
}
.ab-hs-n {
  font-family: var(--font-d);
  font-size: 2.8rem;
  color: var(--gold);
  line-height: 1;
}
.ab-hs sup {
  font-family: var(--font-b);
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--gold-lt);
  vertical-align: super;
}
.ab-hs-l {
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.12em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.5);
  margin-top: 4px;
}
.ab-hs-div {
  width: 1px;
  height: 40px;
  background: rgba(201,146,10,0.25);
}
.ab-hero-scroll {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.74rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.4);
}
.ab-hero-scroll span {
  display: block;
  width: 1px;
  height: 32px;
  background: linear-gradient(to bottom, var(--gold), transparent);
  animation: scrollPulse 2s ease-in-out infinite;
}
@keyframes scrollPulse {
  0%,100% { opacity: 0.3; transform: scaleY(1); }
  50%      { opacity: 0.9; transform: scaleY(1.3); }
}

/* ── CORE CAPABILITIES ── */
.ab-capabilities { background: #FFFFFF; }
.ab-cap-layout {
  display: grid;
  grid-template-columns: 1fr 1.35fr;
  gap: 40px;
  align-items: center;
}
.ab-media-card {
  position: relative;
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: 0 16px 40px rgba(0,0,0,0.12);
  border: 1px solid var(--steel);
}
.ab-media-img {
  width: 100%;
  height: 440px;
  object-fit: cover;
  display: block;
  transition: transform 0.5s ease;
}
.ab-media-card:hover .ab-media-img {
  transform: scale(1.03);
}
.ab-media-badge {
  position: absolute;
  bottom: 16px;
  left: 16px;
  right: 16px;
  background: rgba(14,14,14,0.85);
  border: 1px solid rgba(201,146,10,0.35);
  padding: 12px 18px;
  border-radius: 8px;
  color: #FFF;
  font-size: 0.82rem;
  font-weight: 600;
  display: flex;
  align-items: center;
  gap: 10px;
  backdrop-filter: blur(6px);
}
.ab-media-badge i { color: var(--gold); font-size: 1.1rem; }
.ab-cap-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}
.ab-cap-card {
  background: var(--ivory);
  border: 1px solid var(--steel);
  border-radius: var(--radius);
  padding: 24px;
  transition: all 0.3s var(--ease);
  position: relative;
  overflow: hidden;
}
.ab-cap-card::before {
  content: '';
  position: absolute;
  top: 0; left: 0; width: 4px; height: 0;
  background: var(--gold);
  transition: height 0.3s var(--ease);
}
.ab-cap-card:hover {
  background: #FFF;
  border-color: rgba(201,146,10,0.4);
  transform: translateY(-4px);
  box-shadow: 0 12px 28px rgba(0,0,0,0.07);
}
.ab-cap-card:hover::before { height: 100%; }
.ab-cap-icon {
  width: 48px;
  height: 48px;
  border-radius: 10px;
  background: rgba(201,146,10,0.12);
  color: var(--gold);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.3rem;
  margin-bottom: 16px;
  transition: background 0.3s, color 0.3s;
}
.ab-cap-card:hover .ab-cap-icon {
  background: var(--gold);
  color: #FFF;
}
.ab-cap-card h3 {
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--char);
  margin-bottom: 8px;
}
.ab-cap-card p {
  font-size: 0.88rem;
  color: var(--smoke);
  line-height: 1.55;
}

/* ── OUR STORY & GALLERY ── */
.ab-who { background: var(--cream); }
.ab-who-grid {
  display: grid;
  grid-template-columns: 1.1fr 1fr;
  gap: 56px;
  align-items: center;
}
.ab-who-text p {
  color: #3a3530;
  font-size: 0.96rem;
  line-height: 1.75;
  margin-bottom: 16px;
}
.ab-p-highlight {
  font-size: 1.05rem !important;
  font-weight: 600;
  color: var(--char) !important;
  border-left: 3px solid var(--gold);
  padding-left: 14px;
}
.ab-who-tags {
  display: flex;
  flex-wrap: wrap;
  gap: 10px;
  margin: 24px 0 30px;
}
.ab-who-tags span {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  background: #FFF;
  border: 1px solid var(--steel);
  padding: 8px 14px;
  border-radius: 8px;
  font-size: 0.8rem;
  font-weight: 600;
  color: var(--char);
}
.ab-who-tags i { color: var(--gold); }
.ab-cta-inline {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: var(--gold);
  color: #FFF;
  font-weight: 700;
  font-size: 0.88rem;
  padding: 13px 28px;
  border-radius: 8px;
  box-shadow: 0 4px 16px rgba(201,146,10,0.3);
  transition: all 0.25s var(--ease);
}
.ab-cta-inline:hover {
  background: var(--gold-dk);
  transform: translateY(-2px);
  color: #FFF;
}
.ab-who-gallery {
  display: flex;
  flex-direction: column;
  gap: 16px;
}
.ab-gallery-main {
  position: relative;
  border-radius: var(--radius);
  overflow: hidden;
  border: 1px solid var(--steel);
  box-shadow: 0 16px 40px rgba(0,0,0,0.12);
}
.ab-gallery-main img {
  width: 100%;
  height: 320px;
  object-fit: cover;
  display: block;
}
.ab-gallery-badge {
  position: absolute;
  bottom: 12px;
  left: 12px;
  background: rgba(14,14,14,0.85);
  border: 1px solid rgba(201,146,10,0.3);
  color: #FFF;
  font-size: 0.78rem;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 6px;
  backdrop-filter: blur(4px);
}
.ab-gallery-badge i { color: var(--gold); margin-right: 4px; }
.ab-gallery-thumbs {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 16px;
}
.ab-thumb {
  position: relative;
  border-radius: var(--radius);
  overflow: hidden;
  border: 1px solid var(--steel);
  box-shadow: 0 8px 20px rgba(0,0,0,0.06);
}
.ab-thumb img {
  width: 100%;
  height: 160px;
  object-fit: cover;
  display: block;
  transition: transform 0.4s ease;
}
.ab-thumb:hover img { transform: scale(1.05); }
.ab-thumb-lbl {
  position: absolute;
  bottom: 8px;
  left: 8px;
  right: 8px;
  background: rgba(14,14,14,0.75);
  color: #FFF;
  font-size: 0.7rem;
  font-weight: 600;
  padding: 4px 8px;
  border-radius: 4px;
  text-align: center;
  backdrop-filter: blur(4px);
}

/* ── INDUSTRIES WE SERVE ── */
.ab-industries { background: #FFFFFF; }
.ab-ind-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 22px;
}
.ab-ind-card {
  background: var(--ivory);
  border: 1px solid var(--steel);
  border-radius: var(--radius);
  padding: 28px 22px;
  position: relative;
  transition: all 0.3s var(--ease);
}
.ab-ind-card:hover {
  background: #FFF;
  border-color: var(--gold);
  transform: translateY(-5px);
  box-shadow: 0 14px 30px rgba(0,0,0,0.08);
}
.ab-ind-num {
  font-family: var(--font-d);
  font-size: 1.8rem;
  color: rgba(201,146,10,0.25);
  position: absolute;
  top: 18px;
  right: 20px;
  line-height: 1;
}
.ab-ind-icon {
  width: 44px;
  height: 44px;
  background: rgba(201,146,10,0.1);
  color: var(--gold);
  border-radius: 10px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.25rem;
  margin-bottom: 16px;
  transition: all 0.3s ease;
}
.ab-ind-card:hover .ab-ind-icon {
  background: var(--gold);
  color: #FFF;
}
.ab-ind-card h3 {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--char);
  margin-bottom: 8px;
  line-height: 1.3;
}
.ab-ind-card p {
  font-size: 0.82rem;
  color: var(--smoke);
  line-height: 1.55;
}

/* ── OUR PROCESS ── */
.ab-process {
  background-size: cover;
  background-position: center;
  background-attachment: fixed;
  color: #FFF;
}
.ab-proc-grid {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 22px;
}
.ab-proc-card {
  background: rgba(255,255,255,0.05);
  border: 1px solid rgba(201,146,10,0.25);
  border-radius: var(--radius);
  padding: 30px 22px;
  backdrop-filter: blur(8px);
  position: relative;
  transition: all 0.3s ease;
}
.ab-proc-card:hover {
  background: rgba(255,255,255,0.09);
  border-color: var(--gold);
  transform: translateY(-4px);
  box-shadow: 0 12px 30px rgba(0,0,0,0.3);
}
.ab-proc-step {
  font-family: var(--font-d);
  font-size: 1.15rem;
  letter-spacing: 0.1em;
  color: var(--gold);
  margin-bottom: 14px;
}
.ab-proc-icon {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  background: rgba(201,146,10,0.15);
  border: 1px solid rgba(201,146,10,0.3);
  color: var(--gold-lt);
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.15rem;
  margin-bottom: 16px;
}
.ab-proc-card h3 {
  font-size: 1.05rem;
  font-weight: 700;
  color: #FFF;
  margin-bottom: 10px;
}
.ab-proc-card p {
  font-size: 0.84rem;
  color: rgba(255,255,255,0.68);
  line-height: 1.6;
}

/* ── MISSION, VISION, VALUES ── */
.ab-mvv { background: var(--ivory); }
.ab-mvv-grid {
  display: grid;
  grid-template-columns: 1fr 1fr 1fr;
  gap: 28px;
}
.ab-mvv-card {
  background: #FFF;
  border: 1px solid var(--steel);
  border-radius: var(--radius);
  padding: 36px 28px;
  box-shadow: 0 6px 20px rgba(0,0,0,0.04);
  transition: all 0.3s var(--ease);
}
.ab-mvv-card:hover {
  transform: translateY(-4px);
  border-color: var(--gold);
  box-shadow: 0 16px 36px rgba(0,0,0,0.08);
}
.ab-mvv-icon {
  width: 52px;
  height: 52px;
  border-radius: 12px;
  background: rgba(201,146,10,0.1);
  color: var(--gold);
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: 20px;
}
.ab-mvv-card h3 {
  font-family: var(--font-d);
  font-size: 1.65rem;
  letter-spacing: 0.04em;
  color: var(--char);
  margin-bottom: 12px;
}
.ab-mvv-card p {
  font-size: 0.9rem;
  color: var(--smoke);
  line-height: 1.7;
}
.ab-values-list { list-style: none; display: flex; flex-direction: column; gap: 10px; }
.ab-values-list li {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.85rem;
  font-weight: 500;
  color: var(--char);
}

/* ── TIMELINE ── */
.ab-timeline-section { background: var(--cream); }
.ab-timeline {
  position: relative;
  max-width: 860px;
  margin: 0 auto;
  padding: 40px 0;
}
.ab-tl-spine {
  position: absolute;
  top: 0; bottom: 0;
  left: 50%;
  width: 2px;
  background: rgba(201,146,10,0.3);
  transform: translateX(-50%);
}
.ab-tl-item {
  position: relative;
  margin-bottom: 50px;
  display: flex;
  justify-content: flex-end;
  padding-right: calc(50% + 40px);
}
.ab-tl-item.ab-tl-right {
  justify-content: flex-start;
  padding-right: 0;
  padding-left: calc(50% + 40px);
}
.ab-tl-yr {
  position: absolute;
  left: 50%;
  top: 0;
  transform: translate(-50%, -50%);
  background: var(--char);
  color: var(--gold);
  font-family: var(--font-d);
  font-size: 1.1rem;
  letter-spacing: 0.08em;
  padding: 3px 12px;
  border-radius: 100px;
  border: 1px solid var(--gold);
  z-index: 3;
}
.ab-tl-node {
  position: absolute;
  left: 50%;
  top: 24px;
  width: 14px;
  height: 14px;
  border-radius: 50%;
  background: var(--gold);
  border: 3px solid var(--cream);
  transform: translate(-50%, -50%);
  z-index: 2;
}
.ab-tl-node-latest {
  background: #27ae60;
  box-shadow: 0 0 12px rgba(39,174,96,0.6);
}
.ab-tl-card {
  background: #FFF;
  border: 1px solid var(--steel);
  border-radius: 10px;
  padding: 24px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.06);
  position: relative;
}
.ab-tl-card h4 {
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--char);
  margin-bottom: 8px;
}
.ab-tl-card p {
  font-size: 0.86rem;
  color: var(--smoke);
  line-height: 1.6;
}
.ab-tl-live {
  display: inline-block;
  background: #27ae60;
  color: #FFF;
  font-size: 0.65rem;
  font-weight: 700;
  letter-spacing: 0.1em;
  padding: 2px 8px;
  border-radius: 4px;
  margin-bottom: 8px;
}

/* ── FAQS ── */
.ab-faqs { background: #FFFFFF; }
.ab-faq-layout {
  display: grid;
  grid-template-columns: 1fr 1.35fr;
  gap: 48px;
  align-items: start;
}
.ab-faq-img-card {
  position: relative;
  border-radius: var(--radius);
  overflow: hidden;
  box-shadow: 0 16px 40px rgba(0,0,0,0.12);
  border: 1px solid var(--steel);
}
.ab-faq-img-card img {
  width: 100%;
  height: 480px;
  object-fit: cover;
  display: block;
}
.ab-faq-highlight {
  position: absolute;
  bottom: 0; left: 0; right: 0;
  background: linear-gradient(to top, rgba(14,14,14,0.95), rgba(14,14,14,0.7) 70%, transparent);
  padding: 30px 24px 24px;
  color: #FFF;
}
.ab-faq-highlight-badge {
  display: inline-block;
  font-size: 0.7rem;
  font-weight: 700;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: var(--gold);
  background: rgba(201,146,10,0.2);
  border: 1px solid rgba(201,146,10,0.4);
  padding: 4px 12px;
  border-radius: 100px;
  margin-bottom: 10px;
}
.ab-faq-highlight h3 {
  font-family: var(--font-d);
  font-size: 1.5rem;
  letter-spacing: 0.04em;
  color: #FFF;
  margin-bottom: 6px;
}
.ab-faq-highlight p {
  font-size: 0.82rem;
  color: rgba(255,255,255,0.7);
  line-height: 1.5;
}
.ab-faq-list {
  display: flex;
  flex-direction: column;
  gap: 14px;
}
.ab-faq-item {
  border: 1px solid var(--steel);
  border-radius: 10px;
  background: var(--ivory);
  overflow: hidden;
  transition: border-color 0.25s;
}
.ab-faq-item.active {
  border-color: var(--gold);
  background: #FFF;
  box-shadow: 0 6px 20px rgba(0,0,0,0.05);
}
.ab-faq-trigger {
  width: 100%;
  text-align: left;
  background: none;
  border: none;
  padding: 18px 22px;
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 16px;
  cursor: pointer;
  font-family: var(--font-b);
  font-size: 0.95rem;
  font-weight: 700;
  color: var(--char);
  transition: color 0.2s;
}
.ab-faq-item.active .ab-faq-trigger {
  color: var(--gold-dk);
}
.ab-faq-icon {
  font-size: 0.85rem;
  color: var(--smoke);
  transition: transform 0.3s ease, color 0.3s;
  flex-shrink: 0;
}
.ab-faq-item.active .ab-faq-icon {
  transform: rotate(180deg);
  color: var(--gold);
}
.ab-faq-body {
  display: none;
  padding: 0 22px 20px;
  font-size: 0.9rem;
  color: var(--smoke);
  line-height: 1.65;
  border-top: 1px solid rgba(0,0,0,0.05);
  margin-top: 4px;
  padding-top: 14px;
}

/* ── CTA STRIP ── */
.ab-cta-strip {
  background: var(--char);
  padding: 72px 0;
  border-top: 2px solid var(--gold);
}
.ab-cta-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 40px;
}
.ab-cta-text h2 {
  font-family: var(--font-d);
  font-size: clamp(2.2rem, 4vw, 3.2rem);
  letter-spacing: 0.04em;
  color: #FFF;
  line-height: 1.1;
  margin-bottom: 12px;
}
.ab-cta-text p {
  color: rgba(255,255,255,0.65);
  font-size: 0.96rem;
  max-width: 520px;
  line-height: 1.65;
}
.ab-cta-btns {
  display: flex;
  align-items: center;
  gap: 16px;
  flex-shrink: 0;
}
.ab-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: var(--gold);
  color: #FFF;
  font-weight: 700;
  font-size: 0.9rem;
  padding: 14px 28px;
  border-radius: 8px;
  box-shadow: 0 4px 18px rgba(201,146,10,0.35);
  transition: all 0.25s var(--ease);
}
.ab-btn-primary:hover {
  background: var(--gold-lt);
  color: var(--char);
  transform: translateY(-2px);
}
.ab-btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: rgba(255,255,255,0.08);
  border: 1px solid rgba(255,255,255,0.2);
  color: #FFF;
  font-weight: 600;
  font-size: 0.9rem;
  padding: 14px 26px;
  border-radius: 8px;
  transition: all 0.25s var(--ease);
}
.ab-btn-secondary:hover {
  background: rgba(255,255,255,0.16);
  border-color: rgba(255,255,255,0.4);
  color: #FFF;
}

/* ── RESPONSIVE DESIGN ── */
@media (max-width: 1024px) {
  .ab-cap-layout { grid-template-columns: 1fr; }
  .ab-media-img { height: 320px; }
  .ab-who-grid { grid-template-columns: 1fr; gap: 40px; }
  .ab-ind-grid { grid-template-columns: repeat(2, 1fr); }
  .ab-proc-grid { grid-template-columns: repeat(2, 1fr); }
  .ab-mvv-grid { grid-template-columns: 1fr; max-width: 540px; margin: 0 auto; }
  .ab-faq-layout { grid-template-columns: 1fr; }
  .ab-faq-img-card img { height: 340px; }
}

@media (max-width: 768px) {
  .ab-section { padding: 60px 0; }
  .ab-hero { min-height: auto; }
  .ab-hero-content { padding: 90px 20px 60px; }
  .ab-cap-grid { grid-template-columns: 1fr; }
  .ab-ind-grid { grid-template-columns: 1fr; }
  .ab-proc-grid { grid-template-columns: 1fr; }
  .ab-hero-stats { padding: 12px 16px; flex-wrap: wrap; justify-content: center; }
  .ab-hs { padding: 0 14px; }
  .ab-hs-n { font-size: 2.2rem; }
  
  /* Timeline mobile */
  .ab-tl-spine { left: 20px; transform: none; }
  .ab-tl-item, .ab-tl-item.ab-tl-right {
    padding-left: 54px;
    padding-right: 0;
    justify-content: flex-start;
  }
  .ab-tl-yr { left: 20px; transform: translate(-50%, -50%); }
  .ab-tl-node { left: 20px; transform: translate(-50%, -50%); }
  
  .ab-cta-inner { flex-direction: column; text-align: center; }
  .ab-cta-btns { flex-direction: column; width: 100%; }
  .ab-btn-primary, .ab-btn-secondary { width: 100%; justify-content: center; }
}
</style>

<!-- ═══════════════════════════════════════════
     SCRIPTS
════════════════════════════════════════════ -->
<script>
/* ── Golden Sparks Canvas ── */
(function(){
  const canvas = document.getElementById('sparksCanvas');
  if(!canvas) return;
  const ctx = canvas.getContext('2d');
  let W, H, particles = [];

  function resize(){
    W = canvas.width  = canvas.offsetWidth;
    H = canvas.height = canvas.offsetHeight;
  }
  resize();
  window.addEventListener('resize', resize);

  function rand(a,b){ return a + Math.random()*(b-a); }

  class Spark {
    reset(){
      this.x = rand(0, W);
      this.y = rand(0, H);
      this.vx = rand(-0.3, 0.3);
      this.vy = rand(-0.8, -0.2);
      this.size = rand(0.8, 2.4);
      this.life = 0;
      this.maxLife = rand(90, 220);
      this.gold = Math.random() > 0.4;
    }
    constructor(){ this.reset(); this.life = rand(0, 200); }
    update(){
      this.x += this.vx;
      this.y += this.vy;
      this.life++;
      if(this.life > this.maxLife) this.reset();
    }
    draw(){
      const a = Math.sin(Math.PI * this.life / this.maxLife) * 0.75;
      ctx.save();
      ctx.globalAlpha = a;
      ctx.fillStyle = this.gold ? '#C9920A' : '#E8B84B';
      ctx.shadowColor = this.gold ? '#C9920A' : '#E8B84B';
      ctx.shadowBlur = 5;
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI*2);
      ctx.fill();
      ctx.restore();
    }
  }

  for(let i = 0; i < 80; i++) particles.push(new Spark());

  function loop(){
    ctx.clearRect(0, 0, W, H);
    particles.forEach(p => { p.update(); p.draw(); });
    requestAnimationFrame(loop);
  }
  loop();
})();

/* ── Counter Animation ── */
function animateCounters(els){
  els.forEach(el => {
    const target = +el.dataset.target;
    const dur = 1800;
    const start = performance.now();
    function tick(now){
      const t = Math.min((now - start)/dur, 1);
      const ease = 1 - Math.pow(1 - t, 3);
      el.textContent = Math.floor(ease * target);
      if(t < 1) requestAnimationFrame(tick);
      else el.textContent = target;
    }
    requestAnimationFrame(tick);
  });
}

/* ── Intersection Observer for Counters & Timeline ── */
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if(!e.isIntersecting) return;
    const el = e.target;

    if(el.matches('.ab-hero')){
      const counters = el.querySelectorAll('.ab-hs-n');
      animateCounters(counters);
    }
    if(el.classList.contains('ab-tl-card')){
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    }
    io.unobserve(el);
  });
}, { threshold: 0.15 });

document.querySelectorAll('.ab-hero').forEach(el => io.observe(el));

document.querySelectorAll('.ab-tl-card').forEach(card => {
  card.style.opacity = '0';
  card.style.transform = 'translateY(16px)';
  card.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
  io.observe(card);
});

/* ── FAQ Accordion ── */
document.querySelectorAll('.ab-faq-trigger').forEach(trigger => {
  trigger.addEventListener('click', function(){
    const item = this.closest('.ab-faq-item');
    const body = item.querySelector('.ab-faq-body');
    const isOpen = item.classList.contains('active');

    // Close all other items
    document.querySelectorAll('.ab-faq-item').forEach(other => {
      other.classList.remove('active');
      other.querySelector('.ab-faq-trigger').setAttribute('aria-expanded', 'false');
      other.querySelector('.ab-faq-body').style.display = 'none';
    });

    // Toggle current item
    if(!isOpen) {
      item.classList.add('active');
      this.setAttribute('aria-expanded', 'true');
      body.style.display = 'block';
    }
  });
});
</script>
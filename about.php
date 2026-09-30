<?php
$page_title = "About Us";
include 'includes/header.php';
?>

<!-- Google Fonts: Bebas Neue + Inter -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">

<!-- ═══════════════════════════════════════════════
     ABOUT HERO  —  Full-viewport, blueprint sparks
════════════════════════════════════════════════ -->
<section class="ab-hero" id="ab-hero">
  <!-- Particle canvas (golden sparks) -->
  <canvas class="ab-sparks" id="sparksCanvas" aria-hidden="true"></canvas>

  <!-- Blueprint grid overlay -->
  <div class="ab-grid-overlay" aria-hidden="true"></div>

  <!-- Crosshair + machine visual -->
  <div class="ab-hero-machine" aria-hidden="true">
    <svg viewBox="0 0 560 420" xmlns="http://www.w3.org/2000/svg" class="machine-svg">
      <!-- Blueprint base -->
      <defs>
        <radialGradient id="glow" cx="50%" cy="50%" r="50%">
          <stop offset="0%" stop-color="#C9920A" stop-opacity="0.18"/>
          <stop offset="100%" stop-color="#C9920A" stop-opacity="0"/>
        </radialGradient>
        <filter id="blur2">
          <feGaussianBlur stdDeviation="2.5"/>
        </filter>
        <filter id="glow3">
          <feGaussianBlur stdDeviation="4" result="b"/>
          <feMerge><feMergeNode in="b"/><feMergeNode in="SourceGraphic"/></feMerge>
        </filter>
      </defs>

      <!-- Glow halo -->
      <ellipse cx="280" cy="210" rx="220" ry="160" fill="url(#glow)"/>

      <!-- CNC Machine Body -->
      <rect x="80" y="90" width="200" height="230" rx="10" fill="none" stroke="#C9920A" stroke-width="1.2" stroke-dasharray="6 3" opacity="0.4"/>
      <rect x="88" y="98" width="184" height="214" rx="7" fill="rgba(201,146,10,0.04)" stroke="#C9920A" stroke-width="0.6" opacity="0.6"/>

      <!-- Machine screen -->
      <rect x="98" y="110" width="164" height="108" rx="5" fill="rgba(0,0,0,0.6)" stroke="#C9920A" stroke-width="1"/>
      <!-- Screen scanlines -->
      <line x1="98" y1="122" x2="262" y2="122" stroke="#C9920A" stroke-width="0.3" opacity="0.3"/>
      <line x1="98" y1="134" x2="262" y2="134" stroke="#C9920A" stroke-width="0.3" opacity="0.3"/>
      <line x1="98" y1="146" x2="262" y2="146" stroke="#C9920A" stroke-width="0.3" opacity="0.3"/>
      <line x1="98" y1="158" x2="262" y2="158" stroke="#C9920A" stroke-width="0.3" opacity="0.3"/>
      <line x1="98" y1="170" x2="262" y2="170" stroke="#C9920A" stroke-width="0.3" opacity="0.3"/>
      <line x1="98" y1="182" x2="262" y2="182" stroke="#C9920A" stroke-width="0.3" opacity="0.3"/>
      <line x1="98" y1="194" x2="262" y2="194" stroke="#C9920A" stroke-width="0.3" opacity="0.3"/>
      <line x1="98" y1="206" x2="262" y2="206" stroke="#C9920A" stroke-width="0.3" opacity="0.3"/>

      <!-- CNC spindle/crosshair on screen -->
      <circle cx="180" cy="164" r="40" fill="none" stroke="#C9920A" stroke-width="1.2" filter="url(#glow3)" opacity="0.85"/>
      <circle cx="180" cy="164" r="26" fill="none" stroke="#C9920A" stroke-width="0.7" stroke-dasharray="4 3" opacity="0.6"/>
      <circle cx="180" cy="164" r="10" fill="none" stroke="#C9920A" stroke-width="1" opacity="0.9"/>
      <circle cx="180" cy="164" r="3.5" fill="#C9920A" opacity="0.95"/>
      <line x1="148" y1="164" x2="168" y2="164" stroke="#C9920A" stroke-width="0.9" opacity="0.8"/>
      <line x1="192" y1="164" x2="212" y2="164" stroke="#C9920A" stroke-width="0.9" opacity="0.8"/>
      <line x1="180" y1="132" x2="180" y2="152" stroke="#C9920A" stroke-width="0.9" opacity="0.8"/>
      <line x1="180" y1="176" x2="180" y2="196" stroke="#C9920A" stroke-width="0.9" opacity="0.8"/>

      <!-- Control panel row -->
      <rect x="94" y="228" width="172" height="28" rx="5" fill="rgba(201,146,10,0.06)" stroke="#C9920A" stroke-width="0.8" opacity="0.7"/>
      <circle cx="114" cy="242" r="7" fill="rgba(39,174,96,0.25)" stroke="#27ae60" stroke-width="1.2"/>
      <circle cx="137" cy="242" r="7" fill="rgba(201,146,10,0.25)" stroke="#C9920A" stroke-width="1.2"/>
      <circle cx="160" cy="242" r="7" fill="rgba(231,76,60,0.25)" stroke="#e74c3c" stroke-width="1.2"/>
      <rect x="176" y="235" width="80" height="14" rx="3" fill="rgba(201,146,10,0.08)" stroke="#C9920A" stroke-width="0.6" opacity="0.7"/>

      <!-- Machine legs -->
      <rect x="104" y="276" width="24" height="36" rx="3" fill="none" stroke="#C9920A" stroke-width="0.8" stroke-dasharray="3 2" opacity="0.5"/>
      <rect x="232" y="276" width="24" height="36" rx="3" fill="none" stroke="#C9920A" stroke-width="0.8" stroke-dasharray="3 2" opacity="0.5"/>

      <!-- Sheet metal bender (right machine) -->
      <rect x="320" y="110" width="150" height="160" rx="8" fill="none" stroke="#C9920A" stroke-width="1" stroke-dasharray="5 3" opacity="0.45"/>
      <rect x="330" y="124" width="130" height="88" rx="4" fill="rgba(201,146,10,0.04)" stroke="#C9920A" stroke-width="0.6" opacity="0.55"/>
      <!-- bending blade lines -->
      <line x1="330" y1="168" x2="460" y2="168" stroke="#C9920A" stroke-width="1.8" opacity="0.7" filter="url(#glow3)"/>
      <rect x="340" y="172" width="110" height="32" rx="2" fill="rgba(201,146,10,0.07)" stroke="#C9920A" stroke-width="0.7" opacity="0.6"/>
      <!-- side cylinder -->
      <ellipse cx="320" cy="168" rx="8" ry="16" fill="none" stroke="#C9920A" stroke-width="0.9" opacity="0.5"/>
      <ellipse cx="470" cy="168" rx="8" ry="16" fill="none" stroke="#C9920A" stroke-width="0.9" opacity="0.5"/>
      <!-- control box -->
      <rect x="458" y="128" width="28" height="48" rx="4" fill="rgba(201,146,10,0.07)" stroke="#C9920A" stroke-width="0.8" opacity="0.6"/>
      <circle cx="472" cy="148" r="5" fill="none" stroke="#C9920A" stroke-width="1" opacity="0.7"/>
      <circle cx="472" cy="162" r="3" fill="#C9920A" opacity="0.7"/>

      <!-- Conveyor floor -->
      <rect x="60" y="312" width="440" height="10" rx="5" fill="none" stroke="#C9920A" stroke-width="0.8" opacity="0.35"/>
      <circle cx="100" cy="317" r="5" fill="none" stroke="#C9920A" stroke-width="0.7" opacity="0.4"/>
      <circle cx="160" cy="317" r="5" fill="none" stroke="#C9920A" stroke-width="0.7" opacity="0.4"/>
      <circle cx="220" cy="317" r="5" fill="none" stroke="#C9920A" stroke-width="0.7" opacity="0.4"/>
      <circle cx="280" cy="317" r="5" fill="none" stroke="#C9920A" stroke-width="0.7" opacity="0.4"/>
      <circle cx="340" cy="317" r="5" fill="none" stroke="#C9920A" stroke-width="0.7" opacity="0.4"/>
      <circle cx="400" cy="317" r="5" fill="none" stroke="#C9920A" stroke-width="0.7" opacity="0.4"/>
      <circle cx="460" cy="317" r="5" fill="none" stroke="#C9920A" stroke-width="0.7" opacity="0.4"/>

      <!-- dimension lines / annotations -->
      <line x1="80" y1="76" x2="280" y2="76" stroke="#C9920A" stroke-width="0.6" opacity="0.35" marker-start="url(#arr)" marker-end="url(#arr)"/>
      <text x="180" y="72" fill="#C9920A" font-size="8" text-anchor="middle" opacity="0.5" font-family="Inter,sans-serif">2400mm</text>
      <line x1="66" y1="90" x2="66" y2="280" stroke="#C9920A" stroke-width="0.6" opacity="0.35"/>
      <text x="58" y="190" fill="#C9920A" font-size="8" text-anchor="middle" opacity="0.5" font-family="Inter,sans-serif" transform="rotate(-90,58,190)">1800mm</text>

      <!-- Label -->
      <text x="280" y="390" fill="#C9920A" font-size="9.5" text-anchor="middle" font-family="Inter,sans-serif" font-weight="800" letter-spacing="3.5" opacity="0.6">DIPBAN PRODUCTION FLOOR  ·  HOWRAH</text>
      <line x1="80" y1="382" x2="480" y2="382" stroke="#C9920A" stroke-width="0.5" opacity="0.3"/>
    </svg>
  </div>

  <!-- Hero text -->
  <div class="ab-hero-content container">
    <nav class="ab-breadcrumb" aria-label="breadcrumb">
      <a href="index.php">Home</a>
      <svg width="10" height="10" viewBox="0 0 10 10"><path d="M3 2l4 3-4 3" stroke="currentColor" stroke-width="1.4" fill="none"/></svg>
      <span>About Us</span>
    </nav>

    <div class="ab-hero-badge">Est. 2022 · Howrah, West Bengal</div>

    <h1 class="ab-hero-title">
      <span class="ab-title-line1">Engineering</span>
      <span class="ab-title-line2">Excellence</span>
      <span class="ab-title-line3">38+ Years</span>
    </h1>

    <p class="ab-hero-sub">Trusted manufacturer, supplier &amp; service provider of industrial machinery for woodworking and sheet metal industries across India.</p>

    <div class="ab-hero-stats">
      <div class="ab-hs"><span class="ab-hs-n" data-target="3">0</span><sup>+</sup><span class="ab-hs-l">Years</span></div>
      <div class="ab-hs-div"></div>
      <div class="ab-hs"><span class="ab-hs-n" data-target="200">0</span><sup>+</sup><span class="ab-hs-l">Installations</span></div>
      <div class="ab-hs-div"></div>
      <div class="ab-hs"><span class="ab-hs-n" data-target="100">0</span><sup>%</sup><span class="ab-hs-l">Satisfaction</span></div>
    </div>

    <div class="ab-hero-scroll" aria-hidden="true">
      <span></span>Scroll to explore
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════
     WHO WE ARE
════════════════════════════════════════════ -->
<section class="ab-section ab-who">
  <div class="container">
    <div class="ab-who-grid">

      <!-- Visual side -->
      <div class="ab-who-visual">
        <div class="ab-visual-frame">
          <!-- Premium 3D-style machine illustration -->
          <svg viewBox="0 0 480 340" xmlns="http://www.w3.org/2000/svg" style="width:100%;display:block;">
            <defs>
              <linearGradient id="machineGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#2a2a2a"/>
                <stop offset="100%" stop-color="#1a1a1a"/>
              </linearGradient>
              <linearGradient id="panelGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#3a3530"/>
                <stop offset="100%" stop-color="#2a2520"/>
              </linearGradient>
              <linearGradient id="goldGrad" x1="0%" y1="0%" x2="100%" y2="0%">
                <stop offset="0%" stop-color="#8B6508"/>
                <stop offset="50%" stop-color="#C9920A"/>
                <stop offset="100%" stop-color="#E8B84B"/>
              </linearGradient>
              <radialGradient id="screenGlow" cx="50%" cy="50%" r="50%">
                <stop offset="0%" stop-color="#C9920A" stop-opacity="0.25"/>
                <stop offset="100%" stop-color="#C9920A" stop-opacity="0"/>
              </radialGradient>
            </defs>

            <!-- Background -->
            <rect width="480" height="340" fill="#1a1714" rx="12"/>
            <!-- Floor grid -->
            <line x1="0" y1="290" x2="480" y2="290" stroke="#C9920A" stroke-width="0.5" opacity="0.2"/>
            <line x1="0" y1="300" x2="480" y2="300" stroke="#C9920A" stroke-width="0.3" opacity="0.1"/>

            <!-- MACHINE 1: CNC Panel Borer -->
            <!-- Shadow -->
            <ellipse cx="160" cy="288" rx="90" ry="10" fill="rgba(0,0,0,0.6)"/>
            <!-- Base cabinet -->
            <rect x="52" y="155" width="216" height="130" rx="8" fill="url(#machineGrad)" stroke="#C9920A" stroke-width="1"/>
            <!-- Top surface -->
            <rect x="48" y="148" width="224" height="18" rx="4" fill="url(#panelGrad)" stroke="#C9920A" stroke-width="0.8"/>
            <!-- Gantry arm -->
            <rect x="60" y="72" width="200" height="16" rx="4" fill="url(#panelGrad)" stroke="#C9920A" stroke-width="0.8"/>
            <!-- Gantry posts -->
            <rect x="60" y="72" width="14" height="80" rx="3" fill="url(#panelGrad)" stroke="#C9920A" stroke-width="0.7"/>
            <rect x="246" y="72" width="14" height="80" rx="3" fill="url(#panelGrad)" stroke="#C9920A" stroke-width="0.7"/>
            <!-- Work table -->
            <rect x="74" y="148" width="172" height="8" rx="2" fill="#C9920A" opacity="0.8"/>
            <!-- Spindle head -->
            <rect x="138" y="80" width="44" height="74" rx="5" fill="#2d2a24" stroke="#C9920A" stroke-width="1.2"/>
            <circle cx="160" cy="125" r="18" fill="url(#screenGlow)" stroke="#C9920A" stroke-width="1.4"/>
            <circle cx="160" cy="125" r="10" fill="#0a0a0a" stroke="#C9920A" stroke-width="1"/>
            <circle cx="160" cy="125" r="4" fill="#C9920A"/>
            <!-- spindle bit -->
            <rect x="157" y="143" width="6" height="16" rx="2" fill="url(#goldGrad)"/>
            <!-- Control panel on cabinet -->
            <rect x="62" y="168" width="196" height="100" rx="5" fill="#111" stroke="#C9920A" stroke-width="0.6" opacity="0.8"/>
            <!-- Screen on cabinet -->
            <rect x="72" y="176" width="96" height="58" rx="4" fill="#0d1a0d" stroke="#27ae60" stroke-width="0.8"/>
            <rect x="76" y="180" width="88" height="50" rx="2" fill="url(#screenGlow)" opacity="0.6"/>
            <text x="116" y="208" fill="#00ff88" font-size="7" text-anchor="middle" font-family="monospace" opacity="0.9">RPM: 3400</text>
            <text x="116" y="218" fill="#00ff88" font-size="7" text-anchor="middle" font-family="monospace" opacity="0.9">FEED: 24mm/s</text>
            <text x="116" y="228" fill="#00cc66" font-size="6" text-anchor="middle" font-family="monospace" opacity="0.7">STATUS: RUNNING</text>
            <!-- Buttons -->
            <circle cx="188" cy="188" r="6" fill="rgba(39,174,96,0.3)" stroke="#27ae60" stroke-width="1.2"/>
            <circle cx="206" cy="188" r="6" fill="rgba(231,76,60,0.3)" stroke="#e74c3c" stroke-width="1.2"/>
            <circle cx="224" cy="188" r="6" fill="rgba(201,146,10,0.3)" stroke="#C9920A" stroke-width="1.2"/>
            <!-- Dial -->
            <circle cx="218" cy="220" r="16" fill="#1a1a1a" stroke="#C9920A" stroke-width="1"/>
            <circle cx="218" cy="220" r="10" fill="none" stroke="#C9920A" stroke-width="0.7" stroke-dasharray="3 2"/>
            <line x1="218" y1="220" x2="224" y2="212" stroke="#C9920A" stroke-width="1.5"/>
            <!-- Legs -->
            <rect x="68" y="283" width="20" height="8" rx="2" fill="#111" stroke="#C9920A" stroke-width="0.5"/>
            <rect x="232" y="283" width="20" height="8" rx="2" fill="#111" stroke="#C9920A" stroke-width="0.5"/>

            <!-- MACHINE 2: Sheet Metal Bender (right) -->
            <ellipse cx="380" cy="288" rx="72" ry="8" fill="rgba(0,0,0,0.5)"/>
            <!-- Body -->
            <rect x="310" y="120" width="140" height="165" rx="7" fill="url(#machineGrad)" stroke="#C9920A" stroke-width="1"/>
            <!-- Top beam -->
            <rect x="305" y="112" width="150" height="20" rx="5" fill="url(#panelGrad)" stroke="#C9920A" stroke-width="1"/>
            <!-- Bending beam (glowing) -->
            <rect x="315" y="178" width="130" height="12" rx="3" fill="url(#goldGrad)" opacity="0.9" filter="url(#blur2)"/>
            <rect x="315" y="178" width="130" height="10" rx="3" fill="#C9920A" opacity="0.7"/>
            <!-- Back plate -->
            <rect x="322" y="192" width="116" height="68" rx="4" fill="#111" stroke="#C9920A" stroke-width="0.5" opacity="0.7"/>
            <!-- Side pistons -->
            <rect x="303" y="142" width="12" height="50" rx="3" fill="#2a2520" stroke="#C9920A" stroke-width="0.7"/>
            <rect x="445" y="142" width="12" height="50" rx="3" fill="#2a2520" stroke="#C9920A" stroke-width="0.7"/>
            <!-- Control box -->
            <rect x="446" y="122" width="30" height="52" rx="4" fill="#1a1a1a" stroke="#C9920A" stroke-width="0.8"/>
            <circle cx="461" cy="140" r="5" fill="none" stroke="#27ae60" stroke-width="1"/>
            <rect x="453" y="148" width="16" height="3" rx="1" fill="#C9920A" opacity="0.6"/>
            <rect x="453" y="154" width="12" height="3" rx="1" fill="#C9920A" opacity="0.4"/>
            <rect x="453" y="160" width="14" height="3" rx="1" fill="#C9920A" opacity="0.5"/>
            <!-- Legs -->
            <rect x="320" y="282" width="20" height="9" rx="2" fill="#111" stroke="#C9920A" stroke-width="0.5"/>
            <rect x="420" y="282" width="20" height="9" rx="2" fill="#111" stroke="#C9920A" stroke-width="0.5"/>

            <!-- Label bar -->
            <rect x="0" y="315" width="480" height="25" rx="0" fill="rgba(201,146,10,0.07)"/>
            <text x="240" y="331" fill="#C9920A" font-size="8.5" text-anchor="middle" font-family="Inter,sans-serif" font-weight="700" letter-spacing="4">DIPBAN TECHNICAL SERVICES  ·  HOWRAH</text>
          </svg>

          <div class="ab-visual-badge">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" stroke="#C9920A" stroke-width="2" stroke-linecap="round"/></svg>
            Factory-Trained Engineers
          </div>
        </div>

        <!-- Stat pills below image -->
        <div class="ab-stat-row">
          <div class="ab-stat">
            <span class="ab-sn" data-target="3">0</span><sup>+</sup>
            <span class="ab-sl">Yrs Excellence</span>
          </div>
          <div class="ab-stat">
            <span class="ab-sn" data-target="200">0</span><sup>+</sup>
            <span class="ab-sl">Installations</span>
          </div>
          <div class="ab-stat">
            <span class="ab-sn" data-target="100">0</span><sup>%</sup>
            <span class="ab-sl">Satisfaction</span>
          </div>
        </div>
      </div>

      <!-- Text side -->
      <div class="ab-who-text">
        <span class="ab-eyebrow">Our Story</span>
        <h2 class="ab-h2">Powering Industries with <em>Precision &amp; Innovation</em></h2>
        <p>Arup Enterprise was established in 2022 with a clear vision: to deliver world-class industrial machinery that gives businesses a genuine competitive edge. We entered the market as a wholesaler and trader of woodworking panel processing machinery and hydraulic machines — and quickly earned a reputation built on performance, reliability, and true after-sales partnership.</p>
        <p>Headquartered in Liluah, Howrah, we serve clients across West Bengal, Odhisha, Assam, Jharkhand, Northeast and beyond, offering a comprehensive range of machines alongside factory-trained service engineers and genuine spare parts always in stock.</p>
        <p>Our philosophy is straightforward — a machine is only as valuable as the support behind it. That's why every Arup Enterprise client benefits from 24×7 technical assistance, rapid response deployment, and a commitment that doesn't end at delivery.</p>

        <div class="ab-who-tags">
          <span><i class="fas fa-map-marker-alt"></i>Howrah, West Bengal</span>
          <span><i class="fas fa-calendar-check"></i>Est. 2022</span>
          <span><i class="fas fa-globe"></i>Pan-India Service</span>
        </div>

        <a href="contact.php" class="ab-cta-inline">
          Get a Free Consultation
          <svg width="18" height="18" viewBox="0 0 24 24" fill="none"><path d="M5 12h14M12 5l7 7-7 7" stroke="currentColor" stroke-width="2" stroke-linecap="round"/></svg>
        </a>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════
     MISSION  VISION  VALUES
════════════════════════════════════════════ -->
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
        <p>To empower manufacturing businesses with advanced, reliable industrial machinery — backed by exceptional engineering support, genuine spare parts, and a commitment to zero downtime for every client we serve.</p>
      </div>

      <div class="ab-mvv-card ab-mvv-vision">
        <div class="ab-mvv-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" stroke="currentColor" stroke-width="1.8"/><circle cx="12" cy="12" r="3" stroke="currentColor" stroke-width="1.8"/></svg>
        </div>
        <h3>Our Vision</h3>
        <p>To become the most trusted industrial machinery partner in Eastern India — recognized for precision, team integrity, and the transformative impact we create in every factory we work with.</p>
      </div>

      <div class="ab-mvv-card ab-mvv-values">
        <div class="ab-mvv-icon">
          <svg width="26" height="26" viewBox="0 0 24 24" fill="none"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" stroke="currentColor" stroke-width="1.8" stroke-linejoin="round"/></svg>
        </div>
        <h3>Our Values</h3>
        <ul class="ab-values-list">
          <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#C9920A" stroke-width="2.5" stroke-linecap="round"/></svg> Precision in every component</li>
          <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#C9920A" stroke-width="2.5" stroke-linecap="round"/></svg> Integrity in every interaction</li>
          <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#C9920A" stroke-width="2.5" stroke-linecap="round"/></svg> Innovation that creates real value</li>
          <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#C9920A" stroke-width="2.5" stroke-linecap="round"/></svg> Sustainability in design &amp; process</li>
          <li><svg width="14" height="14" viewBox="0 0 24 24" fill="none"><path d="M20 6L9 17l-5-5" stroke="#C9920A" stroke-width="2.5" stroke-linecap="round"/></svg> Partnership beyond the sale</li>
        </ul>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════
     TIMELINE
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
        <div class="ab-tl-yr">2022</div>
        <div class="ab-tl-node"></div>
        <div class="ab-tl-card">
          <h4>Founded</h4>
          <p>Arup Enterprise established in Howrah as a wholesale trader of woodworking and hydraulic machinery — setting the foundation for a full-service industrial machinery company.</p>
        </div>
      </div>

      <div class="ab-tl-item ab-tl-right">
        <div class="ab-tl-yr">2022</div>
        <div class="ab-tl-node"></div>
        <div class="ab-tl-card">
          <h4>First 25 Installations</h4>
          <p>Completed 25 machine installations across furniture and fabrication workshops in West Bengal, earning early recognition for responsive service and machine quality.</p>
        </div>
      </div>

      <div class="ab-tl-item">
        <div class="ab-tl-yr">2023</div>
        <div class="ab-tl-node"></div>
        <div class="ab-tl-card">
          <h4>Expanded Product Portfolio</h4>
          <p>Added CNC Boring Machines and Sheet Metal Machinery — becoming a one-stop solution for both woodworking and metal fabrication industries.</p>
        </div>
      </div>

      <div class="ab-tl-item ab-tl-right">
        <div class="ab-tl-yr">2023</div>
        <div class="ab-tl-node"></div>
        <div class="ab-tl-card">
          <h4>Service Network Growth</h4>
          <p>Established dedicated service centers and expanded our field engineer team to ensure same-day or next-day response for all clients across the region.</p>
        </div>
      </div>

      <div class="ab-tl-item">
        <div class="ab-tl-yr">2024</div>
        <div class="ab-tl-node"></div>
        <div class="ab-tl-card">
          <h4>100+ Active Clients</h4>
          <p>Crossed 100 active clients with installations spanning Howrah, Kolkata, Durgapur, extending into Jharkhand and Odisha.</p>
        </div>
      </div>

      <div class="ab-tl-item ab-tl-right">
        <div class="ab-tl-yr">2025</div>
        <div class="ab-tl-node ab-tl-node-latest"></div>
        <div class="ab-tl-card ab-tl-card-latest">
          <span class="ab-tl-live">LATEST</span>
          <h4>200+ Installations &amp; Digital Expansion</h4>
          <p>Surpassed 200 total installations. Launched our digital platform and AI-powered website to better serve clients and streamline quote &amp; support requests 24×7.</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════
     PERFORMANCE METRICS
════════════════════════════════════════════ -->
<section class="ab-section ab-metrics">
  <div class="container">
    <div class="ab-section-head">
      <span class="ab-eyebrow">By the Numbers</span>
      <h2 class="ab-h2">Engineering <em>Performance</em></h2>
    </div>

    <div class="ab-metrics-wrap">
      <div class="ab-metric-item">
        <div class="ab-metric-head">
          <span class="ab-metric-lbl">Production Quality</span>
          <span class="ab-metric-pct">98%</span>
        </div>
        <div class="ab-metric-track">
          <div class="ab-metric-bar" data-pct="98"></div>
        </div>
      </div>
      <div class="ab-metric-item">
        <div class="ab-metric-head">
          <span class="ab-metric-lbl">Technology Integration</span>
          <span class="ab-metric-pct">97%</span>
        </div>
        <div class="ab-metric-track">
          <div class="ab-metric-bar" data-pct="97"></div>
        </div>
      </div>
      <div class="ab-metric-item">
        <div class="ab-metric-head">
          <span class="ab-metric-lbl">Customer Satisfaction</span>
          <span class="ab-metric-pct">100%</span>
        </div>
        <div class="ab-metric-track">
          <div class="ab-metric-bar" data-pct="100"></div>
        </div>
      </div>
      <div class="ab-metric-item">
        <div class="ab-metric-head">
          <span class="ab-metric-lbl">On-Time Delivery</span>
          <span class="ab-metric-pct">95%</span>
        </div>
        <div class="ab-metric-track">
          <div class="ab-metric-bar" data-pct="95"></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ═══════════════════════════════════════════
     CTA STRIP
════════════════════════════════════════════ -->
<section class="ab-cta-strip">
  <div class="ab-cta-sparks" aria-hidden="true"></div>
  <div class="container">
    <div class="ab-cta-inner">
      <div class="ab-cta-text">
        <span class="ab-eyebrow" style="color:#E8B84B">Let's Work Together</span>
        <h2>Ready to Transform Your<br>Production Capabilities?</h2>
        <p>Talk to our experts today. We'll identify the right machinery for your requirements and budget.</p>
      </div>
      <div class="ab-cta-btns">
        <a href="contact.php#quote" class="ab-btn-primary">
          <i class="fas fa-paper-plane"></i> Get a Free Quote
        </a>
        <a href="tel:+918013635806" class="ab-btn-secondary">
          <i class="fas fa-phone-alt"></i> Call Us Now
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
/* ── TOKENS ── */
:root {
  --gold:       #C9920A;
  --gold-lt:    #E8B84B;
  --gold-dk:    #8B6508;
  --char:       #0E0E0E;
  --ink:        #1C1C1C;
  --cream:      #F5EED8;
  --ivory:      #FAFAF5;
  --steel:      #E8E4DC;
  --smoke:      #6B6560;
  --white:      #FFFFFF;
  --font-d:     'Bebas Neue', 'Impact', sans-serif;
  --font-b:     'Inter', -apple-system, sans-serif;
  --ease:       cubic-bezier(0.4, 0, 0.2, 1);
  --shadow-g:   0 8px 32px rgba(201,146,10,0.22);
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
body { font-family: var(--font-b); }
.container { max-width: 1240px; margin: 0 auto; padding: 0 24px; }
a { text-decoration: none; }

/* ── EYEBROW ── */
.ab-eyebrow {
  display: inline-block;
  font-size: 0.67rem;
  font-weight: 700;
  letter-spacing: 0.22em;
  text-transform: uppercase;
  color: var(--gold);
  background: rgba(201,146,10,0.09);
  border: 1px solid rgba(201,146,10,0.22);
  border-radius: 100px;
  padding: 4px 14px;
  margin-bottom: 12px;
}

/* ── SECTION HEAD ── */
.ab-section-head { text-align: center; margin-bottom: 56px; }
.ab-h2 {
  font-family: var(--font-d);
  font-size: clamp(2rem, 4vw, 3rem);
  letter-spacing: 0.02em;
  color: var(--char);
  line-height: 1.12;
}
.ab-h2 em { color: var(--gold); font-style: normal; }

/* ── SECTION BASE ── */
.ab-section { padding: 88px 0; }

/* ═══════════════════════════════════════════
   HERO
════════════════════════════════════════════ */
.ab-hero {
  position: relative;
  min-height: 100vh;
  background: var(--char);
  display: flex;
  align-items: center;
  overflow: hidden;
  border-bottom: 2px solid var(--gold);
}

/* Sparks canvas */
.ab-sparks {
  position: absolute;
  inset: 0;
  width: 100%;
  height: 100%;
  pointer-events: none;
  opacity: 0.7;
}

/* Blueprint grid */
.ab-grid-overlay {
  position: absolute;
  inset: 0;
  background-image:
    linear-gradient(rgba(201,146,10,0.04) 1px, transparent 1px),
    linear-gradient(90deg, rgba(201,146,10,0.04) 1px, transparent 1px);
  background-size: 52px 52px;
  pointer-events: none;
}

/* Machine visual (right side) */
.ab-hero-machine {
  position: absolute;
  right: -40px;
  top: 50%;
  transform: translateY(-50%);
  width: clamp(380px, 50vw, 640px);
  opacity: 0.38;
  pointer-events: none;
  filter: drop-shadow(0 0 40px rgba(201,146,10,0.15));
}
.machine-svg { width: 100%; }

/* Hero content */
.ab-hero-content {
  position: relative;
  z-index: 2;
  padding: 120px 24px 80px;
  max-width: 680px;
}

.ab-breadcrumb {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.78rem;
  color: #666;
  margin-bottom: 24px;
}
.ab-breadcrumb a { color: var(--gold); transition: color 0.2s; }
.ab-breadcrumb a:hover { color: var(--gold-lt); }
.ab-breadcrumb svg { color: #555; }

.ab-hero-badge {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  font-size: 0.72rem;
  font-weight: 700;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  color: var(--gold);
  border: 1px solid rgba(201,146,10,0.3);
  border-radius: 100px;
  padding: 6px 18px;
  margin-bottom: 24px;
  background: rgba(201,146,10,0.07);
}

.ab-hero-title {
  display: flex;
  flex-direction: column;
  margin-bottom: 22px;
}
.ab-title-line1 {
  font-family: var(--font-d);
  font-size: clamp(3rem, 7vw, 6rem);
  letter-spacing: 0.05em;
  color: rgba(255,255,255,0.55);
  line-height: 1;
}
.ab-title-line2 {
  font-family: var(--font-d);
  font-size: clamp(4.5rem, 11vw, 9.5rem);
  letter-spacing: 0.02em;
  color: var(--gold);
  line-height: 0.92;
  text-shadow: 0 0 80px rgba(201,146,10,0.35);
}
.ab-title-line3 {
  font-family: var(--font-d);
  font-size: clamp(2rem, 4.5vw, 4rem);
  letter-spacing: 0.1em;
  color: rgba(255,255,255,0.35);
  line-height: 1.1;
}

.ab-hero-sub {
  color: rgba(255,255,255,0.58);
  font-size: 1rem;
  line-height: 1.7;
  max-width: 500px;
  margin-bottom: 40px;
}

/* Hero stats bar */
.ab-hero-stats {
  display: flex;
  align-items: center;
  gap: 0;
  background: rgba(255,255,255,0.04);
  border: 1px solid rgba(201,146,10,0.18);
  border-radius: 14px;
  padding: 18px 28px;
  width: fit-content;
  backdrop-filter: blur(10px);
  margin-bottom: 44px;
}
.ab-hs {
  display: flex;
  flex-direction: column;
  align-items: center;
  padding: 0 28px;
}
.ab-hs-n {
  font-family: var(--font-d);
  font-size: 2.8rem;
  letter-spacing: 0.03em;
  color: var(--gold);
  line-height: 1;
}
.ab-hs sup {
  font-family: var(--font-b);
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--gold-lt);
  vertical-align: super;
  line-height: 1;
}
.ab-hs-l {
  font-size: 0.68rem;
  font-weight: 700;
  letter-spacing: 0.14em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.4);
  margin-top: 4px;
}
.ab-hs-div {
  width: 1px;
  height: 40px;
  background: rgba(201,146,10,0.2);
  flex-shrink: 0;
}

/* Scroll hint */
.ab-hero-scroll {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 0.72rem;
  letter-spacing: 0.15em;
  text-transform: uppercase;
  color: rgba(255,255,255,0.3);
}
.ab-hero-scroll span {
  display: block;
  width: 1px;
  height: 36px;
  background: linear-gradient(to bottom, var(--gold), transparent);
  animation: scrollPulse 2s ease-in-out infinite;
}
@keyframes scrollPulse {
  0%,100% { opacity: 0.3; transform: scaleY(1); }
  50%      { opacity: 0.8; transform: scaleY(1.3); }
}

/* ═══════════════════════════════════════════
   WHO WE ARE
════════════════════════════════════════════ */
.ab-who { background: var(--cream); }
.ab-who-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 64px;
  align-items: start;
}

/* Visual frame */
.ab-visual-frame {
  position: relative;
  border-radius: 16px;
  overflow: hidden;
  border: 1px solid rgba(201,146,10,0.18);
  box-shadow: 0 20px 60px rgba(0,0,0,0.18), var(--shadow-g);
  margin-bottom: 20px;
  background: #1a1714;
}
.ab-visual-badge {
  position: absolute;
  bottom: 14px;
  left: 14px;
  display: inline-flex;
  align-items: center;
  gap: 7px;
  background: rgba(0,0,0,0.75);
  border: 1px solid rgba(201,146,10,0.3);
  color: rgba(255,255,255,0.82);
  font-size: 0.74rem;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 100px;
  backdrop-filter: blur(8px);
}

/* Stat row */
.ab-stat-row {
  display: grid;
  grid-template-columns: repeat(3,1fr);
  gap: 12px;
}
.ab-stat {
  background: var(--white);
  border: 1px solid rgba(201,146,10,0.14);
  border-radius: 12px;
  padding: 16px 12px;
  text-align: center;
  transition: border-color 0.25s, transform 0.25s;
}
.ab-stat:hover { border-color: var(--gold); transform: translateY(-3px); }
.ab-sn {
  display: inline-block;
  font-family: var(--font-d);
  font-size: 2rem;
  letter-spacing: 0.03em;
  color: var(--gold);
  line-height: 1;
}
.ab-stat sup {
  font-size: 0.9rem;
  font-weight: 800;
  color: var(--gold-lt);
  vertical-align: super;
}
.ab-sl {
  display: block;
  font-size: 0.67rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  color: var(--smoke);
  margin-top: 4px;
}

/* Text side */
.ab-who-text .ab-h2 { font-size: clamp(1.7rem, 3vw, 2.4rem); text-align: left; margin-bottom: 20px; }
.ab-who-text p { color: var(--smoke); line-height: 1.75; margin-bottom: 14px; font-size: 0.93rem; }
.ab-who-tags { display: flex; gap: 10px; flex-wrap: wrap; margin: 22px 0 28px; }
.ab-who-tags span {
  display: inline-flex;
  align-items: center;
  gap: 7px;
  font-size: 0.76rem;
  font-weight: 600;
  color: var(--gold-dk);
  background: rgba(201,146,10,0.09);
  border: 1px solid rgba(201,146,10,0.22);
  border-radius: 100px;
  padding: 6px 14px;
}
.ab-who-tags i { font-size: 0.72rem; }

.ab-cta-inline {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dk) 100%);
  color: white;
  font-weight: 700;
  font-size: 0.9rem;
  padding: 13px 24px;
  border-radius: 10px;
  box-shadow: var(--shadow-g);
  transition: all 0.25s var(--ease);
}
.ab-cta-inline:hover { transform: translateY(-2px); box-shadow: 0 12px 32px rgba(201,146,10,0.38); }
.ab-cta-inline svg { transition: transform 0.25s; }
.ab-cta-inline:hover svg { transform: translateX(4px); }

/* ═══════════════════════════════════════════
   MISSION / VISION / VALUES
════════════════════════════════════════════ */
.ab-mvv { background: var(--ivory); }
.ab-mvv-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 24px; }
.ab-mvv-card {
  background: var(--white);
  border-radius: 16px;
  padding: 34px 28px;
  border: 1px solid rgba(201,146,10,0.1);
  border-top: 3px solid var(--gold);
  box-shadow: 0 4px 20px rgba(0,0,0,0.05);
  transition: transform 0.28s var(--ease), box-shadow 0.28s;
  position: relative;
  overflow: hidden;
}
.ab-mvv-card::before {
  content: '';
  position: absolute;
  inset: 0;
  background: radial-gradient(circle at top left, rgba(201,146,10,0.04) 0%, transparent 60%);
  pointer-events: none;
}
.ab-mvv-card:hover { transform: translateY(-8px); box-shadow: 0 20px 50px rgba(0,0,0,0.1), var(--shadow-g); }
.ab-mvv-icon {
  width: 56px; height: 56px;
  background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dk) 100%);
  border-radius: 14px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: white;
  margin-bottom: 20px;
  box-shadow: var(--shadow-g);
}
.ab-mvv-card h3 {
  font-family: var(--font-b);
  font-size: 1.1rem;
  font-weight: 800;
  color: var(--char);
  margin-bottom: 12px;
}
.ab-mvv-card p { color: var(--smoke); font-size: 0.87rem; line-height: 1.72; }
.ab-values-list { list-style: none; margin-top: 4px; }
.ab-values-list li {
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 7px 0;
  font-size: 0.85rem;
  color: var(--smoke);
  border-bottom: 1px solid rgba(201,146,10,0.07);
}
.ab-values-list li:last-child { border-bottom: none; }

/* ═══════════════════════════════════════════
   TIMELINE
════════════════════════════════════════════ */
.ab-timeline-section { background: var(--cream); }
.ab-timeline {
  position: relative;
  max-width: 860px;
  margin: 0 auto;
}
.ab-tl-spine {
  position: absolute;
  left: 50%;
  top: 0; bottom: 0;
  width: 2px;
  background: linear-gradient(to bottom, var(--gold) 0%, rgba(201,146,10,0.08) 100%);
  transform: translateX(-50%);
}
.ab-tl-item {
  display: flex;
  justify-content: flex-end;
  padding-right: calc(50% + 36px);
  padding-bottom: 52px;
  position: relative;
}
.ab-tl-item.ab-tl-right {
  justify-content: flex-start;
  padding-right: 0;
  padding-left: calc(50% + 36px);
}
.ab-tl-yr {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  top: 2px;
  background: var(--gold);
  color: white;
  font-size: 0.68rem;
  font-weight: 800;
  letter-spacing: 0.1em;
  padding: 4px 12px;
  border-radius: 100px;
  white-space: nowrap;
  z-index: 2;
  box-shadow: 0 4px 12px rgba(201,146,10,0.4);
}
.ab-tl-node {
  position: absolute;
  left: 50%;
  top: 28px;
  transform: translateX(-50%);
  width: 16px; height: 16px;
  background: var(--cream);
  border: 3px solid var(--gold);
  border-radius: 50%;
  z-index: 2;
  transition: background 0.25s;
}
.ab-tl-node-latest {
  background: var(--gold);
  box-shadow: 0 0 0 6px rgba(201,146,10,0.2);
  animation: nodePulse 2.5s ease-in-out infinite;
}
@keyframes nodePulse {
  0%,100% { box-shadow: 0 0 0 6px rgba(201,146,10,0.2); }
  50%      { box-shadow: 0 0 0 12px rgba(201,146,10,0.08); }
}
.ab-tl-card {
  background: var(--white);
  border: 1px solid rgba(201,146,10,0.12);
  border-radius: 14px;
  padding: 22px 24px;
  max-width: 360px;
  box-shadow: 0 4px 16px rgba(0,0,0,0.06);
  transition: border-color 0.25s, transform 0.25s, box-shadow 0.25s;
  position: relative;
}
.ab-tl-card:hover { border-color: var(--gold); transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,0.1); }
.ab-tl-card-latest { border-color: rgba(201,146,10,0.28); border-top: 2px solid var(--gold); }
.ab-tl-live {
  display: inline-block;
  font-size: 0.6rem;
  font-weight: 800;
  letter-spacing: 0.18em;
  text-transform: uppercase;
  background: rgba(201,146,10,0.12);
  color: var(--gold);
  border: 1px solid rgba(201,146,10,0.25);
  border-radius: 100px;
  padding: 2px 10px;
  margin-bottom: 8px;
}
.ab-tl-card h4 {
  font-family: var(--font-b);
  font-weight: 800;
  font-size: 1rem;
  color: var(--char);
  margin-bottom: 8px;
}
.ab-tl-card p { color: var(--smoke); font-size: 0.83rem; line-height: 1.65; }

/* ═══════════════════════════════════════════
   METRICS
════════════════════════════════════════════ */
.ab-metrics { background: var(--char); }
.ab-metrics .ab-section-head .ab-h2 { color: var(--white); }
.ab-metrics .ab-section-head .ab-h2 em { color: var(--gold); }
.ab-metrics .ab-eyebrow { background: rgba(201,146,10,0.12); }

.ab-metrics-wrap {
  max-width: 720px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  gap: 28px;
}
.ab-metric-item {}
.ab-metric-head {
  display: flex;
  justify-content: space-between;
  align-items: baseline;
  margin-bottom: 10px;
}
.ab-metric-lbl { font-size: 0.9rem; font-weight: 600; color: rgba(255,255,255,0.72); }
.ab-metric-pct { font-family: var(--font-d); font-size: 1.4rem; letter-spacing: 0.04em; color: var(--gold); }
.ab-metric-track {
  height: 8px;
  background: rgba(255,255,255,0.07);
  border-radius: 100px;
  overflow: hidden;
}
.ab-metric-bar {
  height: 100%;
  width: 0;
  background: linear-gradient(90deg, var(--gold-dk) 0%, var(--gold) 50%, var(--gold-lt) 100%);
  border-radius: 100px;
  transition: width 1.6s var(--ease);
  position: relative;
}
.ab-metric-bar::after {
  content: '';
  position: absolute;
  right: 0;
  top: 50%;
  transform: translateY(-50%);
  width: 14px; height: 14px;
  background: var(--gold-lt);
  border-radius: 50%;
  box-shadow: 0 0 12px rgba(232,184,75,0.7);
  opacity: 0;
  transition: opacity 0.3s 1.4s;
}
.ab-metric-bar.animated::after { opacity: 1; }

/* ═══════════════════════════════════════════
   CTA STRIP
════════════════════════════════════════════ */
.ab-cta-strip {
  position: relative;
  background: linear-gradient(135deg, #0E0E0E 0%, #1a1410 100%);
  border-top: 2px solid var(--gold);
  padding: 72px 0;
  overflow: hidden;
}
.ab-cta-inner {
  display: flex;
  align-items: center;
  justify-content: space-between;
  gap: 40px;
  flex-wrap: wrap;
}
.ab-cta-text h2 {
  font-family: var(--font-d);
  font-size: clamp(1.8rem, 3.5vw, 2.8rem);
  letter-spacing: 0.03em;
  color: var(--white);
  margin: 8px 0;
  line-height: 1.1;
}
.ab-cta-text p { color: rgba(255,255,255,0.5); font-size: 0.93rem; margin-top: 8px; }
.ab-cta-btns { display: flex; gap: 14px; flex-wrap: wrap; flex-shrink: 0; }

.ab-btn-primary {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  background: linear-gradient(135deg, var(--gold) 0%, var(--gold-dk) 100%);
  color: white;
  font-weight: 700;
  font-size: 0.93rem;
  padding: 14px 28px;
  border-radius: 10px;
  box-shadow: var(--shadow-g);
  transition: all 0.25s var(--ease);
}
.ab-btn-primary:hover { transform: translateY(-2px); box-shadow: 0 14px 36px rgba(201,146,10,0.45); }

.ab-btn-secondary {
  display: inline-flex;
  align-items: center;
  gap: 9px;
  border: 2px solid rgba(255,255,255,0.25);
  color: rgba(255,255,255,0.85);
  font-weight: 600;
  font-size: 0.93rem;
  padding: 14px 28px;
  border-radius: 10px;
  transition: all 0.25s var(--ease);
}
.ab-btn-secondary:hover { border-color: var(--gold); color: var(--gold-lt); }

/* ═══════════════════════════════════════════
   RESPONSIVE
════════════════════════════════════════════ */
@media (max-width: 1024px) {
  .ab-who-grid { grid-template-columns: 1fr; gap: 40px; }
  .ab-mvv-grid { grid-template-columns: 1fr; max-width: 520px; margin: 0 auto; }
}
@media (max-width: 768px) {
  .ab-section { padding: 56px 0; }
  .ab-hero { min-height: auto; padding-bottom: 60px; }
  .ab-hero-machine { width: 260px; opacity: 0.12; right: -20px; }
  .ab-title-line2 { font-size: clamp(3.5rem, 16vw, 5.5rem); }
  .ab-hero-stats { gap: 0; padding: 14px 14px; }
  .ab-hs { padding: 0 16px; }
  .ab-hs-n { font-size: 2rem; }
  /* Timeline mobile */
  .ab-tl-spine { left: 20px; }
  .ab-tl-item, .ab-tl-item.ab-tl-right {
    padding-left: 52px;
    padding-right: 0;
    justify-content: flex-start;
  }
  .ab-tl-yr, .ab-tl-node { left: 20px; }
  .ab-tl-card { max-width: 100%; }
  /* Metrics */
  .ab-metric-lbl { font-size: 0.82rem; }
  /* CTA */
  .ab-cta-inner { flex-direction: column; text-align: center; }
  .ab-cta-btns { justify-content: center; }
}
@media (max-width: 480px) {
  .ab-stat-row { grid-template-columns: 1fr; }
  .ab-hero-stats { flex-direction: column; gap: 12px; }
  .ab-hs-div { width: 60px; height: 1px; }
}
</style>

<!-- ═══════════════════════════════════════════
     SCRIPTS
════════════════════════════════════════════ -->
<script>
/* ── Sparks particle system ── */
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
      this.size = rand(0.8, 2.5);
      this.life = 0;
      this.maxLife = rand(80, 200);
      this.gold = Math.random() > 0.5;
    }
    constructor(){ this.reset(); this.life = rand(0, 200); }
    update(){
      this.x += this.vx;
      this.y += this.vy;
      this.life++;
      if(this.life > this.maxLife) this.reset();
    }
    draw(){
      const a = Math.sin(Math.PI * this.life / this.maxLife) * 0.7;
      ctx.save();
      ctx.globalAlpha = a;
      ctx.fillStyle = this.gold ? '#C9920A' : '#E8B84B';
      ctx.shadowColor = this.gold ? '#C9920A' : '#E8B84B';
      ctx.shadowBlur = 6;
      ctx.beginPath();
      ctx.arc(this.x, this.y, this.size, 0, Math.PI*2);
      ctx.fill();
      ctx.restore();
    }
  }

  for(let i = 0; i < 90; i++) particles.push(new Spark());

  function loop(){
    ctx.clearRect(0,0,W,H);
    particles.forEach(p => { p.update(); p.draw(); });
    requestAnimationFrame(loop);
  }
  loop();
})();

/* ── Counter animation ── */
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

/* ── Metric bars animate on scroll ── */
function animateBars(bars){
  bars.forEach(bar => {
    const pct = bar.dataset.pct;
    bar.style.width = pct + '%';
    bar.classList.add('animated');
  });
}

/* ── Intersection Observer ── */
const io = new IntersectionObserver((entries) => {
  entries.forEach(e => {
    if(!e.isIntersecting) return;
    const el = e.target;

    if(el.matches('.ab-hero')){
      const counters = el.querySelectorAll('.ab-hs-n');
      animateCounters(counters);
    }
    if(el.matches('.ab-who-visual')){
      const counters = el.querySelectorAll('.ab-sn');
      animateCounters(counters);
    }
    if(el.matches('.ab-metrics-wrap')){
      animateBars(el.querySelectorAll('.ab-metric-bar'));
    }
    // Fade-in for TL cards
    if(el.classList.contains('ab-tl-card')){
      el.style.opacity = '1';
      el.style.transform = 'translateY(0)';
    }
    io.unobserve(el);
  });
}, { threshold: 0.15 });

document.querySelectorAll('.ab-hero, .ab-who-visual, .ab-metrics-wrap').forEach(el => io.observe(el));

// TL card init
document.querySelectorAll('.ab-tl-card').forEach(card => {
  card.style.opacity = '0';
  card.style.transform = 'translateY(16px)';
  card.style.transition = 'opacity 0.5s ease, transform 0.5s ease, border-color 0.25s, box-shadow 0.25s';
  io.observe(card);
});
</script>
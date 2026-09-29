<?php
// help.php - Help Center / Support Page for Friends Travels
session_start();
require_once "includes/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help Center | Friends Travels</title>
    <link rel="icon" type="image/png" href="images/logo.png">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Playfair+Display:wght@600;700;800&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --crimson: #b91c2e;
            --crimson-dark: #8b1522;
            --crimson-light: #e02035;
            --gold: #c9a84c;
            --gold-light: #e8c97a;
            --ink: #080b12;
            --deep: #0f1219;
            --surface: #161b26;
            --snow: #f2ede4;
            --mist: #c8c4bc;
            --dim: #6e7385;
            --white: #ffffff;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }
        
        body {
            font-family: 'Inter', sans-serif;
            background: var(--ink);
            color: var(--snow);
            overflow-x: hidden;
        }

        .page-banner {
            position: relative;
            background: linear-gradient(135deg, rgba(185,28,46,0.85), rgba(8,11,18,0.92)), url('https://images.unsplash.com/photo-1506905925346-21bda4d32df4?w=1800&q=85');
            background-size: cover;
            background-position: center;
            height: 280px;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        .page-banner::before {
            content: '';
            position: absolute;
            bottom: -50px;
            left: 0;
            right: 0;
            height: 100px;
            background: var(--ink);
            clip-path: polygon(0 50%, 100% 0, 100% 100%, 0 100%);
        }
        .page-banner .overlay {
            text-align: center;
            color: var(--white);
            position: relative;
            z-index: 2;
        }
        .page-banner h1 {
            font-family: 'Playfair Display', serif;
            font-size: clamp(2.5rem, 5vw, 4rem);
            font-weight: 700;
            margin-bottom: 1rem;
            text-shadow: 0 2px 20px rgba(0,0,0,0.3);
        }
        .page-banner .breadcrumb {
            font-size: 0.9rem;
            background: transparent;
            padding: 0;
        }
        .page-banner .breadcrumb a {
            color: var(--gold);
            text-decoration: none;
            transition: color 0.3s;
        }
        .page-banner .breadcrumb a:hover { color: var(--gold-light); }
        .page-banner .breadcrumb .active { color: var(--snow); }

        .help-section {
            padding: 60px 0 80px;
            background: var(--ink);
        }

        /* Quick Help Cards */
        .help-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .help-card {
            background: var(--surface);
            border: 1px solid rgba(185,28,46,0.15);
            border-radius: 16px;
            padding: 24px 20px;
            text-align: center;
            transition: all 0.3s;
            text-decoration: none;
            color: var(--snow);
        }
        .help-card:hover {
            transform: translateY(-5px);
            border-color: var(--gold);
            box-shadow: 0 12px 30px rgba(0,0,0,0.2);
        }
        .help-card .icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, rgba(185,28,46,0.15), rgba(201,168,76,0.08));
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 12px;
        }
        .help-card .icon i {
            font-size: 1.6rem;
            color: var(--gold);
        }
        .help-card h4 {
            font-size: 0.95rem;
            font-weight: 700;
            margin-bottom: 6px;
        }
        .help-card p {
            font-size: 0.75rem;
            color: var(--dim);
            margin: 0;
        }

        /* Contact Cards */
        .contact-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        .contact-card {
            background: var(--surface);
            border: 1px solid rgba(185,28,46,0.12);
            border-radius: 14px;
            padding: 22px 20px;
            text-align: center;
            transition: all 0.3s;
        }
        .contact-card:hover {
            border-color: var(--gold);
            transform: translateY(-3px);
        }
        .contact-card .icon-circle {
            width: 50px;
            height: 50px;
            background: rgba(185,28,46,0.12);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 10px;
        }
        .contact-card .icon-circle i {
            font-size: 1.4rem;
            color: var(--gold);
        }
        .contact-card h5 {
            font-size: 0.9rem;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .contact-card p {
            font-size: 0.8rem;
            color: var(--dim);
            margin-bottom: 6px;
        }
        .contact-card a {
            color: var(--gold);
            text-decoration: none;
            font-size: 0.85rem;
            font-weight: 600;
        }
        .contact-card a:hover { color: var(--gold-light); }

        /* FAQ Preview */
        .faq-preview {
            background: var(--surface);
            border: 1px solid rgba(185,28,46,0.12);
            border-radius: 16px;
            padding: 28px 30px;
            margin-bottom: 30px;
        }
        .faq-preview h3 {
            font-family: 'Playfair Display', serif;
            font-size: 1.3rem;
            font-weight: 700;
            color: var(--gold);
            margin-bottom: 18px;
        }
        .faq-preview h3 i {
            margin-right: 10px;
        }
        .faq-item {
            padding: 12px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .faq-item:last-child {
            border-bottom: none;
        }
        .faq-item a {
            color: var(--mist);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .faq-item a:hover {
            color: var(--gold);
        }
        .faq-item a .q-icon {
            color: var(--gold);
            font-weight: 700;
        }
        .faq-item .arrow {
            color: var(--dim);
            font-size: 0.8rem;
            transition: color 0.3s;
        }
        .faq-item:hover .arrow {
            color: var(--gold);
        }

        .btn-view-all {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 24px;
            border-radius: 30px;
            background: linear-gradient(135deg, var(--crimson), var(--crimson-dark));
            color: var(--white);
            text-decoration: none;
            font-weight: 700;
            font-size: 0.85rem;
            transition: all 0.3s;
            margin-top: 10px;
        }
        .btn-view-all:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(185,28,46,0.3);
            color: var(--white);
        }

        .btn-back {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 20px;
            border-radius: 30px;
            background: transparent;
            border: 1px solid rgba(201,168,76,0.3);
            color: var(--gold);
            text-decoration: none;
            font-size: 0.85rem;
            transition: all 0.3s;
            margin-top: 20px;
        }
        .btn-back:hover {
            background: var(--gold);
            color: var(--ink);
            border-color: var(--gold);
        }

        @media (max-width: 768px) {
            .page-banner { height: 220px; }
            .help-grid { grid-template-columns: repeat(2, 1fr); }
            .contact-grid { grid-template-columns: 1fr 1fr; }
            .faq-preview { padding: 20px; }
        }
        @media (max-width: 480px) {
            .page-banner { height: 180px; }
            .page-banner h1 { font-size: 1.8rem; }
            .help-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .help-card { padding: 16px; }
            .contact-grid { grid-template-columns: 1fr; }
            .faq-item a { font-size: 0.8rem; }
        }
    </style>
</head>
<body>

    <?php include "includes/navbar.php"; ?>

    <!-- Banner -->
    <section class="page-banner">
        <div class="overlay">
            <h1>Help Center</h1>
            <nav class="breadcrumb justify-content-center">
                <a href="index.php">Home</a>
                <span class="mx-2"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></span>
                <span class="active">Help Center</span>
            </nav>
        </div>
    </section>

    <!-- Help Content -->
    <section class="help-section">
        <div class="container">

            <!-- Quick Help Cards -->
            <div class="help-grid">
                <a href="faq.php" class="help-card">
                    <div class="icon"><i class="fas fa-question-circle"></i></div>
                    <h4>FAQ</h4>
                    <p>Frequently asked questions</p>
                </a>
                <a href="contact.php" class="help-card">
                    <div class="icon"><i class="fas fa-envelope"></i></div>
                    <h4>Contact Us</h4>
                    <p>Send us a message</p>
                </a>
                <a href="blog.php" class="help-card">
                    <div class="icon"><i class="fas fa-newspaper"></i></div>
                    <h4>Travel Blog</h4>
                    <p>Tips & travel guides</p>
                </a>
                <a href="cancellation.php" class="help-card">
                    <div class="icon"><i class="fas fa-file-invoice"></i></div>
                    <h4>Cancellation Policy</h4>
                    <p>Refund and cancellation info</p>
                </a>
                <a href="privacy.php" class="help-card">
                    <div class="icon"><i class="fas fa-shield-alt"></i></div>
                    <h4>Privacy Policy</h4>
                    <p>How we protect your data</p>
                </a>
                <a href="terms.php" class="help-card">
                    <div class="icon"><i class="fas fa-gavel"></i></div>
                    <h4>Terms & Conditions</h4>
                    <p>Terms of service</p>
                </a>
            </div>

            <!-- Contact Information -->
            <h3 style="font-family: 'Playfair Display', serif; font-size: 1.3rem; font-weight: 700; color: var(--gold); margin-bottom: 18px;">
                <i class="fas fa-headset" style="color: var(--gold); margin-right: 10px;"></i> Get in Touch
            </h3>
            <div class="contact-grid">
                <div class="contact-card">
                    <div class="icon-circle"><i class="fas fa-phone-alt"></i></div>
                    <h5>Phone</h5>
                    <p>Call us for immediate help</p>
                    <a href="tel:+919830276259">+91 98302 76259</a>
                </div>
                <div class="contact-card">
                    <div class="icon-circle"><i class="fab fa-whatsapp" style="color: #25d366;"></i></div>
                    <h5>WhatsApp</h5>
                    <p>Chat with us on WhatsApp</p>
                    <a href="https://wa.me/919830276259">+91 98302 76259</a>
                </div>
                <div class="contact-card">
                    <div class="icon-circle"><i class="fas fa-envelope"></i></div>
                    <h5>Email</h5>
                    <p>Send us your queries</p>
                    <a href="mailto:friendstravels234@gmail.com">friendstravels234@gmail.com</a>
                </div>
                <div class="contact-card">
                    <div class="icon-circle"><i class="fas fa-map-marker-alt"></i></div>
                    <h5>Office</h5>
                    <p>Visit us in person</p>
                    <a href="#" style="font-size:0.7rem;">Behala Chowrasta, Kolkata</a>
                </div>
            </div>

            <!-- FAQ Preview -->
            <div class="faq-preview">
                <h3><i class="fas fa-question-circle"></i> Frequently Asked Questions</h3>
                
                <div class="faq-item">
                    <a href="faq.php#booking">
                        <span class="q-icon">Q.</span> How do I book a tour package?
                    </a>
                    <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                </div>
                <div class="faq-item">
                    <a href="faq.php#payment">
                        <span class="q-icon">Q.</span> What payment methods do you accept?
                    </a>
                    <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                </div>
                <div class="faq-item">
                    <a href="faq.php#cancellation">
                        <span class="q-icon">Q.</span> What is your cancellation policy?
                    </a>
                    <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                </div>
                <div class="faq-item">
                    <a href="faq.php#travel">
                        <span class="q-icon">Q.</span> What is included in the tour package?
                    </a>
                    <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                </div>
                <div class="faq-item">
                    <a href="faq.php#general">
                        <span class="q-icon">Q.</span> What are your office hours?
                    </a>
                    <span class="arrow"><i class="fas fa-chevron-right"></i></span>
                </div>
                
                <a href="faq.php" class="btn-view-all">
                    View All FAQs <i class="fas fa-arrow-right"></i>
                </a>
            </div>

            <!-- Quick Help Message -->
            <div style="background: rgba(185,28,46,0.08); border: 1px solid rgba(201,168,76,0.15); border-radius: 14px; padding: 24px 28px; text-align: center;">
                <i class="fas fa-headset" style="font-size: 2rem; color: var(--gold); display: block; margin-bottom: 12px;"></i>
                <h4 style="color: var(--snow); font-weight: 700; margin-bottom: 6px;">Need immediate assistance?</h4>
                <p style="color: var(--dim); font-size: 0.9rem; margin-bottom: 16px;">Our team is available 24/7 to help you with any queries or issues.</p>
                <a href="tel:+919830276259" class="btn-view-all" style="display: inline-flex; width: auto;">
                    <i class="fas fa-phone-alt"></i> Call Now
                </a>
                <a href="contact.php" class="btn-view-all" style="display: inline-flex; width: auto; background: transparent; border: 1px solid rgba(201,168,76,0.3); color: var(--gold); margin-left: 10px;">
                    <i class="fas fa-envelope"></i> Send Message
                </a>
            </div>

            <a href="index.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Back to Home
            </a>

        </div>
    </section>

    <?php include "includes/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
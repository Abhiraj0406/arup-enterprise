<?php
// privacy.php - Privacy Policy Page for Friends Travels
session_start();
require_once "includes/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Privacy Policy | Friends Travels</title>
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

        .privacy-section {
            padding: 60px 0 80px;
            background: var(--ink);
        }
        .privacy-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .privacy-card {
            background: var(--surface);
            border: 1px solid rgba(185,28,46,0.15);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .privacy-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--gold);
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(201,168,76,0.15);
        }
        .privacy-card h2:first-of-type {
            margin-top: 0;
        }
        .privacy-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gold-light);
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .privacy-card p {
            color: var(--mist);
            line-height: 1.8;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        .privacy-card ul {
            list-style: none;
            padding: 0;
            margin-bottom: 15px;
        }
        .privacy-card ul li {
            color: var(--mist);
            padding: 8px 0;
            padding-left: 28px;
            position: relative;
            line-height: 1.6;
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .privacy-card ul li:last-child {
            border-bottom: none;
        }
        .privacy-card ul li::before {
            content: '›';
            position: absolute;
            left: 6px;
            color: var(--gold);
            font-size: 1.2rem;
            font-weight: 700;
        }
        .privacy-card .highlight-box {
            background: rgba(185,28,46,0.08);
            border-left: 3px solid var(--gold);
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .privacy-card .highlight-box p {
            margin: 0;
            color: var(--snow);
            font-weight: 500;
        }
        .privacy-card .highlight-box i {
            color: var(--gold);
            margin-right: 10px;
        }
        .privacy-card .last-updated {
            color: var(--dim);
            font-size: 0.8rem;
            text-align: right;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .privacy-card .last-updated i {
            color: var(--gold);
            margin-right: 6px;
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
            .privacy-card { padding: 25px; }
            .page-banner { height: 220px; }
            .privacy-card h2 { font-size: 1.3rem; }
        }
        @media (max-width: 480px) {
            .privacy-card { padding: 18px; }
            .page-banner { height: 180px; }
            .page-banner h1 { font-size: 1.8rem; }
            .privacy-card h2 { font-size: 1.1rem; }
            .privacy-card p, .privacy-card ul li { font-size: 0.85rem; }
        }
    </style>
</head>
<body>

    <?php include "includes/navbar.php"; ?>

    <!-- Banner -->
    <section class="page-banner">
        <div class="overlay">
            <h1>Privacy Policy</h1>
            <nav class="breadcrumb justify-content-center">
                <a href="index.php">Home</a>
                <span class="mx-2"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></span>
                <span class="active">Privacy Policy</span>
            </nav>
        </div>
    </section>

    <!-- Privacy Content -->
    <section class="privacy-section">
        <div class="container">
            <div class="privacy-container">
                <div class="privacy-card">

                    <div class="highlight-box">
                        <p><i class="fas fa-shield-alt"></i> We take your privacy seriously. This policy explains how we collect, use, and protect your personal information.</p>
                    </div>

                    <h2>1. Information We Collect</h2>
                    <p>Friends Travels collects information to provide better services to our customers. We collect information in the following ways:</p>
                    <ul>
                        <li><strong>Personal Information:</strong> Name, email address, phone number, and address provided during booking.</li>
                        <li><strong>Payment Information:</strong> Transaction details for tour package bookings.</li>
                        <li><strong>Travel Preferences:</strong> Destination choices, travel dates, and special requests.</li>
                        <li><strong>Website Usage:</strong> Cookies and analytics data to improve your experience.</li>
                    </ul>

                    <h2>2. How We Use Your Information</h2>
                    <ul>
                        <li>To process and confirm your tour package bookings.</li>
                        <li>To communicate with you about your travel plans.</li>
                        <li>To send you promotional offers and travel updates (only with your consent).</li>
                        <li>To improve our website and customer service.</li>
                        <li>To comply with legal obligations.</li>
                    </ul>

                    <h2>3. Information Sharing</h2>
                    <p>Friends Travels does not sell, trade, or rent your personal information to third parties. However, we may share your information with:</p>
                    <ul>
                        <li><strong>Service Providers:</strong> Hotels, transport companies, and tour operators to fulfill your booking.</li>
                        <li><strong>Legal Authorities:</strong> When required by law or to protect our rights.</li>
                        <li><strong>Payment Processors:</strong> To securely handle your payments.</li>
                    </ul>

                    <h2>4. Data Security</h2>
                    <p>We implement appropriate security measures to protect your personal information. This includes:</p>
                    <ul>
                        <li>SSL encryption for all transactions.</li>
                        <li>Secure server infrastructure.</li>
                        <li>Access controls and authentication measures.</li>
                        <li>Regular security audits.</li>
                    </ul>

                    <h2>5. Cookies</h2>
                    <p>Friends Travels uses cookies to enhance your browsing experience. Cookies help us:</p>
                    <ul>
                        <li>Remember your preferences.</li>
                        <li>Analyze website traffic.</li>
                        <li>Provide personalized content.</li>
                        <li>Improve site performance.</li>
                    </ul>
                    <p>You can control cookie preferences in your browser settings.</p>

                    <h2>6. Your Rights</h2>
                    <p>You have the right to:</p>
                    <ul>
                        <li>Access the personal information we hold about you.</li>
                        <li>Request correction of inaccurate information.</li>
                        <li>Request deletion of your information (subject to legal requirements).</li>
                        <li>Opt-out of marketing communications.</li>
                        <li>Withdraw consent at any time.</li>
                    </ul>

                    <h2>7. Third-Party Links</h2>
                    <p>Our website may contain links to third-party websites. We are not responsible for the privacy practices of these sites. We encourage you to review their privacy policies.</p>

                    <h2>8. Children's Privacy</h2>
                    <p>Friends Travels does not knowingly collect personal information from children under 13 years of age. If you believe we have collected such information, please contact us immediately.</p>

                    <h2>9. Changes to This Policy</h2>
                    <p>We may update this privacy policy from time to time. We will notify you of any changes by posting the new policy on this page. We encourage you to review this policy periodically.</p>

                    <h2>10. Contact Us</h2>
                    <p>If you have any questions about this privacy policy or our data practices, please contact us:</p>
                    <ul>
                        <li><strong>Email:</strong> <a href="mailto:friendstravels234@gmail.com" style="color: var(--gold); text-decoration: none;">friendstravels234@gmail.com</a></li>
                        <li><strong>Phone:</strong> <a href="tel:+919830276259" style="color: var(--gold); text-decoration: none;">+91 98302 76259</a></li>
                        <li><strong>Address:</strong> Room-2 Ground Floor, Commercial Complex Building, Behala Chowrasta, Opp. Vivekananda Women's College, Kolkata-700034</li>
                    </ul>

                    <div class="last-updated">
                        <i class="fas fa-clock"></i> Last Updated: <?php echo date('F d, Y'); ?>
                    </div>

                    <a href="index.php" class="btn-back">
                        <i class="fas fa-arrow-left"></i> Back to Home
                    </a>

                </div>
            </div>
        </div>
    </section>

    <?php include "includes/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
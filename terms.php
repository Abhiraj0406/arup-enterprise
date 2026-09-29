<?php
// terms.php - Terms & Conditions Page for Friends Travels
session_start();
require_once "includes/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terms & Conditions | Friends Travels</title>
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

        .terms-section {
            padding: 60px 0 80px;
            background: var(--ink);
        }
        .terms-container {
            max-width: 900px;
            margin: 0 auto;
        }
        .terms-card {
            background: var(--surface);
            border: 1px solid rgba(185,28,46,0.15);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
        }
        .terms-card h2 {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            font-weight: 700;
            color: var(--gold);
            margin-top: 30px;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 1px solid rgba(201,168,76,0.15);
        }
        .terms-card h2:first-of-type {
            margin-top: 0;
        }
        .terms-card h3 {
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--gold-light);
            margin-top: 20px;
            margin-bottom: 10px;
        }
        .terms-card p {
            color: var(--mist);
            line-height: 1.8;
            margin-bottom: 15px;
            font-size: 0.95rem;
        }
        .terms-card ul {
            list-style: none;
            padding: 0;
            margin-bottom: 15px;
        }
        .terms-card ul li {
            color: var(--mist);
            padding: 8px 0;
            padding-left: 28px;
            position: relative;
            line-height: 1.6;
            font-size: 0.95rem;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .terms-card ul li:last-child {
            border-bottom: none;
        }
        .terms-card ul li::before {
            content: '›';
            position: absolute;
            left: 6px;
            color: var(--gold);
            font-size: 1.2rem;
            font-weight: 700;
        }
        .terms-card .highlight-box {
            background: rgba(185,28,46,0.08);
            border-left: 3px solid var(--gold);
            padding: 15px 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .terms-card .highlight-box p {
            margin: 0;
            color: var(--snow);
            font-weight: 500;
        }
        .terms-card .highlight-box i {
            color: var(--gold);
            margin-right: 10px;
        }
        .terms-card .last-updated {
            color: var(--dim);
            font-size: 0.8rem;
            text-align: right;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }
        .terms-card .last-updated i {
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
            .terms-card { padding: 25px; }
            .page-banner { height: 220px; }
            .terms-card h2 { font-size: 1.3rem; }
        }
        @media (max-width: 480px) {
            .terms-card { padding: 18px; }
            .page-banner { height: 180px; }
            .page-banner h1 { font-size: 1.8rem; }
            .terms-card h2 { font-size: 1.1rem; }
            .terms-card p, .terms-card ul li { font-size: 0.85rem; }
        }
    </style>
</head>
<body>

    <?php include "includes/navbar.php"; ?>

    <!-- Banner -->
    <section class="page-banner">
        <div class="overlay">
            <h1>Terms &amp; Conditions</h1>
            <nav class="breadcrumb justify-content-center">
                <a href="index.php">Home</a>
                <span class="mx-2"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></span>
                <span class="active">Terms &amp; Conditions</span>
            </nav>
        </div>
    </section>

    <!-- Terms Content -->
    <section class="terms-section">
        <div class="container">
            <div class="terms-container">
                <div class="terms-card">

                    <div class="highlight-box">
                        <p><i class="fas fa-gavel"></i> Please read these Terms &amp; Conditions carefully before using our services. By using our website and booking services, you agree to these terms.</p>
                    </div>

                    <h2>1. Acceptance of Terms</h2>
                    <p>By accessing and using the Friends Travels website, you agree to be bound by these Terms &amp; Conditions. If you do not agree with any part of these terms, please do not use our services.</p>

                    <h2>2. Booking and Payment</h2>
                    <ul>
                        <li><strong>Booking Confirmation:</strong> Bookings are confirmed only upon receipt of full payment or advance payment as specified.</li>
                        <li><strong>Payment Methods:</strong> We accept bank transfers, UPI, and other payment methods as displayed during checkout.</li>
                        <li><strong>Price Validity:</strong> Prices are subject to change without notice. The price at the time of booking is final.</li>
                        <li><strong>Booking Modifications:</strong> Any changes to bookings are subject to availability and may incur additional charges.</li>
                    </ul>

                    <h2>3. Cancellation Policy</h2>
                    <ul>
                        <li><strong>30+ Days Before Travel:</strong> Full refund minus 10% processing fee.</li>
                        <li><strong>15-30 Days Before Travel:</strong> 50% refund of total booking amount.</li>
                        <li><strong>7-14 Days Before Travel:</strong> 25% refund of total booking amount.</li>
                        <li><strong>Less Than 7 Days Before Travel:</strong> No refund will be provided.</li>
                        <li><strong>No-Show:</strong> No refund for no-show or unused services.</li>
                    </ul>
                    <div class="highlight-box">
                        <p><i class="fas fa-info-circle"></i> Cancellation charges are calculated on the total package price. Any bank transaction charges will be deducted.</p>
                    </div>

                    <h2>4. Travel Documents and Requirements</h2>
                    <ul>
                        <li>All travelers must carry valid government-issued identification (Aadhaar, Passport, Driver's License).</li>
                        <li>For international travel, valid passport and visa are the responsibility of the traveler.</li>
                        <li>Friends Travels is not responsible for any travel delays or denials due to improper documentation.</li>
                        <li>Minors traveling must have proper consent from parents/guardians.</li>
                    </ul>

                    <h2>5. Travel Insurance</h2>
                    <p>We strongly recommend all travelers purchase comprehensive travel insurance before departure. Travel insurance covers:</p>
                    <ul>
                        <li>Medical emergencies and hospitalization.</li>
                        <li>Baggage loss or delay.</li>
                        <li>Flight cancellations or delays.</li>
                        <li>Personal liability and accidents.</li>
                    </ul>

                    <h2>6. Health and Safety</h2>
                    <ul>
                        <li>Travelers are responsible for their own health and fitness for the tour.</li>
                        <li>Please disclose any medical conditions or special requirements at the time of booking.</li>
                        <li>Friends Travels reserves the right to refuse service to anyone deemed unfit for travel.</li>
                        <li>COVID-19 protocols must be followed as per government guidelines.</li>
                    </ul>

                    <h2>7. Changes and Cancellations by Friends Travels</h2>
                    <p>Friends Travels reserves the right to modify or cancel tours due to:</p>
                    <ul>
                        <li>Natural disasters, political instability, or other force majeure events.</li>
                        <li>Insufficient bookings for a tour departure.</li>
                        <li>Technical or operational reasons.</li>
                    </ul>
                    <p>In such cases, a full refund or alternative arrangements will be offered.</p>

                    <h2>8. Liability</h2>
                    <ul>
                        <li>Friends Travels acts as a coordinator for travel services and is not liable for the performance of third-party service providers.</li>
                        <li>We are not responsible for personal injury, loss, or damage to property during the tour.</li>
                        <li>Our liability is limited to the total amount paid for the booking.</li>
                        <li>Any claims must be reported within 7 days of the tour completion.</li>
                    </ul>

                    <h2>9. Code of Conduct</h2>
                    <ul>
                        <li>Travelers must respect local customs, traditions, and laws of the destinations visited.</li>
                        <li>Any illegal activity or disruptive behavior may result in termination of the tour without refund.</li>
                        <li>Environmental responsibility is expected—do not litter or damage natural sites.</li>
                        <li>Follow the instructions of the tour guide and local authorities.</li>
                    </ul>

                    <h2>10. Intellectual Property</h2>
                    <p>All content on the Friends Travels website, including text, images, logos, and designs, is the property of Friends Travels and protected by copyright laws. Unauthorized use is prohibited.</p>

                    <h2>11. Privacy and Data Protection</h2>
                    <p>Your privacy is important to us. Please refer to our <a href="privacy.php" style="color: var(--gold); text-decoration: none;">Privacy Policy</a> for details on how we collect, use, and protect your personal information.</p>

                    <h2>12. Governing Law</h2>
                    <p>These Terms &amp; Conditions are governed by the laws of India. Any disputes shall be subject to the exclusive jurisdiction of the courts in Kolkata, West Bengal.</p>

                    <h2>13. Changes to Terms</h2>
                    <p>Friends Travels reserves the right to update these Terms &amp; Conditions at any time. The latest version will always be available on this page. Your continued use of our services constitutes acceptance of the updated terms.</p>

                    <h2>14. Contact Us</h2>
                    <p>If you have any questions about these Terms &amp; Conditions, please contact us:</p>
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
<?php
// faq.php - Frequently Asked Questions Page for Friends Travels
session_start();
require_once "includes/db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FAQ - Frequently Asked Questions | Friends Travels</title>
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

        .faq-section {
            padding: 60px 0 80px;
            background: var(--ink);
        }
        .faq-container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Category Filter */
        .faq-categories {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 30px;
            justify-content: center;
        }
        .faq-category-btn {
            padding: 8px 20px;
            border-radius: 30px;
            border: 1px solid rgba(201,168,76,0.3);
            background: transparent;
            color: var(--dim);
            font-size: 0.8rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        .faq-category-btn:hover {
            border-color: var(--gold);
            color: var(--gold);
        }
        .faq-category-btn.active {
            background: linear-gradient(135deg, var(--crimson), var(--crimson-dark));
            color: var(--white);
            border-color: var(--crimson);
        }

        /* FAQ Accordion */
        .faq-accordion {
            background: var(--surface);
            border: 1px solid rgba(185,28,46,0.15);
            border-radius: 16px;
            overflow: hidden;
            margin-bottom: 16px;
        }
        .faq-accordion .faq-header {
            padding: 18px 24px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .faq-accordion .faq-header:hover {
            background: rgba(185,28,46,0.05);
        }
        .faq-accordion .faq-header h4 {
            font-size: 1rem;
            font-weight: 600;
            color: var(--snow);
            margin: 0;
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .faq-accordion .faq-header h4 .q-icon {
            color: var(--gold);
            font-weight: 700;
            font-size: 0.9rem;
        }
        .faq-accordion .faq-header .toggle-icon {
            color: var(--gold);
            font-size: 1.2rem;
            transition: transform 0.3s;
        }
        .faq-accordion .faq-header .toggle-icon.open {
            transform: rotate(180deg);
        }
        .faq-accordion .faq-body {
            padding: 0 24px;
            max-height: 0;
            overflow: hidden;
            transition: all 0.4s ease;
        }
        .faq-accordion .faq-body.open {
            padding: 18px 24px;
            max-height: 500px;
        }
        .faq-accordion .faq-body p {
            color: var(--mist);
            line-height: 1.8;
            margin: 0;
            font-size: 0.95rem;
        }
        .faq-accordion .faq-body ul {
            list-style: none;
            padding: 0;
            margin: 10px 0 0;
        }
        .faq-accordion .faq-body ul li {
            color: var(--mist);
            padding: 5px 0 5px 22px;
            position: relative;
            line-height: 1.6;
            font-size: 0.9rem;
        }
        .faq-accordion .faq-body ul li::before {
            content: '•';
            position: absolute;
            left: 4px;
            color: var(--gold);
            font-weight: 700;
        }

        .faq-search {
            margin-bottom: 30px;
        }
        .faq-search input {
            width: 100%;
            padding: 14px 20px;
            background: var(--surface);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 12px;
            color: var(--snow);
            font-size: 0.95rem;
            transition: all 0.3s;
        }
        .faq-search input:focus {
            outline: none;
            border-color: var(--gold);
            box-shadow: 0 0 0 3px rgba(201,168,76,0.1);
        }
        .faq-search input::placeholder {
            color: var(--dim);
        }

        .faq-stats {
            text-align: center;
            color: var(--dim);
            font-size: 0.85rem;
            margin-top: 30px;
        }
        .faq-stats span {
            color: var(--gold);
            font-weight: 700;
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
            .faq-accordion .faq-header { padding: 14px 18px; }
            .faq-accordion .faq-header h4 { font-size: 0.9rem; }
            .faq-accordion .faq-body.open { padding: 14px 18px; }
            .faq-category-btn { padding: 6px 14px; font-size: 0.7rem; }
        }
        @media (max-width: 480px) {
            .page-banner { height: 180px; }
            .page-banner h1 { font-size: 1.8rem; }
            .faq-accordion .faq-header h4 { font-size: 0.8rem; }
            .faq-accordion .faq-body p { font-size: 0.85rem; }
        }
    </style>
</head>
<body>

    <?php include "includes/navbar.php"; ?>

    <!-- Banner -->
    <section class="page-banner">
        <div class="overlay">
            <h1>Frequently Asked Questions</h1>
            <nav class="breadcrumb justify-content-center">
                <a href="index.php">Home</a>
                <span class="mx-2"><i class="fas fa-chevron-right" style="font-size: 0.7rem;"></i></span>
                <span class="active">FAQ</span>
            </nav>
        </div>
    </section>

    <!-- FAQ Content -->
    <section class="faq-section">
        <div class="container">
            <div class="faq-container">

                <!-- Search -->
                <div class="faq-search">
                    <input type="text" id="faqSearch" placeholder="Search for answers..." onkeyup="searchFAQ()">
                </div>

                <!-- Categories -->
                <div class="faq-categories">
                    <button class="faq-category-btn active" data-category="all" onclick="filterCategory('all', this)">All</button>
                    <button class="faq-category-btn" data-category="booking" onclick="filterCategory('booking', this)">Booking</button>
                    <button class="faq-category-btn" data-category="payment" onclick="filterCategory('payment', this)">Payment</button>
                    <button class="faq-category-btn" data-category="cancellation" onclick="filterCategory('cancellation', this)">Cancellation</button>
                    <button class="faq-category-btn" data-category="travel" onclick="filterCategory('travel', this)">Travel</button>
                    <button class="faq-category-btn" data-category="general" onclick="filterCategory('general', this)">General</button>
                </div>

                <!-- FAQ List -->
                <div id="faqList">

                    <!-- ========== BOOKING FAQS ========== -->
                    <div class="faq-accordion" data-category="booking">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> How do I book a tour package?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>Booking a tour package with Friends Travels is easy! You can:</p>
                            <ul>
                                <li>Visit our <a href="packages.php" style="color: var(--gold); text-decoration: none;">Packages</a> page and select your desired tour.</li>
                                <li>Fill in the booking form with your details.</li>
                                <li>Make payment via bank transfer or UPI.</li>
                                <li>Receive booking confirmation via email within 24 hours.</li>
                            </ul>
                            <p style="margin-top:10px;">Alternatively, you can call us at <a href="tel:+919830276259" style="color: var(--gold); text-decoration: none;">+91 98302 76259</a> or visit our office.</p>
                        </div>
                    </div>

                    <div class="faq-accordion" data-category="booking">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> What documents do I need for booking?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>You will need the following documents for booking:</p>
                            <ul>
                                <li>Valid government-issued ID (Aadhaar, Passport, Driver's License).</li>
                                <li>Contact details (phone and email).</li>
                                <li>Travel date and preferred package details.</li>
                                <li>For international travel, passport and visa are required.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-accordion" data-category="booking">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> Can I customize my tour package?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>Yes! Friends Travels offers customized tour packages based on your preferences.</p>
                            <ul>
                                <li>Choose your destinations and duration.</li>
                                <li>Select hotel category and preferences.</li>
                                <li>Add optional activities and excursions.</li>
                                <li>Customize group size and travel dates.</li>
                            </ul>
                            <p style="margin-top:10px;">Contact our travel experts at <a href="mailto:friendstravels234@gmail.com" style="color: var(--gold); text-decoration: none;">friendstravels234@gmail.com</a> for a custom quote.</p>
                        </div>
                    </div>

                    <!-- ========== PAYMENT FAQS ========== -->
                    <div class="faq-accordion" data-category="payment">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> What payment methods do you accept?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>We accept the following payment methods:</p>
                            <ul>
                                <li><strong>Bank Transfer:</strong> Direct bank transfer to our account.</li>
                                <li><strong>UPI:</strong> Google Pay, PhonePe, Paytm, and other UPI apps.</li>
                                <li><strong>QR Code:</strong> Scan our QR code for instant payment.</li>
                                <li><strong>Cash:</strong> At our office in Kolkata.</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-accordion" data-category="payment">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> Is there an advance payment required?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>Yes, we require an advance payment to confirm your booking. You can choose:</p>
                            <ul>
                                <li><strong>30% Advance:</strong> Pay 30% upfront and the remaining amount before departure.</li>
                                <li><strong>Full Payment:</strong> Pay the entire amount at the time of booking.</li>
                            </ul>
                            <p style="margin-top:10px;">The remaining amount (if any) must be paid at least 7 days before the travel date.</p>
                        </div>
                    </div>

                    <!-- ========== CANCELLATION FAQS ========== -->
                    <div class="faq-accordion" data-category="cancellation">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> What is your cancellation policy?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>Our cancellation policy varies based on when you cancel:</p>
                            <ul>
                                <li><strong>60+ Days:</strong> 90% refund (minus 10% processing fee).</li>
                                <li><strong>30-59 Days:</strong> 75% refund.</li>
                                <li><strong>15-29 Days:</strong> 50% refund.</li>
                                <li><strong>7-14 Days:</strong> 25% refund.</li>
                                <li><strong>Less than 7 Days:</strong> No refund.</li>
                            </ul>
                            <p style="margin-top:10px;">Please see our <a href="cancellation.php" style="color: var(--gold); text-decoration: none;">Cancellation Policy</a> for more details.</p>
                        </div>
                    </div>

                    <div class="faq-accordion" data-category="cancellation">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> How do I cancel my booking?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>To cancel your booking, please contact us through:</p>
                            <ul>
                                <li><strong>Email:</strong> <a href="mailto:friendstravels234@gmail.com" style="color: var(--gold); text-decoration: none;">friendstravels234@gmail.com</a></li>
                                <li><strong>Phone:</strong> <a href="tel:+919830276259" style="color: var(--gold); text-decoration: none;">+91 98302 76259</a></li>
                                <li><strong>WhatsApp:</strong> <a href="https://wa.me/919830276259" style="color: var(--gold); text-decoration: none;">+91 98302 76259</a></li>
                            </ul>
                            <p style="margin-top:10px;">Please have your booking ID ready for faster processing.</p>
                        </div>
                    </div>

                    <!-- ========== TRAVEL FAQS ========== -->
                    <div class="faq-accordion" data-category="travel">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> What is included in the tour package?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>Our tour packages generally include:</p>
                            <ul>
                                <li><strong>Accommodation:</strong> Premium hotels and resorts.</li>
                                <li><strong>Meals:</strong> Breakfast and dinner daily.</li>
                                <li><strong>Transport:</strong> All transfers by private vehicle.</li>
                                <li><strong>Sightseeing:</strong> Guided tours as per itinerary.</li>
                                <li><strong>Guide:</strong> Expert local guide.</li>
                            </ul>
                            <p style="margin-top:10px;">Please check the package details for specific inclusions.</p>
                        </div>
                    </div>

                    <div class="faq-accordion" data-category="travel">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> Do I need travel insurance?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>We strongly recommend purchasing travel insurance before your trip. Travel insurance covers:</p>
                            <ul>
                                <li>Medical emergencies and hospitalization.</li>
                                <li>Flight cancellations or delays.</li>
                                <li>Baggage loss or theft.</li>
                                <li>Personal liability and accidents.</li>
                            </ul>
                            <p style="margin-top:10px;">Contact us for travel insurance recommendations.</p>
                        </div>
                    </div>

                    <div class="faq-accordion" data-category="travel">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> What should I pack for the tour?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>We recommend packing the following items:</p>
                            <ul>
                                <li><strong>Clothing:</strong> Weather-appropriate clothes (check destination weather).</li>
                                <li><strong>Footwear:</strong> Comfortable walking shoes and sandals.</li>
                                <li><strong>Essentials:</strong> Sunscreen, hat, sunglasses, and insect repellent.</li>
                                <li><strong>Documents:</strong> ID proof, booking confirmation, and travel insurance.</li>
                                <li><strong>Medicines:</strong> Personal medications and basic first-aid kit.</li>
                            </ul>
                            <p style="margin-top:10px;">A detailed packing list will be sent after booking confirmation.</p>
                        </div>
                    </div>

                    <!-- ========== GENERAL FAQS ========== -->
                    <div class="faq-accordion" data-category="general">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> What are your office hours?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>Our office hours are:</p>
                            <ul>
                                <li><strong>Monday - Friday:</strong> 9:00 AM - 7:00 PM</li>
                                <li><strong>Saturday - Sunday:</strong> 9:00 AM - 8:00 PM</li>
                                <li><strong>Holidays:</strong> 9:00 AM - 8:00 PM</li>
                            </ul>
                            <p style="margin-top:10px;">You can also reach us via phone or WhatsApp outside these hours.</p>
                        </div>
                    </div>

                    <div class="faq-accordion" data-category="general">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> How can I contact Friends Travels?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>You can contact us through the following channels:</p>
                            <ul>
                                <li><strong>Phone:</strong> <a href="tel:+919830276259" style="color: var(--gold); text-decoration: none;">+91 98302 76259</a></li>
                                <li><strong>WhatsApp:</strong> <a href="https://wa.me/919830276259" style="color: var(--gold); text-decoration: none;">+91 98302 76259</a></li>
                                <li><strong>Email:</strong> <a href="mailto:friendstravels234@gmail.com" style="color: var(--gold); text-decoration: none;">friendstravels234@gmail.com</a></li>
                                <li><strong>Address:</strong> Room-2 Ground Floor, Commercial Complex Building, Behala Chowrasta, Opp. Vivekananda Women's College, Kolkata-700034</li>
                            </ul>
                        </div>
                    </div>

                    <div class="faq-accordion" data-category="general">
                        <div class="faq-header" onclick="toggleFAQ(this)">
                            <h4><span class="q-icon">Q.</span> Is Friends Travels a registered company?</h4>
                            <span class="toggle-icon"><i class="fas fa-chevron-down"></i></span>
                        </div>
                        <div class="faq-body">
                            <p>Yes, Friends Travels is a registered travel agency operating since 2007. We are:</p>
                            <ul>
                                <li>Licensed tour operator.</li>
                                <li>Registered with local tourism authorities.</li>
                                <li>Committed to providing safe and reliable travel services.</li>
                                <li>Trusted by thousands of happy travelers.</li>
                            </ul>
                        </div>
                    </div>

                </div>

                <!-- Stats -->
                <div class="faq-stats">
                    Found <span id="faqCount"><?php echo count($faqs ?? []); ?></span> answers to your questions
                </div>

                <a href="index.php" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Back to Home
                </a>

            </div>
        </div>
    </section>

    <?php include "includes/footer.php"; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle FAQ
        function toggleFAQ(element) {
            const body = element.nextElementSibling;
            const icon = element.querySelector('.toggle-icon i');
            
            if (body.classList.contains('open')) {
                body.classList.remove('open');
                icon.className = 'fas fa-chevron-down';
            } else {
                // Close all others
                document.querySelectorAll('.faq-body').forEach(b => b.classList.remove('open'));
                document.querySelectorAll('.toggle-icon i').forEach(i => i.className = 'fas fa-chevron-down');
                
                body.classList.add('open');
                icon.className = 'fas fa-chevron-up';
            }
        }

        // Search FAQ
        function searchFAQ() {
            const query = document.getElementById('faqSearch').value.toLowerCase();
            const faqs = document.querySelectorAll('.faq-accordion');
            let visible = 0;
            
            faqs.forEach(faq => {
                const question = faq.querySelector('.faq-header h4').innerText.toLowerCase();
                const answer = faq.querySelector('.faq-body').innerText.toLowerCase();
                
                if (question.includes(query) || answer.includes(query)) {
                    faq.style.display = 'block';
                    visible++;
                } else {
                    faq.style.display = 'none';
                }
            });
            
            document.getElementById('faqCount').innerText = visible;
        }

        // Filter by Category
        function filterCategory(category, button) {
            // Update active button
            document.querySelectorAll('.faq-category-btn').forEach(btn => btn.classList.remove('active'));
            button.classList.add('active');
            
            const faqs = document.querySelectorAll('.faq-accordion');
            let visible = 0;
            
            faqs.forEach(faq => {
                if (category === 'all') {
                    faq.style.display = 'block';
                    visible++;
                } else if (faq.dataset.category === category) {
                    faq.style.display = 'block';
                    visible++;
                } else {
                    faq.style.display = 'none';
                }
            });
            
            document.getElementById('faqCount').innerText = visible;
        }

        // Auto-open first FAQ
        document.addEventListener('DOMContentLoaded', function() {
            const firstFAQ = document.querySelector('.faq-accordion');
            if (firstFAQ) {
                const header = firstFAQ.querySelector('.faq-header');
                setTimeout(() => toggleFAQ(header), 500);
            }
        });
    </script>
</body>
</html>
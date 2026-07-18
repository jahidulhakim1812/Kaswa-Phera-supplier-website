<?php
require_once __DIR__ . '/config/db.php';
$page_title = 'About Us';
require_once __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb-band">
    <div class="container"><a href="index.php">Home</a> / <span>About Us</span></div>
</div>

<section class="section">
    <div class="container split">
        <div>
            <span class="eyebrow">About KASWA Tech</span>
            <h2>Local manufacturing, engineered to industrial standards</h2>
            <p><?= clean(setting('about_text')) ?></p>
            <p>Our team works across two facilities in Dhaka and Gazipur to design, fabricate, test and dispatch equipment for chemical manufacturers, pharmaceutical producers and analytical laboratories throughout Bangladesh.</p>

            <div class="address-cards">
                <div class="address-card">
                    <h4>Corporate Office</h4>
                    <p><?= clean(setting('corporate_address')) ?></p>
                </div>
                <div class="address-card">
                    <h4>Registered Office</h4>
                    <p><?= clean(setting('registered_address')) ?></p>
                </div>
            </div>
        </div>
        <div>
            <!-- Logo inside a circle – modern, clean, and responsive -->
            <div style="
                width: 100%;
                aspect-ratio: 1 / 1;
                border-radius: 50%;
                background: linear-gradient(145deg, var(--forest), var(--leaf));
                display: flex;
                align-items: center;
                justify-content: center;
                overflow: hidden;
                box-shadow: 0 12px 32px rgba(20, 83, 45, 0.25);
                margin: 0 auto;
                max-width: 400px;
            ">
                <img src="assets/images/logo.png" alt="KASWA Tech" style="
                    max-width: 70%;
                    max-height: 70%;
                    object-fit: contain;
                    filter: drop-shadow(0 4px 8px rgba(0,0,0,0.15));
                ">
            </div>
        </div>
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Our Approach</span>
            <h2>What drives how we build equipment</h2>
        </div>
        <div class="feature-grid">
            <div class="feature-item">
                <span class="num">01</span>
                <h4>Process First</h4>
                <p>We size and design equipment around your actual batch size, media and throughput target.</p>
            </div>
            <div class="feature-item">
                <span class="num">02</span>
                <h4>Material Integrity</h4>
                <p>SS316/SS304 fabrication with traceable material certification on request.</p>
            </div>
            <div class="feature-item">
                <span class="num">03</span>
                <h4>Serviceable Design</h4>
                <p>Equipment built with accessible seals, gaskets and wear parts to minimise downtime.</p>
            </div>
            <div class="feature-item">
                <span class="num">04</span>
                <h4>After We Deliver</h4>
                <p>Installation, commissioning, and calibration support don't end at handover.</p>
            </div>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container">
        <h2>Want to discuss a project?</h2>
        <p>Send us your specification sheet or process requirement.</p>
        <a href="contact.php" class="btn btn-light">Contact Our Engineers</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
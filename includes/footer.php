<footer class="site-footer">
    <div class="container">
        <div class="footer-grid">
            <div class="footer-brand">
                <img src="assets/images/logo.png" alt="<?= clean(setting('company_name')) ?> logo">
                <p><?= clean(setting('tagline', 'Engineering chemical and laboratory solutions.')) ?></p>
            </div>
            <div>
                <h4>Company</h4>
                <ul>
                    <li><a href="about.php">About Us</a></li>
                    <li><a href="products.php">Products</a></li>
                    <li><a href="services.php">Services</a></li>
                    <li><a href="contact.php">Contact</a></li>
                    <li><a href="/kaswa/admin/login.php">Account</a></li>
                </ul>
            </div>
            <div>
                <h4>Categories</h4>
                <ul>
                    <li><a href="products.php?category=chemical-manufacturing-plants">Chemical Plants</a></li>
                    <li><a href="products.php?category=lab-analytical-instruments">Lab Instruments</a></li>
                    <li><a href="products.php?category=filtration-separation-systems">Filtration Systems</a></li>
                    <li><a href="products.php?category=mixing-blending-milling">Mixing &amp; Milling</a></li>
                </ul>
            </div>
            <div>
                <h4>Get In Touch</h4>
                <ul>
                    <li><?= clean(setting('corporate_address')) ?></li>
                    <li><a href="tel:<?= clean(setting('phone_1')) ?>"><?= clean(setting('phone_1')) ?></a></li>
                    <li><a href="mailto:<?= clean(setting('email')) ?>"><?= clean(setting('email')) ?></a></li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom">
            <span>&copy; <?= date('Y') ?> <?= clean(setting('company_name', 'KASWA Tech')) ?>. All rights reserved.</span>
            <span>
    <a href="https://www.artsbd.com" target="_blank">AR TECH SOLUTION</a>
</span>
        </div>
    </div>
</footer>

<?php
$whatsappNumber = preg_replace('/[^0-9]/', '', setting('whatsapp_number', setting('phone_1', '')));
$linkedinUrl = setting('linkedin_url', 'https://www.linkedin.com/company/kaswatech');
?>
<div class="floating-actions">
    <?php if ($whatsappNumber): ?>
    <a class="floating-btn whatsapp" href="https://wa.me/<?= clean($whatsappNumber) ?>" target="_blank" rel="noopener" aria-label="Chat with us on WhatsApp" title="Chat on WhatsApp">
        <svg viewBox="0 0 24 24"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2.05 22l5.25-1.38a9.9 9.9 0 0 0 4.74 1.21h.01c5.46 0 9.91-4.45 9.91-9.91 0-2.65-1.03-5.14-2.9-7.01A9.82 9.82 0 0 0 12.04 2Zm5.8 14.02c-.24.68-1.4 1.3-1.93 1.37-.5.07-1.06.1-1.71-.11a9.3 9.3 0 0 1-.72-.27c-1.27-.55-2.5-1.53-3.47-2.75-.68-.85-1.28-1.83-1.65-2.86-.24-.66-.24-1.24-.17-1.72.07-.4.44-.83.79-1.16.18-.17.4-.2.55-.2.16 0 .3 0 .43.01.14.01.32-.05.5.38.19.46.63 1.59.69 1.7.06.12.1.26.02.42-.08.16-.12.26-.24.4-.12.14-.25.3-.36.41-.12.12-.25.25-.11.5.15.25.65 1.08 1.4 1.75.96.86 1.78 1.13 2.03 1.26.25.12.4.1.55-.06.15-.16.63-.73.8-.98.17-.25.34-.2.56-.12.23.08 1.45.68 1.7.81.25.12.41.19.47.29.06.11.06.6-.18 1.28Z"/></svg>
    </a>
    <?php endif; ?>
    <?php if ($linkedinUrl): ?>
    <a class="floating-btn linkedin" href="<?= clean($linkedinUrl) ?>" target="_blank" rel="noopener" aria-label="Follow us on LinkedIn" title="Follow on LinkedIn">
        <svg viewBox="0 0 24 24"><path d="M20.45 20.45h-3.56v-5.57c0-1.33-.02-3.03-1.85-3.03-1.85 0-2.14 1.44-2.14 2.94v5.66H9.34V9h3.42v1.56h.05c.48-.9 1.64-1.85 3.37-1.85 3.6 0 4.27 2.37 4.27 5.45v6.29ZM5.34 7.43a2.07 2.07 0 1 1 0-4.13 2.07 2.07 0 0 1 0 4.13ZM7.12 20.45H3.56V9h3.56v11.45Z"/></svg>
    </a>
    <?php endif; ?>
    <button type="button" class="floating-btn back-to-top" id="backToTop" aria-label="Back to top" title="Back to top">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
    </button>
</div>

<script src="assets/js/main.js"></script>
</body>
</html>

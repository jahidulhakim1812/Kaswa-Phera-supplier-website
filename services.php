<?php
require_once __DIR__ . '/config/db.php';
$page_title = 'Services';
require_once __DIR__ . '/includes/header.php';

$services = [];
try {
    $services = $pdo->query("SELECT * FROM services WHERE status = 1 ORDER BY sort_order ASC")->fetchAll();
} catch (Exception $e) {
    // services table may not exist yet until database/kaswa.sql is (re)imported
}
?>

<div class="breadcrumb-band">
    <div class="container"><a href="index.php">Home</a> / <span>Services</span></div>
</div>

<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">What We Offer</span>
            <h2>End-to-end support, from design to daily operation</h2>
            <p>Equipment supply is only the starting point — our team stays involved through installation, validation and ongoing maintenance.</p>
        </div>

        <!-- Use the same product-grid to get the exact same card styling -->
        <div class="product-grid">
            <?php foreach ($services as $i => $s):
                $svcImg = (!empty($s['image']) && file_exists(__DIR__ . '/uploads/services/' . $s['image']))
                    ? 'uploads/services/' . $s['image'] : null;
            ?>
            <div class="product-card">
                <div class="product-thumb <?= $svcImg ? '' : 'is-placeholder' ?>">
                    <?php if ($svcImg): ?>
                        <img src="<?= clean($svcImg) ?>" alt="<?= clean($s['title']) ?>">
                    <?php else: ?>
                        <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 24 24' fill='none' stroke='%23a0a0a0' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='3' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='3' y1='9' x2='21' y2='9'%3E%3C/line%3E%3Cline x1='3' y1='15' x2='21' y2='15'%3E%3C/line%3E%3Cline x1='9' y1='21' x2='9' y2='9'%3E%3C/line%3E%3C/svg%3E" alt="Placeholder">
                    <?php endif; ?>
                    <!-- Optional badge – change text or remove if not needed -->
                    <span class="badge">Service</span>
                </div>
                <div class="product-body">
                    <!-- You can use a "model" equivalent if you have a short code, else omit -->
                    <?php if (!empty($s['code'])): ?>
                        <div class="model"><?= clean($s['code']) ?></div>
                    <?php endif; ?>
                    <h3><?= clean($s['title']) ?></h3>
                    <p><?= clean($s['description']) ?></p>
                    <!-- Link to a service detail page – adjust href as needed -->
                    <a href="service.php?id=<?= $s['id'] ?>" class="view-link">Learn More →</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="cta-band">
    <div class="container">
        <h2>Need support with an existing installation?</h2>
        <p>Our service team responds to breakdown and spare parts requests via phone and WhatsApp.</p>
        <a href="contact.php" class="btn btn-light">Contact Support</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
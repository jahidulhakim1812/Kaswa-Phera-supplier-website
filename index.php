<?php
require_once __DIR__ . '/config/db.php';
$page_title = 'Home';
require_once __DIR__ . '/includes/header.php';

// Show ALL categories (no LIMIT)
$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC")->fetchAll();
$featured = $pdo->query("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p
                          JOIN categories c ON c.id = p.category_id
                          WHERE p.status = 1 AND p.is_featured = 1 ORDER BY p.created_at DESC LIMIT 8")->fetchAll();

$clients = [];
try {
    $clients = $pdo->query("SELECT * FROM clients WHERE status = 1 ORDER BY sort_order ASC")->fetchAll();
} catch (Exception $e) {
    // clients table may not exist yet
}

$slides = [];
try {
    $slides = $pdo->query("SELECT * FROM hero_slides WHERE status = 1 AND image IS NOT NULL AND image != '' ORDER BY sort_order ASC")->fetchAll();
} catch (Exception $e) {
    // hero_slides table may not exist yet
}
$slides = array_values(array_filter($slides, function ($s) {
    return !empty($s['image']) && file_exists(__DIR__ . '/uploads/hero/' . $s['image']);
}));
?>

<?php if (!empty($slides)): ?>
<section class="hero hero-slider hero-fullscreen" id="heroSlider">
    <div class="hero-slides">
        <?php foreach ($slides as $i => $s):
            $slideImage = 'uploads/hero/' . $s['image'];
        ?>
        <div class="hero-slide <?= $i === 0 ? 'is-active' : '' ?>" data-index="<?= $i ?>">
            <div class="hero-slide-bg" style="background-image:url('<?= clean($slideImage) ?>');"></div>
            <div class="hero-slide-overlay"></div>
            <div class="container hero-no-visual">
                
                    
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php if (count($slides) > 1): ?>
    <button class="hero-arrow prev" id="heroPrev" aria-label="Previous slide">&#10094;</button>
    <button class="hero-arrow next" id="heroNext" aria-label="Next slide">&#10095;</button>
    <div class="hero-dots" id="heroDots">
        <?php foreach ($slides as $i => $s): ?>
            <button class="hero-dot <?= $i === 0 ? 'is-active' : '' ?>" data-index="<?= $i ?>" aria-label="Go to slide <?= $i + 1 ?>"></button>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <a href="#whatWeBuild" class="hero-scroll-cue" aria-label="Scroll down">&#8595;</a>
</section>
<?php endif; ?>

<section class="section" id="whatWeBuild">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">What We Build</span>
            <h2>Equipment categories engineered for every process stage</h2>
            <p>From reaction and separation to drying and final packaging, every category is engineered in-house and backed by local service.</p>
        </div>

        <!-- ========== CATEGORY SLIDER (mobile) / GRID (desktop) ========== -->
        <div class="category-slider-wrapper">
            <div class="category-slider-container" id="categorySlider">
                <?php foreach ($categories as $cat):
                    $catImg = (!empty($cat['image']) && file_exists(__DIR__ . '/uploads/categories/' . $cat['image']))
                        ? 'uploads/categories/' . $cat['image'] : null;
                ?>
                <a href="products.php?category=<?= clean($cat['slug']) ?>" class="product-card category-slide">
                    <div class="product-thumb <?= $catImg ? '' : 'is-placeholder' ?>">
                        <?php if ($catImg): ?>
                            <img src="<?= clean($catImg) ?>" alt="<?= clean($cat['name']) ?>">
                        <?php else: ?>
                            <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='80' height='80' viewBox='0 0 24 24' fill='none' stroke='%23a0a0a0' stroke-width='1.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Crect x='3' y='3' width='18' height='18' rx='2' ry='2'%3E%3C/rect%3E%3Cline x1='3' y1='9' x2='21' y2='9'%3E%3C/line%3E%3Cline x1='3' y1='15' x2='21' y2='15'%3E%3C/line%3E%3Cline x1='9' y1='21' x2='9' y2='9'%3E%3C/line%3E%3C/svg%3E" alt="Placeholder">
                        <?php endif; ?>
                        <span class="badge">Category</span>
                    </div>
                    <div class="product-body">
                        <?php if (!empty($cat['code'])): ?>
                            <div class="model"><?= clean($cat['code']) ?></div>
                        <?php endif; ?>
                        <h3><?= clean($cat['name']) ?></h3>
                        <p><?= clean($cat['description']) ?></p>
                        <span class="view-link">View Products →</span>
                    </div>
                </a>
                <?php endforeach; ?>
            </div>

            <!-- Mobile navigation arrows (hidden on desktop) -->
            <button class="category-arrow prev" id="catPrev" aria-label="Previous category">&#10094;</button>
            <button class="category-arrow next" id="catNext" aria-label="Next category">&#10095;</button>
        </div>
        <!-- ========== END CATEGORY SLIDER ========== -->
    </div>
</section>

<section class="section section-alt">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Featured Equipment</span>
            <h2>Featured Analytical Instruments</h2>
        </div>
        <div class="product-grid">
            <?php foreach ($featured as $p): ?>
            <a href="product-detail.php?slug=<?= clean($p['slug']) ?>" class="product-card">
                <div class="product-thumb <?= product_has_photo($p['image']) ? '' : 'is-placeholder' ?>">
                    <span class="badge">Featured</span>
                    <img src="<?= clean(product_image_url($p['image'])) ?>" alt="<?= clean($p['name']) ?>">
                </div>
                <div class="product-body">
                    <span class="model"><?= clean($p['model_no']) ?></span>
                    <h3><?= clean($p['name']) ?></h3>
                    <p><?= clean($p['short_description']) ?></p>
                    <span class="view-link">View Specs →</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Why KASWA Tech</span>
            <h2>Built for uptime, compliance and local support</h2>
        </div>
        <div class="feature-grid">
            <div class="feature-item">
                <span class="num">01</span>
                <h4>In-House Engineering</h4>
                <p>Every vessel, skid and instrument is engineered and fabricated by our own team, not resold.</p>
            </div>
            <div class="feature-item">
                <span class="num">02</span>
                <h4>Compliance-Ready</h4>
                <p>Documentation and material certificates aligned to GMP and industrial safety standards.</p>
            </div>
            <div class="feature-item">
                <span class="num">03</span>
                <h4>Local Installation</h4>
                <p>On-site commissioning, operator training and calibration across Bangladesh.</p>
            </div>
            <div class="feature-item">
                <span class="num">04</span>
                <h4>Responsive Service</h4>
                <p>Direct WhatsApp and phone support for spare parts and breakdown response.</p>
            </div>
        </div>
    </div>
</section>

<?php if (!empty($clients)): ?>
<section class="section section-alt clients-section">
    <div class="container">
        <div class="section-head">
            <span class="eyebrow">Trusted By</span>
            <h2>Manufacturers &amp; laboratories that rely on KASWA Tech</h2>
        </div>
    </div>
    <div class="clients-marquee">
        <div class="clients-track">
            <?php
            for ($rep = 0; $rep < 2; $rep++):
                foreach ($clients as $c):
                    $logoPath = (!empty($c['logo']) && file_exists(__DIR__ . '/uploads/clients/' . $c['logo']))
                        ? 'uploads/clients/' . $c['logo'] : null;
            ?>
                <div class="client-logo">
                    <?php if ($c['website_url']): ?><a href="<?= clean($c['website_url']) ?>" target="_blank" rel="noopener"><?php endif; ?>
                    <div class="client-logo-mark">
                        <?php if ($logoPath): ?>
                            <img src="<?= clean($logoPath) ?>" alt="<?= clean($c['name']) ?>">
                        <?php else: ?>
                            <span class="client-logo-initial"><?= clean(mb_substr($c['name'], 0, 1)) ?></span>
                        <?php endif; ?>
                    </div>
                    <span class="client-logo-name"><?= clean($c['name']) ?></span>
                    <?php if ($c['website_url']): ?></a><?php endif; ?>
                </div>
            <?php
                endforeach;
            endfor;
            ?>
        </div>
    </div>
</section>
<?php endif; ?>

<section class="cta-band">
    <div class="container">
        <h2>Have a process or lab requirement in mind?</h2>
        <p>Share your specification and our engineering team will respond with a quotation.</p>
        <a href="contact.php" class="btn btn-light">Talk to Our Team</a>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

<!-- ========== ADDITIONAL STYLES & SCRIPTS FOR CATEGORY SLIDER ========== -->
<style>
    /* Category slider wrapper – contains the sliding container and arrows */
    .category-slider-wrapper {
        position: relative;
        margin: 0 -15px; /* compensate container padding */
    }
    .category-slider-container {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 30px;
        padding: 0 15px;
        transition: none;
    }
    /* On mobile: turn into a horizontal scrollable slider */
    @media (max-width: 767px) {
        .category-slider-container {
            display: flex;
            overflow-x: auto;
            scroll-snap-type: x mandatory;
            -webkit-overflow-scrolling: touch;
            gap: 20px;
            padding: 0 15px;
            padding-bottom: 20px; /* space for scrollbar */
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE */
        }
        .category-slider-container::-webkit-scrollbar {
            display: none;
        }
        .category-slide {
            flex: 0 0 80%;
            max-width: 320px;
            scroll-snap-align: start;
            margin-right: 5px;
        }
        /* Navigation arrows – visible only on mobile */
        .category-arrow {
            display: flex !important;
        }
        .category-slider-wrapper {
            margin: 0;
        }
    }
    @media (min-width: 768px) {
        .category-arrow {
            display: none !important;
        }
    }

    /* Arrow styling */
    .category-arrow {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        z-index: 2;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: rgba(255,255,255,0.8);
        border: 1px solid #ddd;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        font-size: 20px;
        line-height: 1;
        cursor: pointer;
        transition: all 0.2s;
        display: none;
        align-items: center;
        justify-content: center;
        color: #333;
    }
    .category-arrow:hover {
        background: #fff;
        box-shadow: 0 4px 12px rgba(0,0,0,0.2);
    }
    .category-arrow.prev {
        left: 5px;
    }
    .category-arrow.next {
        right: 5px;
    }
    @media (max-width: 767px) {
        .category-arrow {
            width: 32px;
            height: 32px;
            font-size: 16px;
        }
        .category-arrow.prev {
            left: 2px;
        }
        .category-arrow.next {
            right: 2px;
        }
    }
</style>

<script>
    (function() {
        const container = document.getElementById('categorySlider');
        if (!container) return;

        const prevBtn = document.getElementById('catPrev');
        const nextBtn = document.getElementById('catNext');

        // Only attach events if arrows exist (they do on all screens but hidden on desktop)
        if (prevBtn && nextBtn) {
            // Scroll by one card width on mobile
            const scrollStep = () => {
                const card = container.querySelector('.category-slide');
                return card ? card.offsetWidth + 20 : 300; // card width + gap
            };

            prevBtn.addEventListener('click', function(e) {
                e.preventDefault();
                container.scrollBy({ left: -scrollStep(), behavior: 'smooth' });
            });

            nextBtn.addEventListener('click', function(e) {
                e.preventDefault();
                container.scrollBy({ left: scrollStep(), behavior: 'smooth' });
            });
        }
    })();
</script>
<?php
require_once __DIR__ . '/config/db.php';

$slug = $_GET['slug'] ?? '';
$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p
                        JOIN categories c ON c.id = p.category_id
                        WHERE p.slug = :slug AND p.status = 1 LIMIT 1");
$stmt->execute([':slug' => $slug]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

$page_title = $product['name'];

// Handle inquiry form submission
$formError = '';
$formSuccess = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inquiry_submit'])) {
    require_once __DIR__ . '/includes/functions.php';
    $name = clean($_POST['name'] ?? '');
    $email = clean($_POST['email'] ?? '');
    $phone = clean($_POST['phone'] ?? '');
    $message = clean($_POST['message'] ?? '');

    if ($name === '' || $email === '' || $message === '') {
        $formError = 'Please fill in your name, email and message.';
    } else {
        $ins = $pdo->prepare("INSERT INTO inquiries (name, email, phone, subject, message, product_id) VALUES (?, ?, ?, ?, ?, ?)");
        $ins->execute([$name, $email, $phone, 'Product Inquiry: ' . $product['name'], $message, $product['id']]);
        $formSuccess = true;
    }
}

$related = $pdo->prepare("SELECT * FROM products WHERE category_id = :cid AND id != :pid AND status = 1 LIMIT 4");
$related->execute([':cid' => $product['category_id'], ':pid' => $product['id']]);
$relatedProducts = $related->fetchAll();

$galleryStmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
$galleryStmt->execute([$product['id']]);
$galleryImages = $galleryStmt->fetchAll();
if (empty($galleryImages) && product_has_photo($product['image'])) {
    $galleryImages = [['image' => $product['image']]];
}

$specs = [];
if (!empty($product['specifications'])) {
    // New format: one spec per line, "Label, Value". Still understands the
    // older single-line "Label: Value | Label: Value" format for products
    // that were saved before this change.
    $normalized = str_replace(["\r\n", "\r"], "\n", $product['specifications']);
    $lines = preg_split('/\n|\|/', $normalized);
    foreach ($lines as $line) {
        $line = trim($line);
        if ($line === '') {
            continue;
        }
        if (strpos($line, ',') !== false) {
            [$k, $v] = array_map('trim', explode(',', $line, 2));
        } elseif (strpos($line, ':') !== false) {
            [$k, $v] = array_map('trim', explode(':', $line, 2));
        } else {
            continue;
        }
        if ($k !== '') {
            $specs[$k] = $v;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb-band">
    <div class="container"><a href="index.php">Home</a> / <a href="products.php">Products</a> / <a href="products.php?category=<?= clean($product['category_slug']) ?>"><?= clean($product['category_name']) ?></a> / <span><?= clean($product['name']) ?></span></div>
</div>

<section class="section">
    <div class="container split">
        <div>
            <div class="product-detail-photo <?= product_has_photo($product['image']) ? '' : 'is-placeholder' ?>">
                <img id="mainProductPhoto" src="<?= clean(product_image_url($product['image'])) ?>" alt="<?= clean($product['name']) ?>">
            </div>
            <?php if (count($galleryImages) > 1): ?>
            <div class="product-gallery-thumbs" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:12px;">
                <?php foreach ($galleryImages as $i => $img): ?>
                <img src="uploads/products/<?= clean($img['image']) ?>" alt="<?= clean($product['name']) ?> photo <?= $i + 1 ?>"
                     class="product-gallery-thumb <?= $img['image'] === $product['image'] ? 'is-active' : '' ?>"
                     style="width:64px; height:64px; object-fit:cover; border-radius:8px; cursor:pointer; border:2px solid <?= $img['image'] === $product['image'] ? 'var(--leaf, #2f8a4b)' : 'transparent' ?>;"
                     onclick="document.getElementById('mainProductPhoto').src=this.src; document.querySelectorAll('.product-gallery-thumb').forEach(function(t){t.style.borderColor='transparent';}); this.style.borderColor='var(--leaf, #2f8a4b)';">
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
        <div>
            <span class="eyebrow"><?= clean($product['category_name']) ?></span>
            <h1 style="font-size:1.9rem; color:var(--forest-dark);"><?= clean($product['name']) ?></h1>
            <p style="font-family:var(--font-mono); color:var(--leaf); font-size:0.85rem;">MODEL: <?= clean($product['model_no']) ?></p>
            <p><?= clean($product['description']) ?></p>

            <?php if (!empty($specs)): ?>
            <h3 style="font-size:1rem; margin-top:26px;">Specifications</h3>
            <table class="spec-table">
                <?php foreach ($specs as $k => $v): ?>
                <tr><td><?= clean($k) ?></td><td><?= clean($v) ?></td></tr>
                <?php endforeach; ?>
            </table>
            <?php endif; ?>

            <div style="margin-top:26px; display:flex; gap:12px;">
                <a href="#inquire" class="btn btn-primary">Request a Quote</a>
                <a href="tel:<?= clean(setting('phone_1')) ?>" class="btn btn-outline">Call an Engineer</a>
            </div>
        </div>
    </div>
</section>

<section class="section section-alt" id="inquire">
    <div class="container" style="max-width:640px;">
        <div class="section-head">
            <span class="eyebrow">Product Inquiry</span>
            <h2>Ask about <?= clean($product['name']) ?></h2>
        </div>

        <?php if ($formSuccess): ?>
            <div class="alert alert-success">Thank you — your inquiry has been received. Our team will contact you shortly.</div>
        <?php endif; ?>
        <?php if ($formError): ?>
            <div class="alert alert-error"><?= clean($formError) ?></div>
        <?php endif; ?>

        <form class="card-form" method="post">
            <div class="form-row">
                <div class="form-group"><label>Full Name *</label><input type="text" name="name" required value="<?= clean($_POST['name'] ?? '') ?>"></div>
                <div class="form-group"><label>Email *</label><input type="email" name="email" required value="<?= clean($_POST['email'] ?? '') ?>"></div>
            </div>
            <div class="form-group"><label>Phone</label><input type="text" name="phone" value="<?= clean($_POST['phone'] ?? '') ?>"></div>
            <div class="form-group"><label>Message *</label><textarea name="message" rows="4" required><?= clean($_POST['message'] ?? ('I would like a quotation for ' . $product['name'] . ' (' . $product['model_no'] . ').')) ?></textarea></div>
            <button type="submit" name="inquiry_submit" class="btn btn-primary" style="width:100%; justify-content:center;">Send Inquiry</button>
        </form>
    </div>
</section>

<?php if (!empty($relatedProducts)): ?>
<section class="section">
    <div class="container">
        <div class="section-head left">
            <span class="eyebrow">You May Also Need</span>
            <h2>Related equipment</h2>
        </div>
        <div class="product-grid">
            <?php foreach ($relatedProducts as $rp): ?>
            <a href="product-detail.php?slug=<?= clean($rp['slug']) ?>" class="product-card">
                <div class="product-thumb <?= product_has_photo($rp['image']) ? '' : 'is-placeholder' ?>"><img src="<?= clean(product_image_url($rp['image'])) ?>" alt="<?= clean($rp['name']) ?>"></div>
                <div class="product-body">
                    <span class="model"><?= clean($rp['model_no']) ?></span>
                    <h3><?= clean($rp['name']) ?></h3>
                    <p><?= clean($rp['short_description']) ?></p>
                    <span class="view-link">View Specs →</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

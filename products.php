<?php
require_once __DIR__ . '/config/db.php';
$page_title = 'Products';

$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY sort_order ASC")->fetchAll();

$activeSlug = isset($_GET['category']) ? trim($_GET['category']) : '';
$search = isset($_GET['q']) ? trim($_GET['q']) : '';

$sql = "SELECT p.*, c.name AS category_name, c.slug AS category_slug FROM products p
        JOIN categories c ON c.id = p.category_id
        WHERE p.status = 1";
$params = [];

if ($activeSlug !== '') {
    $sql .= " AND c.slug = :slug";
    $params[':slug'] = $activeSlug;
}
if ($search !== '') {
    // Each occurrence needs its own named placeholder — PDO's native prepared
    // statements (ATTR_EMULATE_PREPARES => false) don't allow the same named
    // parameter to be bound more than once in a single query.
    $sql .= " AND (p.name LIKE :q1 OR p.short_description LIKE :q2 OR p.model_no LIKE :q3)";
    $searchTerm = '%' . $search . '%';
    $params[':q1'] = $searchTerm;
    $params[':q2'] = $searchTerm;
    $params[':q3'] = $searchTerm;
}
$sql .= " ORDER BY p.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="breadcrumb-band">
    <div class="container"><a href="index.php">Home</a> / <span>Products</span></div>
</div>

<section class="section">
    <div class="container">
        <div class="section-head left">
            <span class="eyebrow">Product Catalogue</span>
            <h2>Chemical manufacturing &amp; lab equipment</h2>
            <p>Search by name, model number, or filter by category.</p>
        </div>

        <!-- Search form with category dropdown -->
        <form method="get" style="max-width:700px; margin-bottom:36px;">
            <div style="display:flex; flex-wrap:wrap; gap:10px; align-items:center;">
                <input type="text" name="q" placeholder="Search products or model no..." value="<?= clean($search) ?>" style="flex:1; min-width:180px; padding:12px 14px; border:1px solid var(--steel-light); border-radius:8px; font-size:0.95rem; background:var(--white);">
                <select name="category" style="padding:12px 14px; border:1px solid var(--steel-light); border-radius:8px; background:var(--white); font-size:0.95rem; min-width:160px;">
                    <option value="">All Categories</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= clean($cat['slug']) ?>" <?= $activeSlug === $cat['slug'] ? 'selected' : '' ?>><?= clean($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" class="btn btn-primary" style="white-space:nowrap;">Search</button>
                <?php if ($search !== '' || $activeSlug !== ''): ?>
                    <a href="products.php" class="btn btn-outline" style="white-space:nowrap;">Clear</a>
                <?php endif; ?>
            </div>
        </form>

        <?php if (empty($products)): ?>
            <p style="color:var(--steel);">No products matched your search. Try a different keyword or browse all categories.</p>
        <?php else: ?>
        <div class="product-grid">
            <?php foreach ($products as $p): ?>
            <a href="product-detail.php?slug=<?= clean($p['slug']) ?>" class="product-card">
                <div class="product-thumb <?= product_has_photo($p['image']) ? '' : 'is-placeholder' ?>">
                    <?php if ($p['is_featured']): ?><span class="badge">Featured</span><?php endif; ?>
                    <img src="<?= clean(product_image_url($p['image'])) ?>" alt="<?= clean($p['name']) ?>">
                </div>
                <div class="product-body">
                    <span class="model"><?= clean($p['model_no']) ?> · <?= clean($p['category_name']) ?></span>
                    <h3><?= clean($p['name']) ?></h3>
                    <p><?= clean($p['short_description']) ?></p>
                    <span class="view-link">View Specs →</span>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

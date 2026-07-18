<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Add Product';

$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0);
    $model_no = trim($_POST['model_no'] ?? '');
    $short_description = trim($_POST['short_description'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $specifications = trim($_POST['specifications'] ?? '');
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $status = isset($_POST['status']) ? 1 : 0;

    if ($name === '' || $category_id === 0) {
        $error = 'Product name and category are required.';
    } else {
        $slug = admin_slugify($name . '-' . $model_no);
        $check = $pdo->prepare("SELECT id FROM products WHERE slug = ?");
        $check->execute([$slug]);
        if ($check->fetch()) {
            $slug .= '-' . substr(md5(uniqid()), 0, 4);
        }

        $imageName = null;
        $galleryFiles = [];
        if (!empty($_FILES['images']['name'][0])) {
            $upload = admin_handle_multi_image_upload($_FILES['images'], $slug, __DIR__ . '/../uploads/products');
            $galleryFiles = $upload['files'];
            if (empty($galleryFiles) && $upload['error']) {
                $error = $upload['error'];
            }
            if (!empty($galleryFiles)) {
                $imageName = $galleryFiles[0]; // first photo becomes the cover image shown on cards
            }
        }

        if ($error === '') {
            $ins = $pdo->prepare("INSERT INTO products (category_id, name, slug, model_no, short_description, description, specifications, image, is_featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $ins->execute([$category_id, $name, $slug, $model_no, $short_description, $description, $specifications, $imageName, $is_featured, $status]);
            $newProductId = (int)$pdo->lastInsertId();

            if (!empty($galleryFiles)) {
                $imgIns = $pdo->prepare("INSERT INTO product_images (product_id, image, sort_order) VALUES (?, ?, ?)");
                foreach ($galleryFiles as $order => $file) {
                    $imgIns->execute([$newProductId, $file, $order]);
                }
            }

            header('Location: products.php');
            exit;
        }
    }
}

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-wide">
    <div class="card-head"><h3>Add New Product</h3></div>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group"><label>Product Name *</label><input type="text" name="name" required value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"></div>
            <div class="form-group"><label>Category *</label>
                <select name="category_id" required>
                    <option value="">Select category</option>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Model Number</label><input type="text" name="model_no" placeholder="e.g. KSW-RV500"></div>
            <div class="form-group full">
                <label>Product Photos (you can select multiple)</label>
                <input type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple data-preview-multi="#imgPreviewGrid">
                <div class="hint">JPG, PNG or WEBP, up to 25MB per photo. The first photo becomes the main image shown on product cards. Leave empty to use a placeholder.</div>
                <div id="imgPreviewGrid" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:10px;">
                    <img src="../assets/images/product-placeholder.svg" style="width:90px; height:90px; object-fit:contain; background:var(--paper); border:1px solid var(--steel-light); border-radius:8px; padding:8px;">
                </div>
            </div>
            <div class="form-group full"><label>Short Description</label><input type="text" name="short_description" placeholder="One-line summary shown on product cards" value="<?= htmlspecialchars($_POST['short_description'] ?? '') ?>"></div>
            <div class="form-group full"><label>Full Description</label><textarea name="description" rows="4"><?= htmlspecialchars($_POST['description'] ?? '') ?></textarea></div>
            <div class="form-group full">
                <label>Specifications</label>
                <textarea name="specifications" rows="7" class="spec-textarea" placeholder="Capacity, 500 L
Material, SS316
Max Pressure, 3 bar"><?= htmlspecialchars($_POST['specifications'] ?? '') ?></textarea>
                <div class="hint">One specification per line, label and value separated by a comma — each line becomes a row with two columns on the product page.</div>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_featured" style="width:auto; display:inline-block; margin-right:8px;"> Mark as Featured</label>
                <label style="margin-top:10px;"><input type="checkbox" name="status" checked style="width:auto; display:inline-block; margin-right:8px;"> Active (visible on site)</label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Product</button>
            <a href="products.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

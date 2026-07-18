<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Edit Product';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();
$error = '';

// Delete a single gallery photo
if (isset($_GET['delete_image'])) {
    $imgId = (int)$_GET['delete_image'];
    $imgStmt = $pdo->prepare("SELECT * FROM product_images WHERE id = ? AND product_id = ?");
    $imgStmt->execute([$imgId, $id]);
    $img = $imgStmt->fetch();
    if ($img) {
        $pdo->prepare("DELETE FROM product_images WHERE id = ?")->execute([$imgId]);
        if ($product['image'] === $img['image']) {
            $next = $pdo->prepare("SELECT image FROM product_images WHERE product_id = ? ORDER BY sort_order ASC LIMIT 1");
            $next->execute([$id]);
            $nextImage = $next->fetchColumn();
            $pdo->prepare("UPDATE products SET image = ? WHERE id = ?")->execute([$nextImage ?: null, $id]);
        }
        if ($img['image'] && file_exists(__DIR__ . '/../uploads/products/' . $img['image'])) {
            $stillUsed = $pdo->prepare("SELECT COUNT(*) FROM product_images WHERE image = ?");
            $stillUsed->execute([$img['image']]);
            if ((int)$stillUsed->fetchColumn() === 0) {
                @unlink(__DIR__ . '/../uploads/products/' . $img['image']);
            }
        }
    }
    header('Location: product-edit.php?id=' . $id);
    exit;
}

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
        $imageName = $product['image'];
        $galleryFiles = [];

        if (!empty($_FILES['images']['name'][0])) {
            $upload = admin_handle_multi_image_upload($_FILES['images'], $product['slug'], __DIR__ . '/../uploads/products');
            $galleryFiles = $upload['files'];
            if (empty($galleryFiles) && $upload['error']) {
                $error = $upload['error'];
            }
            if (!empty($galleryFiles) && empty($imageName)) {
                $imageName = $galleryFiles[0]; // no cover yet, use the first newly added photo
            }
        }

        if ($error === '') {
            $upd = $pdo->prepare("UPDATE products SET category_id=?, name=?, model_no=?, short_description=?, description=?, specifications=?, image=?, is_featured=?, status=? WHERE id=?");
            $upd->execute([$category_id, $name, $model_no, $short_description, $description, $specifications, $imageName, $is_featured, $status, $id]);

            if (!empty($galleryFiles)) {
                $orderStmt = $pdo->prepare("SELECT COALESCE(MAX(sort_order),-1) FROM product_images WHERE product_id = ?");
                $orderStmt->execute([$id]);
                $nextOrder = (int)$orderStmt->fetchColumn() + 1;

                $imgIns = $pdo->prepare("INSERT INTO product_images (product_id, image, sort_order) VALUES (?, ?, ?)");
                foreach ($galleryFiles as $file) {
                    $imgIns->execute([$id, $file, $nextOrder]);
                    $nextOrder++;
                }
            }

            header('Location: products.php');
            exit;
        }
    }
}

$galleryStmt = $pdo->prepare("SELECT * FROM product_images WHERE product_id = ? ORDER BY sort_order ASC");
$galleryStmt->execute([$id]);
$gallery = $galleryStmt->fetchAll();

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-wide">
    <div class="card-head"><h3>Edit Product</h3></div>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group"><label>Product Name *</label><input type="text" name="name" required value="<?= htmlspecialchars($product['name']) ?>"></div>
            <div class="form-group"><label>Category *</label>
                <select name="category_id" required>
                    <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= $cat['id'] == $product['category_id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group"><label>Model Number</label><input type="text" name="model_no" value="<?= htmlspecialchars($product['model_no']) ?>"></div>
            <div class="form-group full">
                <label>Add More Photos (you can select multiple)</label>
                <input type="file" name="images[]" accept=".jpg,.jpeg,.png,.webp" multiple data-preview-multi="#imgPreviewGrid">
                <div class="hint">JPG, PNG or WEBP, up to 25MB per photo. New photos are added to the gallery below rather than replacing it.</div>
                <div id="imgPreviewGrid" style="display:flex; flex-wrap:wrap; gap:10px; margin-top:10px;"></div>
            </div>
            <div class="form-group full"><label>Short Description</label><input type="text" name="short_description" value="<?= htmlspecialchars($product['short_description']) ?>"></div>
            <div class="form-group full"><label>Full Description</label><textarea name="description" rows="4"><?= htmlspecialchars($product['description']) ?></textarea></div>
            <div class="form-group full">
                <label>Specifications</label>
                <textarea name="specifications" rows="7" class="spec-textarea" placeholder="Capacity, 500 L
Material, SS316
Max Pressure, 3 bar"><?= htmlspecialchars($product['specifications']) ?></textarea>
                <div class="hint">One specification per line, label and value separated by a comma — each line becomes a row with two columns on the product page.</div>
            </div>
            <div class="form-group">
                <label><input type="checkbox" name="is_featured" <?= $product['is_featured'] ? 'checked' : '' ?> style="width:auto; display:inline-block; margin-right:8px;"> Mark as Featured</label>
                <label style="margin-top:10px;"><input type="checkbox" name="status" <?= $product['status'] ? 'checked' : '' ?> style="width:auto; display:inline-block; margin-right:8px;"> Active (visible on site)</label>
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="products.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<div class="card">
    <div class="card-head"><h3>Product Gallery (<?= count($gallery) ?>)</h3></div>
    <?php if (empty($gallery)): ?>
        <div class="empty-state"><div class="icon">🖼️</div>No photos uploaded yet. Add some using the form above.</div>
    <?php else: ?>
    <div style="display:flex; flex-wrap:wrap; gap:14px;">
        <?php foreach ($gallery as $img): ?>
        <div style="width:120px; text-align:center;">
            <img src="../uploads/products/<?= htmlspecialchars($img['image']) ?>" style="width:120px; height:120px; object-fit:cover; border:1px solid var(--steel-light); border-radius:8px;">
            <?php if ($product['image'] === $img['image']): ?>
                <div class="badge-pill badge-green" style="margin-top:6px;">Cover</div>
            <?php endif; ?>
            <a href="?id=<?= $id ?>&delete_image=<?= $img['id'] ?>" class="btn btn-danger btn-sm" style="margin-top:6px; width:100%;" data-confirm="Remove this photo?">Delete</a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

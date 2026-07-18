<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Edit Category';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM categories WHERE id = ?");
$stmt->execute([$id]);
$category = $stmt->fetch();

if (!$category) {
    header('Location: categories.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'flask');
    $removeImage = isset($_POST['remove_image']);

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        $imageName = $category['image'];

        if ($removeImage && $imageName) {
            if (file_exists(__DIR__ . '/../uploads/categories/' . $imageName)) {
                @unlink(__DIR__ . '/../uploads/categories/' . $imageName);
            }
            $imageName = null;
        }

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 3 * 1024 * 1024) {
                if ($imageName && file_exists(__DIR__ . '/../uploads/categories/' . $imageName)) {
                    @unlink(__DIR__ . '/../uploads/categories/' . $imageName);
                }
                $imageName = 'category-' . time() . '-' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/categories/' . $imageName);
            } else {
                $error = 'Image must be JPG, PNG or WEBP and under 3MB.';
            }
        }

        if ($error === '') {
            $upd = $pdo->prepare("UPDATE categories SET name = ?, description = ?, icon = ?, image = ? WHERE id = ?");
            $upd->execute([$name, $description, $icon, $imageName, $id]);
            header('Location: categories.php');
            exit;
        }
    }
}

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-medium">
    <div class="card-head"><h3>Edit Category</h3></div>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group"><label>Category Name *</label><input type="text" name="name" required value="<?= htmlspecialchars($category['name']) ?>"></div>
        <div class="form-group"><label>Icon Keyword</label><input type="text" name="icon" value="<?= htmlspecialchars($category['icon']) ?>"></div>
        <div class="form-group"><label>Description</label><textarea name="description" rows="3"><?= htmlspecialchars($category['description']) ?></textarea></div>
        <div class="form-group">
            <label>Category Image</label>
            <?php if (!empty($category['image']) && file_exists(__DIR__ . '/../uploads/categories/' . $category['image'])): ?>
                <div style="margin-bottom:10px;">
                    <img class="thumb-sm" src="../uploads/categories/<?= htmlspecialchars($category['image']) ?>" alt="">
                    <label style="display:inline-flex; align-items:center; gap:6px; font-weight:400; margin-left:10px;">
                        <input type="checkbox" name="remove_image" value="1" style="width:auto;"> Remove current image
                    </label>
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" data-preview="#catImgPreview">
            <p class="hint">Upload a new file to replace the current image. Leave blank to keep it. Max 3MB.</p>
            <img id="catImgPreview" class="thumb-sm" style="margin-top:10px; display:none;" onload="this.style.display='inline-block'">
        </div>
        <div class="form-group"><label>Slug (read-only)</label><input type="text" value="<?= htmlspecialchars($category['slug']) ?>" disabled></div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="categories.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

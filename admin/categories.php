<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Categories';

$error = '';
$success = '';

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $icon = trim($_POST['icon'] ?? 'flask');

    if ($name === '') {
        $error = 'Category name is required.';
    } else {
        $imageName = null;
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 3 * 1024 * 1024) {
                $imageName = 'category-' . time() . '-' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/categories/' . $imageName);
            } else {
                $error = 'Image must be JPG, PNG or WEBP and under 3MB.';
            }
        }

        if ($error === '') {
            $slug = admin_slugify($name);
            $check = $pdo->prepare("SELECT id FROM categories WHERE slug = ?");
            $check->execute([$slug]);
            if ($check->fetch()) {
                $slug .= '-' . substr(md5(uniqid()), 0, 4);
            }
            $maxOrder = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM categories")->fetchColumn();
            $ins = $pdo->prepare("INSERT INTO categories (name, slug, description, icon, image, sort_order) VALUES (?, ?, ?, ?, ?, ?)");
            $ins->execute([$name, $slug, $description, $icon, $imageName, $maxOrder + 1]);
            $success = 'Category added successfully.';
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $count = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
    $count->execute([$id]);
    if ($count->fetchColumn() > 0) {
        $error = 'Cannot delete a category that still has products. Move or delete its products first.';
    } else {
        $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$id]);
        $success = 'Category deleted.';
    }
}

// Handle toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE categories SET status = 1 - status WHERE id = ?")->execute([$id]);
    header('Location: categories.php');
    exit;
}

$categories = $pdo->query("SELECT c.*, (SELECT COUNT(*) FROM products p WHERE p.category_id = c.id) AS product_count
                            FROM categories c ORDER BY c.sort_order ASC")->fetchAll();

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
    <div class="card-head"><h3>Add New Category</h3></div>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group"><label>Category Name *</label><input type="text" name="name" required placeholder="e.g. Reactors & Vessels"></div>
            <div class="form-group"><label>Icon Keyword</label><input type="text" name="icon" placeholder="e.g. reactor, flask, filter"></div>
            <div class="form-group full"><label>Description</label><textarea name="description" rows="2" placeholder="Short description shown on the category card"></textarea></div>
            <div class="form-group full">
                <label>Category Image (shown on the homepage card)</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" data-preview="#catImgPreview">
                <p class="hint">Square images work best. Max 3MB. Leave blank to show the numbered badge instead.</p>
                <img id="catImgPreview" class="thumb-sm" style="margin-top:10px; display:none;" onload="this.style.display='inline-block'">
            </div>
        </div>
        <div class="form-actions"><button type="submit" name="add_category" class="btn btn-primary">Add Category</button></div>
    </form>
</div>

<div class="card">
    <div class="card-head"><h3>All Categories (<?= count($categories) ?>)</h3></div>
    <?php if (empty($categories)): ?>
        <div class="empty-state"><div class="icon">🗂️</div>No categories yet. Add your first one above.</div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Image</th><th>Name</th><th>Slug</th><th>Products</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($categories as $cat): ?>
            <tr>
                <td>
                    <?php if (!empty($cat['image']) && file_exists(__DIR__ . '/../uploads/categories/' . $cat['image'])): ?>
                        <img class="thumb-sm" src="../uploads/categories/<?= htmlspecialchars($cat['image']) ?>" alt="">
                    <?php else: ?>
                        <span class="badge-pill badge-gray">No image</span>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($cat['name']) ?></strong><br><span style="color:var(--steel); font-size:0.82rem;"><?= htmlspecialchars($cat['description']) ?></span></td>
                <td><code><?= htmlspecialchars($cat['slug']) ?></code></td>
                <td><?= (int)$cat['product_count'] ?></td>
                <td>
                    <a href="?toggle=<?= $cat['id'] ?>" class="badge-pill <?= $cat['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $cat['status'] ? 'Active' : 'Hidden' ?></a>
                </td>
                <td class="table-actions">
                    <a href="category-edit.php?id=<?= $cat['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                    <a href="?delete=<?= $cat['id'] ?>" class="btn btn-danger btn-sm" data-confirm="Delete category '<?= htmlspecialchars($cat['name']) ?>'? This cannot be undone.">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

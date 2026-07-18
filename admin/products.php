<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Products';

$success = '';
$error = '';

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->execute([$id]);
    $img = $stmt->fetchColumn();
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$id]);
    if ($img && file_exists(__DIR__ . '/../uploads/products/' . $img)) {
        @unlink(__DIR__ . '/../uploads/products/' . $img);
    }
    $success = 'Product deleted.';
}
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE products SET status = 1 - status WHERE id = ?")->execute([$id]);
    header('Location: products.php');
    exit;
}
if (isset($_GET['feature'])) {
    $id = (int)$_GET['feature'];
    $pdo->prepare("UPDATE products SET is_featured = 1 - is_featured WHERE id = ?")->execute([$id]);
    header('Location: products.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY sort_order ASC")->fetchAll();
$catFilter = (int)($_GET['category_id'] ?? 0);
$search = trim($_GET['q'] ?? '');

$sql = "SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id WHERE 1=1";
$params = [];
if ($catFilter) { $sql .= " AND p.category_id = ?"; $params[] = $catFilter; }
if ($search !== '') { $sql .= " AND (p.name LIKE ? OR p.model_no LIKE ?)"; $params[] = "%$search%"; $params[] = "%$search%"; }
$sql .= " ORDER BY p.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card">
    <div class="card-head">
        <h3>All Products (<?= count($products) ?>)</h3>
        <a href="product-add.php" class="btn btn-primary btn-sm">+ Add Product</a>
    </div>

    <form method="get" style="display:flex; gap:10px; margin-bottom:18px; flex-wrap:wrap;">
        <input type="text" name="q" placeholder="Search name or model no..." value="<?= htmlspecialchars($search) ?>" style="flex:1; min-width:200px; padding:10px 13px; border:1px solid var(--steel-light); border-radius:8px;">
        <select name="category_id" style="padding:10px 13px; border:1px solid var(--steel-light); border-radius:8px;">
            <option value="0">All Categories</option>
            <?php foreach ($categories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= $catFilter === (int)$cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" class="btn btn-outline">Filter</button>
    </form>

    <?php if (empty($products)): ?>
        <div class="empty-state"><div class="icon">⚙️</div>No products found.</div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th></th><th>Name</th><th>Model No.</th><th>Category</th><th>Featured</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($products as $p): ?>
            <tr>
                <td><img class="thumb-sm" src="../<?= htmlspecialchars(product_thumb_admin($p['image'])) ?>" alt=""></td>
                <td><strong><?= htmlspecialchars($p['name']) ?></strong></td>
                <td><code><?= htmlspecialchars($p['model_no']) ?></code></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td><a href="?feature=<?= $p['id'] ?>" class="badge-pill <?= $p['is_featured'] ? 'badge-amber' : 'badge-gray' ?>"><?= $p['is_featured'] ? '★ Featured' : 'Standard' ?></a></td>
                <td><a href="?toggle=<?= $p['id'] ?>" class="badge-pill <?= $p['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $p['status'] ? 'Active' : 'Hidden' ?></a></td>
                <td class="table-actions">
                    <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                    <a href="?delete=<?= $p['id'] ?>" class="btn btn-danger btn-sm" data-confirm="Delete '<?= htmlspecialchars($p['name']) ?>'? This cannot be undone.">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

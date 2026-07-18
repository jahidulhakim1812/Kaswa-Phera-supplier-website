<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Services';

$error = '';
$success = '';

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_service'])) {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');

    if ($title === '') {
        $error = 'Service title is required.';
    } else {
        $imageName = null;
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 3 * 1024 * 1024) {
                $imageName = 'service-' . time() . '-' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/services/' . $imageName);
            } else {
                $error = 'Image must be JPG, PNG or WEBP and under 3MB.';
            }
        }

        if ($error === '') {
            $maxOrder = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM services")->fetchColumn();
            $ins = $pdo->prepare("INSERT INTO services (title, description, image, sort_order) VALUES (?, ?, ?, ?)");
            $ins->execute([$title, $description, $imageName, $maxOrder + 1]);
            $success = 'Service added successfully.';
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        if (!empty($row['image']) && file_exists(__DIR__ . '/../uploads/services/' . $row['image'])) {
            @unlink(__DIR__ . '/../uploads/services/' . $row['image']);
        }
        $pdo->prepare("DELETE FROM services WHERE id = ?")->execute([$id]);
        $success = 'Service removed.';
    }
}

// Handle toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE services SET status = 1 - status WHERE id = ?")->execute([$id]);
    header('Location: services.php');
    exit;
}

// Handle reorder
if (isset($_GET['move'])) {
    $id = (int)$_GET['move'];
    $dir = $_GET['dir'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();
    if ($current) {
        $op = $dir === 'up' ? '<' : '>';
        $orderDir = $dir === 'up' ? 'DESC' : 'ASC';
        $neighborStmt = $pdo->prepare("SELECT * FROM services WHERE sort_order $op ? ORDER BY sort_order $orderDir LIMIT 1");
        $neighborStmt->execute([$current['sort_order']]);
        $neighbor = $neighborStmt->fetch();
        if ($neighbor) {
            $pdo->prepare("UPDATE services SET sort_order = ? WHERE id = ?")->execute([$neighbor['sort_order'], $current['id']]);
            $pdo->prepare("UPDATE services SET sort_order = ? WHERE id = ?")->execute([$current['sort_order'], $neighbor['id']]);
        }
    }
    header('Location: services.php');
    exit;
}

$services = $pdo->query("SELECT * FROM services ORDER BY sort_order ASC")->fetchAll();

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-wide">
    <div class="card-head"><h3>Add New Service</h3></div>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group full"><label>Service Title *</label><input type="text" name="title" required placeholder="e.g. Custom Equipment Design"></div>
            <div class="form-group full"><label>Description</label><textarea name="description" rows="2" placeholder="Short description shown on the service card"></textarea></div>
            <div class="form-group full">
                <label>Service Image</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" data-preview="#svcImgPreview">
                <p class="hint">Square images work best. Max 3MB. Leave blank to show the numbered badge instead.</p>
                <img id="svcImgPreview" class="thumb-sm" style="margin-top:10px; display:none;" onload="this.style.display='inline-block'">
            </div>
        </div>
        <div class="form-actions"><button type="submit" name="add_service" class="btn btn-primary">Add Service</button></div>
    </form>
</div>

<div class="card card-wide">
    <div class="card-head"><h3>All Services (<?= count($services) ?>) — shown in order on the Services page</h3></div>
    <?php if (empty($services)): ?>
        <div class="empty-state"><div class="icon">🛠️</div>No services yet. Add your first one above.</div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Image</th><th>Title</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($services as $s): ?>
            <tr>
                <td>
                    <?php if (!empty($s['image']) && file_exists(__DIR__ . '/../uploads/services/' . $s['image'])): ?>
                        <img class="thumb-sm" src="../uploads/services/<?= htmlspecialchars($s['image']) ?>" alt="">
                    <?php else: ?>
                        <span class="badge-pill badge-gray">No image</span>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($s['title']) ?></strong><br><span style="color:var(--steel); font-size:0.82rem;"><?= htmlspecialchars(mb_strimwidth($s['description'], 0, 80, '...')) ?></span></td>
                <td class="table-actions">
                    <a href="?move=<?= $s['id'] ?>&dir=up" class="btn btn-outline btn-sm" title="Move up">↑</a>
                    <a href="?move=<?= $s['id'] ?>&dir=down" class="btn btn-outline btn-sm" title="Move down">↓</a>
                </td>
                <td><a href="?toggle=<?= $s['id'] ?>" class="badge-pill <?= $s['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $s['status'] ? 'Active' : 'Hidden' ?></a></td>
                <td class="table-actions">
                    <a href="service-edit.php?id=<?= $s['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                    <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm" data-confirm="Remove service '<?= htmlspecialchars($s['title']) ?>'?">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Hero Slider';

$error = '';
$success = '';

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_slide'])) {
    $eyebrow = trim($_POST['eyebrow'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $cta_text = trim($_POST['cta_text'] ?? 'Explore Products');
    $cta_link = trim($_POST['cta_link'] ?? 'products.php');

    if ($title === '') {
        $error = 'Slide title is required.';
    } else {
        $imageName = null;
        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 5 * 1024 * 1024) {
                $imageName = 'slide-' . time() . '-' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/hero/' . $imageName);
            } else {
                $error = 'Slide image must be JPG, PNG or WEBP and under 5MB.';
            }
        }

        if ($error === '') {
            $maxOrder = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM hero_slides")->fetchColumn();
            $ins = $pdo->prepare("INSERT INTO hero_slides (eyebrow, title, subtitle, image, cta_text, cta_link, sort_order) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $ins->execute([$eyebrow, $title, $subtitle, $imageName, $cta_text, $cta_link, $maxOrder + 1]);
            $success = 'Slide added successfully.';
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT image FROM hero_slides WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        if (!empty($row['image']) && file_exists(__DIR__ . '/../uploads/hero/' . $row['image'])) {
            @unlink(__DIR__ . '/../uploads/hero/' . $row['image']);
        }
        $pdo->prepare("DELETE FROM hero_slides WHERE id = ?")->execute([$id]);
        $success = 'Slide deleted.';
    }
}

// Handle toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE hero_slides SET status = 1 - status WHERE id = ?")->execute([$id]);
    header('Location: hero-slides.php');
    exit;
}

// Handle reorder (move up / down)
if (isset($_GET['move'])) {
    $id = (int)$_GET['move'];
    $dir = $_GET['dir'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM hero_slides WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();
    if ($current) {
        $op = $dir === 'up' ? '<' : '>';
        $orderDir = $dir === 'up' ? 'DESC' : 'ASC';
        $neighborStmt = $pdo->prepare("SELECT * FROM hero_slides WHERE sort_order $op ? ORDER BY sort_order $orderDir LIMIT 1");
        $neighborStmt->execute([$current['sort_order']]);
        $neighbor = $neighborStmt->fetch();
        if ($neighbor) {
            $pdo->prepare("UPDATE hero_slides SET sort_order = ? WHERE id = ?")->execute([$neighbor['sort_order'], $current['id']]);
            $pdo->prepare("UPDATE hero_slides SET sort_order = ? WHERE id = ?")->execute([$current['sort_order'], $neighbor['id']]);
        }
    }
    header('Location: hero-slides.php');
    exit;
}

$slides = $pdo->query("SELECT * FROM hero_slides ORDER BY sort_order ASC")->fetchAll();

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-wide">
    <div class="card-head"><h3>Add New Slide</h3></div>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group"><label>Eyebrow (small label above heading)</label><input type="text" name="eyebrow" placeholder="e.g. Reactors & Process Vessels"></div>
            <div class="form-group"><label>Title *</label><input type="text" name="title" required placeholder="Main slide headline"></div>
            <div class="form-group full"><label>Subtitle</label><textarea name="subtitle" rows="2" placeholder="Supporting sentence shown under the title"></textarea></div>
            <div class="form-group"><label>Button Text</label><input type="text" name="cta_text" value="Explore Products"></div>
            <div class="form-group"><label>Button Link</label><input type="text" name="cta_link" value="products.php" placeholder="e.g. products.php or contact.php"></div>
            <div class="form-group full">
                <label>Background Photo (optional — full-screen background image for this slide)</label>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" data-preview="#slidePreview">
                <p class="hint">Landscape photos work best, 1920×1080 or larger. Max 5MB. Leave blank to use the default green brand background.</p>
                <img id="slidePreview" class="thumb-sm" style="margin-top:10px; display:none; width:120px; height:70px; object-fit:cover;" onload="this.style.display='inline-block'">
            </div>
        </div>
        <div class="form-actions"><button type="submit" name="add_slide" class="btn btn-primary">Add Slide</button></div>
    </form>
</div>

<div class="card card-wide">
    <div class="card-head"><h3>All Slides (<?= count($slides) ?>) — shown in order on the homepage</h3></div>
    <?php if (empty($slides)): ?>
        <div class="empty-state"><div class="icon">🖼️</div>No slides yet. Add your first one above — until then the homepage shows a default brand slide.</div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Preview</th><th>Title</th><th>Button</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($slides as $s): ?>
            <tr>
                <td>
                    <?php if (!empty($s['image']) && file_exists(__DIR__ . '/../uploads/hero/' . $s['image'])): ?>
                        <img class="thumb-sm" style="width:64px;height:42px;object-fit:cover;" src="../uploads/hero/<?= htmlspecialchars($s['image']) ?>" alt="">
                    <?php else: ?>
                        <span class="badge-pill badge-gray">Default bg</span>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($s['title']) ?></strong><br><span style="color:var(--steel); font-size:0.82rem;"><?= htmlspecialchars(mb_strimwidth($s['subtitle'] ?? '', 0, 70, '...')) ?></span></td>
                <td><code><?= htmlspecialchars($s['cta_text']) ?></code></td>
                <td class="table-actions">
                    <a href="?move=<?= $s['id'] ?>&dir=up" class="btn btn-outline btn-sm" title="Move up">↑</a>
                    <a href="?move=<?= $s['id'] ?>&dir=down" class="btn btn-outline btn-sm" title="Move down">↓</a>
                </td>
                <td><a href="?toggle=<?= $s['id'] ?>" class="badge-pill <?= $s['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $s['status'] ? 'Active' : 'Hidden' ?></a></td>
                <td class="table-actions">
                    <a href="hero-slide-edit.php?id=<?= $s['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                    <a href="?delete=<?= $s['id'] ?>" class="btn btn-danger btn-sm" data-confirm="Delete slide '<?= htmlspecialchars($s['title']) ?>'?">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

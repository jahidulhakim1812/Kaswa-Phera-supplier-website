<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Clients / Logos';

$error = '';
$success = '';

// Handle add
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_client'])) {
    $name = trim($_POST['name'] ?? '');
    $website_url = trim($_POST['website_url'] ?? '');

    if ($name === '') {
        $error = 'Client name is required.';
    } else {
        $logoName = null;
        if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['logo']['size'] <= 2 * 1024 * 1024) {
                $logoName = 'client-' . time() . '-' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
                move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . '/../uploads/clients/' . $logoName);
            } else {
                $error = 'Logo must be JPG, PNG, WEBP or SVG and under 2MB.';
            }
        }

        if ($error === '') {
            $maxOrder = (int)$pdo->query("SELECT COALESCE(MAX(sort_order),0) FROM clients")->fetchColumn();
            $ins = $pdo->prepare("INSERT INTO clients (name, logo, website_url, sort_order) VALUES (?, ?, ?, ?)");
            $ins->execute([$name, $logoName, $website_url, $maxOrder + 1]);
            $success = 'Client added successfully.';
        }
    }
}

// Handle delete
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("SELECT logo FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    $row = $stmt->fetch();
    if ($row) {
        if (!empty($row['logo']) && file_exists(__DIR__ . '/../uploads/clients/' . $row['logo'])) {
            @unlink(__DIR__ . '/../uploads/clients/' . $row['logo']);
        }
        $pdo->prepare("DELETE FROM clients WHERE id = ?")->execute([$id]);
        $success = 'Client removed.';
    }
}

// Handle toggle status
if (isset($_GET['toggle'])) {
    $id = (int)$_GET['toggle'];
    $pdo->prepare("UPDATE clients SET status = 1 - status WHERE id = ?")->execute([$id]);
    header('Location: clients.php');
    exit;
}

// Handle reorder (move up / down) — controls the order clients scroll past in the homepage slider
if (isset($_GET['move'])) {
    $id = (int)$_GET['move'];
    $dir = $_GET['dir'] ?? '';
    $stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();
    if ($current) {
        $op = $dir === 'up' ? '<' : '>';
        $orderDir = $dir === 'up' ? 'DESC' : 'ASC';
        $neighborStmt = $pdo->prepare("SELECT * FROM clients WHERE sort_order $op ? ORDER BY sort_order $orderDir LIMIT 1");
        $neighborStmt->execute([$current['sort_order']]);
        $neighbor = $neighborStmt->fetch();
        if ($neighbor) {
            $pdo->prepare("UPDATE clients SET sort_order = ? WHERE id = ?")->execute([$neighbor['sort_order'], $current['id']]);
            $pdo->prepare("UPDATE clients SET sort_order = ? WHERE id = ?")->execute([$current['sort_order'], $neighbor['id']]);
        }
    }
    header('Location: clients.php');
    exit;
}

$clients = $pdo->query("SELECT * FROM clients ORDER BY sort_order ASC")->fetchAll();

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-wide">
    <div class="card-head"><h3>Add New Client</h3></div>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group"><label>Client / Company Name *</label><input type="text" name="name" required placeholder="e.g. Nessotech"></div>
            <div class="form-group"><label>Website URL</label><input type="text" name="website_url" placeholder="https://example.com"></div>
            <div class="form-group full">
                <label>Logo (optional — shown as a text badge if left blank)</label>
                <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.svg" data-preview="#logoPreview">
                <p class="hint">A PNG with a transparent background looks best. Max 2MB.</p>
                <img id="logoPreview" class="thumb-sm" style="margin-top:10px; display:none;" onload="this.style.display='inline-block'">
            </div>
        </div>
        <div class="form-actions"><button type="submit" name="add_client" class="btn btn-primary">Add Client</button></div>
    </form>
</div>

<div class="card card-wide">
    <div class="card-head"><h3>All Clients (<?= count($clients) ?>) — scroll order on the homepage logo strip</h3></div>
    <?php if (empty($clients)): ?>
        <div class="empty-state"><div class="icon">🤝</div>No clients yet. Add your first one above.</div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Logo</th><th>Name</th><th>Website</th><th>Order</th><th>Status</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($clients as $c): ?>
            <tr>
                <td>
                    <?php if (!empty($c['logo']) && file_exists(__DIR__ . '/../uploads/clients/' . $c['logo'])): ?>
                        <img class="thumb-sm" src="../uploads/clients/<?= htmlspecialchars($c['logo']) ?>" alt="">
                    <?php else: ?>
                        <span class="badge-pill badge-gray">Text badge</span>
                    <?php endif; ?>
                </td>
                <td><strong><?= htmlspecialchars($c['name']) ?></strong></td>
                <td><?= $c['website_url'] ? '<a href="' . htmlspecialchars($c['website_url']) . '" target="_blank">' . htmlspecialchars($c['website_url']) . '</a>' : '—' ?></td>
                <td class="table-actions">
                    <a href="?move=<?= $c['id'] ?>&dir=up" class="btn btn-outline btn-sm" title="Move up">↑</a>
                    <a href="?move=<?= $c['id'] ?>&dir=down" class="btn btn-outline btn-sm" title="Move down">↓</a>
                </td>
                <td><a href="?toggle=<?= $c['id'] ?>" class="badge-pill <?= $c['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $c['status'] ? 'Active' : 'Hidden' ?></a></td>
                <td class="table-actions">
                    <a href="client-edit.php?id=<?= $c['id'] ?>" class="btn btn-outline btn-sm">Edit</a>
                    <a href="?delete=<?= $c['id'] ?>" class="btn btn-danger btn-sm" data-confirm="Remove client '<?= htmlspecialchars($c['name']) ?>'?">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Edit Client';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM clients WHERE id = ?");
$stmt->execute([$id]);
$client = $stmt->fetch();

if (!$client) {
    header('Location: clients.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $website_url = trim($_POST['website_url'] ?? '');
    $removeLogo = isset($_POST['remove_logo']);

    if ($name === '') {
        $error = 'Client name is required.';
    } else {
        $logoName = $client['logo'];

        if ($removeLogo && $logoName) {
            if (file_exists(__DIR__ . '/../uploads/clients/' . $logoName)) {
                @unlink(__DIR__ . '/../uploads/clients/' . $logoName);
            }
            $logoName = null;
        }

        if (!empty($_FILES['logo']['name']) && $_FILES['logo']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'svg'];
            $ext = strtolower(pathinfo($_FILES['logo']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['logo']['size'] <= 2 * 1024 * 1024) {
                if ($logoName && file_exists(__DIR__ . '/../uploads/clients/' . $logoName)) {
                    @unlink(__DIR__ . '/../uploads/clients/' . $logoName);
                }
                $logoName = 'client-' . time() . '-' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
                move_uploaded_file($_FILES['logo']['tmp_name'], __DIR__ . '/../uploads/clients/' . $logoName);
            } else {
                $error = 'Logo must be JPG, PNG, WEBP or SVG and under 2MB.';
            }
        }

        if ($error === '') {
            $upd = $pdo->prepare("UPDATE clients SET name = ?, logo = ?, website_url = ? WHERE id = ?");
            $upd->execute([$name, $logoName, $website_url, $id]);
            header('Location: clients.php');
            exit;
        }
    }
}

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-medium">
    <div class="card-head"><h3>Edit Client</h3></div>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group"><label>Client / Company Name *</label><input type="text" name="name" required value="<?= htmlspecialchars($client['name']) ?>"></div>
            <div class="form-group"><label>Website URL</label><input type="text" name="website_url" value="<?= htmlspecialchars($client['website_url'] ?? '') ?>" placeholder="https://example.com"></div>
            <div class="form-group full">
                <label>Logo</label>
                <?php if (!empty($client['logo']) && file_exists(__DIR__ . '/../uploads/clients/' . $client['logo'])): ?>
                    <div style="margin-bottom:10px;">
                        <img class="thumb-sm" src="../uploads/clients/<?= htmlspecialchars($client['logo']) ?>" alt="">
                        <label style="display:inline-flex; align-items:center; gap:6px; font-weight:400; margin-left:10px;">
                            <input type="checkbox" name="remove_logo" value="1" style="width:auto;"> Remove current logo
                        </label>
                    </div>
                <?php endif; ?>
                <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp,.svg" data-preview="#logoPreview">
                <p class="hint">Upload a new file to replace the current logo. Leave blank to keep it. Max 2MB.</p>
                <img id="logoPreview" class="thumb-sm" style="margin-top:10px; display:none;" onload="this.style.display='inline-block'">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="clients.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

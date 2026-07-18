<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Edit Service';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM services WHERE id = ?");
$stmt->execute([$id]);
$service = $stmt->fetch();

if (!$service) {
    header('Location: services.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $removeImage = isset($_POST['remove_image']);

    if ($title === '') {
        $error = 'Service title is required.';
    } else {
        $imageName = $service['image'];

        if ($removeImage && $imageName) {
            if (file_exists(__DIR__ . '/../uploads/services/' . $imageName)) {
                @unlink(__DIR__ . '/../uploads/services/' . $imageName);
            }
            $imageName = null;
        }

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 3 * 1024 * 1024) {
                if ($imageName && file_exists(__DIR__ . '/../uploads/services/' . $imageName)) {
                    @unlink(__DIR__ . '/../uploads/services/' . $imageName);
                }
                $imageName = 'service-' . time() . '-' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/services/' . $imageName);
            } else {
                $error = 'Image must be JPG, PNG or WEBP and under 3MB.';
            }
        }

        if ($error === '') {
            $upd = $pdo->prepare("UPDATE services SET title = ?, description = ?, image = ? WHERE id = ?");
            $upd->execute([$title, $description, $imageName, $id]);
            header('Location: services.php');
            exit;
        }
    }
}

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-medium">
    <div class="card-head"><h3>Edit Service</h3></div>
    <form method="post" enctype="multipart/form-data">
        <div class="form-group"><label>Service Title *</label><input type="text" name="title" required value="<?= htmlspecialchars($service['title']) ?>"></div>
        <div class="form-group"><label>Description</label><textarea name="description" rows="3"><?= htmlspecialchars($service['description']) ?></textarea></div>
        <div class="form-group">
            <label>Service Image</label>
            <?php if (!empty($service['image']) && file_exists(__DIR__ . '/../uploads/services/' . $service['image'])): ?>
                <div style="margin-bottom:10px;">
                    <img class="thumb-sm" src="../uploads/services/<?= htmlspecialchars($service['image']) ?>" alt="">
                    <label style="display:inline-flex; align-items:center; gap:6px; font-weight:400; margin-left:10px;">
                        <input type="checkbox" name="remove_image" value="1" style="width:auto;"> Remove current image
                    </label>
                </div>
            <?php endif; ?>
            <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" data-preview="#svcImgPreview">
            <p class="hint">Upload a new file to replace the current image. Leave blank to keep it. Max 3MB.</p>
            <img id="svcImgPreview" class="thumb-sm" style="margin-top:10px; display:none;" onload="this.style.display='inline-block'">
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="services.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

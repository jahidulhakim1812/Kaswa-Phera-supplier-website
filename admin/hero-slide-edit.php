<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Edit Slide';

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare("SELECT * FROM hero_slides WHERE id = ?");
$stmt->execute([$id]);
$slide = $stmt->fetch();

if (!$slide) {
    header('Location: hero-slides.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $eyebrow = trim($_POST['eyebrow'] ?? '');
    $title = trim($_POST['title'] ?? '');
    $subtitle = trim($_POST['subtitle'] ?? '');
    $cta_text = trim($_POST['cta_text'] ?? 'Explore Products');
    $cta_link = trim($_POST['cta_link'] ?? 'products.php');
    $removeImage = isset($_POST['remove_image']);

    if ($title === '') {
        $error = 'Slide title is required.';
    } else {
        $imageName = $slide['image'];

        if ($removeImage && $imageName) {
            if (file_exists(__DIR__ . '/../uploads/hero/' . $imageName)) {
                @unlink(__DIR__ . '/../uploads/hero/' . $imageName);
            }
            $imageName = null;
        }

        if (!empty($_FILES['image']['name']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowed) && $_FILES['image']['size'] <= 5 * 1024 * 1024) {
                if ($imageName && file_exists(__DIR__ . '/../uploads/hero/' . $imageName)) {
                    @unlink(__DIR__ . '/../uploads/hero/' . $imageName);
                }
                $imageName = 'slide-' . time() . '-' . substr(md5(uniqid()), 0, 6) . '.' . $ext;
                move_uploaded_file($_FILES['image']['tmp_name'], __DIR__ . '/../uploads/hero/' . $imageName);
            } else {
                $error = 'Slide image must be JPG, PNG or WEBP and under 5MB.';
            }
        }

        if ($error === '') {
            $upd = $pdo->prepare("UPDATE hero_slides SET eyebrow = ?, title = ?, subtitle = ?, image = ?, cta_text = ?, cta_link = ? WHERE id = ?");
            $upd->execute([$eyebrow, $title, $subtitle, $imageName, $cta_text, $cta_link, $id]);
            header('Location: hero-slides.php');
            exit;
        }
    }
}

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-wide">
    <div class="card-head"><h3>Edit Slide</h3></div>
    <form method="post" enctype="multipart/form-data">
        <div class="form-grid">
            <div class="form-group"><label>Eyebrow</label><input type="text" name="eyebrow" value="<?= htmlspecialchars($slide['eyebrow'] ?? '') ?>"></div>
            <div class="form-group"><label>Title *</label><input type="text" name="title" required value="<?= htmlspecialchars($slide['title']) ?>"></div>
            <div class="form-group full"><label>Subtitle</label><textarea name="subtitle" rows="2"><?= htmlspecialchars($slide['subtitle'] ?? '') ?></textarea></div>
            <div class="form-group"><label>Button Text</label><input type="text" name="cta_text" value="<?= htmlspecialchars($slide['cta_text'] ?? 'Explore Products') ?>"></div>
            <div class="form-group"><label>Button Link</label><input type="text" name="cta_link" value="<?= htmlspecialchars($slide['cta_link'] ?? 'products.php') ?>"></div>
            <div class="form-group full">
                <label>Background Photo</label>
                <?php if (!empty($slide['image']) && file_exists(__DIR__ . '/../uploads/hero/' . $slide['image'])): ?>
                    <div style="margin-bottom:10px;">
                        <img src="../uploads/hero/<?= htmlspecialchars($slide['image']) ?>" style="width:220px; height:120px; object-fit:cover; border-radius:8px; border:1px solid var(--steel-light);">
                        <label style="display:inline-flex; align-items:center; gap:6px; font-weight:400; margin-left:14px;">
                            <input type="checkbox" name="remove_image" style="width:auto;"> Remove current photo
                        </label>
                    </div>
                <?php endif; ?>
                <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" data-preview="#slidePreview">
                <p class="hint">Upload to replace. Max 5MB. Leave blank to keep the current photo (or the default brand background if none).</p>
                <img id="slidePreview" class="thumb-sm" style="margin-top:10px; display:none; width:120px; height:70px; object-fit:cover;" onload="this.style.display='inline-block'">
            </div>
        </div>
        <div class="form-actions">
            <button type="submit" class="btn btn-primary">Save Changes</button>
            <a href="hero-slides.php" class="btn btn-outline">Cancel</a>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

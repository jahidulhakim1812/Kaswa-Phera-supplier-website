<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Change Password';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current = $_POST['current_password'] ?? '';
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE id = ?");
    $stmt->execute([$_SESSION['admin_id']]);
    $admin = $stmt->fetch();

    if (!$admin || !password_verify($current, $admin['password'])) {
        $error = 'Current password is incorrect.';
    } elseif (strlen($new) < 8) {
        $error = 'New password must be at least 8 characters.';
    } elseif ($new !== $confirm) {
        $error = 'New password and confirmation do not match.';
    } else {
        $hash = password_hash($new, PASSWORD_BCRYPT);
        $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?")->execute([$hash, $admin['id']]);
        $success = 'Password updated successfully.';
    }
}

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-narrow">
    <div class="card-head"><h3>Change Password</h3></div>
    <form method="post">
        <div class="form-group"><label>Current Password</label><input type="password" name="current_password" required></div>
        <div class="form-group"><label>New Password</label><input type="password" name="new_password" required minlength="8"></div>
        <div class="form-group"><label>Confirm New Password</label><input type="password" name="confirm_password" required minlength="8"></div>
        <div class="form-actions"><button type="submit" class="btn btn-primary">Update Password</button></div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

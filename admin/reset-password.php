<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';

if (empty($_SESSION['reset_admin_id'])) {
    header('Location: forgot-password.php');
    exit;
}

$adminId = (int)$_SESSION['reset_admin_id'];
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = trim($_POST['code'] ?? '');
    $new = $_POST['new_password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (empty($code)) {
        $error = 'Please enter the 6‑digit verification code.';
    } else {
        // Check the code with detailed feedback
        $stmt = $pdo->prepare("SELECT id, expires, used FROM password_resets WHERE admin_id = ? AND code = ? ORDER BY id DESC LIMIT 1");
        $stmt->execute([$adminId, $code]);
        $reset = $stmt->fetch();

        if (!$reset) {
            $error = 'No matching code found. Please request a new one.';
        } elseif ($reset['used'] == 1) {
            $error = 'This code has already been used. Please request a new one.';
        } elseif (strtotime($reset['expires']) < time()) {
            $error = 'This code has expired (valid for 15 minutes). Please request a new one.';
        } else {
            // Code is valid – now validate passwords
            if (empty($new) || empty($confirm)) {
                $error = 'Please enter and confirm your new password.';
            } elseif ($new !== $confirm) {
                $error = 'Passwords do not match.';
            } elseif (strlen($new) < 8) {
                $error = 'Password must be at least 8 characters.';
            } else {
                $hashed = password_hash($new, PASSWORD_BCRYPT);
                $pdo->beginTransaction();
                try {
                    $pdo->prepare("UPDATE admin_users SET password = ? WHERE id = ?")->execute([$hashed, $adminId]);
                    $pdo->prepare("UPDATE password_resets SET used = 1 WHERE id = ?")->execute([$reset['id']]);
                    $pdo->commit();
                    $success = 'Password reset successful! Redirecting to login...';
                    unset($_SESSION['reset_admin_id'], $_SESSION['reset_email']);
                    echo '<meta http-equiv="refresh" content="3;url=login.php">';
                } catch (Exception $e) {
                    $pdo->rollBack();
                    $error = 'Something went wrong. Please try again.';
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password | KASWA Admin</title>
    <link rel="icon" href="../assets/images/logo.png">
    <style>
        /* same style as before – keep unchanged */
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#0d3b20; min-height:100vh; display:flex; align-items:center; justify-content:center; }
        .login-screen { position:relative; width:100%; min-height:100vh; display:flex; align-items:center; justify-content:center; background:linear-gradient(135deg,#0a2b18 0%,#14532d 50%,#2a7a3a 100%); padding:20px; overflow:hidden; }
        .login-screen::before, .login-screen::after { content:''; position:absolute; border-radius:50%; background:rgba(255,255,255,0.04); animation:floatBg 10s ease-in-out infinite alternate; }
        .login-screen::before { width:500px; height:500px; top:-150px; right:-100px; }
        .login-screen::after { width:350px; height:350px; bottom:-100px; left:-80px; animation-delay:2s; }
        @keyframes floatBg { 0% { transform:translate(0,0) scale(1); } 100% { transform:translate(60px,40px) scale(1.15); } }
        .login-logo-wrap { text-align:center; animation:logoPop 0.8s cubic-bezier(0.34,1.56,0.64,1) forwards; }
        @keyframes logoPop { 0% { opacity:0; transform:scale(0.5); } 70% { opacity:1; transform:scale(1.08); } 100% { opacity:1; transform:scale(1); } }
        .login-logo-wrap img { height:72px; width:auto; filter:drop-shadow(0 8px 24px rgba(0,0,0,0.25)); display:block; margin:0 auto 8px; }
        .login-card { background:rgba(255,255,255,0.88); backdrop-filter:blur(20px); -webkit-backdrop-filter:blur(20px); border:1px solid rgba(255,255,255,0.25); border-radius:24px; padding:40px 36px 32px; max-width:400px; width:100%; box-shadow:0 30px 60px rgba(0,0,0,0.3); text-align:center; transition:transform 0.3s ease, box-shadow 0.3s ease; opacity:0; transform:translateY(20px); animation:cardSlide 0.7s ease-out 0.9s forwards; }
        @keyframes cardSlide { 0% { opacity:0; transform:translateY(30px) scale(0.95); } 100% { opacity:1; transform:translateY(0) scale(1); } }
        .login-card:hover { transform:translateY(-4px); box-shadow:0 40px 80px rgba(0,0,0,0.35); }
        .login-card h1 { font-family:'Space Grotesk',sans-serif; font-size:1.5rem; color:#0d3b20; margin-bottom:2px; letter-spacing:-0.02em; }
        .login-card .sub { color:#4a5a52; font-size:0.9rem; margin-bottom:24px; }
        .login-card .form-group { text-align:left; margin-bottom:18px; }
        .login-card label { display:block; font-size:0.82rem; font-weight:600; color:#1b2c24; margin-bottom:5px; }
        .login-card input { width:100%; padding:12px 16px; border:1.5px solid rgba(0,0,0,0.08); border-radius:12px; font-size:0.95rem; background:rgba(255,255,255,0.6); transition:border-color 0.2s, box-shadow 0.2s; }
        .login-card input:focus { outline:none; border-color:#4d9e35; box-shadow:0 0 0 4px rgba(77,158,53,0.15); }
        .btn-block { width:100%; padding:14px; border-radius:12px; font-size:1rem; font-weight:600; background:#14532d; color:#fff; border:none; cursor:pointer; transition:background 0.2s, transform 0.1s; }
        .btn-block:hover { background:#0d3b20; }
        .btn-block:active { transform:scale(0.98); }
        .back-link { display:block; margin-top:12px; font-size:0.85rem; color:#4a7a5a; text-decoration:none; font-weight:500; transition:color 0.2s; }
        .back-link:hover { color:#0d3b20; text-decoration:underline; }
        .alert { padding:12px 16px; border-radius:12px; font-size:0.85rem; margin-bottom:20px; }
        .alert-success { background:#e6f4ea; color:#1e7b34; border:1px solid #b8e4c4; }
        .alert-error { background:#fdecea; color:#b3261e; border:1px solid #f6c6c2; }
        @media (max-width:480px) { .login-card { padding:30px 20px 24px; } .login-logo-wrap img { height:56px; } }
    </style>
</head>
<body>
<div class="login-screen">
    <div class="login-card">
        <div class="login-logo-wrap"><img src="../assets/images/logo.png" alt="KASWA Tech"></div>

        <?php if ($success): ?>
            <h1>✅ Success!</h1>
            <p class="sub"><?= $success ?></p>
            <a href="login.php" class="btn-block" style="display:inline-block; text-decoration:none; margin-top:10px;">Go to Login</a>
        <?php else: ?>
            <h1>Reset Password</h1>
            <p class="sub">Enter the 6‑digit code from your email and set a new password</p>
            <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
            <form method="post">
                <div class="form-group">
                    <label>Verification Code</label>
                    <input type="text" name="code" required autofocus maxlength="6" placeholder="e.g. 123456">
                </div>
                <div class="form-group">
                    <label>New Password</label>
                    <input type="password" name="new_password" required minlength="8">
                </div>
                <div class="form-group">
                    <label>Confirm Password</label>
                    <input type="password" name="confirm_password" required minlength="8">
                </div>
                <button type="submit" class="btn-block">Reset Password</button>
            </form>
            <a href="forgot-password.php" class="back-link">← Request a new code</a>
        <?php endif; ?>
        <a href="login.php" class="back-link" style="margin-top:12px;">← Back to Login</a>
    </div>
</div>
</body>
</html>
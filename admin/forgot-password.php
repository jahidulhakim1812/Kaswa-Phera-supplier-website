<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/db.php';

// ---------- Mail function (SMTP + fallback) ----------
function sendMail($to, $subject, $body) {
    $mailerLoaded = false;
    // Try Composer autoload
    if (file_exists(__DIR__ . '/../vendor/autoload.php')) {
        require_once __DIR__ . '/../vendor/autoload.php';
        $mailerLoaded = true;
    } 
    // Try manual includes in parent folder
    elseif (file_exists(__DIR__ . '/../PHPMailer/Exception.php') &&
            file_exists(__DIR__ . '/../PHPMailer/PHPMailer.php') &&
            file_exists(__DIR__ . '/../PHPMailer/SMTP.php')) {
        require_once __DIR__ . '/../PHPMailer/Exception.php';
        require_once __DIR__ . '/../PHPMailer/PHPMailer.php';
        require_once __DIR__ . '/../PHPMailer/SMTP.php';
        $mailerLoaded = true;
    } 
    // Try manual includes in the same folder
    elseif (file_exists(__DIR__ . '/PHPMailer/Exception.php') &&
            file_exists(__DIR__ . '/PHPMailer/PHPMailer.php') &&
            file_exists(__DIR__ . '/PHPMailer/SMTP.php')) {
        require_once __DIR__ . '/PHPMailer/Exception.php';
        require_once __DIR__ . '/PHPMailer/PHPMailer.php';
        require_once __DIR__ . '/PHPMailer/SMTP.php';
        $mailerLoaded = true;
    }

    if ($mailerLoaded && class_exists('PHPMailer\PHPMailer\PHPMailer')) {
        try {
            $mail = new PHPMailer\PHPMailer\PHPMailer(true);
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = 'artechsolution.online@gmail.com';   // your Gmail
            $mail->Password   = 'giwr wrcr mnyi lkpf';               // your app password
            $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port       = 587;
            $mail->setFrom('noreply@artechsolution.com', 'AR Tech Admin');
            $mail->addAddress($to);
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = nl2br($body);
            $mail->AltBody = strip_tags($body);
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("SMTP error: " . $e->getMessage());
        }
    }

    // Fallback to native PHP mail()
    $headers = "From: noreply@artechsolution.com\r\nReply-To: noreply@artechsolution.com\r\n";
    return mail($to, $subject, $body, $headers);
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    if (empty($email)) {
        $error = 'Please enter your email or username.';
    } else {
        $stmt = $pdo->prepare("SELECT id, email, username FROM admin_users WHERE email = ? OR username = ? LIMIT 1");
        $stmt->execute([$email, $email]);
        $admin = $stmt->fetch();
        if (!$admin) {
            $error = 'No account found with that email or username.';
        } else {
            // Invalidate old unused codes
            $pdo->prepare("UPDATE password_resets SET used = 1 WHERE admin_id = ? AND used = 0")->execute([$admin['id']]);

            // Generate 6‑digit OTP
            $code = sprintf("%06d", mt_rand(1, 999999));
            $expires = date('Y-m-d H:i:s', strtotime('+15 minutes'));
            $stmt = $pdo->prepare("INSERT INTO password_resets (admin_id, code, expires) VALUES (?, ?, ?)");
            $stmt->execute([$admin['id'], $code, $expires]);

            // Send email
            $subject = "🔐 Password Reset OTP – KASWA Admin";
            $body = "Hello,\n\nYou requested to reset your password for your KASWA Tech admin account.\n\n"
                  . "Your 6‑digit verification code is:\n\n"
                  . $code . "\n\n"
                  . "This code is valid for 15 minutes.\n\n"
                  . "If you didn't request this, please ignore this email.";
            $sent = sendMail($admin['email'], $subject, $body);

            if ($sent) {
                $_SESSION['reset_admin_id'] = $admin['id'];
                $_SESSION['reset_email'] = $admin['email'];
                header('Location: reset-password.php');
                exit;
            } else {
                $error = 'Could not send the email. Please try again later.';
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
    <title>Forgot Password | KASWA Admin</title>
    <link rel="icon" href="../assets/images/logo.png">
    <style>
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
        <h1>Reset Password</h1>
        <p class="sub">Enter your email or username to receive a 6‑digit code</p>
        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>
        <?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
        <form method="post">
            <div class="form-group">
                <label>Email or Username</label>
                <input type="text" name="email" required autofocus value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
            </div>
            <button type="submit" class="btn-block">Send OTP</button>
        </form>
        <a href="login.php" class="back-link">← Back to Login</a>
    </div>
</div>
</body>
</html>
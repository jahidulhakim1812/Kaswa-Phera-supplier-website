<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/../config/db.php';

if (!empty($_SESSION['admin_id'])) {
    header('Location: dashboard.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        $error = 'Please enter both username and password.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM admin_users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['admin_name'] = $admin['name'];
            $_SESSION['admin_role'] = $admin['role'];
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid username or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login | KASWA Tech</title>
<link rel="icon" href="../assets/images/logo.png">
<link rel="stylesheet" href="assets/css/admin.css">
<style>
/* ---------- Reset & Base ---------- */
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: 'Inter', sans-serif;
    background: #0d3b20;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
}

/* ---------- Animated Background ---------- */
.login-screen {
    position: relative;
    width: 100%;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #0a2b18 0%, #14532d 50%, #2a7a3a 100%);
    padding: 20px;
    overflow: hidden;
}
.login-screen::before,
.login-screen::after {
    content: '';
    position: absolute;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    animation: floatBg 10s ease-in-out infinite alternate;
}
.login-screen::before {
    width: 500px;
    height: 500px;
    top: -150px;
    right: -100px;
}
.login-screen::after {
    width: 350px;
    height: 350px;
    bottom: -100px;
    left: -80px;
    animation-delay: 2s;
}
@keyframes floatBg {
    0% { transform: translate(0, 0) scale(1); }
    100% { transform: translate(60px, 40px) scale(1.15); }
}

/* ---------- Logo (pop‑up with bounce) ---------- */
.login-logo-wrap {
    text-align: center;
    animation: logoPop 0.8s cubic-bezier(0.34, 1.56, 0.64, 1) forwards;
}
@keyframes logoPop {
    0% { opacity: 0; transform: scale(0.5); }
    70% { opacity: 1; transform: scale(1.08); }
    100% { opacity: 1; transform: scale(1); }
}
.login-logo-wrap img {
    height: 72px;
    width: auto;
    filter: drop-shadow(0 8px 24px rgba(0,0,0,0.25));
    display: block;
    margin: 0 auto 8px;
}

/* ---------- Login Card (slides in after logo) ---------- */
.login-card {
    background: rgba(255, 255, 255, 0.88);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.25);
    border-radius: 24px;
    padding: 40px 36px 32px;
    max-width: 400px;
    width: 100%;
    box-shadow: 0 30px 60px rgba(0, 0, 0, 0.3);
    text-align: center;
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    opacity: 0;
    transform: translateY(20px);
    animation: cardSlide 0.7s ease-out 0.9s forwards;
}
@keyframes cardSlide {
    0% { opacity: 0; transform: translateY(30px) scale(0.95); }
    100% { opacity: 1; transform: translateY(0) scale(1); }
}
.login-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 40px 80px rgba(0, 0, 0, 0.35);
}

.login-card h1 {
    font-family: 'Space Grotesk', sans-serif;
    font-size: 1.5rem;
    color: #0d3b20;
    margin-bottom: 2px;
    letter-spacing: -0.02em;
}
.login-card .sub {
    color: #4a5a52;
    font-size: 0.9rem;
    margin-bottom: 24px;
}

/* ---------- Form ---------- */
.login-card .form-group {
    text-align: left;
    margin-bottom: 18px;
}
.login-card label {
    display: block;
    font-size: 0.82rem;
    font-weight: 600;
    color: #1b2c24;
    margin-bottom: 5px;
}

/* ---------- Password toggle wrapper ---------- */
.password-toggle-wrapper {
    position: relative;
}
.password-toggle-wrapper input {
    width: 100%;
    padding: 12px 44px 12px 16px; /* extra right padding for the button */
    border: 1.5px solid rgba(0,0,0,0.08);
    border-radius: 12px;
    font-size: 0.95rem;
    background: rgba(255,255,255,0.6);
    transition: border-color 0.2s, box-shadow 0.2s;
}
.password-toggle-wrapper input:focus {
    outline: none;
    border-color: #4d9e35;
    box-shadow: 0 0 0 4px rgba(77, 158, 53, 0.15);
}
.toggle-password-btn {
    position: absolute;
    right: 12px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: #4a5a52;
    transition: color 0.2s;
}
.toggle-password-btn:hover {
    color: #0d3b20;
}
.toggle-password-btn svg {
    width: 20px;
    height: 20px;
    stroke: currentColor;
    fill: none;
    stroke-width: 2;
    stroke-linecap: round;
    stroke-linejoin: round;
}
.toggle-password-btn .eye-open,
.toggle-password-btn .eye-closed {
    display: block;
}
.toggle-password-btn.show .eye-open {
    display: none;
}
.toggle-password-btn.show .eye-closed {
    display: block;
}
.toggle-password-btn:not(.show) .eye-closed {
    display: none;
}

.btn-block {
    width: 100%;
    padding: 14px;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 600;
    background: #14532d;
    color: #fff;
    border: none;
    cursor: pointer;
    transition: background 0.2s, transform 0.1s;
}
.btn-block:hover {
    background: #0d3b20;
}
.btn-block:active {
    transform: scale(0.98);
}

/* ---------- Forgot password link ---------- */
.forgot-link {
    display: block;
    margin-top: 12px;
    font-size: 0.85rem;
    color: #4a7a5a;
    text-decoration: none;
    font-weight: 500;
    transition: color 0.2s;
}
.forgot-link:hover {
    color: #0d3b20;
    text-decoration: underline;
}

.login-hint {
    margin-top: 20px;
    font-size: 0.75rem;
    color: #6b7a73;
    font-family: 'IBM Plex Mono', monospace;
    background: rgba(255,255,255,0.3);
    padding: 6px 14px;
    border-radius: 30px;
    display: inline-block;
}
.alert {
    padding: 12px 16px;
    border-radius: 12px;
    font-size: 0.85rem;
    margin-bottom: 20px;
}
.alert-error {
    background: #fdecea;
    color: #b3261e;
    border: 1px solid #f6c6c2;
}

/* ---------- Responsive ---------- */
@media (max-width: 480px) {
    .login-card {
        padding: 30px 20px 24px;
    }
    .login-logo-wrap img {
        height: 56px;
    }
}
</style>
</head>
<body>
<div class="login-screen">
    <div class="login-card">
        <!-- Logo (pop‑up) -->
        <div class="login-logo-wrap">
            <img src="../assets/images/logo.png" alt="KASWA Tech">
        </div>
        <h1>Admin Panel</h1>
        <p class="sub">Sign in to manage your site</p>

        <?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

        <form method="post">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" required autofocus value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label>Password</label>
                <div class="password-toggle-wrapper">
                    <input type="password" name="password" id="password" required>
                    <button type="button" class="toggle-password-btn" id="togglePasswordBtn" aria-label="Show password">
                        <!-- Eye open SVG (visible when password hidden) -->
                        <svg class="eye-open" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                        <!-- Eye closed SVG (visible when password shown) -->
                        <svg class="eye-closed" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                            <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                            <line x1="1" y1="1" x2="23" y2="23"/>
                        </svg>
                    </button>
                </div>
            </div>
            <button type="submit" class="btn-block">Sign In</button>
        </form>

        <!-- Forgot Password link -->
        <a href="forgot-password.php" class="forgot-link">Forgot Password?</a>

        
    </div>
</div>

<script>
    (function() {
        const toggleBtn = document.getElementById('togglePasswordBtn');
        const passwordInput = document.getElementById('password');

        if (toggleBtn && passwordInput) {
            toggleBtn.addEventListener('click', function() {
                const isPassword = passwordInput.type === 'password';
                passwordInput.type = isPassword ? 'text' : 'password';
                this.classList.toggle('show', !isPassword);
                // Update aria-label
                this.setAttribute('aria-label', isPassword ? 'Hide password' : 'Show password');
            });
        }
    })();
</script>
</body>
</html>
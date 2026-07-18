<?php
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/../config/settings.php';
$current = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($page_title) ? clean($page_title) . ' | ' : '' ?><?= clean(setting('company_name', 'KASWA Tech')) ?></title>
<meta name="description" content="<?= clean(setting('tagline', 'Chemical manufacturing equipment and laboratory instruments')) ?>">
<link rel="icon" href="assets/images/logo.png">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body<?= $current === 'index.php' ? ' class="no-topbar"' : '' ?>>

<?php if ($current !== 'index.php'): ?>

<?php endif; ?>

<header class="site-header">
    <div class="nav-wrap">
        <a href="index.php" class="brand">
            <img src="assets/images/logo.png" alt="<?= clean(setting('company_name')) ?> logo">
            <span class="brand-text">
                <strong><?= clean(setting('company_name', 'KASWA Tech')) ?></strong>
                <span><?= clean(setting('tagline', 'Chemical & Lab Equipment')) ?></span>
            </span>
        </a>
        <nav class="main-nav" id="mainNav">
            <ul>
                <li><a href="index.php" class="<?= $current === 'index.php' ? 'active' : '' ?>">Home</a></li>
                <li><a href="about.php" class="<?= $current === 'about.php' ? 'active' : '' ?>">About</a></li>
                <li><a href="products.php" class="<?= in_array($current, ['products.php','product-detail.php']) ? 'active' : '' ?>">Products</a></li>
                <li><a href="services.php" class="<?= $current === 'services.php' ? 'active' : '' ?>">Services</a></li>
                <li><a href="contact.php" class="<?= $current === 'contact.php' ? 'active' : '' ?>">Contact</a></li>
            </ul>
        </nav>

        <div class="nav-actions">
            <form class="search-form" action="products.php" method="get" role="search">
                <svg class="search-form-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                <input type="text" name="q" placeholder="Search products & categories..." aria-label="Search products and categories" value="<?= isset($_GET['q']) ? clean($_GET['q']) : '' ?>">
                <button type="submit" aria-label="Submit search">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                </button>
            </form>
            <button class="nav-toggle" id="navToggle" aria-label="Toggle menu"><span></span><span></span><span></span></button>
        </div>
    </div>

    <!-- Compact search bar shown only on small screens, below the header row -->
    <div class="mobile-search">
        <form class="search-form" action="products.php" method="get" role="search">
            <svg class="search-form-icon" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            <input type="text" name="q" placeholder="Search products & categories..." aria-label="Search products and categories" value="<?= isset($_GET['q']) ? clean($_GET['q']) : '' ?>">
            <button type="submit" aria-label="Submit search">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
            </button>
        </form>
    </div>
</header>

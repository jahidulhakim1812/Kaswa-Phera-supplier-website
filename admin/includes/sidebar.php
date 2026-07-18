<?php $current = basename($_SERVER['SCRIPT_NAME']); ?>
<aside class="sidebar">
    <div class="sidebar-brand">
        <img src="../assets/images/logo.png" alt="KASWA">
        <span>KASWA Admin</span>
    </div>
    <nav class="sidebar-nav">
        <a href="dashboard.php" class="<?= $current === 'dashboard.php' ? 'active' : '' ?>"><span class="icon">📊</span><span class="label">Dashboard</span></a>

        <div class="sidebar-section-label">Catalogue</div>
        <a href="categories.php" class="<?= $current === 'categories.php' ? 'active' : '' ?>"><span class="icon">🗂️</span><span class="label">Categories</span></a>
        <a href="products.php" class="<?= in_array($current, ['products.php','product-add.php','product-edit.php']) ? 'active' : '' ?>"><span class="icon">⚙️</span><span class="label">Products</span></a>
        <a href="services.php" class="<?= in_array($current, ['services.php','service-edit.php']) ? 'active' : '' ?>"><span class="icon">🛎️</span><span class="label">Services</span></a>

        <div class="sidebar-section-label">Marketing</div>
        <a href="hero-slides.php" class="<?= in_array($current, ['hero-slides.php','hero-slide-edit.php']) ? 'active' : '' ?>"><span class="icon">🖼️</span><span class="label">Hero Slider</span></a>
        <a href="clients.php" class="<?= $current === 'clients.php' ? 'active' : '' ?>"><span class="icon">🤝</span><span class="label">Clients / Logos</span></a>

        <div class="sidebar-section-label">Customers</div>
        <a href="inquiries.php" class="<?= $current === 'inquiries.php' ? 'active' : '' ?>"><span class="icon">📨</span><span class="label">Inquiries</span></a>

        <div class="sidebar-section-label">Site</div>
        <a href="settings.php" class="<?= $current === 'settings.php' ? 'active' : '' ?>"><span class="icon">🛠️</span><span class="label">Site Settings</span></a>
        <a href="change-password.php" class="<?= $current === 'change-password.php' ? 'active' : '' ?>"><span class="icon">🔒</span><span class="label">Password</span></a>
        <a href="../index.php" target="_blank"><span class="icon">🌐</span><span class="label">View Website</span></a>
    </nav>
    <div class="sidebar-footer">
        <a href="logout.php"><span class="icon">⏻</span><span class="label">Sign Out</span></a>
    </div>
</aside>

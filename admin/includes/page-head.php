<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($page_title ?? 'Admin') ?> | KASWA Admin</title>
<link rel="icon" href="../assets/images/logo.png">
<link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
<div class="admin-shell" id="adminShell">
    <?php require_once __DIR__ . '/sidebar.php'; ?>
    <div class="main-content">
        <div class="topbar">
            <div class="topbar-left">
                <button class="sidebar-toggle" id="sidebarToggle" aria-label="Toggle sidebar">☰</button>
                <h1><?= htmlspecialchars($page_title ?? 'Dashboard') ?></h1>
            </div>
            <div class="topbar-user">
                <div class="avatar"><?= strtoupper(substr(admin_name(), 0, 1)) ?></div>
                <span><?= htmlspecialchars(admin_name()) ?></span>
            </div>
        </div>
        <div class="page-body">

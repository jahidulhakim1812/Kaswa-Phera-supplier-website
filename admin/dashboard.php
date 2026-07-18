<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Dashboard';

$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$totalInquiries = $pdo->query("SELECT COUNT(*) FROM inquiries")->fetchColumn();
$newInquiries = $pdo->query("SELECT COUNT(*) FROM inquiries WHERE status = 'new'")->fetchColumn();

$recentInquiries = $pdo->query("SELECT * FROM inquiries ORDER BY created_at DESC LIMIT 6")->fetchAll();
$recentProducts = $pdo->query("SELECT p.*, c.name AS category_name FROM products p JOIN categories c ON c.id = p.category_id ORDER BY p.created_at DESC LIMIT 5")->fetchAll();

require_once __DIR__ . '/includes/page-head.php';
?>

<div class="stat-grid">
    <div class="stat-card">
        <div class="stat-icon">⚙️</div>
        <strong><?= (int)$totalProducts ?></strong>
        <span>Total Products</span>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🗂️</div>
        <strong><?= (int)$totalCategories ?></strong>
        <span>Categories</span>
    </div>
    <div class="stat-card">
        <div class="stat-icon">📨</div>
        <strong><?= (int)$totalInquiries ?></strong>
        <span>Total Inquiries</span>
    </div>
    <div class="stat-card">
        <div class="stat-icon">🔔</div>
        <strong><?= (int)$newInquiries ?></strong>
        <span>New / Unread Inquiries</span>
    </div>
</div>

<div class="card">
    <div class="card-head">
        <h3>Recent Inquiries</h3>
        <a href="inquiries.php" class="btn btn-outline btn-sm">View All</a>
    </div>
    <?php if (empty($recentInquiries)): ?>
        <div class="empty-state"><div class="icon">📭</div>No inquiries yet.</div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Name</th><th>Subject</th><th>Contact</th><th>Status</th><th>Received</th></tr></thead>
        <tbody>
        <?php foreach ($recentInquiries as $inq): ?>
            <tr>
                <td><?= htmlspecialchars($inq['name']) ?></td>
                <td><?= htmlspecialchars($inq['subject']) ?></td>
                <td><?= htmlspecialchars($inq['email']) ?></td>
                <td>
                    <?php
                    $badgeClass = ['new' => 'badge-blue', 'in_progress' => 'badge-amber', 'resolved' => 'badge-green'][$inq['status']] ?? 'badge-gray';
                    ?>
                    <span class="badge-pill <?= $badgeClass ?>"><?= htmlspecialchars(str_replace('_', ' ', $inq['status'])) ?></span>
                </td>
                <td><?= date('d M Y', strtotime($inq['created_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<div class="card">
    <div class="card-head">
        <h3>Recently Added Products</h3>
        <a href="products.php" class="btn btn-outline btn-sm">Manage Products</a>
    </div>
    <?php if (empty($recentProducts)): ?>
        <div class="empty-state"><div class="icon">📦</div>No products yet.</div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th></th><th>Name</th><th>Category</th><th>Model No.</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($recentProducts as $p): ?>
            <tr>
                <td><img class="thumb-sm" src="../<?= htmlspecialchars(product_thumb_admin($p['image'])) ?>" alt=""></td>
                <td><?= htmlspecialchars($p['name']) ?></td>
                <td><?= htmlspecialchars($p['category_name']) ?></td>
                <td><?= htmlspecialchars($p['model_no']) ?></td>
                <td><span class="badge-pill <?= $p['status'] ? 'badge-green' : 'badge-gray' ?>"><?= $p['status'] ? 'Active' : 'Hidden' ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

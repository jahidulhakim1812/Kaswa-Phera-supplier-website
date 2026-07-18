<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Inquiries';

if (isset($_POST['update_status'])) {
    $id = (int)$_POST['id'];
    $status = in_array($_POST['status'], ['new','in_progress','resolved']) ? $_POST['status'] : 'new';
    $pdo->prepare("UPDATE inquiries SET status = ? WHERE id = ?")->execute([$status, $id]);
    header('Location: inquiries.php');
    exit;
}
if (isset($_GET['delete'])) {
    $pdo->prepare("DELETE FROM inquiries WHERE id = ?")->execute([(int)$_GET['delete']]);
    header('Location: inquiries.php');
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT i.*, p.name AS product_name FROM inquiries i LEFT JOIN products p ON p.id = i.product_id WHERE 1=1";
$params = [];
if (in_array($statusFilter, ['new','in_progress','resolved'])) {
    $sql .= " AND i.status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY i.created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$inquiries = $stmt->fetchAll();

require_once __DIR__ . '/includes/page-head.php';
?>

<div class="card">
    <div class="card-head">
        <h3>All Inquiries (<?= count($inquiries) ?>)</h3>
        <div style="display:flex; gap:8px;">
            <a href="inquiries.php" class="btn btn-sm <?= $statusFilter === '' ? 'btn-primary' : 'btn-outline' ?>">All</a>
            <a href="?status=new" class="btn btn-sm <?= $statusFilter === 'new' ? 'btn-primary' : 'btn-outline' ?>">New</a>
            <a href="?status=in_progress" class="btn btn-sm <?= $statusFilter === 'in_progress' ? 'btn-primary' : 'btn-outline' ?>">In Progress</a>
            <a href="?status=resolved" class="btn btn-sm <?= $statusFilter === 'resolved' ? 'btn-primary' : 'btn-outline' ?>">Resolved</a>
        </div>
    </div>

    <?php if (empty($inquiries)): ?>
        <div class="empty-state"><div class="icon">📭</div>No inquiries found.</div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Name</th><th>Contact</th><th>Subject / Product</th><th>Message</th><th>Status</th><th>Date</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($inquiries as $inq): ?>
            <tr>
                <td><strong><?= htmlspecialchars($inq['name']) ?></strong><?= $inq['company'] ? '<br><span style="color:var(--steel); font-size:0.8rem;">' . htmlspecialchars($inq['company']) . '</span>' : '' ?></td>
                <td><?= htmlspecialchars($inq['email']) ?><?= $inq['phone'] ? '<br>' . htmlspecialchars($inq['phone']) : '' ?></td>
                <td><?= htmlspecialchars($inq['subject']) ?><?= $inq['product_name'] ? '<br><span style="color:var(--leaf); font-size:0.8rem;">' . htmlspecialchars($inq['product_name']) . '</span>' : '' ?></td>
                <td style="max-width:220px;"><?= nl2br(htmlspecialchars(mb_strimwidth($inq['message'], 0, 140, '...'))) ?></td>
                <td>
                    <form method="post" style="display:flex; gap:6px;">
                        <input type="hidden" name="id" value="<?= $inq['id'] ?>">
                        <select name="status" onchange="this.form.submit()" style="padding:6px 8px; border-radius:6px; border:1px solid var(--steel-light); font-size:0.78rem;">
                            <option value="new" <?= $inq['status'] === 'new' ? 'selected' : '' ?>>New</option>
                            <option value="in_progress" <?= $inq['status'] === 'in_progress' ? 'selected' : '' ?>>In Progress</option>
                            <option value="resolved" <?= $inq['status'] === 'resolved' ? 'selected' : '' ?>>Resolved</option>
                        </select>
                        <input type="hidden" name="update_status" value="1">
                    </form>
                </td>
                <td><?= date('d M Y', strtotime($inq['created_at'])) ?></td>
                <td class="table-actions">
                    <a href="?delete=<?= $inq['id'] ?>" class="btn btn-danger btn-sm" data-confirm="Delete this inquiry?">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

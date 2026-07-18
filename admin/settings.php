<?php
require_once __DIR__ . '/includes/auth.php';
$page_title = 'Site Settings';

// Known/common fields shown with friendly labels in the main form above.
$fields = [
    'company_name' => 'Company Name',
    'tagline' => 'Tagline',
    'about_text' => 'About Text',
    'corporate_address' => 'Corporate Address',
    'registered_address' => 'Registered Address',
    'phone_1' => 'Phone / WhatsApp 1',
    'phone_2' => 'Phone / WhatsApp 2',
    'email' => 'Email',
    'website' => 'Website',
    'whatsapp_number' => 'Floating WhatsApp Number (digits only, with country code)',
    'linkedin_url' => 'Floating LinkedIn URL',
];

// Fallback values shown when a field has never been saved yet, so the form
// never looks blank on a fresh install. These match the same defaults used
// elsewhere on the public site (header, footer, floating buttons).
$defaults = [
    'company_name' => 'KASWA Tech',
    'tagline' => 'Engineering chemical and laboratory solutions.',
    'about_text' => 'KASWA Tech designs, builds and supports chemical processing and laboratory equipment for manufacturing and research facilities.',
    'corporate_address' => 'Corporate office address not set yet.',
    'registered_address' => 'Registered office address not set yet.',
    'phone_1' => '+880 1670 974843',
    'phone_2' => '',
    'email' => 'info@kaswatech.net',
    'website' => 'https://www.kaswatech.net',
    'whatsapp_number' => '8801670974843',
    'linkedin_url' => 'https://www.linkedin.com/company/kaswatech',
];

$success = '';
$error = '';

// Update the known company-info fields
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_settings'])) {
    foreach ($fields as $key => $label) {
        $value = trim($_POST[$key] ?? '');
        $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)
                                ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
        $stmt->execute([$key, $value]);
    }
    $success = 'Settings updated successfully.';
}

// Add a brand-new custom setting key/value pair
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_setting'])) {
    $newKey = trim($_POST['new_key'] ?? '');
    $newValue = trim($_POST['new_value'] ?? '');
    $normalizedKey = strtolower(preg_replace('/[^a-zA-Z0-9_]+/', '_', $newKey));
    $normalizedKey = trim($normalizedKey, '_');

    if ($normalizedKey === '') {
        $error = 'Setting key is required.';
    } else {
        $check = $pdo->prepare("SELECT id FROM settings WHERE setting_key = ?");
        $check->execute([$normalizedKey]);
        if ($check->fetch()) {
            $error = 'A setting with that key already exists. Delete it first or edit it below.';
        } else {
            $ins = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?, ?)");
            $ins->execute([$normalizedKey, $newValue]);
            $success = 'New setting "' . $normalizedKey . '" added.';
        }
    }
}

// Delete a setting entirely (old/unused settings can be removed here)
if (isset($_GET['delete_setting'])) {
    $keyToDelete = trim($_GET['delete_setting']);
    $del = $pdo->prepare("DELETE FROM settings WHERE setting_key = ?");
    $del->execute([$keyToDelete]);
    header('Location: settings.php?deleted=1');
    exit;
}
if (isset($_GET['deleted'])) {
    $success = 'Setting deleted.';
}

// Always re-read the full, current list of settings from the database,
// so the page reflects exactly what's stored -- old values, edits, and
// anything added or removed below.
$currentSettings = [];
$rows = $pdo->query("SELECT setting_key, setting_value, updated_at FROM settings ORDER BY setting_key ASC")->fetchAll();
foreach ($rows as $row) { $currentSettings[$row['setting_key']] = $row; }

// Any settings in the DB that aren't part of the known company-info fields
// above (e.g. custom keys added below, or legacy keys) get listed separately.
$extraSettings = array_diff_key($currentSettings, $fields);

require_once __DIR__ . '/includes/page-head.php';
?>

<?php if ($success): ?><div class="alert alert-success"><?= htmlspecialchars($success) ?></div><?php endif; ?>
<?php if ($error): ?><div class="alert alert-error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

<div class="card card-wide">
    <div class="card-head"><h3>Company Information</h3></div>
    <form method="post">
        <div class="form-grid">
            <?php foreach ($fields as $key => $label): ?>
                <?php
                    $isSaved = isset($currentSettings[$key]) && $currentSettings[$key]['setting_value'] !== '';
                    $fieldValue = $isSaved ? $currentSettings[$key]['setting_value'] : ($defaults[$key] ?? '');
                ?>
                <div class="form-group <?= in_array($key, ['about_text','corporate_address','registered_address']) ? 'full' : '' ?>">
                    <label><?= htmlspecialchars($label) ?></label>
                    <?php if (in_array($key, ['about_text','corporate_address','registered_address'])): ?>
                        <textarea name="<?= $key ?>" rows="3"><?= htmlspecialchars($fieldValue) ?></textarea>
                    <?php else: ?>
                        <input type="text" name="<?= $key ?>" value="<?= htmlspecialchars($fieldValue) ?>">
                    <?php endif; ?>
                    <?php if (!$isSaved && $fieldValue !== ''): ?>
                        <p class="hint">Showing a default value — not saved yet. Edit and click Save Settings to keep it, or change it.</p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div class="form-actions"><button type="submit" name="save_settings" class="btn btn-primary">Save Settings</button></div>
    </form>
</div>

<div class="card">
    <div class="card-head"><h3>Add New Setting</h3></div>
    <form method="post">
        <div class="form-grid">
            <div class="form-group">
                <label>Setting Key</label>
                <input type="text" name="new_key" placeholder="e.g. facebook_url" required>
                <p class="hint">Letters, numbers and underscores only -- spaces will be converted automatically.</p>
            </div>
            <div class="form-group">
                <label>Setting Value</label>
                <input type="text" name="new_value" placeholder="Value for this setting">
            </div>
        </div>
        <div class="form-actions"><button type="submit" name="add_setting" class="btn btn-primary">Add Setting</button></div>
    </form>
</div>

<div class="card">
    <div class="card-head"><h3>All Settings in Database (<?= count($currentSettings) ?>)</h3></div>
    <?php if (empty($currentSettings)): ?>
        <div class="empty-state"><div class="icon">⚙️</div>No settings stored yet.</div>
    <?php else: ?>
    <table class="data-table">
        <thead><tr><th>Key</th><th>Value</th><th>Last Updated</th><th>Type</th><th>Actions</th></tr></thead>
        <tbody>
        <?php foreach ($currentSettings as $key => $row): ?>
            <tr>
                <td><code><?= htmlspecialchars($key) ?></code></td>
                <td style="max-width: 320px; word-break: break-word;"><?= htmlspecialchars(mb_strimwidth($row['setting_value'] ?? '', 0, 120, '...')) ?></td>
                <td><?= $row['updated_at'] ? date('d M Y, h:i A', strtotime($row['updated_at'])) : '-' ?></td>
                <td>
                    <?php if (isset($fields[$key])): ?>
                        <span class="badge-pill badge-green">Company field</span>
                    <?php else: ?>
                        <span class="badge-pill badge-gray">Custom</span>
                    <?php endif; ?>
                </td>
                <td class="table-actions">
                    <a href="?delete_setting=<?= urlencode($key) ?>" class="btn btn-danger btn-sm" data-confirm="Delete setting '<?= htmlspecialchars($key) ?>'? This cannot be undone.">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/page-foot.php'; ?>

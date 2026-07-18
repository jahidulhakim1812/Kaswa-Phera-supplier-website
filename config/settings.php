<?php
/**
 * Loads all key/value rows from the settings table once per request
 * and exposes a small helper to read them with a fallback default.
 */
require_once __DIR__ . '/db.php';

$GLOBALS['site_settings'] = [];
try {
    $stmt = $pdo->query("SELECT setting_key, setting_value FROM settings");
    foreach ($stmt->fetchAll() as $row) {
        $GLOBALS['site_settings'][$row['setting_key']] = $row['setting_value'];
    }
} catch (Exception $e) {
    // Settings table may not exist yet on first run; fail silently and use defaults.
}

function setting(string $key, string $default = ''): string
{
    return $GLOBALS['site_settings'][$key] ?? $default;
}
